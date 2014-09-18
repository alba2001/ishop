<?php
/**
* @version     1.0.0
* @package     com_jugraauto
* @copyright   Copyright (C) 2013. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
* @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
*/

// no direct access
defined('_JEXEC') or die;
jimport('incase.init');
//    var_dump($this->caddy);
?>

<h1><?=JTEXT::_('COM_ISHOP_ORDER')?> № <?=$this->item->id?></h1>

<?/*<h4><?=JText::_('COM_ISHOP_CAN_PAY_MSG')?></h4>*/?>
<div class="cart">
<table class="table_size_small table_cols_2">
    <tr>
        <td>
            <div class="wr">
                <b><?=JTEXT::_('COM_ISHOP_FIO').': '?></b>
            </div>
        </td>
        <td>
            <div class="wr">
                <?=$this->user->fam.' '.$this->user->im.' '.$this->user->ot?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="wr">
                <b><?=JTEXT::_('COM_ISHOP_ADDRESS').': '?></b>
            </div>
        </td>
        <td>
            <div class="wr">
                <?=$this->user->address?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="wr">
                <b><?=JTEXT::_('COM_ISHOP_PHONE').': '?></b>
            </div>
        </td>
        <td>
            <div class="wr">
                <?=$this->user->phone?>
            </div>
        </td>
    </tr>
        <tr>
            <td>
                <div class="wr">
                    <b><?=JTEXT::_('COM_ISHOP_EMAIL').': '?></b>
                </div>
            </td>
            <td>
                <div class="wr">
                    <?=$this->user->email?>
                </div>
            </td>
        </tr>
    <tr>
        <td>
            <div class="wr">
                <b><?=JTEXT::_('COM_ISHOP_ORDER_OPLATA').': '?></b>
            </div>
        </td>
        <td>
            <div class="wr">
                <?=$this->sposob_oplaty?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="wr">
                <b><?=JTEXT::_('COM_ISHOP_ORDER_DOSTAVKA').': '?></b>
            </div>
        </td>
        <td>
            <div class="wr">
                <?=$this->sposob_dostavki?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="wr">
                <b><?=JTEXT::_('COM_ISHOP_ITOGO').': '?></b>
            </div>
        </td>
        <td>
            <div class="wr">
                <?php if((int)$this->total_sum):?>
                    <?=$this->total_sum?>
                    <span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
                <?php else:?>
                    <?=' '.JTEXT::_('COM_ISHOP_MANAGER_CENA')?>
                <?php endif;?>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="wr">
                <b><?=JTEXT::_('COM_ISHOP_CAN_PAY').': '?></b>
            </div>
        </td>
        <td>
            <div class="wr">
                <?=ComponentHelper::getCheckoutSum($this->total_sum)?>
                <span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
            </div>
        </td>
    </tr>
</table>

                
                    <table>
                        <thead>
                            <tr>
                                <th class="col1 first">
                                    <span class="wr">Фото</span>
                                </th>
                                <th class="col2">
                                    <span class="wr">Наименование</span>
                                </th>
                                <th class="col3">
                                    <span class="wr">Количество</span>
                                </th>
                                <th class="col4">
                                    <span class="wr">Цена</span>
                                </th>
                                <th class="col5 last">
                                    <span class="wr">Сумма</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($this->products as $item):?>
                                <?php $id = $item['id']?>
                                <? $href = JRoute::_('index.php?option=com_ishop&alias=' . $id ); ?>
                                <tr>
                                    <td class="first">
                                        <div class="image wr">
                                            <a href="<?=$href?>">
                                                <img src="<?=incase::thumb($item['img_src'], $item['id'],100,100)?>" alt="<?=$item['artikul']?>">
                                            </a>
                                        </div>
                                    </td>
                                    <td class="info">

                                        <div class="wr">
                                            <div class="item_title">
                                                <a href="<?=$href;?>">
                                                    <?=$item['name']?>
                                                </a>
                                            </div>
                                            
                                            <div class="article">
                                                Артикул: <?=$item['artikul']?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="">
                                        <div class="wr"><?=$item['count']?></div>
                                    </td>
                                    <td class="price">
                                        <div class="wr"><?php if((int)$item['price']):?>
                                                <span class="nowrap">
                                                    <span id="caddy_item_price_<?=$id?>">
                                                        <?=$item['price']?>
                                                    </span>
                                                    <span class="ruble">
                                                        <?=JTEXT::_('COM_ISHOP_RUB')?>
                                                    </span>
                                                </span>
                                            <?php else:?>
                                                <?=' '.JTEXT::_('COM_ISHOP_MANAGER_CENA')?>
                                            <?php endif;?></div>
                                    </td>
                                    <td class="caddy_item_sum last">
                                        <div class="wr">
                                            <span id="caddy_item_sum_<?=$id?>"><?=(int)$item['sum']?></span>
                                            <span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
                                        </div>
                                    </td>
                                </tr>
                                <?php if(0 && $item['purchases']):?>
                                    <tr>
                                        <td colspan="5">
                                            <h3>С этим товаром покупают:</h3>
                                            <?php foreach($item['purchases'] as $purchase):?>
                                                <? $href = JRoute::_('index.php?option=com_ishop&alias=' . $purchase['id'] ); ?>
                                                <div class="b-caddy_buy_with_this">
                                                    <div class="b-caddy_buy_with_this__img">
                                                        <a href="<?=$href?>">
                                                            <img src="<?=incase::thumb($purchase['img_src'],$purchase['id'],97,97)?>" alt="<?=$purchase['artikul']?>">
                                                        </a>
                                                    </div>
                                                    <a href="<?=$href?>">
                                                        <?=$purchase['name']?>
                                                    </a>
                                                </div>
                                            <?php endforeach?>
                                        </td>
                                    </tr>
                                <?php endif?>

                            <?php endforeach;?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <?php if($this->item->order_status_id == '1'): ?>
                                    <th colspan="3" class="left first">
                                        <a href="<?php echo JUri::base().'spisok-zakazov'?>" class="button prev"><?=JTEXT::_('COM_ISHOP_ORDERS_LIST')?></a>
                                    </th>
                                    <th colspan="2" class="right last">
                                        <a href="<?php echo $this->robokassa_href?>" class="button next"><?=JTEXT::_('COM_ISHOP_ORDER_PAY')?></a>
                                    </th>
                                <?php else:?>
                                    <th colspan="5" class="left first last">
                                        <a href="<?php echo JUri::base().'spisok-zakazov'?>" class="button prev"><?=JTEXT::_('COM_ISHOP_ORDERS_LIST')?></a>
                                    </th>
                                <?php endif; ?>
                            </tr>
                        </tfoot>

                    </table>
                </div>
