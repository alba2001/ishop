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
 * product Table class
 */
class IshopTableProducttype extends IshopTableKtable {

    protected $asset_name;

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        $this->asset_name = 'producttype';
        $this->_check_fields = array('name');
        parent::__construct('#__ishop_producttypes', 'id', $db);
    }
    
    
    public function store($updateNulls = false) {
        // Переписываем псевдоним
        if($this->name)
        {
            $this->alias = JApplication::stringURLSafe($this->name);
        }
        // Меняем запятые на точки в цене
        if($this->cena_mag)
        {
            $this->cena_mag = str_replace(',', '.', $this->cena_mag);
        }
        if($this->cena_tut)
        {
            $this->cena_tut = str_replace(',', '.', $this->cena_tut);
        }
        return parent::store($updateNulls);
    }
}
