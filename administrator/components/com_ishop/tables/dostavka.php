<?php

/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */
// No direct access
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/ktable.php'; 
/**
 * city Table class
 */
class IshopTableDostavka extends IshopTableKtable {

    protected $asset_name;

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        $this->asset_name = 'dostavka';
        parent::__construct('#__ishop_dostavka', 'id', $db);
    }
}
