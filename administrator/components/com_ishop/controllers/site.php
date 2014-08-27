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
 * site controller class.
 */
class IshopControllerSite extends JControllerForm
{

    function __construct() {
        $this->view_list = 'sites';
        parent::__construct();
    }
    
    /**
     * Публикация
     */
    public function publish()
    {
        $cids = JRequest::getVar('cid',array(),'','array');
        $this->getModel()->publish($cids, 1);
        parent::display();
    }
   
    /**
     * Снятие с публикации 
     */
    public function unpublish()
    {
        $cids = JRequest::getVar('cid',array(),'','array');
        $this->getModel()->publish($cids, 0);
        parent::display();
    }

    /**
     * Парсинг заводов
     */
    public function parse()
    {
        // Check for request forgeries.
//        JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));

        $result = $this->getModel()->parse();
        echo json_encode($result);
        exit;
    }
}