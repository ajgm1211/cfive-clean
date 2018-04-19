
function display(id){

    $("#tr"+id+" select, #tr"+id+" input").prop('disabled', false);
    
    var forty = $("#spanforty"+id).html();
    $("#spanforty"+id).html("<input type='text' class='form-control m-input' id ='forty"+id+"' value ='"+forty+"' >");
    
    $("#save"+id).removeAttr('hidden');
    $("#cancel"+id).removeAttr('hidden');
    $("#edit"+id).attr('hidden','true');
}

function cancel(id){

    $("#tr"+id+" select, #tr"+id+" input").prop('disabled', true);

    var forty = $("#forty"+id).val();
    $("#spanforty"+id).html(forty);
    
    $("#save"+id).attr('hidden','true');
    $("#cancel"+id).attr('hidden','true');
    $("#edit"+id).removeAttr('hidden');


}

function save(id,idval){

    var origin = $("#twuenty"+id).val();


    $.ajax({
        type: 'GET',
        url: '../updateRate/' + idval,
        data: {
            'origin_port': $("#origin"+id).val(),
            'destiny_port': $("#destiny"+id).val(),
            'carrier_id': $("#carrier"+id).val(),
            'twuenty': $("#twuenty"+id).val()
        },
        success: function(data) {
            $("#save"+id).attr('hidden','true');
            $("#cancel"+id).attr('hidden','true');
            $("#edit"+id).removeAttr('hidden');
             $("#tr"+id+" select, #tr"+id+" input").prop('disabled', true);
        }
    });

    //$("#tr"+id+" input, tr.statuscheck select, tr.statuscheck textarea").prop('disabled', false);

}
