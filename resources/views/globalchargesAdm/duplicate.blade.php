<script>
	activarCountry('{{ $activacion['act'] }}');
</script>
<div class="m-portlet">

	{{ Form::model($globalcharges, array('route' => array('gcadm.store', $globalcharges->id), 'id' => 'frmSurcharges')) }}
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
								{{ Form::radio('typeroute', 'port', $activacion['rdrouteP'] ,['id' => 'rdrouteP' , 'onclick' => 'activarCountry(\'divport\')' ]) }} Port
								<span></span>
							</label>
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
							<i onclick="inverter()" style="font-size:22px" class="la la-arrows-h" title="Exchange Origin / Destination"></i>
						</div>
					</div>
					<div class="col-lg-3">
						{!! Form::label('company_user', 'Company User') !!}
						<div class="m-input-icon m-input-icon--right">
							{{ Form::select('company_user_id',$company_users,$globalcharges->company_user_id,['id' => 'company_user_id','class'=>'m-select2-general form-control' ,'required' => 'true' ]) }}
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
				{{ Form::select('type', $surcharge,$globalcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ','style' => 'width:100%;' ,'required' => 'true']) }}
			</div>
			<div class="col-lg-4">
				<div class="divport" >
					{!! Form::label('orig', 'Origin Port') !!}
					{{ Form::select('port_orig[]', $harbor,$globalcharges->globalcharport->pluck('port_orig'),['id' => 'portOrig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;']) }}
				</div>
				<div class="divcountry" hidden="true">
					{!! Form::label('origC', 'Origin Country') !!}
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
			</div>
			<div class="col-lg-4">
				<div class="divport" >
					{!! Form::label('dest', 'Destination Port') !!}
					<div class="m-input-icon m-input-icon--right">
						{{ Form::select('port_dest[]', $harbor,$globalcharges->globalcharport->pluck('port_dest'),['id' => 'portDest','class'=>'m-select2-general  form-control ','multiple' => 'multiple','style' => 'width:100%;']) }}
						<span class="m-input-icon__icon m-input-icon__icon--right">
							<span>
								<i class="la la-info-circle"></i>
							</span>
						</span>
					</div>
				</div>
				<div class="divcountry" hidden="true">
					{!! Form::label('destC', 'Destination Country') !!}
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
			</div>
		</div>
		<div class="form-group m-form__group row">
			<div class="col-lg-4">
				{!! Form::label('typed', 'Destination type') !!}
				{{ Form::select('changetype',$typedestiny, $globalcharges->typedestiny_id,['id' => 'changetype','class'=>'m-select2-general form-control','style' => 'width:100%;']) }}
			</div>


			<div class="col-lg-4">
				{!! Form::label('validation_expire', 'Validation') !!}
				{!! Form::text('validation_expire', $globalcharges->validation_expire, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
			</div>


			<div class="col-lg-4">
				{!! Form::label('calculationt', 'Calculation Type') !!}
				<div class="m-input-icon m-input-icon--right">
					{{ Form::select('calculationtype[]', $calculationT,$globalcharges->calculationtype_id,['id' => 'calculationtype','class'=>'m-select2-general form-control ','style' => 'width:80%;','multiple' => 'multiple'  ]) }}
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
					{{ Form::select('localcarrier[]', $carrier,$globalcharges->globalcharcarrier->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
					<span class="m-input-icon__icon m-input-icon__icon--right">
						<span>
							<i class="la la-info-circle"></i>
						</span>
					</span>
				</div>

			</div>
			<div class="col-lg-4">
				{!! Form::label('ammountL', 'Amount') !!}
				<div class="m-input-icon m-input-icon--right">
					{!! Form::text('ammount', $globalcharges->ammount, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','required','class' => 'form-control m-input']) !!}
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
					{{ Form::select('localcurrency_id', $currency,$globalcharges->currency_id,['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
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
	<div class="m-portlet__foot m-portlet__foot--fit">
		<br>
		<div class="m-form__actions m-form__actions">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			{!! Form::submit('Duplicate', ['class'=> 'btn btn-primary']) !!}
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

	function inverter(){
		var radioValue = $("input[name='typeroute']:checked").val();
		if(radioValue == 'port'){
			var origen_port  = $("#portOrig").select2('val');
			var destino_port = $("#portDest").select2('val');
			$('#portOrig').val(destino_port).trigger('change');
			$('#portDest').val(origen_port).trigger('change');
			//alert('va por puerto a puerto');
		} else if(radioValue == 'country'){
			var origen_country  = $("#country_orig").select2('val');
			var destino_country = $("#country_dest").select2('val');
			$('#country_orig').val(destino_country).trigger('change');
			$('#country_dest').val(origen_country).trigger('change');
			//alert('va por country a country');
		} else if(radioValue == 'portcountry'){
			//alert('invierte selects con puertoCountry a countryPuerto');
			var origen_PrCr  = $("#portcountry_orig").select2('val');
			var destino_CrPr = $("#portcountry_dest").select2('val');
			activarCountry('divcountryport');
			$('#countryport_orig').val(destino_CrPr).trigger('change');
			$('#countryport_dest').val(origen_PrCr).trigger('change');
			$("#rdrouteCP").attr('checked', 'checked');
			$("#rdrouteCP").prop('checked', 'true');
		} else if(radioValue == 'countryport'){
			var origen_CrPr  = $("#countryport_orig").select2('val');
			var destino_PrCr = $("#countryport_dest").select2('val');
			activarCountry('divportcountry');
			$('#portcountry_orig').val(destino_PrCr).trigger('change');
			$('#portcountry_dest').val(origen_CrPr).trigger('change');
			$("#rdroutePC").attr('checked', 'checked');
			$("#rdroutePC").prop('checked', 'true');
			//alert('invierte selects con countryPuerto a puertoCountry');
		}
	}
	$('.m-select2-general').select2({
		placeholder: "Select an option"
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
