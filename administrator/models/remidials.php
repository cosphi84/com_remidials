<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die();

class RemidialsModelRemidials extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id'
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

        $search = $this->getState('filter.search');
        $prodi = $this->getState('filter.prodi');
        $konsentrasi = $this->getState('filter.jurusan');
        $status = $this->getState('filter.status');

        return $query;
    }
}
