<span class="size-8px">Fields with <span style="color:red">*</span> are mandatory</span>
<hr>
<div class="form-group m-form__group">
    {!! Form::label('first_name', 'First Name') !!}<span style="color:red">*</span>
    {!! Form::text('first_name', null, ['placeholder' => 'Please enter your first name','class' => 'form-control m-input first_namec_input','required' => 'required', 'id'=>'firts_name_contact']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('last_name', 'Last Name') !!}<span style="color:red">*</span>
    {!! Form::text('last_name', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input last_namec_input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}<span style="color:red">*</span>
    {!! Form::email('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input emailc_input','required' => 'required']) !!}
</div>