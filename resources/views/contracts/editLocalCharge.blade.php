
<div class="m-portlet">






  <!--begin::Form-->
  {{ Form::model($localcharges, array('route' => array('update-local-charge', $localcharges->id), 'method' => 'PUT', 'id' => 'frmSurcharges')) }}
  <div class="m-portlet__body">
    <div class="m-form__section m-form__section--first">
      <div class="form-group m-form__group">
        {!! Form::label('type', 'Type') !!}
        {{ Form::select('surcharge_id', $surcharge,$localcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ','style' => 'width:100%;']) }}
      </div>
      <div class="form-group m-form__group">
        {!! Form::label('orig', 'Origin Port') !!}
        {{ Form::select('port_origlocal[]', $harbor,$localcharges->localcharports->pluck('port_orig'),['id' => 'portOrig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;']) }}
      </div>
      <div class="form-group m-form__group">
        {!! Form::label('dest', 'Destination Port') !!}
        {{ Form::select('port_destlocal[]', $harbor,$localcharges->localcharports->pluck('port_dest'),['id' => 'portDest','class'=>'m-select2-general  form-control ','multiple' => 'multiple','style' => 'width:100%;']) }}
      </div>
      <div class="form-group m-form__group">
        {!! Form::label('typed', 'Destination type') !!}
        {{ Form::select('changetype',$typedestiny, $localcharges->typedestiny_id,['id' => 'changetype','class'=>'m-select2-general form-control','style' => 'width:100%;']) }}
      </div>

      <div class="form-group m-form__group">
        {!! Form::label('carrierL', 'Carrier') !!}
        {{ Form::select('carrier_id[]', $carrier,$localcharges->localcharcarriers->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
      </div>
      <div class="form-group m-form__group">
        {!! Form::label('calculationt', 'Calculation Type') !!}
        {{ Form::select('calculationtype_id', $calculationT,$localcharges->calculationtype_id,['id' => 'calculationtype','class'=>'m-select2-general form-control ','style' => 'width:80%;']) }}

      </div>
      <div class="form-group m-form__group">
        {!! Form::label('ammountL', 'Ammount') !!}
        {!! Form::text('ammount', $localcharges->ammount, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}
      </div> 

      <div class="form-group m-form__group">
        {!! Form::label('currencyl', 'Currency') !!}
        {{ Form::select('currency_id', $currency,$localcharges->currency_id,['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
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
  </div>
  {!! Form::close() !!}
  <!--end::Form-->
</div>

<script>
  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });

  $('#originRate').select2({
    placeholder: "Select an option"
  });

</script>