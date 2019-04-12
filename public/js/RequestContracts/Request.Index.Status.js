
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
    }