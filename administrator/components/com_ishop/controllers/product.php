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

jimport('joomla.application.component.controllerform');

/**
 * Company controller class.
 */
class IshopControllerProduct extends JControllerForm
{

    function __construct() {
        $this->view_list = 'products';
        parent::__construct();
    }
    
    /**
     * Заглушка родительского метода
     * @return boolean 
     */
    protected function checkEditId() {
        return TRUE;
    }
    
   /**
    * Добавление категории к товару
    */
    public function add_category()
    {
        JSession::checkToken() or die( 'Invalid Token' );
        $model = $this->getModel();
        $result = $model->add_category();
        echo json_encode($result);
        exit;
    }
    
   /**
    * Удаление категории к товару
    */
    public function remove_category()
    {
        JSession::checkToken() or die( 'Invalid Token' );
        $model = $this->getModel();
        $result = $model->remove_category();
        echo json_encode($result);
        exit;
    }
}