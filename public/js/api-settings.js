//Mostrar/Ocultar opciones api
$(document).on('change', '#enable_api', function () {
    var value = 0;
    if($(this). prop("checked") == true){
        value = 1;
        $('#api-table').removeClass('hide');
    }else{
        $('#api-table').addClass('hide');
    }

    $.ajax({
        type: 'GET',
        url: '/api/enable/',
        data: {
            'value' : value,
            'company_user_id' : $('#company_user_id').val(),
        },
        success: function(data) {
            /*swal(
                'Updated!',
                'Api enabled successfully',
                'success'
            )*/
        }
    });
});

//Guardar Api Key
$(document).on('click', '#store_api_key', function () {
    $.ajax({
        type: 'GET',
        url: '/api/store/key',
        data: {
            'api_key' : $('#api_key').val(),
            'company_user_id' : $('#company_user_id').val(),
        },
        success: function(data) {
            swal(
                'Well done!',
                'Api key saved successfully',
                'success'
            )
        }
    });
});