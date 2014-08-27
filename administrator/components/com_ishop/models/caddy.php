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

//jimport('joomla.application.component.model');
require_once dirname(__FILE__) . '/kmodelform.php'; 
require_once JPATH_ROOT.'/administrator/components/com_ishop/helpers/component.php';
/**
 * Ishop model.
 */
//class IshopModelCaddy extends JModel
class IshopModelCaddy extends ModelKModelform
{
    
    /**
     * Добавить новый заказ
     * @return bolean
     */
    public function order_add()
    {
        $caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
        $user = $this->getUser();
        $caddy_zakaz = JFactory::getApplication()->getUserState('com_ishop.zakaz', array());
        $caddy_data = $this->get_caddy_data($caddy);
        $data = array(
            'userid'=>$user->id,
            'order_status_id'=>'1',
            'order_dt'=>JFactory::getDate()->toMySQL(),
            'sum'=>$caddy_data['sum'],
            'caddy'=>  json_encode($caddy),
            'ch_status'=> date('d.m.Y H:i:s').' '.$user->fam.' '.$user->im.' '.$user->ot.' '.JText::_('COM_ISHOP_CHANGE_ORDER_STATUS_TO_INITIAL'),
            'oplata_id' => $caddy_zakaz['oplata'],
            'dostavka_id' => $caddy_zakaz['dostavka'],
        );
        $order = $this->getTable('order');
        // Cохраняем заказ
        if($order->save($data))
        {
            // Отправляем мыло
            $mail_send =& $this->_send_email($order, $user);
            JFactory::getApplication()->setUserState('com_ishop.caddy', array());
                $msg =  JTEXT::_('COM_ISHOP_ORDER_SAVER');
        }
        else
        {
            $order->id = 0;
            $msg =  JTEXT::_('COM_ISHOP_ERROR_SAVE_ORDER');
        }
        return  array($order->id,$msg);
    }
    
    /**
     * Отправка уведомлений о заказе
     * @param object $data Детали заказа
     * @param object $user Детали заказчика
     */
    private function _send_email($order, $user)
    {
        $mailer =& JFactory::getMailer();
        // Отправитель
        $config =& JFactory::getConfig();
        $sender = array( 
            $config->getValue( 'config.mailfrom' ),
            $config->getValue( 'config.fromname' ) 
        );
        $sitename = $config->getValue( 'config.sitename' );
        $mailer->setSender($sender);
        
        // Получатель
        $recipient = $user->email;
        $mailer->addRecipient($recipient);
        
        // Тело письма
        $caddy = json_decode($order->caddy, TRUE);
        $body_head   = '<h1>Заказ №'.$order->id.'</h1>';
        $body_head .= '<table style="border: 1px solid">';
        $body_head .= '<tr>';
        $body_head .= '<th>Выбранные товары</th>';
        $body_head .= '<th>Количество</th>';
        $body_head .= '<th>Размер</th>';
        $body_head .= '<th>Цена</th>';
        $body_head .= '<th>Сумма</th>';
        $body_head .= '</tr>';
        
        $body = '';
        foreach ($caddy as $key=>$value)
        {
            $cids = explode('_', $key);
            $id = $cids[0];
            $razmer_key = $cids[1];
            
            $product_data = $this->_get_product_data($id, $razmer_key);
            $prises = ComponentHelper::getPrices($id, $razmer_key);
            $body .= '<tr>';
            $body .= '<td><img src="'.$product_data['img_src'].'"/><b>'
                    .$product_data['artikul'].'</b></td>';
            $body .= '<td>'.$value['count'].'</td>';
            $body .= '<td>'.$product_data['razmer'].'</td>';
            $client_cena = (int)$prises['cena_tut']?$prises['cena_tut']:JTEXT::_('COM_ISHOP_MANAGER_CENA');
            $body .= '<td>'.$client_cena.'</td>';
            $body .= '<td>'.$value['sum'].'</td>';
            $body .= '</tr>';
        }
        $body_foot = '</tr>';
        $body_foot .= '<td colspan="4">Итого: '.$order->sum.'руб.</td>';
        $body_foot .= '<tr>';
        $body_foot .= '</table>';
        $body_foot .= '<b>Способ оплаты: </b>'.$this->_get_oplata_name($order->oplata_id);
        $body_foot .= '<br>';
        $body_foot .= '<b>Способ доставки: </b>'.$this->_get_dostavka_name($order->dostavka_id);
        $body_client = $body_head.$body.$body_foot;
        $mailer->setBody($body_client);
        
        // Тема письма
        $subject = 'Заказ №'.$order->id.' на сайте '.$sitename;
        $mailer->setSubject($subject);
        
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
     
        // Отправка письма заказчику
        $send_to_client = $mailer->Send();
        
        // Отправляем второе письмо менеджеру
        unset($mailer);
        $mailer =& JFactory::getMailer();
        $mailer->setSender($sender);
        $mailer->addRecipient($sender);
        $mailer->setSubject($subject);
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';

        $body = '';
        foreach ($caddy as $key=>$value)
        {
            $cids = explode('_', $key);
            $id = $cids[0];
            $razmer_key = $cids[1];
            
            $product_data = $this->_get_product_data($id, $razmer_key);
            $prises = ComponentHelper::getPrices($id, $razmer_key);
            $body .= '<tr>';
            $body .= '<td><img src="'.$product_data['img_src'].'"/><b>'
                    .$product_data['artikul'].'</b></td>';
            $body .= '<td>'.$value['count'].'</td>';
            $body .= '<td>'.$product_data['razmer'].'</td>';
            $body .= '<td>'.$prises['cena_manager'].'</td>';
            $body .= '<td>'.$value['sum'].'</td>';
            $body .= '</tr>';
        }

        $user_data = '<b>ФИО: </b>'.$user->fam.' '.$user->im.' '.$user->ot.'<br>';
        $user_data .= '<b>Почтовый адрес: </b>'.$user->address.'<br>';
        $user_data .= '<b>Телефон: </b>'.$user->phone.'<br>';
        $user_data .= '<b>email: </b>'.$user->email.'<br>';
        $body_manager = $user_data.$body_head.$body.$body_foot;
        $mailer->setBody($body_manager);
        
        return $mailer->Send() AND $send_to_client;
    }
    
    /**
     * Наименование оплаты
     * @param int $oplata_id
     * @return string
     */
    private function _get_oplata_name($oplata_id)
    {
        $oplata_name = '';
        $oplata = $this->getTable('Oplata');
        
        if($oplata->load(array('id'=>$oplata_id)))
        {
            $oplata_name = $oplata->name;
        }
        return $oplata_name;
    }

    /**
     * Наименование доставки
     * @param int $dostavka_id
     * @return string
     */
    private function _get_dostavka_name($dostavka_id)
    {
        $dostavka_name = '';
        $dostavka = $this->getTable('Dostavka');
        if($dostavka->load($dostavka_id))
        {
            $dostavka_name = $dostavka->name;
        }
        return $dostavka_name;
    }

    /**
     * Детали продукта
     * @param int $id ИД продукта
     * @param int $razmer_key ключ размера продукта
     * @return array Детали продукта
     */
    private function _get_product_data($id, $razmer_key=0)
    {
        $prises = ComponentHelper::getPrices($id, $razmer_key);
        $result = array(
               'artikul'=>'',
               'img_src'=>'',
               'cena_tut'=>'',
               'razmer'=>''
           );
       $product = $this->getTable('Product');
       if($product->load($id))
       {
           $desc = json_decode($product->desc);
           $razmers = explode(',', $product->razmer);
           $result = array(
               'artikul'=>$product->artikul,
               'img_src'=>$desc->img_small,
               'cena_tut'=>$prises['cena_tut'],
               'razmer'=>$razmers[$razmer_key],
           );
       }
       return $result;
    }

    /**
     * Добавить товара в корзину
     * @return type 
     */
    public function add()
    {
        $item_id = JRequest::getInt('item_id');
        $razmer_key = JRequest::getInt('razmer_key', 0);
        
        $prises = ComponentHelper::getPrices($item_id, $razmer_key);
        
        // Проверк на наличие ИД товара
        if(!$item_id)
        {
            return array(0,  JText::_('COM_ISHOP_DATA_NOT_MATCH'));
        }
        
        $product = $this->getTable('product');
        
        // Если товар не найден, возвращаем ошибку
        if(!$product->load($item_id))
        {
            return array(0,  JText::_('COM_ISHOP_ITEM_NOT_EXIST'));
        }
        
        $caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
        
        // Добавляем товар в корзину
        if(!$this->_check_product_in_caddy($caddy, $item_id, $razmer_key))
        {
            $caddy[$product->id.'_'.$razmer_key]['count'] = 1;
            $caddy[$product->id.'_'.$razmer_key]['sum'] = $prises['cena_tut'];
        }
        else
        {
            $caddy[$product->id.'_'.$razmer_key]['count']++;
            $caddy[$product->id.'_'.$razmer_key]['sum'] += $prises['cena_tut'];
        }
        JFactory::getApplication()->setUserState('com_ishop.caddy', $caddy);
//        var_dump($_SESSION);
//        echo '<hr/>';
//        var_dump($caddy);exit;
        
        return array(1, $this->get_caddy_data($caddy));
    }

    /**
     * Проверяем наличие товара с заданным размером в корзине
     * @param caddy $caddy
     * @param int $item_id
     * @param int $razmer_key 
     * @return bolean
     */
    private function _check_product_in_caddy($caddy, $item_id, $razmer_key)
    {
        if(isset($caddy[$item_id.'_'.$razmer_key]))
        {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Удалить товара из корзины
     * @return type 
     */
    public function del()
    {
        $item_id = JRequest::getInt('item_id');
        $razmer_key = JRequest::getInt('razmer_key', 0);
        
        $prises = ComponentHelper::getPrices($item_id, $razmer_key);
        
        // Проверк на наличие ИД завода и ИД товара
        if(!$item_id)
        {
            return array(0,  JText::_('COM_ISHOP_DATA_NOT_MATCH'));
        }
        $product = $this->getTable('product');
        
        // Если товар не найден, возвращаем ошибку
        if(!$product->load($item_id))
        {
            return array(0,  JText::_('COM_ISHOP_ITEM_NOT_EXIST'));
        }
        $caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
//        unset($caddy);
        // Удаляем товар из корзины
        if($this->_check_product_in_caddy($caddy, $item_id, $razmer_key))
        {
            $caddy[$product->id.'_'.$razmer_key]['count']--;
            $caddy[$product->id.'_'.$razmer_key]['sum'] -= $prises['cena_tut'];
            if((int)$caddy[$product->id.'_'.$razmer_key]['count'] < 1)
            {
                unset($caddy[$product->id.'_'.$razmer_key]);
            }
        }
        JFactory::getApplication()->setUserState('com_ishop.caddy', $caddy);
        
        return array(1, $this->get_caddy_data($caddy));
    }

    /**
     * Итоговые данные по корзине
     * @param type $caddy
     * @return type 
     */
    public function get_caddy_data($caddy)
    {
        $sum = 0;
        $count = 0;
        foreach ($caddy as $row)
        {
            $sum += $row['sum']; 
            $count += $row['count']; 
        }
        return array(
            'sum'=>$sum,
            'count'=>$count,
        );
    }

    /**
     * Возвращаем таблицу
     * @param type $type
     * @param type $prefix
     * @param type $config
     * @return type 
     */
    public function getTable($type = 'Caddy', $prefix = 'IshopTable', $config = array())
	{   
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
        return JTable::getInstance($type, $prefix, $config);
	}     
 
        /**
         * Список товаров в корзине
         * @return array 
         */
        public function getItems($caddy=NULL)
        {
            if(!isset($caddy))
            {
                $caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
            }
            $items = array();
            foreach ($caddy as $cid=>$value)
            {
                $cids = explode('_', $cid);
                $id = $cids[0];
                $razmer_key = $cids[1];
                $product = $this->getTable('product');
                if($product->load($id))
                {
                    $prises = ComponentHelper::getPrices($id, $razmer_key);
                    $site = $this->getTable('site');
                    $site_name = $site->load($product->site_id)?$site->name:'';
                    $desc = json_decode($product->desc);
                    $razmers = explode(',', $product->razmer);
                    $item = array(
                        'id'=>$id,
                        'site_name'=>$site_name,
                        'site_id'=>$product->site_id,
                        'artikul'=>$product->artikul,
                        'razmer_key'=>$razmer_key,
                        'razmer'=>$razmers[$razmer_key],
                        'name'=>$product->name,
                        'src'=>$desc->img_small,
                        'price'=>$prises['cena_tut'],
                        'count'=>$value['count'],
                        'sum'=>$value['sum'],
                        'path'=>$this->_get_path($product->category_id),
                    );
                    $items[] = $item;
                }
            }
            return $items;
        }
        
        /**
         *  Возвращаем путь к категории
         * @param int - ID категории
         * @return string Путь к категории
         */
        private function _get_path($id)
        {
            $result = '';
            $category = JTable::getInstance('Category', 'IshopTable', array());
            if($category->load($id))
            {
                $result = $category->path;
            }
            return $result;
        }

    /**
     * Пересчет товара в корзине
     * @return type 
     */
    public function correction()
    {
        $get_counts = JRequest::getVar('count', array());
        
        // Проверк на наличие количества товаров и их размеров
        if(!($get_counts))
        {
            return array(0,  JText::_('COM_ISHOP_DATA_NOT_MATCH'));
        }
        $counts = explode(' ', $get_counts);
        $caddy = array();
        foreach($counts as $count_item)
        {
            $parts = explode(':', $count_item);
            $cids = explode('_', $parts[0]);
            $item_id = $cids[0];
            $razmer_key = $cids[1];
            $count = $parts[1];
//            var_dump($parts, $cids, $item_id, $razmer_key, $count);
//            exit;
            $product = $this->getTable('product');
            if($product->load($item_id))
            {
                $prises = ComponentHelper::getPrices($item_id, $razmer_key);
                $caddy[$item_id.'_'.$razmer_key]['count'] = $count;
                $caddy[$item_id.'_'.$razmer_key]['sum'] = $prises['cena_tut']*$count;
                if((int)$caddy[$item_id.'_'.$razmer_key]['count'] < 1)
                {
                    unset($caddy[$item_id.'_'.$razmer_key]);
                }
            }
        }
        JFactory::getApplication()->setUserState('com_ishop.caddy', $caddy);
        
        return array(1, $this->get_caddy_data($caddy));
    }
    
    /**
     * Записываем способ доставки 
     */
    public function dostavka_submit()
    {
        $dostavka = JRequest::getInt('dostavka','1');
        $caddy_zakaz = JFactory::getApplication()->getUserState('com_ishop.zakaz', array());
        $caddy_zakaz['dostavka'] = $dostavka;
        JFactory::getApplication()->setUserState('com_ishop.zakaz', $caddy_zakaz);
        return array(1,'');
    }
    /**
     * Записываем способ оплаты 
     */
    public function oplata_submit()
    {
        $oplata = JRequest::getInt('oplata','1');
        $caddy_zakaz = JFactory::getApplication()->getUserState('com_ishop.zakaz', array());
        $caddy_zakaz['oplata'] = $oplata;
        JFactory::getApplication()->setUserState('com_ishop.zakaz', $caddy_zakaz);
        return array(1,'');
    }
    
    /**
     * Получение наименования способа доставки
     * @param int $id
     * @return string
     */
    public function get_dostavka($id)
    {
        $name = '';
        $table = $this->getTable('dostavka');
        if($table->load($id))
        {
            $name = $table->name;;
        }
        return $name;
    }
    
    /**
     * Получение наименования способа оплаты
     * @param int $id
     * @return string
     */
    public function get_oplata($id)
    {
        $name = '';
        $table = $this->getTable('oplata');
        if($table->load($id))
        {
            $name = $table->name;;
        }
        return $name;
    }
}