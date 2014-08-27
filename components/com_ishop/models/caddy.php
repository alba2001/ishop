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
     * Возвращаем товары, покупаемые с этим товаром
     * @param type $product_id
     * @return array 
     */
    public function get_purchases($product_id)
    {
       $purchases = array();
       $product = $this->getTable('Product');
       if($product->load($product_id))
       {
           foreach($product->purchases as $purchase)
           {
               $desc = json_decode($purchase->desc);
               $purchases[] = array(
                   'id'=>$purchase->id,
                   'name'=>$purchase->name,
                   'artikul'=>$purchase->artikul,
                   'img_src'=>$desc->img_small,
                   'price'=>$purchase->cena_tut,
                   );
           }
       }
       return $purchases;
   }


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
            'order_dt'=>JFactory::getDate()->toSql(),
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
            // Заполняем таблицу: С этим товаром покупают...
            $this->_fill_purchases($caddy);

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
     * Заполнение таблицы: С этим товаром покупают...
     * @param object caddy $caddy
     * @return boolean 
     */
    private function _fill_purchases($caddy)
    {
        $result = TRUE;
        $cids = array_keys($caddy);
        $ids = array();
        foreach ($cids as $cid)
        {
            $ids[] = $cid[0];
        }
        foreach ($ids as $product_id)
        {
            foreach ($ids as $purchase_id)
            {
                if($product_id != $purchase_id)
                {
                    $data = array(
                     'product_id' => $product_id,
                     'purchase_id' => $purchase_id,
                     );
                    $table = $this->getTable('Product_Purchase');
                    if(!$table->save($data))
                    {
                        $result = FALSE;
                    }
                }
            }
        }
        return TRUE;
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
                $config->get( 'config.mailfrom' ),
                $config->get( 'config.fromname' ) 
                );
            
            $sitename = $config->get( 'config.sitename' );
            $mailer->setSender($sender);
            
        // Получатель
            $recipient = $user->email;
            $mailer->addRecipient($recipient);
            
            $css_table = ' style="
            border-spacing: 4px; 
            border: none; width: 100%; 
            text-align: center; 
            vertical-align: middle;
            "';

            $css_th = ' style="
            background: #C836AE;
            color: #fff;
            padding: 6px 20px;
            width: 20%;
            "';

            $css_td = ' style="
            background: #F7EEF5;
            color: #444;
            padding: 6px 20px;
            "';

        // Тело письма
            $caddy = json_decode($order->caddy, TRUE);
            
            $body_head  = '';
            $body_head .= '<img align="eww3333" SRCs="wewew"/><h1>Заказ №'.$order->id.'</h1>';
            $body_head .= '<table'.$css_table.'>';
            $body_head .= '<tr>';
            $body_head .= '<th'.$css_th.'>Выбранные товары</th>';
            $body_head .= '<th'.$css_th.'>Количество</th>';
        // $body_head .= '<th>Размер</th>';
            $body_head .= '<th'.$css_th.'>Цена</th>';
            $body_head .= '<th'.$css_th.'>Сумма</th>';
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
//            $body .= '<td'.$css_td.'><img src="'.$product_data['img_src'].'" />';
                $body .= '<td style="background-image: url(http://www.kazanova.su/images/cat_position/images_upload/small/9700.jpg)"><img src="'.$product_data['img_src'].'" />';
                $body .= '</br></br><b>'.$product_data['artikul'].'</b></td>';
                $body .= '<td'.$css_td.'>'.$value['count'].'</td>';
            // $body .= '<td>'.$product_data['razmer'].'</td>';
                $client_cena = (int)$prises['cena_tut']?$prises['cena_tut']:JTEXT::_('COM_ISHOP_MANAGER_CENA');
                $body .= '<td'.$css_td.'>'.$client_cena.'</td>';
                $body .= '<td'.$css_td.'>'.$value['sum'].'</td>';
                $body .= '</tr>';
            }
            $body_foot = '</tr>';
            $body_foot .= '<td colspan="3"'.$css_td.'></td>';
            $body_foot .= '<td'.$css_td.'>Итого: '.$order->sum.' руб.</td>';
            $body_foot .= '<tr>';
            $body_foot .= '</table>';
            $body_foot .= '<p><b>Способ оплаты: </b></p>'.$this->_get_oplata_name($order->oplata_id);
            $body_foot .= '<p><b>Способ доставки: </b></p>'.$this->_get_dostavka_name($order->dostavka_id);
            $body_client = $body_head.$body.$body_foot;
            $mailer->setBody('<html><head></head><body>'.$body_client.'</body></html>');
            
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
                $body .= '<td'.$css_td.'><img src="'.$product_data['img_src'].'"/></br></br><b>'
                .$product_data['artikul'].'</b></td>';
                $body .= '<td'.$css_td.'>'.$value['count'].'</td>';
            // $body .= '<td>'.$product_data['razmer'].'</td>';
                $body .= '<td'.$css_td.'>'.$prises['cena_manager'].'</td>';
                $body .= '<td'.$css_td.'>'.$value['sum'].'</td>';
                $body .= '</tr>';
            }

            $user_data = '<p><b>ФИО: </b>'.$user->fam.' '.$user->im.' '.$user->ot.'</p>';
            $user_data .= '<p><b>Почтовый адрес: </b>'.$user->address.'</p>';
            $user_data .= '<p><b>Телефон: </b>'.$user->phone.'</p>';
            $user_data .= '<p><b>email: </b>'.$user->email.'</p>';

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
        $dir_dest = JPATH_ROOT.'/media/com_ishop/images/img_small/';
        $url_dest = JURI::base().'media/com_ishop/images/img_small/';
        
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
            $src = $desc->img_small;
            $ar_path = explode('.', $src);
            $ext = $ar_path[count($ar_path)-1];
            $file_dest = $dir_dest.$id.'.'.$ext;
            if(file_exists($file_dest))
            {
                $src = $url_dest.$id.'.'.$ext;
            }

            $razmers = explode(',', $product->razmer);
            $result = array(
             'artikul'=>$product->artikul,
             'img_src'=>$src,
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
                    $desc = json_decode($product->desc);
                    $item = array(
                        'id'=>$id,
                        'artikul'=>$product->artikul,
                        'razmer_key'=>$razmer_key,
                        'name'=>$product->name,
                        'src'=>$desc->img_small,
                        'price'=>$prises['cena_tut'],
                        'count'=>$value['count'],
                        'sum'=>$value['sum'],
                        'path'=>$this->_get_path($product->id),
                        'purchases'=>$this->get_purchases($product->id),
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
        private function _get_path($product_id)
        {
            $result = '';
            $product_category = JTable::getInstance('Product_Category', 'IshopTable', array());
            if($product_category->load($product_id))
            {
                $category_id = $product_category->category_id;
                $category = JTable::getInstance('Category', 'IshopTable', array());
                if($category->load($category_id))
                {
                    $result = $category->path;
                }
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
            $name = $table->name;
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
            $name = $table->name;
        }
        return $name;
    }
    
    /**
     * Список способов оплат
     * @return object
     */
    public function getOplatas()
    {
        $query = $this->_db->getQuery(TRUE);
        $query->select('*');
        $query->from('#__ishop_oplata');
        $this->_db->setQuery($query);
        
        return $this->_db->loadObjectList();
    }
    
    /**
     * Список способов оплат
     * @return object
     */
    public function getDostavkas($oplata_id=0)
    {
        $query = $this->_db->getQuery(TRUE);
        $query->select($this->_db->quoteName('dostavka.id'));
        $query->select($this->_db->quoteName('dostavka.name'));
//        $query->select($this->_db->quoteName('od.desc'));
        $query->from('#__ishop_dostavka AS dostavka');
//        $query->join('INNER','#__ishop_oplata_dostavka AS od ON od.dostavka_id = dostavka.id');
//        $query->where('od.oplata_id = '.$oplata_id);
        $this->_db->setQuery($query);
        
        return $this->_db->loadObjectList();
    }
    
    /**
     * Из группы товаров выбираем уникальные товары, которые покупались с этим товаром
     * @return object list 
     */
    public function getPurchases()
    {
        $items = $this->getItems();
        $purchases = array();
        $item_keys = array();
        // Формируем уникальный список
        foreach($items as $item)
        {
            $item_keys[] = $item['id'];
            foreach($item['purchases'] as $purchase)
            {
                if(!array_key_exists($purchase['id'], $purchases))
                {
                    $purchases[$purchase['id']] = $purchase;
                }
            }
        }
        
        // Если в списке есть товары, присутствующие в этой корзине, то их убираем
        foreach ($item_keys as $key)
        {
            if(isset($purchases[$key]))
            {
                unset($purchases[$key]);
            }
        }
        return $purchases;
    }
    
}