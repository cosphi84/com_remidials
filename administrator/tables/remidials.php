<?php

use Joomla\CMS\Table\Table;

/**
 * @package     com_remidials
 * @subpackage  Table Remidials
 *
 * @copyright   (C) 2022 @ Risam, S.T
 * @license     Limited for FT-UNTAG Cirebon use Only
 */
class RemidialsTableRemidials extends Table
{
    public function __construct(&$db)
    {
        $this->setColumnAlias('published', 'auth_fakultas');
        parent::__construct('#__remidials', 'id', $db);
    }
}
