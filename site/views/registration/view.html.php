<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

class RemidialsViewRegistration extends HtmlView
{
    protected $form;

    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $doc = Factory::getDocument();
        $user = Factory::getUser();
        $usrGroups = $user->get('groups');
        $grpMahasiswa = ComponentHelper::getParams('com_siak')->get('grpMahasiswa');
        $params = ComponentHelper::getParams('com_remidials');

        // Memastikan hanya mahasiswa yang boleh akses ke halaman ini.
        if (!in_array($grpMahasiswa, $usrGroups)) {
            $app->enqueueMessage(Text::_('JGLOBAL_AUTH_ACCESS_DENIED', 'error'));
            $app->redirect(Route::_('index.php'));
            return false;
        }

        $this->form = $this->get('Form');
        $errors = $this->get('Errors');

        if (count($errors) > 0) {
            throw new Exception(implode('<br />', $errors), 500);
            return false;
        }

        $date1 = new Date();
        $date2 = new Date($params->get('off_date'));
        $diff = $date1->diff($date2);
        

        if ((bool) $params->get('remidial_en') == false) {
            $tpl = 'close';
        } else {
            if ((bool) $params->get('auto_off') && $diff->invert >= 1 && $diff->days >= 0) {
                $tpl = 'close';
            }
        }

        

        $title = Text::_('COM_REMIDIALS_VIEW_REGISTRATION_PAGE_TITLE');
        $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        $doc->setTitle($title);
        Text::script('COM_REMIDIAL_ISIAN_SALAH');

        parent::display($tpl);
    }
}
