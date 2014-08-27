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

jimport('joomla.application.component.modeladmin');

/**
 * Ishop model.
 */
class IshopModelProduct extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_ISHOP';
        
        private $_product_id = 0;
        private $_category_id = 0;

        public function set_product_id($product_id)
        {
            $this->_product_id = $product_id;
        }

        public function set_category_id($category_id)
        {
            $this->_category_id = $category_id;
        }

        /**
         * Override parent function
         * 
         * @param type $pk
         * @return type 
         */
        public function getItem($pk = null) {
            $item =& parent::getItem($pk);
            $item->categories = $this->_get_categories($item->id);
            return $item;
        }
        
        private function _get_categories($pk)
        {
            $query = $this->_db->getQuery(TRUE);
            $query->from('`#__ishop_categories` AS a');
            $query->innerjoin('`#__ishop_product_category` AS pc ON a.id = pc.category_id');
            $query->where('pc.product_id = '.$pk);
            $query->select('a.id');
            $query->select('a.name');
            $this->_db->setQuery((string)$query);
            
            return $this->_db->loadObjectList();
        }

                /**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = '', $prefix = 'IshopTable', $config = array())
	{
            if(!$type)
            {
                $type = 'Product';
            }
            return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_ishop.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_ishop.edit.product.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}
                if(isset($data->product_category_table))
                {
                    unset($data->product_category_table);
                }
		return $data;
	}

        /**
         * Возвращаем массив ИД продуктов, в которых есть продукты с этим  кодом
         * @param string
         * @return array
         */
         private function _get_saved_product($data)
         {
            if(!$data['code'] OR !$data['site_alias'])
            {
                return FALSE;
            }
            $query = $this->_db->getQuery(TRUE)
                    ->select('`id`')
                    ->from('`#__ishop_products`')
                    ->where('`code` = "'.$data['code'].'"')
                    ->where('`site_alias` = "'.$data['site_alias'].'"')
            ;
            
            $this->_db->setQuery($query);
            $result = $this->_db->loadResult();
            if(!isset($result))
            {
                return FALSE;
            }
            return $result;
         }

         /**
         * Запись отпарсеных продуктов
         * @param type $data
         * @return type 
         */
        public function save_product($data)
        {
            
            $id = $this->_get_saved_product($data);
            if((int)$data['cena_tut']===0 AND (int)$data['cena_mag']!==0)
            {
                $data['cena_tut'] = $data['cena_mag'];
            }
            
            if($id) 
            {
                $table = $this->getTable('Product');
                $table->load($id);
                
                // Записываем цену здесь как цену в магазине, 
                // если не установлена цена здесь
                if((int)$table->cena_tut !== 0)
                {
                    $data['cena_tut'] = $table->cena_tut;
                }
                return $table->save($data);
            }
            
            return $this->save($data);
        }
        
        /**
         * Overload parent save method
         * @param type $data 
         */
        public function save($data) 
        {
            
            // Вставляем дату создания
            if(!isset($data['created_dt']) OR !$data['created_dt'])
            {
                $data['created_dt'] = date('Y-m-d');
            }
            
            // Вставляем рисунки
            if(isset($data['product_image']) AND $data['product_image'])
            {
                $uri_base = str_replace('administrator/', '',  JURI::base());
                $img_src = $uri_base.$data['product_image'];
                $desc = array(
                        'img_medium'=>$img_src,
                        'img_large'=>$img_src,
                        'img_small'=>$img_src,
                    );
                $data['desc'] = json_encode($desc);
                unset($data['product_image']);
            }
            return parent::save($data);
        }
        
        /**
         * Удаление категории к товару
         * 
         * @return array 
         */
        public function remove_category()
        {
            $data = array();
            $data['product_id'] = $this->_product_id?$this->_product_id:JRequest::getInt('product_id', 0);
            if(!$data['product_id'])
            {
                return array(0, JText::_('PRODUCT_ID_NOT_DEFINED'));
            }
            
            $data['category_id'] = $this->_category_id?$this->_category_id:JRequest::getInt('category_id', 0);
            if(!$data['category_id'])
            {
                return array(0, JText::_('CATEGORY_ID_NOT_DEFINED'));
            }
            
            if($this->_is_last_category($data['product_id']))
            {
                return array(0, JText::_('YOU_CANNOT_REMOVE_LAST_CATEGORY'));
            }
            
            if(!$this->_remove_category($data))
            {
                return array(0, JText::_('ERROR_REMOVED_CATEGORY'));
            }
            
            return array(1, JText::_('SUCESS_REMOVED_CATEGORY'));
        }
        
        private function _is_last_category($product_id)
        {
            $query = $this->_db->getQuery(TRUE);
            $query->select('category_id');
            $query->from('#__ishop_product_category');
            $query->where('product_id = '.$product_id);
            
            $this->_db->setQuery($query);
            $category_ids = $this->_db->loadResultArray();
            
            if(count($category_ids)>1)
            {
                return FALSE;
            }
            
            return TRUE;
        }

        /**
         * Удаление категории у продукта
         * @param type $keys
         * @return boolean 
         */
        private function _remove_category($keys)
        {
            $table = $this->getTable('product_category');
            if(!$table->load($keys))
            {
                return FALSE;
            }
            return $table->delete();
        }

        /**
         * Добавление категории к товару
         * 
         * @return array 
         */
        public function add_category()
        {
            $data = array();
            $data['product_id'] = $this->_product_id?$this->_product_id:JRequest::getInt('product_id', 0);
            if(!$data['product_id'])
            {
                return array(0, JText::_('PRODUCT_ID_NOT_DEFINED'));
            }
            
            $data['category_id'] = $this->_category_id?$this->_category_id:JRequest::getInt('category_id', 0);
            if(!$data['category_id'])
            {
                return array(0, JText::_('CATEGORY_ID_NOT_DEFINED'));
            }
            
            if($this->_check_category_exist($data))
            {
                return array(0, JText::_('THIS_CATEGORY_ALLREADY_EXIST'));
            }
            
            if(!$this->_add_category($data))
            {
                return array(0, JText::_('ERROR_ADDING_CATEGORY'));
            }
            
            return array(1, $data['category_id'], $this->_get_category_name($data['category_id']));
        }
        
        /**
         * Проверка наличия дубля записи в таблице product_category
         * по product_id и category_id
         * 
         * @param array $keys
         * @return bool
         */
        private function _check_category_exist($keys)
        {
            $table = $this->getTable('product_category');
            
            return $table->load($keys);
        }

        /**
         * Добавляем категорию
         * @param array $data
         * @return bool 
         */
        private function _add_category($data)
        {
            $table = $this->getTable('product_category');
            
            return $table->save($data);
        }
        
        /**
         * Возвращаем название категории
         * @param int $category_id
         * @return string 
         */
        private function _get_category_name($category_id)
        {
            $table = $this->getTable('category');
            if(!$table->load($category_id))
            {
                return '';
            }
                
            return $table->name;
        }
}