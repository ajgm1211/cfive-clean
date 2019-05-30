<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="createRateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content" style="min-width: 900px; right: 200px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>New Rate</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo Form::open(['route' => 'quotes-v2.rates.store', 'class' => 'form-group m-form__group dfw']); ?>

                <div class="row">
                    <input  type="hidden" name="quote_id" value="<?php echo e($quote->id); ?>" class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <div class="col-md-4" <?php echo e($quote->type=='AIR' ? 'hidden':''); ?>>
                        <div id="origin_harbor_label">
                          <label>Origin port</label>
                          <?php echo e(Form::select('originport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor','required' => 'true'])); ?>

                        </div>
                        <div id="origin_airport_label" <?php echo e($quote->type!='AIR' ? 'hidden':''); ?>>
                          <label>Origin airport</label>
                          <select id="origin_airport" name="origin_airport_id" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div  id="destination_harbor_label" <?php echo e($quote->type=='AIR' ? 'hidden':''); ?>>
                          <label>Destination port</label>
                          <?php echo e(Form::select('destinyport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor','required' => 'true'])); ?>

                        </div>
                        <div id="destination_airport_label" <?php echo e($quote->type!='AIR' ? 'hidden':''); ?>>
                          <label>Destination airport</label>
                          <select id="destination_airport" name="destination_airport_id" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-4" id="delivery_type_label">
                        <label>Delivery type</label>
                        <?php echo e(Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type'])); ?>

                    </div>
                    <div class="col-md-4" id="delivery_type_air_label" style="display: none;">
                        <label>Delivery type</label>
                        <?php echo e(Form::select('delivery_type_air',['5' => 'AIRPORT(Origin) To AIRPORT(Destination)','6' => 'AIRPORT(Origin) To DOOR(Destination)','7'=>'DOOR(Origin) To AIRPORT(Destination)','8'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type_air'])); ?>

                    </div>                 
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4 <?php echo e($hideO); ?>" id="origin_address_label">
                        <label>Origin address</label>
                        <?php echo Form::text('origin_address',null, ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']); ?>

                    </div>
                    <div class="col-md-4 <?php echo e($hideD); ?>" id="destination_address_label">
                        <label>Destination address</label>
                        <?php echo Form::text('destination_address',null, ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']); ?>

                    </div>                    
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <label>Date</label>
                        <div class="input-group date">
                            <?php echo Form::text('date',null, ['id' => 'm_daterangepicker_1' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off']); ?>

                            <?php echo Form::text('date_hidden', null, ['id' => 'date_hidden','hidden'  => 'true']); ?>

                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" class="" id="carrier_label"> 
                        <label>Carrier Manual</label>
                        <?php echo e(Form::select('carrieManual',$carrierMan,null,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-general','id'=>'carrieManual'])); ?>

                    </div>
                    <div class="col-md-4" id="airline_label" style="display:none;">
                        <br>
                        <label>Airline</label>
                        <div class="form-group">
                          <?php echo e(Form::select('airline_id',$airlines,null,['class'=>'custom-select form-control','id' => 'airline_id','placeholder'=>'Choose an option'])); ?>

                      </div>
                    </div>
                </div>
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button data-toggle="modal" data-target="#createRateModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
                <?php echo Form::close(); ?>

            </div>
        </div>
    </div>
</div>