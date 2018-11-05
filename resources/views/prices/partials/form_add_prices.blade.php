<div class="form-group m-form__group">
    {!! Form::label('name', 'Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Please enter a name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('type_20', '20\'') !!}
    {!! Form::text('type_20', null, ['placeholder' => 'Please enter a price','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('type_40', '40\'') !!}
    {!! Form::text('type_40', null, ['placeholder' => 'Please enter a price','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('type_40_hc', '40\' HC') !!}
    {!! Form::text('type_40_hc', null, ['placeholder' => 'Please enter a price','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('description', 'Description') !!}
    {!! Form::textarea('description', null, ['placeholder' => 'Please enter a description','class' => 'form-control m-input','required' => 'required']) !!}
</div>