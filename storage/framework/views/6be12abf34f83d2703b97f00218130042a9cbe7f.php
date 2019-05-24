<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Quote #<?php echo e($quote->company_quote); ?></title>
        <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>" media="all" />
        <link rel="stylesheet" href="<?php echo e(asset('css/style-pdf.css')); ?>" media="all" />
    </head>
    <body style="background-color: white; font-size: 11px;">
        <header class="clearfix">
            <div id="logo">
                <?php if($user->companyUser->logo!=''): ?>
                    <img src="<?php echo e(Storage::disk('s3_upload')->url($user->companyUser->logo)); ?>" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:25px">
                <?php endif; ?>
            </div>
            <div id="company">
                <div><span class="color-title"><b>Cotización: </b> </span><span style="color: #20A7EE"><b>#<?php echo e($quote->custom_id == '' ? $quote->company_quote:$quote->custom_id); ?></b></span></div>
                <div><span class="color-title"><b>Fecha de creación:</b> </span><?php echo e(date_format($quote->created_at, 'M d, Y H:i')); ?></div>
                <?php if($quote->validity!=''&&$quote->since_validity!=''): ?>
                <div><span class="color-title"><b>Validez: </b></span> <?php echo e(\Carbon\Carbon::parse( $quote->since_validity)->format('d M Y')); ?> -  <?php echo e(\Carbon\Carbon::parse( $quote->validity)->format('d M Y')); ?></div>
                <?php endif; ?>
            </div>
        </header>
        <main>
            <div id="details" class="clearfix details">
                <div class="client">
                    <p ><b>De:</b></p>
                    <span id="destination_input" style="line-height: 0.5">
                        <p><?php echo e($user->name); ?> <?php echo e($user->lastname); ?></p>
                        <p><span style="color: #031B4E"><b><?php echo e($user->companyUser->name); ?></b></span></p>
                        <p><?php echo e($user->companyUser->address); ?></p>
                        <p><?php echo e($user->phone); ?></p>
                        <p><?php echo e($user->email); ?></p>
                    </span>

                </div>
                <div class="company text-right" style="float: right; width: 350px;">
                    <p><b>Para:</b></p>
                    <span id="destination_input" style="line-height: 0.5">
                        <?php if($quote->company->logo!=''): ?>
                            <img src="<?php echo e(Storage::disk('s3_upload')->url($quote->company->logo)); ?>" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px"/>
                        <?php endif; ?>
                        <p><?php echo e($quote->contact->first_name.' '.$quote->contact->last_name); ?></p>
                        <p><span style="color: #031B4E"><b><?php echo e($quote->company->business_name); ?></b></span></p>
                        <p><?php echo e($quote->company->address); ?></p>
                        <p><?php echo e($quote->contact->phone); ?></p>
                        <p><?php echo e($quote->contact->email); ?></p>
                    </span>
                </div>
            </div>
            <br>
            <div class="clearfix">
                <div class="">
                    <table class="" style="width: 45%; float:left; border-radius:2px !Important; ">
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>Origen</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span id="origin_input" style="color: #787878;">
                                        <?php if($quote->origin_harbor_id!=''): ?>
                                        Port: <?php echo e($quote->origin_harbor->name); ?>, <?php echo e($quote->origin_harbor->code); ?>

                                        <?php endif; ?>
                                        <?php if($quote->origin_airport_id!=''): ?>
                                        Airport: <?php echo e($quote->origin_airport->name); ?>

                                        <?php endif; ?>
                                        <br>
                                        <?php if($quote->origin_address!=''): ?>
                                        Address: <?php echo e($quote->origin_address); ?>

                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="" style="width: 45%; float: right;">
                        <thead class="title-quote text-center ">
                            <tr>
                                <th class="unit"><b>Destino</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span id="destination_input" style="color: #787878;">
                                        <?php if($quote->destination_harbor_id!=''): ?>
                                        Port: <?php echo e($quote->destination_harbor->name); ?>, <?php echo e($quote->destination_harbor->code); ?>

                                        <?php endif; ?>
                                        <?php if($quote->destination_airport_id!=''): ?>
                                        Airport: <?php echo e($quote->destination_airport->name); ?>

                                        <?php endif; ?>
                                        <br>
                                        <?php if($quote->destination_address!=''): ?>
                                        Address: <?php echo e($quote->destination_address); ?>

                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($quote->hide_carrier==false): ?>
            <br>
            <div class="clearfix">
                <div class="client" style="color: #525F7F;">
                    <p class="title"><b><?php echo e($quote->type==3 ? 'Aerolínea':'Naviera'); ?></b></p>
                    <hr style="margin-bottom:5px;margin-top:1px;border:0.1px solid #f1f1f1">
                    <?php if($quote->carrier_id!=''): ?>
                    <p><?php echo e($quote->carrier->name); ?></p>
                    <?php endif; ?>
                    <?php if($quote->airline_id!=''): ?>
                    <p><?php echo e($quote->airline->name); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <br>
            <div id="details" class="clearfix details">
                <div class="company" style="color: #1D3A6E;">
                    <p class="title"><b>Detalles de la carga</b></p>
                    <hr style="margin-bottom:5px;margin-top:1px;">

                    <?php if($quote->qty_20 != '' || $quote->qty_40 != '' || $quote->qty_40_hc != '' || $quote->qty_45_hc != '' || $quote->qty_20_reefer != '' || $quote->qty_40_reefer != '' || $quote->qty_40_hc_reefer != '' || $quote->qty_20_open_top != '' || $quote->qty_40_open_top != ''): ?>
                        <table style="text-align: left !important;">
                            <?php if($quote->qty_20 != '' || $quote->qty_40 != '' || $quote->qty_40_hc != '' || $quote->qty_45_hc != ''): ?>
                                <tr>
                                    <?php if($quote->qty_20 != ''): ?><td><?php echo $quote->qty_20 != '' && $quote->qty_20 > 0 ? $quote->qty_20.' x 20\' container':''; ?></td><?php endif; ?>
                                    <?php if($quote->qty_40 != ''): ?><td><?php echo $quote->qty_40 != '' && $quote->qty_40 > 0 ? $quote->qty_40.' x 40\' container':''; ?></td><?php endif; ?>
                                    <?php if($quote->qty_40_hc != ''): ?><td><?php echo $quote->qty_40_hc != '' && $quote->qty_40_hc > 0 ? $quote->qty_40_hc.' x 40\' HC container':''; ?></td><?php endif; ?>
                                    <?php if($quote->qty_45_hc != ''): ?><td><?php echo $quote->qty_45_hc != '' && $quote->qty_45_hc > 0 ? $quote->qty_45_hc.' x 45\' HC container':''; ?></td><?php endif; ?>
                                </tr>
                            <?php endif; ?>
                            <?php if($quote->qty_20_reefer != '' || $quote->qty_40_reefer != '' || $quote->qty_40_hc_reefer != ''): ?>
                                <tr>
                                    <?php if($quote->qty_20_reefer != ''): ?><td><?php echo $quote->qty_20_reefer != '' &&  $quote->qty_20_reefer > 0 ? $quote->qty_20_reefer.' x 20\' Reefer container':''; ?></td><?php endif; ?>
                                    <?php if($quote->qty_40_reefer != ''): ?><td><?php echo $quote->qty_40_reefer != '' &&  $quote->qty_40_reefer > 0 ? $quote->qty_40_reefer.' x 40\' Reefer container':''; ?></td><?php endif; ?>
                                    <?php if($quote->qty_40_hc_reefer != ''): ?><td><?php echo $quote->qty_40_hc_reefer != '' &&  $quote->qty_40_hc_reefer > 0 ? $quote->qty_40_hc_reefer.' x 40\' HC Reefer container':''; ?></td><?php endif; ?>
                                </tr>
                            <?php endif; ?>
                            <?php if($quote->qty_20_open_top != '' || $quote->qty_40_open_top != ''): ?>
                                <tr>
                                    <?php if($quote->qty_20_open_top != ''): ?><td><?php echo $quote->qty_20_open_top != '' &&  $quote->qty_20_open_top > 0 ? $quote->qty_20_open_top.' x 20\' Open Top container':''; ?></td><?php endif; ?>
                                    <?php if($quote->qty_40_open_top != ''): ?><td><?php echo $quote->qty_40_open_top != '' &&  $quote->qty_40_open_top > 0 ? $quote->qty_40_open_top.' x 40\' Open Top container':''; ?></td><?php endif; ?>
                                </tr>
                            <?php endif; ?>
                        </table>
                    <?php endif; ?>

                    <?php if($quote->total_quantity!='' && $quote->total_quantity>0): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div id="cargo_details_cargo_type_p"><b>Tipo de carga:</b> <?php echo e($quote->type_cargo == 1 ? 'Pallets' : 'Packages'); ?></div>
                        </div>
                        <div class="col-md-3">
                            <div id="cargo_details_total_quantity_p"><b>Cantidad total:</b> <?php echo e($quote->total_quantity != '' ? $quote->total_quantity : ''); ?></div>
                        </div>
                        <div class="col-md-3">
                            <div id="cargo_details_total_weight_p"><b>Peso total: </b> <?php echo e($quote->total_weight != '' ? $quote->total_weight.'Kg' : ''); ?></div>
                        </div>
                        <div class="col-md-3">
                            <p id="cargo_details_total_volume_p"><b>Volumen total: </b> <?php echo $quote->total_volume != '' ? $quote->total_volume.'m<sup>3</sup>' : ''; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($package_loads) && count($package_loads)>0): ?>
                    <table class="table table-bordered color-blue">
                        <thead class="title-quote text-center header-table">
                            <tr>
                                <th class="unit"><b>Tipo de carga</b></th>
                                <th class="unit"><b>Cantidad</b></th>
                                <th class="unit"><b>Alto</b></th>
                                <th class="unit"><b>Ancho</b></th>
                                <th class="unit"><b>Largo</b></th>
                                <th class="unit"><b>Peso</b></th>
                                <th class="unit"><b>Peso total</b></th>
                                <th class="unit"><b>Volumen</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $package_loads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package_load): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="text-center">
                                <td><?php echo e($package_load->type_cargo==1 ? 'Pallets':'Packages'); ?></td>
                                <td><?php echo e($package_load->quantity); ?></td>
                                <td><?php echo e($package_load->height); ?> cm</td>
                                <td><?php echo e($package_load->width); ?> cm</td>
                                <td><?php echo e($package_load->large); ?> cm</td>
                                <td><?php echo e($package_load->weight); ?> kg</td>
                                <td><?php echo e($package_load->total_weight); ?> kg</td>
                                <td><?php echo e($package_load->volume); ?> m<sup>3</sup></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-12 pull-right">
                            <b>Total:</b> <?php echo e($package_loads->sum('quantity')); ?> un <?php echo e($package_loads->sum('volume')); ?> m<sup>3</sup> <?php echo e($package_loads->sum('total_weight')); ?> kg
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($quote->chargeable_weight!='' && $quote->chargeable_weight>0): ?>
                        <div class="row">
                            <div class="col-md-12 ">
                                <b>Peso tasable:</b> <?php echo e($quote->chargeable_weight); ?> kg
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <?php if($charges_type==1): ?>
            <table class="page-break" border="0" cellspacing="1" cellpadding="1">
                <thead class="title-quote text-center header-table">
                    <tr >
                        <?php if($quote->sub_total_origin!=''): ?>
                        <th class="unit"><b>Cargos en origen</b></th>
                        <?php endif; ?>
                        <?php if($quote->sub_total_freight!=''): ?>
                        <th class="unit"><b>Cargos de flete</b></th>
                        <?php endif; ?>
                        <?php if($quote->sub_total_destination!=''): ?>
                        <th class="unit"><b>Cargos de destino</b></th>
                        <?php endif; ?>
                        <th class="unit"><b>Total</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php if($quote->sub_total_origin!=''): ?>
                        <td>
                            <?php echo e($quote->sub_total_origin); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?>

                        </td>
                        <?php endif; ?>
                        <?php if($quote->sub_total_freight!=''): ?>
                        <td>
                            <?php echo e($quote->sub_total_freight); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?>

                        </td>
                        <?php endif; ?>
                        <?php if($quote->sub_total_destination!=''): ?>
                        <td>
                            <?php echo e($quote->sub_total_destination); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?>

                        </td>
                        <?php endif; ?>
                        <td>
                            <?php echo e($quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination); ?>&nbsp;<?php echo e($quote->currencies->alphacode); ?>

                        </td>
                    </tr>
                </tbody>
            </table>
            <?php else: ?>
            <?php if(count($origin_ammounts)>0): ?>
            <p class="title">Cargos en origen</p>
            <br>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit"><b>Carga</b></th>
                        <th class="unit"><b>Detalle</b></th>
                        <th class="unit"><b>Unidades</b></th>
                        <th class="unit"><b>Precio por unidad</b></th>
                        <th class="unit"><b>Total </b></th>
                        <th class="unit"><b>Total en &nbsp;<?php echo e($quote->currencies->alphacode); ?></b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $origin_ammounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin_ammount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="text-center color-table">
                        <td class="white"><?php echo e($origin_ammount->charge); ?></td>
                        <td class="white"><?php echo e($origin_ammount->detail); ?></td>
                        <td class="white"><?php echo e($origin_ammount->units); ?></td>
                        <?php if($origin_ammount->currency->alphacode!=$quote->currencies->alphacode): ?>
                        <?php if($ammounts_type==1): ?>
                        <td><?php echo e(number_format((float)$origin_ammount->total_ammount_2 / $origin_ammount->units, 2,'.', '')); ?> <?php echo e($quote->currencies->alphacode); ?></td>
                        <?php else: ?>
                        <?php
                        $markup_per_unit=$origin_ammount->markup_converted/$origin_ammount->units
                        ?>
                        <td><?php echo e(number_format((float)$markup_per_unit+$origin_ammount->price_per_unit, 2,'.', '')); ?> <?php echo e($origin_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php else: ?>
                        <td><?php echo e(number_format((float)$origin_ammount->total_ammount_2 / $origin_ammount->units, 2,'.', '')); ?> <?php echo e($origin_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php if($origin_ammount->currency->alphacode!=$quote->currencies->alphacode): ?>
                        <?php if($ammounts_type==1): ?>
                        <td><?php echo e($origin_ammount->total_ammount_2); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></td>
                        <?php else: ?>
                        <td><?php echo e(number_format((float)$origin_ammount->total_ammount + $origin_ammount->markup_converted, 2,'.', '')); ?> <?php echo e($origin_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php else: ?>
                        <td><?php echo e($origin_ammount->total_ammount + $origin_ammount->markup); ?> <?php echo e($origin_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <td class="white"><?php echo e($origin_ammount->total_ammount_2); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="text-center subtotal">
                        <td colspan="4"></td>
                        <td style="font-size: 12px; color: #01194F"><b>Monto total</b></td>
                        <td style="font-size: 12px; color: #01194F"><b><?php echo e($quote->sub_total_origin); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></b></td>
                    </tr>
                </tfoot>
            </table>
            <?php endif; ?>
            <?php if(count($freight_ammounts)>0): ?>
            <p class="title">Cargos de flete</p>
            <br>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit"><b>Carga</b></th>
                        <th class="unit"><b>Detalle</b></th>
                        <th class="unit"><b>Unidades</b></th>
                        <th class="unit"><b>Precio por unidad</b></th>
                        <th class="unit"><b>Total </b></th>
                        <th class="unit"><b>Total en &nbsp;<?php echo e($quote->currencies->alphacode); ?></b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $freight_ammounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freight_ammount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="text-center color-table">
                        <td><?php echo e($freight_ammount->charge); ?></td>
                        <td><?php echo e($freight_ammount->detail); ?></td>
                        <td><?php echo e($freight_ammount->units); ?></td>
                        <?php if($freight_ammount->currency->alphacode!=$quote->currencies->alphacode): ?>
                        <?php if($ammounts_type==1): ?>
                        <td><?php echo e(number_format((float)$freight_ammount->total_ammount_2 / $freight_ammount->units, 2,'.', '')); ?> <?php echo e($quote->currencies->alphacode); ?></td>
                        <?php else: ?>
                        <?php
                        $markup_per_unit=$freight_ammount->markup_converted/$freight_ammount->units
                        ?>
                        <td><?php echo e(number_format((float)$markup_per_unit+$freight_ammount->price_per_unit, 2,'.', '')); ?> <?php echo e($freight_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php else: ?>
                        <td><?php echo e(number_format((float)$freight_ammount->total_ammount_2 / $freight_ammount->units, 2,'.', '')); ?> <?php echo e($freight_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php if($freight_ammount->currency->alphacode!=$quote->currencies->alphacode): ?>
                        <?php if($ammounts_type==1): ?>
                        <td><?php echo e($freight_ammount->total_ammount_2); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></td>
                        <?php else: ?>
                        <td><?php echo e(number_format((float)$freight_ammount->total_ammount + $freight_ammount->markup_converted, 2,'.', '')); ?> <?php echo e($freight_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php else: ?>
                        <td><?php echo e($freight_ammount->total_ammount + $freight_ammount->markup); ?> <?php echo e($freight_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <td><?php echo e($freight_ammount->total_ammount_2); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="text-center" style="font-size: 12px;">
                        <td colspan="4"></td>
                        <td style="font-size: 12px; color: #01194F"><b>Monto total</b></td>
                        <td style="font-size: 12px; color: #01194F"><b><?php echo e($quote->sub_total_freight); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></b></td>
                    </tr>
                </tfoot>
            </table>
            <?php endif; ?>
            <?php if(count($destination_ammounts)>0): ?>
            <p class="title">Cargos en destino</p>
            <br>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Carga</b></th>
                        <th class="unit"><b>Detalle</b></th>
                        <th class="unit"><b>Unidades</b></th>
                        <th class="unit"><b>Precio por unidad</b></th>
                        <th class="unit"><b>Total </b></th>
                        <th class="unit"><b>Total en &nbsp;<?php echo e($quote->currencies->alphacode); ?></b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $destination_ammounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destination_ammount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="text-center color-table">
                        <td><?php echo e($destination_ammount->charge); ?></td>
                        <td><?php echo e($destination_ammount->detail); ?></td>
                        <td><?php echo e($destination_ammount->units); ?></td>
                        <?php if($destination_ammount->currency->alphacode!=$quote->currencies->alphacode): ?>
                        <?php if($ammounts_type==1): ?>
                        <td><?php echo e(number_format((float)$destination_ammount->total_ammount_2 / $destination_ammount->units, 2,'.', '')); ?> <?php echo e($quote->currencies->alphacode); ?></td>
                        <?php else: ?>
                        <?php
                        $markup_per_unit=$destination_ammount->markup_converted/$destination_ammount->units
                        ?>
                        <td><?php echo e(number_format((float)$markup_per_unit+$destination_ammount->price_per_unit, 2,'.', '')); ?> <?php echo e($destination_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php else: ?>
                        <td><?php echo e(number_format((float)$destination_ammount->total_ammount_2 / $destination_ammount->units, 2,'.', '')); ?> <?php echo e($destination_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php if($destination_ammount->currency->alphacode!=$quote->currencies->alphacode): ?>
                        <?php if($ammounts_type==1): ?>
                        <td><?php echo e($destination_ammount->total_ammount_2); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></td>
                        <?php else: ?>
                        <td><?php echo e(number_format((float)$destination_ammount->total_ammount + $destination_ammount->markup_converted, 2,'.', '')); ?> <?php echo e($destination_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <?php else: ?>
                        <td><?php echo e($destination_ammount->total_ammount + $destination_ammount->markup); ?> <?php echo e($destination_ammount->currency->alphacode); ?></td>
                        <?php endif; ?>
                        <td><?php echo e($destination_ammount->total_ammount_2); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="text-center">
                        <td colspan="4"></td>
                        <td style="font-size: 12px; color: #01194F"><b>Monto total</b></td>
                        <td style="font-size: 12px; color: #01194F"><b><?php echo e($quote->sub_total_destination); ?>

                            &nbsp; <?php echo e($quote->currencies->alphacode); ?></b></td>
                    </tr>
                </tfoot>
            </table>
            <?php endif; ?>
            <?php endif; ?>
        </main>
        <?php if($charges_type!=1): ?>
        <div class="clearfix details page-break">
            <div class="company">
                <p class="title text-center pull-right total"><b>Total: <?php echo e($quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination); ?> &nbsp;<?php echo e($quote->currencies->alphacode); ?></b></p>
            </div>
        </div>
        <?php endif; ?>
        <div class="clearfix">
            <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote header-table">
                    <tr>
                        <th class="unit text-left"><b>&nbsp;&nbsp;&nbsp;Términos y condiciones</b></th>
                    </tr>
                </thead>
                <tbody>

                    <?php if($quote->term!=''): ?>
                        <tr>
                            <td style="padding:20px;">
                                <span class="text-justify">
                                    <?php echo $quote->term; ?>

                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($quote->payment_conditions!=''): ?>
        <div class="clearfix">
            <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote header-table">
                    <tr>
                        <th class="unit text-left"><b>&nbsp;&nbsp;&nbsp;Términos de pago</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:20px;">
                            <span class="text-justify"><?php echo $quote->payment_conditions; ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <!--<footer>
Cargofive &copy; <?php echo e(date('Y')); ?>

</footer>-->
    </body>
</html>
