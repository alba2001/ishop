<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */
// no direct access
defined('_JEXEC') or die;
require_once JPATH_COMPONENT.'/helpers/khtml.php';
jimport('incase.init');

JHtml::_('behavior.tooltip');
JHTML::_('script', 'system/multiselect.js', false, true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_ishop/assets/css/ishop.css');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$href = 'index.php?option=com_ishop&view=product';
?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
    <!--Поиск по наименованию-->
    <div id="filter-bar" class="btn-toolbar">
        <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_ISHOP_SEARCH_IN_NAME'); ?>" />
        </div>
        <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
        </div>
        
    <!--Поиск по артикулу-->
        <div class="filter-search btn-group pull-left">
                <label for="filter_search_artikul" class="element-invisible"><?php echo JText::_('COM_ISHOP_ARTIKUL_SEARCH');?></label>
                <input type="text" name="filter_search_artikul" id="filter_search_artikul" placeholder="<?php echo JText::_('COM_ISHOP_ARTIKUL_SEARCH'); ?>" value="<?=$this->escape($this->state->get('filter.search_artikul')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_ISHOP_ARTIKUL_SEARCH'); ?>" />
        </div>
        <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search_artikul').value='';this.form.submit();"><i class="icon-remove"></i></button>
        </div>
     
    <!--Выбор состояния-->
    <div class="btn-group pull-right">
            <label for="filter_published" class="element-invisible"><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></label>
            <select name="filter_published" id="filter_published" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true); ?>
            </select>
    </div>


    <!--Выбор категории-->
    <div class="btn-group pull-right">
            <label for="filter_category" class="element-invisible"><?php echo JText::_('JOPTION_SELECT_CATEGORIES');?></label>
            <select name="filter_category" id="filter_category" class="input-medium" onchange="this.form.submit()">
                    <option value="0"><?php echo JText::_('JOPTION_SELECT_CATEGORIES'); ?></option>
                    <?php echo JHtml::_('select.options', KhtmlHelper::categories($this->state->get('filter.site')), "value", "text", $this->state->get('filter.category'), true); ?>
            </select>
    </div>

    <!--Выбор сайта-->
    <div class="btn-group pull-right">
            <label for="filter_site_alias" class="element-invisible"><?php echo JText::_('COM_ISHOP_SELECT_SITE');?></label>
            <select name="filter_site_alias" id="filter_site_alias" class="input-medium" onchange="document.getElementById('filter_category').value='0';this.form.submit()">
                    <option value=""><?php echo JText::_('COM_ISHOP_SELECT_SITE');?></option>
                    <?php echo JHtml::_('select.options', KhtmlHelper::site_aliases(), "value", "text", $this->state->get('filter.site_alias'), true); ?>
            </select>
    </div>
    
    
    </div>
    <div class="clearfix"> </div>
    
    

    <table class="table table-striped" id="articleList">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_ISHOP_PRODUCTS_AVIALABLE'); ?>
                </th>

                <th class='left'>
                    <?php echo JText::_('COM_ISHOP_PRODUCTS_NAME'); ?>
                </th>
                <th class='left'>
                    <?php echo JHtml::_('grid.sort',  'COM_ISHOP_ARTIKUL', 'a.artikul', $listDirn, $listOrder); ?>
                </th>
                <th class='left'>
                    <?php echo JText::_('COM_ISHOP_PRODUCTS_CREATED_BY'); ?>
                </th>
                <th class='left'>
                    <?php echo JText::_('COM_ISHOP_PRODUCTS_CATEGORY_PATH'); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('JPUBLISHED'); ?>
                </th>
                    <?php if (isset($this->items[0]->state)) { ?>
                    <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
                    <th width="1%" class="nowrap">
                    <?php echo JText::_('JGRID_HEADING_ID'); ?>
                    </th>
                    <?php } ?>
            </tr>
        </thead>
        <tfoot>
<?php
if (isset($this->items[0])) {
    $colspan = count(get_object_vars($this->items[0]));
} else {
    $colspan = 10;
}
?>
            <tr>
                <td colspan="<?php echo $colspan ?>">
            <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
<?php
foreach ($this->items as $i => $item) :
    ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->available, $i, 'products.available_', 1, 'cb'); ?>
                    </td>

                    <td>
                        <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'products.', 1); ?>
                         <?php endif; ?>
                        <a href="<?php echo JRoute::_($href.'&id=' . (int) $item->id); ?>">
                            <?php $img = json_decode($item->dopinfo)->img_large; ?>
                            <?php $src = incase::thumb($img, $item->id, 50, 50); ?>
                            <img src="<?=$src?>" height="50" width="50" />
                            <?php echo $this->escape($item->name); ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $item->artikul; ?>
                    </td>
                    <td>
                        <?php echo $item->created_by; ?>
                    </td>
                    <td>
                        <?php echo $this->get_category_path($item->category_id); ?>
                    </td>

                        <?php if (isset($this->items[0]->state)) { ?>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->state, $i, 'products.', 1, 'cb'); ?>
                    </td>
                        <?php } ?>
                        <?php if (isset($this->items[0]->id)) { ?>
                    <td class="center">
                        <?php echo (int) $item->id; ?>
                    </td>
                        <?php } ?>
                </tr>
                <?php endforeach; ?>
        </tbody>
    </table>
    <div class="btn-group pull-right">
            <div class="btn-wrapper" id="a_rm_category">
                    <button  class="btn btn-small">
                        <span class="icon-unpublish"></span>
                        Удалить категорию
                    </button>
            </div>            
    </div>
    <div class="btn-group pull-right">
            <div class="btn-wrapper" id="a_add_category">
                    <button  class="btn btn-small">
                        <span class="icon-publish"></span>
                        Добавить категорию
                    </button>
            </div>            
    </div>
    <div class="btn-group pull-right">
            <select name="add_category" id="add_category" class="input-medium" onchange="Joomla.orderTable()">
                    <option value="0"><?php echo JText::_('JOPTION_SELECT_CATEGORIES'); ?></option>
                    <?php echo JHtml::_('select.options', KhtmlHelper::categories(), "value", "text", 0, true); ?>
            </select>
    </div>
    <div class="btn-group pull-right">
            <label for="add_category" ><?php echo JText::_('GROUP_ADD_REMOVE_CATEGORY');?></label>
    </div>

    <div>
        <input type="hidden" name="option" value="com_ishop" />
        <input type="hidden" name="view" value="products" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="category_id" value="0" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('.jgrid').click(function(){
            var attr_id = $(this).attr('id');
            
            /*Если это добавление или удаление категорий*/
            if(attr_id == 'a_add_category' ||  attr_id == 'a_rm_category')
            {
                return
            }
            
            var i = $(this).attr('rel');
            var task = $(this).attr('task');
            var span = $(this).children().first();
            var _this = $(this);
            $('input[type=checkbox]').attr('checked',false)
            $('#cb'+i).attr('checked',true)
            $("form [name=task]").val(task);
            var form = $('#adminForm');
            $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: $(form).serialize(),
                success: function(data){
                    $("form [name=task]").val('');
                    if(data)
                    {
                        $('#cb'+i).attr('checked',false)
                        if($(span).hasClass('publish'))
                        {
                            $(span).removeClass('publish');
                            $(span).addClass('unpublish');
                            _this.attr('task','products.set_available');
                        }
                        else
                        {
                            $(span).removeClass('unpublish');
                            $(span).addClass('publish');
                            _this.attr('task','products.unset_available');
                        }
                    }

                }
            });
        });
        
        /* Групповое добавление и удаление категорий */
            $('#a_add_category').click(function(){
                var category_id = validate_change_category();
                if(category_id == 0)
                {
                    return;
                }
                $('input[name="task"]').val('products.add_categories');
                $('#adminForm').submit();
            });
            $('#a_rm_category').click(function(){
                var category_id = validate_change_category();
                if(category_id == 0)
                {
                    return;
                }
                $('input[name="task"]').val('products.rm_categories');
                $('#adminForm').submit();
            });
    });

    function validate_change_category()
    {
        var $ = jQuery;
        var category_id = $('#add_category').val();
        if(category_id == '0')
        {
            alert('Вы не выбрали категорию');
            return 0;
        }
        
        if(!$('input[name="cid[]"]').is(':checked'))
        {
            alert('Вы не выбрали ни одного продукта');
            return 0;
        }

        $('input[name="category_id"]').val(category_id);
        return category_id;
    }
</script>    
    