//Business_name
function display_business_name() {
    $("#business_name_span").attr('hidden', 'true');
    $("#business_name_input").removeAttr('hidden');
    $("#save_business_name").removeAttr('hidden');
    $("#cancel_business_name").removeAttr('hidden');
    $("#edit_business_name").attr('hidden', 'true');
}

function cancel_business_name() {
    $("#business_name_span").removeAttr('hidden');
    $("#business_name_input").attr('hidden', 'true');
    $("#save_business_name").attr('hidden', 'true');
    $("#cancel_business_name").attr('hidden', 'true');
    $("#edit_business_name").removeAttr('hidden');
}

function save_business_name(id) {
    $.ajax({
        type: 'GET',
        url: '/companies/update/details/name/' + id,
        data: {
            'business_name': $("#business_name_input").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'Business name has been updated.',
                'success'
            )
            $("#business_name_span").removeAttr('hidden');
            $("#business_name_input").attr('hidden', 'true');
            $("#save_business_name").attr('hidden', 'true');
            $("#cancel_business_name").attr('hidden', 'true');
            $("#edit_business_name").removeAttr('hidden');
            $("#business_name_span").html(data.business_name);
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

//Phone
function display_phone() {
    $("#phone_span").attr('hidden', 'true');
    $("#phone_input").removeAttr('hidden');
    $("#save_phone").removeAttr('hidden');
    $("#cancel_phone").removeAttr('hidden');
    $("#edit_phone").attr('hidden', 'true');
}

function cancel_phone() {
    $("#phone_span").removeAttr('hidden');
    $("#phone_input").attr('hidden', 'true');
    $("#save_phone").attr('hidden', 'true');
    $("#cancel_phone").attr('hidden', 'true');
    $("#edit_phone").removeAttr('hidden');
}

function save_phone(id) {
    $.ajax({
        type: 'GET',
        url: '/companies/update/details/phone/' + id,
        data: {
            'phone': $("#phone_input").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'Phone has been updated.',
                'success'
            )
            $("#phone_span").removeAttr('hidden');
            $("#phone_input").attr('hidden', 'true');
            $("#save_phone").attr('hidden', 'true');
            $("#cancel_phone").attr('hidden', 'true');
            $("#edit_phone").removeAttr('hidden');
            $("#phone_span").html(data.phone);
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

//Address
function display_address() {
    $("#address_span").attr('hidden', 'true');
    $("#address_input").removeAttr('hidden');
    $("#save_address").removeAttr('hidden');
    $("#cancel_address").removeAttr('hidden');
    $("#edit_address").attr('hidden', 'true');
}

function cancel_address() {
    $("#address_span").removeAttr('hidden');
    $("#address_input").attr('hidden', 'true');
    $("#save_address").attr('hidden', 'true');
    $("#cancel_address").attr('hidden', 'true');
    $("#edit_address").removeAttr('hidden');
}

function save_address(id) {
    $.ajax({
        type: 'GET',
        url: '/companies/update/details/address/' + id,
        data: {
            'address': $("#address_input").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'Address has been updated.',
                'success'
            )
            $("#address_span").removeAttr('hidden');
            $("#address_input").attr('hidden', 'true');
            $("#save_address").attr('hidden', 'true');
            $("#cancel_address").attr('hidden', 'true');
            $("#edit_address").removeAttr('hidden');
            $("#address_span").html(data.address);
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

//Email
function display_email() {
    $("#email_span").attr('hidden', 'true');
    $("#email_input").removeAttr('hidden');
    $("#save_email").removeAttr('hidden');
    $("#cancel_email").removeAttr('hidden');
    $("#edit_email").attr('hidden', 'true');
}

function cancel_email() {
    $("#email_span").removeAttr('hidden');
    $("#email_input").attr('hidden', 'true');
    $("#save_email").attr('hidden', 'true');
    $("#cancel_email").attr('hidden', 'true');
    $("#edit_email").removeAttr('hidden');
}

function save_email(id) {
    $.ajax({
        type: 'GET',
        url: '/companies/update/details/email/' + id,
        data: {
            'email': $("#email_input").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'Email has been updated.',
                'success'
            )
            $("#email_span").removeAttr('hidden');
            $("#email_input").attr('hidden', 'true');
            $("#save_email").attr('hidden', 'true');
            $("#cancel_email").attr('hidden', 'true');
            $("#edit_email").removeAttr('hidden');
            $("#email_span").html(data.email);
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }

    });
}

//Tax number
function display_tax_number() {
    $("#tax_number_span").attr('hidden', 'true');
    $("#tax_number_input").removeAttr('hidden');
    $("#save_tax_number").removeAttr('hidden');
    $("#cancel_tax_number").removeAttr('hidden');
    $("#edit_tax_number").attr('hidden', 'true');
}

function cancel_tax_number() {
    $("#tax_number_span").removeAttr('hidden');
    $("#tax_number_input").attr('hidden', 'true');
    $("#save_tax_number").attr('hidden', 'true');
    $("#cancel_tax_number").attr('hidden', 'true');
    $("#edit_tax_number").removeAttr('hidden');
}

function save_tax_number(id) {
    $.ajax({
        type: 'GET',
        url: '/companies/update/details/tax/' + id,
        data: {
            'tax_number': $("#tax_number_input").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'Tax number has been updated.',
                'success'
            )
            $("#tax_number_span").removeAttr('hidden');
            $("#tax_number_input").attr('hidden', 'true');
            $("#save_tax_number").attr('hidden', 'true');
            $("#cancel_tax_number").attr('hidden', 'true');
            $("#edit_tax_number").removeAttr('hidden');
            $("#tax_number_span").html(data.tax_number);
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }

    });
}

//PDF Language
function display_pdf_language() {
    $("#pdf_language_span").attr('hidden', 'true');
    $("#pdf_language_select").removeAttr('hidden');
    $("#save_pdf_language").removeAttr('hidden');
    $("#cancel_pdf_language").removeAttr('hidden');
    $("#edit_pdf_language").attr('hidden', 'true');
}

function cancel_pdf_language() {
    $("#pdf_language_span").removeAttr('hidden');
    $("#pdf_language_select").attr('hidden', 'true');
    $("#save_pdf_language").attr('hidden', 'true');
    $("#cancel_pdf_language").attr('hidden', 'true');
    $("#edit_pdf_language").removeAttr('hidden');
}

function save_pdf_language(id) {
    var language = '';
    $.ajax({
        type: 'GET',
        url: '/companies/update/details/pdf/' + id,
        data: {
            'pdf_language': $("#pdf_language_select").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'PDF language has been updated.',
                'success'
            )
            $("#pdf_language_span").removeAttr('hidden');
            $("#pdf_language_select").attr('hidden', 'true');
            $("#save_pdf_language").attr('hidden', 'true');
            $("#cancel_pdf_language").attr('hidden', 'true');
            $("#edit_pdf_language").removeAttr('hidden');
            if (data.pdf_language == 1) {
                language = 'English';
            } else if (data.pdf_language == 2) {
                language = 'Spanish';
            } else {
                language = 'Portuguese';
            }
            $("#pdf_language_span").html(language);
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }

    });
}

//Price level
function display_price_level() {
    $("#price_level_span").attr('hidden', 'true');
    $("#price_level_list").attr('hidden', 'true');
    $("#price_level_select").removeAttr('hidden');
    $("#save_prices").removeAttr('hidden');
    $("#cancel_prices").removeAttr('hidden');
    $("#edit_prices").attr('hidden', 'true');
}

function cancel_price_level() {
    $("#price_level_span").removeAttr('hidden');
    $("#price_level_list").removeAttr('hidden');
    $("#price_level_select").attr('hidden', 'true');
    $("#save_prices").attr('hidden', 'true');
    $("#cancel_prices").attr('hidden', 'true');
    $("#edit_prices").removeAttr('hidden');
}

function save_price_level(id) {
    var language = '';
    $.ajax({
        type: 'GET',
        url: '/companies/update/details/prices/' + id,
        data: {
            'price_id': $("#price_level_select").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'Price levels has been updated.',
                'success'
            )
            $("#price_level_span").removeAttr('hidden');
            $("#price_level_list").removeAttr('hidden');
            $("#price_level_select").attr('hidden', 'true');
            $("#save_prices").attr('hidden', 'true');
            $("#cancel_prices").attr('hidden', 'true');
            $("#edit_prices").removeAttr('hidden');

            $("#price_level_ul").html('');
            $.each(data, function(key, value) {
                $("#price_level_ul").append('<li style="margin-left: -25px;" class="color-black">' + value + '</li>');
            });

        },
        error: function(request, status, error) {
            alert(request.responseText);
        }

    });
}

//binds to onchange event of your input field
$(document).on('change', '#logo', function(e) {
    if (this.files[0].size > 1000000) {
        $("#logo-error").removeClass('hide');
    } else {
        $("#logo-error").addClass('hide');
    }
});

$(document).on('click', '#syncCompanies', function(e) {
    swal({
        title: 'Are you sure?',
        text: "Do you want to synchronize your information with the external API?",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then(function(result) {
        if (result.value) {
            $("#syncCompanies").addClass("hide");
            $("#syncCompaniesLoading").removeClass("hide");
            msg('Synchronizing. This process may take a few minutes &nbsp;<i class="fa fa-spin fa-spinner"></i>');
            $.ajax({
                type: 'GET',
                url: '/api/get/companies',
                success: function(data) {
                    console.log(data.message);
                    if (data.message == 'Ok') {
                        swal(
                            'Done!',
                            'Synchronization completed successfully.',
                            'success'
                        )

                        setTimeout(function() { location.reload(); }, 3000);

                        $("#syncCompaniesLoading").addClass("hide");
                        $("#syncCompanies").removeClass("hide");
                    }
                },
                error: function(request, status, error) {
                    msg('An error has occurred!', 'error');
                    console.log(request.responseText);
                    $("#syncCompaniesLoading").addClass("hide");
                    $("#syncCompanies").removeClass("hide");
                }
            });
        }
    });
});

function msg(message, type) {
    switch (type) {
        case "error":
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-bottom-center",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "0",
                "hideDuration": "0",
                "timeOut": "0",
                "extendedTimeOut": "0",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.error(message, 'ERROR');
            break;
        case "success":
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-bottom-center",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "0",
                "hideDuration": "0",
                "timeOut": "10000",
                "extendedTimeOut": "0",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.success(message, 'SUCCESS');
            break;
        default:
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-bottom-center",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "0",
                "hideDuration": "0",
                "timeOut": "10000",
                "extendedTimeOut": "0",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.info(message, '');
    }
}