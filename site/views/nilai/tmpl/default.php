<?php
/**
 * Layout display nilai mahasiswa yang harus remidial
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDir = $this->escape($this->state->get('list.direction'));
$params = ComponentHelper::getParams('com_remidials');
?>
<form
    action="<?php echo Route::_('index.php?option=com_remidials&view=nilai'); ?>"
    method="POST" id="adminForm" name="adminForm">
    <legend>
        <h1>
            <?php echo Text::_('COM_REMIDIALS_VIEW_NILAI_PAGE_LEGEND'); ?>
        </h1>
    </legend>

    <div class="clearfix"></div>
    <div id="j-main-container" class="span10">
        <div class="row-fluid">
            <div class="span10">
                <?php
                    echo LayoutHelper::render('joomla.searchtools.default', array('view'=>$this, 'searchbutton'=>false));
                ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <?php
                    if (empty($this->items)) {
                        echo '<div class="alert alert-no-items">';
                        echo Text::_('JGLOBAL_NO_MATCHING_RESULTS');
                        echo '</div>';
                    } else {
                        ?>
                <table class="table table-hover" id="adminTable">
                    <thead>
                        <th class="center">#</th>
                        <th>Matakuliah</th>
                        <th>Semester</th>
                        <?php
                        $colspan = 3;
                        if (isset($this->items[0]->uas)) {
                            $colspan++;
                            echo '<th>UAS</th>';
                        }
                        if (isset($this->items[0]->uts)) {
                            $colspan++;
                            echo '<th>UTS</th>';
                        }
                        if (isset($this->items[0]->nilai_akhir)) {
                            $colspan++;
                            echo '<th>Nilai Akhir</th>';
                        } ?>
                    </thead>
                    <tfoot>
                        <tr>
                            <td class="center"
                                colspan="<?php echo $colspan; ?>">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php foreach ($this->items as $i=>$item) { ?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center">
                                <?php echo $this->pagination->getRowOffset($i); ?>
                            </td>
                            <td>
                                <?php echo $item->kodemk; ?><br />
                                <div class="small"><?php echo $item->matakuliah; ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $item->semester; ?>
                            </td>

                            <?php
                            if (isset($item->uas)) {
                                if ($item->uas > $params->get('treshold_uas')) {
                                    echo '<td style="color:green">';
                                    echo 'OK';
                                } else {
                                    echo '<td style="color:red">';
                                    echo $item->uas;
                                }
                                echo '</td>';
                            }
                            if (isset($item->uts)) {
                                if ($item->uts > $params->get('treshold_uts')) {
                                    echo '<td style="color:green">';
                                    echo 'OK';
                                } else {
                                    echo '<td style="color:red">';
                                    echo $item->uts;
                                }
                                echo '</td>';
                            }
                            if (isset($item->nilai_akhir)) {
                                if ($item->nilai_akhir > $params->get('treshold_sp')) {
                                    echo '<td style="color:green">';
                                    echo 'OK';
                                } else {
                                    echo '<td style="color:red">';
                                    echo $item->nilai_akhir;
                                }
                                echo '</td>';
                            }
                            ?>


                        </tr>

                        <?php  } ?>
                    </tbody>
                </table>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>