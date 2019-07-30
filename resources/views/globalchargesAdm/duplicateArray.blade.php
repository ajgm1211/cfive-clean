<div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-max-height="200">

    {!! Form::open(['route' => 'gcadm.store.array', 'method' => 'post','class' => 'form-group m-form__group']) !!}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        {!! Form::label('company_user', 'Company User') !!}
                        <div class="m-input-icon m-input-icon--right">
                            {{ Form::select('company_user_id',$company_users,null,['id' => 'company_user_id','class'=>'m-select2-general form-control' ,'required' => 'true' ]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group row">
            
            <div class="col-lg-12">
                <center>
                    <h5 >{{$count}} - Globalchargers selected</h5>
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
