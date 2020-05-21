//Mostrar/Ocultar opciones api
$(document).on('change', '#enable_api', function() {
    var value = 0;
    if ($(this).prop("checked") == true) {
        value = 1;
        $('#api-table').removeClass('hide');
    } else {
        $('#api-table').addClass('hide');
    }
    $.ajax({
        type: 'GET',
        url: '/api/enable',
        data: {
            'enable': value,
            'company_user_id': $('#company_user_id').val(),
        },
        success: function(data) {
            $('#api_integration_setting_id').val(data.data.id);
            /*swal(
                'Updated!',
                'Api enabled successfully',
                'success'
            )*/
        }
    });
});

//Guardar Api Key
$(document).on('click', '#store_api_key', function() {
    var enable = $('#enable_api').val();
    var key_name = $('#key_name').val();
    var url = $('#url').val();
    var api_key = $('#api_key').val();
    if (enable == 'on') {
        enable = 1;
    } else {
        enable = 0;
    }
    $.ajax({
        type: 'GET',
        url: '/api/store/key',
        data: {
            'api_key': api_key,
            'enable': enable,
            'key_name': key_name,
            'url': url,
            'company_user_id': $('#company_user_id').val(),
        },
        success: function(data) {
            swal(
                'Well done!',
                'Records saved successfully',
                'success'
            )
        }
    });
});