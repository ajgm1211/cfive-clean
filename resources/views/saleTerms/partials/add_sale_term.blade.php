<div class="form-group m-form__group">
    {!! Form::label('name', 'Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Please enter sale term name','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('description', 'Description') !!}
    {!! Form::text('description', null, ['placeholder' => 'Please enter a description','class' => 'form-control m-input','required' => 'required']) !!}
</div>