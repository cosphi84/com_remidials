<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die();

class RemidialsModelRemidial extends AdminModel
{
    public function getForm($data = array(), $loadData = true)
    {
    }

    public function getTable($name = 'Remidials', $prefix = 'RemidialsTable', $options = array())
    {
        return Table::getInstance($name, $prefix, $options);
    }
}
