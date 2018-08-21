>
<!--begin::Form-->
{!! Form::open(['route' => 'companies.store','class' => 'form-group m-form__group']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('companies.partials.form_add_companies')
        <div class="form-group m-form__group">
            {!! Form::label('price_id', 'Price Level') !!}<br>
            {{ Form::select('price_id[]',$prices,null,['class'=>'custom-select form-control','id' => 'price_level_company','multiple'=>'true']) }}
        </div>
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        <button id= 'savecompany' class="btn btn-success" type="button" class="close " aria->
            <span aria-hidden="true">Save</span>
        </button>
        <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">Cancel</span>
        </button>
    </div>
</div>
{!! Form::close() !!}
<!--end::Form-->

<script>

    $('#price_level_company').select2({
        placeholder: "Select an option"
    });
</script>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

