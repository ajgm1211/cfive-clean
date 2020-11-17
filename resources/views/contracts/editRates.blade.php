
<div class="m-portlet" style="box-shadow:none;">
    <!--begin::Form-->
    {{ Form::model($rates, array('route' => array('update-rates', $rates->id), 'method' => 'PUT', 'id' => 'frmRates')) }}


    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-4">
                <i class="la la-anchor icon__modal"></i>
                {!! Form::label('origin_port', 'Origin Port') !!}
                {{ Form::select('origin_port', $harbor,$rates->port_origin->id,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;']) }} 
            </div>
            <div class="col-lg-4">
               <i class="la la-anchor icon__modal"></i> {!! Form::label('destination_port', 'Destination Port') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('destiny_port', $harbor,$rates->port_destiny->id,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;']) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>

            </div>
            <div class="col-lg-4">
               <i class="la la-ship icon__modal"></i> {!! Form::label('carrier', 'Carrier') !!}
                {{ Form::select('carrier_id', $carrier,$rates->carrier->id,['id' => 'carrier','class'=>'m-select2-general form-control']) }}

            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                <i class="la la-database icon__modal" style="transform: rotate(90deg); position: relative; bottom:-2px"></i>{!! Form::label('twuenty', 'Rate 20 \' ') !!}
                {!! Form::number('twuenty', $rates->twuenty, ['id' => 'twuenty','placeholder' => 'Default value zero','class' => 'form-control m-input' ,'required'=>'true'  ]) !!} 
            </div>


            <div class="col-lg-4">
                <i class="la la-database icon__modal" style="transform: rotate(90deg); position: relative; bottom:-2px"></i>{!! Form::label('forty', 'Rates 40  \' ') !!}
                {!! Form::number('forty', $rates->forty, ['id' => 'forty','placeholder' => 'Default value zero','class' => 'form-control m-input' ,'required'=>'true'  ]) !!} 

            </div>
            <div class="col-lg-4">
                <i class="la la-database icon__modal" style="transform: rotate(90deg); position: relative; bottom:-2px"></i>{!! Form::label('fortyhc', 'Rates 40 HC \' ') !!}
                {!! Form::number('fortyhc', $rates->fortyhc, ['id' => 'fortyhc','placeholder' => 'Default value zero','class' => 'form-control ' ,'required'=>'true'  ]) !!}

            </div>

        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                <i class="la la-database icon__modal" style="transform: rotate(90deg); position: relative; bottom:-2px"></i>{!! Form::label('fortynor', 'Rates 40 NOR \' ') !!}
                {!! Form::number('fortynor', $rates->fortynor, ['id' => 'fortynor','placeholder' => 'Default value zero','class' => 'form-control m-input' ,'required'=>'true'  ]) !!} 

            </div>
            <div class="col-lg-4">

                <i class="la la-database icon__modal" style="transform: rotate(90deg); position: relative; bottom:-2px"></i>{!! Form::label('fortyfive', 'Rates 45 \' ') !!}
                {!! Form::number('fortyfive', $rates->fortyfive, ['id' => 'fortyfive','placeholder' => 'Default value zero','class' => 'form-control ' ,'required'=>'true'  ]) !!}

            </div>
            <div class="col-lg-4">
                <i class="la la-dollar icon__modal"></i>{!! Form::label('currency', 'Currency') !!}

                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('currency_id', $currency,$rates->currency->id,['id' => 'currency','class'=>'m-select2-general form-control']) }}
                </div>

            </div>
        </div>


        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                <i class="la la-send icon__modal"></i>{!! Form::label('sh-tpy', 'Schedule Type') !!}
                {{ Form::select('schedule_type_id', $schedulesT,$rates->schedule_type_id,['id' => 'scheduleT','class'=>'m-select2-general form-control']) }}
            </div>
            <div class="col-lg-4">

               <i class="la la-clock-o icon__modal"></i>  {!! Form::label('transit time', 'Transit Time') !!}
                {!! Form::number('transit_time',$rates->transit_time, ['id' => 'transit_time','placeholder' => 'Transit Time','class' => 'form-control ']) !!}

            </div>
            <div class="col-lg-4">

                <i class="la la-exchange icon__modal"></i>{!! Form::label('via', 'Via') !!}
                {!! Form::text('via',$rates->via, ['id' => 'via','placeholder' => 'Via','class' => 'form-control ']) !!}

            </div>
        </div>
    </div>
    <br>
    <br>
    <div class="m-portlet__foot m-portlet__foot--fit" style="border-top:none;">
        <br>
        <div class="m-form__actions m-form__actions" style="text-align:center">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {!! Form::submit('Update', ['class'=> 'btn btn-primary btn-save__modal']) !!}
            <!-- <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button> -->
        </div>
        <br>
    </div>
</div>
{!! Form::close() !!}
<!--end::Form-->
</div>
<script>


    $('.m-select2-general').select2({

    });


</script>
