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
use Joomla\CMS\Router\Route;

 defined('_JEXEC') or die();

 HTMLHelper::_('behavior.formvalidator');
 HTMLHelper::_('formbehavior.chosen', 'select');
 HTMLHelper::_('script', 'com_remidials/submitbutton.js', array('version' => 'auto', 'relative'=>true));

 ?>
<legend>
    <h1>
        <?php echo Text::sprintf('COM_REMIDIALS_VIEW_REMIDIAL_LEGEND', strtoupper($this->item->catid)); ?>
    </h1>
</legend>
<div class="clearfix"></div>
<div class="span10">
    <div class="row-fluid">
        <div class="span12">
            <form method="POST"
                action="<?php echo Route::_('index.php?option=com_remidials&view=remidial&layout=edit&id='.$this->item->id); ?>"
                name="adminForm" id="adminForm" class="form-validate form-horizontal well">
                <div class="form-horizontal">
                    <fieldset class="adminForm">
                        <div class="row-fluid">
                            <?php echo $this->form->renderFieldset('frmNilaiRemidial'); ?>
                        </div>
                    </fieldset>

                    <div class="btn-toolbar center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary"
                                onClick="Joomla.submitbutton('remidial.save')">
                                <span class="icon-ok"></span><?php echo Text::_('JSAVE'); ?>
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn" onClick="Joomla.submitbutton('remidial.cancel')">
                                <span class="icon-cancel"></span><?php echo Text::_('JCANCEL'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="task" value="" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
        </div>
    </div>
</div>