window.addEvent('domready', function() {
	document.formvalidator.setHandler('fio',
		function (value) {
			regex=/^[^0-9]+$/;
			return regex.test(value);
	});
});
    jQuery(document).ready(function($){
        $.mask.definitions['#']='[9]';  
        $("#jform_phone").mask("+7(#99) 999-99-99");
        
        $('#com_ishop_user_registration').click(function() {
            if ($(this).is(':checked')) 
            {
                $('#ishop-user_register').show('slow');
                $('.registration').attr('required','required');
            }
            else
            {
                $('#ishop-user_register').hide('slow');
                $('#error_msg').hide('slow');
                $('.registration').removeAttr('required');
            }
        });
        $('.fio').change(function(){
            var name = '';
            $('.fio').each(function(){
                name = name=='-'?name:name+' '+$(this).val();
            });
            $('#jform_name').val(name);
        });
        $('#jform_email').change(function(){
            var email = $(this).val();
            $('#jform_email1').val(email);
        });
        $('#jform_email1').change(function(){
            var email = $(this).val();
            $('#jform_email2').val(email);
        });
        // Инициируем Имя пользователя
        $('.inputbox').change();
    });

