<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Quote #{{$quote->quote_id}}</title>
        <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
        <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
        <style>

        </style>
    </head>
    <body style="background-color: white; font-size: 11px;">
        <header class="clearfix" style="margin-top:-25px; margin-bottom:-10px">
            <div id="logo">
                @if($user->companyUser->logo!='')
                <img src="{{Storage::disk('s3_upload')->url($user->companyUser->logo)}}" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:0">
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
                    <span class="color-title"><b>@if($quote->pdf_option->language=='English')Validity:@elseif($quote->pdf_option->language=='Spanish') Validez: @else Validade: @endif </b></span>{{\Carbon\Carbon::parse( $quote->validity_start)->format('d M Y') }} -  {{\Carbon\Carbon::parse( $quote->validity_end)->format('d M Y') }}
                </div>
                @endif
            </div>
            <hr>
        </header>

        <main>
            <div id="details" class="clearfix details">
                <div class="client" style="line-height: 10px;">
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>From:</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>De:</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>A partir de:</p>
                    <span id="destination_input" style="line-height: 0.5">
                        <p style="line-height:10px;">{{@$quote->user->name}} {{@$quote->user->lastname}}</p>
                        <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                        <p style="line-height:10px;">{{@$user->companyUser->address}}</p>
                        <p style="line-height:10px;">{{@$user->phone}}</p>
                        <p style="line-height:10px;">{{@$quote->user->email}}</p>
                    </span>
                </div>
                <div class="company text-right" style="float: right; width: 350px; line-height: 10px;">
                    @if($quote->company_id!='')
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>To:</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Para:</b></p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Para:</b></p>
                    @endif
                    <span id="destination_input" style="line-height: 0.5">
                        @if($quote->pdf_option->show_logo==1)
                        @if($quote->company->logo!='')
                        <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px">
                        @endif
                        @endif
                        <p style="line-height:10px;">{{@$quote->contact->first_name.' '.@$quote->contact->last_name}}</p>
                        <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                        <p style="line-height:10px;">{{@$quote->company->address}}</p>
                        <p style="line-height:10px;">{{@$quote->contact->phone}}</p>
                        <p style="line-height:10px;">{{@$quote->contact->email}}</p>
                    </span>
                </div>
            </div>
            @if($quote->kind_of_cargo!='' || $quote->commodity!='' || $quote->risk_level!='')
            <div style="margin-top: 25px;">
                <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Kind of cargo:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Commodity:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Risk level:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercancía:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nivel de riesgo:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercadoria:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nível de risco:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
            </div>
            @endif 
            <br>
            @if($quote->pdf_option->show_type=='total in')
            <div {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total estimated costs</b></p>
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
                    <thead class="title-quote text-left header-table">
                        <tr >
                            <th class="unit"><b>POL</b></th>
                            <th class="unit"><b>POD</b></th>
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                            <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                            <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                            <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                            <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                            <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                            @if($quote->pdf_option->show_schedules==1)
                            <th ><b>Type</b></th>
                            <th ><b>TT</b></th>
                            <th ><b>Via</b></th>
                            @endif
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rates as $rate)
                        <?php 

                        $total_20 = 0;
                        $total_40 = 0;
                        $total_40hc = 0;
                        $total_40nor = 0;
                        $total_45 = 0;

                        $sum_total20= 0;
                        $sum_total40= 0;
                        $sum_total40hc= 0;
                        $sum_total40nor= 0;
                        $sum_total45= 0;

                        $sum_inland20= 0;
                        $sum_inland40= 0;
                        $sum_inland40hc= 0;
                        $sum_inland40nor= 0;
                        $sum_inland45= 0;
                        ?>
                        @foreach($rate->charge as $value)
                        <?php

                        $total_20=$value->total_20+$value->total_markup20;
                        $sum_total20+=$total_20;
                        $total_40=$value->total_40+$value->total_markup40;
                        $sum_total40+=$total_40;
                        $total_40hc=$value->total_40hc+$value->total_markup40hc;
                        $sum_total40hc+=$total_40hc;
                        $total_40nor=$value->total_40nor+$value->total_markup40nor;
                        $sum_total40nor+=$total_40nor;
                        $total_45=$value->total_45+$value->total_markup45;
                        $sum_total45+=$total_45;
                        ?>

                        @endforeach
                        @if(!$rate->inland->isEmpty())
                        @foreach($rate->inland as $item)
                        <?php
                        $sum_inland20+=$item->total_20;
                        $sum_inland40+=$item->total_40;
                        $sum_inland40hc+=$item->total_40hc;
                        $sum_inland40nor+=$item->total_40nor;
                        $sum_inland45+=$item->total_45;
                        ?>
                        @endforeach
                        @endif
                        <tr class="text-left color-table">
                            <td >
                                @if($rate->origin_address=='' && $rate->origin_port_id!='') 
                                {{$rate->origin_port->name}}, {{$rate->origin_port->code}} 
                                @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') 
                                {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}}
                                @else 
                                {{$rate->origin_address}} 
                                @endif
                            </td>
                            <td >
                                @if($rate->destination_address=='' && $rate->destination_port_id!='') 
                                {{$rate->destination_port->name}}, {{$rate->destination_port->code}} 
                                @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') 
                                {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}
                                @else 
                                {{$rate->destination_address}} 
                                @endif
                            </td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                            <td {{ @$equipmentHides['20'] }}>{{number_format((float)@$sum_total20+@$sum_inland20, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40'] }}>{{number_format((float)@$sum_total40+@$sum_inland40, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40hc'] }}>{{number_format((float)@$sum_total40hc+@$sum_inland40hc, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40nor'] }}>{{number_format((float)@$sum_total40nor+@$sum_inland40nor, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['45'] }}>{{number_format((float)@$sum_total45+@$sum_inland45, 2, '.', '')}}</td>
                            @if($quote->pdf_option->show_schedules==1)
                            <td>{{$rate->schedule_type!='' ? $rate->schedule_type:'-'}}</td>
                            <td>{{$rate->transit_time!='' ? $rate->transit_time:'-'}}</td>
                            <td>{{$rate->via!='' ? $rate->via:'-'}}</td>
                            @endif
                            <td >{{$quote->pdf_option->grouped_total_currency==0 ?$currency_cfg->alphacode:$quote->pdf_option->total_in_currency}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                </table>
                <br>

                <!-- DETAILED TABLES -->

                <!-- Freights table all in-->
                @if($quote->pdf_option->show_type=='detailed' && $rates->count()>1)
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
                    <br>
                </div>

                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-left header-table">
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
                        @foreach($freight_charges_grouped as $origin=>$freight)
                        @foreach($freight as $destination=>$detail)
                        @foreach($detail as $item)
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
                        @foreach($item as $rate)
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
                        <tr class="text-left color-table">
                            <td >
                                @if($rate->origin_address=='' && $rate->origin_port_id!='') 
                                {{$rate->origin_port->name}}, {{$rate->origin_port->code}} 
                                @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') 
                                {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}}
                                @else 
                                {{$rate->origin_address}} 
                                @endif
                            </td>
                            <td >
                                @if($rate->destination_address=='' && $rate->destination_port_id!='') 
                                {{$rate->destination_port->name}}, {{$rate->destination_port->code}} 
                                @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') 
                                {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}
                                @else 
                                {{$rate->destination_address}} 
                                @endif
                            </td>                           
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                            <td {{ @$equipmentHides['20'] }}>{{number_format(@$sum_freight_20, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40'] }}>{{number_format(@$sum_freight_40, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40hc'] }}>{{number_format(@$sum_freight_40hc, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40nor'] }}>{{number_format(@$sum_freight_40nor, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['45'] }}>{{number_format(@$sum_freight_45, 2, '.', '')}}</td>
                            <td >{{$currency_cfg->alphacode}}</td>
                        </tr>
                        @endforeach
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
                @endif

                @if($quote->pdf_option->show_type=='detailed' && $rates->count()==1)
                @if($quote->pdf_option->grouped_freight_charges==0)
                @foreach($freight_charges_grouped as $origin => $value)
                @foreach($value as $destination => $item)
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges - {{$origin}} | {{$destination}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete - {{$origin}} | {{$destination}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete - {{$origin}} | {{$destination}}</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1"  {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <thead class="title-quote text-left header-table">
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
                        $sum_freight_20= 0;
                        $sum_freight_40= 0;
                        $sum_freight_40hc= 0;
                        $sum_freight_40nor= 0;
                        $sum_freight_45= 0;
                        ?>
                        @foreach($rate as $r)
                        @foreach($r->charge as $v)

                        @if($v->type_id==3)
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
                        <tr class="text-left color-table">
                            @if($v->surcharge_id!='')
                            <td>{{$v->surcharge->name}}</td>
                            @else
                            <td>Ocean freight</td>
                            @endif
                            @if($v->surcharge_id!='')
                            <td>{{$v->calculation_type->name}}</td>
                            @else
                            <td>Per container</td>
                            @endif
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                            <td {{ @$equipmentHides['20'] }}>{{number_format($v->total_20, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40'] }}>{{number_format($v->total_40, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40hc'] }}>{{number_format($v->total_40hc, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40nor'] }}>{{number_format($v->total_40nor, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['45'] }}>{{number_format($v->total_45, 2, '.', '')}}</td>
                            <td>{{$v->currency->alphacode}}</td>
                        </tr>
                        @endif
                        @endforeach                                
                        @endforeach
                        @endforeach
                        <tr>
                            <td><b>Total local charges</b></td>
                            <td></td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                            <td {{ @$equipmentHides['20'] }}><b>{{number_format(@$sum_freight_20, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['40'] }}><b>{{number_format(@$sum_freight_40, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['40hc'] }}><b>{{number_format(@$sum_freight_40hc, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['40nor'] }}><b>{{number_format(@$sum_freight_40nor, 2, '.', '')}}</b></td>
                            <td {{ @$equipmentHides['45'] }}><b>{{number_format(@$sum_freight_45, 2, '.', '')}}</b></td>
                            <td><b>{{$currency_cfg->alphacode}}</b></td>
                        </tr>
                    </tbody>
                </table>
                @endforeach
                @endforeach
                @else
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-left header-table">
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
                        @foreach($freight_charges_grouped as $origin=>$freight)
                        @foreach($freight as $destination=>$detail)
                        @foreach($detail as $item)
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
                        @foreach($item as $rate)
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
                        <tr class="text-left color-table">
                            <td >
                                @if($rate->origin_address=='' && $rate->origin_port_id!='') 
                                {{$rate->origin_port->name}}, {{$rate->origin_port->code}} 
                                @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') 
                                {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}}
                                @else 
                                {{$rate->origin_address}} 
                                @endif
                            </td>
                            <td >
                                @if($rate->destination_address=='' && $rate->destination_port_id!='') 
                                {{$rate->destination_port->name}}, {{$rate->destination_port->code}} 
                                @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') 
                                {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}
                                @else 
                                {{$rate->destination_address}} 
                                @endif
                            </td>                           
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                            <td {{ @$equipmentHides['20'] }}>{{number_format(@$sum_freight_20, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40'] }}>{{number_format(@$sum_freight_40, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40hc'] }}>{{number_format(@$sum_freight_40hc, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['40nor'] }}>{{number_format(@$sum_freight_40nor, 2, '.', '')}}</td>
                            <td {{ @$equipmentHides['45'] }}>{{number_format(@$sum_freight_45, 2, '.', '')}}</td>
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
                @endif

                <br>

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
                    <thead class="title-quote text-left header-table">
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
                        @foreach($item as $rate)

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
                        <tr class="text-left color-table">
                            <td colspan="2">Total Origin Charges</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
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
                    <thead class="title-quote text-left header-table">
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
                        <tr class="text-left color-table">
                            <td>{{$v->surcharge->name}}</td>
                            <td>{{$v->calculation_type->name}}</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
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
                        <tr class="text-left color-table">
                            <td>Inland @if($quote->pdf_option->language=='English') Origin-Destination @elseif($quote->pdf_option->language=='Spanish') Origen - Destino @else Origem - Destino @endif</td>
                            <td>{{$v->distance!='' ? $v->distance:'0'}}</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
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
                <br>
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
                    <thead class="title-quote text-left header-table">
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
                        <tr class="text-left color-table">
                            <td colspan="2">Total Destination Charges</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
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
                @foreach($destination_charges as $destination => $value)
                @foreach($value as $carrier => $item)
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$destination}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de destino - {{$destination}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$destination}}</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1"  {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <thead class="title-quote text-left header-table">
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
                        <tr class="text-left color-table">
                            <td>{{$v->surcharge->name}}</td>
                            <td>{{$v->calculation_type->name}}</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
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
                        <tr class="text-left color-table">
                            <td>Inland @if($quote->pdf_option->language=='English') Origin-Destination @elseif($quote->pdf_option->language=='Spanish') Origen - Destino @else Origem - Destino @endif</td>
                            <td>{{$v->distance}}</td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
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
                <br>
                @endforeach
                @endforeach
                @endif
                <br>
                <?php
                $i=0;
                ?>
                @foreach($rates as $rate)
                @if($rate->remarks != '' && $rate->remarks!='<br>')
                <?php
                $i++;
                ?>
                @endif
                @endforeach
                @if($i>0)    
                <br>
                <div class="clearfix">
                    <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                        <thead class="title-quote header-table">
                            <tr>
                                <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Remarks</b></th>
                                <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Observaciones</b></th>
                                <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Observações</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:15px;">
                                    @foreach($rates as $rate)
                                    @if($rate->remarks != '')
                                    <span class="text-justify">{!! $rate->remarks !!}</span>
                                    @endif
                                    @endforeach
                                </td>
                            </tr>                        
                        </tbody>
                    </table>
                </div>
                @endif
                <br>
                @if($quote->terms_and_conditions!='')
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
                                <td style="padding:15px;">
                                    <span class="text-justify">{!! @$quote->terms_and_conditions !!}</span>
                                </td>
                            </tr>                        
                        </tbody>
                    </table>
                </div>
                @endif
                <br>
                @if($quote->payment_conditions!='')
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
                </main>
                <footer>
                    @if($user->companyUser->footer_type=='Image')
                        @if($user->companyUser->footer_image!='')
                            <div class="clearfix">
                                <img src="{{Storage::disk('s3_upload')->url($user->companyUser->footer_image)}}" class="img img-fluid" style="max-height:70px;">
                            </div>
                        @endif
                    @else
                        {!!$user->companyUser->footer_text!!}
                    @endif
                </footer>
            </body>
        </html>