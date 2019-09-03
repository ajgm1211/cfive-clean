//binds to onchange event of your input field
$(document).on('change', '#logo', function (e) {
    if(this.files[0].size>1000000){
        $("#logo-error").removeClass('hide');
        $("#default-currency-submit").prop("disabled", true);
    }else{
        $("#logo-error").addClass('hide');
        $("#default-currency-submit").prop("disabled", false);
    }
});
$('#email_from').blur(function() {
    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
    if ($('#email_from').val()!='' && !testEmail.test($('#email_from').val())){
        $('#email_from_error').removeClass('hide');
    }else{
        $('#email_from_error').addClass('hide');
    }
});
//Guardar settings
$(document).on('click', '#default-currency-submit', function () {
    var id = $('#company_id').val();
    var form = $('#default-currency');
    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
    var email_from_format = '';
    
    if ($('#email_from').val()!='' && testEmail.test($('#email_from').val())){
        var email_from_format = $('#email_from').val();
    }
    
    if($('#company_id').val()!=''&&$('#name').val()!=''&&$('#phone').val()&&$('#address').val()) {
        swal({
            title: 'Are you sure?',
            text: "Please confirm!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, I am sure!'
        }).then(function (result) {
            if (result.value) {

                // Create an FormData object
                //var data = new FormData(form);
                var footer_text = tinymce.get("footer_text").getContent();
                var signature_text = tinymce.get("email_signature_text").getContent();
                var data = new FormData($("#default-currency")[0]);
                data.append("footer_text_content", footer_text);
                data.append("signature_text_content", signature_text);
                data.append("email_from_format", email_from_format);
                // disabled the submit button
                $("#default-currency-submit").prop("disabled", true);

                $.ajax({
                    type: 'POST',
                    enctype: 'multipart/form-data',
                    url: '/settings/store/profile/company',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.message == 'Ok') {
                            swal(
                                'Done!',
                                'Your choice has been saved.',
                                'success'
                            )
                            location.reload();
                        }
                        $("#default-currency-submit").prop("disabled", false);
                    }
                });

            }

        });
    }else{
        swal({
            title: 'There are empty fields',
            text: "",
            type: 'error',
            showCancelButton: false,
        })
    }
});

//Mostrar/Ocultar opciones pdf
$(document).on('change', '#pdf_footer', function () {
    var value = $(this).val();
    if(value=='Text'){
        $('#footer_text').removeClass('hide');
        $('#footer_image').addClass('hide');
    }else{
        $('#footer_image').removeClass('hide');
        $('#footer_text').addClass('hide');
    }
});

//Mostrar/Ocultar opciones firma email
$(document).on('change', '#signature_type', function () {
    var value = $(this).val();
    if(value=='text'){
        $('#signature_text').removeClass('hide');
        $('#signature_image').addClass('hide');
    }else{
        $('#signature_image').removeClass('hide');
        $('#signature_text').addClass('hide');
    }
});

//Select2
$('#currency_id').select2({
    placeholder: "Select an option"
});
$('#pdf_language').select2({
    placeholder: "Select an option"
});
$('#pdf_footer').select2({
    placeholder: "Select an option"
});
$('#signature_type').select2({
    placeholder: "Select an option"
});