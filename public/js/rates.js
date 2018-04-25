
function display(id){

    $("#tr"+id+" .val").attr('hidden','true');
    $("#tr"+id+" .in").removeAttr('hidden');
    $("#tr"+id+" .in input , #tr"+id+" .in select ").prop('disabled', false);

    $("#save"+id).removeAttr('hidden');
    $("#cancel"+id).removeAttr('hidden');
    $("#edit"+id).attr('hidden','true');
}

function cancel(id){

    $("#tr"+id+" .val").removeAttr('hidden');
    $("#tr"+id+" .in").attr('hidden','true');
    $("#tr"+id+" .in input , #tr"+id+" .in select ").prop('disabled', true);

    $("#save"+id).attr('hidden','true');
    $("#cancel"+id).attr('hidden','true');
    $("#edit"+id).removeAttr('hidden');

}

function save(id,idval){


    $.ajax({
        type: 'GET',
        url: '../updateRate/' + idval,
        data: {
            'origin_port': $("#origin"+id).val(),
            'destiny_port': $("#destiny"+id).val(),
            'carrier_id': $("#carrier"+id).val(),
            'twuenty': $("#twuenty"+id).val(),
            'forty': $("#forty"+id).val(),
            'fortyhc': $("#fortyhc"+id).val(),
            'currency_id': $("#currency"+id).val(),
        },
        success: function(data) {
            $("#save"+id).attr('hidden','true');
            $("#cancel"+id).attr('hidden','true');
            $("#edit"+id).removeAttr('hidden');

            $("#tr"+id+" .val").removeAttr('hidden');
            $("#tr"+id+" .in").attr('hidden','true');
            $("#tr"+id+" .in input , #tr"+id+" .in select ").prop('disabled', true);

            $("#divoriginport"+id).html($("#origin"+id+" option:selected").text());
            $("#divdestinyport"+id).html($("#destiny"+id+" option:selected").text());
            $("#divcarrier"+id).html($("#carrier"+id+" option:selected").text());
            $("#divtwuenty"+id).html($("#twuenty"+id).val());
            $("#divforty"+id).html($("#forty"+id).val());
            $("#divfortyhc"+id).html($("#fortyhc"+id).val());
            $("#divalphacode"+id).html($("#currency"+id+" option:selected").text());

        }
    });

}

function display_l(id){

    $("#tr_l"+id+" .val").attr('hidden','true');
    $("#tr_l"+id+" .in").removeAttr('hidden');
    $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', false);

    $("#save_l"+id).removeAttr('hidden');
    $("#cancel_l"+id).removeAttr('hidden');
    $("#remove_l"+id).removeAttr('hidden');
    $("#edit_l"+id).attr('hidden','true');

}

function cancel_l(id){

    $("#tr_l"+id+" .val").removeAttr('hidden');
    $("#tr_l"+id+" .in").attr('hidden','true');
    $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', true);

    $("#save_l"+id).attr('hidden','true');
    $("#cancel_l"+id).attr('hidden','true');
    $("#remove_l"+id).attr('hidden','true');
    $("#edit_l"+id).removeAttr('hidden');

}

function save_l(id,idval){

    $.ajax({
        type: 'GET',
        url: '../updateLocalCharge/' + idval,
        data: {
            'surcharge_id' : $("#type"+id).val(),
            'port' : $("#port"+id).val(),
            'changetype' : $("#changetype"+id).val(),
            'carrier_id' : $("#localcarrier"+id).val(),
            'calculationtype_id' : $("#calculationtype"+id).val(),
            'ammount' : $("#ammount"+id).val(),
            'currency_id' : $("#localcurrency"+id).val()

        },
        success: function(data) {

            swal(
                'Updated!',
                'Your file has been updated.',
                'success'
            )
            $("#save_l"+id).attr('hidden','true');
            $("#cancel_l"+id).attr('hidden','true');
            $("#remove_l"+id).attr('hidden','true');
            $("#edit_l"+id).removeAttr('hidden');

            $("#tr_l"+id+" .val").removeAttr('hidden');
            $("#tr_l"+id+" .in").attr('hidden','true');
            $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', true);

            $("#divtype"+id).html($("#type"+id+" option:selected").text());
            $("#divport"+id).html($("#port"+id+" option:selected").text());
            $("#divchangetype"+id).html($("#changetype"+id+" option:selected").text());
            $("#divcarrier"+id).html($("#localcarrier"+id+" option:selected").text());
            $("#divcalculation"+id).html($("#calculationtype"+id+" option:selected").text());
            $("#divammount"+id).html($("#ammount"+id).val());
            $("#divcurrency"+id).html($("#localcurrency"+id+" option:selected").text());

        }
    });

}

$("#new").on("click", function() {

    var $template = $('#tclone');
    // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
    $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$("#newL").on("click", function() {

   var $template = $('#tclone2');
    
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
    $myClone.find("select").select2();
    $("#sample_editable_1").append($myClone);


});

$(document).on('click', '.remove', function () {
    $(this).closest('tr').remove();
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
                url: '../deleteLocalCharge/' + idval,
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

$('.m-select2-general').select2({
    placeholder: "Select an option"
});



