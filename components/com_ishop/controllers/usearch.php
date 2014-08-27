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

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Company controller class.
 */
class IshopControllerUsearch extends IshopController
{
    /**
     * Добавить товар в корзину 
     */
    function ch_size()
    {
        // Check for request forgeries.
        JSession::checkToken('GET') or jexit(JText::_('JINVALID_TOKEN'));
        $product_vid = JRequest::getString('productvid');
        $model = $this->getModel('Usearch');
        $result = $model->ch_size($product_vid);
        echo implode(' ', $result);
        exit;
    }
}