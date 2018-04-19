
function display(id){

    $("#tr"+id+" select, #tr"+id+" input").prop('disabled', false);

    $("#save"+id).removeAttr('hidden');
    $("#cancel"+id).removeAttr('hidden');
    $("#edit"+id).attr('hidden','true');
}

function cancel(id){

    $("#tr"+id+" select, #tr"+id+" input").prop('disabled', true);

    $("#save"+id).attr('hidden','true');
    $("#cancel"+id).attr('hidden','true');
    $("#edit"+id).removeAttr('hidden');


}

function save(){

    var origin = $("#twuenty"+id).val();

    alert(origin);
    /*  $.ajax({
                type: 'PUT',
                url: 'posts/' + id,
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': $("#id_edit").val(),
                    'title': $('#title_edit').val(),
                    'content': $('#content_edit').val()
                },
                success: function(data) {
                    $('.errorTitle').addClass('hidden');
                    $('.errorContent').addClass('hidden');
                });*/

    //$("#tr"+id+" input, tr.statuscheck select, tr.statuscheck textarea").prop('disabled', false);

}