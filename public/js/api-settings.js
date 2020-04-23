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
            'enable' : value,
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
    var enable = $('#enable_api').val();
    if(enable=='on'){
        enable=1;
    }else{
        enable=0;
    }
    $.ajax({
        type: 'GET',
        url: '/api/store/key',
        data: {
            'api_key' : $('#api_key').val(),
            'enable' : enable,
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