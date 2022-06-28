<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die();

class RemidialsControllerRemidials extends AdminController
{
    public function getModel($name = 'Remidial', $prefix = 'RemidialsModel', $config = array('ignore_request'=>true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
