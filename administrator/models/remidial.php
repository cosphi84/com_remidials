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
        $form = $this->loadForm('com_remidials.frmEditRemidi', 'remidial', array('control'=>'jform', 'load_data'=>$loadData));

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

    protected function canDelete($record)
    {
        if (!empty($record->id)) {
            return Factory::getUser()->authorise('core.delete', 'com_remidials.remidial.'.$record->id);
        }
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

            $dataTable = $table->getProperties(1);
            if ($data['nilai_remidial'] !== $dataTable['nilai_remidial']) {
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

        return true;
    }

    public function updateNilaiMaster(&$pks)
    {
        $table = $this->getTable('Nilai');
        foreach ($pks as $i=>$pk) {
            $dataNilai = $this->getItem($pk);
            $masterNIlai = array();
            switch ($dataNilai->catid) {
                case 'sp':
                    $col = 'nilai_akhir';
                    break;
                default:
                    $col = strtolower($dataNilai->catid);
                    break;
            }
            $masterNIlai['id'] = $dataNilai->nid;
            $masterNIlai[$col] = $dataNilai->nilai_remidial;
            
            if ($table->load($dataNilai->nid)) {
                
                // Bind the data.
                if (!$table->bind($masterNIlai)) {
                    $this->setError($table->getError());

                    return false;
                }
                

                // Check the data.
                if (!$table->check()) {
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
            } else {
                $this->setError($table->getError());

                return false;
            }
        }
        return true;
    }
}
