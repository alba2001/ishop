<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="5">
		<?php echo JText::_('COM_ISHOP_HEADING_ID'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_ISHOP_USER_FIO'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_ISHOP_ORDER_STATUS'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_ISHOP_ORDER_OPLATA'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_ISHOP_ORDER_DOSTAVKA'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_ISHOP_ORDER_DATE'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_ISHOP_ORDER_SUM'); ?>
	</th>
</tr>
