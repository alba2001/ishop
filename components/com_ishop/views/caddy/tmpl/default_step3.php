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
$val_dostavka = isset($this->zakaz['dostavka'])?$this->zakaz['dostavka']:'1';
?>
<form action="<?php echo JUri::base().'zavershenie-zakaza'; ?>" method="post" name="step1_form" id="step2_form" class="cart">
    <table>
        <?php foreach($this->dostavkas as $dostavka):?>
            <tr>
                <td colspan="6" class="left first last">
                    <label for="com_ishop_dostavka_<?=$dostavka->id?>" type="radio" >
                        <input id="com_ishop_dostavka_<?=$dostavka->id?>" type="radio" name="dostavka" value="<?=$dostavka->id?>" <?=$val_dostavka==$dostavka->id?'checked="checked"':''?>/>
                        <span><?=$dostavka->name?></span>
                    </label>
                    <?php if (isset($dostavka->desc)): ?>
                        <span class="span_desc"><?=$dostavka->desc?></span>
                    <?php endif ?>
                </td>
            </tr>
            <?php if(isset($dostavka->desc)):?>
                <tr>
                </tr>
            <?php endif;?>
        <?php endforeach?>

        <!--Кнопки-->

        <tr>
            <th colspan="4" class="left">
                <a href="<?php echo JUri::base().'sposob-oplaty'?>" class="button prev" />Назад</a>
            </th>
            <th colspan="2" class="right">
                <input id="to_step4" class="button next" type="submit" value="Далее" />
            </th>
        </tr>
    </table>

</form>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#to_step4').click(function(e){
         e.preventDefault();
         var dostavka = $('input[name=dostavka]:checked', '#step2_form').val();
         $.ajax({
            type: 'GET',
            data:{
                option: 'com_ishop',
                task: 'caddy.dostavka_submit',
                dostavka: dostavka
            },
            url: '<?=JURI::base()?>index.php?<?=JSession::getFormToken()?>=1',
            success: function(html){
                console.log(html);
                $('#step2_form').submit();
            }
        });
     });
    });
</script>

