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
<ul class="step-wrapper">
	<li class="step"><span>1</span><div>Список покупок</div></li>
	<li class="step"><span>2</span><div>Способ доставки</div></li>
	<li class="step"><span>3</span><div>Способ оплаты</div></li>
	<li class="step active"><span>4</span><div>Завершение заказа</div></li>
</ul>
<div class="div_user_detail expand-source">
	<p>Личные данные</p>
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="user_detail_show" id="user_detail_show">
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
	    <input type="hidden" name="option" value="com_ishop" />
	    <input type="hidden" name="task" value="caddy.order_add" />
	    <?php echo JHtml::_('form.token'); ?>
	    <input class="button" type="submit" value="<?=JTEXT::_('COM_ISHOP_EDIT_USERDATA')?>" />

	</form>
</div>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="caddy_show" id="caddy_show">
    <table>
    	<thead>
    	    <tr>
    	        <th colspan="2" class="left">Выбранные товары</span></th>
    	        <th>
    	        	<?=JTEXT::_('COM_ISHOP_COUNT')?>
    	        	<span class="separator"></span>
    	        </th>
    	        <th>
    	        	<?=JTEXT::_('COM_ISHOP_PRICE')?>
    	        	<span class="separator"></span>
    	        </th>
    	        <th>
    	    		<?=JTEXT::_('COM_ISHOP_SUM')?>
    	        	<span class="separator">
    	        </th>
    	    </tr>
    	</thead>
    	<tbody>
    		<tr class="separator">
    			<td colpan="5"></td>
    		</tr>
	        <?php foreach($this->items as $item):?>
	            <?php $id = $item['site_id'].'_'.$item['id']?>
	            <tr>
	                <td>
	                	<div class="image">
	                		<a href="<?=$href;?>">
		                		<img src="<?=$item['src']?>" alt="<?=$item['artikul']?>">
		                	</a>
	                	</div>
	                </td>
	                <td class="info">
	                	<?php if(isset($this->item->name) AND $this->item->name):?>
	        			<?php endif;?>
	                		<div class="item_title">
	                			<a href="<?=$href;?>">
		                			<?=$item['name']?>
		                		</a>
	                		</div>

	            		<div class="manufacturer">
	            			Завод: <?=$item['site_name']?>
	            		</div>

	            		<?php if(isset($this->item->razmer) AND $this->item->razmer):?>
	                		<div class="size">
	                			Размер: <?=$item['razmer']?>
	                		</div>
	        			<?php endif;?>

	            		<div class="article">
	            			Артикул: <?=$item['artikul']?>
	            		</div>
	                </td>
	                <td class="price">
	                    <?=$item['count']?>
	                </td>
	                <td class="price">
	                	<span id="caddy_item_price_<?=$id?>"><?=$item['price']?></span>
	                	<span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
	                </td>
	                <td class="caddy_item_sum">
	                	<span id="caddy_item_sum_<?=$id?>"><?=(int)$item['sum']?></span>
	                	<span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
	                </td>
	            </tr>
	        <?php endforeach;?>
        </tbody>
        <tfoot>
        	<tr>
        		<td colspan="5" class="right">
        			<span class="gray-sum">
        				<?=JTEXT::_('COM_ISHOP_ITOGO')?>:
        			</span>
	    			<span class="black-sum" id="caddy_total_sum">
	    				<?=$this->caddy_data['sum']?>
	    			</span>
	    			<span class="ruble">
	    				<?=JTEXT::_('COM_ISHOP_RUB')?>
	    			</span>
        		</td>
        	</tr>
        	<tr>
        		<th colspan="3" class="left">
        			<a href="javascript:history.back()">
	        			<button class="button">
	        				Назад
	        			</button>
        			</a>
        		</th>
        		<th colspan="2" class="right">
				    <input type="hidden" name="option" value="com_ishop" />
				    <input type="hidden" name="task" value="caddy.order_add" />
				    <?php echo JHtml::_('form.token'); ?>
        			<input class="button" type="submit" value="Подтвердить"/>
        		</th>
        	</tr>
        </tfoot>

    </table>

</form>
<?php else: ?>
    <?=JTEXT::_('COM_ISHOP_CADDY_IS_EMPTY')?>
<?php endif ?>
<div id="ishop_debud"></div>