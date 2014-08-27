<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="user-form" class="form-validate">
 
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_ISHOP_USER_DETAILS' ); ?></legend>
			<ul class="adminformlist">
                            <?php foreach($this->form->getFieldset('user_fields') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
                            <?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
 
 
	<div>
		<input type="hidden" name="option" value="com_ishop" />
		<input type="hidden" name="layout" value="edit" />
		<input type="hidden" name="id" value="<?=(int) $this->item->id?>" />
		<input type="hidden" name="task" value="user.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $.mask.definitions['#']='[9]';  
        $("#jform_phone").mask("+7(#99) 999-99-99");
    });
</script>    
    