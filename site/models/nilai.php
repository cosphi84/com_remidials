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


class RemidialsModelNilai extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'jenis'
            );
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $user = Factory::getUser();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $layout = Factory::getApplication()->input->getCmd('layout', 'default');

        $params = ComponentHelper::getParams('com_remidials');
        
        $filter = $this->getState('filter.jenis');
        if ($layout == 'modal') {
            $filter = $params->get('jenis_remidial');
        }

        $query->select('n.id');
        switch ($filter) {
            case 'uts':
                $query->select($db->qn('n.uts'))
                    ->where($db->qn('n.uts'). ' <= '. (int) $params->get('treshold_uts', 50))
                    ->where($db->qn('n.nilai_final') . ' = '. 0);
                break;
            case 'uas':
                $query->select($db->qn('n.uas'))
                    ->where($db->qn('n.uas'). ' <= '. (int) $params->get('treshold_uas', 50))
                    ->where($db->qn('n.nilai_final') . ' = '. 0);
                break;
            case 'sp':
                $query->select($db->qn('n.nilai_akhir'))
                    ->where($db->qn('n.nilai_akhir'). ' <= '. (int) $params->get('treshold_sp', 50));
                break;
            default:
                $query->select(array('n.uts', 'n.uas', 'n.nilai_akhir'))
                    ->where('('. implode(' OR ', [
                        $db->qn('n.uts'). ' <= '. $params->get('treshold_uts'),
                        $db->qn('n.uas'). ' <= '. $params->get('treshold_uas'),
                        $db->qn('n.nilai_akhir'). ' <= '. $params->get('treshold_sp')
                        ]) .' )');
                
        }

        $query->from($db->quoteName('#__siak_nilai', 'n'))
                ->where($db->qn('n.user_id').' = '. (int) $user->id)
                ->where($db->qn('n.state') . ' = '. (int) 1)
                ->where('r.id IS NULL');

        $query->select(array('mk.title AS kodemk', 'mk.alias AS matakuliah', 's.title AS semester'))
                ->leftJoin('#__siak_matakuliah AS mk ON mk.id = n.matakuliah')
                ->leftJoin('#__remidials AS r ON r.nilai_id = n.id')
                ->leftJoin('#__siak_semester AS s ON s.id = n.semester');
        
        $keyword = $this->getState('filter.keyword');
        if (isset($keyword)) {
            $keyword = $db->q('%'.$keyword.'%');
            $keywords = implode(' OR ', array(
                $db->qn('mk.title'). ' LIKE '. $keyword,
                $db->qn('mk.alias'). ' LIKE '. $keyword,
            ));

            $query->where('( '. $keywords .' )');
        }
 
        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $jenis = $this->getUserStateFromRequest($this->context.'.filter.jenis', 'filter_jenis', '', 'string');
        $this->setState('filter.jenis', $jenis);
        $this->setState('filter.keyword', $search);
        parent::populateState($ordering, $direction);
    }
}
