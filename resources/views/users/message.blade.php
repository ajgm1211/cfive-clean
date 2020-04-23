


<!--begin::Form-->
{!! Form::open(['route' => ['delete-user', $userid],'method' => 'PUT']) !!}

@if(isset($users) && $users->count()>0)
    <div class="m-form__section m-form__section--first">
        <div class="form-group m-form__group" style="font-size: 14px; font-weight: 500;">
            Would you like to transfer data before delete this user?
        </div>
    </div>

    <div class="m-form__section m-form__section--first">
        <div class="form-group m-form__group" style="font-size: 14px;">
            {{ Form::select('user_id',@$users,null,['class'=>'m-select2-general form-control']) }}
        </div>
    </div>

    <div class="m-form__actions m-form__actions">
        {!! Form::submit('Transfer data and delete user', ['class'=> 'btn btn-primary']) !!}
    </div>

    <br>

    <div class="m-form__section m-form__section--first">
        <div class="form-group m-form__group" style="font-size: 14px; font-weight: bold;">
            OR
        </div>
    </div>
@endif

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group" style="font-size: 14px; font-weight: 500;">
        If you are sure you want to delete this user and all associated information press the button below.
    </div>
</div>

<div class="m-form__actions m-form__actions">
    {!! Form::submit('Delete user and data', ['class'=> 'btn btn-danger']) !!}
</div>

<br>

{!! Form::close() !!}
<!--end::Form-->


