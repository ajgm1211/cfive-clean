<div class="m-portlet__body">
  <div class="form-group m-form__group row">
    <div class="col-lg-2">
      {!! Form::label('name', 'Provider') !!}
      {!! Form::text('name', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']) !!}
    </div>
    <div class="col-lg-3">
      {!! Form::label('ports', 'Port') !!}
      {{ Form::select('irelandports[]', $harbor,null,['class'=>'m-select2-general form-control port','multiple' => 'multiple']) }}
    </div>
    <div class="col-lg-3">
      {!! Form::label('validation_expire', 'Validity') !!}
      {!! Form::text('validation_expire', $validation_expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
    </div>
    <div class="col-lg-2">

      {!! Form::label('change', 'Charge Type') !!}<br>
      {{ Form::select('status',['1' => 'Export','2' => 'Import','3' => 'All'],null,['class'=>'m-select2-general form-control']) }}


    </div>

    <div class="col-lg-2">
      <label>Company Restriction</label>
      <div class="form-group m-form__group align-items-center">
        {{ Form::select('companies[]',$companies,null,['multiple','class'=>'m-select2-general','id' => 'm-select2-company']) }}
      </div>
    </div>

  </div>
</div>

