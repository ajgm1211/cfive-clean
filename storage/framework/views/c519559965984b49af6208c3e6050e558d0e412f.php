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
                                                    <img src="<?php echo e(url('imgcarrier/'.$rate->carrier->image)); ?>"  class="img img-responsive" width="45" height="auto" style="margin-top: 15px;" />
                                                <?php endif; ?>
                                            </li>
                                            <li class="size-12px">POL: <?php echo e($rate->origin_address != '' ? $rate->origin_address:$rate->origin_port->name.', '.$rate->origin_port->code); ?> &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/<?php echo e($rate->origin_country_code); ?>.svg"/></li>
                                            <li class="size-12px">POD: <?php echo e($rate->destination_address != '' ? $rate->destination_address:$rate->destination_port->name.', '.$rate->destination_port->code); ?> &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/<?php echo e($rate->destination_country_code); ?>.svg"/></li>
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
                                                          <td class="td-table" <?php echo e(@$equipmentHides['20']); ?>>20'</td>
                                                          <td class="td-table" <?php echo e(@$equipmentHides['40']); ?>>40'</td>
                                                          <td class="td-table" <?php echo e(@$equipmentHides['40hc']); ?>>40HC'</td>
                                                          <td class="td-table" <?php echo e(@$equipmentHides['40nor']); ?>>40NOR'</td>
                                                          <td class="td-table" <?php echo e(@$equipmentHides['45']); ?>>45'</td>
                                                          <td class="td-table" >Currency</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                        <?php

                                                            $i=0;
                                                            $sum20=0;
                                                            $sum40=0;
                                                            $sum40hc=0;
                                                            $sum40nor=0;
                                                            $sum45=0;

                                                            $sum_m20=0;
                                                            $sum_m40=0;
                                                            $sum_m40hc=0;
                                                            $sum_m40nor=0;
                                                            $sum_m45=0;

                                                            $rate_amounts = json_decode($rate->rates,true);
                                                            $rate_markups = json_decode($rate->markups,true);

                                                        ?>
                                                        <tr style="height:40px;">
                                                          <td class="tds" style="padding-left: 30px">
                                                                <input name="charge_id" value="<?php echo e($rate->id); ?>" class="form-control charge_id td-a" type="hidden" style="max-width: 50px;"/>
                                                                Ocean freight
                                                            </td>
                                                            <td class="tds">
                                                               Per Container
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['20']); ?> class="tds">
                                                                <a href="#" class="editable-rate-amount-20 amount_20 bg-rates td-a" data-type="text" data-name="rates->c20" data-value="<?php echo e(@$rate_amounts['c20']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>
                                                                +
                                                                <a href="#" class="editable-rate-markup-20 markup_20 bg-rates td-a"data-type="text" data-name="markups->m20" data-value="<?php echo e(@$rate_markups['m20']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>
                                                                <i class="la la-caret-right arrow-down"></i> 
                                                                <span class="total_20"><?php echo e(@$rate_amounts['c20']+@$rate_markups['m20']); ?></span>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40']); ?> class="tds">
                                                                <a href="#" class="editable-rate-amount-40 amount_40 bg-rates td-a" data-type="text" data-name="rates->c40" data-value="<?php echo e(@$rate_amounts['c40']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>
                                                                +
                                                                <a href="#" class="editable-rate-markup-40 markup_40 bg-rates td-a" data-type="text" data-name="markups->m40" data-value="<?php echo e(@$rate_markups['m40']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>

                                                                <i class="la la-caret-right arrow-down"></i> 
                                                                <span class="total_40"><?php echo e(@$rate_amounts['c40']+@$rate_markups['m40']); ?></span>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds">
                                                                <a href="#" class="editable-rate-amount-40hc amount_40hc bg-rates td-a" data-type="text" data-name="rates->c40hc" data-value="<?php echo e(@$rate_amounts['c40hc']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>
                                                                +
                                                                <a href="#" class="editable-rate-markup-40hc markup_40hc bg-rates td-a" data-type="text" data-name="markups->m40hc" data-value="<?php echo e(@$rate_markups['m40hc']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>
                                                                <i class="la la-caret-right arrow-down"></i> 
                                                                <span class="total_40hc"><?php echo e(@$rate_amounts['c40hc']+@$rate_markups['m40hc']); ?></span>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds">
                                                                <a href="#" class="editable-rate-amount-40nor amount_40nor bg-rates td-a" data-type="text" data-name="rates->c40nor" data-value="<?php echo e(@$rate_amounts['c40nor']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>
                                                                +
                                                                <a href="#" class="editable-rate-markup-40nor markup_40nor bg-rates td-a" data-type="text" data-name="markups->m40nor" data-value="<?php echo e(@$rate_markups['m40nor']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>

                                                                <i class="la la-caret-right arrow-down"></i> 
                                                                <span class="total_40nor"><?php echo e(@$rate_amounts['c40nor']+@$rate_markups['m40nor']); ?></span>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['45']); ?> class="tds">
                                                                <a href="#" class="editable-rate-amount-45 amount_45 bg-rates td-a" data-type="text" data-name="rates->c45" data-value="<?php echo e(@$rate_amounts['c45']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>
                                                                +
                                                                <a href="#" class="editable-rate-markup-45 markup_45 bg-rates td-a" data-type="text" data-name="markups->m45" data-value="<?php echo e(@$rate_markups['m45']); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Total"></a>

                                                                <i class="la la-caret-right arrow-down"></i> 
                                                                <span class="total_45"><?php echo e(@$rate_amounts['c45']+@$rate_markups['m45']); ?></span>
                                                            </td>
                                                            <td class="tds">
                                                                <a href="#" class="editable td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($rate->currency_id); ?>" data-pk="<?php echo e($rate->id); ?>" data-title="Select currency"></a>
                                                                &nbsp;
                                                                <a class="delete-charge" style="cursor: pointer;" title="Delete">
                                                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                                </a>
                                                            </td>
                                                        </tr>                                                        
                                                        <?php $__currentLoopData = $rate->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($item->type_id==3): ?>
                                                                <?php
                                                                    $rate_id=$item->automatic_rate_id;
                                                                    
                                                                    $freight_amounts = json_decode($item->amount,true);
                                                                    $freight_markups = json_decode($item->markups,true);
                                                                    
                                                                    $sum20+=$item->total_20;
                                                                    $sum40+=@$item->total_40;
                                                                    $sum40hc+=@$item->total_40hc;
                                                                    $sum40nor+=@$item->total_40nor;
                                                                    $sum45+=@$item->total_45;

                                                                    $sum_m20+=$item->total_markup20;
                                                                    $sum_m40+=@$item->total_markup40;
                                                                    $sum_m40hc+=@$item->total_markup40hc;
                                                                    $sum_m40nor+=@$item->total_markup40nor;
                                                                    $sum_m45+=@$item->total_markup45;                                                                    
                                                                ?>
                                                                <tr style="height:40px;">
                                                                  <td class="tds" style="padding-left: 30px">
                                                                        <input name="charge_id" value="<?php echo e($item->id); ?>" class="form-control charge_id" type="hidden" />

                                                                        <a href="#" class="editable td-a" data-source="<?php echo e($surcharges); ?>" data-type="select" data-name="surcharge_id" data-value="<?php echo e($item->surcharge_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select surcharge"></a>
                                                                    </td>
                                                                    <td class="tds">
                                                                        <a href="#" class="editable td-a" data-source="<?php echo e($calculation_types); ?>" data-type="select" data-name="calculation_type_id" data-value="<?php echo e($item->calculation_type_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select calculation type"></a>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['20']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-20 amount_20 bg-rates td-a" data-type="text" data-name="amount->c20" data-value="<?php echo e(@$freight_amounts['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-20 markup_20 bg-rates td-a" data-type="text" data-name="markups->c20" data-value="<?php echo e(@$freight_markups['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_20"><?php echo e(@$freight_amounts['c20']+@$freight_markups['c20']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40 amount_40 bg-rates td-a" data-type="text" data-name="amount->c40" data-value="<?php echo e(@$freight_amounts['c40']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40 markup_40 bg-rates td-a" data-type="text" data-name="markups->c40" data-value="<?php echo e(@$freight_markups['c40']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40"><?php echo e(@$freight_amounts['c40']+@$freight_markups['c40']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40hc amount_40hc bg-rates td-a" data-type="text" data-name="amount->c40hc" data-value="<?php echo e(@$freight_amounts['c40hc']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40hc markup_40hc bg-rates td-a" data-type="text" data-name="markups->c40hc" data-value="<?php echo e(@$freight_markups['c40hc']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40hc"><?php echo e(@$freight_amounts['c40hc']+@$freight_markups['c40hc']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40nor amount_40nor bg-rates td-a" data-type="text" data-name="amount->c40nor" data-value="<?php echo e(@$freight_amounts['c40nor']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40nor markup_40nor bg-rates td-a" data-type="text" data-name="markups->c40nor" data-value="<?php echo e(@$freight_markups['c40nor']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40nor"><?php echo e(@$freight_amounts['c40nor']+@$freight_markups['c40nor']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['45']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-45 amount_45 bg-rates td-a" data-type="text" data-name="amount->c45" data-value="<?php echo e(@$freight_amounts['c45']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-45 markup_45 bg-rates td-a" data-type="text" data-name="markups->c45" data-value="<?php echo e(@$freight_markups['c45']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_45"><?php echo e(@$freight_amounts['c45']+@$freight_markups['c45']); ?></span>
                                                                    </td>
                                                                    <td class="tds">
                                                                        <a href="#" class="editable td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($item->currency_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select currency"></a>
                                                                        &nbsp;
                                                                        <a class="delete-charge" style="cursor: pointer;" title="Delete">
                                                                            <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                    $i++;
                                                                ?>
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
                                                                <?php echo e(Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true])); ?>

                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['20']); ?>>
                                                                <div class="row ">
                                                                    <div class="col-6">
                                                                        <input name="amount_c20" class="amount_c20 form-control" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c20" class="form-control markup_c20" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40" class="form-control markup_c40" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40hc']); ?>>
                                                                 <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40hc" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" style="max-width: 100px;"/>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40hc" class="form-control markup_c40hc" type="number" min="0" step="0.0000001" style="max-width: 100px;"/>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40nor']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40nor" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" style="max-width: 100px;"/>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40nor" class="form-control markup_c40nor" type="number" min="0" step="0.0000001" style="max-width: 100px;"/>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['45']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">                                                                
                                                                        <input name="amount_c45" class="form-control amount_c45" type="number" min="0" step="0.0000001" style="max-width: 100px;"/>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c45" class="form-control markup_c45" type="number" min="0" step="0.0000001" style="max-width: 100px;" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <div class="input-group-btn">
                                                                        <div class="btn-group">
                                                                            <?php echo e(Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width'])); ?>

                                                                        </div>
                                                                        <a class="btn btn-xs btn-primary-plus store_charge">
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
                                                                <td></td>
                                                                <td class="title-quote size-12px tds" colspan=""><span class="td-a">Total</span></td>
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum20+@$sum_m20+@$rate->total_rate20, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum40+@$sum_m40+@$rate->total_rate40, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum40hc+@$sum_m40hc+@$rate->total_rate40hc, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum40nor+@$sum_m40nor+@$rate->total_rate40nor, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum45+@$sum_m45+@$rate->total_rate45, 2, '.', '')); ?></span></td>
                                                                <td class="tds"><span class="td-a"><?php echo e($rate->currency->alphacode); ?></span></td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td></td>
                                                                <td class="title-quote size-12px tds" colspan=""><span class="td-a">Total</span></td>
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$rate->total_rate20, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$rate->total_rate40, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$rate->total_rate40hc, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$rate->total_rate40nor, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$rate->total_rate45, 2, '.', '')); ?></span></td>
                                                                <td class="tds"><span class="td-a"><?php echo e($rate->currency->alphacode); ?></span></td>
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

                                        <div class="row no-mg-row">
                                            <div class="col-md-12 header-charges">
                                                <h5 class="title-quote size-12px">Origin charges</h5>
                                            </div>
                                            <div class="col-md-12 thead">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                        <tr style="height: 40px;">
                                                            <td class="td-table" style="padding-left: 30px">Charge</td>
                                                            <td class="td-table">Detail</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['20']); ?>>20'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['40']); ?>>40'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['40hc']); ?>>40HC'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['40nor']); ?>>40NOR'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['45']); ?>>45'</td>
                                                            <td class="td-table" >Currency</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                        <?php
                                                            $a=0;
                                                            $sum_origin_20=0;
                                                            $sum_origin_40=0;
                                                            $sum_origin_40hc=0;
                                                            $sum_origin_40nor=0;
                                                            $sum_origin_45=0;
                                                            $sum_origin_m20=0;
                                                            $sum_origin_m40=0;
                                                            $sum_origin_m40hc=0;
                                                            $sum_origin_m40nor=0;
                                                            $sum_origin_m45=0;                                                            
                                                        ?>
                                                        <?php $__currentLoopData = $rate->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($item->type_id==1): ?>
                                                                <?php
                                                                    $rate_id=$item->automatic_rate_id;
                                                                    $origin_amounts = json_decode($item->amount,true);
                                                                    $origin_markups = json_decode($item->markups,true);
                                                            
                                                                    $sum_origin_20+=@$item->total_20;
                                                                    $sum_origin_40+=@$item->total_40;
                                                                    $sum_origin_40hc+=@$item->total_40hc;
                                                                    $sum_origin_40nor+=@$item->total_40nor;
                                                                    $sum_origin_45+=@$item->total_45;

                                                                    $sum_origin_m20+=@$item->total_markup20;
                                                                    $sum_origin_m40+=@$item->total_markup40;
                                                                    $sum_origin_m40hc+=@$item->total_markup40hc;
                                                                    $sum_origin_m40nor+=@$item->total_markup40nor;
                                                                    $sum_origin_m45+=@$item->total_markup45;                                                                    
                                                                ?>
                                                                <tr style="height:40px;">
                                                                    <td class="tds" style="padding-left: 30px">
                                                                        <input name="charge_id td-a" value="<?php echo e($item->id); ?>" class="form-control charge_id" type="hidden" />

                                                                        <a href="#" class="editable surcharge_id td-a" data-source="<?php echo e($surcharges); ?>" data-type="select" data-value="<?php echo e($item->surcharge_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select surcharge"></a>
                                                                    </td>
                                                                    <td class="tds">
                                                                        <a href="#" class="editable calculation_type_id td-a" data-source="<?php echo e($calculation_types); ?>" data-name="calculation_type_id" data-type="select" data-value="<?php echo e($item->calculation_type_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select calculation type"></a>
                                                                    </td>
                                                                   <td <?php echo e(@$equipmentHides['20']); ?> class="tds">
                                                                        <!--<div class="row">
                                                                            <div class="col-8 text-center">
                                                                                <a href="#" class="editable-amount-20 amount_20 bg-rates" data-type="text" data-name="amount->c20" data-value="<?php echo e(@$origin_amounts['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                                +
                                                                                <a href="#" class="editable-markup-20 markup_20 bg-rates"data-type="text" data-name="markups->c20" data-value="<?php echo e(@$origin_markups['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                            </div>
                                                                            <div class="col-auto text-left">
                                                                                <i class="la la-caret-right arrow-down"></i> 
                                                                                <span class="total_20"><?php echo e(@$origin_amounts['c20']+@$origin_markups['c20']); ?></span>
                                                                            </div>
                                                                        </div>-->
                                                                        <a href="#" class="editable-amount-20 amount_20 bg-rates td-a" data-type="text" data-name="amount->c20" data-value="<?php echo e(@$origin_amounts['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-20 markup_20 bg-rates td-a" data-type="text" data-name="markups->c20" data-value="<?php echo e(@$origin_markups['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_20"><?php echo e(@$origin_amounts['c20']+@$origin_markups['c20']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40 amount_40 bg-rates td-a" data-type="text" data-name="amount->c40" data-value="<?php echo e(@$origin_amounts['c40']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40 markup_40 bg-rates td-a" data-type="text" data-name="markups->c40" data-value="<?php echo e(@$origin_markups['c40']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40"><?php echo e(@$origin_amounts['c40']+@$origin_markups['c40']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40hc amount_40hc bg-rates td-a" data-type="text" data-name="amount->c40hc" data-value="<?php echo e(@$origin_amounts['c40hc']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40hc markup_40hc bg-rates td-a" data-type="text" data-name="markups->c40hc" data-value="<?php echo e(@$origin_markups['c40hc']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40hc"><?php echo e(@$origin_amounts['c40hc']+@$origin_markups['c40hc']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40nor amount_40nor bg-rates td-a" data-type="text" data-name="amount->c40nor" data-value="<?php echo e(@$origin_amounts['c40nor']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40nor markup_40nor bg-rates td-a" data-type="text" data-name="markups->c40nor" data-value="<?php echo e(@$origin_markups['c40nor']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40nor"><?php echo e(@$origin_amounts['c40nor']+@$origin_markups['c40nor']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['45']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-45 amount_45 bg-rates td-a" data-type="text" data-name="amount->c45" data-value="<?php echo e(@$origin_amounts['c45']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-45 markup_45 bg-rates td-a" data-type="text" data-name="markups->c45" data-value="<?php echo e(@$origin_markups['c45']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_45"><?php echo e(@$origin_amounts['c45']+@$origin_markups['c45']); ?></span>
                                                                    </td>
                                                                    <td class="tds">
                                                                        <a href="#" class="editable td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($item->currency_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select currency"></a>
                                                                        &nbsp;
                                                                        <a class="delete-charge" style="cursor: pointer;" title="Delete">
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
                                                                <?php echo e(Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true])); ?>

                                                            </td>
                                                           <td <?php echo e(@$equipmentHides['20']); ?>>
                                                                <div class="row ">
                                                                    <div class="col-6">
                                                                        <input name="amount_c20" class="amount_c20 form-control" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c20" class="form-control markup_c20" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40" class="form-control markup_c40" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40hc']); ?>>
                                                                 <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40hc" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40hc" class="form-control markup_c40hc" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40nor']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40nor" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40nor" class="form-control markup_c40nor" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['45']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">                                                                
                                                                        <input name="amount_c45" class="form-control amount_c45" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c45" class="form-control markup_c45" type="number" min="0" step="0.0000001"  />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <div class="input-group-btn">
                                                                        <div class="btn-group">
                                                                            <?php echo e(Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width'])); ?>

                                                                        </div>
                                                                        <a class="btn btn-xs btn-primary-plus store_charge">
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
                                                                <td></td>
                                                                <td class="title-quote size-12px tds" colspan=""><span class="td-a">Total</span></td>
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_origin_20+@$sum_origin_m20, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_origin_40+@$sum_origin_m40, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_origin_40hc+@$sum_origin_m40hc, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_origin_40nor+@$sum_origin_m40nor, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_origin_45+@$sum_origin_m45, 2, '.', '')); ?></span></td>
                                                                <td class="tds"><span class="td-a"><?php echo e($currency_cfg->alphacode); ?></span></td>
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

                                        <div class="row no-mg-row">
                                            <div class="col-md-12 header-charges">
                                                <h5 class="title-quote size-12px">Destination charges</h5>
                                            </div>
                                            <div class="col-md-12 thead">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                        <tr style="height: 40px;">
                                                            <td class="td-table" style="padding-left: 30px">Charge</td>
                                                            <td class="td-table">Detail</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['20']); ?>>20'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['40']); ?>>40'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['40hc']); ?>>40HC'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['40nor']); ?>>40NOR'</td>
                                                            <td class="td-table" <?php echo e(@$equipmentHides['45']); ?>>45'</td>
                                                            <td class="td-table" >Currency</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                        <?php
                                                            $a=0;
                                                            $sum_destination_20=0;
                                                            $sum_destination_40=0;
                                                            $sum_destination_40hc=0;
                                                            $sum_destination_40nor=0;
                                                            $sum_destination_45=0;
                                                            $sum_destination_m20=0;
                                                            $sum_destination_m40=0;
                                                            $sum_destination_m40hc=0;
                                                            $sum_destination_m40nor=0;
                                                            $sum_destination_m45=0;                                                            
                                                        ?>

                                                        <?php $__currentLoopData = $rate->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($item->type_id==2): ?>
                                                                <?php
                                                                    $rate_id=$item->automatic_rate_id;
                                                                    $destination_amounts = json_decode($item->amount,true);
                                                                    $destination_markups = json_decode($item->markups,true);

                                                                    $sum_destination_20+=@$item->total_20;
                                                                    $sum_destination_40+=@$item->total_40;
                                                                    $sum_destination_40hc+=@$item->total_40hc;
                                                                    $sum_destination_40nor+=@$item->total_40nor;
                                                                    $sum_destination_45+=@$item->total_45;

                                                                    $sum_destination_m20+=@$item->total_markup20;
                                                                    $sum_destination_m40+=@$item->total_markup40;
                                                                    $sum_destination_m40hc+=@$item->total_markup40hc;
                                                                    $sum_destination_m40nor+=@$item->total_markup40nor;
                                                                    $sum_destination_m45+=@$item->total_markup45;
                                                                ?>                                                     

                                                                <tr style="height:40px;">
                                                                    <td class="tds" style="padding-left: 30px">
                                                                        <input name="charge_id" value="<?php echo e($item->id); ?>" class="form-control charge_id td-a" type="hidden" />

                                                                        <a href="#" class="editable surcharge_id td-a" data-source="<?php echo e($surcharges); ?>" data-type="select" data-value="<?php echo e($item->surcharge_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select surcharge"></a>
                                                                    </td>
                                                                    <td class="tds">
                                                                        <a href="#" class="editable calculation_type_id td-a" data-source="<?php echo e($calculation_types); ?>" data-name="calculation_type_id" data-type="select" data-value="<?php echo e($item->calculation_type_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select calculation type"></a>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['20']); ?> class="tds">
                                                                        <!--<div class="row">
                                                                            <div class="col-8 text-center">
                                                                                <a href="#" class="editable-amount-20 amount_20 bg-rates" data-type="text" data-name="amount->c20" data-value="<?php echo e(@$destination_amounts['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                                +
                                                                                <a href="#" class="editable-markup-20 markup_20 bg-rates"data-type="text" data-name="markups->c20" data-value="<?php echo e(@$destination_markups['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                            </div>
                                                                            <div class="col-auto text-left">
                                                                                <i class="la la-caret-right arrow-down"></i> 
                                                                                <span class="total_20"><?php echo e(@$destination_amounts['c20']+@$destination_markups['c20']); ?></span>
                                                                            </div>
                                                                        </div>-->
                                                                        <a href="#" class="editable-amount-20 amount_20 bg-rates td-a" data-type="text" data-name="amount->c20" data-value="<?php echo e(@$destination_amounts['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-20 markup_20 bg-rates td-a" data-type="text" data-name="markups->c20" data-value="<?php echo e(@$destination_markups['c20']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_20"><?php echo e(@$destination_amounts['c20']+@$destination_markups['c20']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40 amount_40 bg-rates td-a" data-type="text" data-name="amount->c40" data-value="<?php echo e(@$destination_amounts['c40']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40 markup_40 bg-rates td-a" data-type="text" data-name="markups->c40" data-value="<?php echo e(@$destination_markups['c40']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40"><?php echo e(@$destination_amounts['c40']+@$destination_markups['c40']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40hc amount_40hc bg-rates td-a" data-type="text" data-name="amount->c40hc" data-value="<?php echo e(@$destination_amounts['c40hc']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40hc markup_40hc bg-rates td-a" data-type="text" data-name="markups->c40hc" data-value="<?php echo e(@$destination_markups['c40hc']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40hc"><?php echo e(@$destination_amounts['c40hc']+@$destination_markups['c40hc']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-40nor amount_40nor bg-rates td-a" data-type="text" data-name="amount->c40nor" data-value="<?php echo e(@$destination_amounts['c40nor']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-40nor markup_40nor bg-rates td-a" data-type="text" data-name="markups->c40nor" data-value="<?php echo e(@$destination_markups['c40nor']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_40nor"><?php echo e(@$destination_amounts['c40nor']+@$destination_markups['c40nor']); ?></span>
                                                                    </td>
                                                                    <td <?php echo e(@$equipmentHides['45']); ?> class="tds">
                                                                        <a href="#" class="editable-amount-45 amount_45 bg-rates td-a" data-type="text" data-name="amount->c45" data-value="<?php echo e(@$destination_amounts['c45']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>
                                                                        +
                                                                        <a href="#" class="editable-markup-45 markup_45 bg-rates td-a" data-type="text" data-name="markups->c45" data-value="<?php echo e(@$destination_markups['c45']); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Total"></a>

                                                                        <i class="la la-caret-right arrow-down"></i> 
                                                                        <span class="total_45"><?php echo e(@$destination_amounts['c45']+@$destination_markups['c45']); ?></span>
                                                                    </td>
                                                                    <td class="tds">
                                                                        <a href="#" class="editable td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($item->currency_id); ?>" data-pk="<?php echo e($item->id); ?>" data-title="Select currency"></a>
                                                                        &nbsp;
                                                                        <a class="delete-charge" style="cursor: pointer;" title="Delete">
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
                                                                <?php echo e(Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true])); ?>

                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['20']); ?>>
                                                                <div class="row ">
                                                                    <div class="col-6">
                                                                        <input name="amount_c20" class="amount_c20 form-control" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c20" class="form-control markup_c20" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40" class="form-control markup_c40" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40hc']); ?>>
                                                                 <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40hc" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40hc" class="form-control markup_c40hc" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['40nor']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <input name="amount_c40nor" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c40nor" class="form-control markup_c40nor" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td <?php echo e(@$equipmentHides['45']); ?>>
                                                                <div class="row">
                                                                    <div class="col-6">                                                                
                                                                        <input name="amount_c45" class="form-control amount_c45" type="number" min="0" step="0.0000001"/>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <input name="markup_c45" class="form-control markup_c45" type="number" min="0" step="0.0000001" />
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <div class="input-group-btn">
                                                                        <div class="btn-group">
                                                                            <?php echo e(Form::select('destination_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width'])); ?>

                                                                        </div>
                                                                        <a class="btn btn-xs btn-primary-plus store_charge">
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
                                                                <td></td>
                                                                <td class="title-quote size-12px tds" colspan=""><span class="td-a">Total</span></td>
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_destination_20+@$sum_destination_m20, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_destination_40+@$sum_destination_m40, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_destination_40hc+@$sum_destination_m40hc, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_destination_40nor+@$sum_destination_m40nor, 2, '.', '')); ?></span></td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds"><span class="td-a"><?php echo e(number_format(@$sum_destination_45+@$sum_destination_m45, 2, '.', '')); ?></span></td>
                                                                <td class="tds"><span class="td-a"><?php echo e($currency_cfg->alphacode); ?></span></td>
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
                                        <!-- Totals -->
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
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds">20'</td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds">40'</td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds">40HC'</td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds">40NOR'</td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds">45'</td>
                                                                <td class="tds">Currency</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                            <?php
                                                                $amount_20=number_format(@$sum20+@$sum_origin_20+@$sum_destination_20+$rate->total_rate_a20, 2, '.', '');
                                                                $amount_40=number_format(@$sum40+@$sum_origin_40+@$sum_destination_40+$rate->total_rate_a40, 2, '.', '');
                                                                $amount_40hc=number_format(@$sum40hc+@$sum_origin_40hc+@$sum_destination_40hc+$rate->total_rate_a40hc, 2, '.', '');
                                                                $amount_40nor=number_format(@$sum40nor+@$sum_origin_40nor+@$sum_destination_40nor+$rate->total_rate_a40nor, 2, '.', '');
                                                                $amount_45=number_format(@$sum45+@$sum_origin_45+@$sum_destination_45+$rate->total_rate_a45, 2, '.', '');

                                                                $markup_20=number_format(@$sum_m20+@$sum_origin_m20+@$sum_destination_m20+$rate->total_rate_m20, 2, '.', '');
                                                                $markup_40=number_format(@$sum_m40+@$sum_origin_m40+@$sum_destination_m40+$rate->total_rate_m40, 2, '.', '');
                                                                $markup_40hc=number_format(@$sum_m40hc+@$sum_origin_m40hc+@$sum_destination_m40hc+$rate->total_rate_m40hc, 2, '.', '');
                                                                $markup_40nor=number_format(@$sum_m40nor+@$sum_origin_m40nor+@$sum_destination_m40nor+$rate->total_rate_m40nor, 2, '.', '');
                                                                $markup_45=number_format(@$sum_m45+@$sum_origin_m45+@$sum_destination_m45+$rate->total_rate_m45, 2, '.', '');

                                                                $amount_markup_20=number_format($amount_20+$markup_20, 2, '.', '');
                                                                $amount_markup_40=number_format($amount_40+$markup_40, 2, '.', '');
                                                                $amount_markup_40hc=number_format($amount_40hc+$markup_40hc, 2, '.', '');
                                                                $amount_markup_40nor=number_format($amount_40nor+$markup_40nor, 2, '.', '');
                                                                $amount_markup_45=number_format($amount_45+$markup_45, 2, '.', '');
                                                            ?>
                                                            <tr style="height:40px;">
                                                                <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_20); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_20); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_20); ?></td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_40); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_40); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_40); ?></td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_40hc); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_40hc); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_40hc); ?></td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_40nor); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_40nor); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_40nor); ?></td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_45); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_45); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_45); ?></td>
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
                                        <?php else: ?>
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
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds">20'</td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds">40'</td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds">40HC'</td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds">40NOR'</td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds">45'</td>
                                                                <td class="tds">Currency</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                            <?php
                                                                $amount_20=number_format(@$sum20+@$sum_origin_20+@$sum_destination_20+$rate->total_rate_a20, 2, '.', '');
                                                                $amount_40=number_format(@$sum40+@$sum_origin_40+@$sum_destination_40+$rate->total_rate_a40, 2, '.', '');
                                                                $amount_40hc=number_format(@$sum40hc+@$sum_origin_40hc+@$sum_destination_40hc+$rate->total_rate_a40hc, 2, '.', '');
                                                                $amount_40nor=number_format(@$sum40nor+@$sum_origin_40nor+@$sum_destination_40nor+$rate->total_rate_a40nor, 2, '.', '');
                                                                $amount_45=number_format(@$sum45+@$sum_origin_45+@$sum_destination_45+$rate->total_rate_a45, 2, '.', '');

                                                                $markup_20=number_format(@$sum_m20+@$sum_origin_m20+@$sum_destination_m20+$rate->total_rate_m20, 2, '.', '');
                                                                $markup_40=number_format(@$sum_m40+@$sum_origin_m40+@$sum_destination_m40+$rate->total_rate_m40, 2, '.', '');
                                                                $markup_40hc=number_format(@$sum_m40hc+@$sum_origin_m40hc+@$sum_destination_m40hc+$rate->total_rate_m40hc, 2, '.', '');
                                                                $markup_40nor=number_format(@$sum_m40nor+@$sum_origin_m40nor+@$sum_destination_m40nor+$rate->total_rate_m40nor, 2, '.', '');
                                                                $markup_45=number_format(@$sum_m45+@$sum_origin_m45+@$sum_destination_m45+$rate->total_rate_m45, 2, '.', '');

                                                                $amount_markup_20=number_format($amount_20+$markup_20, 2, '.', '');
                                                                $amount_markup_40=number_format($amount_40+$markup_40, 2, '.', '');
                                                                $amount_markup_40hc=number_format($amount_40hc+$markup_40hc, 2, '.', '');
                                                                $amount_markup_40nor=number_format($amount_40nor+$markup_40nor, 2, '.', '');
                                                                $amount_markup_45=number_format($amount_45+$markup_45, 2, '.', '');
                                                            ?>
                                                            <tr style="height:40px;">
                                                                <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>
                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_20); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_20); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_20); ?></td>
                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_40); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_40); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_40); ?></td>
                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_40hc); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_40hc); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_40hc); ?></td>
                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_40nor); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_40nor); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_40nor); ?></td>
                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds"><span class="bg-rates td-a"><?php echo e($amount_45); ?></span> + <span class="bg-rates td-a"><?php echo e($markup_45); ?></span> <i class="la la-caret-right arrow-down"></i> <?php echo e($amount_markup_45); ?></td>
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
                                        <div class="row no-mg-row">
                                            <div class="col-md-12 header-charges">
                                                <h5 class="title-quote size-12px">Inlands</h5>
                                            </div>
                                            <div class="col-md-12 thead">
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
                                                                            <li class="size-12px no-border-left d-flex justify-content-end">
                                                                              <div onclick="show_hide_element('details_inland_<?php echo e($v); ?>')"><i class="fa fa-angle-down"></i></div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="details_inland_<?php echo e($x); ?> hide">
                                                                            <table class="table table-sm table-bordered color-blue text-center">
                                                                                <thead class="title-quote text-center header-table">
                                                                                    <tr style="height: 40px;">
                                                                                        <td class="td-table" style="padding-left: 30px">Charge</td>
                                                                                        <td class="td-table">Distance</td>
                                                                                        <td class="td-table" <?php echo e(@$equipmentHides['20']); ?>>20'</td>
                                                                                        <td class="td-table" <?php echo e(@$equipmentHides['40']); ?>>40'</td>
                                                                                        <td class="td-table" <?php echo e(@$equipmentHides['40hc']); ?>>40HC'</td>
                                                                                        <td class="td-table" <?php echo e(@$equipmentHides['40nor']); ?>>40NOR'</td>
                                                                                        <td class="td-table" <?php echo e(@$equipmentHides['45']); ?>>45'</td>
                                                                                        <td class="td-table" >Currency</td>
                                                                                    </tr>
                                                                            </thead>
                                                                            <tbody style="background-color: white;">
                                                                                <tr style="height:40px;">
                                                                                    <td class="tds" style="padding-left: 30px">
                                                                                    <a href="#" class="editable-inland provider td-a" data-type="text" data-value="<?php echo e($inland->provider); ?>" data-name="provider" data-pk="<?php echo e(@$inland->id); ?>" data-title="Provider"></a>
                                                                                </td>
                                                                                <td class="tds">
                                                                                    <a href="#" class="editable-inland distance td-a" data-type="text" data-name="distance" data-value="<?php echo e(@$inland->distance); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Distance"></a> &nbsp;km
                                                                                </td>
                                                                                <td <?php echo e(@$equipmentHides['20']); ?> class="tds">
                                                                                    <a href="#" class="editable-inland-20 amount_20 td-a" data-type="text" data-name="rate->c20" data-value="<?php echo e(@$inland_rates['c20']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Amount"></a>
                                                                                    +
                                                                                    <a href="#" class="editable-inland-m20 markup_20 td-a" data-type="text" data-name="markup->c20" data-value="<?php echo e(@$inland_markups['c20']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Markup"></a>
                                                                                    <i class="la la-caret-right arrow-down"></i> 
                                                                                    <span class="total_20 td-a"><?php echo e(@$inland_rates['c20']+@$inland_markups['c20']); ?></span>
                                                                                </td>
                                                                                <td <?php echo e(@$equipmentHides['40']); ?> class="tds">
                                                                                    <a href="#" class="editable-inland-40 amount_40 td-a" data-type="text" data-name="rate->c40" data-value="<?php echo e(@$inland_rates['c40']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    +
                                                                                    <a href="#" class="editable-inland-m40 markup_40 td-a"data-type="text" data-name="markup->c40" data-value="<?php echo e(@$inland_markups['c40']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    <i class="la la-caret-right arrow-down"></i> 
                                                                                    <span class="total_40 td-a"><?php echo e(@$inland_rates['c40']+@$inland_markups['c40']); ?></span>
                                                                                </td>
                                                                                <td <?php echo e(@$equipmentHides['40hc']); ?> class="tds">
                                                                                    <a href="#" class="editable-inland-40hc amount_40hc td-a" data-type="text" data-name="rate->c40hc" data-value="<?php echo e(@$inland_amounts['c40hc']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    +
                                                                                    <a href="#" class="editable-inland-m40hc markup_40hc td-a" data-type="text" data-name="markup->c40hc" data-value="<?php echo e(@$inland_markups['c40hc']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    <i class="la la-caret-right arrow-down"></i> 
                                                                                    <span class="total_40hc td-a"><?php echo e(@$inland_amounts['c40hc']+@$inland_markups['c40hc']); ?></span>
                                                                                </td>
                                                                                <td <?php echo e(@$equipmentHides['40nor']); ?> class="tds">
                                                                                    <a href="#" class="editable-inland-40nor amount_40nor td-a" data-type="text" data-name="rate->c40nor" data-value="<?php echo e(@$inland_amounts['c40nor']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    +
                                                                                    <a href="#" class="editable-inland-m40nor markup_40nor td-a" data-type="text" data-name="markup->c40nor" data-value="<?php echo e(@$inland_markups['c40nor']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    <i class="la la-caret-right arrow-down"></i> 
                                                                                    <span class="total_40nor td-a"><?php echo e(@$inland_amounts['c40nor']+@$inland_markups['c40nor']); ?></span>
                                                                                </td>
                                                                                <td <?php echo e(@$equipmentHides['45']); ?> class="tds">
                                                                                    <a href="#" class="editable-inland-45 amount_45 td-a" data-type="text" data-name="rate->45" data-value="<?php echo e(@$inland_amounts['c45']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    +
                                                                                    <a href="#" class="editable-inland-m45 markup_45 td-a" data-type="text" data-name="markup->c45" data-value="<?php echo e(@$inland_markups['c45']); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Total"></a>
                                                                                    <i class="la la-caret-right arrow-down"></i> 
                                                                                    <span class="total_45 td-a"><?php echo e(@$inland_amounts['c45']+@$inland_markups['c45']); ?></span>
                                                                                </td>
                                                                                <td class="tds">
                                                                                    <a href="#" class="editable-inland td-a" data-source="<?php echo e($currencies); ?>" data-type="select" data-name="currency_id" data-value="<?php echo e($inland->currency_id); ?>" data-pk="<?php echo e(@$inland->id); ?>" data-title="Select currency"></a>
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>

                                                                        <div class='row'>
                                                                            <div class="col-md-12">
                                                                                <h5 class="title-quote pull-right">
                                                                                    <b>Add inland charge</b>
                                                                                    <a class="btn" onclick="addInlandCharge(<?php echo e($i); ?>)" style="vertical-align: middle">
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
                                                <div class="m-portlet custom-portlet" style="box-shadow: none; background-color: #fff !important; border:none;">
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