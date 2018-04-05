<div>
    {!! Form::label('name', 'First Name') !!}
    {!! Form::text('name', null, ['class' => 'form-control m-input','required' => 'required']) !!}
    <span class="m-form__help">
        Please enter your firts name
    </span>
</div>

<div class="form-group m-form__group">
    {!! Form::label('lastname', 'Last Name') !!}
    {!! Form::text('lastname', null, ['class' => 'form-control m-input','required' => 'required']) !!}
    <span class="m-form__help">
        Please enter your last name
    </span>
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::text('email', null, ['class' => 'form-control m-input','required' => 'required']) !!}

    <span class="m-form__help">
        We'll never share your email with anyone else
    </span>
</div>
<div class="form-group m-form__group">
    {!! Form::label('password', 'password') !!}
    {!! Form::text('password', null, ['class' => 'form-control m-input','required' => 'required']) !!}
    <span class="m-form__help">
        Please enter your password
    </span>
</div>
<div class="form-group m-form__group">

    <span class="m-form__help">
        Please select this rol
    </span>
</div>