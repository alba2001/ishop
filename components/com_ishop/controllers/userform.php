<?php

// No direct access.
defined('_JEXEC') or die;

// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');

class IshopControllerUserform extends JControllerForm
{




    public function submit()
	{
            // Check for request forgeries.
            JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
            $model = $this->getModel();

            // Get the data from the form POST
            $data = JRequest::getVar('jform', array(), 'post', 'array');

            // Now save the loaded data to the database via a function in the model
            // check if ok and display appropriate message.  This can also have a redirect if desired.
            if ($model->createItem($data)) 
            {
                $text = JTEXT::_('USER_SUCCES_SAVE');
                $mainframe = JFactory::getApplication();
                $url = $mainframe->getUserState('com_ishop.old_uri',NULL);
                $mainframe->setUserState('com_ishop.old_uri',NULL);
                if (isset($url))
                {
                    $mainframe->redirect($url, $text);
                }
                else 
                {
                    echo $text;
                }
            } 
            else 
            {
                echo JTEXT::_('USER_ERROR_SAVE');
            }

            return true;
        }
        
}
