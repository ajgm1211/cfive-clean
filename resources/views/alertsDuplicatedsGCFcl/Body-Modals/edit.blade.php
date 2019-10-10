<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            ID:
        </label>
        <input type="text" name="name" value="{{$alert->id}}" required="required" class="form-control" disabled id="id_alert_status">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Date:
        </label>
        <input type="text" name="number" value="{{$alert->date}}" required="required" class="form-control" disabled id="Number">
    </div>
    <div class="col-lg-2">
        <label for="number" class="form-control-label">
            N° Duplicateds:
        </label>
        <input type="text" name="number" value="{{$alert->n_duplicate}}" required="required" class="form-control" disabled id="Company">
    </div>

    <div class="col-lg-2">
        <label for="number" class="form-control-label">
            N° Companies:
        </label>
        <input type="text" name="number" value="{{$alert->n_company}}" required="required" class="form-control" disabled id="NumberMD">
    </div>
</div>
<div class="form-group row">

    <div class="col-lg-12">
        {!! Form::label('Status', 'Status',["class"=>"form-control-label"]) !!}
        {{ Form::select('status',$status,$alert->status_alert_id,['id' => 'statusSelectMD','class'=>'m-select2-general  form-control ','style' => 'width:100%;']) }}
    </div>
</div>

<script>

    $('.m-select2-general').select2({

    });

    function SaveStatusModal(){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        var status_id   = $('#statusSelectMD').val();
        var alert_id    = $('#id_alert_status').val();
        url='{!! route("change.status.alert.dp",":id") !!}';
        url = url.replace(':id', alert_id);
        // $(this).closest('tr').remove();
        $.ajax({
            url:url,
            method:'post',
            data:{status_id:status_id},
            success: function(data){
                alert(data);
                if(data == 1){
                    /*swal(
                        'Deleted!',
                        'Your Surcharge has been deleted.',
                        'success'
                    )*/
                }else if(data == 2){
                    //swal("Error!", "An internal error occurred!", "error");
                }
            }
        });

    }
</script>