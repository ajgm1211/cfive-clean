//Quote Id
function display_quote_id(){
    $("#quote_id_span").attr('hidden','true');
    $("#quote_id_input").removeAttr('hidden');
    $("#save_quote_id").removeAttr('hidden');
    $("#cancel_quote_id").removeAttr('hidden');
    $("#edit_quote_id").attr('hidden','true');
}

function cancel_quote_id(){
    $("#quote_id_span").removeAttr('hidden');
    $("#quote_id_input").attr('hidden','true');
    $("#save_quote_id").attr('hidden','true');
    $("#cancel_quote_id").attr('hidden','true');
    $("#edit_quote_id").removeAttr('hidden');
}

function save_quote_id(id){
    $.ajax({
        type: 'GET',
        url: '/v2/quotes/update/details/quoteid/' + id,
        data: {
            'quote_id' : $("#quote_id_input").val(),
        },
        success: function(data) {
            swal(
                'Updated!',
                'Business name has been updated.',
                'success'
            )
            $("#quote_id_span").removeAttr('hidden');
            $("#quote_id_input").attr('hidden','true');
            $("#save_quote_id").attr('hidden','true');
            $("#cancel_quote_id").attr('hidden','true');
            $("#edit_quote_id").removeAttr('hidden');
            $("#quote_id_span").html(data.business_name);
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

$.fn.editable.defaults.mode = 'inline';

$(document).ready(function() {
    $('#type').editable({
        tpl:'<input type="text" style="width: 100px">',
        value: 2,
        source: [
            {value: 1, text: 'Active'},
            {value: 2, text: 'Blocked'},
            {value: 3, text: 'Deleted'}
        ]
    });
});