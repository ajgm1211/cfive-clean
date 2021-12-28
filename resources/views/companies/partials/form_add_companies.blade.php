<span class="size-8px">Fields with <span style="color:red">*</span> are mandatory</span>
<hr>
<div class="form-group m-form__group">
    {!! Form::label('business_name', 'Business Name') !!}<span style="color:red">*</span>
    {!! Form::text('business_name', null, 
        ['pattern' => '[a-z0-9_ ]+', 
         'autocomplete' => 'off', 
         'placeholder' => 'Please enter a business name',
         'class' => 'form-control m-input business_name_input',
         'onkeypress' => 'return validateInputBusinessName(event)',
         'required' => 'required']) !!}
    <small id="passwordHelp" class="text-dark">
        You must use only lowercase letters. The use of special characters is not allowed.
    </small>   
</div>

<div style="display:none;" class="text-danger" id="companyMessage">
</div>
<div style="display:none; padding-bottom:9px; border" id="similarityList">
</div>
<hr>
<div class="form-group m-form__group">
    {!! Form::label('phone', 'Phone') !!}
    {!! Form::text('phone', null, ['placeholder' => 'Please enter phone number','class' => 'form-control m-input phone_input']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::email('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input email_input']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('tax_number', 'Tax number') !!}
    {!! Form::text('tax_number', null, ['placeholder' => 'Please enter a tax number','class' => 'form-control m-input tax_number_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('address', 'Address') !!}
    {!! Form::textarea('address', null, ['placeholder' => 'Please enter a address','class' => 'form-control m-input address_input','rows'=>1]) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('pdf_language', 'PDF Language') !!}
    {{ Form::select('pdf_language',['0'=>'Choose a language','english'=>'English','spanish'=>'Spanish','portuguese'=>'Portuguese'],null,['class'=>'custom-select form-control','id' => 'pdf_language']) }}
</div>
