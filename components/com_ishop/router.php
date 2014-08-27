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
 * @param	array	A named array
 * @return	array
 */
function IshopBuildRoute(&$query)
{
    
	$segments = array();
    
	if (isset($query['task'])) {
		$segments[] = implode('/',explode('.',$query['task']));
		unset($query['task']);
	}
	if (isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}
	if (isset($query['alias'])) {
		$segments[] = $query['alias'];
		unset($query['alias']);
	}
       return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/ishop/task/id/Itemid
 *
 * index.php?/ishop/id/Itemid
 */
function IshopParseRoute($segments)
{
	$vars = array();
    
	// view is always the first element of the array
	$count = count($segments);
        if($count)
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(TRUE)
                    ->select('alias')
                    ->from('#__ishop_sites')
            ;
            $db->setQuery($query);
            $site_aliases = $db->loadColumn();
            
            foreach ($site_aliases as $site_alias)
            {
                if(preg_match("/$site_alias/", $_SERVER["REQUEST_URI"], $regs))
                {
                    $vars['site_alias'] = $site_alias;
                }
            }

            $segment = array_pop($segments) ;
            $vars['alias'] = $segment;
            $vars['view'] = 'product';
        }
    	return $vars;
}
