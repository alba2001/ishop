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
class IshopTableProduct extends IshopTableKtable {

    protected $asset_name;
    protected $category_id = 0;
    public $product_category_table;
    public $product_purchase_table;
    public $purchases;

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        $this->asset_name = 'product';
        $this->_check_fields = array('desc');
        $this->_date_fields = array('created_dt','novinka_dt');
        $this->product_category_table = JTable::getInstance('Product_Category', 'IshopTable');
        $this->product_purchase_table = JTable::getInstance('Product_Purchase', 'IshopTable');
        $this->purchases = array();
        
        parent::__construct('#__ishop_products', 'id', $db);
    }
    
    public function bind($array, $ignore = '') {
        if(isset($array['category_id']) AND $array['category_id'])
        {
            $this->category_id = $array['category_id'];
        }
        return parent::bind($array, $ignore);
    }
    
    public function store($updateNulls = false) 
    {
        if(!parent::store($updateNulls))
        {
            return FALSE;
        }
        
        // Записываем категорию
        if($this->category_id)
        {
            return $this->_save_product_category();
        }
        return TRUE;
    }
    

    public function save($src, $orderingFilter = '', $ignore = '') {
        $result = parent::save($src, $orderingFilter, $ignore);
        if(!$result)
        {
            return $result;
        }
        // Записываем категорию
        if(isset($src['category_id']) AND $src['category_id'] AND $this->id)
        {
            $this->category_id = $src['category_id'];
            return $this->_save_product_category();
        }
        return TRUE;
    }

    private function _save_product_category()
    {
        $data = array(
            'product_id'=>$this->id,
            'category_id'=>$this->category_id,
        );
        return $this->product_category_table->save($data);
    }
    
    /**
     * Override parent load method
     * @param type $keys
     * @param type $reset 
     */
    public function load($keys = null, $reset = true) {
        if (!parent::load($keys, $reset))
        {
            return FALSE;
        }
        
        // Присоединяем покупаемые с этим товаром товары
        $product_purchases = $this->product_purchase_table->get_rows(array('product_id' => $this->id), '`id` DESC');
 
        foreach($product_purchases as $product_purchase)
        {
        
            $this->purchases[] = $this->load_object($product_purchase->purchase_id);
        }
        return TRUE;
    }
    
    public function delete($pk = null) {
        if(!parent::delete($pk))
        {
            return FALSE;
        }
        $this->product_category_table->delete_by_product_id($pk);
        $this->product_purchase_table->delete_by_product_id($pk);
        $this->product_purchase_table->delete_by_purchase_id($pk);
    }
    
}
