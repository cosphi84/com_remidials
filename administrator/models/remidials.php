<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

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
                'status',
                'prodi',
            );
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select(array('r.id', 'r.dosen_id', 'r.catid', 'r.tahun_ajaran', 'r.auth_fakultas', 'r.nilai_awal', 'r.nilai_remidial', 'r.update_master_nilai'))
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
            
        $search = $this->getState('filter.search');
        $prodi = $this->getState('filter.prodi');
        $status = $this->getState('filter.status');

        if (!empty($search)) {
            $likes = array( $db->qn('u.name'). ' LIKE '. $db->quote('%'. $search.'%'),
                            $db->qn('u.username'). ' LIKE '. $db->quote('%'. $search.'%')
                            );
            $query->where('( '. implode(' OR ', $likes). ' )');
        }

        if (!empty($status)) {
            $query->where($db->qn('n.state'). ' = '. (int) $status);
        }

        if (!empty($prodi)) {
            $query->where($db->qn('n.prodi'). ' = '. $db->q($prodi));
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'id');
        $orderDirn 	= $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();
        $this->setState('filter.prodi', $this->getUserStateFromRequest($this->context.'.filter.prodi', 'filter_prodi', '', 'string'));
        parent::populateState($ordering, $direction);
    }
}
