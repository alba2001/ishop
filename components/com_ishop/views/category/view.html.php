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
class IshopViewCategory extends JViewLegacy {

    protected $state;
    protected $item;
    protected $params;
    protected $_model;
    protected $pagination;
    protected $caddy;


    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->_model = $this->getModel();
        $this->item = $this->get('Item');
        $this->items = $this->get('Items');
        $this->children = $this->get('Children');
        $this->pagination	= $this->get('Pagination');
        $this->caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
        $this->products_group = 0;

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
                $doc = JFactory::getDocument();
		$doc->setTitle($this->item->name);
	}
   
}
