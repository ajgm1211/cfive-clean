<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->quote_id}}</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
</head>
<body style="background-color: white; font-size: 11px;">
    <header class="clearfix">

        <div id="logo">
            @if($user->companyUser->logo!='')
            <img src="{{Storage::disk('s3_upload')->url($user->companyUser->logo)}}" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:25px">
            @endif
        </div>
        <div id="company">
            <div>
                <span class="color-title"><b>@if($quote->pdf_option->language=='English')Quotation Id:@elseif($quote->pdf_option->language=='Spanish') Cotización: @else Numero de cotação: @endif</b></span> 
                <span style="color: #20A7EE"><b>#{{$quote->custom_id == '' ? $quote->quote_id:$quote->custom_quote_id}}</b></span>
            </div>
            <div>
                <span class="color-title"><b>@if($quote->pdf_option->language=='English')Date of issue:@elseif($quote->pdf_option->language=='Spanish') Fecha creación: @else Data de emissão: @endif</b></span> {{date_format($quote->created_at, 'M d, Y H:i')}}
            </div>
            @if($quote->validity_start!=''&&$quote->validity_end!='')
            <div>
                <span class="color-title">
                    <b>@if($quote->pdf_option->language=='English')Validity:@elseif($quote->pdf_option->language=='Spanish') Validez: @else Validade: @endif </b>
                </span> 
                {{\Carbon\Carbon::parse( $quote->validity_start)->format('d M Y') }} -  {{\Carbon\Carbon::parse( $quote->validity_end)->format('d M Y') }}
            </div>
            @endif
        </div>
    </header>
    <main>
        <div id="details" class="clearfix details">
            <div class="client">
                <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>From:</b></p>
                <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>De:</b></p>
                <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>A partir de:</b></p>
                <span id="destination_input" style="line-height: 0.5">
                    <p>{{$user->name}} {{$user->lastname}}</p>
                    <p><span style="color: #031B4E"><b>{{$user->companyUser->name}}</b></span></p>
                    <p>{{$user->companyUser->address}}</p>
                    <p>{{$user->phone}}</p>
                    <p>{{$user->email}}</p>
                </span>
            </div>
            <div class="company text-right" style="float: right; width: 350px;">
                <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>To:</b></p>
                <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Para:</b></p>
                <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Para:</b></p>
                <span id="destination_input" style="line-height: 0.5">
                    @if($quote->pdf_option->show_logo==1)
                    @if($quote->company->logo!='')
                    <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px">
                    @endif
                    @endif
                    <p>{{$quote->contact->first_name.' '.$quote->contact->last_name}}</p>
                    <p><span style="color: #031B4E"><b>{{$quote->company->business_name}}</b></span></p>
                    <p>{{$quote->company->address}}</p>
                    <p>{{$quote->contact->phone}}</p>
                    <p>{{$quote->contact->email}}</p>
                </span>
            </div>
        </div>
        <br>
        <div class="clearfix">
        <!--<div class="">
            <table class="" style="width: 45%; float: left;">
                <thead class="title-quote text-center header-table">
                <tr >
                    <th class="unit"><b>Origin</b></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div id="origin_input" style="color: #787878;">
                            @if($quote->origin_harbor_id!='')
                                Port: {{$quote->origin_harbor->name}}, {{$quote->origin_harbor->code}}
                            @endif
                            @if($quote->origin_airport_id!='')
                                Airport: {{$quote->origin_airport->name}}
                            @endif
                            <br>
                            @if($quote->origin_address!='')
                                Address: {{$quote->origin_address}}
                            @endif
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="" style="width: 45%; float: right;">
                <thead class="title-quote text-center ">
                <tr>
                    <th class="unit"><b>Destination</b></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div id="destination_input" style="color: #787878;">
                            @if($quote->destination_harbor_id!='')
                                Port: {{$quote->destination_harbor->name}}, {{$quote->destination_harbor->code}}
                            @endif
                            @if($quote->destination_airport_id!='')
                                Airport: {{$quote->destination_airport->name}}
                            @endif
                            <br>
                            @if($quote->destination_address!='')
                                Address: {{$quote->destination_address}}
                            @endif
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>-->
    </div>
    <br>
    @if($quote->pdf_option->show_type=='total in')
        <div {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
            <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total estimated costs</p>
            <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos totales estimados</p>
            <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Custos totais estimados</p>
            <br>
        </div>
    @else
        <div {{$quote->pdf_option->grouped_total_currency==1 ? '':'hidden'}}>
            <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total estimated costs</p>
            <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos totales estimados</p>
            <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Custos totais estimados</p>
            <br>
        </div>
    @endif
    <!-- All in table freights-->
    @if($quote->pdf_option->show_type=='total in')
        <table border="0" cellspacing="1" cellpadding="1" {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
    @else
        <table border="0" cellspacing="1" cellpadding="1" {{$quote->pdf_option->grouped_total_currency==1 ? '':'hidden'}}>
    @endif
        <thead class="title-quote text-center header-table">
            <tr >
                <th class="unit"><b>POL</b></th>
                <th class="unit"><b>POD</b></th>
                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                <th {{ $equipmentHides['20'] }}><b>20'</b></th>
                <th {{ $equipmentHides['40'] }}><b>40'</b></th>
                <th {{ $equipmentHides['40hc'] }}><b>40' HC</b></th>
                <th {{ $equipmentHides['40nor'] }}><b>40' NOR</b></th>
                <th {{ $equipmentHides['45'] }}><b>45'</b></th>
                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach($rates as $rate)
                <?php 
                    $sum20= 0;
                    $sum40= 0;
                    $sum40hc= 0;
                    $sum40nor= 0;
                    $sum45= 0;
                ?>
                @foreach($rate->charge as $value)
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
                    @if(!$rate->inland->isEmpty())
                        @foreach($rate->inland as $item)
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
                        @endforeach
                    @endif
                @endforeach
                <tr class="text-center color-table">
                    <td >{{$rate->origin_port->name}}, {{$rate->origin_port->code}}</td>
                    <td >{{$rate->destination_port->name}}, {{$rate->destination_port->code}}</td>
                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier->name}}</td>
                    <td {{ $equipmentHides['20'] }}>{{number_format((float)@$sum20+@$inland20, 2, '.', '')}}</td>
                    <td {{ $equipmentHides['40'] }}>{{number_format((float)@$sum40+@$inland40, 2, '.', '')}}</td>
                    <td {{ $equipmentHides['40hc'] }}>{{number_format((float)@$sum40hc+@$inland40hc, 2, '.', '')}}</td>
                    <td {{ $equipmentHides['40nor'] }}>{{number_format((float)@$sum40nor+@$inland40nor, 2, '.', '')}}</td>
                    <td {{ $equipmentHides['45'] }}>{{number_format((float)@$sum45+@$inland45, 2, '.', '')}}</td>
                    <td >{{$quote->pdf_option->grouped_total_currency==0 ? $rate->currency->alphacode:$quote->pdf_option->total_in_currency}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
    </table>
    <br>

    <!-- DETAILED TABLES -->

    <!-- Freights table -->
    <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete</p>
        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
        <br>
    </div>
    <table border="0" cellspacing="1" cellpadding="1" {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
        <thead class="title-quote text-center header-table">
            <tr >
                <th class="unit"><b>POL</b></th>
                <th class="unit"><b>POD</b></th>
                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                <th {{ $equipmentHides['20'] }}><b>20'</b></th>
                <th {{ $equipmentHides['40'] }}><b>40'</b></th>
                <th {{ $equipmentHides['40hc'] }}><b>40' HC</b></th>
                <th {{ $equipmentHides['40nor'] }}><b>40' NOR</b></th>
                <th {{ $equipmentHides['45'] }}><b>45'</b></th>
                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
            </tr>
        </thead>
        <tbody>

            @foreach($freight_charges as $rate)
                <?php 
                    $total= 0;
                    $total40= 0;
                    $total40hc= 0;
                    $total40nor= 0;
                    $total45= 0;
                    $inland20= 0;
                    $inland40= 0;
                    $inland40hc= 0;
                    $inland40nor= 0;
                    $inland45= 0;
                ?>

                @foreach($rate->charge as $v)
                    <?php
                        if($v->type_id==3){
                            $total20=$v->total_20;
                            $total40=$v->total_40;
                            $total40hc=$v->total_40hc;
                            $total40nor=$v->total_40nor;
                            $total45=$v->total_45;
                        }
                    ?>
                @endforeach                
                <tr class="text-center color-table">
                    <td>{{$rate->origin_port->name}}, {{$rate->origin_port->code}}</td>
                    <td>{{$rate->destination_port->name}}, {{$rate->destination_port->code}}</td>
                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier->name}}</td>
                    <td {{ $equipmentHides['20'] }}>{{@$total20}}</td>
                    <td {{ $equipmentHides['40'] }}>{{@$total40}}</td>
                    <td {{ $equipmentHides['40hc'] }}>{{@$total40hc}}</td>
                    <td {{ $equipmentHides['40nor'] }}>{{@$total40nor}}</td>
                    <td {{ $equipmentHides['45'] }}>{{@$total45}}</td>                            
                    <td>{{$rate->currency->alphacode}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="footer" style="border-color: yellow">
        
        </tfoot>
    </table>

    <!-- ALL in origin table -->
    @if($quote->pdf_option->grouped_origin_charges==1 && $quote->pdf_option->show_type=='detailed' )
    <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges grouped </p>
        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de origen agrupados</p>
        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem</p>
        <br>
    </div>
    <table border="0" cellspacing="1" cellpadding="1" >
        <thead class="title-quote text-center header-table">
            <tr >
                <th class="unit"><b>POL</b></th>
                <th class="unit"><b>POD</b></th>
                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                <th {{ $equipmentHides['20'] }}><b>20'</b></th>
                <th {{ $equipmentHides['40'] }}><b>40'</b></th>
                <th {{ $equipmentHides['40hc'] }}><b>40' HC</b></th>
                <th {{ $equipmentHides['40nor'] }}><b>40' NOR</b></th>
                <th {{ $equipmentHides['45'] }}><b>45'</b></th>
                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach($rates as $rate)
                <?php 
                    $sum20= 0;
                    $sum40= 0;
                    $sum40hc= 0;
                    $sum40nor= 0;
                    $sum45= 0;
                    $inland20= 0;
                    $inland40= 0;
                    $inland40hc= 0;
                    $inland40nor= 0;
                    $inland45= 0;
                    $carrier_id='';
                ?>

                @foreach($rate->charge as $value)
                    <?php
                        if($value->type_id==1){
                            $array_amounts = json_decode($value->amount,true);
                            $array_markups = json_decode($value->markups,true);
                            if(isset($array_amounts['c20']) && isset($array_markups['c20'])){
                                $amount20=$array_amounts['c20'];
                                $markup20=$array_markups['c20'];
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    if($quote->pdf_option->origin_charges_currency=='USD'){
                                        $total20=($amount20+$markup20)/$value->currency_usd;
                                    }else{
                                        $total20=($amount20+$markup20)/$value->currency_eur;
                                    }
                                }else{
                                    if($currency_cfg->alphacode=='USD'){
                                        $total20=($amount20+$markup20)/$value->currency_usd;
                                    }else{
                                        $total20=($amount20+$markup20)/$value->currency_eur;
                                    }
                                }
                                //$total20=$amount20+$markup20;
                                $sum20 += number_format($total20, 2, '.', '');
                            }
                            if(isset($array_amounts['c40']) && isset($array_markups['c40'])){
                                $amount40=$array_amounts['c40'];
                                $markup40=$array_markups['c40'];
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    if($quote->pdf_option->origin_charges_currency=='USD'){
                                        $total40=($amount40+$markup40)/$value->currency_usd;
                                    }else{
                                        $total40=($amount40+$markup40)/$value->currency_eur;
                                    }
                                }else{
                                    if($currency_cfg->alphacode=='USD'){
                                        $total40=($amount40+$markup40)/$value->currency_usd;
                                    }else{
                                        $total40=($amount40+$markup40)/$value->currency_eur;
                                    }
                                }
                                $sum40 += number_format($total40, 2, '.', '');
                            }
                            if(isset($array_amounts['c40hc']) && isset($array_markups['c40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                $markup40hc=$array_markups['c40hc'];
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    if($quote->pdf_option->origin_charges_currency=='USD'){
                                        $total40hc=($amount40hc+$markup40hc)/$value->currency_usd;
                                    }else{
                                        $total40hc=($amount40hc+$markup40hc)/$value->currency_eur;
                                    }
                                }else{
                                    if($currency_cfg->alphacode=='USD'){
                                        $total40hc=($amount40hc+$markup40hc)/$value->currency_usd;
                                    }else{
                                        $total40hc=($amount40hc+$markup40hc)/$value->currency_eur;
                                    }
                                }
                                $sum40hc += number_format($total40hc, 2, '.', '');
                            }
                            if(isset($array_amounts['c40nor']) && isset($array_markups['c40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                $markup40nor=$array_markups['c40nor'];
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    if($quote->pdf_option->origin_charges_currency=='USD'){
                                        $total40nor=($amount40nor+$markup40nor)/$value->currency_usd;
                                    }else{
                                        $total40nor=($amount40nor+$markup40nor)/$value->currency_eur;
                                    }
                                }else{
                                    if($currency_cfg->alphacode=='USD'){
                                        $total40nor=($amount40nor+$markup40nor)/$value->currency_usd;
                                    }else{
                                        $total40nor=($amount40nor+$markup40nor)/$value->currency_eur;
                                    }
                                }
                                //$total40nor=$amount40nor+$markup40nor;
                                $sum40nor += number_format($total40nor, 2, '.', '');
                            }
                            if(isset($array_amounts['c45']) && isset($array_markups['c45'])){
                                $amount45=$array_amounts['c45'];
                                $markup45=$array_markups['c45'];
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    if($quote->pdf_option->origin_charges_currency=='USD'){
                                        $total45=($amount45+$markup45)/$value->currency_usd;
                                    }else{
                                        $total45=($amount45+$markup45)/$value->currency_eur;
                                    }
                                }else{
                                    if($currency_cfg->alphacode=='USD'){
                                        $total45=($amount45+$markup45)/$value->currency_usd;
                                    }else{
                                        $total45=($amount45+$markup45)/$value->currency_eur;
                                    }
                                }
                                //$total45=$amount45+$markup45;
                                $sum45 += number_format($total45, 2, '.', '');
                            }

                            if(!$rate->inland->isEmpty()){
                                foreach($rate->inland as $item){
                                
                                    $arr_amounts = json_decode($item->rate,true);
                                    $arr_markups = json_decode($item->markup,true);
                                    if(isset($arr_amounts['c20']) && isset($arr_markups['c20'])){
                                        $amount_inland20=$arr_amounts['c20'];
                                        $markup_inland20=$arr_markups['c20'];
                                        $total_inland20=$amount_inland20+$markup_inland20;
                                        $inland20 += $total_inland20;
                                    }
                                    if(isset($arr_amounts['c40']) && isset($arr_markups['c40'])){
                                        $amount_inland40=$arr_amounts['c40'];
                                        $markup_inland40=$arr_markups['c40'];
                                        $total_inland40=$amount_inland40+$markup_inland40;
                                        $inland40 += $total_inland40;
                                    }
                                    if(isset($arr_amounts['c40hc']) && isset($arr_markups['c40hc'])){
                                        $amount_inland40hc=$arr_amounts['c40hc'];
                                        $markup_inland40hc=$arr_markups['c40hc'];
                                        $total_inland40hc=$amount_inland40hc+$markup_inland40hc;
                                        $inland40hc += $total_inland40hc;
                                    }
                                    if(isset($arr_amounts['c40nor']) && isset($arr_markups['c40nor'])){
                                        $amount_inland40nor=$arr_amounts['c40nor'];
                                        $markup_inland40nor=$arr_markups['c40nor'];
                                        $total_inland40nor=$amount_inland40nor+$markup4_inland40nor;
                                        $inland40nor += $total_inland40nor;
                                    }
                                    if(isset($arr_amounts['c45']) && isset($arr_markups['c45'])){
                                        $amount_inland45=$arr_amounts['c45'];
                                        $markup_inland45=$arr_markups['c45'];
                                        $total_inland45=$amount_inland45+$markup_inland45;
                                        $inland45 += $total_inland45;
                                    }
                                }
                            }      
                        }
                    ?>
                @endforeach
                @if(@$sum20>0 || @$sum40>0 || @$sum40hc>0 || @$sum40nor>0 || @$sum45>0)
                    <tr class="text-center color-table">
                        <td >{{$rate->origin_port->name}}, {{$rate->origin_port->code}}</td>
                        <td >{{$rate->destination_port->name}}, {{$rate->destination_port->code}}</td>
                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier->name}}</td>
                        <td {{ $equipmentHides['20'] }}>{{@$sum20}}</td>
                        <td {{ $equipmentHides['40'] }}>{{@$sum40}}</td>
                        <td {{ $equipmentHides['40hc'] }}>{{@$sum40hc}}</td>
                        <td {{ $equipmentHides['40nor'] }}>{{@$sum40nor}}</td>
                        <td {{ $equipmentHides['45'] }}>{{@$sum45}}</td>
                        @if($quote->pdf_option->grouped_origin_charges==1)
                            @if($quote->pdf_option->origin_charges_currency=='USD')
                                <td >USD</td>
                            @else
                                <td>EUR</td>
                            @endif
                        @else
                            @if($currency_cfg->alphacode=='USD')
                                <td >USD</td>
                            @else
                                <td>EUR</td>
                            @endif
                        @endif
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot class="footer" style="border-color: yellow">
        
        </tfoot>
    </table>
    @endif
    <!-- -->
    @foreach($origin_charges as $carrier => $value)
        <?php 
            $total20= 0;
            $total40= 0;
            $total40hc= 0;
            $total40nor= 0;
            $total45= 0;
            $sum40hc= 0;
            $sum40nor= 0;
            $sum45= 0;
            $inland20= 0;
            $inland40= 0;
            $inland40hc= 0;
            $inland40nor= 0;
            $inland45= 0;
        ?>
        
        <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
            <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$carrier}}</p>
            <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de origen - {{$carrier}}</p>
            <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$carrier}}</p>
            <br>
        </div>        
        @foreach($value as $item)
            @foreach($item as $carrier=>$rate)
            <table border="0" cellspacing="1" cellpadding="1"  {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit"><b>POL</b></th>
                        <th class="unit"><b>POD</b></th>
                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                        <th {{ $equipmentHides['20'] }}><b>20'</b></th>
                        <th {{ $equipmentHides['40'] }}><b>40'</b></th>
                        <th {{ $equipmentHides['40hc'] }}><b>40' HC</b></th>
                        <th {{ $equipmentHides['40nor'] }}><b>40' NOR</b></th>
                        <th {{ $equipmentHides['45'] }}><b>45'</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rate as $r)
                        @foreach($r->charge as $v)
                            <?php
                                if($v->type_id==1){
                                    $total20+=$v->total_20;
                                    $total40+=$v->total_40;
                                    $total40hc+=$v->total_40hc;
                                    $total40nor+=$v->total_40nor;
                                    $total45+=$v->total_45;
                                }
                            ?>
                        @endforeach
                        <tr>
                            <td>{{$r->origin_port->name}}, {{$r->origin_port->code}}</td>
                            <td>{{$r->destination_port->name}}, {{$r->destination_port->code}}</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$r->carrier->name}}</td>
                            <td {{ $equipmentHides['20'] }}>{{$total20}}</td>
                            <td {{ $equipmentHides['40'] }}>{{$total40}}</td>
                            <td {{ $equipmentHides['40hc'] }}>{{$total40hc}}</td>
                            <td {{ $equipmentHides['40nor'] }}>{{$total40nor}}</td>
                            <td {{ $equipmentHides['45'] }}>{{$total45}}</td>                            
                            <td>{{$r->currency->alphacode}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endforeach
        @endforeach
    @endforeach
</main>
</body>
</html>