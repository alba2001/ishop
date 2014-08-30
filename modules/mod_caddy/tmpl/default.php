<?php
/**
 * @package		Ishop.Site
 * @subpackage	mod_caddy
 * @copyright	Copyright (C) 2010 - 2013 Konstantin Ovcharenko.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div id="mod_caddy_ishop">
    <ul>
        <li>
            <?php echo JText::_('MOD_CADDY_PRODUCT_CADDY_COUNT').': '?>
            <span id="mod_caddy_product_count"><?php echo $caddy_data['count']?></span>
            <?php echo JText::_('MOD_CADDY_PRODUCT_COUNT')?>
        </li>
        <li>
            <?php echo JText::_('MOD_CADDY_PRODUCT_CADDY_SUM').': '?>
            <span id="mod_caddy_product_sum"><?php echo $caddy_data['sum']?></span>
            <?php echo JText::_('MOD_CADDY_RUB')?>
        </li>
    </ul>
    <form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
        <input type="submit" value="<?=JText::_('MOD_CADDY_CHECKOUT')?>">
        <input type="hidden" name="option" value="com_ishop" />
        <input type="hidden" name="view" value="caddy" />
        <?php echo JHtml::_('form.token'); ?>
    </form>    
</div>