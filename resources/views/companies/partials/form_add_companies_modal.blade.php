<span class="size-8px">Fields with <span style="color:red">*</span> are mandatory</span>
<hr>
<div class="form-group m-form__group">
    {!! Form::label('business_name', 'Business Name') !!}<span style="color:red">*</span>
    {!! Form::text('business_name', null, ['placeholder' => 'Please enter a business name','class' => 'form-control m-input business_name_input','required' => 'required']) !!}
</div>

<div class="form-group m-form__group">
    {!! Form::label('tax_number', 'Tax number') !!}<span style="color:red">*</span>
    {!! Form::text('tax_number', null, ['placeholder' => 'Please enter a tax number','class' => 'form-control m-input tax_number_input']) !!}
</div>


