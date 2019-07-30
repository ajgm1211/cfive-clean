

<!--begin::Form-->
{{ Form::open(['route' => 'CarrierImportation.store', 'method' => 'POST']) }}


<div class="m-portlet__body">
    <div class="form-group m-form__group row"> 
        <div class="col-lg-7">
            {!! Form::label('Name', 'Name') !!}
            {{ Form::text('name',null,['id' => 'surcharger','class'=>' form-control']) }}

        </div>
        <div class="col-lg-4">
            {!! Form::label('status', 'Status') !!}
            {{ Form::select('status',[ 1 => 'Active', 0 => 'Inactive'],1,['id' => 'carrier','class'=>'m-select2-general form-control']) }}

        </div>
    </div>
    <div class="form-group m-form__group row"> 
        <div class="col-lg-7">
            {!! Form::label('Carriers', 'Carriers') !!}
            <div class="m-input-icon m-input-icon--right">
                {{ Form::select('carrier_id[]',$carriers,null,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;','multiple','multiple']) }}
            </div>

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
