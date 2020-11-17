<div class="m-portlet__body">
    <div class="form-group m-form__group row"> 
        <div class="col-lg-3">
        </div>
        <div class="col-lg-5">
            {!! Form::label('Between', 'Between') !!}
            <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="between" type="text" value="Please enter validation date">

        </div>
    </div>
</div>  
<hr>

<div class="m-portlet__foot m-portlet__foot--fit" style="border-top:none;">
    <br>
    <div class="m-form__actions m-form__actions"  style="text-align:center">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        {!! Form::submit('Save', ['class'=> 'btn btn-primary btn-save__modal', 'onclick'=>'forwardRequets()']) !!}
        <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">Cancel</span>
        </button> 
    </div>
    <br>
</div>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>

<script>

    function forwardRequets(){
        $('#company-imp-modal').modal('hide');
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, send it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(function(result){
            if (result.value) {
                var url     = '{!! route("forward.request") !!}';
                var between = $('#m_daterangepicker_1').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    cache:false,
                    url:url,
                    method:'POST',
                    data:{between:between},
                    success: function(data){
                        if(data.success == 1){
                            swal(
                                'Request!',
                                'Your request is being processed.',
                                'success'
                            )
                        }else if(data.success == 2){
                            swal("Error!", "An internal error occurred!", "error");
                        }
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your request has not been processed :)',
                    'error'
                )
            }
        });
    }
</script>