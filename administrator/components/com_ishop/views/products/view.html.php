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
class IshopViewProducts extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
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
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/ishop.php';

                $canDo	= IshopHelper::getActions('com_ishop.products');

		JToolBarHelper::title(JText::_('COM_ISHOP_TITLE_PRODUCTS'), 'products.png');

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('product.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('product.edit','JTOOLBAR_EDIT');
		    }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::custom('products.available', 'publish.png', 'publish_f2.png','JTOOLBAR_AVAILABLE', true);
			    JToolBarHelper::custom('products.unavailable', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNAVAILABLE', true);
			    JToolBarHelper::divider();
			    JToolBarHelper::addNew('product.add','JTOOLBAR_NEW');
			    JToolBarHelper::editList('product.edit','JTOOLBAR_EDIT');
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('products.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('products.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			    JToolBarHelper::divider();
                            JToolBarHelper::deleteList('', 'products.delete','JTOOLBAR_DELETE');
			    JToolBarHelper::divider();
                            JToolBarHelper::custom( 'products.fill_cenas', 'unpublish.png', '', 'FILL_CENAS', FALSE, false );
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'products.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('products.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('products.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_ishop');
		}
	}
        
        /**
         * Наименование категории с путем
         * @param int $category_id
         * @return string
         */
        protected function get_category_path($category_id)
        {
            return ComponentHelper::getCategory_path($category_id);
        }
}
