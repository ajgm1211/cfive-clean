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

$(document).on('click', '.delete-api-integration', function() {
    var id = $(this).closest("td").find(".api_id").val();
    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "Please confirm!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, I am sure!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: 'GET',
                url: '/api/delete/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
                        swal(
                            'Well done!',
                            'The record has been deleted.',
                            'success'
                        )
                    }
                    $(theElement).closest('tr').remove();
                }
            });
        }
    });
});

$(document).on('click', '.open_edit_modal', function(e) {
    var id = $(this).closest("td").find(".api_id").val();
    $.ajax({
        type: 'get',
        url: '/api/edit/' + id,
        success: function(data) {

            console.log(data);
            $('#EditIntegrationModal').modal('show');
            $('#id').val(data.id);
            $('#name').val(data.data.name);
            $('#url').val(data.data.url);
            $('#api_key').val(data.data.api_key);
            $('#partner_id').val(data.data.partner_id);
            $('#module').val(data.data.module);
            $('#api_integration_id').val(data.data.id);
        }
    });
})

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