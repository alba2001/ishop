<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/kmodellist.php'; 

/**
 * Methods supporting a list of Ishop records.
 */
class IshopModelProducts extends IshopModelKModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'name', 'a.name',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'created_by', 'a.created_by',
                'artikul', 'a.artikul',

            );
        }

        parent::__construct($config);
    }
    

    /**
     * Overload parent populateState function
     * @param type $ordering
     * @param type $direction 
     */
    protected function populateState($ordering = null, $direction = null) {

        // Load the filter state.
        $app = JFactory::getApplication();

        // Устанавливаем наименование контекста
        $app->setUserState('com_ishop.this_context', $this->context);
        
        // Фильтр по заводу
        $site_alias = $app->getUserStateFromRequest($this->context.'.filter.site_alias', 'filter_site_alias', 'oriflame', 'string');
        $this->setState('filter.site_alias', $site_alias);        
        
        // Фильтр по категории
        $category = $app->getUserStateFromRequest($this->context.'.filter.category', 'filter_category', '0', 'string');
        $this->setState('filter.category', $category);        

        // Поиск по артикулу
        $search = $app->getUserStateFromRequest($this->context.'.filter.search_artikul', 'filter_search_artikul');
        $this->setState('filter.search_artikul', $search);

        
        
        parent::populateState($ordering, $direction);
    }

    /**
     * Overload parent getStoreId function
     * @param type $id
     * @return type 
     */
    protected function getStoreId($id = '') {
        $id .= parent::getStoreId($id);
        $id.= ':' . $this->getState('filter.site_alias');

        return parent::getStoreId($id);
    }
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
            
            $this->setState(
                            'list.select',
                            'prcat.category_id, a.*'
                    );
            $query = parent::getListQuery();
            
            $query->from('`#__ishop_products` AS a');
            $query->join('INNER', '`#__ishop_product_category` AS prcat ON prcat.product_id = a.id');
            $query->join('INNER', '`#__ishop_categories` AS c ON c.id = prcat.category_id');
            
            // Фильтр по заводу
            $site_alias = $this->getState('filter.site_alias', 'oriflame');
            $query->where('c.site_alias = "'.$site_alias.'"');

            // Фильтр по категории
            $category = $this->getState('filter.category', '0');
            if($category)
            {
                $query->where('c.id = '.$category);
            }

            // Filter by search in title
            $search = $this->getState('filter.search');
            $search_artikul = $this->getState('filter.search_artikul');
            if (!empty($search_artikul)) 
            {
                $search_artikul = $this->_db->Quote('%'.$this->_db->escape($search_artikul, true).'%');
                $query->where('( a.artikul LIKE '.$search_artikul.' )');
            }

            if (!empty($search)) 
            {
                if (stripos($search, 'id:') === 0) 
                {
                    $query->where('a.id = '.(int) substr($search, 3));
                } 
                else 
                {
                    $search = $this->_db->Quote('%'.$this->_db->escape($search, true).'%');
                    $query->where('( a.name LIKE '.$search.' )');
                }
            }
//            var_dump((string)$query);
            return $query;
        }
        
        /**
         * Меняем статус наличия товара
         * @param array $cids
         * @param int $value
         * @return array
         */
        public function set_available($cids, $value)
        {
            $result = 1;
            foreach($cids as $cid)
            {
                $result = (int)$this->_availabled($cid, $value) * $result;
            }
            return $result;
        }
        
       
        /**
         * Установка наличия товара
         * @param int $cid
         * @param int $value
         * @return bolean
         */
        private function _availabled($cid, $value)
        {
            $table = $this->getTable('Product','IshopTable');
            if($table->load($cid))
            {
                $table->available = $value;
                if($table->store())
                {
                    return TRUE;
                }
            }
            return FALSE;
        }
        
        public function fill_cenas()
        {
            require_once JPATH_ROOT.'/administrator/components/com_ishop/helpers/component.php';
            $query = $this->_db->getQuery(TRUE);
            $query->select('id');
            $query->from('#__ishop_products');
            $this->_db->setQuery($query);
            $keys = $this->_db->loadResultArray();
            foreach($keys as $key)
            {
                $prises = ComponentHelper::getPrices($key);
                //Временная заглушка не переписывать цену, если она уже прописана
                $query = 'SELECT cena_tut from #__ishop_products WHERE id='.$key;
                $this->_db->setQuery($query);
                $sum_in_table = $this->_db->loadResult();
                if(!(int)$sum_in_table)
                {
                    $query = 'UPDATE  `#__ishop_products` SET  `cena_mag` =  '.$prises['cena_mag'].
                            ', `cena_tut` ='.$prises['cena_tut'].
                            ' WHERE  `id` ='.$key;
                    $this->_db->setQuery($query);
                    $this->_db->query();
                }
            }
            return TRUE;
        }

       /**
        * Групповое добавление категории к товарам
        */
        public function add_categories()
        {
            $cids = JRequest::getVar('cid', array(), '', 'array');
            if(!$cids)
            {
                return FALSE;
            }
            
            $category_id = JRequest::getInt('category_id', 0);
            if(!$category_id)
            {
                return FALSE;
            }
            require_once dirname(__FILE__) . '/product.php'; 
            $model = new IshopModelProduct;
            $model->set_category_id($category_id);
            foreach($cids as $product_id)
            {
                $model->set_product_id($product_id);
                $result = $model->add_category();
                if(!$result[0])
                {
//                    return FALSE;
                }
            }
            
            return TRUE;
        }

       /**
        * Групповое удаление категории к товарам
        */
        public function rm_categories()
        {
            $cids = JRequest::getVar('cid', array(), '', 'array');
            if(!$cids)
            {
                return FALSE;
            }
            
            $category_id = JRequest::getInt('category_id', 0);
            if(!$category_id)
            {
                return FALSE;
            }
            require_once dirname(__FILE__) . '/product.php'; 
            $model = new IshopModelProduct;
            $model->set_category_id($category_id);
            foreach($cids as $product_id)
            {
                $model->set_product_id($product_id);
                $result = $model->remove_category();
                if(!$result[0])
                {
//                    return FALSE;
                }
            }
            
            return TRUE;
        }
        
        
}
