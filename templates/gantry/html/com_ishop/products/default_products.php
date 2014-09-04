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
jimport('incase.init');
$token = JSession::getFormToken();
?>
<style type="text/css">
    img.product_list_img{max-width: 117ph; max-height: 110px}
</style>


<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="adminForm" class="ishop-items items-wrapper">
        <?php if(isset($this->sort_order_products_list)): // Порядок сортировки товаров?>
        <div class="product_sort_order">
            <?=$this->sort_order_products_list?>
        </div>
        <?php endif?>
	<div class="items">
		<?php foreach ($this->items as $item) : ?>
			<?php
                        $href = IshopHelper::getURI($item->id);
			$dopinfo = json_decode($item->dopinfo);
			$src = incase::thumb($dopinfo->img_large, $item->id, 315, 495);;

			$novinka = ($item->novinka_dt > date('d.m.Y'))?'new':'';
			$spets_predl = $item->spets_predl?'spets_predl':'';

         // Вычисляем кол-во товаров в корзине без учета размеров
			$caddy_count = 0;
			$btn_del_style = $count_li_style = '';
			foreach($this->caddy as $key=>$value)
			{
				/* @var $key string */
				if(preg_match('/^'.$item->id.'_\d+$/', $key))
				{
					$caddy_count += $value['count'];
				}
			}
			if(!$caddy_count)
			{
				$caddy_count = 0;
				$btn_del_style = $count_li_style = 'style="display:none"';
			}
			?>
			<div class="com_ishop_item <?=$novinka.' '.$spets_predl?>">

				<div class="image">
					<a href="<?=$href?>">
						<img class="product_list_img" src="<?=$src?>" alt="<?=$item->name?>"/>
					</a>
				</div>

				<div class="item-title">
					<a href="<?=$href;?>"><?=incase::strimwidth( $item->name, 45); ?></a>
				</div>

				<div class="right">
					<?php $prises = ComponentHelper::getPrices($item->id); ?>

					<?php if((int)$prises['cena_mag']):?>
						<span class="price"><?=incase::format( $prises['cena_tut'] )?></span>
						<span class="rub"><?=' '.JTEXT::_('COM_ISHOP_RUB')?></span>
					<?php else:?>
						<?=' '.JTEXT::_('COM_ISHOP_MANAGER_CENA')?>
					<?php endif?>
				</div>
				<div class="left">
					<input class="addButton button" id="add_<?php echo $item->id?>" type="button" value="Купить"
						onclick="ishop_caddy_add({
						action:'<?php echo JRoute::_('index.php'); ?>',
						data:{
							option:     'com_ishop',
							task:       'caddy.add',
							item_id:    '<?php echo $item->id?>',
							'<?=$token;?>':'1'
							}
						});"
					/>		
				</div>


			</div><?//<!-- com_ishop_item -->?>

		<?php endforeach; ?>
	</div><?//<!-- items -->?>

<div class="pagination">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<input type="hidden" name="option" value="com_ishop" />
<input type="hidden" name="view" value="products" />
<input type="hidden" name="item_id" value="" />
<input type="hidden" name="products_group" value="<?=$this->products_group?>" />

<?php echo JHtml::_('form.token'); ?>
</form>
<script src="<?=JURI::root()?>components/com_ishop/assets/js/caddy.js"></script>
