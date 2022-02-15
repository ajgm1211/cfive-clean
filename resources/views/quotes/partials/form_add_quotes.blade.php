<div class="form-group m-form__group">
    {!! Form::label('company_id', 'Client') !!}<br>
    {{ Form::select('company_id',$companies,null,['placeholder' => 'Please choose a client','class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
</div>
<div class="form-group m-form__group">
    {!! Form::label('origin', 'Origin') !!}<br>
    {{ Form::select('origin',$countries,null,['placeholder' => 'Please choose an origin','class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
</div>
<div class="form-group m-form__group">
    {!! Form::label('destination', 'Destination') !!}<br>
    {{ Form::select('destination',$countries,null,['placeholder' => 'Please choose a destination','class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
</div>
<div class="form-group m-form__group">
    {!! Form::label('ammount', 'Ammount') !!}
    {!! Form::text('ammount', null, ['placeholder' => 'Please enter an ammount','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('status_id', 'Status') !!}<br>
    {{ Form::select('status_id',[1=>'Sent',2=>'Accepted'],null,['placeholder' => 'Please choose a status','class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
</div>