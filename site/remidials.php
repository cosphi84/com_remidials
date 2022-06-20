<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

defined('_JEXEC') or die;

$task = JFactory::getApplication()->input->get('task');
$controller = JControllerLegacy::getInstance('Remidials');
$controller->execute($task);
$controller->redirect();
