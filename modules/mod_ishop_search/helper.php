<?php
/**
 * @package		Uvelir.Site
 * @subpackage	mod_ishop_search
 * @copyright	Copyright (C) 2010 - 2014 Konstantin Ovcharenko.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once JPATH_ROOT.'/components/com_ishop/helpers/ishop.php';

/**
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @since		1.5
 */
class modIshop_searchHelper
{
        function getListBrands($selected)
        {
            $attribs = array();
            $db =& JFactory::getDBO();
            $query = $db->getQuery(TRUE)
                    ->select('id')
                    ->select('name')
                    ->from('#__ishop_categories')
                    ->where('`state` = 1')
                    ->where('`parent_id` = 1')
            ;
            
            $db->setQuery($query);
            $state = array();
            $state[] = JHTML::_('select.option'
                    , 0
                    , JText::_('MOD_ISHOP_SEARCH_BRAND')
            );
            if ($list = $db->LoadObjectList())
            {
                foreach ($list as $row)
                {
                    $state[] = JHTML::_('select.option'
                            , $row->id
                            , JText::_($row->name)
                    );
                }
            }
            return JHTML::_('select.genericlist'
                            , $state
                            , 'ishop_search_data[brand]'
                            , $attribs
                            , 'value'
                            , 'text'
                            , $selected
                            , 'mod_ishop_search_brand'
                            , false );
         }
         
        function getListCategory($selected, $parent_id = 1)
        {
            $attribs = array();
            
            $category_model = IshopHelper::getModel('category');
            $list = $category_model->get_children($parent_id);
            $parent_level = $category_model->get_parent_level($parent_id);
            
            $state = array();
            $state[] = JHTML::_('select.option'
                    , 0
                    , JText::_('MOD_ISHOP_SEARCH_CATEGORY')
            );
            if ($list)
            {
                foreach ($list as $row)
                {
                    if($row->name)
                    {
                        $state[] = JHTML::_('select.option'
                                , $row->id
                                , str_repeat("...|", $row->level-$parent_level-1).JText::_($row->name)
                        );
                    }
                }
            }
            return JHTML::_('select.genericlist'
                            , $state
                            , 'ishop_search_data[category]'
                            , $attribs
                            , 'value'
                            , 'text'
                            , $selected
                            , 'mod_ishop_search_category'
                            , false );
         }
 
         function getCheckboxAvailable($checked)
         {
             $checked = $checked?'checked="checked"':'';
             $html = '<input type="checkbox" id="mod_ishop_search_available" name="ishop_search_data[available]" value="1" '.$checked.' />';
             return $html;
         }
}
