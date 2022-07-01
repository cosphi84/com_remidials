<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();
HTMLHelper::_('behavior.formvalidator');
?>
<form method="POST"
    action="index.php?option=com_remidials&view=remidial&layout=edit&id=<?php echo $this->item->id; ?>"
    id="adminForm" name="adminForm" class="form-validate form-horizontal">
    <fieldset class="adminForm">
        <div class="form-horizontal">
            <?php echo $this->form->renderFieldset('frmEditRemidi'); ?>
        </div>
    </fieldset>
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>