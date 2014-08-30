<?php
// no direct access
defined('_JEXEC') or die;
jimport('incase.init');
?>

<div id="ShopModule<?php echo $module->id; ?>">
	<? if(count($items)){ ?>

			<div class="rs-carousel">
				<ul class="slides">

					<?php foreach ($items as $key=>$item){?>
						<li>
							<div class="com_uvelir_item  ">

								<div class="labels <?=$item->label?>"></div>


								<div class="image">
									<a href="<?=$item->link?>">
                                        <img src="<?=$item->image?>" alt="<?=$item->title?>"/>
									</a>
								</div>

								<div class="item-title">
									<a href="<?=$item->link?>"><?=incase::strimwidth($item->title, 38); ?></a>
								</div>

								<div class="right">
									<span class="price"><?=$item->cena_tut?></span>
									<span class="rub"> pуб.</span>
								</div>

								<div class="left">
									<input class="addButton button" id="add_<?php echo $item->id?>" type="button" value="Купить"
										onclick="uvelir_caddy_add({
										action:'<?php echo JRoute::_('index.php'); ?>',
										data:{
											option: 'com_uvelir',
											task: 'caddy.add',
											item_id: '<?php echo $item->id?>',
											'<?php echo JSession::getFormToken()?>':'1'
										}
									}); jQuery.jGrowl('<a href=\'<?=$item->link?>\'><?=htmlspecialchars($item->title, ENT_QUOTES)?></a>');"
									/>		
								</div>

							</div>
						</li>

					<?}?>

				</ul>
			</div>
		
	<?php }?>
</div>
