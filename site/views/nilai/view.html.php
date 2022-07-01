<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Exception\ExceptionHandler;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

class RemidialsViewNilai extends HtmlView
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
        
        $user = Factory::getUser();
        $usrGroups = $user->get('groups');
        $grpMahasiswa = ComponentHelper::getParams('com_siak')->get('grpMahasiswa');
        $doc = Factory::getDocument();
        $app = Factory::getApplication();

        

        $errors = $this->get('Errors');
        if (count($errors)>0) {
            throw new Exception(implode("<br />", $errors), 500);
            return false;
        }

        // Memastikan hanya mahasiswa yang boleh akses ke halaman ini.
        if (!in_array($grpMahasiswa, $usrGroups)) {
            $app->enqueueMessage(Text::_('JGLOBAL_AUTH_ACCESS_DENIED', 'error'));
            //$app->redirect(Route::_('index.php'));
            return false;
        }

        
        $title = Text::_('COM_REMIDIALS_VIEW_NILAI_PAGETITLE');
        $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        $doc->setTitle($title);
        
        parent::display($tpl);
    }
}
