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

//var_dump($this->children);
    $empty_img_src = JURI::base().'images/dummy.png';


    if($this->item->img)
    {
        $img_src = JURI::base().$this->item->img;
    }
    else
    {
        $img_src = $empty_img_src;
    }
?>

<?php if( $this->item ) : ?>
	<div class="category items-wrapper">

		<!--Детали категории-->
	    <h1>
	        <?/*<img class="thumb" src="<?=$img_src?>" atl="<?=$this->item->name?>"/>*/?>
	        <?=ucfirst( mb_convert_case($this->item->name, MB_CASE_TITLE, 'UTF-8') );?>
	    </h1>
	    <div class="description">
		    <?php if($this->item->note):?>
		        <h6><?=$this->item->note?></h6>
		    <?php endif;?>
		    <?php if($this->item->description):?>
		        <?=$this->item->description?>
		    <?php endif;?>
	    </div>

		<!--Список зависимых категорий-->
	    <?php if($this->children):?>
	        <div class="ishop_subcategiries">
	            <ul class="cats">
	            <?php foreach ($this->children as $child):?>
	                <?php
	                    if(!empty($child->img))
	                    {
	                        $img_src = $child->img;
	                    }
	                    else
	                    {
	                        $img_src = $empty_img_src;
	                    }
	                ?>
                        <?php $ch_alias = preg_replace('/_\d+$/', '', $child->alias);?>
	                <li class="com_ishop_cat">
	                    <?php $href = JRoute::_('index.php?option=com_ishop&alias='.$ch_alias)?>
	                    <div class="image">
		                    <a href="<?=$href?>">
		                    		<img src="<?=$img_src?>" alt="<?=$this->item->name?>"/>
		                    </a>
	                    </div>
	                    <a href="<?=$href?>">
		                    <?=$child->name?>
	                    </a>
	                </li>
	            <?php endforeach;?>
	            </ul>
	        </div>
	    <?php endif;?>

		<!--Список товаров категории-->
		<?php if($this->items):?>
			<?php echo $this->loadTemplate('products');?>
		<?php endif;?>
	</div><!-- category -->
<?php endif ?>
<script src="<?=JURI::root()?>components/com_ishop/assets/js/caddy.js"></script>