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

/**
 * Ishop helper.
 */
class KhtmlHelper
{
        /**
        * Список уровней вложенности
        * @param int макс. уровень
        * @return array 
        */
	public static function levels($max_level = 11)
	{
            $levels = array();
            for($i=1; $i<$max_level; $i++)
            {
                $levels[] = array('text'=>$i, 'value'=>$i);
            }
            return $levels;
	}

        /**
        * Список заводов
        * @param noting
        * @return object list
        */
	public static function categories($site_alias = 'oriflame')
	{
            $db = JFactory::getDbo();
            $query	= $db->getQuery(true);

            // Select the required fields from the table.
            $query->select('`id` AS value, `name` AS text, level ')
                    ->from('`#__ishop_categories`')
                    ->where('`level` > 0')
                    ->order('lft');
            if($site_alias)
            {
                $query->where('`site_alias` = "'.$site_alias.'"');
            }
                    
            $db->setQuery($query);
            $categories = $db->loadObjectList();
            foreach($categories as &$category)
            {
                $category->text = str_repeat('|-----', ($category->level-1)).$category->text;
                unset($category->level);
            }
            
            return $categories;
	}

        /**
        * Список категорий с учетом завода
        * @param noting
        * @return object list
        */
	public static function site_aliases()
	{
            $db = JFactory::getDbo();
            $query	= $db->getQuery(true);

            // Select the required fields from the table.
            $query->select('`alias` AS value, `name` AS text')
                    ->from('`#__ishop_sites`');
            $db->setQuery($query);
            return $db->loadObjectList();
	}


        /**
        * Список вставок
        * @param noting
        * @return object list
        */
	public static function vstavkis()
	{
            $db = JFactory::getDbo();
            $query	= $db->getQuery(true);

            // Select the required fields from the table.
            $query->select('`id` AS value, `name` AS text')
                    ->from('`#__ishop_vstavkis`');
            $db->setQuery($query);
            return $db->loadObjectList();
	}

        /**
        * Список металлов
        * @param noting
        * @return object list
        */
	public static function metals()
	{
            $db = JFactory::getDbo();
            $query	= $db->getQuery(true);

            // Select the required fields from the table.
            $query->select('`id` AS value, `name` AS text')
                    ->from('`#__ishop_metals`');
            $db->setQuery($query);
            return $db->loadObjectList();
	}

        /**
        * Статус заказа
        * @param int $order_status_id
        * @return string 
        */
	public static function getOrderStatus($order_status_id)
	{
            $order_status = '';
            $table = self::getTable('Order_statuses');
            if($table->load($order_status_id))
            {
                $order_status = $table->name;
            }

            return $order_status;
	}

        /**
         * Дата заказа
         * @param type $order_status_id
         * @return string 
         */
	public static function getOrderDt($order_dt)
	{
//            preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})(\.)+$/", $order_dt, $regs);
            preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})(.+)/", $order_dt, $regs);
            if(count($regs))
            {
                $order_dt = $regs[3].'.'.$regs[2].'.'.$regs[1].$regs[4];
            }

            return $order_dt;
	}

        /**
         * Сумма заказа
         * @param type $sum
         * @return string 
         */
	public static function getSum($sum)
	{
            return (int)$sum;
	}
        
        /**
         * Наименование завода
         * @param int $site
         * @return string 
         */
	public static function getSite_name($site)
	{
            $site_name = '';
            $table = self::getTable('Site');
            if($table->load($site))
            {
                $site_name  = $table->name;
            }
            return $site_name;
	}
        
        /**
         * Наименование категории с путем
         * @param int $category_id
         * @return string
         */
	public static function getCategory_path($category_id, $category_path = '')
	{
            $table = self::getTable('Category');
            if($category_id AND $table->load($category_id))
            {
                $_category_path = $category_path?' => '.$category_path:'';
                $category_path = self::getCategory_path($table->parent_id,$table->name.$_category_path);
            }
            return $category_path;
        }

        /**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	private function getTable($type = '', $prefix = 'IshopTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}
