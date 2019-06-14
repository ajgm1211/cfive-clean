
<div class="m-portlet">
    <!--begin::Form-->
    <?php echo e(Form::model($failrates, array('route' => array('Create.Rates.Lcl',$failrates['rate_id']), 'method' => 'PUT', 'id' => 'frmRates'))); ?>



    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-4">
                <?php echo Form::label('origin_port', 'Origin Port',['style' => $failrates['classorigin']]); ?>

                <?php echo e(Form::select('origin_port', $harbor,$failrates['origin_port'],['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;'])); ?> 
            </div>
            <div class="col-lg-4">
                <?php echo Form::label('destination_port', 'Destination Port',['style' => $failrates['classdestiny']]); ?>

                <div class="m-input-icon m-input-icon--right">
                    <?php echo e(Form::select('destiny_port', $harbor,$failrates['destiny_port'],['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;'])); ?>

                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>

            </div>
            <div class="col-lg-4">
                <?php echo Form::label('carrier', 'Carrier',['style' => $failrates['classcarrier']]); ?>

                <?php echo e(Form::select('carrier_id', $carrier,$failrates['carrierAIn'],['id' => 'carrier','class'=>'m-select2-general form-control'])); ?>


            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                <?php echo Form::label('uom', 'W/M',['style' => $failrates['classuom']]); ?>

                <?php echo Form::text('uom', $failrates['uom'], ['id' => 'uom','placeholder' => 'Please enter the W/M','class' => 'form-control m-input','required']); ?> 
            </div>
            <div class="col-lg-4">
                <?php echo Form::label('minimum', 'Minimum',['style' => $failrates['classminimum']]); ?>

                <?php echo Form::text('minimum', $failrates['minimum'], ['id' => 'minimum','placeholder' => 'Please enter the minimum','class' => 'form-control m-input','required' ]); ?> 

            </div>
            <div class="col-lg-4">
                <?php echo Form::label('currency', 'Currency',['style' => $failrates['classcurrency']]); ?>


                <div class="m-input-icon m-input-icon--right">
                    <?php echo e(Form::select('currency_id', $currency,$failrates['currencyAIn'],['id' => 'currency','class'=>'m-select2-general form-control'])); ?>

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
                <?php echo Form::label('scheduleT','Schedule Type',['style' => $failrates['classscheduleT']]); ?>

                <?php echo e(Form::select('scheduleT',$schedulesT,$failrates['schedueleT'],['id' => 'schedulesT','class'=>'m-select2-general form-control'])); ?>

            </div>
            <div class="col-lg-4">

                <?php echo Form::label('Transit Time', 'Transit Time',['style' => $failrates['classtransittime']]); ?>

                <?php echo Form::number('transit_time',$failrates['transit_time'], ['id' => 'transit_time','placeholder' => 'Transit Time','class' => 'form-control ','required']); ?>


            </div>
            <div class="col-lg-4">

                <?php echo Form::label('via', 'Via',['style' => $failrates['classvia']]); ?>

                <?php echo Form::text('via',$failrates['via'], ['id' => 'via','placeholder' => 'via','class' => 'form-control ']); ?>


            </div>
        </div>
    </div>  

    <input type="hidden" value="<?php echo e($failrates['contract_id']); ?>" name="contract_id" id="contract_id" />

    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
            <br>
            <?php echo Form::submit('Update', ['class'=> 'btn btn-primary']); ?>

            <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
    </div>
</div>
<?php echo Form::close(); ?>

<!--end::Form-->
</div>
<script>


    $('.m-select2-general').select2({

    });


</script>
