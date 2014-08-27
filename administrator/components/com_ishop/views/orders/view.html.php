<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
require_once JPATH_COMPONENT.'/helpers/component.php';

 
/**
 * Orders View
 */
class IshopViewOrders extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $model;
    
	/**
	 * Orders view display method
	 * @return void
	 */
	function display($tpl = null) 
	{

		// Get data from the model
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign data to the view
                $this->state = $this->get('State');
		$this->items = $items;
		$this->pagination = $pagination;
		$this->model = $this->getModel();
 
		// Set the toolbar
		$this->addToolBar();
                
                // Add submenu
                $input = JFactory::getApplication()->input;
                $view = $input->getCmd('view', '');
                IshopHelper::addSubmenu($view);

		// Display the template
		parent::display($tpl);
 
		// Set the document
		$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
            JToolBarHelper::title(JText::_('COM_ORDER_ADMINISTRATION'), 'order.png');
            JToolBarHelper::editList('order.edit', 'JTOOLBAR_EDIT');
            JToolBarHelper::deleteList('', 'orders.delete', 'JTOOLBAR_DELETE');
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_order');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_ORDER_ADMINISTRATION'));
                $document->addStyleSheet('components/com_ishop/assets/css/ishop.css');
	}
        
        /**
         * ФИО клиента
         * @param int $userid
         * @return string
         */
        protected function get_user_fio($userid)
        {
            return ComponentHelper::getUserFio($userid);
            
        }
        
        /**
         * Способ оплаты заказа
         * @param int $oplata_id
         * @return string 
         */
        protected function get_order_oplata($oplata_id)
        {
            return ComponentHelper::getOplata($oplata_id);
            
        }
        /**
         * Способ доставки заказа
         * @param int $dostavka_id
         * @return string 
         */
        protected function get_order_dostavka($dostavka_id)
        {
            return ComponentHelper::getDostavka($dostavka_id);
            
        }
        /**
         * Статус заказа
         * @param type $order_status_id
         * @return string 
         */
        protected function get_order_status($order_status_id)
        {
            return ComponentHelper::getOrderStatus($order_status_id);
            
        }
        
        /**
         * Дата заказа
         * @param type $order_dt
         * @return string 
         */
        protected function get_order_dt($order_dt)
        {
            return ComponentHelper::getOrderDt($order_dt);
            
        }
        
        /**
         * Сумма заказа
         * @param type $sum
         * @return string 
         */
        protected function get_sum($sum)
        {
            return ComponentHelper::getSum($sum);
            
        }
}
