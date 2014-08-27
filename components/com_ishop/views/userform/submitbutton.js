jQuery(document).ready(function($){

    
    jQuery('#member-registration_submit').click(function(e){
        e.preventDefault();
        var isValid=true;
        var forms = $$('form.form-validate');
        for (var i=0;i<forms.length;i++)
        {
                if (!document.formvalidator.isValid(forms[i]))
                {
                        isValid = false;
                        break;
                }
        }
        // Если нужно зарегистрировать пользователя
        if (isValid && $('#com_ishop_user_registration').is(':checked'))
        {
            isValid = false;
            var url = 'index.php?option=com_users&task=userform.register&'+Token+'=1';
            var jform_data = {
                        'jform[name]':$('#jform_name').val(),
                        'jform[username]':$('#jform_username').val(),
                        'jform[password1]':$('#jform_password1').val(),
                        'jform[password2]':$('#jform_password2').val(),
                        'jform[email1]':$('#jform_email1').val(),
                        'jform[email2]':$('#jform_email1').val()
                    };
//            console.log(jform_data);
//            var form = $('#member-registration');
            $.ajax({
                beforeSend : function (){
                    $('#error_msg').html('').hide('slow');
                },
                type: "POST",
                url: url,
                async: false,
                data: jform_data,
                success: function(result){
                    var data = jQuery.parseJSON(result);
                    console.log(data);
                    if(data[0] == 1)
                    {
                        isValid = true;
                        $('#jform_uid').val(data[2]);
                        alert(data[1]);
                    }
                    else
                    {
                        $('#error_msg').html(data[1]).show('slow');
                        
                    }
                },
                error: function(result){
                    $('#error_msg').html(result).show('slow');
                }
            });

        }
        if (isValid)
        {
                jQuery('#member-registration').submit();
                return true;
        }
        else
        {
                alert(Joomla.JText._('COM_ISHOP_ISHOP_ERROR_UNACCEPTABLE','Некоторые поля заполнены не правильно'));
                return false;
        }
    });
});
