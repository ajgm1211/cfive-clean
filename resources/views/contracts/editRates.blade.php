
<div class="m-portlet">
  <!--begin::Form-->
  {{ Form::model($rates, array('route' => array('update-rates', $rates->id), 'method' => 'PUT', 'id' => 'frmRates')) }}
  <div class="m-portlet__body">
    <div class="m-form__section m-form__section--first">
      <div class="form-group m-form__group">
        {!! Form::label('origin_port', 'Origin Port') !!}
        {{ Form::select('origin_id', $harbor,$rates->port_origin->id,['id' => 'sale_term_id','class'=>'form-control ', 'style' => 'width:100%;']) }} 
      </div>
      <div class="form-group m-form__group">
        {!! Form::label('destination_port', 'Destination Port') !!}
        {{ Form::select('destiny_id', $harbor,$rates->port_destiny->id,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
      </div>
      <div class="form-group m-form__group">
        {!! Form::label('carrier', 'Carrier') !!}
        {{ Form::select('carrier_id', $carrier,$rates->carrier->id,['id' => 'carrier','class'=>'m-select2-general form-control']) }}
      </div>
      <div class="form-group m-form__group">
        {!! Form::label('twuenty', '20 \' ') !!}
        {!! Form::text('twuenty', $rates->twuenty, ['id' => 'twuenty','placeholder' => 'Please enter the 20','class' => 'form-control m-input' ]) !!} 
      </div>

      <div class="form-group m-form__group">
        {!! Form::label('forty', '40  \' ') !!}
        {!! Form::text('forty', $rates->forty, ['id' => 'forty','placeholder' => 'Please enter the 40','class' => 'form-control m-input' ]) !!} 

      </div>
      <div class="form-group m-form__group">
        {!! Form::label('fortyhc', '40 HC \' ') !!}
        {!! Form::text('fortyhc', $rates->fortyhc, ['id' => 'fortyhc','placeholder' => '40HC','class' => 'form-control']) !!}

      </div>
      <div class="form-group m-form__group">
        {!! Form::label('currency', 'Currency') !!}
        {{ Form::select('currency_id', $currency,$rates->currency->id,['id' => 'currency','class'=>'m-select2-general form-control']) }}
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