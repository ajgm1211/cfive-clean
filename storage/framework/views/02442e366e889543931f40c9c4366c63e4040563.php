<div class="row">
  <div class="col-md-12">
    <div class="">
      <div class=" ">
        <!-- Rates -->
        <?php
        $v=0;
        ?>
        <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="row">
          <div class="col-md-12">
            <div class="m-portlet custom-portlet no-border">
              <div class="m-portlet__body padding-portlet">
                <div class="tab-content">
                  <div class="flex-list" style=" margin-bottom:-30px; margin-top: 0;">
                    <ul >
                      <li style="max-height: 20px;">                                            
                        <?php if(isset($rate->carrier->image) && $rate->carrier->image!=''): ?>
                        <img src="<?php echo e(url('imgcarrier/'.$rate->carrier->image)); ?>"  class="img img-responsive" width="50" height="auto" style="margin-top: 10px;" />
                        <?php endif; ?>
                        <?php if(isset($rate->airline->image) && $rate->airline->image!=''): ?>
                        <img src="<?php echo e(url('imgcarrier/'.$rate->airline->image)); ?>"  class="img img-responsive" width="50" height="auto" style="margin-top: 10px;" />
                        <?php endif; ?>                                        
                      </li>
                      <li class="size-12px">POL: <?php if($quote->type=='LCL'): ?> <?php echo e($rate->origin_address != '' ? $rate->origin_address:$rate->origin_port->name.', '.$rate->origin_port->code); ?>  <?php else: ?> <?php echo e($rate->origin_address != '' ? $rate->origin_address:$rate->origin_airport->display_name); ?> <?php endif; ?> &nbsp;
                        <?php if($quote->type!='AIR'): ?>
                        <img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/<?php echo e($rate->origin_country_code); ?>.svg"/>
                        <?php endif; ?>
                      </li>
                      <li class="size-12px">POD: <?php if($quote->type=='LCL'): ?> <?php echo e($rate->destination_address != '' ? $rate->destination_address:$rate->destination_port->name.', '.$rate->destination_port->code); ?> <?php else: ?> <?php echo e($rate->destination_address != '' ? $rate->destination_address:$rate->destination_airport->display_name); ?> <?php endif; ?> &nbsp;
                        <?php if($quote->type!='AIR'): ?>
                        <img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/<?php echo e($rate->destination_country_code); ?>.svg"/>
                        <?php endif; ?>
                      </li>
                      <li class="size-12px">Contract: <?php echo e($rate->contract); ?></li>
                      <li class="size-12px no-border-left d-flex justify-content-end">
                        <div onclick="show_hide_element('details_<?php echo e($v); ?>')"><i class="fa fa-angle-down"></i></div>
                      </li>
                      <li class="size-12px">
                        <div class="delete-rate" data-rate-id="<?php echo e($rate->id); ?>" style="cursor:pointer;"><i class="fa fa-trash fa-4x"></i></div>
                      </li>
                    </ul>
                  </div>
                  <br>
                  <div class="details_<?php echo e($v); ?> hide" style="background-color: white; border-radius: 5px; margin-top: 20px;">
                    <!-- Freight charges -->
                    <div class="row no-mg-row">
                      <div class="col-md-12 header-charges">
                        <h5 class="title-quote size-12px">Freight charges</h5>
                      </div>
                      <div class="col-md-12 thead">
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered table-hover table color-blue text-center">
                            <thead class="title-quote text-center header-table">
                              <tr style="height: 40px;">
                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                <td class="td-table">Detail</td>
                                <td class="td-table">Units</td>
                                <td class="td-table">Rate</td>
                                <td class="td-table">Markup</td>
                                <td class="td-table">Total</td>
                                <td class="td-table">Currency</td>
                              </tr>
                            </thead>
                            <tbody style="background-color: white;">
                              <?php
                              $total_freight=0;
                              $total_origin=0;
                              $total_destination=0;
                              $total_freight_units=0;
                              $total_freight_rates=0;
                              $total_freight_markups=0;
                              ?>
                              <?php $__currentLoopData = $rate->charge_lcl_air; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php if($item->type_id==3): ?>
                              <?php
                              $rate_id=$item->automatic_rate_id;
                              $total_freight+=$item->total_freight;
                              $total_freight_units+=$item->units;
                              $total_freight_rates+=$item->price_per_unit*$item->units;
                              $total_freight_markups+=$item->markup;
                              ?>
                              <tr style="height:40px;">
                                <td class="tds" style="padding-left: 30px">
                                  <input name="charge_id" value="<?php echo e(@$item->id); ?>" class="form-control charge_id" type="hidden" style="max-width: 50px;"/>


                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($surcharges); ?>" data-type="select" data-name="surcharge_id" data-value="<?php echo e($item->surcharge_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select surcharge"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($calculation_types_lcl_air); ?>" data-type="select" data-name="calculation_type_id" data-value="<?php echo e($item->calculation_type_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select calculation type"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air units td-a" data-type="text" data-name="units" data-value="<?php echo e($item->units); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Units"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air price_per_unit td-a" data-type="text" data-name="price_per_unit" data-value="<?php echo e($item->price_per_unit); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Price per unit"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-type="text" data-name="markup" data-value="<?php echo e($item->markup); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Markup"></a>
                                </td>
                                <td class="tds">
                                  <?php echo e(($item->units*$item->price_per_unit)+$item->markup); ?>

                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($item->currency_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select currency"></a>
                                  &nbsp;
                                  <a class="delete-charge-lcl" style="cursor: pointer;" title="Delete">
                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                  </a>
                                </td>
                              </tr>
                              <?php endif; ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <!-- Hide Freight -->

                              <tr class="hide" id="freight_charges_<?php echo e($v); ?>">
                                <input name="type_id" value="3" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                <input name="automatic_rate_id" value="<?php echo e($rate->id); ?>" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                <td>
                                  <?php echo e(Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true])); ?>

                                </td>
                                <td>
                                  <?php echo e(Form::select('calculation_type_id[]',$calculation_types_lcl_air,null,['class'=>'form-control calculation_type_id','required'=>true])); ?>

                                </td>
                                <td >
                                  <input name="units" class="units form-control" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="price_per_unit" class="form-control price_per_unit" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="markup" class="form-control markup" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="total" class="form-control total_2" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <div class="input-group">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        <?php echo e(Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width'])); ?>

                                      </div>
                                      <a class="btn btn-xs btn-primary-plus store_charge_lcl">
                                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                      </a>
                                      <a class="btn btn-xs btn-primary-plus removeFreightCharge">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                      </a>
                                    </div>
                                  </div>                                                  
                                </td>
                              </tr>

                              <?php if($rate->id == @$rate_id ): ?>
                              <tr>
                                <td colspan="4" class="tds"></td>
                                <td class="title-quote size-12px tds" ><span class="td-a">Total</span></td>
                                <td class="tds"><b><span class="td-a"><?php echo e($total_freight); ?></span></b></td>
                                <td class="tds"><b><span class="td-a"> <?php echo e($currency_cfg->alphacode); ?></span></b></td>
                              </tr>
                              <?php endif; ?>

                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class='row'>
                      <div class="col-md-12">
                        <h5 class="title-quote pull-right">
                          <b>Add freight charge</b><a class="btn" onclick="addFreightCharge(<?php echo e($v); ?>)" style="vertical-align: middle">
                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                          </a>
                        </h5>
                      </div>
                    </div>

                    <!-- Origin charges -->
                    <div class="no-mg-row">
                      <div class="col-md-12 header-charges">
                        <h5 class="title-quote size-12px">Origin charges</h5>
                      </div>
                      <div class="col-md-12 thead">
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered color-blue text-center">
                            <thead class="title-quote text-center header-table">
                              <tr>
                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                <td class="td-table">Detail</td>
                                <td class="td-table">Units</td>
                                <td class="td-table">Rate</td>
                                <td class="td-table">Markup</td>
                                <td class="td-table">Total</td>
                                <td class="td-table">Currency</td>
                              </tr>
                            </thead>
                            <tbody style="background-color: white;">
                              <?php
                              $a=0;
                              $total_origin_units=0;
                              $total_origin_rates=0;
                              $total_origin_markups=0;
                              ?>
                              <?php $__currentLoopData = $rate->charge_lcl_air; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php if($item->type_id==1): ?>
                              <?php

                              $rate_id=$item->automatic_rate_id;
                              $total_origin+=$item->total_origin;
                              $total_origin_units+=$item->units;
                              $total_origin_rates+=$item->price_per_unit*$item->units;
                              $total_origin_markups+=$item->markup;

                              ?>
                              <tr style="height:40px;">
                                <td class="tds" style="padding-left: 30px">
                                  <input name="charge_id" value="<?php echo e(@$item->id); ?>" class="form-control charge_id" type="hidden" style="max-width: 50px;"/>


                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($surcharges); ?>" data-type="select" data-name="surcharge_id" data-value="<?php echo e($item->surcharge_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select surcharge"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($calculation_types_lcl_air); ?>" data-type="select" data-name="calculation_type_id" data-value="<?php echo e($item->calculation_type_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select calculation type"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air units td-a" data-type="text" data-name="units" data-value="<?php echo e($item->units); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Units"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air price_per_unit td-a" data-type="text" data-name="price_per_unit" data-value="<?php echo e($item->price_per_unit); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Price per unit"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-type="text" data-name="markup" data-value="<?php echo e($item->markup); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Markup"></a>
                                </td>
                                <td class="tds">
                                  <?php echo e(($item->units*$item->price_per_unit)+$item->markup); ?>

                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($item->currency_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select currency"></a>
                                  &nbsp;
                                  <a class="delete-charge-lcl" style="cursor: pointer;" title="Delete">
                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                  </a>
                                </td>
                              </tr>
                              <?php
                              $a++;
                              ?>
                              <?php endif; ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <!-- Hide origin charges-->

                              <tr class="hide" id="origin_charges_<?php echo e($v); ?>">
                                <input name="type_id" value="1" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                <input name="automatic_rate_id" value="<?php echo e($rate->id); ?>" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                <td>
                                  <?php echo e(Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true])); ?>

                                </td>
                                <td>
                                  <?php echo e(Form::select('calculation_type_id[]',$calculation_types_lcl_air,null,['class'=>'form-control calculation_type_id','required'=>true])); ?>

                                </td>
                                <td >
                                  <input name="units" class="units form-control" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="price_per_unit" class="form-control price_per_unit" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="markup" class="form-control markup" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="total" class="form-control total_2" type="number" min="0" step="0.0000001" />
                                </td>                                                
                                <td >
                                  <div class="input-group">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        <?php echo e(Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width'])); ?>

                                      </div>
                                      <a class="btn btn-xs btn-primary-plus store_charge_lcl">
                                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                      </a>
                                      <a class="btn btn-xs btn-primary-plus removeOriginCharge">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                      </a>
                                    </div>
                                  </div>                                                  
                                </td>
                              </tr>
                              <?php if($rate->id == @$rate_id ): ?>
                              <tr>
                                <td colspan="4" class="tds"></td>
                                <td class="title-quote size-12px tds" ><span class="td-a">Total</span></td>
                                <td class="tds"><b><span class="td-a"><?php echo e($total_origin); ?></span></b></td>
                                <td class="tds"><b><span class="td-a"> <?php echo e($currency_cfg->alphacode); ?></span></b></td>
                              </tr>
                              <?php endif; ?>                                              
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class='row'>
                      <div class="col-md-12">
                        <h5 class="title-quote pull-right">
                          <b>Add origin charge</b>
                          <a class="btn" onclick="addOriginCharge(<?php echo e($v); ?>)" style="vertical-align: middle">
                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                          </a>
                        </h5>
                      </div>
                    </div>

                    <!-- Destination charges -->

                    <div class="no-mg-row">
                      <div class="col-md-12 header-charges">
                        <h5 class="title-quote size-12px">Destination charges</h5>
                      </div>
                      <div class="col-md-12 thead">
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered color-blue text-center">
                            <thead class="title-quote text-center header-table">
                              <tr>
                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                <td class="td-table">Detail</td>
                                <td class="td-table">Units</td>
                                <td class="td-table">Rate</td>
                                <td class="td-table">Markup</td>
                                <td class="td-table">Total</td>
                                <td class="td-table">Currency</td>
                              </tr>
                            </thead>
                            <tbody style="background-color: white;">
                              <?php
                              $a=0;
                              $total_destination_units=0;
                              $total_destination_rates=0;
                              $total_destination_markups=0;
                              ?>

                              <?php $__currentLoopData = $rate->charge_lcl_air; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php if($item->type_id==2): ?>
                              <?php
                              $rate_id=$item->automatic_rate_id;
                              $total_destination+=$item->total_destination;
                              $total_destination_units+=$item->units;
                              $total_destination_rates+=$item->price_per_unit*$item->units;
                              $total_destination_markups+=$item->markup;
                              ?>                                                   

                              <tr style="height:40px;">
                                <td class="tds" style="padding-left: 30px">
                                  <input name="charge_id" value="<?php echo e(@$item->id); ?>" class="form-control charge_id" type="hidden" style="max-width: 50px;"/>


                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($surcharges); ?>" data-type="select" data-name="surcharge_id" data-value="<?php echo e($item->surcharge_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select surcharge"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($calculation_types_lcl_air); ?>" data-type="select" data-name="calculation_type_id" data-value="<?php echo e($item->calculation_type_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select calculation type"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air units td-a" data-type="text" data-name="units" data-value="<?php echo e($item->units); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Units"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air price_per_unit td-a" data-type="text" data-name="price_per_unit" data-value="<?php echo e($item->price_per_unit); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Price per unit"></a>
                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-type="text" data-name="markup" data-value="<?php echo e($item->markup); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Markup"></a>
                                </td>
                                <td class="tds">
                                  <?php echo e(($item->units*$item->price_per_unit)+$item->markup); ?>

                                </td>
                                <td class="tds">
                                  <a href="#" class="editable-lcl-air td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($item->currency_id); ?>" data-pk="<?php echo e(@$item->id); ?>" data-title="Select currency"></a>
                                  &nbsp;
                                  <a class="delete-charge-lcl" style="cursor: pointer;" title="Delete">
                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                  </a>
                                </td>
                              </tr>
                              <?php
                              $a++;
                              ?>
                              <?php endif; ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <!-- Hide destination charges -->

                              <tr class="hide" id="destination_charges_<?php echo e($v); ?>">
                                <input name="type_id" value="2" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                <input name="automatic_rate_id" value="<?php echo e($rate->id); ?>" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                <td>
                                  <?php echo e(Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true])); ?>

                                </td>
                                <td>
                                  <?php echo e(Form::select('calculation_type_id[]',$calculation_types_lcl_air,null,['class'=>'form-control calculation_type_id','required'=>true])); ?>

                                </td>
                                <td >
                                  <input name="units" class="units form-control" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="price_per_unit" class="form-control price_per_unit" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="markup" class="form-control markup" type="number" min="0" step="0.0000001" />
                                </td>
                                <td >
                                  <input name="total" class="form-control total_2" type="number" min="0" step="0.0000001"  />
                                </td>                                                
                                <td >
                                  <div class="input-group">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                       <?php echo e(Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width'])); ?>

                                     </div>
                                     <a class="btn btn-xs btn-primary-plus store_charge_lcl">
                                      <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                    </a>
                                    <a class="btn btn-xs btn-primary-plus removeOriginCharge">
                                      <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                    </a>
                                  </div>
                                </div>                                                  
                              </td>                                                
                            </tr>
                            <?php if($rate->id == @$rate_id ): ?>
                            <tr>
                              <td colspan="4" class="tds"></td>
                              <td class="title-quote size-12px tds" ><span class="td-a">Total</span></td>
                              <td class="tds"><b><span class="td-a"><?php echo e($total_destination); ?></span></b></td>
                              <td class="tds"><b><span class="td-a"> <?php echo e($currency_cfg->alphacode); ?></span></b></td>
                            </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class='row'>
                    <div class="col-md-12">
                      <h5 class="title-quote pull-right">
                        <b>Add destination charge</b>
                        <a class="btn" onclick="addDestinationCharge(<?php echo e($v); ?>)" style="vertical-align: middle">
                          <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                        </a>
                      </h5>
                    </div>
                  </div>
                  <br>
                  <!-- Total -->
                  <?php if($rate->id == @$rate_id ): ?>
                  <div class="row no-mg-row">
                    <div class="col-md-12 header-charges">
                      <h5 class="title-quote size-12px">Totals</h5>
                    </div>
                    <div class="col-md-12 thead">
                      <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover table color-blue text-center">
                          <thead class="title-quote text-center header-table">
                            <tr style="height:40px;">
                              <td class="tds" style="padding-left: 30px"></td>
                              <td class="tds">Units * Rate</td>
                              <td class="tds">Markups</td>
                              <td class="tds">Total</td>
                              <td class="tds">Currency</td>
                            </tr>
                          </thead>
                          <tbody style="background-color: white;">
                            <?php
                            $total_units=number_format($total_freight_units+$total_origin_units+$total_destination_units, 2, '.', '');
                            $total_rates=number_format($total_freight_rates+$total_origin_rates+$total_destination_rates, 2, '.', '');
                            $total_markups=number_format($total_freight_markups+$total_origin_markups+$total_destination_markups, 2, '.', '');
                            ?>
                            <tr style="height:40px;">
                              <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>

                              <td class="tds"><span class="bg-rates td-a"><?php echo e($total_rates); ?></td>
                                <td class="tds"><span class="bg-rates td-a"><?php echo e($total_markups); ?></td>
                                  <td class="tds"><span class="bg-rates td-a"><?php echo e($total_freight+$total_origin+$total_destination); ?></td>
                                    <td class="tds"><span class="td-a"><?php echo e($currency_cfg->alphacode); ?></span></td>
                                  </tr>
                                </tbody>
                              </table>
                              <br>
                              <br>
                              <br>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>
                        <!-- Inlands -->
                        <?php if(!$rate->inland->isEmpty()): ?>
                        <div class="row" >
                          <div class="col-md-12">
                            <br>
                            <h5 class="title-quote size-12px">Inland charges</h5>
                            <hr>
                            <div class="">
                              <div class="">
                                <?php
                                $x=0;
                                ?>
                                <?php $__currentLoopData = $rate->inland; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inland): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                $inland_rates = json_decode($inland->rate,true);
                                $inland_markups = json_decode($inland->markup,true);
                                ?>
                                <div class="tab-content">
                                  <div class="flex-list">
                                    <ul >
                                      <li ><i class="fa fa-truck" style="font-size: 2rem"></i></li>
                                      <li class="size-12px">From: <?php echo e($rate->origin_address != '' ? $rate->origin_address:$rate->origin_port->name); ?> &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/<?php echo e(strtolower(substr($rate->origin_port->code, 0, 2))); ?>.svg"></li>
                                      <li class="size-12px">To: <?php echo e($rate->destination_address != '' ? $rate->destination_address:$rate->destination_port->name); ?> &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/<?php echo e(strtolower(substr($rate->destination_port->code, 0, 2))); ?>.svg"></li>
                                      <li class="size-12px">Contract: <?php echo e($inland->contract); ?></li>
                                      <li class="size-12px">
                                        <div onclick="show_hide_element('details_inland_<?php echo e($x); ?>')"><i class="down"></i></div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="details_inland_<?php echo e($x); ?> hide">
                                    <table class="table table-sm table-bordered color-blue text-center">
                                      <thead class="title-quote text-center header-table">
                                        <tr>
                                          <td >Charge</td>
                                          <td >Distance</td>
                                          <td <?php echo e(@$equipmentHides['20']); ?> >20'</td>
                                          <td <?php echo e(@$equipmentHides['40']); ?> >40'</td>
                                          <td <?php echo e(@$equipmentHides['40hc']); ?> >40HC'</td>
                                          <td <?php echo e(@$equipmentHides['40nor']); ?> >40NOR'</td>
                                          <td <?php echo e(@$equipmentHides['45']); ?> >45'</td>
                                          <td >Currency</td>
                                        </tr>
                                      </thead>
                                      <tbody style="background-color: white;">
                                        <tr >
                                          <td>
                                            <a href="#" class="editable-inland provider" data-type="text" data-value="<?php echo e($inland->provider); ?>" data-name="provider" data-pk="<?php echo e(@$inland->id); ?>" data-title="Provider"></a>
                                          </td>
                                          <td>
                                            <a href="#" class="editable-inland distance" data-type="text" data-name="distance" data-value="<?php echo e(@$inland->distance); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Distance"></a> &nbsp;km
                                          </td>
                                          <td <?php echo e(@$equipmentHides['20']); ?>>
                                            <a href="#" class="editable-inland-20 amount_20" data-type="text" data-name="rate->c20" data-value="<?php echo e(@$inland_rates['c20']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Amount"></a>
                                            +
                                            <a href="#" class="editable-inland-m20 markup_20" data-type="text" data-name="markup->c20" data-value="<?php echo e(@$inland_markups['c20']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Markup"></a>
                                            <i class="la la-caret-right arrow-down"></i> 
                                            <span class="total_20"><?php echo e(@$inland_rates['c20']+@$inland_markups['c20']); ?></span>
                                          </td>
                                          <td <?php echo e(@$equipmentHides['40']); ?>>
                                            <a href="#" class="editable-inland-40 amount_40" data-type="text" data-name="rate->c40" data-value="<?php echo e(@$inland_rates['c40']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            +
                                            <a href="#" class="editable-inland-m40 markup_40"data-type="text" data-name="markup->c40" data-value="<?php echo e(@$inland_markups['c40']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            <i class="la la-caret-right arrow-down"></i> 
                                            <span class="total_40"><?php echo e(@$inland_rates['c40']+@$inland_markups['c40']); ?></span>
                                          </td>
                                          <td <?php echo e(@$equipmentHides['40hc']); ?>>
                                            <a href="#" class="editable-inland-40hc amount_40hc" data-type="text" data-name="rate->c40hc" data-value="<?php echo e(@$inland_amounts['c40hc']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            +
                                            <a href="#" class="editable-inland-m40hc markup_40hc" data-type="text" data-name="markup->c40hc" data-value="<?php echo e(@$inland_markups['c40hc']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            <i class="la la-caret-right arrow-down"></i> 
                                            <span class="total_40hc"><?php echo e(@$inland_amounts['c40hc']+@$inland_markups['c40hc']); ?></span>
                                          </td>
                                          <td <?php echo e(@$equipmentHides['40nor']); ?>>
                                            <a href="#" class="editable-inland-40nor amount_40nor " data-type="text" data-name="rate->c40nor" data-value="<?php echo e(@$inland_amounts['c40nor']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            +
                                            <a href="#" class="editable-inland-m40nor markup_40nor" data-type="text" data-name="markup->c40nor" data-value="<?php echo e(@$inland_markups['c40nor']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            <i class="la la-caret-right arrow-down"></i> 
                                            <span class="total_40nor"><?php echo e(@$inland_amounts['c40nor']+@$inland_markups['c40nor']); ?></span>
                                          </td>
                                          <td <?php echo e(@$equipmentHides['45']); ?>>
                                            <a href="#" class="editable-inland-45 amount_45" data-type="text" data-name="rate->45" data-value="<?php echo e(@$inland_amounts['c45']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            +
                                            <a href="#" class="editable-inland-m45 markup_45" data-type="text" data-name="markup->c45" data-value="<?php echo e(@$inland_markups['c45']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                            <i class="la la-caret-right arrow-down"></i> 
                                            <span class="total_45"><?php echo e(@$inland_amounts['c45']+@$inland_markups['c45']); ?></span>
                                          </td>
                                          <td>
                                            <a href="#" class="editable-inland" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($inland->currency_id); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Select currency"></a>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>

                                    <div class='row'>
                                      <div class="col-md-12">
                                        <h5 class="title-quote pull-right">
                                          <b>Add inland charge</b>
                                          <a class="btn" onclick="addInlandCharge(1)" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                          </a>
                                        </h5>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <br>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>

                        <!-- Remarks -->
                        <div class="row">
                          <div class="col-md-12">
                            <div class="m-portlet" style="box-shadow: none;">
                              <div class="m-portlet__head">
                                <div class="row" style="padding-top: 20px;">
                                  <h5 class="title-quote size-12px">Remarks</h5>
                                </div>
                                <div class="m-portlet__head-tools">
                                  <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                    <li class="nav-item m-tabs__item" id="edit_li">
                                      <button class="btn btn-primary-v2 edit-remarks btn-edit" onclick="edit_remark('remarks_span_<?php echo e($v); ?>','remarks_textarea_<?php echo e($v); ?>','update_remarks_<?php echo e($v); ?>')">
                                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                      </button>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                              <div class="m-portlet__body">
                                <div class="card card-body bg-light remarks_span_<?php echo e($v); ?>">
                                  <span><?php echo $rate->remarks; ?></span>
                                </div>
                                <div class="remarks_textarea_<?php echo e($v); ?>" hidden>
                                  <textarea name="remarks_<?php echo e($v); ?>" class="form-control remarks_<?php echo e($v); ?> editor"><?php echo $rate->remarks; ?></textarea>
                                </div>
                                <div class="row">
                                  <div class="col-md-12 text-center update_remarks_<?php echo e($v); ?>"  hidden>
                                    <br>
                                    <button class="btn btn-danger cancel-remarks_<?php echo e($v); ?>" onclick="cancel_update('remarks_span_<?php echo e($v); ?>','remarks_textarea_<?php echo e($v); ?>','update_remarks_<?php echo e($v); ?>')">
                                      Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </button>
                                    <button class="btn btn-primary update-remarks_<?php echo e($v); ?>" onclick="update_remark(<?php echo e($rate->id); ?>,'remarks_<?php echo e($v); ?>',<?php echo e($v); ?>)">
                                      Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </button>
                                    <br>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
            $v++;
            ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          </div>
        </div>
      </div>
</div>