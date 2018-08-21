


<!--begin::Form-->
{!! Form::open(['route' => ['delete-user', $userid],'method' => 'PUT']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group" style="font-size: 14px; font-weight: bold;">
        Are you sure you want to delete the user?
    </div>
</div>

<div class="m-form__actions m-form__actions">
    {!! Form::submit('Yes, I am sure', ['class'=> 'btn btn-primary']) !!}
</div>

<br>

{!! Form::close() !!}
<!--end::Form-->


