<?php
/**
 * @version		$Id: mod_ishop.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
 * @package		ISHOP
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

define("DS", DIRECTORY_SEPARATOR);

require_once(dirname(__FILE__).DS.'helper.php');
$db = JFactory::getDbo();

$order_goods = $params->get('order_goods', 'nothing');

$limit =  $params->get('itemCount', 5);
switch ($order_goods)
{
    case 'seen_products':
        $items = modIshopContentHelper::getSeenProducts($limit);
        break;
    case 'discounts':
        $items = modIshopContentHelper::getDisconts($limit);
        break;
    case 'new_flag':
    case 'recommended_flag':
        $items = modIshopContentHelper::getByFlag($order_goods, $limit);
        break;
    default :
    $items = modIshopContentHelper::getGoods(array($limit, $order_goods));
}

if(count($items)){
	require(JModuleHelper::getLayoutPath('mod_ishop', 'default'));
}
