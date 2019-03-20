$.fn.editable.defaults.mode = 'inline';

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.editable').editable({
        url:'/v2/quotes/update/details',
        success: function(response, newValue) {
            if(!response) {
                return "Unknown error!";
            }

            if(response.success === false) {
                return response.msg;
            }
        }
    });
    $('#created_at').editable({
        format: 'yyyy-mm-dd',
        viewformat: 'dd/mm/yyyy',
        datepicker: {
            weekStart: 1
        },
        url:'/v2/quotes/update/details',
        success: function(response, newValue) {
            if(!response) {
                return "Unknown error!";
            }

            if(response.success === false) {
                return response.msg;
            }
        }
    });
});