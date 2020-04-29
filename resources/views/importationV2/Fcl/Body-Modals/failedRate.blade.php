
<div class="m-portlet">
    <!--begin::Form-->
    {{ Form::model($failrates, array('route' => array('create.Rates.For.Contracts',$failrates['rate_id']), 'method' => 'PUT', 'id' => 'frmRates')) }}

    <input type="hidden" name="equiment_id" value="{{$failrates['equiment_id']}}" >
    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-4">
                {!! Form::label('origin_port', 'Origin Port',['style' => 'color:'.$failrates['classorigin']]) !!}
                {{ Form::select('origin_port[]', $harbor,$failrates['origin_port'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','multiple'=>'multiple']) }} 
            </div>
            <div class="col-lg-4">
                {!! Form::label('destination_port', 'Destination Port',['style' => 'color:'.$failrates['classdestiny']]) !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('destiny_port[]', $harbor,$failrates['destiny_port'],['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;','multiple'=>'multiple']) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>

            </div>
            <div class="col-lg-4">
                {!! Form::label('carrier', 'Carrier',['style' => 'color:'.$failrates['classcarrier']]) !!}
                {{ Form::select('carrier_id', $carrier,$failrates['carrierAIn'],['id' => 'carrier','class'=>'m-select2-general form-control']) }}

            </div>
        </div>

        @php
        $contador = 0;
        @endphp
        @foreach($failrates['containers'] as $key => $value)
        @if($contador == 0)
        <div class="form-group m-form__group row">
            @endif
            <div class="col-lg-4">
                {!! Form::label($key, $value['name'],['style' => 'color:'.$value['color']]) !!}
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
            {!! Form::label('currency', 'Currency',['style' => 'color:'.$failrates['classcurrency']]) !!}

            <div class="m-input-icon m-input-icon--right">
                {{ Form::select('currency_id', $currency,$failrates['currencyAIn'],['id' => 'currency','class'=>'m-select2-general form-control']) }}
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
            {!! Form::label('scheduleT','Schedule Type',['style' => 'color:'.$failrates['classscheduleT']]) !!}
            {{ Form::select('scheduleT',$schedulesT,$failrates['schedueleT'],['id' => 'schedulesT','class'=>'m-select2-general form-control']) }}
        </div>
        <div class="col-lg-4">

            {!! Form::label('Transit Time', 'Transit Time',['style' => 'color:'.$failrates['classtransittime']]) !!}
            {!! Form::number('transit_time',$failrates['transit_time'], ['id' => 'transit_time','placeholder' => 'Transit Time','class' => 'form-control ']) !!}

        </div>
        <div class="col-lg-4">

            {!! Form::label('via', 'Via',['style' => 'color:'.$failrates['classvia']]) !!}
            {!! Form::text('via',$failrates['via'], ['id' => 'via','placeholder' => 'via','class' => 'form-control ']) !!}

        </div>
    </div>  
</div>  

<input type="hidden" value="{{$failrates['contract_id']}}" name="contract_id" id="contract_id" />

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
</div>
{!! Form::close() !!}
<!--end::Form-->
</div>
<script>


    $('.m-select2-general').select2({

    });


</script>
