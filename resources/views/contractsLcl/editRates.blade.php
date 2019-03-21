
<div class="m-portlet">
  <!--begin::Form-->
  {{ Form::model($rates, array('route' => array('update-rates-lcl', $rates->id), 'method' => 'PUT', 'id' => 'frmRates')) }}


  <div class="m-portlet__body">
    <div class="form-group m-form__group row"> 
      <div class="col-lg-4">
        {!! Form::label('origin_port', 'Origin Port') !!}
        {{ Form::select('origin_port', $harbor,$rates->port_origin->id,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
      <div class="col-lg-4">
        {!! Form::label('destination_port', 'Destination Port') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('destiny_port', $harbor,$rates->port_destiny->id,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        {!! Form::label('carrier', 'Carrier') !!}
        {{ Form::select('carrier_id', $carrier,$rates->carrier->id,['id' => 'carrier','class'=>'m-select2-general form-control']) }}

      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        {!! Form::label(' W/M', ' W/M') !!}
        {!! Form::number('uom', $rates->uom, ['id' => 'uom','placeholder' => 'Please enter the Uom','class' => 'form-control m-input' ]) !!} 
      </div>


      <div class="col-lg-4">
        {!! Form::label('minimum', 'minimum') !!}
        {!! Form::number('minimum', $rates->minimum, ['id' => 'minimum','placeholder' => 'Please enter the Minimum','class' => 'form-control m-input' ]) !!} 

      </div>
      <div class="col-lg-4">

        {!! Form::label('currency', 'Currency') !!}

        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('currency_id', $currency,$rates->currency->id,['id' => 'currency','class'=>'m-select2-general form-control']) }}
        </div>
      </div>
    </div>
  </div>  
  <br>
  <br>
  <div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
      <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
      </button>
    </div>
    <br>
  </div>
</div>
{!! Form::close() !!}
<!--end::Form-->
</div>
<script>


  $('.m-select2-general').select2({

  });


</script>
