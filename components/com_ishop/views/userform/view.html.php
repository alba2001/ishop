<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
// import Joomla html for use with stylesheets
jimport('joomla.html.html');

/**
 * HTML View class for the UpdIshop Component
 */
class IshopViewUserform extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{

		// Get some data from the models
		$this->form	= $this->get('Form');
		$this->user	= $this->get('User');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// get the stylesheet and/or other document values
        $this->addDocStyle();

		// Display the view
        parent::display($tpl);

	}

	/**
	 * Add the stylesheet to the document.
	 */
	protected function addDocStyle()
	{
        $doc = JFactory::getDocument();
        $doc->setTitle(JText::_('COM_ISHOP_USERFORM'));
        $doc->addStyleSheet('media/com_ishop/css/site.stylesheet.css');
        $doc->addScript(JURI::root()."media/system/js/validate.js");
        $doc->addScript(JURI::root()."components/com_ishop/views/userform/submitbutton.js");
        $doc->addScript(JURI::root()."components/com_ishop/models/forms/userform.js");
        $doc->addScript(JURI::base().'components/com_ishop/assets/js/jquery.maskedinput-1.3.min.js');
    }
}
