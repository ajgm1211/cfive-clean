@if(!$globalcharges->globalcharport->isEmpty())
@php
$portRadio = true; 
$countryRadio = false; 
@endphp
<script>
    activarCountry('divport');
</script>
@endif
@if(!$globalcharges->globalcharcountry->isEmpty())
@php
$countryRadio = true; 
$portRadio = false; 
@endphp
<script>
    activarCountry('divcountry');
</script>
@endif

<div class="m-portlet">

    {{ Form::model($globalcharges, array('route' => array('GlobalsDuplicatedEspecific.update', $globalcharges->id), 'method' => 'PUT', 'id' => 'frmSurcharges')) }}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <label>
                            {!! Form::label('Type Route', 'Type Route') !!}
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
                    <div class="col-lg-8">
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
                {{ Form::select('surcharge_id', $surcharge,$globalcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ']) }}
            </div>
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('orig', 'Origin Port') !!}
                    {{ Form::select('port_orig[]', $harbor,
                    $globalcharges->globalcharport->pluck('portOrig')->unique()->pluck('id'),['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple']) }}
                </div>
                <div class="divcountry" hidden="true">

                    {!! Form::label('origC', 'Origin Country') !!}
                    {{ Form::select('country_orig[]', $countries,
                    $globalcharges->globalcharcountry->pluck('countryOrig')->unique()->pluck('id'),['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple']) }}

                </div>
            </div>
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('dest', 'Destination Port') !!}
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
                <div class="divcountry" hidden="true" >
                    {!! Form::label('destC', 'Destination Country') !!}
                    {{ Form::select('country_dest[]',$countries,$globalcharges->globalcharcountry->pluck('countryDest')->unique()->pluck('id'),[ 'id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ]) }}
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
            <div class="col-lg-4">
                {!! Form::label('carrierL', 'Carrier') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('carrier_id[]', $carrier,$globalcharges->globalcharcarrier->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple','required' => 'required']) }}
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
                    {!! Form::number('ammount', $globalcharges->ammount, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','required','class' => 'form-control m-input','step'=>'0.01']) !!}
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
        <input type="hidden" name="grupo_id" value="{{$grupo_id}}">
    </div>  
    <br>
    <hr>
    <div class="m-portlet__foot m-portlet__foot--fit" style="margin-left: 20px;">
        <div class="m-form__actions m-form__actions">
            {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
            <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
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

