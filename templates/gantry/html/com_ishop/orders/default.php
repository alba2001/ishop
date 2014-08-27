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
$href = JUri::base().'index.php?option=com_ishop&view=order&id=';
$src = JURI::base().'components/com_ishop/assets/img/info_16.png';

?>
<table class="cart">
    <thead>
        <tr>
            <th><?=JText::_('COM_ISHOP_ORDER_ID')?></th>
            <th><?=JText::_('COM_ISHOP_ORDER_DT')?></th>
            <th><?=JText::_('COM_ISHOP_ORDER_SUM')?></th>
            <th><?=JText::_('COM_ISHOP_ORDER_STATUS')?></th>
            <th class="center"><?=JText::_('COM_ISHOP_ORDER_INFO')?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $item):?>
        <tr>
            <td><?= $item->id?></td>
            <td><?= $this->model->get_order_dt($item->order_dt)?></td>
            <td><?= (int)$item->sum?$item->sum:JTEXT::_('COM_ISHOP_MANAGER_CENA')?></td>
            <td><?= $this->model->get_order_status($item->order_status_id)?></td>
            <td class="center">
                <a href="<?=$href.$item->id?>" title="<?=JText::_('COM_ISHOP_ORDER_INFO')?>">
                    <img src="<?=$src?>" alt="<?=JText::_('COM_ISHOP_ORDER_INFO')?>"/>
                </a>
            </td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
