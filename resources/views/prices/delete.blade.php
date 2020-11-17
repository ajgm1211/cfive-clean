<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 08:45 PM
 */
?>

<div class="m-portlet">
    <!--begin::Form-->
    {{ Form::model($price, array('route' => array('prices.destroy', $price->id), 'method' => 'DELETE')) }}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                This register it will erased. Are you sure?
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Confirm', ['class'=> 'btn btn-danger']) !!}
                <button class="btn btn-warning" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">Cancel</span>
                </button>
            </div>
        </div>
    </div>
{!! Form::close() !!}
<!--end::Form-->
</div>
