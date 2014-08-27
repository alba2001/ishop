<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
 
/**
 * Order Controller
 */
class IshopControllerOrder extends JControllerForm
{
    
    protected function checkEditId($context, $id) {
        return TRUE;
    }
}
