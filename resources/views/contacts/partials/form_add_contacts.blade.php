<div class="form-group m-form__group">
    {!! Form::label('first_name', 'First Name') !!}
    {!! Form::text('first_name', null, ['placeholder' => 'Please enter your first name','class' => 'form-control m-input first_namec_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('last_name', 'Last Name') !!}
    {!! Form::text('last_name', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input last_namec_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::email('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input emailc_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('phone', 'Phone') !!}
    {!! Form::text('phone', null, ['placeholder' => 'Please enter a phone','class' => 'form-control m-input phonec_input','required' => 'required']) !!}
</div>