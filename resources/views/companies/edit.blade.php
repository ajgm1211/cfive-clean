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
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

