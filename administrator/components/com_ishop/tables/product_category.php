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
class IshopTableProduct_Category extends IshopTableKtable {

    protected $asset_name;

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {

        $this->asset_name = 'product_category';
        parent::__construct('#__ishop_product_category', 'id', $db);
        
    }
    
    public function save($src, $orderingFilter = '', $ignore = '') {
        if(!isset($src['product_id']) OR !$src['product_id'])
        {
            return FALSE;
        }
        if(!isset($src['category_id']) OR !$src['category_id'])
        {
            return FALSE;
        }
        if(!$this->load(array(
                'product_id' => $src['product_id'],
                'category_id' => $src['category_id'],
            )))
        {
            return parent::save($src, $orderingFilter, $ignore);
        }
        return TRUE;
    }
    
    private function _delete_by_where($where)
    {
        $query = $this->_db->getQuery(true);
        $query->select();
        $query->from($this->_tbl);
        $query->where($where);
        $this->_db->setQuery($query);
        $ids = $this->_db->loadResultArray();
        foreach ($ids as $id)
        {
            if(!$this->delete($id))
            {
                return FALSE;
            }
        }
        return TRUE;
        
    }

    public function delete_by_product_id($product_id)
    {
        return $this->_delete_by_where('`product_id` = '.$product_id);
    }
    
    public function delete_by_category_id($category_id)
    {
        return $this->_delete_by_where('`category_id` = '.$category_id);
    }
}
