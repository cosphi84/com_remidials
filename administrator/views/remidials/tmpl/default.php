<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die();

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir = $this->escape($this->state->get('list.direction'));
?>

<form method="POST" name="adminForm" id="adminForm" action="index.php?option=com_remidials&view=remidials">

    <div id="j-sidebar-container" class="span2">
        <?php echo JHtmlSidebar::render(); ?>
    </div>

    <div class="span10" id="j-main-container">
        <div class="row-fluid">
            <div class="span6">
                <?php echo LayoutHelper::render('joomla.searchtools.default', array('view'=>$this)); ?>
            </div>
        </div>


    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>