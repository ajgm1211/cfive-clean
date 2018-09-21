<div class="form-group m-form__group row">
  <div class="col-lg-3">
    {!! Form::label('name', 'Contract Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Contract Name','class' => 'form-control m-input','required' => 'required']) !!}
  </div>
  <div class="col-lg-3">
    {!! Form::label('number', 'Number') !!}
    {!! Form::text('number', null, ['placeholder' => 'Contract Number','class' => 'form-control m-input','required' => 'required']) !!}
  </div>
  <div class="col-lg-3">
    {!! Form::label('validation_expire', 'Validation') !!}
    {!! Form::text('validation_expire', $validation_expire, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
  </div>
  <!--
  <div class="col-lg-1">

      {!! Form::label('free_days', 'Free Days') !!}<br>
      {{ Form::number('free_days',null,['placeholder'=> '0','class'=>'custom-select form-control','id' => 'free_days','min' =>'0']) }}

  </div> -->
  <div class="col-lg-2">
    <div class="row">
      {!! Form::label('status', 'Status') !!}<br>
      {{ Form::select('status',['1' => 'Publish','2' => 'Draft'],null,['class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
    </div>
  </div>
</div>
