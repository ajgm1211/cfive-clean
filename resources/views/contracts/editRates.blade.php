
<div class="m-portlet">
  <!--begin::Form-->
  {{ Form::model($rates, array('route' => array('update-rates', $rates->id), 'method' => 'PUT', 'id' => 'frmRates')) }}


  <div class="m-portlet__body">
    <div class="form-group m-form__group row"> 
      <div class="col-lg-4">
        {!! Form::label('origin_port', 'Origin Port') !!}
        {{ Form::select('origin_id', $harbor,$rates->port_origin->id,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
      <div class="col-lg-4">
        {!! Form::label('destination_port', 'Destination Port') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('destiny_id', $harbor,$rates->port_destiny->id,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
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
        {!! Form::label('twuenty', '20 \' ') !!}
        {!! Form::number('twuenty', $rates->twuenty, ['id' => 'twuenty','placeholder' => 'Please enter the 20','class' => 'form-control m-input' ]) !!} 
      </div>


      <div class="col-lg-4">
        {!! Form::label('forty', '40  \' ') !!}
        {!! Form::number('forty', $rates->forty, ['id' => 'forty','placeholder' => 'Please enter the 40','class' => 'form-control m-input' ]) !!} 

      </div>
      <div class="col-lg-4">

        {!! Form::label('fortyhc', '40 HC \' ') !!}
        {!! Form::number('fortyhc', $rates->fortyhc, ['id' => 'fortyhc','placeholder' => '40HC','class' => 'form-control ']) !!}

      </div>

    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        {!! Form::label('currency', 'Currency') !!}

        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('currency_id', $currency,$rates->currency->id,['id' => 'currency','class'=>'m-select2-general form-control']) }}
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
</div>
{!! Form::close() !!}
<!--end::Form-->
</div>
<script>


  $('.m-select2-general').select2({

  });


</script>
