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
    </div>
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
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

