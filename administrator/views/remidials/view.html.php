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

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode('<br />', $errors), 500);
            return false;
        }

        parent::display($tpl);
    }
}
