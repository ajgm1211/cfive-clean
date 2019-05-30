
<div class="m-portlet">

    {{ Form::open(array('route' => array('contractslcl.storeRate', $id)),['class' => 'form-group m-form__group']) }}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-4">
                {!! Form::label('origin_port', 'Origin Port') !!}
                {{ Form::select('origin_port[]', $harbor,null,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','multiple' => 'multiple']) }} 
            </div>
            <div class="col-lg-4">
                {!! Form::label('destination_port', 'Destination Port') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('destiny_port[]', $harbor,null,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;','multiple' => 'multiple']) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>

            </div>
            <div class="col-lg-4">
                {!! Form::label('carrier', 'Carrier') !!}
                {{ Form::select('carrier_id', $carrier,null,['id' => 'carrier','class'=>'m-select2-general form-control','required' => 'true']) }}

            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label(' W/M', ' W/M') !!}
                {!! Form::number('uom',0, ['id' => 'uom','placeholder' => 'Please enter the Uom','class' => 'form-control m-input' ]) !!} 
            </div>


            <div class="col-lg-4">
                {!! Form::label('minimum', 'minimum') !!}
                {!! Form::number('minimum', 0, ['id' => 'minimum','placeholder' => 'Please enter the Minimum','class' => 'form-control m-input' ]) !!} 

            </div>
            <div class="col-lg-4">
                {!! Form::label('currency', 'Currency') !!}

                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('currency_id', $currency,null,['id' => 'currency','class'=>'m-select2-general form-control']) }}
                </div>
            </div>

        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('scheduleT','Schedule Type') !!}
                {{ Form::select('schedule_type_id',$scheduleT,null,['id' => 'schedulesT','class'=>'m-select2-general form-control']) }}
            </div>
            <div class="col-lg-4">

                {!! Form::label('Transit Time', 'Transit Time') !!}
                {!! Form::number('transit_time',null, ['id' => 'transit_time','placeholder' => 'Transit Time','class' => 'form-control ']) !!}

            </div>
            <div class="col-lg-4">

                {!! Form::label('via', 'Via') !!}
                {!! Form::text('via',null, ['id' => 'via','placeholder' => 'via','class' => 'form-control ']) !!}

            </div>
        </div>
    </div>  
    <br>
    <br>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <br>
        <div class="m-form__actions m-form__actions">
            &nbsp;&nbsp;&nbsp;{!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
            <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
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
