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
$caddy = JFactory::getApplication()->getUserState('com_ishop.caddy');
?>
<form action="<?=JURI::base()?>index.php?<?=JSession::getFormToken()?>=1" method="post" name="step1_form" id="step1_form" class="cart">
    <table>
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
                <th class="col5">
            		<span>Сумма</span>
                </th>
                <th class="col6 last"></th>
            </tr>
        </thead>
        <tbody>
	        <?php foreach($this->items as $item):?>
	            <?php $id = $item['id'].'_'.$item['razmer_key']?>
                <? $href = JRoute::_('index.php?option=com_ishop&alias=' . $id ); ?>
	            <tr id="item_row_<?=$id?>">
                    <td class="first"> 
	                	<div class="image">
                                    <a href="<?=$href?>">
                                        <img src="<?=incase::thumb($item['src'],$item['id'], 315, 495)?>" alt="<?=$item['artikul']?>">
                                    </a>
                                </div>
	                </td>
	                <td class="info">
	                	<?php if(isset($item['name']) AND $item['name']):?>
	                		<div class="item_title">
	                			<a href="<?=$href?>">
		                			<?=$item['name']?>
		                		</a>
	                		</div>
                        <?php endif;?>

                		<div class="article">
                			Артикул: <?=$item['artikul']?>
                		</div>
	                </td>
	                <td>
                        <span class="button com_ishop-arow arow_left" id="arow_left_<?=$id?>">-</span>
	                    <input id="caddy_item_count_<?=$id?>" name="count[<?=$id?>]" size="1" class="caddy_item_count" type="text" rel="<?=$id?>" value="<?=$item['count']?>"/>
                        <span class="button com_ishop-arow  arow_right" id="arow_right_<?=$id?>">+</span>
	                </td>
	                <td class="price">
                        <?php if((int)$item['price']):?>
                            <span class="nowrap">
        	                	<span id="caddy_item_price_<?=$id?>">
                                    <?=$item['price']?>
                                </span>
        	                	<span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
                            </span>
                        <?php else:?>
                            <?=' '.JTEXT::_('COM_ISHOP_MANAGER_CENA')?>
                        <?php endif;?>
	                </td>
	                <td class="caddy_item_sum">
	                	<span id="caddy_item_sum_<?=$id?>"><?=(int)$item['sum']?></span>
	                	<span class="ruble"><?=JTEXT::_('COM_ISHOP_RUB')?></span>
	                </td>
	                <td class="last">
                        <div class="com_ishop-delete" id="delete_<?=$id?>">
                        	<span class="com_ishop-arow button remove">x</span>
                        </div>
	                </td>
	            </tr>
	        <?php endforeach;?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="left first">
                    <a href="/" class="button prev">Назад</a>
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
        		<th colspan="2" class="right last">
                     <a href="<?php echo JUri::base().'sposob-oplaty'?>" class="button next" />Далее</a>
        		</th>
        	</tr>
        </tfoot>
    </table>
</form>

<!-- С этим товаром покупают -->

<?php if($this->purchases):?>
    <h3><? echo (count($this->items)>1) ? 'С этими товарами покупают:' : 'С этим товаром покупают:' ?></h3>
    <div class="ishop-items items-wrapper">
        <div class="items">
                <?php foreach($this->purchases as $item):?>
                    <? $href = JRoute::_('index.php?option=com_ishop&alias=' . $item['id'] ); ?>
                    <div class="com_ishop_item  ">
                            <div class="image">
                                <a href="<?=$href?>">
                                    <img src="<?=incase::thumb($item['img_src'],$item['id'],97,97)?>" alt="<?=$item['artikul']?>">
                                </a>
                            </div>
                            <div class="item-title">
                                <a href="<?=$href?>">
                                    <?=$item['name']?>
                                </a>
                            </div>
                            <div class="left">
                                <input class="addButton button" id="add_<?php echo $item->id?>" type="button" value="Купить"
                                onclick="ishop_caddy_add({
                                action:'<?php echo JRoute::_('index.php'); ?>',
                                data:{
                                option:     'com_ishop',
                                task:       'caddy.add',
                                item_id:    '<?php echo $item[id]?>',
                                '<?php echo JUtility::getToken()?>':'1'
                            }
                            });jQuery.jGrowl('<a href=\'<?=$href?>\'><?=htmlspecialchars($item['name'], ENT_QUOTES)?></a>');"
                            />      
                        </div>

                        <div class="right">
                            <?php if((int)$item['price']):?>
                                <span class="price"><?=incase::format( $item['price'] )?></span>
                                <span class="rub"><?=' '.JTEXT::_('COM_ISHOP_RUB')?></span>
                            <?php else:?>
                                <span class="small"></span>
                            <?php endif?>
                        </div>

                    </div>
                <?php endforeach?>
        </div>
    </div>
<?php endif?>
<?/* <!-- С этим товаром покупают end -->  */?>
