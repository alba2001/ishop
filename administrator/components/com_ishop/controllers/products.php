<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Products list controller class.
 */
class IshopControllerProducts extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'product', $prefix = 'IshopModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
        
        /**
         * Устанавливаем группе товаров в наличии
         */
        public function available_publish()
        {
            // Check for request forgeries
            JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
            
            $cid = JRequest::getVar('cid', array(), '', 'array');
            // Get the model.
            $model = $this->getModel('Products');

            $model->set_available($cid, 1);
            
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
        }
        
        /**
         * Устанавливаем группе товаров в не наличии
         */
        public function available_unpublish()
        {
            // Check for request forgeries
            JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
            
            $cid = JRequest::getVar('cid', array(), '', 'array');
            // Get the model.
            $model = $this->getModel('Products');

            $model->set_available($cid, 0);
            
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
        }
        
        /**
         * Устанавливаем товар в наличие
         */
        public function set_available()
        {
            // Check for request forgeries
            JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
            
            $cid = JRequest::getVar('cid', array(), '', 'array');
            // Get the model.
            $model = $this->getModel('Products');

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);
            
            echo (int)$model->set_available($cid, 1);
            exit;
        }
        
        /**
         * Снимаем товар с наличия 
         */
        public function unset_available()
        {
            // Check for request forgeries
            JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
            
            $cid = JRequest::getVar('cid', array(), '', 'array');
            // Get the model.
            $model = $this->getModel('Products');

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);
            
            echo (int)$model->set_available($cid, 0);
            exit;
        }
        
        public function fill_cenas()
        {
            $model = $this->getModel('Products');
            $model->fill_cenas();
            
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
        }
        
   /**
    * Групповое добавление категории к товарам
    */
    public function add_categories()
    {
        JSession::checkToken() or die( 'Invalid Token' );
        $model = $this->getModel('Products');
        $result = $model->add_categories();
        $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
    }
    
   /**
    * Групповое ндаление категории к товарам
    */
    public function rm_categories()
    {
        JSession::checkToken() or die( 'Invalid Token' );
        $model = $this->getModel('Products');
        $result = $model->rm_categories();
        $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));        
    }
        
}