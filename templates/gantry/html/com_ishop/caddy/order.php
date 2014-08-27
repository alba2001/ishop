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

<?php implode(dirname(__FILE__) . '/defailt_step4.php'); ?>
<?php require_once JPATH_COMPONENT.DS.'views'.DS.'caddy'.DS.'tmpl'.DS.'defailt_step4.php';  ?>


<?php else: ?>
    <?=JTEXT::_('COM_ISHOP_CADDY_IS_EMPTY')?>
<?php endif ?>
<script type="text/javascript">
</script>
<div id="ishop_debud"></div>