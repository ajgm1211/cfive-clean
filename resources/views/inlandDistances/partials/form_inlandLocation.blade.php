<div class="form-group m-form__group">
    {!! Form::label('Zip Code', 'Zip Code') !!}
    {!! Form::number('zip', null, ['placeholder' => 'Please enter  zip','class' => 'form-control ','required' => 'required','min' => '0']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('Address', 'Address') !!}
    {!! Form::text('address', null, ['placeholder' => 'Please enter  address','class' => 'form-control ','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('distance', 'Distance') !!}
    {!! Form::number('distance', null, ['placeholder' => 'Please enter  distance','class' => 'form-control ','required' => 'required','min' => '0']) !!}
</div>

