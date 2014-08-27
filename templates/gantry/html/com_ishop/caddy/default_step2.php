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

$val_oplata = isset($this->zakaz['oplata'])?$this->zakaz['oplata']:'1';

?>
<form action="<?php echo JUri::base().'sposob-dostavki'?>" method="post" name="step1_form" id="step3_form" class="cart">
    <table>
        <?php foreach($this->oplatas as $oplata):?>
            <tr>
                <td colspan="6" class="left first last">
                    <label for="com_ishop_oplata_<?=$oplata->id?>" type="radio" >
                        <input id="com_ishop_oplata_<?=$oplata->id?>" type="radio" name="oplata" value="<?=$oplata->id?>" <?=$val_oplata==$oplata->id?'checked="checked"':''?>/>
                        <span><?=$oplata->name?></span>
                    </label>
                    <?php if(isset($oplata->desc)):?>
                     <span class="span_desc"><?=$oplata->desc?></span>
                 <?php endif;?>
             </td>
         </tr>
     <?php endforeach?>
     <!--Кнопки-->    
     <tr>
        <th colspan="4" class="left first">
            <a href="<?php echo JUri::base().'spisok-pokupok'?>" class="button prev" />Назад</a>
        </th>
        <th colspan="2" class="right last">
            <input id="to_step3" class="button next" type="submit" value="Далее" />
        </th>
    </tr>
</table>

</form>

<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#to_step3').click(function(e){
            e.preventDefault();
            var oplata = $('input[name=oplata]:checked', '#step3_form').val();
            $.ajax({
                type: 'GET',
                data:{
                    option: 'com_ishop',
                    task: 'caddy.oplata_submit',
                    oplata: oplata
                },
                url: '<?=JURI::base()?>index.php?<?=JSession::getFormToken()?>=1',
                success: function(html){
                   console.log(html);
                   $('#step3_form').submit();
               }
           });

        });
    });
</script>

