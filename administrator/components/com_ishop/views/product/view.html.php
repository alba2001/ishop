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
jimport('incase.init');

/**
 * View to edit
 */
class IshopViewProduct extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
        protected $prises;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
                $this->prises = array();
                if($this->item->id)
                {
                    JFactory::getApplication()->setUserState('com_ishop.product_id',$this->item->id);
                    $this->prises = ComponentHelper::getPrices($this->item->id);
                }
		$this->form = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
                    throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		$this->addDocStyle();
		parent::display($tpl);
	}
	/**
	 * Add the stylesheet to the document.
	 */
	protected function addDocStyle()
	{
            $document = JFactory::getDocument();
            $document->addStyleSheet('components/com_ishop/assets/css/ishop.css');
        }
	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
            JFactory::getApplication()->input->set('hidemainmenu', true);

            $user		= JFactory::getUser();
            if (isset($this->item->checked_out)) {
                        $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
            } else {
                $checkedOut = false;
            }
            if(isset($this->item->dopinfo))
            {
                $img = json_decode($this->item->dopinfo)->img_large;
                $img = incase::thumb($img, $this->item->id, 300, 300);
                $artikul = $this->item->artikul;
            }
            else
            {
                $img = JURI::base().'components/com_ishop/assets/images/l_products.png';
                $artikul = '';
            }
            JToolBarHelper::title('<img height="100" width="100" src="'.$img.'"/> '.$this->item->artikul, $img) ;
            JToolBarHelper::apply('product.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('product.save', 'JTOOLBAR_SAVE');
            JToolBarHelper::custom('product.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
            JToolBarHelper::custom('product.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
            if (empty($this->item->id)) {
                    JToolBarHelper::cancel('product.cancel', 'JTOOLBAR_CANCEL');
            }
            else {
                    JToolBarHelper::cancel('product.cancel', 'JTOOLBAR_CLOSE');
            }

	}
}
