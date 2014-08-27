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
class IshopModelCategories extends IshopModelKModelList
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

            );
        }

        parent::__construct($config);
    }
    
    protected function populateState($ordering = null, $direction = null) {
        
        $app = JFactory::getApplication();
        
        // Устанавливаем наименование контекста
        $app->setUserState('com_ishop.this_context', $this->context);
        
        // Load the filter state.
        $site_alias = $app->getUserStateFromRequest($this->context.'.filter.site_alias', 'filter_site_alias', 'oriflame', 'string');
        $this->setState('filter.site_alias', $site_alias);        
        
        // Фильтр по уровню вложенности
        $level = $app->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', '10', 'string');
        $this->setState('filter.level', $level);        

        // Фильтр по родительской категории
        $parent = $app->getUserStateFromRequest($this->context.'.filter.parent', 'filter_parent', '10', 'string');
        $this->setState('filter.parent', $parent);        

        parent::populateState('lft', 'asc');
    }
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
            $query = parent::getListQuery();
            
            $query->from('`#__ishop_categories` AS a');
            
            // Фильтр по родительской категории
            $parent = $this->getState('filter.parent', '0');
            if($parent)
            {
                $this->_filter_parent($parent, $query);
            }

            
            // Фильтр по уровню вложенности
            $level = $this->getState('filter.level', '0');
            if($level)
            {
                $query->where('a.level <= '.$level);
            }

            
            // Фильтр по заводу
            $site_alias = $this->getState('filter.site_alias', 'oriflame');
            $query->where('site_alias = "'.$site_alias.'"');
            
            // Filter by search in title
            $search = $this->getState('filter.search');
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
         * Фильтр по текущей и всем дочерним категориям
         * @param int $parent
         * @return array 
         */
        private function _filter_parent($parent, &$_query)
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(TRUE)
                    ->select('*')
                    ->from('`#__ishop_categories`')
                    ->where('`id` = '.$parent)
            ;
            $db->setQuery($query);
            $category = $db->loadObject();

            $_query->where('`a`.`lft` >= '.$category->lft);
            $_query->where('`a`.`rgt` <= '.$category->rgt);
        }
        
        /**
         * Возвращает ИД категорий прямых наследников
         * @param int $parent_id
         * @return array
         */
        private function _get_chilgren($parent_id)
        {
            $query = $this->_db->getQuery(TRUE);
            $query->select('id');
            $query->from('`#__ishop_categories`');
            $query->where('parent_id = '.$parent_id);
            $this->_db->setQuery($query);
            
            return $this->_db->loadResultArray();
        }

        /**
         * Возвращаем наименование типа продукта
         * @param int $producttype_id 
         * @return string 
         */
        public function get_producttype_id($producttype_id) 
        {
            $name = '';
            $table = $this->getTable('Producttype', 'IshopTable');
            if($table->load($producttype_id))
            {
                $name = $table->name;
            }
            return $name;
        }
        
        /**
         * 
         * @return type 
         */
        public function parse_one_catrgory()
        {
            $cids = JRequest::getVar('cid', array(), '', 'array');
            if(!$cids)
            {
                return array(0, JText::_('COM_ISHOP_NOT_SELECTED'));
            }
            
            $query = $this->_db->getQuery(TRUE)
                    ->select('`name`')
                    ->select('`category_sourse_path` as `link`')
                    ->select('`id` as `category_id`')
                    ->select('`path` as `category_path`')
                    ->from('#__ishop_categories')
                    ->where('`category_sourse_path` <> ""')
                    ->where('`id` IN ('.  implode(',', $cids).')')
            ;
            $this->_db->setQuery($query);
            
            $categories = $this->_db->loadAssocList();
            if(!$categories)
            {
                return array(0, JText::_('COM_ISHOP_NOT_CATEGORIES_FIND'));
            }
            $file_data = JPATH_ROOT.DS.'tmp'.DS.'parse_oriflame_data.txt';
            $data = array(
                'func' => array('parse_subcategories','get_category_page'),
                'categories'=>$categories,
                'subcategories'=>array()
            );
            
            JFactory::getApplication()->setUserState('com_ishop.parse', $data);
            if (!JFile::write($file_data, json_encode($data)))
            {
                return array(0, JText::_('COM_ISHOP_NOT_WRITE_DATA'));
            }
            return array(1, 'oriflame');
            
        }
}
