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
                    <p>{{@$quote->contact->first_name.' '.@$quote->contact->last_name}}</p>
                    <p><span style="color: #4e4e4e"><b>{{$quote->company->business_name}}</b></span></p>
                    <p>{{$quote->company->address}}</p>
                    <p>{{@$quote->contact->phone}}</p>
                    <p>{{@$quote->contact->email}}</p>
                </span>
            </div>
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
                          $sum_total20+=$value->total_20;
                          $sum_total40+=$value->total_40;
                          $sum_total40hc+=$value->total_40hc;
                          $sum_total40nor+=$value->total_40nor;
                          $sum_total45+=$value->total_45;
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
                    <tr class="text-center color-table">
                        <td >{{$rate->origin_port->name}}, {{$rate->origin_port->code}}</td>
                        <td >{{$rate->destination_port->name}}, {{$rate->destination_port->code}}</td>
                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier->name}}</td>
                        <td {{ @$equipmentHides['20'] }}>{{number_format((float)@$sum_total20+@$sum_inland20, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['40'] }}>{{number_format((float)@$sum_total40+@$sum_inland40, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['40hc'] }}>{{number_format((float)@$sum_total40hc+@$sum_inland40hc, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['40nor'] }}>{{number_format((float)@$sum_total40nor+@$sum_inland40nor, 2, '.', '')}}</td>
                        <td {{ @$equipmentHides['45'] }}>{{number_format((float)@$sum_total45+@$sum_inland45, 2, '.', '')}}</td>
                        @if($quote->pdf_option->show_schedules==1)
                            <td>{{$rate->schedule_type}}</td>
                            <td>{{$rate->transit_time}}</td>
                            <td>{{$rate->via}}</td>
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
        @if($quote->pdf_option->grouped_freight_charges==1 && $quote->pdf_option->show_type=='detailed' && $freight_charges_grouped->count()>1)
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

        @if($quote->pdf_option->grouped_freight_charges==1 && $quote->pdf_option->show_type=='detailed' && $freight_charges_grouped->count()==1)
            @foreach($freight_charges_detailed as $origin => $value)
                @foreach($value as $destination => $item)
                    <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges - {{$origin}} | {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete - {{$origin}} | {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete - {{$origin}} | {{$destination}}</p>
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
                                            <tr class="text-center color-table">
                                                <td>{{$v->surcharge->name}}</td>
                                                <td>{{$v->calculation_type->name}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$r->carrier->name}}</td>
                                                <td {{ @$equipmentHides['20'] }}>{{number_format($v->total_20, 2, '.', '')}}</td>
                                                <td {{ @$equipmentHides['40'] }}>{{number_format($v->total_40, 2, '.', '')}}</td>
                                                <td {{ @$equipmentHides['40hc'] }}>{{number_format($v->total_40hc, 2, '.', '')}}</td>
                                                <td {{ @$equipmentHides['40nor'] }}>{{number_format($v->total_40nor, 2, '.', '')}}</td>
                                                <td {{ @$equipmentHides['45'] }}>{{number_format($v->total_45, 2, '.', '')}}</td>
                                                @if($quote->pdf_option->grouped_freight_charges==1)
                                                    <td>{{$quote->pdf_option->freight_charges_currency}}</td>
                                                @else
                                                    <td>{{$currency_cfg->alphacode}}</td>
                                                @endif
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
                                @if($quote->pdf_option->grouped_freight_charges==1)
                                    <td><b>{{$quote->pdf_option->freight_charges_currency}}</b></td>
                                @else
                                    <td><b>{{$currency_cfg->alphacode}}</b></td>
                                @endif     
                            </tr>
                        </tbody>
                    </table>
                @endforeach
            @endforeach
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