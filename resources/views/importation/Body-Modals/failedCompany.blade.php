
<div class="m-portlet">
  <!--begin::Form-->
  {{ Form::model($detalle, array('route' => array('Update.RatesD.For.Contracts', $detalle['id]), 'method' => 'get', 'id' => 'frmFCompany')) }}


  <div class="m-portlet__body">
    <div class="form-group m-form__group row"> 
      <div class="col-lg-4">
        {!! Form::label('businessnamelb', 'Business Name) !!}
        {{ Form::text('businessname', $detalle['businessname'],['id' => 'm_businessname_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

      <div class="col-lg-4">
        {!! Form::label('phonelb', 'Phone') !!}
        {{ Form::text('phone', $detalle['phone'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

      <div class="col-lg-4">
        {!! Form::label('addresslb', 'Address') !!}
        {{ Form::text('address', $detalle['address'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
    </div>
    <div class="form-group m-form__group row">


      <div class="col-lg-4">
        {!! Form::label('email', 'Email') !!}
        {{ Form::text('email', $detalle['email'],['id' => 'm_email_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>
      <div class="col-lg-4">
        {!! Form::label('compnyuserlb', 'compnyuser') !!}
        {{ Form::text('compnyuser', $detalle['compnyuser'],['id' => 'm_compnyuser_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

      <div class="col-lg-4">
        {!! Form::label('taxnumberlb', 'Tax Number') !!}
        {{ Form::text('taxnumber', $detalle['taxnumber'],['id' => 'm_taxnumber_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
      </div>

    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">

        {!! Form::label('fortynor', '40\'NOR ') !!}
        {!! Form::number('fortynor', $rates->fortynor, ['id' => 'fortynor','placeholder' => '40\'NOR','class' => 'form-control ','required']) !!}

      </div>
      <div class="col-lg-4">

        {!! Form::label('fortyfive', '45\'') !!}
        {!! Form::number('fortyfive', $rates->fortyfive, ['id' => 'fortyfive','placeholder' => '45\'','class' => 'form-control ','required']) !!}

      </div>
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
  <input type="hidden" value="{{$rates['contract_id']}}" name="contract_id" id="contract_id" />


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
