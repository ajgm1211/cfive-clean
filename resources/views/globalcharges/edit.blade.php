<div class="m-portlet">

  {{ Form::model($globalcharges, array('route' => array('update-global-charge', $globalcharges->id), 'method' => 'PUT', 'id' => 'frmSurcharges')) }}
  <div class="m-portlet__body">
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <label>
          {!! Form::label('type', 'Type') !!}
        </label>
        {{ Form::select('surcharge_id', $surcharge,$globalcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ']) }}
      </div>
      <div class="col-lg-4">
        {!! Form::label('orig', 'Origin Port') !!}
        {{ Form::select('port_orig[]', $harbor,
        $globalcharges->globalcharport->pluck('portOrig')->unique()->pluck('id'),['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple']) }}
      </div>
      <div class="col-lg-4">
        {!! Form::label('dest', 'Destination Port') !!}

        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('port_dest[]', $harbor,
          $globalcharges->globalcharport->pluck('portDest')->unique()->pluck('id'),['id' => 'port_dest','class'=>'m-select2-general form-control ','multiple' => 'multiple']) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>


      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        {!! Form::label('typed', 'Destination type') !!}
        {{ Form::select('changetype',$typedestiny, $globalcharges->typedestiny_id,['id' => 'changetype','class'=>'m-select2-general form-control']) }}
      </div>
      <div class="col-lg-4">
        {!! Form::label('validation_expire', 'Validation') !!}
        {!! Form::text('validation_expire', $globalcharges->validation_expire, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
      </div>

    
    <div class="col-lg-4">
      {!! Form::label('calculationt', 'Calculation Type') !!}
      <div class="m-input-icon m-input-icon--right">
        {{ Form::select('calculationtype_id', $calculationT,$globalcharges->calculationtype_id,['id' => 'calculationtype','class'=>'m-select2-general form-control ']) }}
        <span class="m-input-icon__icon m-input-icon__icon--right">
          <span>
            <i class="la la-map-marker"></i>
          </span>
        </span>
      </div>

    </div>
  </div>
  <div class="form-group m-form__group row">
    <div class="col-lg-4">
      {!! Form::label('carrierL', 'Carrier') !!}
      <div class="m-input-icon m-input-icon--right">
        {{ Form::select('carrier_id[]', $carrier,$globalcharges->globalcharcarrier->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
        <span class="m-input-icon__icon m-input-icon__icon--right">
          <span>
            <i class="la la-info-circle"></i>
          </span>
        </span>
      </div>
    </div>

      <div class="col-lg-4">
        {!! Form::label('ammountL', 'Ammount') !!}
        <div class="m-input-icon m-input-icon--right">
          {!! Form::text('ammount', $globalcharges->ammount, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-bookmark-o"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        {!! Form::label('currencyl', 'Currency') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('currency_id', $currency,$globalcharges->currency_id,['id' => 'localcurrency','class'=>'m-select2-general form-control' ]) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-bookmark-o"></i>
            </span>
          </span>
        </div>

      </div>
    </div>
  </div>  
  <br>
  <hr>
  <div class="m-portlet__foot m-portlet__foot--fit">
    <div class="m-form__actions m-form__actions">
      {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
      <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
      </button>
    </div>
  </div>
  {!! Form::close() !!}
</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script>


  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });


</script>
