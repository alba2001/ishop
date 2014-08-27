<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
 
require_once dirname(__FILE__) . '/ktable.php'; 
/**
 * Users Table class
 */
class IshopTableOrder_statuses extends IshopTableKtable
{
    
    /**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) 
	{
		parent::__construct('#__ishop_order_statuses', 'id', $db);
	}
}
