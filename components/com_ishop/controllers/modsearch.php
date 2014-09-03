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

require_once JPATH_COMPONENT.'/controller.php';
require_once JPATH_ROOT.'/modules/mod_ishop_search/helper.php';

/**
 * Company controller class.
 */
class IshopControllerModsearch extends IshopController
{

    /**
     * Записываем способ доставки 
     */
    public function get_category_list()
    {
        // Check for request forgeries.
        JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));
        $brand_id = JRequest::getInt('brand_id');
        $category_id = JRequest::getInt('category_id');
        $categories = modIshop_searchHelper::getListCategory($category_id, $brand_id);
        echo $categories;
        exit;
    }
}