/*
    function SaveStatusModal(){
        var id = $('#idContract').val();
        var status = $('#statusSelectMD').val();
        
        $.ajax({
            url:'/Requests/RequestStatus',
            method:'get',
            data:{id:id,status:status},
            success: function(data){
                console.log(data);
                if(data.status == 1){
                    swal("Good job!", "Updated The Status!", "success");
                    var posicion = $('#posicionval').val();
                    $('#thstatus'+posicion).text(data.data);
                   location.reload();
                } else {
                    swal("Error!", "An error occurred!", "error");
                }
            }
        })
    }*/

function SaveStatusModal(){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	
        var status_id   = $('#statusSelectMD').val();
        var idContract    = $('#idContract').val();
        url='{!! route("Request.status") !!}';
        //url = url.replace(':id', alert_id);
        // $(this).closest('tr').remove();
        $.ajax({
            url:url,
            method:'post',
            data:{id:id,status:status_id},
            success: function(data){
                //alert(data.data + data.status);
                if(data.data == 1){
                    $('a#statusHrf'+idContract).text(data.status);
                    $('a#statusHrf'+idContract).css('color',data.color);
                    $('#statusSamp'+idContract).css('color',data.color);
                    swal(
                        'Deleted!',
                        'Your Status has been changed.',
                        'success'
                    )
                }else if(data.data == 2){
                    swal("Error!", "An internal error occurred!", "error");
                }
            }
        });

    }