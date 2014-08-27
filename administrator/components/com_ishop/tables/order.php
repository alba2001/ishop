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
require_once dirname(__FILE__) . '/ktable.php'; 

/**
 * product Table class
 */
class IshopTableOrder extends IshopTableKtable {

    protected $asset_name;

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        $this->asset_name = 'order';
        parent::__construct('#__ishop_orders', 'id', $db);
        
    }
    /**
     * Overload bind function
     * @param string $array
     * @param type $ignore
     * @return type 
     */
    public function bind($array, $ignore = '') {
        // Если статус менялся менеджером, то заносим запись об этом в журнал
        $new_status = JRequest::getString('new_status', '');
        if($new_status)
        {
            $date = date('d.m.Y H:i:s');
            $user_name = JFactory::getUser()->username;
            $array['ch_status'] .= ' \n '.$date.' '.$user_name.' '.JText::_('COM_ISHOP_NEW_STATUS').' '.$new_status;
        }
        return parent::bind($array, $ignore);
    }
}
