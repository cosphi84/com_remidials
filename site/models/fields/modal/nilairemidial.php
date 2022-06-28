<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die();

class JFormFieldModal_Nilairemidial extends FormField
{
    protected function getInput()
    {
        $value = (int) $this->value > 0 ? (int) $this->value : '';
        $modalId = 'Nilairemidial_'.$this->id;

        HTMLHelper::_('jquery.framework');
        HTMLHelper::_('script', 'system/modal-fields.js', array('version' => 'auto', 'relative' => true));

        Factory::getDocument()->addScriptDeclaration('
            function jSelectNilairemidial_'.$this->id."(id, title, catid, object, url, language) {
                window.processModalSelect('Nilairemidial', '".$this->id."', id, title, catid, object, url, language);
            }
        ");

        if ($value) {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select(array('mk.title as kode', 'mk.alias as mk'))
                    ->from($db->quoteName('#__siak_matakuliah', 'mk'))
                    ->leftJoin('#__siak_nilai AS n ON n.matakuliah = mk.id')
                    ->where($db->quoteName('n.id'). ' = '. (int) $value);

            $db->setQuery($query);

            try {
                $result = $db->loadObject();
            } catch (RuntimeException $e) {
                throw new Exception($e->getMessage(), 500);
            }
        }

        $title = empty($result) ? Text::_('COM_REMEDIAL_MENU_PILIH_NILAI_REMED') : $result->kode . ' - '. $result->mk;
        $html = '<span class="input-append">';
        $html .= '<input class="input input-xlarge" id="'.$this->id.'_name" type="text" value="'.$title.'" disabled="disabled" size="70" />';

        $html  .= '<a'
                . ' class="btn has-tooltip'. ($value ? ' hidden' : '') . '"'
                . ' id="'. $this->id .'_select"'
                . ' data-toggle="modal"'
                . ' role="button"'
                . ' href="#ModalSelect'.$modalId.'"'
                . ' title="' . HTMLHelper::tooltipText('COM_REMIDIAL_MENUITEM_SELECT_BUTTON_TOOLTIP').'">'
                . '<span class="icon-file" aria-hidden="true"></span>'. Text::_('JSELECT')
                . '</a>';

        $html .= '<a'
                . ' class="btn'. ($value ? '' : ' hidden') . '"'
                . ' id="'. $this->id.'_clear"'
                . ' href="#"'
                . ' onClick="window.processModalParent(\''.$this->id.'\'); return false;">'
                . '<span class="icon-remove" aria-hidden="true"></span>'. Text::_('JCLEAR')
                . '</a>';
        $html .= '</span>';

        $linkNilai = 'index.php?option=com_remidials&amp;view=nilai&amp;layout=modal&amp;tmpl=component&amp;'.Session::getFormToken().'=1';
        $urlSelect = $linkNilai. '&amp;function=jSelectNilairemidial_'.$this->id;

        $modalTitle = Text::_('COM_REMIDIALS_MODAL_NILAI_TITLE');

        $html .= HTMLHelper::_(
            'bootstrap.renderModal',
            'ModalSelect'. $modalId,
            array(
                'title'         => $modalTitle,
                'url'           => $urlSelect,
                'height'        => '400px',
                'width'         => '800px',
                'bodyHeight'    => '70',
                'modalWidth'    => '80',
                'footer'        => '<a role="button" class="btn" data-dismiss="modal" aria-hidden="true">' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE').'</a>',
            )
        );

        $class = $this->required ? ' class="required modal-value"' : '';

        $html .= '<input type="hidden" id="'. $this->id.'_id" '. $class
                . ' data-required="'.(int) $this->required .'" name="'. $this->name .'"'
                . ' data-text="'. htmlspecialchars(Text::_('COM_REMEDIAL_MENU_PILIH_NILAI_REMED', true), ENT_COMPAT, 'UTF-8'). '"'
                . ' value="'. $value .'" />';

        return $html;
    }

    protected function getLabel()
    {
        return str_replace($this->id, $this->id.'_id', parent::getLabel());
    }
}
