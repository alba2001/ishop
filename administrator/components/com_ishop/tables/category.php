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
require_once dirname(__FILE__) . '/ntable.php'; 
//jimport('joomla.database.tablenested');


/**
 * product Table class
 */
class IshopTableCategory extends IshopTableNtable {
//class IshopTableCategory extends JTableNested {

    protected $asset_name;

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        $this->asset_name = 'category';
        parent::__construct('#__ishop_categories', 'id', $db);
        
    }
	/**
	 * Override check function
	 *
	 * @return  boolean
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check_()
	{
		return parent::check();
	}
 
	/**
	 * Override save function
	 *
	 * @return  boolean
	 *
	 */
        public function save($src, $orderingFilter = '', $ignore = '') 
        {
            if(isset($src['parent_id']))
            {
//                $this->setLocation($src['parent_id'], 'first-child');
            }
            
            if(!parent::save($src, $orderingFilter, $ignore))
            {
                return FALSE;
            }
            
            return $this->_update_menu();
        }
        
        
        private function _update_menu()
        {
            $id = $this->_find_menu_id($this->path);
            $menu_parent_id = $this->_get_parent_menu_id($this->path, JApplication::stringURLSafe($this->name));
            
                // Создаем пункт меню с этой категорией
                $menu = &$this->getTable('menu');
                // Если еще не создан пункт меню - создаем, если создан - переписываем
                // Nesteed tree
                $menu->setLocation( $menu_parent_id, 'last-child' );
                $component = JTable::getInstance('extension');
                $component->load(array('name'=>'com_ishop'));
                $data = array(
                            'id'=>$id,
                            'title'=>$this->name,
                            'alias'=>  str_replace('/', '_', $this->path),
                            'path'=>$this->path,
                            'menutype' => 'com_ishop',
                            'link' => 'index.php?option=com_ishop&view=category',
                            'type' => 'component',
                            'component_id' => $component->extension_id,
                            'published' => '1',
                            'parent_id' => $menu_parent_id,
                            'level' => $this->level,
                            'access' => '1',
                            );
                // Convert to the JObject before adding the params.
                $properties = $menu->getProperties(1);
                $result = JArrayHelper::toObject($properties, 'JObject');
                // Convert the params field to an array.
                $registry = new JRegistry;
                $registry->loadString($menu->params);
                $result->params = $registry->toArray();
                $result->params = array_merge($result->params, array('item_id'=>$this->id));
                $data['params'] = json_encode($result->params);
                if(!$menu->save($data))
                {
                    JFactory::getApplication()
                            ->enqueueMessage(JText::_('COM_ISHOP_ERROR_EDIT_MENU_RECORD'), 'error');
                    return FALSE;
                }
            return TRUE;
        }
        
        
        /**
         * Находим ИД пункта меню по его алиасу
         * @param string $alias
         * @return int 
         */
        private function _find_menu_id($path)
        {
            $menu = $this->getTable('menu');
            if($menu->load(array('path'=> $path)))
            {
                return $menu->id;
            }
            return 0;
        }

        /**
         * Вичисляем ИД родителя в меню
         * 
         * @param string $path
         * @param string $alias
         * @return string 
         */
        private function _get_parent_menu_id($path, $alias)
        {
            $menu_path = str_replace($alias, '', $path);
            $menu_path = preg_replace('/'.$alias.'$/', '', $path);
            if(substr($menu_path, -1) == '/' )
            {
                $menu_path = substr_replace($menu_path, "", -1);
            }
            $db = JFactory::getDbo();
            $query = $db->getQuery(TRUE)
                    ->select('`id`')
                    ->from('`#__menu`')
                    ->where('`path` = "'.$menu_path.'"')
            ;
            $db->setQuery($query);
            
            $result = $db->loadResult();
            return $result;
        }

        /**
	 * Override delete function
	 *
	 * @return  boolean
	 *
	 */
        
        public function delete_($pk = null) {
            if (!parent::delete($pk))
            {
                return FALSE;
            }
            // Удаляем соотв запись в меню
            $menu_id = $this->_find_menu_id($this->path);
            if($menu_id)
            {
                return $this->getTable('menu')->delete($menu_id);
            }
            return TRUE;
        }
}
