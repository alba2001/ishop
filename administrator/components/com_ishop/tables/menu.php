<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Import JTableMenu
JLoader::register('JTableMenu', JPATH_PLATFORM . '/joomla/database/table/menu.php');

//require_once dirname(__FILE__) . '/ktable.php'; 
jimport('joomla.database.tablenested');
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 */
//class IshopTableMenu extends JTableMenu
class IshopTableMenu extends JTableNested
{
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @since   11.1
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__menu', 'id', $db);

		// Set the default access level.
		$this->access = (int) JFactory::getConfig()->get('access');
	}
        
       
}
