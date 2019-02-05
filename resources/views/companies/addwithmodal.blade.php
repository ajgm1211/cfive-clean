<!--begin::Form-->
{!! Form::open(['route' => 'companies.store','class' => 'form-group m-form__group']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('companies.partials.form_add_companies_modal')
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        <button id= 'savecompany' class="btn btn-primary" type="button" class="close " aria->
            <span aria-hidden="true">Save</span>
        </button>
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
</script>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

