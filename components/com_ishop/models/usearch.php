<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

// No direct access.
defined('_JEXEC') or die;

//jimport('joomla.application.component.model');
require_once dirname(__FILE__) . '/kmodelform.php'; 
/**
 * Ishop model.
 */
//class IshopModelUsearch extends JModel
class IshopModelUsearch extends ModelKModelform
{
    
    /**
     * Добавить новый заказ
     * @return bolean
     */
    public function ch_size($productvid_id)
    {
            $db =& JFactory::getDBO();
            $table = $db->NameQuote('#__ishop_productvids');
            $fields[] = $db->NameQuote('sizes');
            $where[] = $db->NameQuote('id').' = '.$productvid_id;
            $query = 'SELECT '.implode(',',$fields);
            $query .= ' FROM '.$table;
            $query .= ' WHERE '.implode(' AND ',$where);
            
            $db->setQuery($query);
            
            $state = array();
            $state[] = '<option value="">'.JText::_('MOD_USEARCH_NOT_IMPORTANT').'</option>';
            
            if ($list = $db->LoadResult())
            {
                $list = explode(';', $list);
                foreach ($list as $row)
                {
                    $state[] = "<option value=$row>$row</option>";
                }
            }
            return $state;
         }
    
}