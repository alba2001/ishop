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

?>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" title="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th class='left'><?php echo JText::_('COM_ISHOP_FORM_LBL_PRODUCTTYPE_ID'); ?></th>
				<th class='left'><?php echo JText::_('COM_ISHOP_FORM_LBL_PRODUCTTYPE_NAME'); ?></th>
				<th class='left'><?php echo JText::_('COM_ISHOP_FORM_LBL_PRODUCTTYPE_ALIAS'); ?></th>
				<th class='left'><?php echo JText::_('COM_ISHOP_PRODUCT_CENA_MAG'); ?></th>
				<th class='left'><?php echo JText::_('COM_ISHOP_PRODUCT_CENA_TUT'); ?></th>
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
					<?php echo $item->id; ?>
				</td>
				<td>
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'producttypes.', $canCheckin); ?>
				<?php endif; ?>
					<a href="<?php echo JRoute::_('index.php?option=com_ishop&task=producttype.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->name); ?></a>
				</td>
				<td>
					<?php echo $item->alias; ?>
				</td>
				<td>
					<?php echo $item->cena_mag; ?>
				</td>
				<td>
					<?php echo $item->cena_tut; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input id="task" type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_ishop" />
		<input type="hidden" name="view" value="producttypes" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
//        console.log(task);
//        return false;
        Joomla.submitform(task);
    };
</script>    
        