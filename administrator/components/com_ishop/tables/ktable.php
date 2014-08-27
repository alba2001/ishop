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

/**
 * city Table class
 */
class IshopTableKtable extends JTable {


    protected $asset_name = '';
    protected $_check_fields = array();
    protected $_date_fields = array();
    protected $_array_fields = array();
    protected $_phone_fields = array();
    
    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param	array		Named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see		JTable:bind
     * @since	1.5
     */
    public function bind($array, $ignore = '') {

        
//        if(!JFactory::getUser()->authorise('core.edit.state','com_ishop.city.'.$array['id']) && $array['state'] == 1){
//                $array['state'] = 0;
//        }

        $user = JFactory::getUser();
        if(!isset($array['created_by']))
        {
            $array['created_by'] = $user->id;
        }
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }
        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
                $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }
    

    /**
     * Overloaded check function
     */
    public function check() 
    {
        //If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0) {
            $this->ordering = self::getNextOrder();
        }
            // Конвертируем номер телефона
            foreach($this->_phone_fields as $phone_field)
            {
                if (substr($this->$phone_field,0,3) == '+7(') 
                {
                    preg_match("/\+7\(([0-9]{3})\) ([0-9]{3})-([0-9]{2})-([0-9]{2})/", $this->$phone_field, $regs);
                    $this->$phone_field = $regs[1].$regs[2].$regs[3].$regs[4];
                }
            }
        
            // Добавляем слеши к текстовым полям
            foreach($this->_check_fields as $check_field)
            {
                $this->$check_field = addslashes($this->$check_field);
            }
            // Преобразуем архив в текстовое поле
            foreach($this->_array_fields as $array_field)
            {
                $this->$array_field = json_encode($this->$array_field);
            }
            // Меняем формат даты
            foreach($this->_date_fields as $date_field)
            {
                preg_match("/([0-9]{2}).([0-9]{2}).([0-9]{4})/", $this->$date_field, $regs);
                if(count($regs) == 4)
                {
                    $this->$date_field = $regs[3].'-'.$regs[2].'-'.$regs[1];
                }
            }
            return parent::check();

        return parent::check();
    }

    /**
     * owerload load function
     * @param type $keys
     * @param type $reset
     * @return boolean 
     */
    public function load($keys = null, $reset = true) {
            if( parent::load($keys, $reset))
            {
                // Конвертируем номер телефона
                foreach($this->_phone_fields as $phone_field)
                {
                    if (preg_match("/([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})/", $this->$phone_field, $regs)) 
                    {
                        $this->$phone_field = '+7 ('.$regs[1].') '.$regs[2].'-'.$regs[3].'-'.$regs[4];
                    }
                }
                
                // Меняем формат даты
                foreach($this->_date_fields as $date_field)
                {
                    preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $this->$date_field, $regs);
                    if(count($regs) == 4)
                    {
                        $this->$date_field = $regs[3].'.'.$regs[2].'.'.$regs[1];
                    }
                }
                
                // Преобразуем текстовое поле в архив
                foreach($this->_array_fields as $array_field)
                {
                    $this->$array_field = json_decode($this->$array_field, TRUE);
                }
                
                // Конвертируем дату оплаты
                foreach($this->_check_fields as $check_field)
                {
                    $this->$check_field = stripcslashes($this->$check_field);
                }
                return TRUE;
            }
            return FALSE;
    }
    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer The user id of the user performing the operation.
     * @return    boolean    True on success.
     * @since    1.0.4
     */
    public function publish($pks = null, $state = 1, $userId = 0) {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        } else {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
                'UPDATE `' . $this->_tbl . '`' .
                ' SET `state` = ' . (int) $state .
                ' WHERE (' . $where . ')' .
                $checkin
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin each row.
            foreach ($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->state = $state;
        }

        $this->setError('');
        return true;
    }
    
    /**
      * Define a namespaced asset name for inclusion in the #__assets table
      * @return string The asset name 
      *
      * @see JTable::_getAssetName 
    */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_ishop.'.$this->asset_name.'.' . (int) $this->$k;
    }
 
    /**
      * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
      *
      * @see JTable::_getAssetParentId 
    */
    protected function _getAssetParentId($table = null, $id = null){
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_ishop');
        // Return the found asset-parent-id
        if ($assetParent->id){
            $assetParentId=$assetParent->id;
        }
        return $assetParentId;
    }
    
    /**
     * Возвращаем список объектов
     * @return object List of rows
     */
    public function get_rows($keys=FALSE, $order='') 
    {
        // Initialise the query.
        $query = $this->_db->getQuery(true);
        $query->select('*');
        $query->from($this->_tbl);
        if($keys)
        {
            if(is_array($keys))
            {
                foreach ($keys as $key=>$value)
                {
                    $query->where('`'.$key.'` = '.$value);
                }
            }
            else
            {
                
            $query->where('`'.$this->_tbl_key.'` = '.$key);
            }
        }
        if($order)
        {
            $query->order($order);
        }
        $this->_db->setQuery($query);

        return  $this->_db->loadObjectList();
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
                $type = $this->asset_name;
            }
		return JTable::getInstance($type, $prefix, $config);
	}

        /**
         * Загрузка строки из таблицы в виде объекта
         * @param id $id
         * @return std object 
         */
        protected function load_object($id)
        {
            $db = $this->getDbo();
            $query = $db->getQuery(TRUE);
            $query->select('*');
            $query->from('#__ishop_products');
            $query->where('`id` = '.$id);
            $db->setQuery($query);
            return $db->loadObject();
        }
        
}
