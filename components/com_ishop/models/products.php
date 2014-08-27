<?php

/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Ishop records.
 */
class IshopModelProducts extends JModelList {

    /**
     * Массив групп изделий при которых показывается меню групп изделий
     * @var array 
     */
    private $_ar_groups_shown;

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        // 
        $this->_ar_groups_shown = array('','0','1','2');
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        
        // Показываем меню продуктов или нет
        $show_menu_groups = JRequest::getInt('show_menu_groups', TRUE);
        $group = 0;
        if($show_menu_groups)
        {
            // Вычисляем группу продуктов
            $menu = JSite::getMenu();
            $active = $menu->getActive();
            $params = isset($active)?$active->params:NULL;
            $product_group = isset($params)?$params->get('product_group'):0;
            $group = JRequest::getInt('product_group',
                    $product_group);
            
        }
        $this->setState('products_group', $group);
        $this->setState('show_menu_groups', $show_menu_groups);
        
        // Если это не показ пунктов главного меню, то включаем фильтр
        if(!$show_menu_groups)
        {
            // Обработка данных модуля фильтрации 
                $init_usearch_data = array(
                    'brand' => '0',
                    'category' => '0',
                    'cena_from' => '',
                    'cena_to' => '',
                    'available' => '0',
                    'artikul' => '',
                );

            $usearch_data = JRequest::getVar('usearch_data', 
                    JFactory::getApplication()->getUserState('com_ishop.usearch', $init_usearch_data),
                    '','array');
            if($usearch_data)
            {
                if(!isset($usearch_data['available']))
                {
                    $usearch_data['available'] = 0;
                }
                $this->setState('usearch_data.brand', $usearch_data['brand']);
                $this->setState('usearch_data.category', $usearch_data['category']);
                $this->setState('usearch_data.available', $usearch_data['available']);
                $this->setState('usearch_data.cena_from', $usearch_data['cena_from']);
                $this->setState('usearch_data.cena_to', $usearch_data['cena_to']);
                $this->setState('usearch_data.artikul', $usearch_data['artikul']);
                $this->setState('usearch_data.text', isset($usearch_data['text'])?$usearch_data['text']:'');
            }
            JFactory::getApplication()->setUserState('com_ishop.usearch', $usearch_data);
        }
        
        
        
        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        
        
        if(empty($ordering)) {
                $ordering = 'a.ordering';
        }
        
        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
     /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState('list.select', 'a.*')
        );
        
        $query->from('`#__ishop_products` AS a');
            
        
        $query->where('`a`.`state` = 1');

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                        $query->where('a.id = '.(int) substr($search, 3));
                } else {
                        $search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where('( a.name LIKE '.$search.' )');
                }
        }
        
        // Обрабртка данных модуля фильтра 
            // Фильтр по виду изделия
            // Фильтр по наличию
            if($filter = $this->getState('usearch_data.available', ''))
            {
                $query->where('available = 1');
            }
            
            // Бренды
            if($filter = $this->getState('usearch_data.brand', ''))
            {
                $query->where('`a`.`id`  IN (SELECT `product_id` FROM `#__ishop_product_category` WHERE `category_id` = '.$filter.')');
            }
            
            // Категории
            if($filter = $this->getState('usearch_data.category', ''))
            {
                $query->join('INNER', '`#__ishop_product_category` AS prcat ON prcat.product_id = a.id');
                $query->join('INNER', '`#__ishop_categories` AS c ON c.id = prcat.category_id');
                $query->where('`c`.`parent_id` = '.$filter);
            }
            
            if($filter = $this->getState('usearch_data.available', ''))
            {
                $query->where('available = 1');
            }
            
            // Фильтр по цене
            if($cena_from = (int)$this->getState('usearch_data.cena_from', ''))
            {
                $query->where('cena_tut >= "'.$cena_from.'"');
            }
            if($cena_to = (int)$this->getState('usearch_data.cena_to', ''))
            {
                $query->where('cena_tut <= "'.$cena_to.'"');
            }
            
            if($artikul = $this->getState('usearch_data.artikul', ''))
            {
                $query->where('artikul = "'.$artikul.'"');
            }
            
            if($search_text = $this->getState('usearch_data.text', ''))
            {
                $query->where('`a`.`name` LIKE "%'.$search_text.'%"');
            }
            
            // Если установлена вторая цена в поиске, а первая или 0 или не
            // установлена, то не включаем товары с нулевой стоимостью
            if($cena_to AND !$cena_from)
            {
                $query->where('cena_tut >= "0.01"');
                
            }
//            var_dump((string)$query);
        return $query;
    }
    /**
     * Заглавие страницы 
     * @return string 
     */
    public function getTitle()
    {
        $group = (int) $this->getState('products_group');
        switch ($group)
        {
            case 1:
                $title = JText::_('COM_ISHOP_PRODUCT_NEW');
                break;
            case 2:
                $title = JText::_('COM_ISHOP_PRODUCT_SPETS');
                break;
            default :
                $title = JText::_('COM_ISHOP_PRODUCT_ALL');
        }
        return $title;
    }
    
    /**
     * Фильтр по группам изделий
     * @return string 
     */
    private function _group_flt()
    {
        $group = (int) $this->getState('products_group');
        switch ($group)
        {
            case 1: // Новинки
                $where = '`novinka_dt` > "'.date('Y-m-d').'"';
                break;
            case 2: // Спецпредложения
                $where = '`spets_predl` = "1"';
                break;
            case 3: // В наличии
                $where = '`available` = "1"';
                break;
            default : // Все изделия
                $where = '';
        }
        return $where;
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
	public function getTable($type = 'Products', $prefix = 'IshopTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

        /**
         *  Проверка принадлежит ли изделие к кольцам?
         * ID вида продукта = 1
         * @return boolean
         */
        public function isKoltsa($product_id)
        {
            $list_koltsa_categories = $this->_get_product_vid_categories('1');
            $query = &$this->_db->getQuery(true);
            $query->select('category_id');
            $query->from('#__ishop_products');
            $query->where('id = '.$product_id);
            $this->_db->setQuery($query);
            $category_id = $this->_db->loadResult();
            
            return in_array($category_id, $list_koltsa_categories);
        }

        /**
         * Получение списка категорий от вида продукта
         * @param type $productvid_id
         * @return type 
         */
        private function _get_product_vid_categories($productvid_id)
        {
            $category_ids = array();
            $product_vid = &$this->getTable('Productvid');
            if($product_vid->load($productvid_id))
            {
                $category_ids = $this->_get_categiry_ids($product_vid->alias);
            }
            
            return $category_ids;
        }

        /**
         * Находим категории и их подкатегории
         * с наименованием совпадающим с псевдонимом вида изделия
         * @param string $alias
         * @return array
         */
        private function _get_categiry_ids($alias)
        {
            $_query = &$this->_db->getQuery(true);
            $_query->select('id');
            $_query->from('#__ishop_categories');
            $_query->where('`alias` LIKE "%'.$alias.'%"');
            $this->_db->setQuery($_query);
            $ar_parents = $this->_db->loadResultArray();
            $ar_childrens = $this->_get_childrens($ar_parents);
            
//            var_dump((string)$_query);
            return array_merge($ar_parents, $ar_childrens);
        }
        
        /**
         * Находим подкатегории списка категорий
         * @param array $ar_parents
         * @return array
         */
        private function _get_childrens($ar_parents)
        {
            $_query = &$this->_db->getQuery(true);
            $_query->select('id');
            $_query->from('#__ishop_categories');
            $_query->where('`parent_id` IN ('.  implode(',', $ar_parents).')');
            $this->_db->setQuery($_query);
            $ar_children = $this->_db->loadResultArray();
            return $ar_children;
            
        }
}
