<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */
defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class RemidialsViewRemidials extends HtmlView
{
    public $filterForm;
    public $activeFilters;
    protected $items;
    protected $pagination;
    protected $state;
    protected $canDo;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->state = $this->get('State');
        $this->canDo = ContentHelper::getActions('com_remidials');

        RemidialsHelper::subMenuRemidi('remidials');
        $this->drawToolbar();
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode('<br />', $errors), 500);
            return false;
        }

        parent::display($tpl);
    }

    protected function drawToolbar()
    {
        $cando = $this->canDo;
        ToolbarHelper::title(Text::_('COM_REMIDIALS_REMIDIALS_TITLE_PAGE'), 'upload');

        if ($cando->get('core.edit')) {
            ToolbarHelper::editList('remidial.edit');
            ToolbarHelper::publishList('remidials.publish', 'COM_REMIDIALS_LABEL_CONFIRM');
        }

        ToolbarHelper::custom('remidials.download', 'download', 'download', 'Unduh', false);
        ToolbarHelper::custom('remidials.sync', 'loop', 'loop', 'Sinkronkan', true);

        if ($cando->get('core.delete')) {
            ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'remidials.delete');
        }

        JToolbarHelper::divider();
        if ($cando->get('core.admin') || $cando->get('core.options')) {
            JToolbarHelper::preferences('com_remidials');
        }
    }
}
