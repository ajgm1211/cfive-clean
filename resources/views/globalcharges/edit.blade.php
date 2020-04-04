
<script>
  activarCountry('{{ $activacion['act'] }}');
</script>


<div class="m-portlet" style="box-shadow:none">

  {{ Form::model($globalcharges, array('route' => array($route, $globalcharges->id), 'method' => 'PUT', 'id' => 'frmSurcharges')) }}
  <div class="m-portlet__body">
    <div class="form-group m-form__group row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-8">
            <label>
              <i class="la la-road icon__modal"  style="position: relative; bottom:-2px"></i>{!! Form::label('Type Route', 'Type Route') !!}
            </label>
            <div class="m-radio-inline">
              <label class="m-radio">
                {{ Form::radio('typeroute', 'port', $activacion['rdrouteP'] ,['id' => 'rdrouteP' , 'onclick' => 'activarCountry(\'divport\')' ]) }} Port
                <span></span>
              </label>
              @if ($route == 'update-global-charge')
                <label class="m-radio">
                  {{ Form::radio('typeroute', 'country', $activacion['rdrouteC'] ,['id' => 'rdrouteC' , 'onclick' => 'activarCountry(\'divcountry\')' ]) }} Country
                  <span></span>
                </label>
                <label class="m-radio">
                  {{ Form::radio('typeroute', 'portcountry', $activacion['rdroutePC'] ,['id' => 'rdroutePC' , 'onclick' => 'activarCountry(\'divportcountry\')' ]) }} Port to Country
                  <span></span>
                </label>
                <label class="m-radio">
                  {{ Form::radio('typeroute', 'countryport', $activacion['rdrouteCP'] ,['id' => 'rdrouteCP' , 'onclick' => 'activarCountry(\'divcountryport\')' ]) }}  Country to Port
                  <span></span>
                </label>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <label>
          <i class="la la-sitemap icon__modal"></i>{!! Form::label('type', 'Surcharges') !!}
        </label>
        {{ Form::select('surcharge_id', $surcharge,$globalcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ']) }}
      </div>
      <div class="col-lg-4">
        <div class="divport" >
          <i class="la la-anchor icon__modal"></i>{!! Form::label('orig', 'Origin Port') !!}
          {{ Form::select('port_orig[]', $harbor,
          $globalcharges->globalcharport->pluck('portOrig')->unique()->pluck('id'),['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple']) }}
        </div>
        @if ($route == 'update-global-charge')
          <div class="divcountry" hidden="true">

            <i class="la la-anchor icon__modal"></i> {!! Form::label('origC', 'Origin Country') !!}
            {{ Form::select('country_orig[]', $countries,
            $globalcharges->globalcharcountry->pluck('countryOrig')->unique()->pluck('id'),['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple']) }}

          </div>

          <div class="divportcountry" hidden="true">

            <i class="la la-anchor icon__modal"></i>{!! Form::label('origPC', 'Origin Port') !!}
            {{ Form::select('portcountry_orig[]', $harbor,$globalcharges->globalcharportcountry->pluck('portOrig')->unique()->pluck('id'),['id' => 'portcountry_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple' ]) }}

          </div>
          <div class="divcountryport" hidden="true">

            <i class="la la-anchor icon__modal"></i>{!! Form::label('origCP', 'Origin Country') !!}
            {{ Form::select('countryport_orig[]', $countries,$globalcharges->globalcharcountryport->pluck('countryOrig')->unique()->pluck('id'),['id' => 'countryport_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple' ]) }}

          </div>
        @endif
      </div>
      <div class="col-lg-4">
        <div class="divport" >
          <i class="la la-anchor icon__modal"></i>{!! Form::label('dest', 'Destination Port') !!}
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
        @if ($route == 'update-global-charge')
          <div class="divcountry" hidden="true" >
            <i class="la la-anchor icon__modal"></i>{!! Form::label('destC', 'Destination Country') !!}
            {{ Form::select('country_dest[]',$countries,$globalcharges->globalcharcountry->pluck('countryDest')->unique()->pluck('id'),[ 'id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ]) }}
          </div>
          <div class="divportcountry" hidden="true" >

            <i class="la la-anchor icon__modal"></i>{!! Form::label('destPC', 'Destination Country') !!}
            {{ Form::select('portcountry_dest[]',$countries,$globalcharges->globalcharportcountry->pluck('countryDest')->unique()->pluck('id'),[ 'id' => 'portcountry_dest','class'=>'m-select2-general form-control' ,'multiple' => 'multiple'   ]) }}

          </div>
          <div class="divcountryport" hidden="true" >
            <i class="la la-anchor icon__modal"></i>{!! Form::label('destCP', 'Destination Port') !!}
            {{ Form::select('countryport_dest[]', $harbor,$globalcharges->globalcharcountryport->pluck('portDest')->unique()->pluck('id'),['id' => 'countryport_dest','class'=>'m-select2-general form-control ','multiple' => 'multiple' ]) }}
          </div>
        @endif
      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <i class="la la-anchor icon__modal"></i>{!! Form::label('typed', 'Destination type') !!}
        {{ Form::select('changetype',$typedestiny, $globalcharges->typedestiny_id,['id' => 'changetype','class'=>'m-select2-general form-control']) }}
      </div>
      <div class="col-lg-4">
        <i class="la la-calendar icon__modal"></i>{!! Form::label('validation_expire', 'Validation') !!}
        {!! Form::text('validation_expire', $globalcharges->validation_expire, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
      </div>


      <div class="col-lg-4">
        <i class="la la-database icon__modal" style="transform: rotate(90deg); position: relative; bottom:-2px"></i> {!! Form::label('calculationt', 'Calculation Type') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('calculationtype_id', $calculationT,$globalcharges->calculationtype_id,['id' => 'calculationtype','class'=>'m-select2-general form-control ','required' => 'required']) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-map-marker"></i>
            </span>
          </span>
        </div>

      </div>
    </div>
    <div class="form-group m-form__group row">
      @if ($route == 'update-global-charge')
      <div class="col-lg-4">
        <i class="la la-ship icon__modal"></i> {!! Form::label('carrierL', 'Carrier') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('carrier_id[]', $carrier,$globalcharges->globalcharcarrier->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple','required' => 'required']) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>
      </div>
      @endif

      @if ($route == 'globalchargesapi.update')
      <div class="col-lg-4">
        <i class="la la-ship icon__modal"></i> {!! Form::label('provider', 'Provider') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('providers[]', $providers, $globalcharges->globalchargeprovider->pluck('provider')->unique()->pluck('id'),['id' => 'providers','class'=>'m-select2-general form-control','multiple' => 'multiple','required' => 'required']) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>
      </div>
      @endif

      <div class="col-lg-4">
        <i class="la la-sort-numeric-asc icon__modal"></i> {!! Form::label('ammountL', 'Amount') !!}
        <div class="m-input-icon m-input-icon--right">
          {!! Form::number('ammount', $amount, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','required','class' => 'form-control m-input','step'=>'0.01']) !!}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-bookmark-o"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        <i class="la la-dollar icon__modal"></i>{!! Form::label('currencyl', 'Currency') !!}
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

  <div class="m-portlet__foot m-portlet__foot--fit" style="border-top:none;">
    <div class="m-form__actions m-form__actions" style="text-align:center">
      {!! Form::submit('Update', ['class'=> 'btn btn-primary btn-save__modal']) !!}
      <!-- <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">Cancel</span>
</button> -->
    </div>
  </div>
  {!! Form::close() !!}
</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/globalcharges.js"></script>
<script>


  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });


</script>

