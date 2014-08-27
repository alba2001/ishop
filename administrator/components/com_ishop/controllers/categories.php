<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Categories list controller class.
 */
class IshopControllerCategories extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'category', $prefix = 'IshopModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
        
    /**
     * Парсинг категорий
     */
    public function parse()
    {
        // Check for request forgeries.
//        JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));

        $result = $this->getModel('categories')->parse_one_catrgory();
        if($result[0])
        {
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view=sites&site='.$result[1], false));
        }
//        echo json_encode($result);
//        exit;
    }
        
}