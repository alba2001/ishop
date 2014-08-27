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
        $this->products_group = (int) $this->_model->getState('products_group');
        $this->show_menu_groups = (bool) $this->_model->getState('show_menu_groups', TRUE);
        $this->pagination	= $this->get('Pagination');
        $this->caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());

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
   
}
