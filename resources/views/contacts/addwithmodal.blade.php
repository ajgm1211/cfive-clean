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
        @include('contacts.partials.form_add_contacts_modal')
        <div class="form-group m-form__group">
            {!! Form::label('company_id', 'Company') !!}<span style="color:red">*</span><br>
            <select class="company_dropdown companyc_input form-control"></select>
        </div>
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        <button id='savecontact' class="btn btn-primary" type="button" class="close " aria->
            <span aria-hidden="true">Save</span>
        </button>
    </div>
</div>
{!! Form::close() !!}
<!--end::Form-->

<script>
    $('.company_dropdown').select2({
        placeholder: "Select an option",
        minimumInputLength: 2,
        ajax: {
            url: '/companies/search',
            dataType: 'json',
            data: function(params) {
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
        }
    });
</script>