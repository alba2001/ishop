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

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_ishop/assets/css/ishop.css');

$user	= JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_ishop');
$saveOrder	= $listOrder == 'a.ordering';

$ordering	= FALSE;
$canEdit	= TRUE;
$canCheckin	= TRUE;
$canChange	= TRUE;
$ajax_url = JRoute::_(JURI::base().'index.php?option=com_ishop&');
$succes_msg = ''.
            '<button type="button" class="close" data-dismiss="alert">X</button>'.
            '<div class="alert alert-success">'.
                    '<h4 class="alert-heading">Сообщение</h4>'.
                            '<p>Успешное завершение.</p>'.
            '</div>';
$error_msg = ''.
            '<button type="button" class="close" data-dismiss="alert">X</button>'.
            '<div class="alert alert-error">'.
                    '<h4 class="alert-heading">Сообщение</h4>'.
                            '<p>Были ошибки.</p>'.
            '</div>';
?>
<div id="html_show"></div>
<form action="<?php echo JRoute::_('index.php?option=com_ishop&view=zavods'); ?>" method="post" name="adminForm" id="adminForm">
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
        
    <!--Выбор состояния-->
    <div class="btn-group pull-right">
            <label for="filter_published" class="element-invisible"><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></label>
            <select name="filter_published" id="filter_published" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true); ?>
            </select>
    </div>
    </div>
    <div class="clearfix"> </div>
    

	<table  class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>

				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_ISHOP_ZAVODS_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_ISHOP_ZAVODS_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_ISHOP_FORM_LBL_ZAVOD_URL'); ?>
				</th>


                <?php if (isset($this->items[0]->state)) { ?>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'zavods.saveorder'); ?>
					<?php endif; ?>
				</th>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <?php } ?>
			</tr>
		</thead>
		<tfoot>
			<?php 
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
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
		<?php foreach ($this->items as $i => $item) :
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>

				<td>
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'zavods.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_ishop&task=zavod.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->name); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->name); ?>
				<?php endif; ?>
				</td>
				<td>
					<?php echo $item->created_by; ?>
				</td>
				<td>
					<?php echo $item->base_url; ?>
				</td>


                <?php if (isset($this->items[0]->state)) { ?>
				    <td class="center">
					    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'zavod.', $canChange, 'cb'); ?>
				    </td>
                <?php } ?>
                <?php if (isset($this->items[0]->ordering)) { ?>
				    <td class="order">
					    <?php if ($canChange) : ?>
						    <?php if ($saveOrder) :?>
							    <?php if ($listDirn == 'asc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'zavods.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'zavods.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php elseif ($listDirn == 'desc') : ?>
								    <span><?php echo $this->pagination->orderUpIcon($i, true, 'zavods.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'zavods.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							    <?php endif; ?>
						    <?php endif; ?>
						    <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						    <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					    <?php else : ?>
						    <?php echo $item->ordering; ?>
					    <?php endif; ?>
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

	<div>
		<input id="task" type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<a href="#myModal" role="button" class="btn" data-toggle="modal">Launch demo modal</a>
 
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Парсинг</h3>
  </div>
  <div class="modal-body">
    
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
  </div>
</div>

<div id="show_process" style="height: 600px; overflow: scroll; display: none"></div>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        var action = task.split('.');
        if(action[1] == 'parse')
        {
            return false;
        }
        if(action[1] == 'parse_continue')
        {
            return false;
        }
        Joomla.submitform(task);
    };
    jQuery(document).ready(function($){
        $('#toolbar-parse').click(function(e){
            $('#task').val('zavod.parse');
            parse(1);
        });
        
        $('#toolbar-parse_continue').click(function(e){
            $('#task').val('zavod.parse');
            var form = $('#adminForm');
            var div_txt = $('#show_process');
            $(form).hide('slow');
            $(div_txt).show('slow');
            parse(0);
        });
        
        <?php if($this->parse_continue):?>
            $('input[value="7"]').click();
            $('#toolbar-parse_continue').click();
        <?php endif?>
    });

    function parse(start)
    {
        var $ = jQuery;
        var form = $('#adminForm');
        var $div_modal = $('#myModal');
        var $div_txt = $('div.modal-body');
        var url = '<?php echo $ajax_url ?>'+$(form).serialize(); 
        if(start)
        {
            $div_modal.modal('show')
            $div_txt.text('<?=JText::_('COM_ISHOP_OPEN_MAIN_PAGE')?>');
            url += '&start=1';
        }
        console.log(url);
        $.ajax({
            type: 'GET',
            url: url,
            success: function(html){
                var data;
                try {
                        data = $.parseJSON(html);
                    }
                    catch (e) {
                        console.log(e)
                        $div_txt.html(html);
                    }
                if(data[0] == 1)
                {
                    $('#system-message-container').html('<?=$succes_msg?>');
                    $div_txt.prepend(data[1]+'<br/>');
                }
                else if(data[0] == 2)
                {
                    $div_txt.prepend(data[1]+'<br/>');
                    parse(0);
                }
                else if(data[0] == 3)
                {
                    $.ajax({
                    type: 'GET',
                    url: data[1],
                    success: function(html){
                        
                        $div_txt.html(html);
                    }});

                }
                else
                {
                    $('#system-message-container').removeClass('success').addClass('error').html('<?=$error_msg?>');
                    $('#parse_error_msg').text(data[1]);
                    $div_modal.modal('hide');
                    
                }
                console.log(data);
            }
        });
    };
</script>    
        