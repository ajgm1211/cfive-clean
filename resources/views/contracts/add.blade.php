@php
    $validation_expire = 'Please enter validation date';
@endphp
<div class="m-portlet">

    <!--begin::Form-->
    {!! Form::open(['route' => 'contracts.store','class' => 'form-group m-form__group']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('contracts.partials.form_contracts')
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Save', ['class'=> 'btn btn-primary btn-sm']) !!}
                <a class="btn btn-danger btn-sm" href="{{url()->previous()}}">
                    Cancel
                </a>
            </div>
            <br>
        </div>
    </div>
{!! Form::close() !!}
<!--end::Form-->
</div>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
