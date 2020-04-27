<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" value="{{$requests->namecontract}}" required="required" class="form-control" disabled id="NameMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Number:
        </label>
        <input type="text" name="number" value="{{$requests->numbercontract}}" required="required" class="form-control" disabled id="Number">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Company User:
        </label>
        <input type="text" name="number" value="{{$requests->companyuser->name}}" required="required" class="form-control" disabled id="Company">
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Id:
        </label>
        <input type="text" name="number" value="{{$requests->id}}" required="required" class="form-control" disabled id="NumberMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Date:
        </label>
        <input type="text" name="number" value="{{$requests->created}}" required="required" class="form-control" disabled id="NumberMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Username Load:
        </label>
        <input type="text" name="number" value="{{$requests->username_load}}" required="required" class="form-control" disabled id="NumberMD">
    </div>
    <input type="hidden" id="idContract" value="{{$requests->id}}"/>
</div>
<div class="form-group row">

    <div class="col-lg-12">
        {!! Form::label('Status', 'Status',["class"=>"form-control-label"]) !!}
        {{ Form::select('status',$status_arr,$requests->status,['id' => 'statusSelectMD','class'=>'m-select2-general  form-control ','style' => 'width:100%;']) }}
    </div>
</div>

<script>

    $('.m-select2-general').select2({

    });

    function SaveStatusModal(){
        //$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

        var status_id   = $('#statusSelectMD').val();
        var idContract    = $('#idContract').val();
        url='{!! route("request.fcl.status") !!}';
        $.ajax({
            url:url,
            method:'get',
            data:{id:idContract,status:status_id},
            success: function(data){
                //alert(data.data + data.status);
                console.log(data);
                if(data.data == 1){
                    $('a#statusHrf'+idContract).text(data.status);
                    if(data.request.time_total == null){
                        $('#timeElapsed'+idContract).text(' ------------------ ');
                    }else {
                        $('#timeElapsed'+idContract).text(data.request.time_total);						
                    }
                    $('#userLoad'+idContract).text(data.request.username_load);
                    $('a#statusHrf'+idContract).css('color',data.color);
                    $('#statusSamp'+idContract).css('color',data.color);
                    $('#changeStatus').modal('hide');
                    if(data.status == 'Done'){
                        $('#statusHiden'+idContract).removeAttr('hidden');
                    } else {
                        $('#statusHiden'+idContract).attr('hidden','hidden');                        
                    }

                    if(data.status != 'Pending'){
                        $('.PrCHidden'+idContract).removeAttr('hidden');
                    } else {
                        $('.PrCHidden'+idContract).attr('hidden','hidden');                        
                    }
                    //swal('Deleted!','Your Status has been changed.','success');
                    toastr.success("Your Status has been changed. ID: "+data.request.id+" - "+data.request.namecontract, "Status. ID: "+data.request.id);
                }else if(data.data == 2){
                    //swal("Error!", "An internal error occurred!", "error");
                    toastr.success("An internal error occurred!", "Error!");
                }
            }
        });

    }

</script>