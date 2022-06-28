<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */


 defined('_JEXEC') or die();
 JLoader::register('RemidialsHelper', __DIR__ . '/helpers/remidials.php');
 $controller = JControllerLegacy::getInstance('Remidials');
 $task = JFactory::getApplication()->input->get('task');
 $controller->execute($task);
 $controller->redirect();
