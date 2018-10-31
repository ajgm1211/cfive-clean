<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 07:15 PM
 */
?>


<!--begin::Form-->
{!! Form::open(['route' => 'companies.store','class' => 'form-group m-form__group','enctype'=>'multipart/form-data']) !!}
<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('companies.partials.form_add_companies')
        <div class="form-group m-form__group">
            {!! Form::label('price_id', 'Price Level') !!}<br>
            {{ Form::select('price_id[]',$prices,null,['class'=>'custom-select form-control','id' => 'price_level_company','multiple'=>'true']) }}
        </div>
        <div class="form-group m-form__group">
            {!! Form::label('users_id', 'Associate User') !!}<br>
            {{ Form::select('users[]',$users,null,['class'=>'custom-select form-control','id' => 'users_company','multiple'=>'true']) }}
        </div>
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    </div>
</div>
{!! Form::close() !!}
<!--end::Form-->
<script>
    $('#price_level_company').select2({
        placeholder: "Select an option"
    });
    $('#users_company').select2({
        placeholder: "Select an option"
    });
    $('#pdf_language').select2({
        placeholder: "Select an option"
    });
</script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

