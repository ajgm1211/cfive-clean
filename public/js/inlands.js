function display_twuenty(id){

    $("#tr_twuenty"+id+" .val").attr('hidden','true');
    $("#tr_twuenty"+id+" .in").removeAttr('hidden');
    $("#tr_twuenty"+id+" .in input , #tr_twuenty"+id+" .in select ").prop('disabled', false);


    $("#save_twuenty"+id).removeAttr('hidden');
    $("#cancel_twuenty"+id).removeAttr('hidden');
    $("#remove_twuenty"+id).removeAttr('hidden');
    $("#edit_twuenty"+id).attr('hidden','true');

}

function cancel_twuenty(id){

    $("#tr_twuenty"+id+" .val").removeAttr('hidden');
    $("#tr_twuenty"+id+" .in").attr('hidden','true');
    $("#tr_twuenty"+id+" .in input , #tr_twuenty"+id+" .in select ").prop('disabled', true);

    $("#save_twuenty"+id).attr('hidden','true');
    $("#cancel_twuenty"+id).attr('hidden','true');
    $("#remove_twuenty"+id).attr('hidden','true');
    $("#edit_twuenty"+id).removeAttr('hidden');

}

function save_twuenty(id,idval){
   
    $.ajax({
        type: 'GET',
        url: '../updateDetails/' + idval,
        data: {
            'lower' : $("#lowertwuenty"+id).val(),
            'upper' : $("#uppertwuenty"+id).val(),
            'ammount' : $("#ammounttwuenty"+id).val(),
            'currency_id' : $("#currencytwuenty"+id).val()
        },
        success: function(data) {
            swal(
                'Updated!',
                'Your Inland has been updated.',
                'success'
            )
            $("#save_twuenty"+id).attr('hidden','true');
            $("#cancel_twuenty"+id).attr('hidden','true');
            $("#remove_twuenty"+id).attr('hidden','true');
            $("#edit_twuenty"+id).removeAttr('hidden');

            $("#tr_twuenty"+id+" .val").removeAttr('hidden');
            $("#tr_twuenty"+id+" .in").attr('hidden','true');
            $("#tr_twuenty"+id+" .in input , #tr_twuenty"+id+" .in select ").prop('disabled', true);

            $("#divlowertwuenty"+id).html($("#lowertwuenty"+id).val());
            $("#divuppertwuenty"+id).html($("#uppertwuenty"+id).val());
 
           var ammount = $("#ammounttwuenty"+id).val()+"/"+$("#currencytwuenty"+id+" option:selected").text();
            $("#divammounttwuenty"+id).html(ammount);
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }

    });

}



$("#newtwuenty").on("click", function() {

    var $template = $('#twuentyclone');
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
    $myClone.find(".sel").addClass('col-lg-4'); 
    $("#twuenty").append($myClone);
});

$("#newforty").on("click", function() {

    var $template = $('#fortyclone');
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
    $myClone.find(".sel").addClass('col-lg-4'); 
    $("#forty").append($myClone);
});
$("#newfortyhc").on("click", function() {

    var $template = $('#fortyhcclone');
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
    $myClone.find(".sel").addClass('col-lg-4'); 
    $("#fortyhc").append($myClone);
});



$(document).on('click', '.m_sweetalert_demo_8', function (e) {
    var res = $("i",this).attr('id'); 
    var theElement = $(this);
    var idval = res.substr(4);

    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {
        if (result.value) {

            $.ajax({
                type: 'get',
                  url: '../deleteDetails/' + idval,
                success: function(data) {
                    swal(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    $(theElement).closest('tr').remove();

                }
            });

        }

    });

});


$(document).on('click', '.remove', function () {
    $(this).closest('tr').remove();

});

$('.m-select2-general').select2({
    placeholder: "Select an option"
});



