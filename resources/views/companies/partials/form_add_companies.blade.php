<div class="form-group m-form__group">
    {!! Form::label('business_name', 'Business Name') !!}
    {!! Form::text('business_name', null, ['placeholder' => 'Please enter a business name','class' => 'form-control m-input business_name_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('phone', 'Phone') !!}
    {!! Form::text('phone', null, ['placeholder' => 'Please enter phone number','class' => 'form-control m-input phone_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::email('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input email_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('tax_number', 'Tax number') !!}
    {!! Form::text('tax_number', null, ['placeholder' => 'Please enter a tax number','class' => 'form-control m-input tax_number_input']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('address', 'Address') !!}
    {!! Form::textarea('address', null, ['placeholder' => 'Please enter a address','class' => 'form-control m-input address_input','required' => 'required','rows'=>1]) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('payment_conditions', 'Payment Conditions') !!}
    {!! Form::textarea('payment_conditions', null, ['placeholder' => 'Please enter payment conditions','class' => 'form-control m-input address_input','rows'=>1]) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('pdf_language', 'PDF Language') !!}
    {{ Form::select('pdf_language',[1=>'English',2=>'Spanish',3=>'Portuguese'],null,['class'=>'custom-select form-control','id' => 'pdf_language']) }}
</div>
