<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote #<?php echo e($quote->quote_id); ?></title>
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
            <div>
                <span class="color-title"><b><?php if($quote->pdf_option->language=='English'): ?>Quotation Id:<?php elseif($quote->pdf_option->language=='Spanish'): ?> Cotización: <?php else: ?> Numero de cotação: <?php endif; ?></b></span> 
                <span style="color: #20A7EE"><b>#<?php echo e($quote->custom_id == '' ? $quote->quote_id:$quote->custom_quote_id); ?></b></span>
            </div>
            <div>
                <span class="color-title"><b><?php if($quote->pdf_option->language=='English'): ?>Date of issue:<?php elseif($quote->pdf_option->language=='Spanish'): ?> Fecha creación: <?php else: ?> Data de emissão: <?php endif; ?></b></span> <?php echo e(date_format($quote->created_at, 'M d, Y H:i')); ?>

            </div>
            <?php if($quote->validity_start!=''&&$quote->validity_end!=''): ?>
            <div>
                <span class="color-title">
                    <b><?php if($quote->pdf_option->language=='English'): ?>Validity:<?php elseif($quote->pdf_option->language=='Spanish'): ?> Validez: <?php else: ?> Validade: <?php endif; ?> </b>
                </span> 
                <?php echo e(\Carbon\Carbon::parse( $quote->validity_start)->format('d M Y')); ?> -  <?php echo e(\Carbon\Carbon::parse( $quote->validity_end)->format('d M Y')); ?>

            </div>
            <?php endif; ?>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix details">
            <div class="client">
                <p <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>From:</b></p>
                <p <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>De:</b></p>
                <p <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>A partir de:</b></p>
                <span id="destination_input" style="line-height: 0.5">
                    <p><?php echo e($quote->user->name); ?> <?php echo e($quote->user->lastname); ?></p>
                    <p><span style="color: #031B4E"><b><?php echo e($user->companyUser->name); ?></b></span></p>
                    <p><?php echo e($user->companyUser->address); ?></p>
                    <p><?php echo e($user->phone); ?></p>
                    <p><?php echo e($user->email); ?></p>
                </span>
            </div>
            <div class="company text-right" style="float: right; width: 350px;">
                <p <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>To:</b></p>
                <p <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Para:</b></p>
                <p <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Para:</b></p>
                <span id="destination_input" style="line-height: 0.5">
                    <?php if($quote->pdf_option->show_logo==1): ?>
                    <?php if($quote->company->logo!=''): ?>
                    <img src="<?php echo e(Storage::disk('s3_upload')->url($quote->company->logo)); ?>" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px">
                    <?php endif; ?>
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
        <?php if($quote->kind_of_cargo!=''): ?>
            <p <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><span class="title" >Kind of cargo:</span> <?php echo e($quote->kind_of_cargo); ?></p>
            <p <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><span class="title" >Tipo de carga:</span> <?php echo e($quote->kind_of_cargo); ?></p>
            <p <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><span class="title" >Tipo de carga:</span> <?php echo e($quote->kind_of_cargo); ?></p>
        <?php endif; ?>
        <?php if($quote->commodity!=''): ?>
            <p <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><span class="title" >Commodity:</span> <?php echo e($quote->commodity); ?></p>
            <p <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><span class="title" >Mercancía:</span> <?php echo e($quote->commodity); ?></p>
            <p <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><span class="title" >Mercadoria:</span> <?php echo e($quote->commodity); ?></p>
        <?php endif; ?>
        <br>
        <?php if($quote->pdf_option->show_type=='total in'): ?>
            <div <?php echo e($quote->pdf_option->show_type=='total in' ? '':'hidden'); ?>>
                <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Total estimated costs</p>
                <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos totales estimados</p>
                <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Custos totais estimados</p>
                <br>
            </div>
        <?php else: ?>
            <div <?php echo e($quote->pdf_option->grouped_total_currency==1 ? '':'hidden'); ?>>
                <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Total estimated costs</p>
                <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos totales estimados</p>
                <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Custos totais estimados</p>
                <br>
            </div>
        <?php endif; ?>
        <!-- All in table -->
        <?php if($quote->pdf_option->show_type=='total in'): ?>
            <table border="0" cellspacing="1" cellpadding="1" <?php echo e($quote->pdf_option->show_type=='total in' ? '':'hidden'); ?>>
        <?php else: ?>
            <table border="0" cellspacing="1" cellpadding="1" <?php echo e($quote->pdf_option->grouped_total_currency==1 ? '':'hidden'); ?>>
        <?php endif; ?>
            <thead class="title-quote text-center header-table">
                <tr >
                    <th class="unit"><b>POL</b></th>
                    <th class="unit"><b>POD</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><b><?php if($quote->pdf_option->language=='English'): ?> Carrier <?php elseif($quote->pdf_option->language=='Spanish'): ?> Línea marítima <?php else: ?> Linha Maritima <?php endif; ?></b></th>
                    <th <?php echo e(@$equipmentHides['20']); ?>><b>20'</b></th>
                    <th <?php echo e(@$equipmentHides['40']); ?>><b>40'</b></th>
                    <th <?php echo e(@$equipmentHides['40hc']); ?>><b>40' HC</b></th>
                    <th <?php echo e(@$equipmentHides['40nor']); ?>><b>40' NOR</b></th>
                    <th <?php echo e(@$equipmentHides['45']); ?>><b>45'</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>Currency</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Moneda</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Moeda</b></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                        $sum20= 0;
                        $sum40= 0;
                        $sum40hc= 0;
                        $sum40nor= 0;
                        $sum45= 0;
                    ?>
                    <?php $__currentLoopData = $rate->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $array_amounts = json_decode($value->amount,true);
                            $array_markups = json_decode($value->markups,true);
                            if(isset($array_amounts['c20']) && isset($array_markups['c20'])){
                                $amount20=$array_amounts['c20'];
                                $markup20=$array_markups['c20'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total20=($amount20+$markup20)/$value->currency_usd;
                                }else{
                                    $total20=($amount20+$markup20)/$value->currency_eur;
                                }
                                $sum20 += $total20;
                            }
                            if(isset($array_amounts['c40']) && isset($array_markups['c40'])){
                                $amount40=$array_amounts['c40'];
                                $markup40=$array_markups['c40'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40=($amount40+$markup40)/$value->currency_usd;
                                }else{
                                    $total40=($amount40+$markup40)/$value->currency_eur;
                                }
                                $sum40 += $total40;
                            }
                            if(isset($array_amounts['c40hc']) && isset($array_markups['c40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $markup40hc=$array_markups['c40hc'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40hc=($amount40hc+$markup40hc)/$value->currency_usd;
                                }else{
                                    $total40hc=($amount40hc+$markup40hc)/$value->currency_eur;
                                }                            
                                $sum40hc += $total40hc;
                            }
                            if(isset($array_amounts['c40nor']) && isset($array_markups['c40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $markup40nor=$array_markups['c40nor'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40nor=($amount40nor+$markup40nor)/$value->currency_usd;
                                }else{
                                    $total40nor=($amount40nor+$markup40nor)/$value->currency_eur;
                                }  
                                $sum40nor += $total40nor;
                            }
                            if(isset($array_amounts['c45']) && isset($array_markups['c45'])){
                                $amount45=$array_amounts['c45'];
                                $markup45=$array_markups['c45'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total45=($amount45+$markup45)/$value->currency_usd;
                                }else{
                                    $total45=($amount45+$markup45)/$value->currency_eur;
                                }  
                                $sum45 += $total45;
                            }
                        ?>
                        <?php 
                            $inland20= 0;
                            $inland40= 0;
                            $inland40hc= 0;
                            $inland40nor= 0;
                            $inland45= 0;
                        ?>
                        <?php if(!$rate->inland->isEmpty()): ?>
                            <?php $__currentLoopData = $rate->inland; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                $arr_amounts = json_decode($item->rate,true);
                                $arr_markups = json_decode($item->markup,true);
                                if(isset($arr_amounts['c20']) && isset($arr_markups['c20'])){
                                    $amount_inland20=$arr_amounts['c20'];
                                    $markup_inland20=$arr_markups['c20'];
                                    $total_inland20=$amount_inland20+$markup_inland20;
                                    if($total_inland20>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland20=$total_inland20/$value->currency_usd;
                                        }else{
                                            $total_inland20=$total_inland20/$value->currency_eur;
                                        }
                                    }
                                    $inland20 += $total_inland20;
                                }
                                if(isset($arr_amounts['c40']) && isset($arr_markups['c40'])){
                                    $amount_inland40=$arr_amounts['c40'];
                                    $markup_inland40=$arr_markups['c40'];
                                    $total_inland40=$amount_inland40+$markup_inland40;
                                    if($total_inland40>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland40=$total_inland40/$value->currency_usd;
                                        }else{
                                            $total_inland40=$total_inland40/$value->currency_eur;
                                        }
                                    }
                                    $inland40 += $total_inland40;
                                }
                                if(isset($arr_amounts['c40hc']) && isset($arr_markups['c40hc'])){
                                    $amount_inland40hc=$arr_amounts['c40hc'];
                                    $markup_inland40hc=$arr_markups['c40hc'];
                                    $total_inland40hc=$amount_inland40hc+$markup_inland40hc;
                                    if($total_inland40hc>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland40hc=$total_inland40hc/$value->currency_usd;
                                        }else{
                                            $total_inland40hc=$total_inland40hc/$value->currency_eur;
                                        }
                                    }
                                    $inland40hc += $total_inland40hc;
                                }
                                if(isset($arr_amounts['c40nor']) && isset($arr_markups['c40nor'])){
                                    $amount_inland40nor=$arr_amounts['c40nor'];
                                    $markup_inland40nor=$arr_markups['c40nor'];
                                    $total_inland40nor=$amount_inland40nor+$markup4_inland40nor;
                                    if($total_inland40nor>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland40nor=$total_inland40nor/$value->currency_usd;
                                        }else{
                                            $total_inland40nor=$total_inland40nor/$value->currency_eur;
                                        }
                                    }
                                    $inland40nor += $total_inland40nor;
                                }
                                if(isset($arr_amounts['c45']) && isset($arr_markups['c45'])){
                                    $amount_inland45=$arr_amounts['c45'];
                                    $markup_inland45=$arr_markups['c45'];
                                    $total_inland45=$amount_inland45+$markup_inland45;
                                    if($total_inland45>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland45=$total_inland45/$value->currency_usd;
                                        }else{
                                            $total_inland45=$total_inland45/$value->currency_eur;
                                        }
                                    }                                
                                    $inland45 += $total_inland45;
                                }
                            ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="text-center color-table">
                        <td ><?php echo e($rate->origin_port->name); ?>, <?php echo e($rate->origin_port->code); ?></td>
                        <td ><?php echo e($rate->destination_port->name); ?>, <?php echo e($rate->destination_port->code); ?></td>
                        <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($rate->carrier->name); ?></td>
                        <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e(number_format((float)@$sum20+@$inland20+@$rate->total_rate20, 2, '.', '')); ?></td>
                        <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e(number_format((float)@$sum40+@$inland40+@$rate->total_rate40, 2, '.', '')); ?></td>
                        <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e(number_format((float)@$sum40hc+@$inland40hc+@$rate->total_rate40hc, 2, '.', '')); ?></td>
                        <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e(number_format((float)@$sum40nor+@$inland40nor+@$rate->total_rate40nor, 2, '.', '')); ?></td>
                        <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e(number_format((float)@$sum45+@$inland45+@$rate->total_rate45, 2, '.', '')); ?></td>
                        <td ><?php echo e($quote->pdf_option->grouped_total_currency==0 ?$currency_cfg->alphacode:$quote->pdf_option->total_in_currency); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
        </table>
        <br>

        <!-- DETAILED TABLES -->

        <!-- Freights table all in-->
        <?php if($quote->pdf_option->grouped_freight_charges==1 && $quote->pdf_option->show_type=='detailed' ): ?>
        <div <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
            <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Freight charges</p>
            <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos de flete</p>
            <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Encargos de frete</p>
            <br>
        </div>

        <table border="0" cellspacing="1" cellpadding="1" >
            <thead class="title-quote text-center header-table">
                <tr >
                    <th class="unit"><b>POL</b></th>
                    <th class="unit"><b>POD</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><b><?php if($quote->pdf_option->language=='English'): ?> Carrier <?php elseif($quote->pdf_option->language=='Spanish'): ?> Línea marítima <?php else: ?> Linha Maritima <?php endif; ?></b></th>
                    <th <?php echo e(@$equipmentHides['20']); ?>><b>20'</b></th>
                    <th <?php echo e(@$equipmentHides['40']); ?>><b>40'</b></th>
                    <th <?php echo e(@$equipmentHides['40hc']); ?>><b>40' HC</b></th>
                    <th <?php echo e(@$equipmentHides['40nor']); ?>><b>40' NOR</b></th>
                    <th <?php echo e(@$equipmentHides['45']); ?>><b>45'</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>Currency</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Moneda</b></th>
                    <th class="unit" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Moeda</b></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $rate_amounts = json_decode($rate->rates,true);
                    $rate_markups = json_decode($rate->markups,true);
                ?>
                <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($freight_charges_grouped->count() == 0): ?>
                        <tr class="text-center color-table">
                            <td >
                                <?php if($rate->origin_port_id!=''): ?> 
                                <?php echo e($rate->origin_port->name); ?>, <?php echo e($rate->origin_port->code); ?> 
                                <?php elseif($rate->origin_address!=''): ?> 
                                <?php echo e($rate->origin_address); ?> 
                                <?php else: ?> 
                                <?php echo e($rate->origin_airport->name); ?>, <?php echo e($rate->origin_airport->code); ?>

                                <?php endif; ?>
                            </td>
                            <td >
                                <?php if($rate->destination_port_id!=''): ?> 
                                <?php echo e($rate->destination_port->name); ?>, <?php echo e($rate->destination_port->code); ?> 
                                <?php elseif($rate->destination_address!=''): ?> 
                                <?php echo e($rate->destination_address); ?> 
                                <?php else: ?> 
                                <?php echo e($rate->destination_airport->name); ?>, <?php echo e($rate->destination_airport->code); ?>

                                <?php endif; ?>
                            </td>                            
                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($rate->carrier->name); ?></td>
                            <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e($rate->total_rate20); ?></td>
                            <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e($rate->total_rate40); ?></td>
                            <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e($rate->total_rate40hc); ?></td>
                            <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e($rate->total_rate40nor); ?></td>
                            <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e($rate->total_rate45); ?></td>
                            <?php if($quote->pdf_option->grouped_freight_charges==1): ?>
                                <td ><?php echo e($quote->pdf_option->freight_charges_currency); ?></td>
                            <?php else: ?>
                                <td ><?php echo e($currency_cfg->alphacode); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__empty_1 = true; $__currentLoopData = $freight_charges_grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin=>$freight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $__currentLoopData = $freight; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destination=>$detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $sum_freight_20= 0;
                                    $sum_freight_40= 0;
                                    $sum_freight_40hc= 0;
                                    $sum_freight_40nor= 0;
                                    $sum_freight_45= 0;
                                    $inland_freight_20= 0;
                                    $inland_freight_40= 0;
                                    $inland_freight_40hc= 0;
                                    $inland_freight_40nor= 0;
                                    $inland_freight_45= 0;
                                ?>  
                                <?php $__currentLoopData = $rate->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $sum_freight_20+=$value->total_20;
                                        $sum_freight_40+=$value->total_40;
                                        $sum_freight_40hc+=$value->total_40hc;
                                        $sum_freight_40nor+=$value->total_40nor;
                                        $sum_freight_45+=$value->total_45;                                
                                    ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr class="text-center color-table">
                                <td >
                                    <?php if($rate->origin_port_id!=''): ?> 
                                        <?php echo e($rate->origin_port->name); ?>, <?php echo e($rate->origin_port->code); ?> 
                                    <?php elseif($rate->origin_address!=''): ?> 
                                        <?php echo e($rate->origin_address); ?> 
                                    <?php else: ?> 
                                        <?php echo e($rate->origin_airport->name); ?>, <?php echo e($rate->origin_airport->code); ?>

                                    <?php endif; ?>
                                </td>
                                <td >
                                    <?php if($rate->destination_port_id!=''): ?> 
                                        <?php echo e($rate->destination_port->name); ?>, <?php echo e($rate->destination_port->code); ?> 
                                    <?php elseif($rate->destination_address!=''): ?> 
                                        <?php echo e($rate->destination_address); ?> 
                                    <?php else: ?> 
                                        <?php echo e($rate->destination_airport->name); ?>, <?php echo e($rate->destination_airport->code); ?>

                                    <?php endif; ?>
                                </td>                            
                                <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($rate->carrier->name); ?></td>
                                <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e(@$sum_freight_20+$rate->total_rate20); ?></td>
                                <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e(@$sum_freight_40+$rate->total_rate40); ?></td>
                                <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e(@$sum_freight_40hc+$rate->total_rate40hc); ?></td>
                                <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e(@$sum_freight_40nor+$rate->total_rate40nor); ?></td>
                                <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e(@$sum_freight_45+$rate->total_rate45); ?></td>
                                <?php if($quote->pdf_option->grouped_freight_charges==1): ?>
                                    <td ><?php echo e($quote->pdf_option->freight_charges_currency); ?></td>
                                <?php else: ?>
                                    <td ><?php echo e($currency_cfg->alphacode); ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                adhalda
                                    <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                        <tr class="text-center color-table">
                            <td >
                                <?php if($rate->origin_port_id!=''): ?> 
                                <?php echo e($rate->origin_port->name); ?>, <?php echo e($rate->origin_port->code); ?> 
                                <?php elseif($rate->origin_address!=''): ?> 
                                <?php echo e($rate->origin_address); ?> 
                                <?php else: ?> 
                                <?php echo e($rate->origin_airport->name); ?>, <?php echo e($rate->origin_airport->code); ?>

                                <?php endif; ?>
                            </td>
                            <td >
                                <?php if($rate->destination_port_id!=''): ?> 
                                <?php echo e($rate->destination_port->name); ?>, <?php echo e($rate->destination_port->code); ?> 
                                <?php elseif($rate->destination_address!=''): ?> 
                                <?php echo e($rate->destination_address); ?> 
                                <?php else: ?> 
                                <?php echo e($rate->destination_airport->name); ?>, <?php echo e($rate->destination_airport->code); ?>

                                <?php endif; ?>
                            </td>                            
                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($rate->carrier->name); ?></td>
                            <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e($rate->total_rate20); ?></td>
                            <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e($rate->total_rate40); ?></td>
                            <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e($rate->total_rate40hc); ?></td>
                            <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e($rate->total_rate40nor); ?></td>
                            <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e($rate->total_rate45); ?></td>
                            <?php if($quote->pdf_option->grouped_freight_charges==1): ?>
                                <td ><?php echo e($quote->pdf_option->freight_charges_currency); ?></td>
                            <?php else: ?>
                                <td ><?php echo e($currency_cfg->alphacode); ?></td>
                            <?php endif; ?>
                        </tr>
                    
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Freights table detailed-->
        <?php if($quote->pdf_option->grouped_freight_charges==0 && $quote->pdf_option->show_type=='detailed' ): ?>
            <?php $__currentLoopData = $freight_charges_detailed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destination => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!--<div <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                        <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Freight charges - <?php echo e($origin); ?> | <?php echo e($destination); ?></p>
                        <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos de flete - <?php echo e($origin); ?> | <?php echo e($destination); ?></p>
                        <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Encargos de frete - <?php echo e($origin); ?> | <?php echo e($destination); ?></p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1"  <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>POL</b></th>
                                <th class="unit"><b>POD</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><b><?php if($quote->pdf_option->language=='English'): ?> Carrier <?php elseif($quote->pdf_option->language=='Spanish'): ?> Línea marítima <?php else: ?> Linha Maritima <?php endif; ?></b></th>
                                <th <?php echo e(@$equipmentHides['20']); ?>><b>20'</b></th>
                                <th <?php echo e(@$equipmentHides['40']); ?>><b>40'</b></th>
                                <th <?php echo e(@$equipmentHides['40hc']); ?>><b>40' HC</b></th>
                                <th <?php echo e(@$equipmentHides['40nor']); ?>><b>40' NOR</b></th>
                                <th <?php echo e(@$equipmentHides['45']); ?>><b>45'</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>Currency</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Moneda</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Moeda</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $sum_freight_20= 0;
                                $sum_freight_40= 0;
                                $sum_freight_40hc= 0;
                                $sum_freight_40nor= 0;
                                $sum_freight_45= 0;
                            ?>
                            <?php $__currentLoopData = $rate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $r->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($v->type_id==3): ?>
                                        <?php
                                            $total_freight_20= 0;
                                            $total_freight_40= 0;
                                            $total_freight_40hc= 0;
                                            $total_freight_40nor= 0;
                                            $total_freight_45= 0;
                                            $sum_freight_20+=$v->total_20;
                                            $sum_freight_40+=$v->total_40;
                                            $sum_freight_40hc+=$v->total_40hc;
                                            $sum_freight_40nor+=$v->total_40nor;
                                            $sum_freight_45+=$v->total_45;
                                        ?>
                                        <tr class="text-center color-table">
                                            <td><?php echo e($v->surcharge->name); ?></td>
                                            <td><?php echo e($v->calculation_type->name); ?></td>
                                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($r->carrier->name); ?></td>
                                            <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e($v->total_20); ?></td>
                                            <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e($v->total_40); ?></td>
                                            <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e($v->total_40hc); ?></td>
                                            <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e($v->total_40nor); ?></td>
                                            <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e($v->total_45); ?></td>
                                            <?php if($quote->pdf_option->grouped_freight_charges==1): ?>
                                                <td><?php echo e($quote->pdf_option->freight_charges_currency); ?></td>
                                            <?php else: ?>
                                                <td><?php echo e($currency_cfg->alphacode); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><b>Total local charges</b></td>
                            <td></td>
                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>></td>
                            <td <?php echo e(@$equipmentHides['20']); ?>><b><?php echo e(number_format(@$sum_freight_20, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['40']); ?>><b><?php echo e(number_format(@$sum_freight_40, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['40hc']); ?>><b><?php echo e(number_format(@$sum_freight_40hc, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['40nor']); ?>><b><?php echo e(number_format(@$sum_freight_40nor, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['45']); ?>><b><?php echo e(number_format(@$sum_freight_45, 2, '.', '')); ?></b></td>
                            <?php if($quote->pdf_option->grouped_freight_charges==1): ?>
                                <td><b><?php echo e($quote->pdf_option->freight_charges_currency); ?></b></td>
                            <?php else: ?>
                                <td><b><?php echo e($currency_cfg->alphacode); ?></b></td>
                            <?php endif; ?>     
                        </tr>
                    </tbody>
                    </table>-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <!-- ORIGINS -->

        <!-- ALL in origin table -->
        <?php if($quote->pdf_option->grouped_origin_charges==1 && $quote->pdf_option->show_type=='detailed' ): ?>
            <?php $__currentLoopData = $origin_charges_grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin=>$detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <br>
                <div <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                    <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Origin charges - <?php echo e($origin); ?></p>
                    <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos de origen - <?php echo e($origin); ?></p>
                    <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Encargos de origem - <?php echo e($origin); ?></p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit" colspan="2"><b>Charge</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><b><?php if($quote->pdf_option->language=='English'): ?> Carrier <?php elseif($quote->pdf_option->language=='Spanish'): ?> Línea marítima <?php else: ?> Linha Maritima <?php endif; ?></b></th>
                            <th <?php echo e(@$equipmentHides['20']); ?>><b>20'</b></th>
                            <th <?php echo e(@$equipmentHides['40']); ?>><b>40'</b></th>
                            <th <?php echo e(@$equipmentHides['40hc']); ?>><b>40' HC</b></th>
                            <th <?php echo e(@$equipmentHides['40nor']); ?>><b>40' NOR</b></th>
                            <th <?php echo e(@$equipmentHides['45']); ?>><b>45'</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>Currency</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Moneda</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Moeda</b></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $sum_origin_20= 0;
                                $sum_origin_40= 0;
                                $sum_origin_40hc= 0;
                                $sum_origin_40nor= 0;
                                $sum_origin_45= 0;
                                $inland_origin_20= 0;
                                $inland_origin_40= 0;
                                $inland_origin_40hc= 0;
                                $inland_origin_40nor= 0;
                                $inland_origin_45= 0;
                            ?>  
                            <?php $__currentLoopData = $rate->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $sum_origin_20+=$value->total_20;
                                    $sum_origin_40+=$value->total_40;
                                    $sum_origin_40hc+=$value->total_40hc;
                                    $sum_origin_40nor+=$value->total_40nor;
                                    $sum_origin_45+=$value->total_45;                                
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $rate->inland; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $inland_origin_20+=$value->total_20;
                                    $inland_origin_40+=$value->total_40;
                                    $inland_origin_40hc+=$value->total_40hc;
                                    $inland_origin_40nor+=$value->total_40nor;
                                    $inland_origin_45+=$value->total_45;                                
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="text-center color-table">
                            <td colspan="2">Total Origin Charges</td>
                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($rate->carrier->name); ?></td>
                            <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e(@$sum_origin_20+@$inland_origin_20); ?></td>
                            <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e(@$sum_origin_40+@$inland_origin_40); ?></td>
                            <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e(@$sum_origin_40hc+@$inland_origin_40hc); ?></td>
                            <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e(@$sum_origin_40nor+@$inland_origin_40nor); ?></td>
                            <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e(@$sum_origin_45+@$inland_origin_45); ?></td>
                            <?php if($quote->pdf_option->grouped_origin_charges==1): ?>
                                <td ><?php echo e($quote->pdf_option->origin_charges_currency); ?></td>
                            <?php else: ?>
                                <td ><?php echo e($currency_cfg->alphacode); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <!-- Origins detailed -->
        <?php if($quote->pdf_option->grouped_origin_charges==0 && $quote->pdf_option->show_type=='detailed' ): ?>
            <?php $__currentLoopData = $origin_charges_detailed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                        <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Origin charges - <?php echo e($origin); ?></p>
                        <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos de origen - <?php echo e($origin); ?></p>
                        <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Encargos de origem - <?php echo e($origin); ?></p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1"  <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>Charge</b></th>
                                <th class="unit"><b>Detail</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><b><?php if($quote->pdf_option->language=='English'): ?> Carrier <?php elseif($quote->pdf_option->language=='Spanish'): ?> Línea marítima <?php else: ?> Linha Maritima <?php endif; ?></b></th>
                                <th <?php echo e(@$equipmentHides['20']); ?>><b>20'</b></th>
                                <th <?php echo e(@$equipmentHides['40']); ?>><b>40'</b></th>
                                <th <?php echo e(@$equipmentHides['40hc']); ?>><b>40' HC</b></th>
                                <th <?php echo e(@$equipmentHides['40nor']); ?>><b>40' NOR</b></th>
                                <th <?php echo e(@$equipmentHides['45']); ?>><b>45'</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>Currency</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Moneda</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Moeda</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $sum_origin_20= 0;
                                $sum_origin_40= 0;
                                $sum_origin_40hc= 0;
                                $sum_origin_40nor= 0;
                                $sum_origin_45= 0;
                                $inland_20= 0;
                                $inland_40= 0;
                                $inland_40hc= 0;
                                $inland_40nor= 0;
                                $inland_45= 0;
                            ?>
                            <?php $__currentLoopData = $rate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $r->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($v->type_id==1): ?>
                                        <?php
                                            $total_origin_20= 0;
                                            $total_origin_40= 0;
                                            $total_origin_40hc= 0;
                                            $total_origin_40nor= 0;
                                            $total_origin_45= 0;                                   
                                            $sum_origin_20+=$v->total_20;
                                            $sum_origin_40+=$v->total_40;
                                            $sum_origin_40hc+=$v->total_40hc;
                                            $sum_origin_40nor+=$v->total_40nor;
                                            $sum_origin_45+=$v->total_45;
                                        ?>
                                        <tr class="text-center color-table">
                                            <td><?php echo e($v->surcharge->name); ?></td>
                                            <td><?php echo e($v->calculation_type->name); ?></td>
                                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($r->carrier->name); ?></td>
                                            <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e($v->total_20); ?></td>
                                            <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e($v->total_40); ?></td>
                                            <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e($v->total_40hc); ?></td>
                                            <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e($v->total_40nor); ?></td>
                                            <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e($v->total_45); ?></td>
                                            <?php if($quote->pdf_option->grouped_origin_charges==1): ?>
                                                <td><?php echo e($quote->pdf_option->origin_charges_currency); ?></td>
                                            <?php else: ?>
                                                <td><?php echo e($currency_cfg->alphacode); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!$r->inland->isEmpty()): ?>{
                                    <?php $__currentLoopData = $r->inland; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($v->type=='Origin'): ?>
                                            <?php
                                                $inland_20+=$v->total_20;
                                                $inland_40+=$v->total_40;
                                                $inland_40hc+=$v->total_40hc;
                                                $inland_40nor+=$v->total_40nor;
                                                $inland_45+=$v->total_45;
                                            ?>
                                            <tr class="text-center color-table">
                                                <td><?php echo e($v->provider); ?></td>
                                                <td><?php echo e($v->distance); ?></td>
                                                <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($r->carrier->name); ?></td>
                                                <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e($v->total_20); ?></td>
                                                <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e($v->total_40); ?></td>
                                                <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e($v->total_40hc); ?></td>
                                                <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e($v->total_40nor); ?></td>
                                                <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e($v->total_45); ?></td>
                                                <?php if($quote->pdf_option->grouped_origin_charges==1): ?>
                                                    <td><?php echo e($quote->pdf_option->origin_charges_currency); ?></td>
                                                <?php else: ?>
                                                    <td><?php echo e($currency_cfg->alphacode); ?></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><b>Total local charges</b></td>
                            <td></td>
                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>></td>
                            <td <?php echo e(@$equipmentHides['20']); ?>><b><?php echo e(number_format(@$sum_origin_20+@$inland_20, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['40']); ?>><b><?php echo e(number_format(@$sum_origin_40+@$inland_40, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['40hc']); ?>><b><?php echo e(number_format(@$sum_origin_40hc+@$inland_40hc, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['40nor']); ?>><b><?php echo e(number_format(@$sum_origin_40nor+@$inland_40nor, 2, '.', '')); ?></b></td>
                            <td <?php echo e(@$equipmentHides['45']); ?>><b><?php echo e(number_format(@$sum_origin_45+@$inland_45, 2, '.', '')); ?></b></td>
                            <?php if($quote->pdf_option->grouped_origin_charges==1): ?>
                            <td><b><?php echo e($quote->pdf_option->origin_charges_currency); ?></b></td>
                            <?php else: ?>
                            <td><b><?php echo e($currency_cfg->alphacode); ?></b></td>
                            <?php endif; ?>     
                        </tr>
                    </tbody>
                </table>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <!-- DESTINATIONS -->

        <!-- ALL in destination table -->
        <?php if($quote->pdf_option->grouped_destination_charges==1 && $quote->pdf_option->show_type=='detailed' ): ?>
            <?php $__currentLoopData = $destination_charges_grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin=>$detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <br>
                <div <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                    <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Destination charges - <?php echo e($origin); ?></p>
                    <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos de destino - <?php echo e($origin); ?></p>
                    <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Encargos de destino - <?php echo e($origin); ?></p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit" colspan="2"><b>Charge</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><b><?php if($quote->pdf_option->language=='English'): ?> Carrier <?php elseif($quote->pdf_option->language=='Spanish'): ?> Línea marítima <?php else: ?> Linha Maritima <?php endif; ?></b></th>
                            <th <?php echo e(@$equipmentHides['20']); ?>><b>20'</b></th>
                            <th <?php echo e(@$equipmentHides['40']); ?>><b>40'</b></th>
                            <th <?php echo e(@$equipmentHides['40hc']); ?>><b>40' HC</b></th>
                            <th <?php echo e(@$equipmentHides['40nor']); ?>><b>40' NOR</b></th>
                            <th <?php echo e(@$equipmentHides['45']); ?>><b>45'</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>Currency</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Moneda</b></th>
                            <th class="unit" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Moeda</b></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $sum_destination_20= 0;
                            $sum_destination_40= 0;
                            $sum_destionation_40hc= 0;
                            $sum_destination_40nor= 0;
                            $sum_destination_45= 0;
                        ?>  
                        <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $rate->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $sum_destination_20+=$value->total_20;
                                    $sum_destination_40+=$value->total_40;
                                    $sum_destionation_40hc+=$value->total_40hc;
                                    $sum_destination_40nor+=$value->total_40nor;
                                    $sum_destination_45+=$value->total_45;                                
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="text-center color-table">
                            <td colspan="2">Total Destination Charges</td>
                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($rate->carrier->name); ?></td>
                            <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e(@$sum_destination_20); ?></td>
                            <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e(@$sum_destination_40); ?></td>
                            <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e(@$sum_destionation_40hc); ?></td>
                            <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e(@$sum_destination_40nor); ?></td>
                            <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e(@$sum_destination_45); ?></td>
                            <?php if($quote->pdf_option->grouped_destination_charges==1): ?>
                            <td ><?php echo e($quote->pdf_option->destination_charges_currency); ?></td>
                            <?php else: ?>
                            <td ><?php echo e($currency_cfg->alphacode); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <!-- Destinations detailed -->
        <?php if($quote->pdf_option->grouped_destination_charges==0 && $quote->pdf_option->show_type=='detailed' ): ?>
        <?php $__currentLoopData = $destination_charges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destination => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                        <p class="title" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>>Destination charges - <?php echo e($destination); ?></p>
                        <p class="title" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>>Costos de destino - <?php echo e($destination); ?></p>
                        <p class="title" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>>Encargos de destino - <?php echo e($destination); ?></p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1"  <?php echo e($quote->pdf_option->show_type=='detailed' ? '':'hidden'); ?>>
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>Charge</b></th>
                                <th class="unit"><b>Detail</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><b><?php if($quote->pdf_option->language=='English'): ?> Carrier <?php elseif($quote->pdf_option->language=='Spanish'): ?> Línea marítima <?php else: ?> Linha Maritima <?php endif; ?></b></th>
                                <th <?php echo e(@$equipmentHides['20']); ?>><b>20'</b></th>
                                <th <?php echo e(@$equipmentHides['40']); ?>><b>40'</b></th>
                                <th <?php echo e(@$equipmentHides['40hc']); ?>><b>40' HC</b></th>
                                <th <?php echo e(@$equipmentHides['40nor']); ?>><b>40' NOR</b></th>
                                <th <?php echo e(@$equipmentHides['45']); ?>><b>45'</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>Currency</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>Moneda</b></th>
                                <th class="unit" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>Moeda</b></th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php $__currentLoopData = $item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $sum_destination_20= 0;
                            $sum_destination_40= 0;
                            $sum_destination_40hc= 0;
                            $sum_destination_40nor= 0;
                            $sum_destination_45= 0;
                            $inland_20= 0;
                            $inland_40= 0;
                            $inland_40hc= 0;
                            $inland_40nor= 0;
                            $inland_45= 0;
                        ?>
                            <?php $__currentLoopData = $rate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $r->charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($v->type_id==2): ?>
                                        <?php
                                            $total_destination_20= 0;
                                            $total_destination_40= 0;
                                            $total_destination_40hc= 0;
                                            $total_destination_40nor= 0;
                                            $total_destination_45= 0;                                   
                                            $sum_destination_20+=$v->total_20;
                                            $sum_destination_40+=$v->total_40;
                                            $sum_destination_40hc+=$v->total_40hc;
                                            $sum_destination_40nor+=$v->total_40nor;
                                            $sum_destination_45+=$v->total_45;
                                        ?>
                                        <tr class="text-center color-table">
                                            <td><?php echo e($v->surcharge->name); ?></td>
                                            <td><?php echo e($v->calculation_type->name); ?></td>
                                            <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($r->carrier->name); ?></td>
                                            <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e($v->total_20); ?></td>
                                            <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e($v->total_40); ?></td>
                                            <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e($v->total_40hc); ?></td>
                                            <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e($v->total_40nor); ?></td>
                                            <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e($v->total_45); ?></td>
                                            <?php if($quote->pdf_option->grouped_destination_charges==1): ?>
                                                <td><?php echo e($quote->pdf_option->destination_charges_currency); ?></td>
                                            <?php else: ?>
                                                <td><?php echo e($currency_cfg->alphacode); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!$r->inland->isEmpty()): ?>{
                                    <?php $__currentLoopData = $r->inland; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($v->type=='Destination'): ?>
                                            <?php
                                                $inland_20+=$v->total_20;
                                                $inland_40+=$v->total_40;
                                                $inland_40hc+=$v->total_40hc;
                                                $inland_40nor+=$v->total_40nor;
                                                $inland_45+=$v->total_45;
                                            ?>
                                            <tr class="text-center color-table">
                                                <td><?php echo e($v->provider); ?></td>
                                                <td><?php echo e($v->distance); ?></td>
                                                <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>><?php echo e($r->carrier->name); ?></td>
                                                <td <?php echo e(@$equipmentHides['20']); ?>><?php echo e($v->total_20); ?></td>
                                                <td <?php echo e(@$equipmentHides['40']); ?>><?php echo e($v->total_40); ?></td>
                                                <td <?php echo e(@$equipmentHides['40hc']); ?>><?php echo e($v->total_40hc); ?></td>
                                                <td <?php echo e(@$equipmentHides['40nor']); ?>><?php echo e($v->total_40nor); ?></td>
                                                <td <?php echo e(@$equipmentHides['45']); ?>><?php echo e($v->total_45); ?></td>
                                                <?php if($quote->pdf_option->grouped_origin_charges==1): ?>
                                                    <td><?php echo e($quote->pdf_option->origin_charges_currency); ?></td>
                                                <?php else: ?>
                                                    <td><?php echo e($currency_cfg->alphacode); ?></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><b>Total local charges</b></td>
                                <td></td>
                                <td <?php echo e($quote->pdf_option->show_carrier==1 ? '':'hidden'); ?>></td>
                                <td <?php echo e(@$equipmentHides['20']); ?>><b><?php echo e(number_format(@$sum_destination_20+@$inland_20, 2, '.', '')); ?></b></td>
                                <td <?php echo e(@$equipmentHides['40']); ?>><b><?php echo e(number_format(@$sum_destination_40+@$inland_40, 2, '.', '')); ?></b></td>
                                <td <?php echo e(@$equipmentHides['40hc']); ?>><b><?php echo e(number_format(@$sum_destination_40hc+@$inland_40hc, 2, '.', '')); ?></b></td>
                                <td <?php echo e(@$equipmentHides['40nor']); ?>><b><?php echo e(number_format(@$sum_destination_40nor+@$inland_40nor, 2, '.', '')); ?></b></td>
                                <td <?php echo e(@$equipmentHides['45']); ?>><b><?php echo e(number_format(@$sum_destination_45+@$inland_45, 2, '.', '')); ?></b></td>
                                <?php if($quote->pdf_option->grouped_destination_charges==1): ?>
                                    <td><b><?php echo e($quote->pdf_option->destination_charges_currency); ?></b></td>
                                <?php else: ?>
                                    <td><b><?php echo e($currency_cfg->alphacode); ?></b></td>
                                <?php endif; ?>     
                            </tr>
                        </tbody>
                    </table>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <br>
        <?php if($quote->payment_conditions!=''): ?>
            <br>
            <div class="clearfix">
                <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote header-table">
                        <tr>
                            <th class="unit text-left" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>&nbsp;&nbsp;&nbsp;Payments conditions</b></th>
                            <th class="unit text-left" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>&nbsp;&nbsp;&nbsp;Condiciones de pago</b></th>
                            <th class="unit text-left" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>&nbsp;&nbsp;&nbsp;Condições de pagamento</b></th>
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
        <?php if($quote->terms_and_conditions!=''): ?>
            <br>
            <div class="clearfix">
                <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote header-table">
                        <tr>
                            <th class="unit text-left" <?php echo e($quote->pdf_option->language=='English' ? '':'hidden'); ?>><b>&nbsp;&nbsp;&nbsp;Terms and conditions</b></th>
                            <th class="unit text-left" <?php echo e($quote->pdf_option->language=='Spanish' ? '':'hidden'); ?>><b>&nbsp;&nbsp;&nbsp;Términos y condiciones</b></th>
                            <th class="unit text-left" <?php echo e($quote->pdf_option->language=='Portuguese' ? '':'hidden'); ?>><b>&nbsp;&nbsp;&nbsp;Termos e Condições</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:20px;">
                                <span class="text-justify"><?php echo $quote->terms_and_conditions; ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?> 
</main>
</body>
</html>