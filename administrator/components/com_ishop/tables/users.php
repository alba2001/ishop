<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
require_once dirname(__FILE__) . '/ktable.php'; 
/**
 * Users Table class
 */
class IshopTableUsers extends IshopTableKtable
{
    
    protected $_check_fields;

    /**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__ishop_users', 'id', $db);
                $this->_check_fields = array('address','fam','im','ot');
	}
	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database.  Child classes should override this
	 * method to make sure the data they are storing in the database is safe and
	 * as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @link    http://docs.joomla.org/JTable/check
	 * @since   11.1
	 */
	public function check()
	{
            // Конвертируем номер телефона
            if (substr($this->phone,0,3) == '+7(') 
            {
                preg_match("/\+7\(([0-9]{3})\) ([0-9]{3})-([0-9]{2})-([0-9]{2})/", $this->phone, $regs);
                $this->phone = $regs[1].$regs[2].$regs[3].$regs[4];
            }
            foreach($this->_check_fields as $check_field)
            {
                $this->$check_field = addslashes($this->$check_field);
            }
            return parent::check();
	}
        
        /**
         * Overload parent bind method
         * @param type $keys
         * @param type $reset
         */
        public function load($keys = null, $reset = true)
	{
            if( parent::load($keys, $reset))
            {
                foreach($this->_check_fields as $check_field)
                {
                    $this->$check_field = stripcslashes($this->$check_field);
                }
                return TRUE;
            }
            return FALSE;
        }
        
}
