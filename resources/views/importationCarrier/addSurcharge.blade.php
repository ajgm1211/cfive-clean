

    <!--begin::Form-->
    {{ Form::open(['route' => 'surcherger.filtro.store', 'method' => 'POST']) }}


    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                {!! Form::label('Surcharger', 'Surcharger') !!}
                {{ Form::text('surcharger',null,['id' => 'surcharger','class'=>' form-control']) }}

            </div>
        </div>
    </div>  
    <br>
    <div class="m-portlet__foot m-portlet__foot--fit" style="border-top:none;">
        <br>
        <div class="m-form__actions m-form__actions"  style="text-align:center">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {!! Form::submit('Save', ['class'=> 'btn btn-primary btn-save__modal']) !!}
            <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button> 
        </div>
        <br>
    </div>
{!! Form::close() !!}
<!--end::Form-->
<script>


    $('.m-select2-general').select2({

    });


</script>
