<?php
/**
 * @package		Ishop.Site
 * @subpackage	mod_ishop_search
 * @copyright	Copyright (C) 2010 - 2013 Konstantin Ovcharenko.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div id="mod_ishop_search_filter">
    <form action="<?php echo JRoute::_('index.php'); ?>" method="get" name="ishop_searchForm" id="ishop_searchForm">

        <div class="block">
            <label for="mod_ishop_search_text">
                <span>Название: </span>
            </label>
            <input type="text" id="mod_ishop_search_text" name="ishop_search_data[text]" value="<?=$ishop_search_data['text']?>" placeholder="" > 
        </div>   

        <div class="block">
            <label for="mod_ishop_search_brand">
                <span><?php echo JText::_('MOD_ISHOP_SEARCH_BRAND')?>: </span>
            </label>
            <?=$brands?>
        </div>

        <div class="block no-label">
            <label for="mod_ishop_search_available"><?=$available?><span><?=  JText::_('MOD_ISHOP_SEARCH_IN_STOCK')?></span></label>
        </div>
        
        <div class="block">
            <label>
                <span><?=  JText::_('MOD_ISHOP_SEARCH_PRICE_RUB')?></span>
            </label>
            
            <label for="mod_ishop_search_cena_from" class="left">
                <span><?=  JText::_('MOD_ISHOP_SEARCH_FROM')?></span>
                <input type="text" id="mod_ishop_search_cena_from" name="ishop_search_data[cena_from]" value="<?=$ishop_search_data['cena_from']?>" placeholder="" class="price"/>
            </label>

            <label for="mod_ishop_search_cena_to" class="left">
                <span><?=  JText::_('MOD_ISHOP_SEARCH_TO')?></span>
                <input type="text" id="mod_ishop_search_cena_to" name="ishop_search_data[cena_to]" value="<?=$ishop_search_data['cena_to']?>" placeholder="" class="price"/>
            </label>
        </div>

        <div class="block">
            <label for="mod_ishop_search_category">
                <span><?php echo JText::_('MOD_ISHOP_SEARCH_CATEGORY')?>: </span>
            </label>
            <div id="mod_ishop_div_search_category">
                <?=$categories?>
            </div>
        </div>
        

        <div class="block no-label last">
            <input type="submit" class="button" value="<?=JText::_('MOD_ISHOP_SEARCH_FIND')?>">        
        </div>

        <input type="hidden" name="option" value="com_ishop" />
        <input type="hidden" name="view" value="products" />
        <input type="hidden" name="show_menu_groups" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
<script>
    jQuery(document).ready(function($){
        $('#mod_ishop_search_brand').change(function(){
            var brand_id = $(this).val();
            var category_id = $('#mod_ishop_search_category').val();
            $.ajax({
                type: 'GET',
                data:{
                    option: 'com_ishop',
                    task: 'modsearch.get_category_list',
                    brand_id: brand_id,
                    category_id: category_id
                },
                url: '<?=JURI::base()?>index.php?<?=JSession::getFormToken()?>=1',
                success: function(html){
                    $('#mod_ishop_div_search_category').html(html);
               }
           });
        });
    });
</script>