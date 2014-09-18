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
//    var_dump($this->caddy);

?>
<?php if( $this->items ) : ?>
	<style type="text/css">
		dl.dl_user_detail dt{
			float: left;
			font-weight: bold;
			width: 200px;
		}
	</style>
	<div class="div_user_detail expand-source">
		<p>Личные данные</p>
		<form action="<?php echo JURI::base().'lichnye-dannye'; ?>" method="post" name="user_detail_show" id="user_detail_show">
			<dl class="dl_user_detail">
				<dt><?=JTEXT::_('COM_ISHOP_FIO').': '?></dt>
				<dd><?=$this->user->fam.' '.$this->user->im.' '.$this->user->ot?></dd>
				<dt><?=JTEXT::_('COM_ISHOP_ADDRESS').': '?></dt>
				<dd><?=$this->user->address?></dd>
				<dt><?=JTEXT::_('COM_ISHOP_PHONE').': '?></dt>
				<dd><?=$this->user->phone?></dd>
				<dt><?=JTEXT::_('COM_ISHOP_EMAIL').': '?></dt>
				<dd><?=$this->user->email?></dd>
			</dl>
			<input class="button" type="submit" value="<?=JTEXT::_('COM_ISHOP_EDIT_USERDATA')?>" />
		</form>
	</div>
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="caddy_show" id="caddy_show" class="cart">
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
					<th class="col5">
						<span class="wr">Сумма</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->items as $item):?>
					<?php $id = $item['id']?>
					<tr>
						<td>
							<div class="image wr">
								<a href="<?= $item['path'].'/'.$item['id'];?>">
                                                                        <img src="<?=incase::thumb($item['src'],$item['id'], 100, 100)?>" alt="<?=$item['artikul']?>">
								</a>
							</div>
						</td>
						<td class="info">
							<div class="wr">
							<?php if(isset($this->item->name) AND $this->item->name):?>
							<?php endif;?>
							<div class="item_title">
								<a href="<?= $item['path'].'/'.$item['id'];?>">
									<?=$item['name']?>
								</a>
							</div>


							<?php if(isset($this->item->razmer) AND $this->item->razmer):?>
								<div class="size">
									Размер: <?=$item['razmer']?>
								</div>
							<?php endif;?>

							<div class="article">
								Артикул: <?=$item['artikul']?>
							</div>
						</div>
						</td>
						<td class="price">
                          <div class="wr" style="display: inline-block;
vertical-align: middle;
line-height: 104px;
padding: 0;"><?=$item['count']?></div>
						</td>
						<td class="price">
							<div class="wr"><?php if((int)$item['price']):?>
									<span id="caddy_item_price_<?=$id?>"><?=$item['price']?></span>
									<span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
								<?php else:?>
									<?=' '.JTEXT::_('COM_ISHOP_MANAGER_CENA')?>
								<?php endif;?></div>
						</td>
						<td class="caddy_item_sum">
							<div class="wr">
								<span id="caddy_item_sum_<?=$id?>"><?=(int)$item['sum']?></span>
								<span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
							</div>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<!--Кнопки-->       
				<tr>
					<th colspan="2" class="left">
						<a href="<?php echo JUri::base().'sposob-dostavki'; ?>" class="button prev" />Вернуться к способу доставки</a>
					</th>
					<th colspan="2" class="right">
						<span class="nowrap">
							<span class="gray-sum">
								<?=JTEXT::_('COM_ISHOP_ITOGO')?>:
							</span>
							<span class="black-sum" id="caddy_total_sum">
								<?=$this->caddy_data['sum']?>
							</span>
							<span class="ruble">
								<?=JTEXT::_('COM_ISHOP_RUB')?>
							</span>
						</span>
					</th>
					<th colspan="2" class="right">
						<input id="to_step_end" class="button next" type="submit" value="Подтвердить" />
					</th>
				</tr>
			</tfoot>

		</table>
            <div style="position: relative;overflow: hidden;margin-top: 20px;">
                <a id="user_sogl_a" href="javascript:void(0)" onclick="jQuery('#user_sogl_text').toggle('slow')" style="float: left;display: block;">
                            <?=JTEXT::_('COM_ISHOP_USER_SOGL_TEXT')?>
                </a>
                <div id="user_sogl_text" style="display: none;margin-top: 35px;">
                    <?=$this->user_sogl?>
                </div>
              <div class="chek" style="float: right;padding-bottom:20px;">
                	<input type="checkbox" name="user_sogl" id="user_sogl" value="1"/>
                	<label for="user_sogl" style="display: inline-block;margin-bottom: 5px;vertical-align: middle;"><?=JTEXT::_('COM_ISHOP_USER_SOGL_LABEL')?></label>
                </div>
            </div>
		<input type="hidden" name="option" value="com_ishop" />
		<input type="hidden" name="view" value="caddy" />
		<input id="caddy_task" type="hidden" name="task" value="caddy.order_add" />
		<?php echo JHtml::_('form.token'); ?>

	</form>
<?php else: ?>
	<?=JTEXT::_('COM_ISHOP_CADDY_IS_EMPTY')?>
<?php endif ?>
