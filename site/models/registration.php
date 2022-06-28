<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die();


class RemidialsModelRegistration extends AdminModel
{
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_remidials.registration', 'registration', array('control'=>'jform', 'load_data'=>$loadData));
        if (empty($form)) {
            $errors = $this->getErrors();
            throw new Exception(implode('<br />', $errors), 5000);
        }
        return $form;
    }

    public function getTable($name = 'Remidials', $prefix = 'RemidialsTable', $options = array())
    {
        return Table::getInstance($name, $prefix, $options);
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_remidials.edit.registrasi.data', array());

        empty($data) ? $data = $this->getItem() : $data;

        return $data;
    }

    /**
     * LoadNilai
     * get Nilai by ID and its property
     * @param int ID The Nilai ID
     * @return Object Data, False on Error
     */
    public function loadNilai($id)
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select(array('n.tahun_ajaran', 'n.dosen', 'n.uts', 'n.uas', 'n.nilai_akhir'))
                ->from($db->quoteName('#__siak_nilai', 'n'))
                ->where($db->qn('n.id'). ' = '. $db->q($id));
            
        $query->select(array('m.title AS kodemk', 'm.alias AS mk'))
                ->leftJoin('#__siak_matakuliah AS m ON m.id = n.matakuliah');

        $query->select('s.title AS semester')
                ->leftJoin('#__siak_semester AS s ON s.id = n.semester');
        
        echo $query->dump();
    
        $db->setQuery($query);
        try {
            $data = $db->loadObject();
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return $data;
    }
}
