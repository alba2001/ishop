<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once JPATH_SITE. '/components/com_ishop/models/caddy.php'; 
$caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
$model = new IshopModelCaddy;
$caddy_data = $model->get_caddy_data($caddy);
//echo '111';exit;
require JModuleHelper::getLayoutPath('mod_caddy', $params->get('layout', 'default'));
