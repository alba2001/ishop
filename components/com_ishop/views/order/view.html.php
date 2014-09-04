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
require_once 'components/com_ishop/helpers/robokassa.php'; 
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/component.php';

/**
 * View to edit
 */
class IshopViewOrder extends JViewLegacy {

    protected $items;
    protected $total_sum=0;
    protected $model;
    protected $sposob_oplaty;
    protected $sposob_dostavki;
    protected $products;


    /**
     * Display the view
     */
    public function display($tpl = null) {

        // Проверяем пользователя
        $this->user = $this->get('User');
        if(!$this->user->id)
        {
            $mainframe = JFactory::getApplication();
            // Redirect to login
            $url = JRoute::_(JURI::base().'index.php');
            $mainframe->redirect($url, JText::_('You must login first'));

        }
        
        $this->model = $this->getModel();
        $this->item = $this->get('Item');
        
        
        // Способ оплаты
        $oplata = $this->model->get_row('Oplata', $this->item->oplata_id);
        $this->sposob_oplaty = $oplata?$oplata->name:'';
        
        // Способ доставки
        $dostavka = $this->model->get_row('Dostavka', $this->item->dostavka_id);
        $this->sposob_dostavki = $dostavka?$dostavka->name:'';
        
        // Список товаров
        $products = json_decode($this->item->caddy,TRUE);
        $this->products = array();
        foreach($products as $key=>$value)
        {
            $product = $this->model->get_row('Product', $key);
            $desc = json_decode($product->dopinfo);
            $this->products[] = array(
                'id'=>$product->id,
                'name'=>$product->name,
                'artikul'=>$product->artikul,
                'img_src'=>$desc->img_large,
                'price'=>$product->cena_tut,
                'count' => $value['count'],
                'sum' => $value['sum'],
                'purchases' => $this->model->get_purchases($product->id),
            );
            $this->total_sum += $value['sum'];
        }

        // Если статус заказа начальный, получаем URL для кнопки оплаты
        if($this->item->order_status_id == '1')
        {
            // Робокасса
            $params = JFactory::getApplication()->getParams('com_ishop');
            $r_config = array(
                '_mrh_login'=>  $params->get('RobokassaMrchLogin'),
                '_mrh_pass1'=>  $params->get('RobokassaMrchPassw1'),
                '_src'=>        $params->get('RobokassaTestMode'),
                '_inv_id'=>     $this->item->id,
                '_inv_desc'=>   JText::_('COM_ISHOP_INV_DESC').$this->item->id,
                '_out_summ'=>   ComponentHelper::getCheckoutSum($this->total_sum)
            );
            $robokassa = new Robokassa($r_config);
            $this->robokassa_href = $robokassa->get_url();
        }
        
        
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
		$doc->setTitle(JText::_('COM_ISHOP_ORDER'));
	}
   
}
