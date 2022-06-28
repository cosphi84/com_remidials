<?php
/**
 * @package     Joomla.Siak
 * @subpackage  com_remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

defined('_JEXEC') or die();

FormHelper::loadFieldClass('list');


class JFormFieldStatusremidi extends JFormFieldList
{
    protected $type = 'Statusremidi';
    protected $loadExternally = 0;

    public function getOptions()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select(array('id', 'status', 'text'))
            ->from($db->quoteName('#__remidial_status'))
            ->order($db->qn('status') . ' ASC');
        $db->setQuery($query);

        try {
            $result = $db->loadObjectList();
        } catch (RuntimeException $e) {
            throw new Exception($e->getMessage(), 500);
        }

        foreach ($result as $item) {
            $options[] = JHtmlSelect::option($item->id, $item->status.' - '. $item->text);
        }

        if (!$this->loadExternally) {
            $opts = array_merge(parent::getOptions(), $options);
        }

        return $opts;
    }

    public function getOptionsExternally()
    {
        $this->loadExternally = 1;
    }
}
