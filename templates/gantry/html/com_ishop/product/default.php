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

	<div class="item_leftside image">
		<div class="big-image">
                    <?php $href = incase::thumb($images->img_large, $this->item->id, 315, 495)?>
                        <a class="fancybox" href="<?=$href?>" rel='{handler: "iframe"}'>
				<img src="<?=incase::thumb($images->img_large, $this->item->id, 100, 100)?>" src="/images/load.gif" alt="<?=$this->item->name?>"/>
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

				<?php if(isset($this->item->opisanije) AND $this->item->opisanije):?>
					<tr>
						<td class="left">
							<span class="title">
								<?= JText::_('COM_ISHOP_OPISANIJE').': ' ?>
							</span>
						</td>
						<td class="right">
							<span class="val">
								<?=$this->item->opisanije?>
							</span>
						</td>
					</tr>
				<?php endif;?>

			</tbody>

		</table>
        </div>
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


<?php endif ?>
<script src="<?=JURI::root()?>components/com_ishop/assets/js/caddy.js"></script>
<script src="<?=JURI::root()?>components/com_ishop/assets/fancybox/jquery.fancybox-1.3.4.js"></script>
<script src="<?=JURI::root()?>components/com_ishop/assets/js/number.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $(".fancybox").fancybox();
    });
</script>
<?php
  $comments = JPATH_ROOT.'/components/com_jcomments/jcomments.php';
  if (file_exists($comments)) {
    require_once($comments);
    echo JComments::showComments($this->item->id, 'com_ishop', $this->item->name);
  }
