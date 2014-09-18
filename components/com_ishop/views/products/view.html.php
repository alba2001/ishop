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

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/component.php';

/**
 * View to edit
 */
class IshopViewProducts extends JViewLegacy {

    protected $state;
    protected $_model;
    protected $pagination;
    protected $items;


    /**
     * Display the view
     */
    public function display($tpl = null) {
//        $usearch_data = JRequest::getVar('usearch_data', 'array');
        $this->_model = $this->getModel();
        $this->items = $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
        $this->sort_order_products_list = $this->sort_order_select();
        $this->is_search_result = JRequest::getVar('ishop_search_data')?TRUE:FALSE;
        

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        
        $this->_prepareDocument();

        parent::display($tpl);
    }


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
	}
        
        /**
         * Строим селект сортировки товаров
         * @return JHTML object
         */
        protected function sort_order_select()
        {
            $selected = $this->_model->getState('sort_order_products',0);
            return IshopHelper::PoductOrderList($selected, array('onchange'=>'this.form.submit()'));
        }
   
   
}
