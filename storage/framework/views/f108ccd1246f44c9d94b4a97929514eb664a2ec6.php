
<div class="m-portlet">
    <!--begin::Form-->
    <?php echo e(Form::model($rates, array('route' => array('Update.RatesD.For.Contracts', $rates->id), 'method' => 'get', 'id' => 'frmRates'))); ?>



    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-4">
                <?php echo Form::label('origin_port', 'Origin Port'); ?>

                <?php echo e(Form::select('origin_id', $harbor,$rates->port_origin->id,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;'])); ?> 
            </div>
            <div class="col-lg-4">
                <?php echo Form::label('destination_port', 'Destination Port'); ?>

                <div class="m-input-icon m-input-icon--right">
                    <?php echo e(Form::select('destiny_id', $harbor,$rates->port_destiny->id,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;'])); ?>

                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>

            </div>
            <div class="col-lg-4">
                <?php echo Form::label('carrier', 'Carrier'); ?>

                <?php echo e(Form::select('carrier_id', $carrier,$rates->carrier->id,['id' => 'carrier','class'=>'m-select2-general form-control'])); ?>


            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                <?php echo Form::label('twuenty', '20 \' '); ?>

                <?php echo Form::number('twuenty', $rates->twuenty, ['id' => 'twuenty','placeholder' => 'Please enter the 20','class' => 'form-control m-input','required' ]); ?> 
            </div>


            <div class="col-lg-4">
                <?php echo Form::label('forty', '40  \' '); ?>

                <?php echo Form::number('forty', $rates->forty, ['id' => 'forty','placeholder' => 'Please enter the 40','class' => 'form-control m-input','required' ]); ?> 

            </div>
            <div class="col-lg-4">

                <?php echo Form::label('fortyhc', '40 HC \' '); ?>

                <?php echo Form::number('fortyhc', $rates->fortyhc, ['id' => 'fortyhc','placeholder' => '40HC','class' => 'form-control ','required']); ?>


            </div>

        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">

                <?php echo Form::label('fortynor', '40\'NOR '); ?>

                <?php echo Form::number('fortynor', $rates->fortynor, ['id' => 'fortynor','placeholder' => '40\'NOR','class' => 'form-control ','required']); ?>


            </div>
            <div class="col-lg-4">

                <?php echo Form::label('fortyfive', '45\''); ?>

                <?php echo Form::number('fortyfive', $rates->fortyfive, ['id' => 'fortyfive','placeholder' => '45\'','class' => 'form-control ','required']); ?>


            </div>
            <div class="col-lg-4">
                <?php echo Form::label('currency', 'Currency'); ?>


                <div class="m-input-icon m-input-icon--right">
                    <?php echo e(Form::select('currency_id', $currency,$rates->currency->id,['id' => 'currency','class'=>'m-select2-general form-control'])); ?>

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
                <?php echo Form::label('scheduleT','Schedule Type'); ?>

                <?php echo e(Form::select('scheduleT',$schedulesT,$rates['schedule_type_id'],['id' => 'schedulesT','class'=>'m-select2-general form-control'])); ?>

            </div>
            <div class="col-lg-4">

                <?php echo Form::label('Transit Time', 'Transit Time'); ?>

                <?php echo Form::number('transit_time',$rates['transit_time'], ['id' => 'transit_time','placeholder' => 'Transit Time','class' => 'form-control ']); ?>


            </div>
            <div class="col-lg-4">

                <?php echo Form::label('via', 'Via'); ?>

                <?php echo Form::text('via',$rates['via'], ['id' => 'via','placeholder' => 'via','class' => 'form-control ']); ?>


            </div>
        </div>  
    </div>  
    <input type="hidden" value="<?php echo e($rates['contract_id']); ?>" name="contract_id" id="contract_id" />


    <br>
    <hr>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
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
