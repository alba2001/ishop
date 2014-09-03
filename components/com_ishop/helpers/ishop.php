<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

defined('_JEXEC') or die;
jimport('incase.init');

abstract class IshopHelper
{
	public static function isKoltsa($product_id)
	{
            require_once JPATH_COMPONENT.'/models/products.php';
            $products = new IshopModelProducts;
            return $products->isKoltsa($product_id);
	}

	public static function getURI($product_id)
	{
            require_once JPATH_SITE.DS.'components'.DS.'com_ishop'.DS.'models'.DS.'product.php';
            $products = new IshopModelProduct;
            return $products->getURI($product_id);
	}
        
        
        public static function get_src($src, $item_id)
        {
            $src= incase::thumb($src, $item_id, 315, 495);
            
            $dir_dest = JPATH_ROOT.'/media/com_ishop/images/img_small/';
            $url_dest = JURI::base().'media/com_ishop/images/img_small/';
            $ar_path = explode('.', $src);
            $ext = $ar_path[count($ar_path)-1];
            $file_dest = $dir_dest.$item_id.'.'.$ext;
            if(file_exists($file_dest))
            {
                    $src = $url_dest.$item_id.'.'.$ext;
            }
            
            return $src;
        }
        
        /**
         * Возвращаем объект модели по указанному имени
         * @param streing $model_name
         * @return object \model_class_name
         */
        public static function getModel($model_name)
        {
            $model_class_name = 'IshopModel'.ucfirst($model_name);
            require_once JPATH_SITE.DS.'components'.DS.'com_ishop'.DS.'models'.DS.$model_name.'.php';
            
            return new $model_class_name;
        }

}

