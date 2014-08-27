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

require_once JPATH_COMPONENT.'/helpers/component.php';

/**
 * View class for a list of Ishop.
 */
class IshopViewCategories extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $model;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->model		= $this->getModel();
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
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/ishop.php';
		JToolBarHelper::title(JText::_('COM_ISHOP_TITLE_CATEGORIES'), 'categories.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/category';
        if (file_exists($formPath)) {
			    JToolBarHelper::addNew('category.add','JTOOLBAR_NEW');
			    JToolBarHelper::editList('category.edit','JTOOLBAR_EDIT');
        }
            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('categories.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('categories.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
                            JToolBarHelper::deleteList('', 'categories.delete','JTOOLBAR_DELETE');
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'categories.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('categories.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('categories.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
			JToolBarHelper::preferences('com_ishop');

            JToolBarHelper::divider();
            JToolBarHelper::custom( 'categories.parse', 'parse', '', 'PARSE', TRUE, false );

	}
        
        /**
         * Наименование завода
         * @param int $site_alias
         * @return string 
         */
        protected function get_site_alias_name($site_alias)
        {
            return ComponentHelper::getSitealias_name($site_alias);
            
        }
        
}
