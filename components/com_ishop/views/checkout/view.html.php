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
class IshopViewCheckout extends JViewLegacy {

    protected $items;
    protected $caddy_data;


    /**
     * Display the view
     */
    public function display($tpl = null) {

        // Проверяем пользователя
        $this->user = $this->get('User');
//        var_dump($this->user);exit;
        if(!$this->user->id)
        {
            $mainframe = JFactory::getApplication();
            // Redirect to login
            $mainframe->setUserState('com_ishop.old_uri',JURI::base().'index.php?option=com_ishop&view=checkout');
            $url = JRoute::_(JURI::base().'index.php?option=com_ishop&view=userform');
            $mainframe->redirect($url, JText::_('You must login first'));

        }
        
        $this->items = $this->get('Items');
        $model = $this->getModel();
        $caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
        $this->caddy_data = $model->get_caddy_data($caddy);

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
		$doc->setTitle(JText::_('COM_ISHOP_CADDY'));
	}
   
}
