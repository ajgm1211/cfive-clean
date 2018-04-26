<div class="form-group m-form__group">
    {!! Form::label('business_name', 'Business Name') !!}
    {!! Form::text('business_name', null, ['placeholder' => 'Please enter a business name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('phone', 'Phone') !!}
    {!! Form::text('phone', null, ['placeholder' => 'Please enter phone number','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::email('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('address', 'Address') !!}
    {!! Form::textarea('address', null, ['placeholder' => 'Please enter a address','class' => 'form-control m-input','required' => 'required']) !!}
</div>






