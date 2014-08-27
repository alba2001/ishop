<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
require_once dirname(__FILE__) . '/kmodelform.php'; 
 
/**
 * Userform Model
 */
class IshopModelUserform extends ModelKModelform
{
	/**
	 * @var object item
	 */
	protected $item;
	/**
	 * @var string model name
	 */
	protected $model_name = 'userform';
	/**
	 * @var string table name
	 */
	protected $table_name = 'users';
	/**
	 * @var string form title
	 */
	protected $form_title = '';
 
        public function __construct($config = array()) {
            parent::__construct($config);
            $this->title = JTEXT::_('COM_ISHOP_USERFORM');
        }

	/**
	 * Get the item
	 * @return object The message to be displayed to the user
	 */
	public function getItem() 
	{
            $this->item = parent::getUser();
            return $this->item;
	}
}
