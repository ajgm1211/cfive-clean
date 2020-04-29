@if(!$goodsurcharges->localcharports->isEmpty())
@php
$portRadio = true; 
$countryRadio = false; 
@endphp
<script>
    activarCountry('divport');
</script>
@endif
@if(!$goodsurcharges->localcharcountries->isEmpty())
@php
$countryRadio = true; 
$portRadio = false; 
@endphp
<script>
    activarCountry('divcountry');
</script>
@endif

{{ Form::model($goodsurcharges, array('route' => array('Update.Surchargers.For.Contracts', $goodsurcharges->id), 'method' => 'get', 'id' => 'frmSurcharges')) }}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Edit Good Surcharger
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">
            &times;
        </span>
    </button>
</div>
<div id="edit-modal-body" class="modal-body">
    <div class="m-portlet">
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
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <div class="col-lg-4">
                        <label>
                            {!! Form::label('type', 'Type') !!}
                        </label>
                        {{ Form::select('surcharge_id', $surchargeSelect,$goodsurcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ','style' => 'width:100%;']) }}
                    </div>
                    <div class="col-lg-4">
                        <div class="divport" >
                            {!! Form::label('orig', 'Origin Port') !!}
                            {{ Form::select('port_origlocal[]', $harbor,$goodsurcharges->localcharports->pluck('port_orig'),['id' => 'portOrig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;']) }}
                        </div>
                        <div class="divcountry" hidden="true">
                            {!! Form::label('origC', 'Origin Country') !!}
                            {{ Form::select('country_orig[]', $countries,
                            $goodsurcharges->localcharcountries->pluck('countryOrig')->unique()->pluck('id'),['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple']) }}

                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="divport" >
                            {!! Form::label('dest', 'Destination Port') !!}
                            <div class="m-input-icon m-input-icon--right">
                                {{ Form::select('port_destlocal[]', $harbor,$goodsurcharges->localcharports->pluck('port_dest'),['id' => 'portDest','class'=>'m-select2-general  form-control ','multiple' => 'multiple','style' => 'width:100%;']) }}
                                <span class="m-input-icon__icon m-input-icon__icon--right">
                                    <span>
                                        <i class="la la-info-circle"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="divcountry" hidden="true">
                            {!! Form::label('destC', 'Destination Country') !!}
                            {{ Form::select('country_dest[]',$countries,$goodsurcharges->localcharcountries->pluck('countryDest')->unique()->pluck('id'),[ 'id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ]) }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <div class="col-lg-4">
                        {!! Form::label('typed', 'Destination type') !!}
                        {{ Form::select('changetype',$typedestiny, $goodsurcharges->typedestiny_id,['id' => 'changetype','class'=>'m-select2-general form-control','style' => 'width:100%;']) }}
                    </div>
                    <div class="col-lg-4">
                        {!! Form::label('carrierL', 'Carrier') !!}
                        <div class="m-input-icon m-input-icon--right">
                            {{ Form::select('carrier_id[]', $carrierSelect,$goodsurcharges->localcharcarriers->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
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
                            {{ Form::select('calculationtype_id', $calculationtypeselect,$goodsurcharges->calculationtype_id,['id' => 'calculationtype','class'=>'m-select2-general form-control ','style' => 'width:80%;']) }}
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
                            {!! Form::text('ammount', $goodsurcharges->ammount, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}
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
                            {{ Form::select('currency_id', $currency,$goodsurcharges->currency_id,['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                <span>
                                    <i class="la la-bookmark-o"></i>
                                </span>
                            </span>
                        </div>

                    </div>
                </div>
            </div>  

            <input type="hidden" value="{{$goodsurcharges['contract_id']}}" name="contract_id" id="contract_id" />
    </div>
</div>
<div id="edit-modal-footer" class="modal-footer">
    {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
    <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
    </button>
</div>
{!! Form::close() !!}
<script src="/js/editcontracts.js"></script>

<script>

    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

</script>
