<div class="form-group m-form__group">
    {!! Form::text('name', null, ['placeholder' => 'Please enter the term name','class' => 'form-control m-input','required' => 'required']) !!}
    
</div>

<div class="form-group m-form__group">
    {!! Form::text('port', null, ['placeholder' => 'Please enter the port name','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::textarea('import', null, ['placeholder' => 'Please enter your  import text','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::textarea('export', null, ['placeholder' => 'Please enter your export text','class' => 'form-control m-input','required' => 'required']) !!}
</div>