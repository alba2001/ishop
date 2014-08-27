<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */


// no direct access
defined('_JEXEC') or die;
// Import CSS
$document = JFactory::getDocument();
$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
$document->addScript(JURI::base().'components/com_ishop/assets/scripts/jquery.maskedinput-1.3.min.js');
$document->addStyleSheet(JURI::base().'components/com_ishop/assets/css/style.css');
        
// Include dependancies

JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_contact'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('ishop');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();