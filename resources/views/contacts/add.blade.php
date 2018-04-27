<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 07:15 PM
 */
?>

<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::open(['route' => 'contacts.store','class' => 'form-group m-form__group']) !!}
        <div class="m-portlet__body">
            <div class="m-form__section m-form__section--first">
                <div class="form-group m-form__group">
                    @include('contacts.partials.form_add_contacts')
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions">
                    {!! Form::submit('Save', ['class'=> 'btn btn-primary  btn-sm']) !!}
                    <button class="btn btn-success btn-sm" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">Cancel</span>
                    </button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
<!--end::Form-->
</div>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

