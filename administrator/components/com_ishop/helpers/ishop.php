<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Ishop helper.
 */
class IshopHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_ISHOP_SUBMENU_CATEGORIES'),
			'index.php?option=com_ishop&view=categories',
			$vName == 'categories'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ISHOP_TITLE_PRODUCTS'),
			'index.php?option=com_ishop&view=products',
			$vName == 'products'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ISHOP_TITLE_SITES'),
			'index.php?option=com_ishop&view=sites',
			$vName == 'sites'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_USER_ADMINISTRATION'),
			'index.php?option=com_ishop&view=users',
			$vName == 'users'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_ORDER_ADMINISTRATION'),
			'index.php?option=com_ishop&view=orders',
			$vName == 'orders'
		);
//		JSubMenuHelper::addEntry(
//			JText::_('COM_ISHOP_PRODUCTTYPES'),
//			'index.php?option=com_ishop&view=producttypes',
//			$vName == 'orders'
//		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function _getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_ishop';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}
		return $result;
	}
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	string	$extension	The extension.
	 * @param	int		$categoryId	The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($extension)
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$parts		= explode('.', $extension);
		$component	= $parts[0];

                $assetName = $component;
                $section = $parts[1];
                $actions = JAccess::getActionsFromFile(
                    JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml',
                    "/access/section[@name='" . $section . "']/"
		);


		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
