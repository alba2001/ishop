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
class IshopViewCaddy extends JViewLegacy {

    protected $items;
    protected $caddy_data;
    protected $zakaz;


    /**
     * Display the view
     */
    public function display($tpl = null) {
        
        $this->action = JRequest::getString('action', 'step1');
        if($this->action == 'step4') // Checkout
        {
            $mainframe = JFactory::getApplication();
            $mainframe->setUserState('com_ishop.old_uri',JUri::base().'zavershenie-zakaza');
            // Проверяем пользователя
            $this->user = $this->get('User');
            if(!$this->user->id)
            {
                // Redirect to login
                $url = JURI::base().'lichnye-dannye';
                $mainframe->redirect($url, JText::_('You must login first'));
            }
            
        }
        
        $this->items = $this->get('Items');
        $this->purchases = $this->get('Purchases');
        $model = $this->getModel();
        $caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
        $this->zakaz = JFactory::getApplication()->getUserState('com_ishop.zakaz', array());
        $this->caddy_data = $model->get_caddy_data($caddy);
        if($this->action == 'step2') // Способы оплаты
        {
            $this->oplatas = $model->getOplatas();
        }
        if($this->action == 'step3') // Способы доставки
        {
            $this->dostavkas = $model->getDostavkas($this->zakaz['oplata']);
        }

        // Договор-соглашение с коиентом
        $this->user_sogl = JComponentHelper::getParams('com_ishop')->get('user_sogl');
        
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
