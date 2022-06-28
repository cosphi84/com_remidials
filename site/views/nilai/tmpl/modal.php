<?php

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die();

HTMLHelper::_('behavior.core');
HTMLHelper::_('script', 'com_siak/modal-handler.js', array('version'=>'auto', 'relative'=> true));

$app = Factory::getApplication();
$function = $app->input->getCmd('function', 'jSelectNilairemidial');
$onclick = $this->escape($function);
$params = ComponentHelper::getParams('com_remidials');
?>

<div class="container-popup">
    <form
        action="<?php echo Route::_('index.php?option=com_remidials&view=nilai&layout=modal&tmpl=component&function='. $function.'&'. Session::getFormToken(). '=1'); ?>"
        method="POST" name="adminForm" id="adminForm" class="form-inline">

        <?php echo LayoutHelper::render('joomla.searchtools.default', array('view'=>$this)); ?>

        <div class="clearfix"></div>

        <table class="table table-striped table-hover" id="adminTable">
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
                <?php if (!empty($this->items)) { ?>
                <?php
                    foreach ($this->items as $i=>$item) {
                        $attribs = 'data-function="'. $this->escape($onclick). '"'
                                    . ' data-id="'. $item->id . '"'
                                    . ' data-title="'.$item->kodemk.' &#8722; '.$item->matakuliah.'"'; ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo $this->pagination->getRowOffset($i); ?>
                    </td>
                    <td>
                        <a class="select-link" href="javascript:void(0);" <?php echo $attribs; ?>>
                            <?php echo $item->kodemk; ?><br />
                            <div class="small break-word"><?php echo $item->matakuliah; ?>
                            </div>
                        </a>
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
                        } ?>
                </tr>
                <?php
                    } ?>
                <?php } ?>
            </tbody>
        </table>
        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>