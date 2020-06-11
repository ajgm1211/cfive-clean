<div class="m-portlet">
  {!! Form::open(['route' => 'gcadm.store', 'method' => 'post','class' => 'form-group m-form__group','id' => 'frmSurcharges']) !!}
  <div class="m-portlet__body">
    <div class="form-group m-form__group row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-9">
            <label>
              {!! Form::label('Type Route', 'Type of route') !!}
            </label>
            <div class="m-radio-inline">
              <label class="m-radio">
                <input type="radio" id="rdrouteP" onclick="activarCountry('divport')" checked='true' name="typeroute" value="port"> Port
                <span></span>
              </label>
              <label class="m-radio">
                <input type="radio" id="rdrouteC" onclick="activarCountry('divcountry')"  name="typeroute" value="country"> Country
                <span></span>
              </label>
              <label class="m-radio">
                <input type="radio" id="rdroutePC" onclick="activarCountry('divportcountry')"  name="typeroute" value="portcountry"> Port to Country
                <span></span>
              </label>
              <label class="m-radio">
                <input type="radio" id="rdrouteCP" onclick="activarCountry('divcountryport')"  name="typeroute" value="countryport"> Country to Port
                <span></span>
              </label>
            </div>
          </div>
          <div class="col-lg-3">
            {!! Form::label('company_user', 'Company User') !!}
            <div class="m-input-icon m-input-icon--right">
              {{ Form::select('company_user_id',$company_users,null,['id' => 'company_user_id','class'=>'m-select2-general form-control' ,'required' => 'true' ]) }}
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
        {{ Form::select('type',[''=>'Select Option'],null,['id' => 'type','class'=>'m-select2-general form-control','required' => 'true']) }}
      </div>
      <div class="col-lg-4">
        <div class="divport" >
          {!! Form::label('orig', 'Origin Port') !!}
          {{ Form::select('port_orig[]', $harbor,
          null,['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple' ,'required' => 'true' ]) }}
        </div>
        <div class="divcountry" hidden="true">

          {!! Form::label('origC', 'Origin Country') !!}
          {{ Form::select('country_orig[]', $countries,
          null,['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple' ]) }}

        </div>
        <div class="divportcountry" hidden="true">

          <i class="la la-anchor icon__modal"></i>{!! Form::label('orig', 'Origin Port') !!}
          {{ Form::select('portcountry_orig[]', $harbor,
          null,['id' => 'portcountry_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple' ]) }}

        </div>
        <div class="divcountryport" hidden="true">

          <i class="la la-anchor icon__modal"></i>{!! Form::label('origC', 'Origin Country') !!}
          {{ Form::select('countryport_orig[]', $countries,
          null,['id' => 'countryport_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple' ]) }}

        </div>
      </div>			
      <div class="col-lg-4">
        <div class="divport" >
          {!! Form::label('dest', 'Destination Port') !!}
          <div class="m-input-icon m-input-icon--right">
            {{ Form::select('port_dest[]', $harbor,
            null,['id' => 'port_dest','class'=>'m-select2-general form-control ','multiple' => 'multiple','required' => 'true' ]) }}
            <span class="m-input-icon__icon m-input-icon__icon--right">
              <span>
                <i class="la la-info-circle"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="divcountry" hidden="true" >

          {!! Form::label('destC', 'Destination Country') !!}
          {{ Form::select('country_dest[]',$countries,null,[ 'id' => 'country_dest','class'=>'m-select2-general form-control' ,'multiple' => 'multiple'   ]) }}

        </div>
        <div class="divportcountry" hidden="true" >

          <i class="la la-anchor icon__modal"></i>{!! Form::label('destC', 'Destination Country') !!}
          {{ Form::select('portcountry_dest[]',$countries,null,[ 'id' => 'portcountry_dest','class'=>'m-select2-general form-control' ,'multiple' => 'multiple'   ]) }}

        </div>
        <div class="divcountryport" hidden="true" >

          <i class="la la-anchor icon__modal"></i>{!! Form::label('dest', 'Destination Port') !!}

          {{ Form::select('countryport_dest[]', $harbor,
          null,['id' => 'countryport_dest','class'=>'m-select2-general form-control ','multiple' => 'multiple' ]) }}

        </div>

      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        {!! Form::label('typed', 'Destination type') !!}
        {{ Form::select('changetype',$typedestiny, null,['id' => 'changetype','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
      </div>
      <div class="col-lg-4">
        {!! Form::label('validation_expire', 'Validation') !!}
        {!! Form::text('validation_expire', null, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
      </div>

      <div class="col-lg-4">
        {!! Form::label('calculationt', 'Calculation Type') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('calculationtype[]', $calculationT,null,['id' => 'calculationtype','class'=>'m-select2-general form-control ' ,'required' => 'true','multiple' => 'multiple']) }}
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
          {{ Form::select('localcarrier[]', $carrier,null,['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple' ,'required' => 'true']) }}
          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        {!! Form::label('currencyl', 'Currency') !!}
        <div class="m-input-icon m-input-icon--right">
          {{ Form::select('localcurrency_id',$currency,null,['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'required' => 'true' ]) }}

        </div>

      </div>
      <div class="col-lg-4">
        {!! Form::label('ammountL', 'Amount') !!}

        {!! Form::number('ammount', 0, ['id' => 'ammount','class' => 'form-control','step'=>'0.01' ,'required' => 'true']) !!}
      </div>
    </div>
  </div>  
  <br>
  <div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
      </button>
    </div>
    <br>
  </div>
  <input type="hidden" name="company_user_id_selec" value="{{$company_user_id_selec}}">
  <input type="hidden" name="carrier_id_selec" value="{{$carrier_id_selec}}">
  <input type="hidden" name="reload_DT" value="{{$reload_DT}}">
  {!! Form::close() !!}
</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/globalcharges.js"></script>

<script>

  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });

  $(document).ready(function(){
    var id = $('select[name=company_user_id]').val()
    url = '{{route("gcadm.typeCharge",":id")}}';
    url = url.replace(':id', id);
    $.get(url,function(response,status){
      if(response.length >= 1){
        $('#type').removeAttr('disabled','disabled');
      } else {
        $('#type').attr('disabled','disabled');
      }
      $('#type').empty();
      for(i=0;i < response.length; i++){
        $('#type').append("<option value='"+response[i].id+"'>"+response[i].name+"</option>");
      }
    })
  });

  $('#company_user_id').change(function(event){
    url = '{{route("gcadm.typeCharge",":id")}}';
    url = url.replace(':id', event.target.value);
    $.get(url,function(response,status){
      //console.log(response);
      if(response.length >= 1){
        $('#type').removeAttr('disabled','disabled');
      } else {
        $('#type').attr('disabled','disabled');
      }
      $('#type').empty();
      $('#type').append("<option value=''>Selecciona</option>");
      for(i=0;i < response.length; i++){
        $('#type').append("<option value='"+response[i].id+"'>"+response[i].name+"</option>");
      }
    })
  });

</script>
