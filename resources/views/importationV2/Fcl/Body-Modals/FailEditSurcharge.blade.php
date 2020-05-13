<script src="/js/Contracts/editcontracts.js"></script>
@if($differentiator == 1)
@php
$portRadio = true; 
$countryRadio = false; 
@endphp
<script>
    activarCountry('divport');
</script>
@endif
@if($differentiator == 2)
@php
$countryRadio = true; 
$portRadio = false; 
@endphp
<script>
    activarCountry('divcountry');
</script>
@endif
{{ Form::model($failsurchargeArre, array('route' => array('create.Surchargers.For.Contracts', $failsurchargeArre['id']), 'method' => 'PUT', 'id' => 'frmSurcharges')) }}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Edit Good Rates
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
                                    {{ Form::radio('typeroute', 'port', $portRadio ,['id' => 'rdrouteP' , 'onclick' => 'activarCountry(\'divport\')','class' => 'radio-place', 'data'=>'1' ]) }} Port
                                    <span></span>
                                </label>
                                <label class="m-radio">
                                    {{ Form::radio('typeroute', 'country', $countryRadio ,['id' => 'rdrouteC' , 'onclick' => 'activarCountry(\'divcountry\')','class' => 'radio-place', 'data'=>'2' ]) }} Country
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
                        {!! Form::label('type', 'Type',['style' => $failsurchargeArre['classsurcharge']]) !!}
                    </label>
                    {{ Form::select('surcharge_id', $surchargeSelect,$failsurchargeArre['surcharge'],['id' => 'type','class'=>'m-select2-general form-control ','style' => 'width:100%;']) }}
                </div>
                <div class="col-lg-4">
                    <div class="divport" >
                        {!! Form::label('orig', 'Origin Port',['style' => $failsurchargeArre['classorigin']]) !!}
                        {{ Form::select('port_origlocal[]', $harbor,$failsurchargeArre['origin_port'],['id' => 'portOrig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;']) }}
                    </div>
                    <div class="divcountry" hidden="true">
                        {!! Form::label('origC', 'Origin Country',['style' => $failsurchargeArre['classorigin']]) !!}
                        {{ Form::select('country_orig[]', $countries,
                        $failsurchargeArre['origin_port'],['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple']) }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="divport" >
                        {!! Form::label('dest', 'Destination Port',['style' => $failsurchargeArre['classdestiny']]) !!}

                        <div class="m-input-icon m-input-icon--right">
                            {{ Form::select('port_destlocal[]', $harbor,$failsurchargeArre['destiny_port'],['id' => 'portDest','class'=>'m-select2-general  form-control ','multiple' => 'multiple','style' => 'width:100%;']) }}
                            <span class="m-input-icon__icon m-input-icon__icon--right">
                                <span>
                                    <i class="la la-info-circle"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="divcountry" hidden="true">
                        {!! Form::label('destC', 'Destination Country',['style' => $failsurchargeArre['classdestiny']]) !!}
                        {{ Form::select('country_dest[]',$countries,$failsurchargeArre['destiny_port'],[ 'id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ]) }}
                    </div>
                </div>
            </div>
            <div class="form-group m-form__group row">
                <div class="col-lg-4">
                    {!! Form::label('typed', 'Destination type',['style' => $failsurchargeArre['classtypedestiny']]) !!}
                    {{ Form::select('changetype',$typedestiny, $failsurchargeArre['typedestiny'],['id' => 'changetype','class'=>'m-select2-general form-control','style' => 'width:100%;']) }}
                </div>
                <div class="col-lg-4">
                    {!! Form::label('carrierL', 'Carrier',['style' => $failsurchargeArre['classcarrier']]) !!}
                    <div class="m-input-icon m-input-icon--right">
                        {{ Form::select('carrier_id[]', $carrierSelect,$failsurchargeArre['carrier'],['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
                        <span class="m-input-icon__icon m-input-icon__icon--right">
                            <span>
                                <i class="la la-info-circle"></i>
                            </span>
                        </span>
                    </div>

                </div>
                <div class="col-lg-4">
                    {!! Form::label('calculationt', 'Calculation Type',['style' => $failsurchargeArre['classcalculationtype']]) !!}
                    <div class="m-input-icon m-input-icon--right">
                        {{ Form::select('calculationtype_id', $calculationtypeselect,$failsurchargeArre['calculationtype'],['id' => 'calculationtype','class'=>'m-select2-general form-control ','style' => 'width:80%;']) }}
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
                    {!! Form::label('ammountL', 'Ammount',['style' => $failsurchargeArre['classammount']]) !!}
                    <div class="m-input-icon m-input-icon--right">
                        {!! Form::text('ammount', $failsurchargeArre['ammount'], ['id' => 'ammount','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','required']) !!}
                        <span class="m-input-icon__icon m-input-icon__icon--right">
                            <span>
                                <i class="la la-bookmark-o"></i>
                            </span>
                        </span>
                    </div>

                </div>
                <div class="col-lg-4">
                    {!! Form::label('currencyl', 'Currency',['style' => $failsurchargeArre['classcurrency']]) !!}
                    <div class="m-input-icon m-input-icon--right">
                        {{ Form::select('currency_id', $currency,$failsurchargeArre['currency'],['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'style' => 'width:100%;'.$failsurchargeArre['classcurrency']]) }}
                        <span class="m-input-icon__icon m-input-icon__icon--right">
                            <span>
                                <i class="la la-bookmark-o"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>  
        <input type="hidden" value="{{$failsurchargeArre['contract_id']}}" name="contract_id" id="contract_id" />
    </div>
</div>

<div id="edit-modal-footer" class="modal-footer">
    {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
    <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
    </button>
</div>
{!! Form::close() !!}

<script>

    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

    $(document).ready(function(e){
        //alert(nameTab);
        // frmSurcharges id del formulario Auto Save TAB
        $("#frmSurcharges").append('<input type="hidden" name="nameTab" value="'+nameTab+'">');
    });

    function radio_place(val){
        if(val == 1){
            $('#portOrig').attr('required','required');
            $('#country_orig').removeAttr('required');
            $('#portDest').attr('required','required');
            $('#country_dest').removeAttr('required');
        }else if(val == 2){
            $('#country_orig').attr('required','required');
            $('#portOrig').removeAttr('required');
            $('#country_dest').attr('required','required');
            $('#portDest').removeAttr('required');
        }
    }
    $(document).on('click','.radio-place',function(e){
        var val = $(this).attr('data');

        if(val == 1){
            $('#portOrig').attr('required','required');
            $('#portDest').attr('required','required');
            $('#country_orig').removeAttr('required');
            $('#country_dest').removeAttr('required');
        }else if(val == 2){
            $('#country_orig').attr('required','required');
            $('#portOrig').removeAttr('required');
            $('#country_dest').attr('required','required');
            $('#portDest').removeAttr('required');
        }
    });

</script>
