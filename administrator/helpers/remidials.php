<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */


use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;

 defined('_JEXEC') or die();

 class RemidialsHelper extends ContentHelper
 {
     public static function subMenuRemidi($vName)
     {
         $canDo = ContentHelper::getActions('com_remidials');
         if ($canDo->get('core.manage')) {
             JHtmlSidebar::addEntry(
                 Text::_('COM_REMIDIALS_SUBMENU_MANAGE'),
                 'index.php?option=com_config&view=component&component=com_remidials'
             );
         }

         JHtmlSidebar::addEntry(
             Text::_('COM_REMIDIALS_SUBMENU_PENDAFTAR'),
             'index.php?option=com_remidials&view=remidials',
             $vName == 'remidials'
         );
     }
 }
