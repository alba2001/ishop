<?php
/**
 * @package		Ishop.Site
 * @subpackage	mod_usearch
 * @copyright	Copyright (C) 2010 - 2013 Konstantin Ovcharenko.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div id="mod_usearch_filter">
    <form action="<?php echo JRoute::_('index.php'); ?>" method="get" name="usearchForm" id="usearchForm">

        <div class="block">
            <label for="mod_usearch_text">
                <span>Название: </span>
            </label>
            <input type="text" id="mod_usearch_text" name="usearch_data[text]" value="<?=$usearch_data['text']?>" placeholder="" > 
        </div>   

        <div class="block">
            <label for="mod_usearch_brand">
                <span><?php echo JText::_('MOD_USEARCH_BRAND')?>: </span>
            </label>
            <?=$brands?>
        </div>

        <div class="block no-label">
            <label for="mod_usearch_available"><?=$available?><span>в наличии</span></label>
        </div>
        
        <div class="block">
            <label>
                <span>Цена (в рублях): </span>
            </label>
            
            <label for="mod_usearch_cena_from" class="left">
                <span>от</span>
                <input type="text" id="mod_usearch_cena_from" name="usearch_data[cena_from]" value="<?=$usearch_data['cena_from']?>" placeholder="" class="price"/>
            </label>

            <label for="mod_usearch_cena_to" class="left">
                <span>до</span>
                <input type="text" id="mod_usearch_cena_to" name="usearch_data[cena_to]" value="<?=$usearch_data['cena_to']?>" placeholder="" class="price"/>
            </label>
        </div>

        <div class="block">
            <label for="mod_usearch_category">
                <span><?php echo JText::_('MOD_USEARCH_CATEGORY')?>: </span>
            </label>
            <?=$categories?>
        </div>
        
        <div class="block">
            <label for="mod_usearch_artikul">
                <span><?php echo JText::_('MOD_USEARCH_ARTIKUL')?>: </span>
            </label>
            <input type="text" name="usearch_data[artikul]" value="<?=$usearch_data['artikul']?>"/>
        </div>

        <div class="block no-label last">
            <input type="submit" class="button" value="Найти">        
        </div>

        <input type="hidden" name="option" value="com_ishop" />
        <input type="hidden" name="view" value="products" />
        <input type="hidden" name="show_menu_groups" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>