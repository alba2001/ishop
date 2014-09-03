<?php
/**
 * @package		Uvelir.Site
 * @subpackage	mod_ishop_search
 * @copyright	Copyright (C) 2010 - 2014 Konstantin Ovcharenko.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$ishop_search_data = JFactory::getApplication()->getUserState('com_ishop.ishop_search', array());

if(!$ishop_search_data)
{
    $ishop_search_data = array(
        'brand' => '0',
        'category' => '0',
        'cena_from' => '',
        'cena_to' => '',
        'available' => '0',
        'artikul' => '',
        'text' => '',
    );
}
require_once dirname(__FILE__).'/helper.php';

$brands = modIshop_searchHelper::getListBrands($ishop_search_data['brand']);
$categories = modIshop_searchHelper::getListCategory($ishop_search_data['category'],$ishop_search_data['brand']);
$available = modIshop_searchHelper::getCheckboxAvailable($ishop_search_data['available']);

//echo '111';exit;
require JModuleHelper::getLayoutPath('mod_ishop_search', $params->get('layout', 'default'));
