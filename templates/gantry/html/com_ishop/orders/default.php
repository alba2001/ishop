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
<div class="cart">
    <table>
        <thead>
            <tr>
                <th><span class="wr"><?=JText::_('COM_ISHOP_ORDER_ID')?></span></th>
                <th><span class="wr"><?=JText::_('COM_ISHOP_ORDER_DT')?></span></th>
                <th><span class="wr"><?=JText::_('COM_ISHOP_ORDER_SUM')?></span></th>
                <th><span class="wr"><?=JText::_('COM_ISHOP_ORDER_STATUS')?></span></th>
                <th class="center"><span class="wr"><?=JText::_('COM_ISHOP_ORDER_INFO')?></span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $item):?>
            <tr>
                <td><span class="wr"><?= $item->id?></span></td>
                <td><span class="wr"><?= $this->model->get_order_dt($item->order_dt)?></span></td>
                <td><span class="wr"><?= (int)$item->sum?$item->sum:JTEXT::_('COM_ISHOP_MANAGER_CENA')?></span></td>
                <td><span class="wr"><?= $this->model->get_order_status($item->order_status_id)?></span></td>
                <td class="center">
                    <span class="wr"><a href="<?=$href.$item->id?>" title="<?=JText::_('COM_ISHOP_ORDER_INFO')?>">
                            <img src="<?=$src?>" alt="<?=JText::_('COM_ISHOP_ORDER_INFO')?>"/>
                        </a></span>
                </td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
