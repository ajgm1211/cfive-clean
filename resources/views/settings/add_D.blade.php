{!! Form::open(['route' => 'settings.storeD','method' => 'POST' ]) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('settings.partials.delegations_form', array('type'=>'add'))

    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
{!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    </div>
    <br>
</div>
{!! Form::close() !!}