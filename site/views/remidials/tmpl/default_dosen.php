<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir = $this->escape($this->state->get('list.direction'));
?>

<form method="POST" name="adminForm" id="adminForm"
    action="<?php echo Route::_('index.php?option=com_remidials&view=remidials'); ?>">

    <legend>
        <?php echo Text::_('COM_REMIDIALS_VIEW_REMIDIALS_PAGETITLE_DOSEN'); ?>
    </legend>
    <div class="clearfix"></div>
    <div class="span11 well" id="j-main-container">
        <div class="row-fluid">
            <div class="span12">
                <?php echo LayoutHelper::render('joomla.searchtools.default', array('view'=>$this)); ?>
            </div>
        </div>

        <?php if (empty($this->items)) { ?>
        <div class="alert alert-no-items">
            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
        <?php } else { ?>
        <table class="table table-hover table-striped">
            <thead>
                <th class="center">
                    #
                </th>
                <th class="center">
                    <?php echo Text::_('COM_REMIDIALS_STATUS_LABEL'); ?>
                </th>
                <th>
                    <?php echo Text::_('COM_REMIDIALS_MAHASISWA_LABEL'); ?>
                </th>
                <th>
                    <?php echo Text::_('COM_REMIDIALS_JENIS_PERBAIKAN'); ?>
                </th>
                <th class="nowrap">
                    <?php echo Text::_('COM_REMIDIALS_MATAKULIAH_DOSEN_SMT'); ?>
                </th>
                <th class="center">
                    <?php echo Text::_('COM_REMIDIALS_NILAI_AWAL_LABEL'); ?>
                </th>
                <th class="center">
                    <?php echo Text::_('COM_REMIDIALS_NILAI_REMID_LABEL'); ?>
                </th>

                <th class="center">
                    ID
                </th>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="8" class="center">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($this->items as $i => $item) { ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo $this->pagination->getRowOffset($i); ?>
                    </td>
                    <td class="center nowrap">
                        <a
                            href="<?php echo Route::_('index.php?option=com_remidials&view=remidial&layout=edit&id='.$item->id); ?>">
                            <?php echo $item->status; ?>
                            <div class=" small nowrap">
                                <?php echo $item->text; ?>
                            </div>
                        </a>
                    </td>
                    <td class="nowrap">
                        <?php echo Factory::getUser($item->user_id)->username; ?>
                        <div class="small break-word">
                            <?php echo Factory::getUser($item->user_id)->name; ?><br />
                            <?php echo $item->prodi .' / '. $item->konsentrasi; ?><br />
                            <?php echo $item->semester .' / '. $item->kelas; ?>
                        </div>
                    </td>
                    <td>
                        <?php echo strtoupper($item->catid); ?>
                    </td>
                    <td class="nowrap">
                        <?php echo  $item->kodemk; ?>
                        <div class="small break-word">
                            <?php echo $item->mk; ?><br />

                        </div>
                    </td>
                    <td class="center">
                        <?php echo $item->nilai_awal; ?>
                    </td>
                    <td class="center">
                        <?php echo $item->nilai_remidial; ?>
                    </td>

                    <td class="center">
                        <?php echo $item->id; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } ?>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>