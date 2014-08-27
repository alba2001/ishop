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
class IshopModelKModelList extends JModelList
{

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
            // Initialise variables.
            $app = JFactory::getApplication('administrator');

            // Load the filter state.
            $search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
            $this->setState('filter.search', $search);

            $published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
            $this->setState('filter.state', $published);



            // Load the parameters.
            $params = JComponentHelper::getParams('com_ishop');
            $this->setState('params', $params);

            // List state information.
            parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
            // Compile the store id.
            $id.= ':' . $this->getState('filter.search');
            $id.= ':' . $this->getState('filter.state');

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
            // Create a new query object.
            $db		= $this->getDbo();
            $query	= $db->getQuery(true);

            // Select the required fields from the table.
            $query->select(
                    $this->getState(
                            'list.select',
                            'a.*'
                    )
            );


            // Join over the users for the checked out user.
            $query->select('uc.name AS editor');
            $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

                        // Join over the user field 'created_by'
                        $query->select('created_by.name AS created_by');
                        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');


            // Filter by published state
            $published = $this->getState('filter.state');
            if (is_numeric($published)) {
                $query->where('a.state = '.(int) $published);
            } else if ($published === '') {
                $query->where('(a.state IN (0, 1))');
            }
    
        
		// Add the list ordering clause.
            $orderCol	= $this->state->get('list.ordering');
            $orderDirn	= $this->state->get('list.direction');
            if ($orderCol && $orderDirn) {
                $query->order($db->escape($orderCol.' '.$orderDirn));
            }

            return $query;
	}
        
        /**
         * Публикация товара
         * @param int $cid
         * @param int $value
         * @return bolean
         */
        public function publish($cids, $value)
        {
            $result = 1;
            foreach($cids as $cid)
            {
                $result = (int)$this->_published($cid, $value) * $result;
            }
            return $result;
        }
        
       
        /**
         * Установка публикации товара
         * @param int $cid
         * @param int $value
         * @return bolean
         */
        private function _published($cid, $value)
        {
            $table = $this->getTable('Product','IshopTable');
            if($table->load($cid))
            {
                $table->state = $value;
                if($table->store())
                {
                    return TRUE;
                }
            }
            return FALSE;
        }
        
}
