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
<?php if( $this->items ) : ?>
	<ul class="step-wrapper">
		<li class="step <?php echo $this->action=='step1'?'active':''?>" >
			<span>1. Список покупок</span>
		</li>
		<li class="step <?php echo $this->action=='step2'?'active':''?>">
			<span>2. Способ оплаты</span>
		</li>
		<li class="step <?php echo $this->action=='step3'?'active':''?>">
			<span>3. Способ доставки</span>
		</li>
		<li class="step <?php echo $this->action=='step4'?'active':''?>">
			<span>4. Завершение заказа</span>
		</li>
	</ul>

	<!--Загружаем нужный шаг--> 
	<?php  echo $this->loadTemplate($this->action);?>


<?php else: ?>
	<?=JTEXT::_('COM_ISHOP_CADDY_IS_EMPTY')?>
<?php endif ?>
<div id="ishop_debud"></div>
<script src="<?=JURI::root()?>components/com_ishop/assets/js/caddy.js"></script>