<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * UserList Model
 */
class IshopModelUsers extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__ishop_users');
		return $query;
	}
}
