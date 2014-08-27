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
require_once dirname(__FILE__) . '/modelhelper.php'; 
/**
 * Methods supporting a list of Ishop records.
 */
class IshopModelOrders extends JModelList {


    /**
     * Get the user
     * @return object The message to be displayed to the user
     */
    public function getUser() 
    {
        return ModelHelper::getUser();
    }

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
        
        $this_user_id = $this->getUser()->id;
        $this->setState('orders.this_user_id', $this_user_id);
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
    protected function getListQuery() 
    {
        $this_user_id = $this->getState('orders.this_user_id', 0);
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('a.*');
        
        $query->from('`#__ishop_orders` AS a');

        $query->where('userid = '.$this_user_id);
        
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
	public function getTable($type = 'Orders', $prefix = 'IshopTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

        /**
         * Возвращаем дату и время заказа в формате
         *  dd.dd.YYY H:m
         * @param string $order_dt
         * @return string 
         */
        public function get_order_dt($order_dt)
        {
            return ModelHelper::getOrderDt($order_dt);
        }
        
        /**
         * Возвращаем статус заказа
         * @param integer $status_id
         * @return string 
         */
        public function get_order_status($status_id)
        {
            return ModelHelper::getOrderStatus($status_id);
        }
}
