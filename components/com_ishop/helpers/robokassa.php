<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

// No direct access.
defined('_JEXEC') or die;

//        public $html = "<html><script language=JavaScript ".
//            "src='https://merchant.roboxchange.com/Handler/MrchSumPreview.ashx?".
//            "MrchLogin=$mrh_login&OutSum=$out_summ&InvId=$inv_id&IncCurrLabel=$in_curr".
//            "&Desc=$inv_desc&SignatureValue=$crc&Shp_item=$shp_item".
//            "&Culture=$culture&Encoding=$encoding'></script></html>";
//        

/**
 * Robokassa class.
 */
class Robokassa extends JModelLegacy
{


    // 1.
    // Оплата заданной суммы с выбором валюты на сайте мерчанта
    // Payment of the set sum with a choice of currency on merchant site 

    // регистрационная информация (логин, пароль #1)
    // registration info (login, password #1)
    private $_mrh_login = "demo"; 
    
    private $_mrh_pass1 = "password_1";

    // номер заказа
    // number of order
    private $_inv_id = 0;

    // описание заказа
    // order description
    private $_inv_desc = "ROBOKASSA Advanced User Guide";

    // сумма заказа
    // sum of order
    private $_out_summ = "0.00";

    // тип товара
    // code of goods
    private $_shp_item = 1;

    // предлагаемая валюта платежа
    // default payment e-currency
    private $_in_curr = "";

    // язык
    // language
    private $_culture = "ru";

    // кодировка
    // encoding
    private $_encoding = "utf-8";

    // HTML-страница с кассой
//    private $_src = 'https://merchant.roboxchange.com/Handler/MrchSumPreview.ashx?';
    private $_src = 'https://auth.robokassa.ru/Merchant/Index.aspx?';
    
    // Подпись при отправке оплаты
    private $_crc;
    
    // Подпись при уведомлении об оплате
    private $_crc_succes;


    public function __construct($config = array()) {
        $this->_packet_set($config);
        $this->_set_crc();
    }

    /**
     * формирование подписи
     * @return type
     */
    private function _set_crc()
    {
        $this->_crc =  md5("$this->_mrh_login:$this->_out_summ:$this->_inv_id:$this->_mrh_pass1:Shp_item=$this->_shp_item");
    }

    /**
     * формирование подписи
     * @return type
     */
    public function get_crc()
    {
        return strtoupper(md5("$this->_out_summ:$this->_inv_id:$this->_mrh_pass2:Shp_item=$this->_shp_item"));
    }

    /**
     * Получение готовго адреса
     * @return type
     */
    public function get_url()
    {
        
        // HTML-страница с кассой
        $url = $this->_src;
        
        // ROBOKASSA HTML-parameters
        $params = array();
        $params[] = 'MrchLogin='.$this->_mrh_login;
        $params[] = 'OutSum='.$this->_out_summ;
        $params[] = 'InvId='.$this->_inv_id;
        $params[] = 'IncCurrLabel='.$this->_in_curr;
        $params[] = 'Desc='.$this->_inv_desc;
        $params[] = 'SignatureValue='.$this->_crc;
        $params[] = 'Shp_item='.$this->_shp_item;
        $params[] = 'Culture='.$this->_culture;
        $params[] = 'Encoding='.$this->_encoding;
        
        return $url.implode('&', $params);
        
    }
    
    /**
     * Возвращаем значения переменных
     * @param string $name
     * @return variant
     */
    public function __get($name) {
        if(method_exists($this, '_get'.$name))
        {
            return call_user_func(array($this, '_get'.$name), $value);
        }
        else
        {
            return $this->$name;
        }
    }

    
    /**
     * Устанавливаем значения переменных
     * @param string $name
     * @param string $value
     * @return noting
     */
    public function __set($name, $value) {
        if(method_exists($this, '_set'.$name))
        {
            return call_user_func(array($this, '_set'.$name), $value);
        }
        else
        {
            $this->$name = $value;
        }
    }
    
    /**
     * Пакетная установка переменных
     * @param array $keys
     */
    private function _packet_set($keys)
    {
        if(is_array($keys))
        {
            foreach ($keys as $name=>$value)
            {
                $this->__set($name, $value);
            }
        }
    }
    /**
     * Пакетная выдача значений переменных
     * @param array $keys
     * @return array Description
     */
    public function packet_get($keys)
    {
        $result = array();
        if(is_array($keys))
        {
            foreach ($keys as $name)
            {
                $result[$name] = $this->__get($name);
            }
        }
        return $result;
    }
    
    /**
     * Установка адреса на тестовый сервер
     * если используется тестовый режим
     * @param type $test
     */
    private function _set_src($test_mode=FALSE)
    {
        if($test_mode)
        {
            $this->_src = 'http://test.robokassa.ru/Index.aspx?';
        }
    }
    
    /**
     * Перекодируем сообщение для адресной строки
     * @param type $inv_desc
     */
    private function _set_inv_desc($inv_desc)
    {
        $this->_inv_desc = urlencode($inv_desc);
    }
}