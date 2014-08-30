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
    <form 
        action="<?php echo JRoute::_('index.php'); ?>" 
        method="post" 
        name="mod_caddy"
        class="btn-header"
        id="mod_caddy"
    >
        <dl>
            <span id="mod_caddy_product_sum"><?php echo $caddy_data['sum']?></span>
            <span class="rub"><?php echo JText::_('MOD_CADDY_RUB')?></span>
            <span class="t">товаров:</span>
            <span id="mod_caddy_product_count"><?php echo $caddy_data['count']?></span> <span class="t">шт.</span>
        </dl>
        <input 
            type="submit" 
            value=""
            id="btn-cart"
        >

        <input type="hidden" name="option" value="com_ishop" />
        <input type="hidden" name="view" value="caddy" />
        <?php echo JHtml::_('form.token'); ?>
    </form>    
</div>