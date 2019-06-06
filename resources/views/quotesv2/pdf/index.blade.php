<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->quote_id}}</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
    <style>
      @font-face {
        font-family: "Sailec";
        src: url(public/fonts/sailec.otf);
      }
      p, span {
        font-family: "Sailec", sans-serif;
      }
    </style>
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
                <span style="color: #20A7EE"><b>#{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</b></span>
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
                    <p>{{$quote->user->name}} {{$quote->user->lastname}}</p>
                    <p><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                    <p>{{$user->companyUser->address}}</p>
                    <p>{{$user->phone}}</p>
                    <p>{{$quote->user->email}}</p>
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
                    <p><span style="color: #4e4e4e"><b>{{$quote->company->business_name}}</b></span></p>
                    <p>{{$quote->company->address}}</p>
                    <p>{{$quote->contact->phone}}</p>
                    <p>{{$quote->contact->email}}</p>
                </span>
            </div>
        </div>
        <br>
        <div class="company" style="color: #1D3A6E;">
            <p class="title"><b>Cargo details</b></p>
            <br>
            @if($quote->total_quantity!='' && $quote->total_quantity>0)
                <!--<div class="row">
                    <div class="col-md-3">
                        <div id="cargo_details_cargo_type_p"><b class="title">Cargo type:</b> {{$quote->cargo_type == 1 ? 'Pallets' : 'Packages'}}</div>
                    </div>
                    <div class="col-md-3">
                        <div id="cargo_details_total_quantity_p"><b class="title">Total quantity:</b> {{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</div>
                    </div>
                    <div class="col-md-3">
                        <div id="cargo_details_total_weight_p"><b class="title">Total weight: </b> {{$quote->total_weight != '' ? $quote->total_weight.'Kg' : ''}}</div>
                    </div>
                    <div class="col-md-3">
                        <p id="cargo_details_total_volume_p"><b class="title">Total volume: </b> {!!$quote->total_volume != '' ? $quote->total_volume.'m<sup>3</sup>' : ''!!}</p>
                    </div>
                </div>-->
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                      <th class="unit"><b>Cargo type</b></th>
                      <th class="unit"><b>Total quantity</b></th>
                      <th class="unit"><b>Total weight</b></th>
                      <th class="unit"><b>Total volume</b></th>
                      <th class="unit"><b>Chargeable weight</b></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="text-center">
                      <td>{{$quote->cargo_type == 1 ? 'Pallets' : 'Packages'}}</td>
                      <td>{{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</td>
                      <td>{{$quote->total_weight != '' ? $quote->total_weight.' Kg' : ''}}</td>
                      <td>{!!$quote->total_volume != '' ? $quote->total_volume.' m<sup>3</sup>' : ''!!}</td>
                      <td>{{$quote->chargeable_weight}} kg</td>
                    </tr>
                  </tbody>
                </table>
            @endif
            @if(!empty($package_loads) && count($package_loads)>0)
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Cargo type</b></th>
                        <th class="unit"><b>Quantity</b></th>
                        <th class="unit"><b>Height</b></th>
                        <th class="unit"><b>Width</b></th>
                        <th class="unit"><b>Large</b></th>
                        <th class="unit"><b>Weight</b></th>
                        <th class="unit"><b>Total weight</b></th>
                        <th class="unit"><b>Volume</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($package_loads as $package_load)
                        <tr class="text-center">
                            <td>{{$package_load->type_cargo==1 ? 'Pallets':'Packages'}}</td>
                            <td>{{$package_load->quantity}}</td>
                            <td>{{$package_load->height}} cm</td>
                            <td>{{$package_load->width}} cm</td>
                            <td>{{$package_load->large}} cm</td>
                            <td>{{$package_load->weight}} kg</td>
                            <td>{{$package_load->total_weight}} kg</td>
                            <td>{{$package_load->volume}} m<sup>3</sup></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <br>
                <div class="row">
                    <div class="col-md-12 pull-right">
                        <b class="title">Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                    </div>
                </div>
                @if($quote->chargeable_weight!='' && $quote->chargeable_weight>0)
                  <div class="row">
                      <div class="col-md-12 ">
                          <b class="title">Chargeable weight:</b> {{$quote->chargeable_weight}} kg
                      </div>
                  </div>
                @endif
            @endif
        </div>
        <br>
        @if($quote->kind_of_cargo!='')
            <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><span class="title" >Kind of cargo:</span> {{$quote->kind_of_cargo}}</p>
            <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}}</p>
            <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}}</p>
        @endif
        @if($quote->commodity!='')
            <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}><span class="title" >Commodity:</span> {{$quote->commodity}}</p>
            <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><span class="title" >Mercancía:</span> {{$quote->commodity}}</p>
            <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><span class="title" >Mercadoria:</span> {{$quote->commodity}}</p>
        @endif
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
        <!-- All in table -->
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
                    <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                    <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                    <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                    <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                    <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
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
                            }else if(isset($array_amounts['c20']) && !isset($array_markups['c20'])){
                                $amount20=$array_amounts['c20'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total20=$amount20/$value->currency_usd;
                                }else{
                                    $total20=$amount20/$value->currency_eur;
                                }
                                $sum20 += $total20;
                            }else if(!isset($array_amounts['c20']) && isset($array_markups['c20'])){
                                $markup20=$array_markups['c20'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total20=$markup20/$value->currency_usd;
                                }else{
                                    $total20=$markup20/$value->currency_eur;
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
                            }else if(isset($array_amounts['c40']) && !isset($array_markups['c40'])){
                                $amount40=$array_amounts['c40'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40=$amount40/$value->currency_usd;
                                }else{
                                    $total40=$amount40/$value->currency_eur;
                                }
                                $sum40 += $total40;
                            }else if(!isset($array_amounts['c40']) && isset($array_markups['c40'])){
                                $markup40=$array_markups['c40'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40=$markup40/$value->currency_usd;
                                }else{
                                    $total40=$markup40/$value->currency_eur;
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
                            }else if(isset($array_amounts['c40hc']) && !isset($array_markups['c40hc'])){
                                $amount40hc=$array_amounts['c40hc'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40hc=$amount40hc/$value->currency_usd;
                                }else{
                                    $total40hc=$amount40hc/$value->currency_eur;
                                }
                                $sum40hc += $total40hc;
                            }else if(!isset($array_amounts['c40hc']) && isset($array_markups['c40hc'])){
                                $markup40hc=$array_markups['c40hc'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40hc=$markup40hc/$value->currency_usd;
                                }else{
                                    $total40hc=$markup40hc/$value->currency_eur;
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
                            }else if(isset($array_amounts['c40nor']) && !isset($array_markups['c40nor'])){
                                $amount40nor=$array_amounts['c40nor'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40nor=$amount40nor/$value->currency_usd;
                                }else{
                                    $total40nor=$amount40nor/$value->currency_eur;
                                }
                                $sum40nor += $total40nor;
                            }else if(!isset($array_amounts['c40nor']) && isset($array_markups['c40nor'])){
                                $markup40nor=$array_markups['c40nor'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total40nor=$markup40nor/$value->currency_usd;
                                }else{
                                    $total40nor=$markup40nor/$value->currency_eur;
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
                            }else if(isset($array_amounts['c45']) && !isset($array_markups['c45'])){
                                $amount45=$array_amounts['c45'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total45=$amount45/$value->currency_usd;
                                }else{
                                    $total45=$amount45/$value->currency_eur;
                                }
                                $sum45 += $total45;
                            }else if(!isset($array_amounts['c45']) && isset($array_markups['c45'])){
                                $markup45=$array_markups['c45'];
                                if($quote->pdf_option->total_in_currency=='USD'){
                                    $total45=$markup45/$value->currency_usd;
                                }else{
                                    $total45=$markup45/$value->currency_eur;
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
                                }else if(isset($arr_amounts['c20']) && !isset($arr_markups['c20'])){
                                    $amount_inland20=$arr_amounts['c20'];
                                    $total_inland20=$amount_inland20;
                                    if($total_inland20>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland20=$total_inland20/$value->currency_usd;
                                        }else{
                                            $total_inland20=$total_inland20/$value->currency_eur;
                                        }
                                    }
                                    $inland20 += $total_inland20;
                                }else if(!isset($arr_amounts['c20']) && isset($arr_markups['c20'])){
                                    $markup_inland20=$arr_markups['c20'];
                                    $total_inland20=$markup_inland20;
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
                                }else if(isset($arr_amounts['c40']) && !isset($arr_markups['c40'])){
                                    $amount_inland40=$arr_amounts['c40'];
                                    $total_inland40=$amount_inland40;
                                    if($total_inland40>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland40=$total_inland40/$value->currency_usd;
                                        }else{
                                            $total_inland40=$total_inland40/$value->currency_eur;
                                        }
                                    }
                                    $inland40 += $total_inland40;
                                }else if(!isset($arr_amounts['c40']) && isset($arr_markups['c40'])){
                                    $markup_inland40=$arr_markups['c40'];
                                    $total_inland40=$markup_inland40;
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
                                }else if(isset($arr_amounts['c40hc']) && !isset($arr_markups['c40hc'])){
                                    $amount_inland40hc=$arr_amounts['c40hc'];
                                    $total_inland40hc=$amount_inland40hc;
                                    if($total_inland40hc>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland40hc=$total_inland40hc/$value->currency_usd;
                                        }else{
                                            $total_inland40hc=$total_inland40hc/$value->currency_eur;
                                        }
                                    }
                                    $inland40hc += $total_inland40hc;
                                }else if(!isset($arr_amounts['c40hc']) && isset($arr_markups['c40hc'])){
                                    $markup_inland40hc=$arr_markups['c40hc'];
                                    $total_inland40hc=$markup_inland40hc;
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
                                    $total_inland40nor=$amount_inland40nor+$markup_inland40nor;
                                    if($total_inland40nor>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland40nor=$total_inland40nor/$value->currency_usd;
                                        }else{
                                            $total_inland40nor=$total_inland40nor/$value->currency_eur;
                                        }
                                    }
                                    $inland40nor += $total_inland40nor;
                                }else if(isset($arr_amounts['c40nor']) && !isset($arr_markups['c40nor'])){
                                    $amount_inland40nor=$arr_amounts['c40nor'];
                                    $total_inland40nor=$amount_inland40nor;
                                    if($total_inland40nor>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland40nor=$total_inland40nor/$value->currency_usd;
                                        }else{
                                            $total_inland40nor=$total_inland40nor/$value->currency_eur;
                                        }
                                    }
                                    $inland40nor += $total_inland40nor;
                                }else if(!isset($arr_amounts['c40nor']) && isset($arr_markups['c40nor'])){
                                    $markup_inland40nor=$arr_markups['c40nor'];
                                    $total_inland40nor=$markup_inland40nor;
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
                                }else if(isset($arr_amounts['c45']) && !isset($arr_markups['c45'])){
                                    $amount_inland45=$arr_amounts['c45'];
                                    $total_inland45=$amount_inland45;
                                    if($total_inland45>0){
                                        if($quote->pdf_option->total_in_currency=='USD'){
                                            $total_inland45=$total_inland45/$value->currency_usd;
                                        }else{
                                            $total_inland45=$total_inland45/$value->currency_eur;
                                        }
                                    }
                                    $inland45 += $total_inland45;
                                }else if(!isset($arr_amounts['c45']) && isset($arr_markups['c45'])){
                                    $markup_inland45=$arr_markups['c45'];
                                    $total_inland45=$markup_inland45;
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
                        <td {{ @$equipmentHides['20'] }}>{{number_format((float)@$sum20+@$inland20, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['40'] }}>{{number_format((float)@$sum40+@$inland40, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['40hc'] }}>{{number_format((float)@$sum40hc+@$inland40hc, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['40nor'] }}>{{number_format((float)@$sum40nor+@$inland40nor, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['45'] }}>{{number_format((float)@$sum45+@$inland45, 2, '.', '')}}</td>
                        <td >{{$quote->pdf_option->grouped_total_currency==0 ?$currency_cfg->alphacode:$quote->pdf_option->total_in_currency}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
        </table>
        <br>

        <!-- DETAILED TABLES -->

        <!-- Freights table all in-->
        @if($quote->pdf_option->grouped_freight_charges==1 && $quote->pdf_option->show_type=='detailed' )
        <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
            <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
            <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete</p>
            <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
            <br>
        </div>

        <table border="0" cellspacing="1" cellpadding="1" >
            <thead class="title-quote text-center header-table">
                <tr >
                    <th class="unit"><b>POL</b></th>
                    <th class="unit"><b>POD</b></th>
                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                    <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                    <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                    <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                    <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                    <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                </tr>
            </thead>
            <tbody>
                @php
                    //dd($freight_charges_grouped);
                @endphp
                @foreach($freight_charges_grouped as $origin=>$freight)
                    @foreach($freight as $destination=>$detail)
                        @foreach($detail as $item)
                            @foreach($item as $rate)
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
                                @foreach($rate->charge as $value)
                                    <?php
                                        $sum_freight_20+=$value->total_20;
                                        $sum_freight_40+=$value->total_40;
                                        $sum_freight_40hc+=$value->total_40hc;
                                        $sum_freight_40nor+=$value->total_40nor;
                                        $sum_freight_45+=$value->total_45;                                
                                    ?>
                                @endforeach
                            @endforeach
                            <tr class="text-center color-table">
                                <td >
                                    @if($rate->origin_port_id!='') 
                                        {{$rate->origin_port->name}}, {{$rate->origin_port->code}} 
                                    @elseif($rate->origin_address!='') 
                                        {{$rate->origin_address}} 
                                    @else 
                                        {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}}
                                    @endif
                                </td>
                                <td >
                                    @if($rate->destination_port_id!='') 
                                        {{$rate->destination_port->name}}, {{$rate->destination_port->code}} 
                                    @elseif($rate->destination_address!='') 
                                        {{$rate->destination_address}} 
                                    @else 
                                        {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}
                                    @endif
                                </td>                            
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier->name}}</td>
                                <td {{ @$equipmentHides['20'] }}>{{@$sum_freight_20}}</td>
                                <td {{ @$equipmentHides['40'] }}>{{@$sum_freight_40}}</td>
                                <td {{ @$equipmentHides['40hc'] }}>{{@$sum_freight_40hc}}</td>
                                <td {{ @$equipmentHides['40nor'] }}>{{@$sum_freight_40nor}}</td>
                                <td {{ @$equipmentHides['45'] }}>{{@$sum_freight_45}}</td>
                                @if($quote->pdf_option->grouped_freight_charges==1)
                                    <td >{{$quote->pdf_option->freight_charges_currency}}</td>
                                @else
                                    <td >{{$currency_cfg->alphacode}}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- ORIGINS -->

        <!-- ALL in origin table -->
        @if($quote->pdf_option->grouped_origin_charges==1 && $quote->pdf_option->show_type=='detailed' )
            @foreach($origin_charges_grouped as $origin=>$detail)
            <br>
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de origen - {{$origin}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit" colspan="2"><b>Charge</b></th>
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                            <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                            <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                            <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                            <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                            <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($detail as $item)

                        @foreach($item as $rate)
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
                            @foreach($rate->charge as $value)
                                <?php
                                    $sum_origin_20+=$value->total_20;
                                    $sum_origin_40+=$value->total_40;
                                    $sum_origin_40hc+=$value->total_40hc;
                                    $sum_origin_40nor+=$value->total_40nor;
                                    $sum_origin_45+=$value->total_45;                                
                                ?>
                            @endforeach
                            @foreach($rate->inland as $value)
                                <?php
                                    $inland_origin_20+=$value->total_20;
                                    $inland_origin_40+=$value->total_40;
                                    $inland_origin_40hc+=$value->total_40hc;
                                    $inland_origin_40nor+=$value->total_40nor;
                                    $inland_origin_45+=$value->total_45;                                
                                ?>
                            @endforeach
                        @endforeach
                        <tr class="text-center color-table">
                            <td colspan="2">Total Origin Charges</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier->name}}</td>
                            <td {{ @$equipmentHides['20'] }}>{{@$sum_origin_20+@$inland_origin_20}}</td>
                            <td {{ @$equipmentHides['40'] }}>{{@$sum_origin_40+@$inland_origin_40}}</td>
                            <td {{ @$equipmentHides['40hc'] }}>{{@$sum_origin_40hc+@$inland_origin_40hc}}</td>
                            <td {{ @$equipmentHides['40nor'] }}>{{@$sum_origin_40nor+@$inland_origin_40nor}}</td>
                            <td {{ @$equipmentHides['45'] }}>{{@$sum_origin_45+@$inland_origin_45}}</td>
                            @if($quote->pdf_option->grouped_origin_charges==1)
                                <td >{{$quote->pdf_option->origin_charges_currency}}</td>
                            @else
                                <td >{{$currency_cfg->alphacode}}</td>
                            @endif
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
        @endif

        <!-- Origins detailed -->
        @if($quote->pdf_option->grouped_origin_charges==0 && $quote->pdf_option->show_type=='detailed' )
            @foreach($origin_charges_detailed as $carrier => $value)
                @foreach($value as $origin => $item)
                    <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de origen - {{$origin}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1"  {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>Charge</b></th>
                                <th class="unit"><b>Detail</b></th>
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                                <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                                <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                                <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                                <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($item as $rate)
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
                            @foreach($rate as $r)
                                @foreach($r->charge as $v)
                                    @if($v->type_id==1)
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
                                            <td>{{$v->surcharge->name}}</td>
                                            <td>{{$v->calculation_type->name}}</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$r->carrier->name}}</td>
                                            <td {{ @$equipmentHides['20'] }}>{{$v->total_20}}</td>
                                            <td {{ @$equipmentHides['40'] }}>{{$v->total_40}}</td>
                                            <td {{ @$equipmentHides['40hc'] }}>{{$v->total_40hc}}</td>
                                            <td {{ @$equipmentHides['40nor'] }}>{{$v->total_40nor}}</td>
                                            <td {{ @$equipmentHides['45'] }}>{{$v->total_45}}</td>
                                            @if($quote->pdf_option->grouped_origin_charges==1)
                                                <td>{{$quote->pdf_option->origin_charges_currency}}</td>
                                            @else
                                                <td>{{$currency_cfg->alphacode}}</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$r->inland->isEmpty()){
                                    @foreach($r->inland as $v)
                                        @if($v->type=='Origin')
                                            <?php
                                                $inland_20+=$v->total_20;
                                                $inland_40+=$v->total_40;
                                                $inland_40hc+=$v->total_40hc;
                                                $inland_40nor+=$v->total_40nor;
                                                $inland_45+=$v->total_45;
                                            ?>
                                            <tr class="text-center color-table">
                                                <td>{{$v->provider}}</td>
                                                <td>{{$v->distance}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$r->carrier->name}}</td>
                                                <td {{ @$equipmentHides['20'] }}>{{$v->total_20}}</td>
                                                <td {{ @$equipmentHides['40'] }}>{{$v->total_40}}</td>
                                                <td {{ @$equipmentHides['40hc'] }}>{{$v->total_40hc}}</td>
                                                <td {{ @$equipmentHides['40nor'] }}>{{$v->total_40nor}}</td>
                                                <td {{ @$equipmentHides['45'] }}>{{$v->total_45}}</td>
                                                @if($quote->pdf_option->grouped_origin_charges==1)
                                                    <td>{{$quote->pdf_option->origin_charges_currency}}</td>
                                                @else
                                                    <td>{{$currency_cfg->alphacode}}</td>
                                                @endif
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                        <tr>
                            <td><b>Total local charges</b></td>
                            <td></td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                            <td {{ @$equipmentHides['20'] }}><b>{{number_format(@$sum_origin_20+@$inland_20, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['40'] }}><b>{{number_format(@$sum_origin_40+@$inland_40, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['40hc'] }}><b>{{number_format(@$sum_origin_40hc+@$inland_40hc, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['40nor'] }}><b>{{number_format(@$sum_origin_40nor+@$inland_40nor, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['45'] }}><b>{{number_format(@$sum_origin_45+@$inland_45, 2, '.', '')}}</b></td>
                            @if($quote->pdf_option->grouped_origin_charges==1)
                            <td><b>{{$quote->pdf_option->origin_charges_currency}}</b></td>
                            @else
                            <td><b>{{$currency_cfg->alphacode}}</b></td>
                            @endif     
                        </tr>
                    </tbody>
                </table>
                @endforeach
            @endforeach
        @endif

        <!-- DESTINATIONS -->

        <!-- ALL in destination table -->
        @if($quote->pdf_option->grouped_destination_charges==1 && $quote->pdf_option->show_type=='detailed' )
            @foreach($destination_charges_grouped as $origin=>$detail)
            <br>
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$origin}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de destino - {{$origin}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$origin}}</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit" colspan="2"><b>Charge</b></th>
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                            <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                            <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                            <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                            <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                            <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                        </tr>
                    </thead>
                    <tbody>

                    @foreach($detail as $item)
                        <?php
                            $sum_destination_20= 0;
                            $sum_destination_40= 0;
                            $sum_destionation_40hc= 0;
                            $sum_destination_40nor= 0;
                            $sum_destination_45= 0;
                        ?>  
                        @foreach($item as $rate)
                            @foreach($rate->charge as $value)
                                <?php
                                    $sum_destination_20+=$value->total_20;
                                    $sum_destination_40+=$value->total_40;
                                    $sum_destionation_40hc+=$value->total_40hc;
                                    $sum_destination_40nor+=$value->total_40nor;
                                    $sum_destination_45+=$value->total_45;                                
                                ?>
                            @endforeach
                        @endforeach
                        <tr class="text-center color-table">
                            <td colspan="2">Total Destination Charges</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier->name}}</td>
                            <td {{ @$equipmentHides['20'] }}>{{@$sum_destination_20}}</td>
                            <td {{ @$equipmentHides['40'] }}>{{@$sum_destination_40}}</td>
                            <td {{ @$equipmentHides['40hc'] }}>{{@$sum_destionation_40hc}}</td>
                            <td {{ @$equipmentHides['40nor'] }}>{{@$sum_destination_40nor}}</td>
                            <td {{ @$equipmentHides['45'] }}>{{@$sum_destination_45}}</td>
                            @if($quote->pdf_option->grouped_destination_charges==1)
                            <td >{{$quote->pdf_option->destination_charges_currency}}</td>
                            @else
                            <td >{{$currency_cfg->alphacode}}</td>
                            @endif
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endforeach
        @endif

        <!-- Destinations detailed -->
        @if($quote->pdf_option->grouped_destination_charges==0 && $quote->pdf_option->show_type=='detailed' )
        @foreach($destination_charges as $carrier => $value)
                @foreach($value as $destination => $item)
                    <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de destino - {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$destination}}</p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1"  {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>Charge</b></th>
                                <th class="unit"><b>Detail</b></th>
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                                <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                                <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                                <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                                <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($item as $rate)
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
                            @foreach($rate as $r)
                                @foreach($r->charge as $v)
                                    @if($v->type_id==2)
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
                                            <td>{{$v->surcharge->name}}</td>
                                            <td>{{$v->calculation_type->name}}</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$r->carrier->name}}</td>
                                            <td {{ @$equipmentHides['20'] }}>{{$v->total_20}}</td>
                                            <td {{ @$equipmentHides['40'] }}>{{$v->total_40}}</td>
                                            <td {{ @$equipmentHides['40hc'] }}>{{$v->total_40hc}}</td>
                                            <td {{ @$equipmentHides['40nor'] }}>{{$v->total_40nor}}</td>
                                            <td {{ @$equipmentHides['45'] }}>{{$v->total_45}}</td>
                                            @if($quote->pdf_option->grouped_destination_charges==1)
                                                <td>{{$quote->pdf_option->destination_charges_currency}}</td>
                                            @else
                                                <td>{{$currency_cfg->alphacode}}</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$r->inland->isEmpty()){
                                    @foreach($r->inland as $v)
                                        @if($v->type=='Destination')
                                            <?php
                                                $inland_20+=$v->total_20;
                                                $inland_40+=$v->total_40;
                                                $inland_40hc+=$v->total_40hc;
                                                $inland_40nor+=$v->total_40nor;
                                                $inland_45+=$v->total_45;
                                            ?>
                                            <tr class="text-center color-table">
                                                <td>{{$v->provider}}</td>
                                                <td>{{$v->distance}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$r->carrier->name}}</td>
                                                <td {{ @$equipmentHides['20'] }}>{{$v->total_20}}</td>
                                                <td {{ @$equipmentHides['40'] }}>{{$v->total_40}}</td>
                                                <td {{ @$equipmentHides['40hc'] }}>{{$v->total_40hc}}</td>
                                                <td {{ @$equipmentHides['40nor'] }}>{{$v->total_40nor}}</td>
                                                <td {{ @$equipmentHides['45'] }}>{{$v->total_45}}</td>
                                                @if($quote->pdf_option->grouped_origin_charges==1)
                                                    <td>{{$quote->pdf_option->origin_charges_currency}}</td>
                                                @else
                                                    <td>{{$currency_cfg->alphacode}}</td>
                                                @endif
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                            <tr>
                                <td><b>Total local charges</b></td>
                                <td></td>
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                <td {{ @$equipmentHides['20'] }}><b>{{number_format(@$sum_destination_20+@$inland_20, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['40'] }}><b>{{number_format(@$sum_destination_40+@$inland_40, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['40hc'] }}><b>{{number_format(@$sum_destination_40hc+@$inland_40hc, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['40nor'] }}><b>{{number_format(@$sum_destination_40nor+@$inland_40nor, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['45'] }}><b>{{number_format(@$sum_destination_45+@$inland_45, 2, '.', '')}}</b></td>
                                @if($quote->pdf_option->grouped_destination_charges==1)
                                    <td><b>{{$quote->pdf_option->destination_charges_currency}}</b></td>
                                @else
                                    <td><b>{{$currency_cfg->alphacode}}</b></td>
                                @endif     
                            </tr>
                        </tbody>
                    </table>
                @endforeach
            @endforeach
        @endif
        <br>
        @if($quote->payment_conditions!='')
            <br>
            <div class="clearfix">
                <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote header-table">
                        <tr>
                            <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Payments conditions</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Condiciones de pago</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Condições de pagamento</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:20px;">
                                <span class="text-justify">{!! $quote->payment_conditions!!}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
        @if($quote->terms_and_conditions!='')
            <br>
            <div class="clearfix">
                <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote header-table">
                        <tr>
                            <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Terms and conditions</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Términos y condiciones</b></th>
                            <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Termos e Condições</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:20px;">
                                <span class="text-justify">{!! $quote->terms_and_conditions!!}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif 
</main>
</body>
</html>