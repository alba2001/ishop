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
require_once JPATH_ROOT.'/components/com_ishop/helpers/ishop.php';
$dir_dest = JPATH_ROOT.'/media/com_ishop/images/';
$url_dest = JURI::base().'media/com_ishop/images/';
$token = JSession::getFormToken();

$images = json_decode($this->item->dopinfo);

    // Обработка корзины
if(isset($this->caddy[$this->item->id.'_0']))
{
	$btn_del_style = $count_li_style = 'style="display:inline-block;"';
	$caddy_count = $this->caddy[$this->item->id.'_0']['count'];
}
else
{
	$caddy_count = 0;
	$btn_del_style = $count_li_style = 'style="display:none"';
}

?>
<?php if( $this->item ) : ?>

	<h1>
		<?php echo $this->item->name ?>
	</h1>
<section class="primary">
	<div class="item_leftside image">
		<div class="big-image">
                    <?php $href = incase::thumb($images->img_large, $this->item->id, 315, 495)?>
                        <a class="fancybox" href="<?=$href?>" rel='{handler: "iframe"}'>
                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32"><g id="icomoon-ignore"><line stroke-width="1" x1="" y1="" x2="" y2="" stroke="" opacity=""></line></g><path d="M13.333 1.333q2.438 0 4.661 0.953t3.828 2.557 2.557 3.828 0.953 4.661q0 2.094-0.682 4.010t-1.943 3.479l7.573 7.563q0.385 0.385 0.385 0.948 0 0.573-0.38 0.953t-0.953 0.38q-0.563 0-0.948-0.385l-7.563-7.573q-1.563 1.26-3.479 1.943t-4.010 0.682q-2.438 0-4.661-0.953t-3.828-2.557-2.557-3.828-0.953-4.661 0.953-4.661 2.557-3.828 3.828-2.557 4.661-0.953zM13.333 4q-1.896 0-3.625 0.74t-2.979 1.99-1.99 2.979-0.74 3.625 0.74 3.625 1.99 2.979 2.979 1.99 3.625 0.74 3.625-0.74 2.979-1.99 1.99-2.979 0.74-3.625-0.74-3.625-1.99-2.979-2.979-1.99-3.625-0.74zM13.333 8q0.552 0 0.943 0.391t0.391 0.943v2.667h2.667q0.552 0 0.943 0.391t0.391 0.943-0.391 0.943-0.943 0.391h-2.667v2.667q0 0.552-0.391 0.943t-0.943 0.391-0.943-0.391-0.391-0.943v-2.667h-2.667q-0.552 0-0.943-0.391t-0.391-0.943 0.391-0.943 0.943-0.391h2.667v-2.667q0-0.552 0.391-0.943t0.943-0.391z"></path></svg>
				<img src="<?=incase::thumb($images->img_large, $this->item->id, 300, 300)?>" src="/images/load.gif" alt="<?=$this->item->name?>"/>
			</a>
		</div>
	</div>

	<div class="item_rightside">

		<table class="attributes">
			<tbody>

				<?php if(isset($this->item->artikul) AND $this->item->artikul):?>
					<tr>
						<td class="left">
							<span class="title">
								<?= JText::_('COM_ISHOP_ARTIKUL').': ' ?>
							</span>
						</td>
						<td class="right">
							<span class="val">
								<?=$this->item->artikul?>
							</span>
						</td>
					</tr>
				<?php endif;?>

				<tr>
					<td class="left">
						<span class="title">
							<?= JText::_('COM_ISHOP_PRODUCTS_AVIALABLE').': ' ?>
						</span>
					</td>
					<td class="right">
						<span class="val">
							<?=$this->item->available?JText::_('COM_ISHOP_PRODUCTS_AVIALABLED'):JText::_('COM_ISHOP_PRODUCTS_NOT_AVIALABLED')?>
						</span>
					</td>
				</tr>

			</tbody>

		</table>
		<div class="wr-bar">
			<div class="button-block">
			
			                        <input class="addButton button" id="add_<?php echo $this->item->id?>" type="button" value="Купить"
			                                onclick="ishop_caddy_add({
			                                action:'<?php echo JRoute::_('index.php'); ?>',
			                                data:{
			                                        option:     'com_ishop',
			                                        task:       'caddy.add',
			                                        item_id:    '<?php echo $this->item->id?>',
			                                        '<?=$token;?>':'1'
			                                        }
			                                });"
			                        />		
			                    
					</div>
			
					<div class="price-block">
						<div>Цена:</div>
						<?php $prises = ComponentHelper::getPrices($this->item->id); ?>
			
						<?php if((int)$prises['cena_tut']):?>
							<span id="item_cena_tut" class="item-price">
								<?=number_format($prises['cena_tut'], 0, '.', ' ') . ' '?>
							</span>
							<span class="currency">
								<?=JTEXT::_('COM_ISHOP_RUB')?>
							</span>
						<?php else:?>
							<?=' '.JTEXT::_('COM_ISHOP_MANAGER_CENA')?>
						<?php endif?>
					</div>
		</div>
				
				
				<?php 
					// Социальнве закладки
					echo JHTML::_('content.prepare', '{loadposition social_bookmarks}'); 
				?>
        </div>
</section>
				
<?php if(isset($this->item->desc) AND $this->item->desc):?>
<ul class="tabs" role="tablist">
  <li class="active"><a href="#desc" role="tab" data-toggle="tab"><?= JText::_('COM_ISHOP_OPISANIJE').': ' ?></a></li>
</ul>
<div class="tab-content grey">
	<div id="desc" class="tab-pane active">
	  <?=$this->item->desc?>
	</div>
</div>
<?php endif;?>

<?php endif ?>
<script src="<?=JURI::root()?>components/com_ishop/assets/js/caddy.js"></script>
<script src="<?=JURI::root()?>components/com_ishop/assets/fancybox/jquery.fancybox-1.3.4.js"></script>
<script src="<?=JURI::root()?>components/com_ishop/assets/js/number.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $(".fancybox").fancybox();
    });
</script>

<div class="featured-products">
<?php
//  Рекомендуемые товары
echo JHTML::_('content.prepare', '{loadposition recomended_products}');
?>
</div>

<?php
// Комментарии
  $comments = JPATH_ROOT.'/components/com_jcomments/jcomments.php';
  if (file_exists($comments)) {
    require_once($comments);
    echo JComments::showComments($this->item->id, 'com_ishop', $this->item->name);
  }
  
