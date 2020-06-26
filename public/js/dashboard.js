$(document).ready(function() {

    $('#filter_by_user').on('click', function(e) {
        e.preventDefault();

        var user = $("#user").val();

        var form = $(this).parents('form');

        if (user != 0) {
            form.submit();
        } else {
            notification('You must select an user', 'error');
        }

    });
});


function notification(message, type) {

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

    switch (type) {
        case "error":
            toastr.error(message, 'ERROR');
            break;
        case "success":
            toastr.success(message, 'SUCCESS');
            break;
        default:
            toastr.info(message, '');
    }
}