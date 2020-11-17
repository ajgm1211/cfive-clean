<div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-max-height="200">

    {!! Form::open(['route' => 'gcadm.update.dates.Array', 'method' => 'post','class' => 'form-group m-form__group','id' => 'frmSurcharges']) !!}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::label('validation_expire', 'Validation') !!}
                        {!! Form::text('validation_expire', null, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
                    </div>
                    <div class="col-lg-6">
                        {!! Form::label('data', 'Data:') !!}
                        <h5 >{{$count}} - Globalchargers selected</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group row">

            <div class="col-lg-12">
                <center>

                </center>
            </div>

            <div class="col-lg-12">

                @foreach($globals_id_array as $gb)
                <input type="hidden" name="idArray[]" value="{{$gb}}">
                @endforeach
                <style>
                    .scrollStyle
                    {
                        overflow-x:auto;
                    }
                </style>
            </div>
        </div>
    </div>  
    <br>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <br>
        <div class="m-form__actions m-form__actions">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
            <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
        <br>
    </div>
    <input type="hidden" name="company_user_id_selec" value="{{$company_user_id_selec}}">
    <input type="hidden" name="carrier_id_selec" value="{{$carrier_id_selec}}">
    <input type="hidden" name="reload_DT" value="{{$reload_DT}}">
    {!! Form::close() !!}

</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/globalcharges.js"></script>
<script>

    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });



    $(document).ready(function(){
        var id =[];
        $('input[name=array]').each(function(){
            id.push($(this).val());
        });
        // alert(id);

        $('#load').DataTable( {
            "scrollY":        "200px",
            "scrollCollapse": true,

            "paging":         false
        } ).columns.adjust().draw();

        setTimeout(function () {
            $('#load').DataTable().columns.adjust().draw();
        },200);
    });



</script>
