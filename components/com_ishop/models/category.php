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

jimport('joomla.application.component.modellist');

/**
 * Ishop model.
 */
class IshopModelCategory extends JModelList
{
    
    var $_item = null;
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        
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
        
        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        
        $_category_id = $app->getUserStateFromRequest('ishop.category_id', 'category_id', 0);
        if(isset($params_array['item_id'])){
            $category_id = $params_array['item_id'];
            $app->setUserState('ishop.category_id', $category_id);
        }
        $this->setState('category.id', $category_id);
        
        $this->setState('params', $params);
        
        
        // List state information.
        parent::populateState($ordering, $direction);
    }
        

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('category.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			} elseif ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}
    
	public function getTable($type = 'Category', $prefix = 'IshopTable', $config = array())
	{   
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
        return JTable::getInstance($type, $prefix, $config);
	}     
        
        /**
         * Находим список зависимых категорий
         * @return object list 
         */
        public function getChildren()
        {
            $parent_id = $this->_item->id;
            $categories = $this->getTable('Category');
            $children = $categories->get_rows(array(
                'parent_id' => $parent_id,
//                'state' => '1',
            ));
            return $children;
        }
 
     /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        
        $category_id = $this->getState('category.id');
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState('list.select', 'a.*')
        );
        
        $query->from('`#__ishop_products` AS a');
        $query->join('INNER', '`#__ishop_product_category` AS prcat ON prcat.product_id = a.id');
        $query->join('INNER', '`#__ishop_categories` AS c ON c.id = prcat.category_id');
        
        
        $query->where('`a`.`state` = 1');
//        $query->where('`a`.`category_id` = '.$this->_item->id);
        $query->where('`c`.`id` = '.$category_id);

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
//        var_dump((string)$query);
        return $query;
    }

}