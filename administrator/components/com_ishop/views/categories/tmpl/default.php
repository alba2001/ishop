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

JHtml::_('behavior.tooltip');
JHTML::_('script', 'system/multiselect.js', false, true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_ishop/assets/css/ishop.css');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');


$ajax_url = JRoute::_(JURI::base().'index.php?option=com_ishop&');
$succes_msg = ''.
                '<dl id="system-message">'.
                '<dt class="message">Сообщение</dt>'.
                '<dd class="message message">'.
                        '<ul>'.
                                '<li>Успешное завершение</li>'.
                        '</ul>'.
                '</dd>'.
                '</dl>'.
                '';
$error_msg = ''.
                '<dl id="system-message">'.
                '<dt class="error">Ошибка</dt>'.
                '<dd class="message message">'.
                        '<ul>'.
                                '<li id="parse_error_msg">Были ошибки</li>'.
                        '</ul>'.
                '</dd>'.
                '</dl>'.
                '';

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
        
<div class="clr"> </div>        
    <!--Фильтр по состоянию-->
    <div class="btn-group pull-right">
            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true); ?>
            </select>
    <!--Фильтр по родительской категории-->
    <div class="btn-group pull-right">
            <select id="filter_level" name="filter_parent" class="inputbox" onchange="this.form.submit()">
                <option value="0"><?php echo JText::_('CATEGORY_PARENT_SELECT'); ?></option>
                <?php echo JHtml::_('select.options', KhtmlHelper::categories(), "value", "text", $this->state->get('filter.parent'), true); ?>
            </select>
    </div>
    
    <!--Фильтр по максимальному числу уровней-->
    <div class="btn-group pull-right">
            <select id="filter_level" name="filter_level" class="inputbox" onchange="this.form.submit()">
                <option value="0"><?php echo JText::_('MAX_LEVELS_SELECT'); ?></option>
                <?php echo JHtml::_('select.options', KhtmlHelper::levels(), "value", "text", $this->state->get('filter.level'), true); ?>
            </select>
    </div>
    
    <!--Фильтр по сайту источнику-->
    <div class="btn-group pull-right">
            <select name="filter_site_alias" id="filter_site_alias" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', KhtmlHelper::site_aliases(), "value", "text", $this->state->get('filter.site_alias'), true); ?>
            </select>
    </div>
    </div>
    
    
    </div>
    
    <div class="clr"> </div>

    <table class="table table-striped" id="articleList">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
                </th>

                <th class='left'>
                    <?php echo JText::_('COM_ISHOP_CATEGORIES_NAME'); ?>
                </th>
<!--                <th class='left'>
                    <?php // echo JText::_('COM_ISHOP_PRODUCTTYPE'); ?>
                </th>-->
                <th class='left'>
                    <?php echo JText::_('COM_ISHOP_CATEGORIES_CREATED_BY'); ?>
                </th>
                <th class='left'>
                    <?php echo JText::_('COM_ISHOP_CATEGORIES_SITE'); ?>
                </th>
                    <?php if (isset($this->items[0]->state)) { ?>
                <th width="5%">
                    <?php echo JText::_('JPUBLISHED'); ?>
                </th>
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
    if($item->level>0):
    ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>

                    <td>
                        <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', 1); ?>
                         <?php endif; ?>
                        <a href="<?php echo JRoute::_('index.php?option=com_ishop&task=category.edit&id=' . (int) $item->id); ?>">
                        <?php echo str_repeat('|-----', ($item->level-1)).$this->escape($item->name); ?></a>
                    </td>
<!--                    <td>
                        <?php // echo $this->model->get_producttype_id($item->producttype_id); ?>
                    </td>-->
                    <td>
                        <?php echo $item->created_by; ?>
                    </td>
                    <td>
                        <?php echo $this->get_site_alias_name($item->site_alias); ?>
                    </td>


                        <?php if (isset($this->items[0]->state)) { ?>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->state, $i, 'categories.', 1, 'cb'); ?>
                    </td>
                        <?php } ?>
                        <?php if (isset($this->items[0]->id)) { ?>
                    <td class="center">
                        <?php echo (int) $item->id; ?>
                    </td>
                        <?php } ?>
                </tr>
    <?php endif; ?>
<?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" name="option" value="com_ishop" />
        <input type="hidden" name="view" value="categories" />
        <input id="task" type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<?php echo JHtml::_('form.token'); ?>
    </div>
</form>


<div id="show_process" style="height: 600px; overflow: scroll; display: none"></div>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        var action = task.split('.');
        if(action[1] == 'parse')
        {
//            return false;
        }
        if(action[1] == 'parse_continue')
        {
            return false;
        }
        Joomla.submitform(task);
    };
    
    jQuery(document).ready(function($){
        $('#toolbar-parse').click(function(e){
//            $('#task').val('categories.parse');
//            parse(1);
        });
    });

    jQuery(document).ready(function($){
        $('#toolbar-parse_continue').click(function(e){
            $('#task').val('categories.parse');
            var form = $('#adminForm');
            var div_txt = $('#show_process');
            $(form).hide('slow');
            $(div_txt).show('slow');
            parse(0);
        });
    });

    function parse(start)
    {
        var $ = jQuery;
        var form = $('#adminForm');
        var div_txt = $('#show_process');
        var url = '<?php echo $ajax_url ?>'+$(form).serialize(); 
        if(start)
        {
            $(form).hide('slow');
            $(div_txt).show('slow');
            $(div_txt).text('<?=JText::_('COM_ISHOP_OPEN_MAIN_PAGE')?>');
            url += '&start=1';
        }
        console.log(url);
        $.ajax({
            type: 'GET',
            url: url,
            success: function(html){
                $('#html_show').html(html);
                var data = $.parseJSON(html);
                if(data[0] == 1)
                {
                    $('#system-message-container').html('<?=$succes_msg?>');
                    $(form).show('slow');
                    $('#html_show').hide();
                    $(div_txt).hide('slow');
                }
                else if(data[0] == 2)
                {
                    $(div_txt).prepend('<br/>'+data[1]);
                    parse(0);
                }
                else if(data[0] == 3)
                {
                    $.ajax({
                    type: 'GET',
                    url: data[1],
                    success: function(html){
                        
                        $('#html_show').html(html);
                    }});

//                    parse(0);
                }
                else
                {
                    $('#system-message-container').html('<?=$error_msg?>');
                    $('#parse_error_msg').text(data[1]);
                    $(form).show('slow');
                    $(div_txt).hide('slow');
                    
                }
                console.log(data);
            }
        });
    };
</script>    
