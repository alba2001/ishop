<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelform');
require_once dirname(__FILE__) . '/modelhelper.php';  
/**
 * Userform Model
 */
class ModelKModelform extends JModelForm
{
        
        protected $_tablelist;
	/**
	 * @var object item
	 */
	protected $item;
	/**
	 * @var string model name
	 */
	protected $model_name = 'ishop';
	/**
	 * @var string table name
	 */
	protected $table_name = 'ishop';
	/**
	 * @var string form title
	 */
	protected $form_title = '';


       public function __construct($config = array()) {
            parent::__construct($config);
       }

       /**
	 * Get the data for a new qualification
	 */
	public function getForm($data = array(), $loadData = true)
	{
            // Get the form.
            $form = $this->loadForm('com_ishop.'.$this->model_name, $this->model_name, array('control' => 'jform', 'load_data' => true));
            if (empty($form)) {
                    return false;
            }
            return $form;

	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() 
	{
            parent::populateState();
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
            $type = $type?$type:$this->table_name;
            return JTable::getInstance($type, $prefix, $config);
	}
 
        /**
         * Данные формы для заполнения
         * @return std object Item
         */
        public function getItem($id = null)
        {
            if(!isset($id))
            {
                $id = JFactory::getApplication()->getUserState('com_ishop.'.$this->table_name.'_id',0,0);           
            }
            $table = $this->getTable($this->table_name);
            if($id AND $table->load($id))
            {
                $this->item =& $table;
            }
            else 
            {
               $this->item = new stdClass;
               $this->item->id = 0;   
            }
            return $this->item;
        }

        /**
	 * Get the user
	 * @return object The message to be displayed to the user
	 */
	public function getUser() 
	{
            $this_user = ModelHelper::getUser();
            return $this_user;
	}
        /**
         * Сохранение записи о клиенте
         * @param type $data
         * @return boolean 
         */
	public function createItem($data)
	{
            $table = $this->getTable($this->table_name);
            
            if (!$table->save($data)) 
            {
                JError::raiseError(500, $this->_db->getErrorMsg());
//                var_dump($this->_db->getErrorMsg());exit;
                return false;
            }
            else
            {
                JFactory::getApplication()->setUserState('com_ishop.'.$this->table_name.'_id',$table->id);
                return TRUE;
            }
        }

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		return $this->getItem();
	}

        /**
         * Заголовок формы
         * @return string
         */
        public function getTitle()
        {
            return $this->title;;
        }

        /**
         * Возвращаем вспомогательные таблицы
         * @return array of objects Список вспомогательных таблиц
         */
        public function getTablelist()
        {
            $tablelist = array();
            
            foreach ($this->_tablelist as $table_alias)
            {
                $table = $this->getTable($table_alias,'IshopTable');
                $tablelist[$table_alias] = $table->get_rows();
            }
            return $tablelist;
        }
        
}
