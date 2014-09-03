<?php
// no direct access
defined('_JEXEC') or die;
?>

<div class="custom search-module">
    <form action="<?php echo JRoute::_('index.php'); ?>" method="get" name="ishop_searchForm_short" id="ishop_searchForm">
        <input type="text" name="ishop_search_data[text]" value="" placeholder="<?=  JText::_('MOD_ISHOP_SEARCH_SEARCH')?>" > 
        <a href="rasshirennyj-poisk"><?=  JText::_('MOD_ISHOP_SEARCH_SEARCH_WIDTH')?></a>
        <input type="submit" name="" class="search-button" value="">        

        <input type="hidden" name="option" value="com_ishop" />
        <input type="hidden" name="view" value="products" />
        <input type="hidden" name="show_menu_groups" value="0" />
        <?php echo JHtml::_('form.token'); ?>  
    </form>
</div>

