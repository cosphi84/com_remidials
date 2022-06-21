<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

defined('_JEXEC') or die();

/**
 * Remidials Controller
 *
 * Since 0.0.1
 */
class RemidialsController extends JControllerLegacy
{
    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     *
     * @since   0.0.1
     */
    public function display($cacheable = false, $urlparams = false)
    {
        // validated user only
        $user = JFactory::getUser();
        if ($user->get('guest') == 1) {
            $uri = base64_encode(JUri::getInstance());
            $login_page = JRoute::_('index.php?option=com_users&view=login&return='.$uri);
            $this->setRedirect($login_page, JText::_('JERROR_ALERTNOAUTHOR'), 'error');

            return 0;
        }

        $doc = JFactory::getDocument();
        $vName = $this->input->getCmd('view', 'dashboard');
        $vFormat = $doc->getType();
        $lName = $this->input->getCmd('layout', 'default');

        if ($view = $this->getView($vName, $vFormat)) {
            $model = $this->getModel($vName);
        }

        JFactory::getApplication()->setHeader('Referrer-Policy', 'no-referrel', true);
        $view->setModel($model);
        $view->setLayout($lName);
        $view->document = $doc;
        $view->display();
    }
}
