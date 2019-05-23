
<div class="m-portlet">
    <!--begin::Form-->
    {{ Form::model($failrates, array('route' => array('create.Rates.For.Contracts',$failrates['rate_id']), 'method' => 'PUT', 'id' => 'frmRates')) }}


    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-4">
                {!! Form::label('origin_port', 'Origin Port',['style' => $failrates['classorigin']]) !!}
                {{ Form::select('origin_port[]', $harbor,$failrates['origin_port'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','multiple'=>'multiple']) }} 
            </div>
            <div class="col-lg-4">
                {!! Form::label('destination_port', 'Destination Port',['style' => $failrates['classdestiny']]) !!}
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
                {!! Form::label('carrier', 'Carrier',['style' => $failrates['classcarrier']]) !!}
                {{ Form::select('carrier_id', $carrier,$failrates['carrierAIn'],['id' => 'carrier','class'=>'m-select2-general form-control']) }}

            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('twuenty', '20 \' ',['style' => $failrates['classtwuenty']]) !!}
                {!! Form::text('twuenty', $failrates['twuenty'], ['id' => 'twuenty','placeholder' => 'Please enter the 20','class' => 'form-control m-input','required']) !!} 
            </div>


            <div class="col-lg-4">
                {!! Form::label('forty', '40  \' ',['style' => $failrates['classforty']]) !!}
                {!! Form::text('forty', $failrates['forty'], ['id' => 'forty','placeholder' => 'Please enter the 40','class' => 'form-control m-input','required' ]) !!} 

            </div>
            <div class="col-lg-4">

                {!! Form::label('fortyhc', '40 \'HC  ',['style' => $failrates['classfortyhc']]) !!}
                {!! Form::text('fortyhc',$failrates['fortyhc'], ['id' => 'fortyhc','placeholder' => '40HC','class' => 'form-control ','required']) !!}

            </div>

        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">

                {!! Form::label('fortynor', '40 \'NOR  ',['style' => $failrates['classfortynor']]) !!}
                {!! Form::text('fortynor',$failrates['fortynor'], ['id' => 'fortynor','placeholder' => '40\'NOR','class' => 'form-control ','required']) !!}

            </div>
            <div class="col-lg-4">

                {!! Form::label('fortyfive', '45\' ',['style' => $failrates['classfortyfive']]) !!}
                {!! Form::text('fortyfive',$failrates['fortyfive'], ['id' => 'fortyfive','placeholder' => '45\'','class' => 'form-control ','required']) !!}

            </div>
            <div class="col-lg-4">
                {!! Form::label('currency', 'Currency',['style' => $failrates['classcurrency']]) !!}

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
                {!! Form::label('scheduleT','Schedule Type',['style' => $failrates['classscheduleT']]) !!}
                {{ Form::select('scheduleT',$schedulesT,$failrates['schedueleT'],['id' => 'schedulesT','class'=>'m-select2-general form-control']) }}
            </div>
            <div class="col-lg-4">

                {!! Form::label('Transit Time', 'Transit Time',['style' => $failrates['classtransittime']]) !!}
                {!! Form::number('transit_time',$failrates['transit_time'], ['id' => 'transit_time','placeholder' => 'Transit Time','class' => 'form-control ','required']) !!}

            </div>
            <div class="col-lg-4">

                {!! Form::label('via', 'Via',['style' => $failrates['classvia']]) !!}
                {!! Form::text('via',$failrates['via'], ['id' => 'via','placeholder' => 'via','class' => 'form-control ','required']) !!}

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
