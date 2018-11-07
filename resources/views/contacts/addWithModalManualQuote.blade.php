<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 07:15 PM
 */
?>

<!--begin::Form-->
{!! Form::open(['route' => 'contacts.store','class' => 'form-group m-form__group']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('contacts.partials.form_add_contacts')
        <div class="form-group m-form__group">
            {!! Form::label('company_id', 'Company') !!}<br>
            {{ Form::select('company_id',$companies,null,['placeholder' => 'Please choose a company','class'=>'custom-select form-control companyc_input','id' => 'm_select2_2_modal']) }}
        </div>
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        <button id= 'savecontactmanualquote' class="btn btn-primary" type="button" class="close " aria->
            <span aria-hidden="true">Save</span>
        </button>
    </div>
</div>
{!! Form::close() !!}
<!--end::Form-->

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

