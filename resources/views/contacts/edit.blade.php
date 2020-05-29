<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 08:24 PM
 */
?>

<!--begin::Form-->
{{ Form::model($contact, array('route' => array('contacts.update', $contact->id), 'method' => 'PUT')) }}
<div class="m-portlet__body">
    <div class="m-form__section m-form__section--first">
        <div class="form-group m-form__group">
            @include('contacts.partials.form_add_contacts')
            <div class="form-group m-form__group">
                {!! Form::label('company_id', 'Company') !!}<span style="color:red">*</span><br>
                {{ Form::select('company_id',$companies,$contact->company->id,['class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
            </div>
        </div>
        <br>
        <hr>
        <div class="form-group m-form__group">
            <div class="row">
                <div class="col-12">
                    {!! Form::label('extra_fields', 'Extra fields') !!}
                    <button type="button" class="btn btn-primary btn-sm pull-right" onclick="addExtraField()">Add <i
                            class="fa fa-plus"></i></button>
                </div>
            </div>
            <br>
            @forelse(json_decode($contact->options, true) as $key=>$value)
                <div class="clone">
                    <div class="row">
                        <div class="col-6">
                            {!! Form::text('key_name[]', $key, ['placeholder' => 'Please enter a key name','class' =>
                            'form-control
                            m-input']) !!}
                        </div>
                        <div class="col-6">
                            <div class="input-group mb-3">
                                {!! Form::text('key_value[]', $value, ['placeholder' => 'Please enter a value','class' =>
                                'form-control
                                m-input']) !!}
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm deleter"><i class="fa fa-close"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="row">
                    <div class="col-6">
                        {!! Form::text('key_name[]', null, ['placeholder' => 'Please enter a key name','class' =>
                        'form-control
                        m-input']) !!}
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            {!! Form::text('key_value[]', null, ['placeholder' => 'Please enter a value','class' =>
                            'form-control
                            m-input']) !!}
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-sm deleter"><i class="fa fa-close"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
            <div class="hide" id="hide_extra_field">
                <div class="row" style="margin-top:3px;">
                    <div class="col-6">
                        {!! Form::text('key_name[]', null, ['placeholder' => 'Please enter a key name','class' =>
                        'form-control
                        m-input']) !!}
                    </div>
                    <div class="col-6">
                        <div class="input-group mb-3">
                            {!! Form::text('key_value[]', null, ['placeholder' => 'Please enter a value','class' =>
                            'form-control
                            m-input']) !!}
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-sm deleter"><i class="fa fa-close"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
            {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
        </div>
        <br>
    </div>
</div>
{!! Form::close() !!}
<!--end::Form-->

<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="{{asset('js/contacts.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

