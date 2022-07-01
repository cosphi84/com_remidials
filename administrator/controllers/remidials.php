<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die();

class RemidialsControllerRemidials extends AdminController
{
    public function getModel($name = 'Remidial', $prefix = 'RemidialsModel', $config = array('ignore_request'=>true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function download()
    {
        $this->checkToken();
        $this->setRedirect('index.php?option=com_remidials&view=remidials&format=xlsx');
        return true;
    }

    public function sync()
    {
        $this->checkToken();

        // Get items to remove from the request.
        $cid = $this->input->get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1) {
            \JLog::add(\JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            $cid = ArrayHelper::toInteger($cid);

           
            // Remove the items.
            if ($model->updateNilaiMaster($cid)) {
                $this->setMessage(\JText::plural($this->text_prefix . '_N_ITEMS_SYNCKED', count($cid)));
            } else {
                $this->setMessage($model->getError(), 'error');
            }
        }

        $this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }
}
