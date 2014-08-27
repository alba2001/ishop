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


/**
 * View to edit
 */
class IshopViewOrders extends JViewLegacy {

    protected $model;
    protected $items;


    /**
     * Display the view
     */
    public function display($tpl = null) {
//        $usearch_data = JRequest::getVar('usearch_data', 'array');
        $this->model = $this->getModel();
        $this->items = $this->get('Items');
        

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        parent::display($tpl);
    }

   
}
