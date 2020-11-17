  function LoadModalStatus(id,posicion){
        var name   = $('#thnamec'+posicion).text();
        var number = $('#thnumc'+posicion).text();
        var status = $('#thstatus'+posicion).text();
        $('#idContract').attr('value',id);
        $('#posicionval').attr('value',posicion);

        $('#NameCon').text('Name Contract: '+name);
        $('#NumCon').text('Number Contract: '+number);
        if(status == 'Pending'){
            $('#Pending').attr('selected','selected');
        } else if(status == 'Processing'){
            $('#Processing').attr('selected','selected');
        } else if(status == 'Done'){
            $('#Done').attr('selected','selected');
        }
        $('#Loadstatus').modal();
    }

    function SaveStatusModal(){
        var id = $('#idContract').val();
        var status = $('#statusSelectMD').val();
        
        $.ajax({
            url:'/RequestsGlobalchargersLcl/RequestGCStatusLcl',
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