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
class IshopViewProduct extends JViewLegacy {

    protected $state;
    protected $item;
    protected $_model;


    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->_model = $this->getModel();
        $this->item = $this->get('Item');
        $this->caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        
        $this->_prepareDocument();
        $this->_set_cookie_view();

        parent::display($tpl);
    }


	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
                $doc = JFactory::getDocument();
		$doc->setTitle($this->item->name);
                $doc->addStyleSheet(JURI::root()."components/com_ishop/assets/fancybox/jquery.fancybox-1.3.4.css");
	}
 
        /**
         * Установка кук в массив просмотренных товаров 
         * 
         */
        private function _set_cookie_view()
        {
//        // Get input cookie object
//        $inputCookie  = JFactory::getApplication()->input->cookie;
//
//        // Get cookie data
//        $value        = $inputCookie->get($name = 'myCookie', $defaultValue = null);
//
//        // Check that cookie exists
//        $cookieExists = ($value === null);
//
//        // Set cookie data
//        $inputCookie->set($name = 'myCookie', $value = '123', $expire = 0);
//
//        // Remove cookie
//        $inputCookie->set('myCookie', null, time() - 1);            
            $input_cookie  = JFactory::getApplication()->input->cookie;
            $you_views = $input_cookie->get('you_views', null);
            if($you_views AND is_array($you_views))
            {
//                $you_views = array_shift($this->item->id, json_decode($you_views, TRUE));
                $you_views = array_shift(json_decode($you_views, TRUE));
                if(isset($you_views[15]))
                {
                    unset($you_views[15]);
                }
            }
            else
            {
                $you_views = array($this->item->id);
            }
            $input_cookie->set('you_views', json_encode($you_views),0);
        }
}
