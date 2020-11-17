<script src="/js/globalcharges.js"></script>
@if(!$globalcharges->globalcharportlcl->isEmpty())
  @php
  $portRadio = true; 
  $countryRadio = false; 
  @endphp
  <script>
    activarCountry('divport');
  </script>
@endif
@if(!$globalcharges->globalcharcountrylcl->isEmpty())
  @php
  $countryRadio = true; 
  $portRadio = false; 
  @endphp
  <script>
    activarCountry('divcountry');
  </script>
@endif

<div class="m-portlet">

  {{ Form::model($globalcharges, array('route' => array('update.globalcharge.modal.lcl', $globalcharges->id), 'method' => 'PUT', 'id' => 'frmSurcharges')) }}
  <div class="m-portlet__body">
    <div class="form-group m-form__group row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-4">
            <label>
              {!! Form::label('Type Route', 'Type Route / Good') !!}
            </label>
            <div class="m-radio-inline">
              <label class="m-radio">
                {{ Form::radio('typeroute', 'port', $portRadio ,['id' => 'rdrouteP' , 'onclick' => 'activarCountry(\'divport\')' ]) }} Port
                <span></span>
              </label>
              <label class="m-radio">
                {{ Form::radio('typeroute', 'country', $countryRadio ,['id' => 'rdrouteC' , 'onclick' => 'activarCountry(\'divcountry\')' ]) }} Country
                <span></span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <label>
          {!! Form::label('type', 'Type') !!}
        </label>
        {{ Form::select('surcharge_id', $surcharge,$globalcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ']) }}
      </div>
      <div class="col-lg-4">
        <div class="divport" >
          {!! Form::label('orig', 'Origin Port') !!}
          {{ Form::select('port_orig[]', $harbor,
          $globalcharges->globalcharportlcl->pluck('portOrig')->unique()->pluck('id'),['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple']) }}
        </div>
        <div class="divcountry" hidden="true">

          {!! Form::label('origC', 'Origin Country') !!}
          {{ Form::select('country_orig[]', $countries,
          $globalcharges->globalcharcountrylcl->pluck('countryOrig')->unique()->pluck('id'),['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple']) }}

        </div>
      </div>
      <div class="col-lg-4">
        <div class="divport" >
          {!! Form::label('dest', 'Destination Port') !!}
          <div class="m-input-icon m-input-icon--right">
            {{ Form::select('port_dest[]', $harbor,
            $globalcharges->globalcharportlcl->pluck('portDest')->unique()->pluck('id'),['id' => 'port_dest','class'=>'m-select2-general form-control ','multiple' => 'multiple']) }}
            <span class="m-input-icon__icon m-input-icon__icon--right">
              <span>
                <i class="la la-info-circle"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="divcountry" hidden="true" >
          {!! Form::label('destC', 'Destination Country') !!}
          {{ Form::select('country_dest[]',$countries,$globalcharges->globalcharcountrylcl->pluck('countryDest')->unique()->pluck('id'),[ 'id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ]) }}
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
          {{ Form::select('calculationtypelcl_id', $calculationT,$globalcharges->calculationtypelcl_id,['id' => 'calculationtype','class'=>'m-select2-general form-control ']) }}
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
          {{ Form::select('carrier_id[]', $carrier,$globalcharges->globalcharcarrierslcl->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
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
        {!! Form::label('minimum', 'Minimum') !!}
        <div class="m-input-icon m-input-icon--right">
          {!! Form::text('minimum', $globalcharges->minimum, ['id' => 'minimum','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}
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

