<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
                    <a href="<?=JURI::base().'index.php?option=com_ishop&view=order&layout=edit&id='.$item->id?>">
                        <?php echo $this->get_user_fio($item->userid);?>
                    </a>
		</td>
		<td>
			<?php echo $this->get_order_status($item->order_status_id);?>
		</td>
		<td>
			<?php echo $this->get_order_oplata($item->oplata_id);?>
		</td>
		<td>
			<?php echo $this->get_order_dostavka($item->dostavka_id);?>
		</td>
		<td>
			<?php echo $this->get_order_dt($item->order_dt);?>
		</td>
		<td>
			<?php echo $this->get_sum($item->sum);?>
		</td>
	</tr>
<?php endforeach; ?>
