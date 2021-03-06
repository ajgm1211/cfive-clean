
{{ Form::model($rate, array('route' => array('Update.RatesD.For.Contracts', $rate->id), 'method' => 'get', 'id' => 'frmRates')) }}
<!--begin::Form-->
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

        <input type="hidden" name="equiment_id" value="{{$equiment_id}}" >
        <div class="m-portlet__body">
            <div class="form-group m-form__group row"> 
                <div class="col-lg-4">
                    {!! Form::label('origin_port', 'Origin Port') !!}
                    {{ Form::select('origin_id', $harbor,$rate->port_origin->id,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
                </div>
                <div class="col-lg-4">
                    {!! Form::label('destination_port', 'Destination Port') !!}
                    <div class="m-input-icon m-input-icon--right">
                        {{ Form::select('destiny_id', $harbor,$rate->port_destiny->id,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
                        <span class="m-input-icon__icon m-input-icon__icon--right">
                            <span>
                                <i class="la la-info-circle"></i>
                            </span>
                        </span>
                    </div>

                </div>
                <div class="col-lg-4">
                    {!! Form::label('carrier', 'Carrier') !!}
                    {{ Form::select('carrier_id', $carrier,$rate->carrier->id,['id' => 'carrier','class'=>'m-select2-general form-control']) }}

                </div>
            </div>
            @php
            $contador = 0;
            @endphp
            @foreach($colec as $key => $value)
            @if($contador == 0)
            <div class="form-group m-form__group row">
                @endif
                <div class="col-lg-4">
                    {!! Form::label($key, $value['name']) !!}
                    {!! Form::text($key, $value['value'], ['id' => $key.'_id','placeholder' => 'Please enter the '.$key,'class' => 'form-control m-input','required']) !!} 
                </div>
                @php
                $contador = $contador +1;
                @endphp
                @if($contador == 3)
                @php
                $contador = 0;
                @endphp
            </div>
            @endif
            @endforeach
            @if($contador > 0 && 3 > $contador)
        </div>
        @endif
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('currency', 'Currency') !!}

                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('currency_id', $currency,$rate->currency->id,['id' => 'currency','class'=>'m-select2-general form-control']) }}
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
                {!! Form::label('scheduleT','Schedule Type') !!}
                {{ Form::select('scheduleT',$schedulesT,$rate['schedule_type_id'],['id' => 'schedulesT','class'=>'m-select2-general form-control']) }}
            </div>
            <div class="col-lg-4">

                {!! Form::label('Transit Time', 'Transit Time') !!}
                {!! Form::number('transit_time',$rate['transit_time'], ['id' => 'transit_time','placeholder' => 'Transit Time','class' => 'form-control ']) !!}

            </div>
            <div class="col-lg-4">

                {!! Form::label('via', 'Via') !!}
                {!! Form::text('via',$rate['via'], ['id' => 'via','placeholder' => 'via','class' => 'form-control ']) !!}

            </div>
        </div>  
    </div>  
</div>  
<input type="hidden" value="{{$rate['contract_id']}}" name="contract_id" id="contract_id" />

<div id="edit-modal-footer" class="modal-footer">
    <div class="m-form__actions m-form__actions">
        {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
        <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">Cancel</span>
        </button>
    </div>
</div>

{!! Form::close() !!}
<!--end::Form-->
<script>


    $('.m-select2-general').select2({

    });
    $(document).ready(function(e){
        //alert(nameTab);
        // frmRates id del formulario Auto Save TAB
        $("#frmRates").append('<input type="hidden" name="nameTab" value="'+nameTab+'">');
    });

</script>
