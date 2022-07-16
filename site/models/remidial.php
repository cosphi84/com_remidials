<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die();

class RemidialsModelRemidial extends AdminModel
{
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_remidials.frmEditRemidi', 'frmnilai', array('control'=>'jform', 'load_data'=>$loadData));

        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getTable($name = 'Remidials', $prefix = 'RemidialsTable', $options = array())
    {
        return Table::getInstance($name, $prefix, $options);
    }

    public function getItem($pk = null)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select(array('r.id', 'r.dosen_id', 'r.state', 'r.catid', 'r.tahun_ajaran', 'r.auth_fakultas', 'r.nilai_awal', 'r.nilai_remidial', 'r.update_master_nilai', 'r.input_by', 'r.input_date', 'r.created_date'))
                ->from($db->quoteName('#__remidials', 'r'));

        $query->select(array('n.id AS nid'))
            ->innerJoin('#__siak_nilai AS n ON n.id = r.nilai_id');

        $query->select(array('m.title AS kodemk', 'm.alias AS mk'))
            ->leftJoin('#__siak_matakuliah AS m ON m.id = n.matakuliah');

        $query->select('s.title AS semester')
            ->leftJoin('#__siak_semester AS s ON s.id = n.semester');

        $query->select(array('p.title as prodi', 'p.alias AS programstudi'))
            ->leftJoin('#__siak_prodi AS p ON p.id = n.prodi');
        
        $query->select(array('k.title AS konsentrasi'))
            ->leftJoin('#__siak_jurusan AS k on k.id = n.jurusan');

        $query->select('g.title AS kelas')
            ->leftJoin('#__siak_kelas_mahasiswa AS g on g.id = n.kelas');
        
        $query->select(array('u.name AS mahasiswa', 'u.username AS NPM'))
            ->leftJoin('#__users AS u ON u.id = n.user_id');
        $query->select(array('rs.status', 'rs.text'))
            ->leftJoin('#__remidial_status AS rs ON rs.id = r.state');
        
        $query->where($db->qn('r.id') . ' = '. (int) $pk);

        $db->setQuery($query);

        try {
            $data = $db->loadObject();
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    
        return $data;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState(
            'com_remidials.edit.remidial.data',
            array()
        );


        if (empty($data)) {
            $data = $this->getItem();
        }
        $data->input = Factory::getUser($data->input_by)->name;
        return $data;
    }

  
    
    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   1.6
     */
    public function save($data)
    {
        $dispatcher = \JEventDispatcher::getInstance();
        $table      = $this->getTable();
        $context    = $this->option . '.' . $this->name;
        $app        = Factory::getApplication();

        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Include the plugins for the save events.
        PluginHelper::importPlugin($this->events_map['save']);


        // Allow an exception to be thrown.
        try {
            // Load the row if saving an existing record.
            if ($pk > 0) {
                $table->load($pk);
                $isNew = false;
            }
           
            $user = Factory::getUser();
            $tanggal = Date::getInstance();
            $tanggal->setTimezone(Factory::getConfig()->get('offset'));

            
            if ($data['nilai_remidial'] > 0) {
                $data['input_by'] = $user->id;
                $data['input_date'] = $tanggal->toSql();
            }
           
            // Bind the data.
            if (!$table->bind($data)) {
                $this->setError($table->getError());

                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check()) {
                $this->setError($table->getError());

                return false;
            }

            // Trigger the before save event.
            $result = $dispatcher->trigger($this->event_before_save, array($context, $table, $isNew, $data));

            if (in_array(false, $result, true)) {
                $this->setError($table->getError());

                return false;
            }

            // Store the data.
            if (!$table->store()) {
                $this->setError($table->getError());

                return false;
            }

            // Clean the cache.
            $this->cleanCache();

            // Trigger the after save event.
            $dispatcher->trigger($this->event_after_save, array($context, $table, $isNew, $data));
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        if ((int)$data['state'] > 5) {
            $this->updateNilaiMaster($data);
        }
        return true;
    }

    protected function getBobotNilai()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select(array('title', 'bobot'))
            ->from($db->qn('#__siak_bobot_nilai'))
            ->where($db->qn('state'). ' = '. 1);
        
        $db->setQuery($query);
        try {
            $result = $db->loadObjectList();
        } catch (RuntimeException $err) {
            $this->setError($err->getMessage());
            return false;
        }
        
        foreach ($result as $key => $value) {
            $ret[$value->title] = $value->bobot;
        }
        
        return $ret;
    }

    public function updateNilaiMaster($data)
    {
        $tableNilai = $this->getTable('Nilai');
        $tableRemid = $this->getTable();
        $nilaiBaru = array();
        $sumNilai = 0;
        
        if (!$tableRemid->load($data['id'])) {
            $this->setError('Data Remid not exist!');
            return false;
        }

        $dataRemid = $tableRemid->getProperties(1);
        
        // Cek apakah Fakultas sudah memberi ijin update nilai atau belum
        if ($dataRemid['auth_fakultas'] == '0') {
            return false;
        }
    

        // Laod Data Nilai
        if (!$tableNilai->load($dataRemid['nilai_id'])) {
            $this->setError('Data Nilai Not exist!');
            return false;
        }

        $dataNilai = $tableNilai->getProperties(1);
       
        // Load Bobot nilai
        $bobotNilai = $this->getBobotNilai();
       

        switch ($dataRemid['catid']) {
            case 'sp':
                // unutk SP nilai_bobot sama nilai_mutu langsung dihitung dari nilai SP nya
                $col = 'nilai_akhir';
                $nilaiBaru = $this->getNilaiMutu($dataRemid['nilai_remidial']);
                $dataNilai['nilai_akhir'] = $dataRemid['nilai_remidial'];
                $dataNilai['nilai_angka'] = $nilaiBaru['nilai_angka'];
                $dataNilai['nilai_mutu'] = $nilaiBaru['nilai_mutu'];
                break;

            default:
                // cek Remid apa( Uts, UAS ?)
                $col = strtolower($data['catid']);
                // Update nilai remid di datashet nilai dengan nilai remidial
                $dataNilai[$col] = $dataRemid['nilai_remidial'];
                // harusnya item yang diremid (uts atau uas) ada di property bobot nilai
                if (array_key_exists($col, $bobotNilai)) {
                    // update nilai akhir di datasheet $nilai dengan nilai remidi kali bobot nilainya
                    $dataNilai['nilai_akhir'] = $bobotNilai[$col] * $dataRemid['nilai_remidial'];
                    // buang item nilai yang diremidi dari datasheet bbot nilai
                    unset($bobotNilai[$col]);
                }

                // hitung ulang nilai akhir tanpa nilai yang diremidi (nilai remidi kan bobotnya sudah dihutng diatas)
                foreach ($bobotNilai as $key => $value) {
                    $sumNilai += $dataNilai[$key] * $value;
                }
                // tambahkan nilai total ke datahset nilai akhir
                $dataNilai['nilai_akhir'] += $sumNilai;
                // hitung nilai bobot dan nilai mutu
                $nilaiBaru = $this->getBobotNilai($dataNilai['nilai_akhir']);
                $dataNilai['nilai_angka'] = $nilaiBaru['nilai_angka'];
                $dataNilai['nilai_mutu'] = $nilaiBaru['nilai_mutu'];
                
                break;
        }


        // simpan data
        if (!$tableNilai->bind($dataNilai)) {
            $this->setError($tableNilai->getError());
            return false;
        }

        if (!$tableNilai->check()) {
            $this->setError($tableNilai->getError());
            return false;
        }

        if (!$tableNilai->store()) {
            $this->setError($tableNilai->getError());
            return false;
        }

        $dataRemid['update_master_nilai'] = 1;
        
        if (!$tableRemid->bind($dataRemid)) {
            $this->setError($tableRemid->getError());
            return false;
        }

        if (!$tableRemid->check()) {
            $this->setError($tableRemid->gettError());
            return false;
        }

        if (!$tableRemid->store()) {
            $this->setError($tableRemid->getError());
            return false;
        }

        //done
        return true;
    }

    protected static function getNilaiMutu($nilai = 0)
    {
        $result = [];

        switch ($nilai) {
            case $nilai >= 75:
                $result['nilai_angka'] = 'A';
                $result['nilai_mutu'] = '4';

                break;

            case $nilai <= 74.99 && $nilai >= 65.00:
                $result['nilai_angka'] = 'B';
                $result['nilai_mutu'] = '3';

                break;

            case $nilai <= 64.99 && $nilai >= 50.00:
                $result['nilai_angka'] = 'C';
                $result['nilai_mutu'] = '2';

                break;

            case $nilai <= 49.99 && $nilai >= 35:
                $result['nilai_angka'] = 'D';
                $result['nilai_mutu'] = '1';

                break;

            default:
            $result['nilai_angka'] = 'E';
            $result['nilai_mutu'] = '0';

            break;
        }

        return $result;
    }

    
}
