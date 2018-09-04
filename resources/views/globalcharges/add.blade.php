<div class="m-portlet">

{!! Form::open(['route' => 'globalcharges.store','class' => 'form-group m-form__group']) !!}
  <div class="m-portlet__body">
    <div class="form-group m-form__group row">


      <div class="col-lg-4">
        <label>
          {!! Form::label('type', 'Type') !!}
        </label>
        {{ Form::select('type', $surcharge,null,['id' => 'type','class'=>'m-select2-general form-control ' ,'required' => 'true']) }}
      </div>
      <div class="col-lg-4">
        {!! Form::label('orig', 'Origin Port') !!}
        {{ Form::select('port_orig[]', $harbor,
        null,['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple' ,'required' => 'true']) }}
      </div>
      <div class="col-lg-4">
        {!! Form::label('dest', 'Destination Port') !!}

        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('port_dest[]', $harbor,
          null,['id' => 'port_dest','class'=>'m-select2-general form-control ','multiple' => 'multiple' ,'required' => 'true']) }}
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
        {{ Form::select('changetype',$typedestiny, null,['id' => 'changetype','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
      </div>
      <div class="col-lg-4">
        {!! Form::label('carrierL', 'Carrier') !!}
        <div class="m-input-icon m-input-icon--right">
           {{ Form::select('localcarrier[]', $carrier,null,['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple' ,'required' => 'true']) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        {!! Form::label('calculationt', 'Calculation Type') !!}
        <div class="m-input-icon m-input-icon--right">
               {{ Form::select('calculationtype', $calculationT,null,['id' => 'calculationtype','class'=>'m-select2-general form-control ' ,'required' => 'true']) }}
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
        {!! Form::label('ammountL', 'Ammount') !!}
        <div class="m-input-icon m-input-icon--right">
         {!! Form::text('ammount', null, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input' ,'required' => 'true']) !!}
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
                 {{ Form::select('localcurrency_id',$currency,null,['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'required' => 'true' ]) }}
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
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      {!! Form::submit('add', ['class'=> 'btn btn-primary']) !!}
      <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
      </button>
    </div>
  </div>
  {!! Form::close() !!}
</div>

<script>


  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });


</script>
