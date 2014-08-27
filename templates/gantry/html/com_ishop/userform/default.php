<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
$form_action = JRoute::_('index.php?option=com_ishop');
// Загружаем тултипы.
// JHtml::_('behavior.tooltip');

JHtml::_('behavior.formvalidation');

$client_fiz = $this->form->getFieldset('client_fiz');
$uid = $client_fiz['jform_uid']->value;


?>
<script type="text/javascript">
	var Token = '<?=JSession::getFormToken();?>';
</script>
<div id="userfrom">
	<form action="<?php echo $form_action ?>" method="post" name="adminForm" id="member-registration" class="form-validate">
            
		<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
			<?php $fields = $this->form->getFieldset($fieldset->name);?>
			<?php if (count($fields)):?>
				<?php
				$fieldset_id = isset($fieldset->id)?'id="'.$fieldset->id.'"':'';
				$fieldset_style = isset($fieldset->style)?'style="'.$fieldset->style.'"':'';
				$fieldset_class = isset($fieldset->class)?'class="'.$fieldset->class.'"':'';
				?>
				<fieldset <?=$fieldset_id?> <?=$fieldset_style?> <?=$fieldset_class?>>
			<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.
			?>
			<h1><?php echo JText::_($fieldset->label);?></h1>
		<?php endif;?>
		<dl>
			<?php foreach($fields as $field):// Iterate through the fields in the set and display them.?>
				<?php if ($field->hidden):// If the field is hidden, just display the input.?>
					<?php echo $field->input;?>
				<?php else:?>
					<dt>
						<?php echo $field->label; ?>
					</dt>
					<dd><?php echo ($field->type!='Spacer') ? $field->input : "&#160;"; ?></dd>
				<?php endif;?>
			<?php endforeach;?>
		</dl>
	</fieldset>
<?php endif;?>
<?php endforeach;?>
<div id="error_msg" class="invalid"></div>
<?php $show_reg_checkbox = $uid?'style="display:none"':''?>
<div id="com_ishop_user_registration_div" <?=$show_reg_checkbox?>>

	<label for="com_ishop_user_registration">
		<input id="com_ishop_user_registration" type="checkbox" name="registration" value="1"/>
		<span><?=JTEXT::_('COM_ISHOP_USER_REGISTRATION')?></span>
	</label>

</div>
<input type="hidden" name="task" value="userform.submit" />
<?php echo JHtml::_('form.token'); ?>
<input class="button" id="member-registration_submit" type="submit" name="jform_submit" value="<?php echo JText::_('COM_ISHOP_FORM_SUBMIT');?>">
</form>
</div>