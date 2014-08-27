<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
 
/**
 * Users View
 */
class IshopViewUsers extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
    
	/**
	 * Users view display method
	 * @return void
	 */
	function display($tpl = null) 
	{

		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		$this->addToolbar();
        
        $input = JFactory::getApplication()->input;
        $view = $input->getCmd('view', '');
        IshopHelper::addSubmenu($view);
        
		parent::display($tpl);
 
		// Set the document
		$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
            JToolBarHelper::title(JText::_('COM_USER_ADMINISTRATION'), 'user.png');
            JToolBarHelper::addNew('user.add', 'JTOOLBAR_NEW');
            JToolBarHelper::editList('user.edit', 'JTOOLBAR_EDIT');
            JToolBarHelper::deleteList('', 'users.delete', 'JTOOLBAR_DELETE');
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_user');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_USER_ADMINISTRATION'));
	}
}
