<?php
/**
 * JComments plugin for AcePolls (http://www.joomace.net/joomla-extensions/ishop-joomla-polls-component)
 *
 * @version 2.3
 * @package JComments
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright 2009-2011 JoomAce LLC, www.joomace.net
 * @copyright (C) 2006-2013 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class jc_com_ishop extends JCommentsPlugin
{
	function getObjectInfo($id, $language = null)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT p.id, p.name, p.available '
			. ' FROM #__ishop_products AS p'
			. ' WHERE p.id = '.$id
			;
		$db->setQuery($query);
		$row = $db->loadObject();

		$info = new JCommentsObjectInfo();

		if (!empty($row)) {
			$Itemid = self::getItemid('com_ishop', 'index.php?option=com_ishop&view=product');
			$Itemid = $Itemid > 0 ? '&Itemid='.$Itemid : '';

			$info->title = $row->name;
			$info->access = $row->available;
			$info->userid = 0;
			$info->link = JRoute::_('index.php?option=com_ishop&amp;view=product&amp;id='.$row->id);
		}

		return $info;
	}
}