<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die();

class RemidialsModelRemidials extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id',
                'jenis',
                'status'
            );
        }
        parent::__construct($config);
        
        $user = Factory::getUser();
        $userGroups = $user->get('groups');

        $siakParams = ComponentHelper::getParams('com_siak');
        $grpDosen = $siakParams->get('grpDosen');

        if (in_array($grpDosen, $userGroups)) {
            $this->setState('mode', 'dosen');
        } else {
            $this->setState('mode', 'mahasiswa');
        }
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $user = Factory::getUser();
        $mode = $this->getState('mode', 'mahasiswa');

        
        $query->select('r.*')
            ->from($db->qn('#__remidials', 'r'));

        $query->select(array('n.id as nid', 'n.user_id' ))
            ->innerJoin('#__siak_nilai AS n ON n.id = r.nilai_id');


        $query->select(array('m.title AS kodemk', 'm.alias AS mk'))
            ->innerJoin('#__siak_matakuliah AS m ON m.id = n.matakuliah');

        $query->select(array('st.status', 'st.text'))
            ->leftJoin('#__remidial_status AS st ON st.id = r.state');

        $query->select(array('p.title as prodi', 'p.alias AS programstudi'))
            ->leftJoin('#__siak_prodi AS p ON p.id = n.prodi');

        $query->select(array('k.title as konsentrasi'))
            ->leftJoin('#__siak_jurusan AS k ON k.id = n.jurusan');
        
        $query->select('s.title as semester')
            ->leftJoin('#__siak_semester AS s ON s.id = n.semester');

        $query->select('kl.title as kelas')
            ->leftJoin('#__siak_kelas_mahasiswa AS kl ON kl.id = n.kelas');

        $search = $this->getState('filter.search');
        $status = $this->getState('filter.status');
        $jenis = $this->getState('filter.jenis');

        if (!empty($search)) {
            $likes = array( $db->qn('m.title'). ' LIKE '. $db->quote('%'. $search.'%'),
                                $db->qn('m.alias'). ' LIKE '. $db->quote('%'. $search.'%')
                                );
            $query->where('( '. implode(' OR ', $likes). ' )');
        }

        if (!empty($status)) {
            $query->where($db->qn('r.state'). ' = '. (int) $status);
        } else {
            $query->where($db->qn('r.state') . ' < 6');
        }

        if ($mode == 'mahasiswa') {
            $query->where($db->qn('n.user_id') . ' = '. (int) $user->id);
        } else {
            $query->where($db->qn('r.dosen_id'). ' = '. $user->id);
        }
        

        if (!empty($jenis)) {
            $query->where($db->qn('r.catid'). ' = '. $db->q($jenis));
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'id');
        $orderDirn 	= $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

    

        return $query;
    }
}
