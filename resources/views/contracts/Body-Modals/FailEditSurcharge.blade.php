<div class="m-portlet">

    {{ Form::model($failsurchargeArre, array('route' => array('create.Surchargers.For.Contracts', $failsurchargeArre['id']), 'method' => 'PUT', 'id' => 'frmSurcharges')) }}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                <label>
                    {!! Form::label('type', 'Type',['style' => $failsurchargeArre['classsurcharge']]) !!}
                </label>
                {{ Form::select('surcharge_id', $surchargeSelect,$failsurchargeArre['surcharge'],['id' => 'type','class'=>'m-select2-general form-control ','style' => 'width:100%;']) }}
            </div>
            <div class="col-lg-4">
                {!! Form::label('orig', 'Origin Port',['style' => $failsurchargeArre['classorigin']]) !!}
                {{ Form::select('port_origlocal[]', $harbor,$failsurchargeArre['origin_port'],['id' => 'portOrig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;']) }}
            </div>
            <div class="col-lg-4">
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
    <br>
    <hr>
    <input type="hidden" value="{{$failsurchargeArre['contract_id']}}" name="contract_id" id="contract_id" />
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
            {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
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
