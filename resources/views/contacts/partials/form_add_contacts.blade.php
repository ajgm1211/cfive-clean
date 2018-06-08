<div class="form-group m-form__group">
    {!! Form::label('first_name', 'First Name') !!}
    {!! Form::text('first_name', null, ['placeholder' => 'Please enter your first name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('last_name', 'Last Name') !!}
    {!! Form::text('last_name', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::email('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('phone', 'Phone') !!}
    {!! Form::text('phone', null, ['placeholder' => 'Please enter a phone','class' => 'form-control m-input','required' => 'required']) !!}
</div>