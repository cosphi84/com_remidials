<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class RemidialsViewRemidial extends HtmlView
{
    protected $form;
    protected $item;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $errors = $this->get('Errors');

        if (count($errors) > 0) {
            throw new Exception(implode('<br />', $errors), 500);
            return false;
        }

        Factory::getApplication()->input->set('hidemainmenu', true);
        $this->drawToolbar();
        parent::display($tpl);
    }

    protected function drawToolbar()
    {
        $canDo = ContentHelper::getActions('com_remidials');
        ToolbarHelper::title(Text::_('COM_REMIDIALS_EDIT_REMIDIALS_PAGE_TITLE'), 'upload');

        if ($canDo->get('core.edit')) {
            ToolbarHelper::apply('remidial.apply');
            ToolbarHelper::save('remidial.save');
        }

        if (empty($this->item->id)) {
            ToolbarHelper::cancel('remidial.cancel');
        } else {
            ToolbarHelper::cancel('remidial.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
