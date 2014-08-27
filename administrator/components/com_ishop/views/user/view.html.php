<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * User View
 */
class IshopViewUser extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');
 
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;
 
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display($tpl);
 
		// Set the document
		$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		JRequest::setVar('hidemainmenu', true);
		$isNew = $this->item->id == 0;
		JToolBarHelper::title($isNew ? JText::_('COM_ISHOP_MANAGER_USER_NEW') : JText::_('COM_ISHOP_MANAGER_USER_EDIT'), 'user');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
                        JToolBarHelper::apply('user.apply', 'JTOOLBAR_APPLY');
                        JToolBarHelper::save('user.save', 'JTOOLBAR_SAVE');
                        JToolBarHelper::custom('user.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			JToolBarHelper::cancel('user.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
                        // We can save the new record
                        JToolBarHelper::apply('user.apply', 'JTOOLBAR_APPLY');
                        JToolBarHelper::save('user.save', 'JTOOLBAR_SAVE');

                        // We can save this record, but check the create permission to see if we can return to make a new one.
                        JToolBarHelper::custom('user.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
                        JToolBarHelper::custom('user.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			JToolBarHelper::cancel('user.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_ISHOP_ISHOP_CREATING') : JText::_('COM_ISHOP_ISHOP_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_ishop/views/user/submitbutton.js");
//                $document->addScript(JURI::base().'components/com_ishop/assets/scripts/jquery.maskedinput-1.3.min.js');
		JText::script('COM_ISHOP_ISHOP_ERROR_UNACCEPTABLE');
	}
}
