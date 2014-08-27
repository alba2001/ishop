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

<table class="table_size_small table_cols_2">
    <tr>
        <td><b><?=JTEXT::_('COM_ISHOP_FIO').': '?></b></td>
        <td><?=$this->user->fam.' '.$this->user->im.' '.$this->user->ot?></td>
    </tr>
    <tr>
        <td><b><?=JTEXT::_('COM_ISHOP_ADDRESS').': '?></b></td>
        <td><?=$this->user->address?></td>
    </tr>
    <tr>
        <td><b><?=JTEXT::_('COM_ISHOP_PHONE').': '?></b></th>
            <td><?=$this->user->phone?></td>
        </tr>
        <tr>
            <td><b><?=JTEXT::_('COM_ISHOP_EMAIL').': '?></b></td>
            <td><?=$this->user->email?></td>
        </tr>
        <tr>
            <td><b><?=JTEXT::_('COM_ISHOP_ORDER_OPLATA').': '?></b></th>
                <td><?=$this->sposob_oplaty?></td>
            </tr>
            <tr>
                <td><b><?=JTEXT::_('COM_ISHOP_ORDER_DOSTAVKA').': '?></b></th>
                    <td><?=$this->sposob_dostavki?></td>
                </tr>
                <tr>
                    <td><b><?=JTEXT::_('COM_ISHOP_ITOGO').': '?></b></th>
                        <td>
                            <?php if((int)$this->total_sum):?>
                                <?=$this->total_sum?>
                                <span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
                            <?php else:?>
                                <?=' '.JTEXT::_('COM_ISHOP_MANAGER_CENA')?>
                            <?php endif;?>
                        </td>
                    </tr>
                    <tr>
                        <td><b><?=JTEXT::_('COM_ISHOP_CAN_PAY').': '?></b></th>
                            <td>
                                <?=ComponentHelper::getCheckoutSum($this->total_sum)?>
                                <span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
                            </td>
                        </tr>
                    </table>

                    <table class="cart">
                        <thead>
                            <tr>
                                <th class="col1 first">
                                    <span>Фото</span>
                                </th>
                                <th class="col2">
                                    <span>Наименование</span>
                                </th>
                                <th class="col3">
                                    <span>Количество</span>
                                </th>
                                <th class="col4">
                                    <span>Цена</span>
                                </th>
                                <th class="col5 last">
                                    <span>Сумма</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($this->products as $item):?>
                                <?php $id = $item['id']?>
                                <? $href = JRoute::_('index.php?option=com_ishop&alias=' . $id ); ?>
                                <tr>
                                    <td class="first">
                                        <div class="image">
                                            <a href="<?=$href?>">
                                                <img src="<?=incase::thumb($item['img_src'], $item['id'],97,97)?>" alt="<?=$item['artikul']?>">
                                            </a>
                                        </div>
                                    </td>
                                    <td class="info">

                                        <div class="item_title">
                                            <a href="<?=$href;?>">
                                                <?=$item['name']?>
                                            </a>
                                        </div>

                                        <div class="article">
                                            Артикул: <?=$item['artikul']?>
                                        </div>
                                    </td>
                                    <td class="">
                                        <?=$item['count']?>
                                    </td>
                                    <td class="price">
                                        <?php if((int)$item['price']):?>
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
                                        <?php endif;?>
                                    </td>
                                    <td class="caddy_item_sum last">
                                        <span id="caddy_item_sum_<?=$id?>"><?=(int)$item['sum']?></span>
                                        <span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
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

