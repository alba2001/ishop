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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
        
		if (task == 'product.cancel' || document.formvalidator.isValid(document.id('product-form'))) {
			Joomla.submitform(task, document.getElementById('product-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
        jQuery(document).ready(function($){
            $('#a_add_category').click(function(){
                var category_id = $('#add_category').val();
                if(category_id == '0')
                {
                    alert('Вы не выбрали категорию');
                    return;
                }
                
                var data = {
                    category_id:category_id,
                    product_id:'<?=$this->item->id?>',
                    option:'com_ishop',
                    task:'product.add_category',
                    '<?=JSession::getFormToken()?>':'1'
                };
                $.ajax({
                    type: 'POST',
                    url: '<?=  JURI::base().'index.php'?>',
                    data:data,
                    success: function(html){
                        var data = $.parseJSON(html);
                        if(data[0] == '1')
                        {
                            var $dl = $('#dl_category_list');
                            var dt = '<dt class="category_'+category_id+'">'+data[2]+'</dt>';
                            var dd = '<dd class="category_'+category_id+'">';
                            dd += '<a class="jgrid" href="javascript:void(0);"';
                            dd += 'onclick="product_remove_category(\''+data[1]+'\')"';
                            dd += 'title="Удалить категорию">';
                            dd += '<span class="state trash"></span>';
                            dd += '</a>';
                            dd += '</dd>';
                            $dl.append(dt+dd);
                        }
                        else
                        {
                            alert(data[1]);
                        }
                    }
                });
            });
        });

        function product_remove_category(category_id)
        {
            var $ = jQuery;
                var data = {
                    category_id:category_id,
                    product_id:'<?=$this->item->id?>',
                    option:'com_ishop',
                    task:'product.remove_category',
                    '<?=JSession::getFormToken()?>':'1'
                };
                $.ajax({
                    type: 'POST',
                    url: '<?=  JURI::base().'index.php'?>',
                    data:data,
                    success: function(html){
                        var data = $.parseJSON(html);
                        alert(data[1]);
                        if(data[0] == '1')
                        {
                            $('.category_'+category_id).hide('slow').remove();
                        }
                    }
                });
            
        }
</script>
<style type="text/css">
    fieldset.adminform dt{
        width: 80%;
        padding: 0 5px 0 0;
        float: left;
        clear: left;
        display: block;
        margin: 5px 0;        
    }
    fieldset.adminform dd{
        float: left;
        width: auto;
        margin: 5px 5px 5px 0;
        font-weight: bold;
    }
    div.with-40{width: 40%}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_ishop&layout=edit&id='.(int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="product-form" class="form-validate">

    <!--Добавление и удаление категорий-->
	<div class="with-40 fltlft left">
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_ISHOP_PRODUCT_CATEGORIES'); ?></legend>
                <dl id="dl_category_list">
                        <dt>
                            <select id="add_category" name="add_category" class="inputbox">
                                <option value="0"><?php echo JText::_('JOPTION_SELECT_CATEGORIES'); ?></option>
<?php echo JHtml::_('select.options', KhtmlHelper::categories(), "value", "text", 0, true); ?>                                

                            </select>
                        </dt>
                        <dd>
<a class="jgrid" id="a_add_category" href="javascript:void(0);" title="Добавить категорию"><span class="state icon-16-newcategory"></span></a>
                        </dd>
                    <?php foreach($this->item->categories as $category) :?>
                        <dt class="category_<?=$category->id?>">
                            <?=$category->name?>
                        </dt>
                        <dd class="category_<?=$category->id?>">
<a class="jgrid" href="javascript:void(0);" onclick="product_remove_category('<?=$category->id?>')" title="Удалить категорию">
        <span class="state trash"></span>
</a>
                        </dd>
                    <?php endforeach?>
                </dl>
            </fieldset>
        </div>
    <div class="form-horizontal">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_ISHOP_LEGEND_PRODUCT'); ?></legend>
            <div class="row-fluid">
                <div class="span6">
                    <?php foreach ($this->form->getFieldset() as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $field->label; ?></div>
                            <div class="controls"><?php echo $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </fieldset>
    </div>
                
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>