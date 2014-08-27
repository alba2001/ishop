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
require_once dirname(__FILE__) . '/category.php'; 
/**
 * Ishop model.
 */
class IshopModelSite extends JModelAdmin
{
    public $error_msg = '';


    /**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_ISHOP';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Site', $prefix = 'IshopTable', $config = array())
	{
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

            // Get the form.
            $form = $this->loadForm('com_ishop.site', 'site', array('control' => 'jform', 'load_data' => $loadData));
            if (empty($form)) {
                    return false;
            }

            return $form;
	}
        
        /**
         * Возвращаем алиас сайта
         * @param integer $id
         * @return string 
         */
        public function get_site_alias($id)
        {
            list($alias, $msg) = $this->_get_alias($id);
            if(!$alias)
            {
                $this->error_msg = $msg;
                return '';
            }
            
            return $alias;
        }

        /**
         * Override parent save method
         * Добавили возможность создания категории 
         * при ручном создании завода
         * @param array $data
         * @return bolean 
         */
        public function save($data) 
        {
            if($data['id'])
            {
                return parent::save($data);
            }
            // Создание нового завода
            $result = FALSE;
            if( parent::save($data))
            {
                $site_id = $this->getState($this->getName() . '.id', 'id');
                $site_alias = $this->get_site_alias($site_id);
                $alias = JApplication::stringURLSafe($data['name']);
                // Создаем категорию для завода
                $category = array(
                    'name'=>  $data['name'],
                    'parent_id'=>'1',
                    'alias'=>$alias,
                    'path'=>$alias,
                    'level'=>1,
                    'site_alias'=>$site_alias,
                );
                $category_model = new IshopModelCategory;
                // Сохраняем категорию для завода
                $result = $category_model->create_category($category);
            }
            return $result;
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
		$data = JFactory::getApplication()->getUserState('com_ishop.edit.site.data', array());

		if (empty($data)) 
                {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM site');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}
       

        /**
         * Парсинг
         * @return array
         */
        public function parse()
        {
            // Определяем ИД завода
            $cids = JRequest::getVar('cid',array(),'','array');
            if(!$cids)
            {
                return array(0,  JText::_('COM_ISHOP_CIDS_NOT_SELECTED'));
            }
            $cid = $cids[0];
            list($alias, $msg) = $this->_get_alias($cid);
            if(!$alias)
            {
                return array(0, $msg);
            }
            
            // Вычисляем контроллер парсера
            jimport('joomla.filesystem.file');
            $file_path = JPATH_COMPONENT.'/parsers/'.$alias.'.php';
            if (!JFile::exists($file_path))
            {
                return array(0,  JText::_('COM_ISHOP_PARSER_DO_NOT_FIND'));
            }

            // Загружаем соотв. парсер
            require_once $file_path;
            $controller_name = 'IshopParser'.  ucfirst($alias); 
            $parser = new $controller_name;
            $parser->set_site_id($cid);
            
            return $parser->parce();
        }
        
   
    private function _get_alias($cid)
    {
        $db  = $this->getDbo();
        $query = $db->getQuery(TRUE)
                ->select('`alias`')
                ->from('`#__ishop_sites`')
                ->where('`id` = '.$cid)
        ;
        $db->setQuery($query);
        $alias = $db->loadResult();
        if(!$alias)
        {
            return array(0, JText::_('COM_ISHOP_ALIAS_NOT_FIND'));
        }
        
        return array($alias, '');
    }
        
}