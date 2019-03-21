<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 08:24 PM
 */
?>



<!--begin::Form-->
{{ Form::model($company, array('route' => array('companies.update', $company->id), 'method' => 'PUT','enctype'=>'multipart/form-data')) }}
<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('companies.partials.form_add_companies')
        <div class="form-group m-form__group">
            {!! Form::label('price_id', 'Price Level') !!}<br>
            {{ Form::select('price_id[]',$prices,@$company->company_price->price_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'm_select2-edit-company','multiple'=>true]) }}
        </div>
        <div class="form-group m-form__group">
            {!! Form::label('users_id', 'Associate User') !!}<br>
            {{ Form::select('users[]',$users,@$company->groupUserCompanies->pluck('user_id'),['class'=>'custom-select form-control','id' => 'users_company','multiple'=>'true']) }}
        </div>
        <div class="form-group m-form__group">
            {!! Form::label('logo', 'Logo (Max size 1 mb)') !!}
            <br>
            {!! Form::file('logo', null, ['placeholder' => 'Please upload a logo','class' => 'form-control m-input logo_input','required' => 'required','id'=>'logo']) !!}
            <div id="logo-error" class="hide"><b>Image size can not be bigger than 1 mb</b></div>
        </div>        
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <div class="m-form__actions m-form__actions">
        {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
    </div>
    <br>
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
<script src="{{asset('js/companies.js')}}"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

