<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

 defined('_JEXEC') or die();

 class RemidialsViewRemidials extends HtmlView
 {
     public $filterForm;
     public $activeFilters;
     protected $items;
     protected $pagination;
     protected $state;

     public function display($tpl = null)
     {
         $this->items = $this->get('Items');
         $this->pagination = $this->get('Pagination');
         $this->activeFilters = $this->get('ActiveFilters');
         $this->filterForm = $this->get('FilterForm');
         $this->state = $this->get('State');
         $errors = $this->get('Errors');

         if (count($errors) > 0) {
             throw new Exception(implode('<br />', $errors), 500);
             return false;
         }

         $doc = Factory::getDocument();
         $app = Factory::getApplication();
         $mode = $this->state->get('mode', 'mahasiswa');
         if ($mode == 'mahasiswa') {
             $title = Text::_('COM_REMIDIALS_VIEW_REMIDIALS_PAGETITLE_MAHASISWA');
         } else {
             $title = Text::_('COM_REMIDIALS_VIEW_REMIDIALS_PAGETITLE_DOSEN');
         }
         $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
         $doc->setTitle($title);

         
         parent::display($mode);
     }
 }
