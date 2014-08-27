<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * Userform Model
 */

class ModelHelper
{
        /**
	 * Get the user
	 * @return object The message to be displayed to the user
	 */
	static public function getUser() 
	{
                $id = JFactory::getApplication()->getUserState('com_ishop.users_id',0,0);
//                var_dump($id);exit;
                $uid = JFactory::getUser()->id;
                $table = self::getTable('users');
                if($id AND $table->load($id))
                {
                    $this_user =& $table;
                }
                else if($uid AND $table->load(array('uid'=>$uid))) 
                {
                    $this_user =& $table;
                }
                else
                {
                    $this_user = new stdClass;
                    $this_user->id = 0;
                }
//                var_dump($this_user);exit;
            return $this_user;
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
	static public function getTable($type = '', $prefix = 'IshopTable', $config = array()) 
	{
            return JTable::getInstance($type, $prefix, $config);
	}
        
        /**
         * Преобразовываем дату из формата MySql в формат дд.мм.ГГГ
         * @param type MySQL date
         * @return string
         */
        static public function mysql_to_german($dt)
        {
            preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", $dt, $regs);
            return $regs[3].'.'.$regs[2].'.'.$regs[1];
        }
        
        /**
         * Возвращаем дату и время заказа в формате
         *  dd.dd.YYY H:m
         * @param string $order_dt
         * @return string 
         */
        static public function getOrderDt($order_dt)
        {
            preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", $order_dt, $regs);
            return $regs[3].'.'.$regs[2].'.'.$regs[1].' '.$regs[4].':'.$regs[5];
        }

        /**
         * Возвращаем статус заказа
         * @param integer $status_id
         * @return string 
         */
        static public function getOrderStatus($status_id)
        {
            $status_name = '';
            $table = self::getTable('Order_statuses');
            if($table->load($status_id))
            {
                $status_name = $table->name;
            }
            return $status_name;
        }
        
}
