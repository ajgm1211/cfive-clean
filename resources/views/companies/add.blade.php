<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 07:15 PM
 */
?>


<!--begin::Form-->
{!! Form::open(['route' => 'companies.store','class' => 'form-group m-form__group','enctype'=>'multipart/form-data'])
!!}
<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('companies.partials.form_add_companies')
        <div class="form-group m-form__group">
            {!! Form::label('price_id', 'Price Level') !!}<br>
            {{ Form::select('price_id[]',$prices,null,['class'=>'custom-select form-control','id' => 'price_level_company','multiple'=>'true']) }}
        </div>
        <div class="form-group m-form__group">
            {!! Form::label('users_id', 'Associated User') !!}<br>
            {{ Form::select('users[]',$users,null,['class'=>'custom-select form-control','id' => 'users_company','multiple'=>'true']) }}
        </div>
        <div class="form-group m-form__group">
            {!! Form::label('logo', 'Logo (Max size 1 mb)') !!}
            <br>
            {!! Form::file('logo', null, ['placeholder' => 'Please upload a logo','class' => 'form-control m-input
            logo_input','required' => 'required','id'=>'logo']) !!}
            <div id="logo-error" class="hide"><b>Image size can not be bigger than 1 mb</b></div>
        </div>
        <hr>
        <div class="form-group m-form__group">
            <div class="row">
                <div class="col-12">
                {!! Form::label('extra_fields', 'Extra fields') !!}
                <button type="button" class="btn btn-primary btn-sm pull-right" onclick="addOriginCharge()">Add <i class="fa fa-plus"></i></button>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-6">
                    {!! Form::text('key_name[]', null, ['placeholder' => 'Please enter a key name','class' => 'form-control
                    m-input']) !!}
                </div>
                <div class="col-6">
                    {!! Form::text('key_value[]', null, ['placeholder' => 'Please enter a value','class' => 'form-control
                    m-input']) !!}
                </div>
            </div>
            <div class="row hide" id="hide_extra_field" style="margin-top:3px;">
                <div class="col-6">
                    {!! Form::text('key_name[]', null, ['placeholder' => 'Please enter a key name','class' => 'form-control
                    m-input']) !!}
                </div>
                <div class="col-6">
                    {!! Form::text('key_value[]', null, ['placeholder' => 'Please enter a value','class' => 'form-control
                    m-input']) !!}
                </div>
            </div>
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
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript">
</script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="{{asset('js/companies.js')}}"></script>