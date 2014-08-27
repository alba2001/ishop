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
    div.div_user_detail{
        padding: 5px;
        margin: 5px;
    }
</style>
<div class="div_user_detail">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'COM_ISHOP_USER_DETAILS' ); ?></legend>
    
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
    </fieldset>
</div>
<div style="clear:both"></div>
        <table>
            <thead>
                <tr>
                    <th><?=JTEXT::_('COM_ISHOP_SITE')?></th>
                    <th><?=JTEXT::_('COM_ISHOP_ARTIKUL')?></th>
                    <th><?=JTEXT::_('COM_ISHOP_HEADING_NAME')?></th>
                    <th><?=JTEXT::_('COM_ISHOP_PRICE')?></th>
                    <th><?=JTEXT::_('COM_ISHOP_COUNT')?></th>
                    <th><?=JTEXT::_('COM_ISHOP_SUM')?></th>
                </tr>
            </thead>
            <?php foreach($this->items as $item):?>
                <?php $id = $item['site_id'].'_'.$item['id']?>
                <tr>
                    <td><img src="<?=$item['src']?>" alt="<?=$item['artikul']?>"> <?=$item['site_name']?></td>
                    <td><?=$item['artikul']?></td>
                    <td><?=$item['name']?></td>
                    <td><?=$item['price']?></td>
                    <td><?=$item['count']?></td>
                    <td><?=(int)$item['sum']?></td>
                </tr>
            <?php endforeach;?>
        </table>
<?php else: ?>
    <?=JTEXT::_('COM_ISHOP_CADDY_IS_EMPTY')?>
<?php endif ?>
<div id="ishop_debud"></div>