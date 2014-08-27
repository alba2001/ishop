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

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Company controller class.
 */
class IshopControllerCaddy extends IshopController
{
    /**
     * Добавить товар в корзину 
     */
    function add()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Caddy');
        $result = json_encode($model->add());
        echo $result;
        exit;
    }
    /**
     * Добавить новый заказ 
     */
    function order_add()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Caddy');
        list($id, $msg) = $model->order_add();
        $url = JURI::base().'index.php?option=com_ishop&view=order&id='.$id;
        
        JFactory::getApplication()->redirect($url, $msg);
        
        return true;
    }
    /**
     * Удалить товар из корзины
     */
    function del()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Caddy');
        $result = json_encode($model->del());
        echo $result;
        exit;
    }
    
    /**
     * корректировка данных в корзине 
     */
    function correction()
    {
        // Check for request forgeries.
        JSession::checkToken('GET') or jexit(json_encode(array(0,JText::_('JINVALID_TOKEN'))));
        
        $model = $this->getModel('Caddy');
        $result = json_encode($model->correction());
        echo $result;
        exit;
    }
    
    public function show_catalog()
    {
        $app = JFactory::getApplication();
//        $url = $app->getUserState('com_ishop.catalog_uri',  JURI::base());
//        $app->setUserState('com_ishop.catalog_uri',NULL);
        $url = JURI::base().'katalog-izdelij';
//        var_dump($url);exit;
        $app->redirect($url);
        
    }

    /**
     * Записываем способ доставки 
     */
    public function dostavka_submit()
    {
        // Check for request forgeries.
        JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Caddy');
        $result = json_encode($model->dostavka_submit());
        echo $result;
        exit;
    }
    /**
     * Записываем способ доставки 
     */
    public function oplata_submit()
    {
        // Check for request forgeries.
        JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Caddy');
        $result = json_encode($model->oplata_submit());
        echo $result;
        exit;
    }
}