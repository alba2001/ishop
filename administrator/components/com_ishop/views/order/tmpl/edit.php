<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="order-form" class="form-validate">
 
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_ISHOP_ORDER_DETAILS' ); ?></legend>
			<dl class="adminformlist">
                            <?php foreach($this->form->getFieldset('order_fields') as $field): ?>
				<dt><?php echo $field->label;?></dt>
				<dd><?php echo $field->input;?></dd>
                            <?php endforeach; ?>
			</dl>
		</fieldset>
	</div>
 
 
	<div>
		<input type="hidden" name="option" value="com_ishop" />
		<input type="hidden" name="layout" value="edit" />
		<input type="hidden" name="id" value="<?=(int) $this->item->id?>" />
		<input id="new_status" type="hidden" name="new_status" value="" />
		<input type="hidden" name="task" value="order.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div><?php echo $this->loadTemplate('caddy');?></div>
<div id="ch_status">
    <?php echo str_replace('\n', '<br/>', $this->item->ch_status);?>
    <br/>
    <span id="ch_status_span"></span>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#jform_order_status_id').change(function(){
            var text = $('#jform_order_status_id option:selected').text();
            $('#new_status').val(text);
        });
    });
</script>    
    