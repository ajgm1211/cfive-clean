<div class="m-portlet__body">
  <div class="form-group m-form__group row">
    <div class="col-lg-3">
      {!! Form::label('name', 'Provider') !!}
      {!! Form::text('name', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']) !!}
    </div>
    <div class="col-lg-3">
      {!! Form::label('ports', 'Port') !!}
      {{ Form::select('irelandports[]', $harbor,null,['class'=>'m-select2-general form-control port','multiple' => 'multiple','required' => 'required' ]) }}
    </div>
    <div class="col-lg-3">
      {!! Form::label('validation_expire', 'Validity') !!}
      {!! Form::text('validation_expire', $validation_expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
    </div>
    <div class="col-lg-3">

      {!! Form::label('change', 'Charge Type') !!}<br>
      {{ Form::select('status',['1' => 'Export','2' => 'Import','3' => 'All'],null,['class'=>'m-select2-general form-control']) }}
    </div>
  </div>
  <div class="form-group m-form__group row">
    <div class="col-lg-3">
      {!! Form::label('KM 20', 'Charge for 20') !!}
      {!! Form::number('km_20',0, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0']) !!}
    </div>
    <div class="col-lg-3">
      {!! Form::label('KM 40', 'Charge for 40') !!}
      {!! Form::number('km_40',0, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0']) !!}
    </div>
    <div class="col-lg-3">
      {!! Form::label('KM 40 HC', 'Charge for 40HC') !!}
      {!! Form::number('km_40hc',0, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0' ]) !!}
    </div>
    <div class="col-lg-3">
      <label>Company Restriction</label>
      <div class="form-group m-form__group align-items-center">
        {{ Form::select('companies[]',$companies,null,['multiple','class'=>'m-select2-general','id' => 'm-select2-company']) }}
      </div>
    </div>
  </div>
  <div class="form-group m-form__group row">
    <div class="col-lg-3">
      {!! Form::label('Charge Currency', 'Charge Currency') !!}
      {{ Form::select('chargecurrencykm',$currency,$currency_cfg->id,['class'=>'custom-select form-control','id' => '']) }}
    </div>
  </div>


</div>

