<?php
/**
 * @package     com_remidials
 * @subpackage  Table Remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */
use Joomla\CMS\Table\Table;

class RemidialsTableNilai extends Table
{
    public function __construct(&$db)
    {
        parent::__construct('#__siak_nilai', 'id', $db);
    }
}
