
function display(id){

    $("#tr"+id+" select, #tr"+id+" input").prop('disabled', false);

    var twuenty = $("#spantwuenty"+id).html();
    $("#spantwuenty"+id).html("<input type='text' name = 'twuenty[]'  class='form-control m-input' id ='twuenty"+id+"' value ='"+twuenty+"' >");

    var forty = $("#spanforty"+id).html();
    $("#spanforty"+id).html("<input  name = 'forty[]'  type='text' class='form-control m-input' id ='forty"+id+"' value ='"+forty+"' >");

    var fortyhc = $("#spanfortyhc"+id).html();
    $("#spanfortyhc"+id).html("<input name = 'fortyhc[]' type='text' class='form-control m-input' id ='fortyhc"+id+"' value ='"+fortyhc+"' >");

    $("#save"+id).removeAttr('hidden');
    $("#cancel"+id).removeAttr('hidden');
    $("#edit"+id).attr('hidden','true');
}

function cancel(id){

    $("#tr"+id+" select, #tr"+id+" input").prop('disabled', true);

    var twuenty = $("#twuenty"+id).val();
    $("#spantwuenty"+id).html(twuenty);

    var forty = $("#forty"+id).val();
    $("#spanforty"+id).html(forty);

    var fortyhc = $("#fortyhc"+id).val();
    $("#spanfortyhc"+id).html(fortyhc);

    $("#save"+id).attr('hidden','true');
    $("#cancel"+id).attr('hidden','true');
    $("#edit"+id).removeAttr('hidden');

}

function save(id,idval){
    var twuenty = $("#twuenty"+id).val();
    var forty = $("#forty"+id).val();
    var fortyhc = $("#fortyhc"+id).val();

    $.ajax({
        type: 'GET',
        url: '../updateRate/' + idval,
        data: {
            'origin_port': $("#origin"+id).val(),
            'destiny_port': $("#destiny"+id).val(),
            'carrier_id': $("#carrier"+id).val(),
            'twuenty': twuenty,
            'forty': forty,
            'fortyhc': fortyhc
        },
        success: function(data) {
            $("#save"+id).attr('hidden','true');
            $("#cancel"+id).attr('hidden','true');
            $("#edit"+id).removeAttr('hidden');
            $("#tr"+id+" select, #tr"+id+" input").prop('disabled', true);
            $("#spantwuenty"+id).html(twuenty);
            $("#spanforty"+id).html(forty);
            $("#spanfortyhc"+id).html(fortyhc);

        }
    });

    //$("#tr"+id+" input, tr.statuscheck select, tr.statuscheck textarea").prop('disabled', false);

}

function display_l(id){

    $("#tr_l"+id+" select, #trl"+id+" input").prop('disabled', false);

    $("#save_l"+id).removeAttr('hidden');
    $("#cancel_l"+id).removeAttr('hidden');
    $("#edit_l"+id).attr('hidden','true');

    /*var twuenty = $("#spantwuenty"+id).html();
    $("#spantwuenty"+id).html("<input type='text' name = 'twuenty[]'  class='form-control m-input' id ='twuenty"+id+"' value ='"+twuenty+"' >");

    var forty = $("#spanforty"+id).html();
    $("#spanforty"+id).html("<input  name = 'forty[]'  type='text' class='form-control m-input' id ='forty"+id+"' value ='"+forty+"' >");

    var fortyhc = $("#spanfortyhc"+id).html();
    $("#spanfortyhc"+id).html("<input name = 'fortyhc[]' type='text' class='form-control m-input' id ='fortyhc"+id+"' value ='"+fortyhc+"' >");

    $("#save"+id).removeAttr('hidden');
    $("#cancel"+id).removeAttr('hidden');
    $("#edit"+id).attr('hidden','true');*/
}

function cancel_l(id){

    $("#tr_l"+id+" select, #tr"+id+" input").prop('disabled', true);
    $("#save_l"+id).attr('hidden','true');
    $("#cancel_l"+id).attr('hidden','true');
    $("#edit_l"+id).removeAttr('hidden');

    /*   var twuenty = $("#twuenty"+id).val();
    $("#spantwuenty"+id).html(twuenty);

    var forty = $("#forty"+id).val();
    $("#spanforty"+id).html(forty);

    var fortyhc = $("#fortyhc"+id).val();
    $("#spanfortyhc"+id).html(fortyhc);

    $("#save"+id).attr('hidden','true');
    $("#cancel"+id).attr('hidden','true');
    $("#edit"+id).removeAttr('hidden');*/

}

$("#new").on("click", function() {

    var $template = $('#tclone');
    // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
    $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$("#newL").on("click", function() {


    $("#tclone2").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_2");

});

$(document).on('click', '.remove', function () {
    $(this).closest('tr').remove();
});

