<script src="/js/globalcharges.js"></script>
@if($failglobal['differentiator'] == 1)
@php
$portRadio = true; 
$countryRadio = false; 
@endphp
<script>
    activarCountry('divport');
</script>
@endif
@if($failglobal['differentiator'] == 2)
@php
$countryRadio = true; 
$portRadio = false; 
@endphp
<script>
    activarCountry('divcountry');
</script>
@endif

<div class="m-portlet">

    {{ Form::model($failglobal, array('route' => array('save.fail.good.globalcharge.fcl', $failglobal['id']), 'method' => 'GET', 'id' => 'frmSurcharges')) }}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <label>
                            {!! Form::label('Type Route', 'Type Route / Good') !!}
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
                    {!! Form::label('type', 'Type',['style'=>$arre['classsurcharge']]) !!}
                </label>
                {{ Form::select('surcharge_id', $surcharge,$arre['surcharge_id'],['id' => 'type','class'=>'m-select2-general form-control ']) }}
            </div>
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('orig', 'Origin Port',['style'=>$arre['classorigin']]) !!}
                    {{ Form::select('port_orig[]', $harbor,$arre['origin_port'],['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple']) }}
                </div>
                <div class="divcountry" hidden="true">

                    {!! Form::label('origC', 'Origin Country',['style'=>$arre['classorigin']]) !!}
                    {{ Form::select('country_orig[]', $countries,$arre['origin_port'],['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple']) }}

                </div>
            </div>
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('dest', 'Destination Port',['style'=>$arre['classdestiny']]) !!}
                    <div class="m-input-icon m-input-icon--right">
                        {{ Form::select('port_dest[]', $harbor,$arre['destiny_port'],['id' => 'port_dest','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
                        <span class="m-input-icon__icon m-input-icon__icon--right">
                            <span>
                                <i class="la la-info-circle"></i>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="divcountry" hidden="true" >
                    {!! Form::label('destC', 'Destination Country',['style'=>$arre['classdestiny']]) !!}
                    {{ Form::select('country_dest[]',$countries,$arre['destiny_port'],[ 'id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ]) }}
                </div>
            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('typed', 'Destination type',['style'=>$arre['classtypedestiny']]) !!}
                {{ Form::select('changetype',$typedestiny, $arre['typedestiny_id'],['id' => 'changetype','class'=>'m-select2-general form-control']) }}
            </div>
            <div class="col-lg-4">
                {!! Form::label('validation_expire', 'Validation',['style'=>$arre['classvalidity']]) !!}
                {!! Form::text('validation_expire', $arre['validation_expire'], ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
            </div>


            <div class="col-lg-4">
                {!! Form::label('calculationt', 'Calculation Type',['style'=>$arre['classcalculationtype']]) !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('calculationtype_id', $calculationT,$arre['calculationtype_id'],['id' => 'calculationtype','class'=>'m-select2-general form-control ']) }}
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
                {!! Form::label('carrierL', 'Carrier',['style'=>$arre['classcarrier']]) !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('carrier_id[]', $carrier,$arre['carrier'],['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>
            </div>

            <div class="col-lg-4">
                {!! Form::label('ammountL', 'Ammount',['style'=>$arre['classammount']]) !!}
                <div class="m-input-icon m-input-icon--right">
                    {!! Form::text('ammount', $arre['ammount'], ['id' => 'ammount','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-bookmark-o"></i>
                        </span>
                    </span>
                </div>

            </div>
            <div class="col-lg-4">
                {!! Form::label('minimum', 'Minimum',['style'=>$arre['classminimum']]) !!}
                <div class="m-input-icon m-input-icon--right">
                    {!! Form::text('minimum', $arre['minimum'], ['id' => 'minimum','placeholder' => 'Please enter the minimum','class' => 'form-control m-input']) !!}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-bookmark-o"></i>
                        </span>
                    </span>
                </div>

            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('currencyl', 'Currency',['style'=>$arre['classcurrency']]) !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('currency_id', $currency,$arre['currency_id'],['id' => 'localcurrency','class'=>'m-select2-general form-control' ]) }}
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
            {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
            <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script>


    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });


</script>

