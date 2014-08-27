<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
//jimport('joomla.application.component.modellist');
require_once dirname(__FILE__) . '/kmodellist.php'; 
/**
 * OrderList Model
 */
class IshopModelOrders extends IshopModelKModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
            $query = parent::getListQuery();
            $query->from('`#__ishop_orders` AS a');

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
                    $query->leftJoin('#__ishop_users AS b ON a.userid = b.id');
                    $query->where('( b.fam LIKE '.$search.' )');
                }
            }
            
            // Фильтр по статусу документа
            $order_status = $this->getState('filter.order_status');
            if($order_status)
            {
                $query->where(' a.order_status_id = '.$order_status);
            }
            
//                    var_dump((string)$query);
            return $query;
	}
        
        /**
         * Override parent populateState method
         * @param type $ordering
         * @param type $direction
         */
        protected function populateState($ordering = null, $direction = null) {
            parent::populateState($ordering, $direction);
            
            // Initialise variables.
            $app = JFactory::getApplication('administrator');
            
            // Фильтр по статусу заказа
            $order_status = $app->getUserStateFromRequest($this->context.'.filter.order_status', 'order_status', '', 'string');
            $this->setState('filter.order_status', $order_status);
            
            // Переопределение filter.state т.к. поле state в таблице заказов отсутствует
            $this->setState('filter.state', '');

        }        
        /**
        * Подготовка селекта статуса заказа
        * @param int $order_status_id
        * @return string 
        */
	public function getOrderStatusOptions()
	{
            $query = $this->_db->getQuery(true);
            $query->select('*');
            $query->from('#__ishop_order_statuses');
            $this->_db->setQuery($query);
            $statuses = $this->_db->loadObjectList();
            $options = array();
            foreach ($statuses as $status)
            {
                $options[] = JHTML::_('select.option', $status->id, $status->name);
            }
            
            return $options;
	}
        
}
