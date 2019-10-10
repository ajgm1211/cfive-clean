<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            ID:
        </label>
        <input type="text" name="name" value="{{$groupsCmp->id}}" required="required" class="form-control" disabled id="id_group_status">
    </div>
    <div class="col-lg-2">
        <label for="number" class="form-control-label">
            N° Duplicateds:
        </label>
        <input type="text" name="number" value="{{$groupsCmp->n_global}}" required="required" class="form-control" disabled id="Company">
    </div>

</div>

<div class="form-group row">

    <div class="col-lg-12">
        {!! Form::label('Status', 'Status',["class"=>"form-control-label"]) !!}
        {{ Form::select('status',$status,$groupsCmp->status_alert_id,['id' => 'statusSelectMD','class'=>'m-select2-general  form-control ','style' => 'width:100%;']) }}
    </div>
</div>

<script>

    $('.m-select2-general').select2({

    });

    function SaveStatusModal(){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        var status_id   = $('#statusSelectMD').val();
        var alert_id    = $('#id_group_status').val();
        url='{!! route("change.status.alert.group",":id") !!}';
        url = url.replace(':id', alert_id);
        // $(this).closest('tr').remove();
        $.ajax({
            url:url,
            method:'post',
            data:{status_id:status_id},
            success: function(data){
                //alert(data.data + data.status);
                if(data.data == 1){
                    $('a#statusHrf'+alert_id).text(data.status);
                    $('a#statusHrf'+alert_id).css('color',data.color);
                    $('#statusSamp'+alert_id).css('color',data.color);
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
</script>