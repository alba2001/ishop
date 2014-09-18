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

jimport('joomla.application.component.model');
require_once JPATH_ROOT.'/administrator/components/com_ishop/helpers/component.php';

/**
 * Ishop model.
 */
class IshopModelProduct extends JModelLegacy
{
    
    var $_item = null;
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
            $app = JFactory::getApplication('com_ishop');
            // Load the parameters.
            $params = $app->getParams();
            $params_array = $params->toArray();
            // Завод
            if(isset($params_array['site'])){
                $this->setState('product.site', $params_array['site']);
            }
            else
            {
                $site = JRequest::getInt('site',0);
                $this->setState('product.site', $site);
            }
            // Продукт
            $alias = JRequest::getString('alias','');

            if ($alias) // если есть артикул, то сразу загружаем продукт
            {
//                $table = $this->getTable('Product_'.$site);
//                if($table->load(array('alias'=>$alias)))
//                {
                   $this->setState('product.id', $alias);
//                   $this->setState('product.id', $table->id);
//                }
            }
            elseif(isset($params_array['item_id'])){
                $this->setState('product.id', $params_array['item_id']);
            }
            else
            {
                $item_id = JRequest::getInt('item_id',0);
                $this->setState('product.id', $item_id);
            }
            
            $this->setState('params', $params);

	}
        

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('product.id');
			}

			// Get a level row instance.
			$table = $this->getTable('Product');

                        
			// Attempt to load the row.
			if ($table->load($id))
			{
                                // Прибавляем хит к продукту
                                $table->hits++;
                                $table->store();
                                
                                // Устанавливаем куку, чтобы получать ее в просмотренных товарвх
                                $this->_set_cookie_products($id);
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			} elseif ($error = $table->getError()) {
				$this->setError($error);
			}
                        
		}
                
		return $this->_item;
	}
        
        
        public function getTable($type = 'Product', $prefix = 'IshopTable', $config = array())
	{   
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
        return JTable::getInstance($type, $prefix, $config);
	}     
 
        /**
         * Возвращаем пересчитанные средний вес и сумму изделия
         * @return array Средний вес и сумма
         */
        public function change_size()
        {
            $result = array(
                'average_weight' => 0,
                'cena_tut' => 0,
                'cena_mag' => 0,
                'count' => 0,
            );
            $cid = JRequest::getInt('cid', 0);
            if($cid)
            {
                $razmer_key = JRequest::getInt('razmer_key', 0);
                $table = $this->getTable('Product');
                if($table->load($cid))
                {
                    $average_weights = explode(',', $table->average_weight);
                    if(isset($average_weights[$razmer_key]))
                    {
                        $caddy = JFactory::getApplication()->getUserState('com_ishop.caddy', array());
                        if(isset($caddy[$cid.'_'.$razmer_key]))
                        {
                            $result['count'] = $caddy[$cid.'_'.$razmer_key]['count'];
                        }
                        $result['average_weight'] = $average_weights[$razmer_key];
                        $prises = ComponentHelper::getPrices($cid, $razmer_key);
                        $result['cena_tut'] = $prises['cena_tut'];
                        $result['cena_mag'] = $prises['cena_mag'];
                    }
                }
            }
            
            return $result;
        }
        

        /**
         * Устанавливаем куку на просматриваемый товар
         * @param int $id 
         */
        private function _set_cookie_products($id)
        {
            $seen_products = JRequest::getString('seen_products', NULL, 'cookie');
            
            if(empty($seen_products))
            {
                $seen_products = array();
            }
            else
            {
                $seen_products = explode('prod',$seen_products);
            }
            
            array_unshift($seen_products, $id);
            $seen_products = array_slice(array_unique($seen_products), 0, 20);
            
            $seen_products = implode('prod',$seen_products);
            setcookie('seen_products', $seen_products, time()+60*60*24*30, '/');
        }
        
        public function getURI($product_id)
        {
            $uri = JURI::base();
            $db = $this->_db;
            $query_1 = $db->getQuery(TRUE)
                    ->select('`category_id`')
                    ->from('#__ishop_product_category')
                    ->where('`product_id` = '.$product_id)
            ;
            $db->setQuery($query_1);
            $category_id = $db->loadResult();
            if(!$category_id)
            {
                return $uri;
            }
            
            $query_2 = $db->getQuery(TRUE)
                    ->select('`path`')
                    ->from('#__ishop_categories')
                    ->where('`id` = '.$category_id)
            ;
            $db->setQuery($query_2);
            $path = $db->loadResult();
            if(!$path)
            {
                return $uri;
            }
            $ar_path = explode('/', $path);
            $site_alias =  $ar_path[0];
            $uri .= str_replace('/', '/'.$site_alias.'_', $path).'/'.$product_id;
            
            return $uri;
        }
}