<?php
/**
 * @version		$Id: helper.php 1514 2012-03-06 10:20:04Z lefteris.kavadas $
 * @package		ISHOP
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_ishop'.DS.'helpers'.DS.'ishop.php');

jimport('incase.init');

class modIshopContentHelper
{

        /**
        * Создаем селект к БД продуктов
        * 
        * @author Konstantin Ovcharenko alba2001@meta.ua
        * @var database object
        * @return query object
        */
        private function _build_product_query(&$db)
        {

        	$query = $db->getQuery(TRUE);
        	$query->select('*');
        	$query->from('#__ishop_products');
                
        	return $query;
        }
        
        /**
         * Возвращает список товаров
         * @author Konstantin Ovcharenko alba2001@meta.ua
         * 
         * @var params object
         * @var string
         * @return object
         */
        public function getGoods($params)
        {
        	list($limit, $order) = $params;
        	$db = JFactory::getDbo();
        	$query = self::_build_product_query($db);
        	$query->order($order.' DESC ');
//			 var_dump((string)$query);
        	$db->setQuery($query,0,$limit);

        	$products = $db->loadObjectList();

        	foreach ($products as &$product)
        	{
                    self::_store_product_attributes($product);
        	}

        	return $products;
        }

        /**
         * Скидки
         * @param type $limit
         * @return type
         */
        public function getDisconts($limit)
        {
        	$db = JFactory::getDbo();
        	$query = self::_build_product_query($db);
        	$query->where('`cena_mag` > `cena_tut`');
        	$query->where('`cena_tut` <> 0');
        	$db->setQuery($query,0,$limit);
        	$products = $db->loadObjectList();
        	foreach ($products as &$product)
        	{
                    self::_store_product_attributes($product);
                }

        	return $products;
        }

        /**
         * В зависимости от флага (Новинки, рекомендованые)
         * @param string $flag
         * @param int $limit
         * @return type
         */
        public function getByFlag($flag,$limit)
        {
        	$db = JFactory::getDbo();
        	$query = self::_build_product_query($db);
        	$query->where('`'.$flag.'` = 1');
        	$query->orderby('RAND()');
        	$db->setQuery($query,0,$limit);
        	$products = $db->loadObjectList();
        	foreach ($products as &$product)
        	{
                    self::_store_product_attributes($product);
                }

        	return $products;
        }

        /**
         * Просмотренные товары
         * @param type $limit
         * @return type
         */
        public function getSeenProducts($limit)
        {
            $ret_products = array();
            // Get input cookie object
            $inputCookie  = JFactory::getApplication()->input->cookie;

            $seen_products = $inputCookie->get('seen_products', '');
            if(!$seen_products)
            {
                $seen_products = array();
            }
            else
            {
                $seen_products = explode('prod',$seen_products);
            }
            
            if($seen_products)
            {
            
        	$db = JFactory::getDbo();
        	$query = self::_build_product_query($db);
                $query->where('`id` IN ('.  implode(',', $seen_products).')');
        	$db->setQuery($query,0,$limit);
        	$products = $db->loadObjectList();
                foreach($seen_products as $seen_product)
                {
                    foreach ($products as $product)
                    {
                        if($product->id == $seen_product)
                        {
                            self::_store_product_attributes($product);
                            $ret_products[] = $product;
                        }
                    }
                }
            }
            return $ret_products;
        }

        private function _get_cena_tut($product_id)
        {
            $price = ComponentHelper::getPrices($product_id);
            return $price['cena_tut'];
        }

        private function _store_product_attributes(&$product)
        {
            $desc = json_decode($product->dopinfo);
            $product->link = IshopHelper::getURI($product->id);
            $product->title = $product->name;
            
            $product->image = incase::thumb($desc->img_large, $product->id, 100, 100);
            $product->cena_tut = self::_get_cena_tut($product->id);
            $product->label = ComponentHelper::getProductLabel($product);
        }
}
