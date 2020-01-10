<div class="m-portlet">
    {{ Form::open(array('route' => array('contracts.storeLocalCharge', $id)),['class' => 'form-group m-form__group']) }}
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
                                <input type="radio" id="rdrouteP" onclick="activarCountry('divport')" checked='true' name="typeroute" value="port"> Port
                                <span></span>
                            </label>
                            <label class="m-radio">
                                <input type="radio" id="rdrouteC" onclick="activarCountry('divcountry')"  name="typeroute" value="country"> Country
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
                {{ Form::select('surcharge_id', $surcharge,null,['id' => 'typeAdd','class'=>'m-select2-general form-control ','style' => 'width:100%;']) }}
            </div>
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('orig', 'Origin Port') !!}
                    {{ Form::select('port_origlocal[]', $harbor,null,['id' => 'portOrigAdd','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;']) }}
                </div>
                <div class="divcountry" hidden="true">
                    {!! Form::label('origC', 'Origin Country') !!}
                    {{ Form::select('country_orig[]', $countries,
                    null,['id' => 'country_origAdd','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple']) }}

                </div>
            </div>
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('dest', 'Destination Port') !!}

                    <div class="m-input-icon m-input-icon--right">
                        {{ Form::select('port_destlocal[]', $harbor,null,['id' => 'portDestAdd','class'=>'m-select2-general  form-control ','multiple' => 'multiple','style' => 'width:100%;']) }}
                        <span class="m-input-icon__icon m-input-icon__icon--right">
                            <span>
                                <i class="la la-info-circle"></i>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="divcountry" hidden="true">
                    {!! Form::label('destC', 'Destination Country') !!}
                    {{ Form::select('country_dest[]',$countries,null,['id' => 'country_destAdd','class'=>'m-select2-general form-control','multiple' => 'multiple'  ]) }}
                </div>


            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('typed', 'Destination type') !!}
                {{ Form::select('typedestiny_id',$typedestiny, null,['id' => 'changetypeAdd','class'=>'m-select2-general form-control','style' => 'width:100%;']) }}
            </div>
            <div class="col-lg-4">
                {!! Form::label('carrierL', 'Carrier') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('carrier_id[]', $carrier,null,['id' => 'localcarrierAdd','class'=>'m-select2-general form-control','multiple' => 'multiple','required' => 'true']) }}
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
                    {{ Form::select('calculationtype_id[]', $calculationT,null,['id' => 'calculationtypeAdd','class'=>'m-select2-general form-control ','multiple' => 'multiple','style' => 'width:80%;']) }}
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
                {!! Form::label('ammountL', 'Amount') !!}
                <div class="m-input-icon m-input-icon--right">
                    {!! Form::number('ammount', null, ['id' => 'ammountAdd','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','step'=>'0.01', 'required'=>'true']) !!}
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
                    {{ Form::select('currency_id', $currency,$currency_cfg->id,['id' => 'localcurrencyAdd','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
                </div>
            </div>
        </div>
    </div>  
    <br>
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
    {!! Form::close() !!}
</div>
<script src="/js/editcontracts.js"></script>
<script>
    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });
</script>
