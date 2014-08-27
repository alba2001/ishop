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

/**
 * Company controller class.
 */
class IshopControllerImage extends IshopController
{
    public function downloads()
    {
        $dir_dest = JPATH_ROOT.'/media/com_ishop/images/';
        $query = 'SELECT `id`, `desc` FROM #__ishop_products';
        $db = JFactory::getDbo();
        $db->setQuery($query);
        $products = $db->loadObjectList();
        foreach($products as $product)
        {
            $desc = json_decode($product->desc, TRUE);
            unset($desc['item_link']);
            foreach($desc as $dir=>$url)
            {
                $ar_path = explode('.', $url);
                $ext = $ar_path[count($ar_path)-1];
                $file_dest = $dir_dest.$dir.'/'.$product->id.'.'.$ext;
                if(!file_exists($file_dest))
                {
                    if ( copy($url, $file_dest) ) {
                       echo "Copy success! ".$file_dest.'<br>';
                    }else{
                        echo "Copy failed. ".$file_dest.'<br>';
                    }
                }
            }
        }
    }
    
}