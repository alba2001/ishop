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
        
        /**
         * Условия сортировки
         */
        $sort_order_products = JRequest::getVar('sort_order_products', 
                JFactory::getApplication()->getUserState('com_ishop.sort_order_products', 0));
        $this->setState('sort_order_products', $sort_order_products);
        $ar_order_products = $this->get_ar_order_products();
        $this->setState('sort_order_products', $sort_order_products);
        $order = $ar_order_products[$sort_order_products]['ordering'];
        $order_dir = $ar_order_products[$sort_order_products]['direction'];
        $this->setState('order', $order);
        $this->setState('order_dir', $order_dir);
        JFactory::getApplication()->setUserState('com_ishop.sort_order_products', $sort_order_products);
        
        /**
         * Фильтр (условия поиска) продуктов
         */
        // Обработка данных модуля фильтрации 
            $init_ishop_search_data = array(
                'brand' => '0',
                'category' => '0',
                'cena_from' => '',
                'cena_to' => '',
                'available' => '0',
                'text' => '',
            );

        $ishop_search_data = JRequest::getVar('ishop_search_data', 
                JFactory::getApplication()->getUserState('com_ishop.ishop_search', $init_ishop_search_data),
                '','array');
        if($ishop_search_data)
        {
            if(!isset($ishop_search_data['available']))
            {
                $ishop_search_data['available'] = 0;
            }
            $this->setState('ishop_search_data.brand', $ishop_search_data['brand']);
            $this->setState('ishop_search_data.category', $ishop_search_data['category']);
            $this->setState('ishop_search_data.available', $ishop_search_data['available']);
            $this->setState('ishop_search_data.cena_from', $ishop_search_data['cena_from']);
            $this->setState('ishop_search_data.cena_to', $ishop_search_data['cena_to']);
            $this->setState('ishop_search_data.text', isset($ishop_search_data['text'])?$ishop_search_data['text']:'');
        }
        JFactory::getApplication()->setUserState('com_ishop.ishop_search', $ishop_search_data);
        
        
        
        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        
        
        if(empty($ordering)) {
                $ordering = $ar_order_products[$sort_order_products]['ordering'];
                $direction = $ar_order_products[$sort_order_products]['direction'];
        }
        
        // List state information.
        parent::populateState($order, $order_dir);
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
        $query = parent::getListQuery()
            ->select($this->getState('list.select', 'a.*'))
            ->from('`#__ishop_products` AS a')
            ->where('`a`.`state` = 1')
        ;

        
        // Обрабртка данных модуля фильтра 
            // Фильтр по наличию
            if($filter = $this->getState('ishop_search_data.available', ''))
            {
                $query->where('available = 1');
            }
            
            // Бренды и категории
            $category_ids = array();
            if($filter = $this->getState('ishop_search_data.category', ''))
             {
                $category_model = IshopHelper::getModel('category');
                $children = $category_model->get_children($filter);
            }
            elseif($filter = $this->getState('ishop_search_data.brand', ''))
            {
                $category_model = IshopHelper::getModel('category');
                $children = $category_model->get_children($filter);
            }
            if(isset($children))
            {
                $category_ids = array($filter);
                foreach ($children as $child)
                {
                    $category_ids[] = $child->id;
                }
                $query->where('a.id IN (SELECT product_id FROM `#__ishop_product_category`  AS prcat WHERE prcat.category_id IN ('.implode(',',$category_ids).'))');
            }
            
            // Фильтр по цене
            if($cena_from = (int)$this->getState('ishop_search_data.cena_from', ''))
            {
                $query->where('cena_tut >= "'.$cena_from.'"');
            }
            if($cena_to = (int)$this->getState('ishop_search_data.cena_to', ''))
            {
                $query->where('cena_tut <= "'.$cena_to.'"');
            }
            
            if($artikul = $this->getState('ishop_search_data.artikul', ''))
            {
                $query->where('artikul = "'.$artikul.'"');
            }
            
            if($search_text = $this->getState('ishop_search_data.text', ''))
            {
                $query->where('`a`.`name` LIKE "%'.$search_text.'%"');
            }
            
            // Если установлена вторая цена в поиске, а первая или 0 или не
            // установлена, то не включаем товары с нулевой стоимостью
            if($cena_to AND !$cena_from)
            {
                $query->where('cena_tut >= "0.01"');
                
            }
            
            $order_by = $this->_get_order();

            if($order_by)
            {
                $query->order($order_by);
            }
//            var_dump((string)$query);
            
        return $query;
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

        private function _get_order()
        {
            $order = $this->getState('order');
            if(!$order)
            {
                return '';
            }
            $order_dir = $this->getState('order_dir');
            
            return $order.' '.$order_dir;
        }

        /**
         * Данные по сортировке товаров
         * @return array
         */
        public function get_ar_order_products()
        {
            return array(
                0=>array(
                    'ordering'=>'', 
                    'direction'=>'ASC', 
                    'name'=>  JText::_('COM_ISHOP_ORDER_BY_NO_ORDER')
                    ),
                1=>array(
                    'ordering'=>'cena_tut' , 
                    'direction'=>'ASC', 
                    'name'=>  JText::_('COM_ISHOP_ORDER_BY_CENA_MIN_TO_MAX')
                    ),
                2=>array('ordering'=>'cena_tut', 
                    'direction'=>'DESC', 
                    'name'=>  JText::_('COM_ISHOP_ORDER_BY_CENA_MAX_TO_MIN')
                    ),
                3=>array(
                    'ordering'=>'hits', 
                    'direction'=>'DESC', 
                    'name'=>  JText::_('COM_ISHOP_ORDER_BY_HITS')
                    ),
            );
        }
  }
