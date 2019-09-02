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
            @if($quote->validity_start!='' && $quote->validity_end!='')
            <div>
                <span class="color-title"><b>@if($quote->pdf_option->language=='English')Validity:@elseif($quote->pdf_option->language=='Spanish') Validez: @else Validade:@endif </b></span>{{\Carbon\Carbon::parse( $quote->validity_start)->format('d M Y') }} - {{\Carbon\Carbon::parse( $quote->validity_end)->format('d M Y') }}
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
                    <p style="line-height:10px;">{{$quote->user->name}} {{$quote->user->lastname}}</p>
                    <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                    <p style="line-height:10px;">{{$user->companyUser->address}}</p>
                    <p style="line-height:10px;">{{$user->phone}}</p>
                    <p style="line-height:10px;">{{$quote->user->email}}</p>
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
                    @if(strlen(@$quote->company->business_name)>49)
                        <p style="line-height:12px; text-align:justify;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                    @else
                        <p style="line-height:10px;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                    @endif
                    @if(strlen(@$quote->company->address)>49)
                        <p style="line-height:12px; text-align:justify;">{{@$quote->company->address}}</p>
                    @else
                        <p style="line-height:10px;">{{@$quote->company->address}}</p>
                    @endif
                    <p style="line-height:10px;">{{@$quote->contact->phone}}</p>
                    <p style="line-height:10px;">{{@$quote->contact->email}}</p>
                </span>
            </div>
        </div>
        @if($quote->incoterm!='' || $quote->kind_of_cargo!='' || $quote->commodity!='' || $quote->risk_level!='')
            <div style="margin-top: 25px;">
                @if($quote->incoterm_id!='')<p><span class="title" >Incoterm: </span>{{$quote->incoterm->name}}</p>@endif
                <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Kind of cargo:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Commodity:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Risk level:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercancía:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nivel de riesgo:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercadoria:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nível de risco:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
            </div>
        @endif      
        <br>
        <div class="company" style="color: #1D3A6E;">
            <p class="title"><b>Cargo details</b></p>
            <br>
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
            @else
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Cargo type</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total quantity</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Cantidad total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Quantidade total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total weight</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Peso total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Peso total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total volume</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Volumen total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Volume total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Chargeable weight</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Peso tasable</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Peso carregável</b></th>
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
        <!-- All in table -->
        @if($quote->pdf_option->show_type=='total in')
            <table border="0" cellspacing="1" cellpadding="1" {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
        @else
            <table border="0" cellspacing="1" cellpadding="1" {{$quote->pdf_option->grouped_total_currency==1 ? '':'hidden'}}>
        @endif
            <thead class="title-quote text-center header-table">
                <tr >
                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POL</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto de embarque</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto de descarga</b></th>
                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POD</b></th>
                    @if($quote->type=='LCL')
                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                    @else
                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                    @endif
                    <th ><b>W/M</b></th>
                    <th ><b>Total</b></th>
                    @if($quote->pdf_option->show_schedules==1)
                        <th {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Type</b></th>
                        <th {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Servicio</b></th>
                        <th {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Tipo</b></th>
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
                        $total_freight= 0;
                        $total_origin= 0;
                        $total_destination= 0;
                        $total_inland= 0;
                        foreach($rate->charge_lcl_air as $value){
                            if($quote->pdf_option->show_type!='charges'){
                                $total_freight+=$value->total_freight;
                            }
                            $total_origin+=$value->total_origin;
                            $total_destination+=$value->total_destination;
                        }
                        foreach($rate->automaticInlandLclAir as $inland){
                            $total_inland+=$inland->total_inland;
                        }
                    ?>
                    <tr class="text-center color-table"> 
                        <td >
                            @if($quote->type=='LCL') 
                                {{@$rate->origin_port->name}}, {{@$rate->origin_port->code}} 
                            @else 
                                {{@$rate->origin_airport->name}}, {{@$rate->origin_airport->code}}
                            @endif
                        </td>
                        <td >
                            @if($quote->type=='LCL') 
                                {{$rate->destination_port->name}}, {{$rate->destination_port->code}} 
                            @else
                                {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}
                            @endif
                        </td> 
                        @if($quote->type=='LCL')
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                        @else
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                        @endif
                        <td >{{number_format((float)(@$total_freight+@$total_origin+@$total_destination)/$quote->chargeable_weight, 4, '.', '')}}</td>
                        <td >{{number_format((float)@$total_freight+@$total_origin+@$total_destination+@$total_inland, 2, '.', '')}}</td>
                        @if($quote->pdf_option->show_schedules==1)
                            @if($quote->pdf_option->language=='Spanish')
                                @if($rate->schedule_type=='Transfer')
                                    <td>Transbordo</td>
                                @elseif($rate->schedule_type=='Direct')
                                    <td>Directo</td>
                                @else
                                    <td>-</td>
                                @endif
                            @else
                                <td>{{$rate->schedule_type!='' ? $rate->schedule_type:'-'}}</td>
                            @endif                            
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

        <!-- Freight charges all in -->
        @if($quote->pdf_option->show_type=='detailed' && $rates->count()>1)
            <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete</p>
                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
                <br>
            </div>
            <table border="0" cellspacing="1" cellpadding="1" >
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POL</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto de embarque</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto de descarga</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POD</b></th>
                        @if($quote->type=='LCL')
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                        @else
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                        @endif
                        <th ><b>Total</b></th>
                        @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Type</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Servicio</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Tipo</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>TT</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>TT</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>TT</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Via</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Vía</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Via</b></th>   
                        @endif                        
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
                                    $total_freight = 0;
                                    $total_freight_units = 0;
                                    $total_freight_rates = 0;
                                    $total_freight_markups = 0; 
                                    foreach($item as $rate){
                                        foreach($rate->charge_lcl_air as $value){
                                            if($value->type_id==3){
                                                $total_freight+=$value->total_freight;
                                                $total_freight_units+=$value->units;
                                                $total_freight_rates+=$value->price_per_unit*$value->units;
                                                $total_freight_markups+=$value->markup;
                                            }
                                        }
                                    }
                                ?>
                                <tr class="text-center color-table">
                                    <td >
                                        @if($quote->type=='LCL') 
                                            {{@$rate->origin_port->name}}, {{@$rate->origin_port->code}} 
                                        @else 
                                            {{@$rate->origin_airport->name}}, {{@$rate->origin_airport->code}}
                                        @endif
                                    </td>
                                    <td >
                                        @if($quote->type=='LCL') 
                                            {{$rate->destination_port->name}}, {{$rate->destination_port->code}} 
                                        @else
                                            {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}
                                        @endif
                                    </td> 
                                    @if($quote->type=='LCL')
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                    @else
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                    @endif
                                    <td >{{@$total_freight}}</td>
                                    @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->show_schedules==0)
                                        @if($quote->pdf_option->language=='Spanish')
                                            @if($rate->schedule_type=='Transfer')
                                                <td>Transbordo</td>
                                            @elseif($rate->schedule_type=='Direct')
                                                <td>Directo</td>
                                            @else
                                                <td>-</td>
                                            @endif
                                        @else
                                            <td>{{$rate->schedule_type!='' ? $rate->schedule_type:'-'}}</td>
                                        @endif  
                                        <td>{{$rate->transit_time!='' ? $rate->transit_time:'-'}}</td>
                                        <td>{{$rate->via!='' ? $rate->via:'-'}}</td>
                                    @endif                                    
                                    <td >{{$currency_cfg->alphacode}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Freigth charges detailed -->
        @if($quote->pdf_option->show_type=='detailed' && $rates->count()==1)
            @if($quote->pdf_option->grouped_freight_charges==0)
                @foreach($freight_charges_grouped as $origin => $value)
                    @foreach($value as $destination => $item)
                        <div>
                             <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges - {{$origin}} | {{$destination}}</p>
                            <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete - {{$origin}} | {{$destination}}</p>
                            <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete - {{$origin}} | {{$destination}}</p>
                            <br>
                        </div>
                        <table border="0" cellspacing="1" cellpadding="1" >
                            <thead class="title-quote text-center header-table">
                                <tr >
                                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Detail</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Detalle</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Detalhe</b></th>
                                    @if($quote->type=='LCL')
                                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                    @else
                                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                                    @endif
                                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Units</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Unidades</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Unidades</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Rate</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Tarifa</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Taxa</b></th>
                                    <th ><b>Total</b></th>
                                    @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Type</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Servicio</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Tipo</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>TT</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>TT</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>TT</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Via</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Vía</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Via</b></th>   
                                    @endif                                    
                                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($item as $rate)
                                <?php
                                    $total_freight= 0;
                                ?>
                                @foreach($rate as $r)
                                    @foreach($r->charge_lcl_air as $v)
                                        @if($v->type_id==3)
                                            <?php
                                                $total_freight+=@$v->total_freight;
                                            ?>
                                            <tr class="text-center color-table">
                                                @if($v->surcharge_id!='')
                                                    <td>{{$v->surcharge->name}}</td>
                                                @else
                                                    <td>{{$quote->type=='LCL' ? 'Ocean Freight':'Freight'}}</td>
                                                @endif
                                                @if($v->surcharge_id!='')
                                                    <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>
                                                        @php
                                                            echo str_replace("Per", "Por", $v->calculation_type->name); 
                                                        @endphp
                                                    </td>
                                                    <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{$v->calculation_type->name}}</td>
                                                    <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{$v->calculation_type->name}}</td>
                                                @else
                                                    <td>TON/M3</td>
                                                @endif
                                                @if($quote->type=='LCL')
                                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                @else
                                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->airline->name}}</td>
                                                @endif
                                                <td >{{$v->units}}</td>
                                                <td >{{$v->rate}}</td>
                                                <td >{{$v->units*$v->rate}}</td>
                                                @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                                    @if($quote->pdf_option->language=='Spanish')
                                                        @if($rate->schedule_type=='Transfer')
                                                            <td>Transbordo</td>
                                                        @elseif($rate->schedule_type=='Direct')
                                                            <td>Directo</td>
                                                        @else
                                                            <td>-</td>
                                                        @endif
                                                    @else
                                                        <td>{{$rate->schedule_type!='' ? $rate->schedule_type:'-'}}</td>
                                                    @endif  
                                                    <td>{{$r->transit_time!='' ? $r->transit_time:'-'}}</td>
                                                    <td>{{$r->via!='' ? $r->via:'-'}}</td>
                                                @endif                                                
                                                <td>{{$v->currency->alphacode}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endforeach
                            <tr>
                                <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total local charges</b></td>
                                <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos de flete</b></td>
                                <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                <td ><b>{{number_format(@$total_freight, 2, '.', '')}}</b></td>
                                <td><b>{{$currency_cfg->alphacode}}</b></td>  
                            </tr>
                        </tbody>
                    </table>
                    @endforeach
                @endforeach
            @else
                <div>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos de flete</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POL</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto de embarque</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto de descarga</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POD</b></th>
                            @if($quote->type=='LCL')
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                            @else
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                            @endif
                            <th ><b>Total</b></th>
                            @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Type</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Servicio</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Tipo</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>TT</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>TT</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>TT</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Via</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Vía</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Via</b></th>   
                            @endif                             
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
                                    $total_freight = 0;
                                    $total_freight_units = 0;
                                    $total_freight_rates = 0;
                                    $total_freight_markups = 0;
                                  ?>  
                                    @foreach($item as $rate)
                                        @foreach($rate->charge_lcl_air as $value)
                                            <?php
                                                if($value->type_id==3){
                                                    if($quote->pdf_option->show_type=='charges'){
                                                        if($value->surcharge_id!=''){
                                                            $total_freight+=$value->total_freight;
                                                            $total_freight_units+=$value->units;
                                                            $total_freight_rates+=$value->price_per_unit*$value->units;
                                                            $total_freight_markups+=$value->markup;
                                                        }
                                                    }else{
                                                        $total_freight+=$value->total_freight;
                                                        $total_freight_units+=$value->units;
                                                        $total_freight_rates+=$value->price_per_unit*$value->units;
                                                        $total_freight_markups+=$value->markup;
                                                    }
                                                }
                                            ?>
                                        @endforeach
                                    @endforeach
                                    <tr class="text-center color-table">
                                        <td >
                                            @if($quote->type=='LCL') 
                                                {{@$rate->origin_port->name}}, {{@$rate->origin_port->code}} 
                                            @else 
                                                {{@$rate->origin_airport->name}}, {{@$rate->origin_airport->code}}
                                            @endif
                                        </td>
                                        <td >
                                            @if($quote->type=='LCL') 
                                                {{$rate->destination_port->name}}, {{$rate->destination_port->code}} 
                                            @else
                                                {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}
                                            @endif
                                        </td> 
                                        @if($quote->type=='LCL')
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                        @else
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                        @endif
                                        <td >{{@$total_freight}}</td>
                                        @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                            @if($quote->pdf_option->language=='Spanish')
                                                @if($rate->schedule_type=='Transfer')
                                                    <td>Transbordo</td>
                                                @elseif($rate->schedule_type=='Direct')
                                                    <td>Directo</td>
                                                @else
                                                    <td>-</td>
                                                @endif
                                            @else
                                                <td>{{$rate->schedule_type!='' ? $rate->schedule_type:'-'}}</td>
                                            @endif  
                                            <td>{{$r->transit_time!='' ? $r->transit_time:'-'}}</td>
                                            <td>{{$r->via!='' ? $r->via:'-'}}</td>
                                        @endif                                         
                                        @if($quote->pdf_option->grouped_freight_charges==1)
                                            <td>{{$quote->pdf_option->freight_charges_currency}}</td>
                                        @else
                                            <td>{{$currency_cfg->alphacode}}</td>
                                        @endif 
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                </table>                
            @endif
        @endif
        
        <!-- ALL in origin table -->
        @if($quote->pdf_option->grouped_origin_charges==1 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            <br>
            @foreach($origin_charges_grouped as $origin=>$detail)
            <br>
                <div>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en origen - {{$origin}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
                            @if($quote->type=='LCL')
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                            @else
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                            @endif
                            <th ><b>Total</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($detail as $v)
                        @foreach($v as $item)
                            <?php
                                $total_origin = 0;
                                $total_origin_units = 0;
                                $total_origin_rates = 0;
                                $total_origin_markups = 0;
                            ?>  
                            @foreach($item as $rate)

                                @foreach($rate->charge_lcl_air as $value)
                                    <?php
                                        if($value->type_id==1){
                                            $total_origin+=$value->total_origin;
                                            $total_origin_units+=$value->units;
                                            $total_origin_rates+=$value->price_per_unit*$value->units;
                                            $total_origin_markups+=$value->markup;
                                        }                            
                                    ?>
                                @endforeach
                            @endforeach
                            <tr class="text-center color-table">
                                <td colspan="2">Total Origin Charges</td>
                                @if($quote->type=='LCL')
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                @else
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                @endif
                                <td >{{@$total_origin}}</td>
                                <td >{{$quote->pdf_option->origin_charges_currency}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                @endforeach
            @endif

        <!-- Origins detailed -->
        @if($quote->pdf_option->grouped_origin_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            <br>
            @foreach($origin_charges_grouped as $origin => $value)
                @foreach($value as $carrier => $item)
                    <div>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en origen - {{$origin}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1">
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Detail</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Detalle</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Detalhe</b></th>
                                @if($quote->type=='LCL')
                                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                @else
                                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                                @endif
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Units</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Unidades</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Unidades</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Rate</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Tarifa</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Taxa</b></th>
                                <th ><b>Total</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($item as $rate)
                            <?php
                                $total_origin = 0;
                                $show_inland = 'hide';
                            ?>
                            @foreach($rate as $r)
                                @foreach($r->charge_lcl_air as $v)
                                    @if($v->type_id==1)
                                        <?php
                                            $total_origin+=@$v->total_origin;
                                        ?>
                                        <tr class="text-center color-table">
                                            <td>{{$v->surcharge->name}}</td>
                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>
                                                @php
                                                    echo str_replace("Per", "Por", $v->calculation_type->name); 
                                                @endphp
                                            </td>
                                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{$v->calculation_type->name}}</td>
                                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{$v->calculation_type->name}}</td>
                                            @if($quote->type=='LCL')
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                            @else
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->airline->name}}</td>
                                            @endif
                                            <td >{{$v->units}}</td>
                                            <td >{{$v->rate}}</td>
                                            <td >{{$v->units*$v->rate}}</td>
                                            <td>{{$v->currency->alphacode}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$r->automaticInlandLclAir->isEmpty()){
                                    @php
                                        $total_inland_origin=0;
                                    @endphp
                                    @foreach($r->automaticInlandLclAir as $v)
                                        @if($v->type=='Origin')
                                            @if($r->automaticInlandLclAir->where('type', 'Origin')->count()==1)
                                                <?php
                                                    $total_inland_origin+=$v->total_inland_origin;
                                                ?>
                                                <tr class="text-center color-table">
                                                    <td>{{$v->provider}}</td>
                                                    <td>-</td>
                                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                                    <td >{{$v->units}}</td>
                                                    <td >{{$v->rate_amount}}</td>
                                                    <td >{{$v->units*$v->rate_amount}}</td>
                                                    <td>{{$v->currency->alphacode}}</td>
                                                </tr>
                                            @else
                                                <?php
                                                    $show_inland = '';
                                                ?>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif                            
                            @endforeach
                        @endforeach
                        <tr>
                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total local charges</b></td>
                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos en origen</b></td>
                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                            <td ><b>{{number_format(@$total_origin+@$total_inland_origin, 2, '.', '')}}</b></td>
                            @if($quote->pdf_option->grouped_origin_charges==1)
                                <td><b>{{$quote->pdf_option->origin_charges_currency}}</b></td>
                            @else
                                <td><b>{{$currency_cfg->alphacode}}</b></td>
                            @endif     
                        </tr>
                        <tr class="{{$show_inland}}" style="background-color: white !important;">
                            <td  style="background-color: white !important;" colspan="2">
                                <br>
                                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin inlands - {{$origin}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Inlands de origen - {{$origin}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Inlands de origem - {{$origin}}</p>
                            </td>
                        </tr>
                        @if(!$r->automaticInlandLclAir->isEmpty()){
                            @php
                                $total_inland=0;
                            @endphp
                            @foreach($r->automaticInlandLclAir as $v)
                                @if($v->type=='Origin')
                                    @if($r->automaticInlandLclAir->where('type', 'Origin')->count()>1)
                                        <?php
                                            //if($r->automaticInlandLclAir->where('type', 'Destination')->count()==1){
                                                $total_inland+=@$v->total_inland_origin;
                                            //}
                                        ?>
                                        <tr class="text-center color-table">
                                            <td>{{$v->provider}}</td>
                                            <td>-</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                            <td >{{$v->units}}</td>
                                            <td >{{$v->rate_amount}}</td>
                                            <td >{{$v->units*$v->rate_amount}}</td>
                                            <td>{{$v->currency->alphacode}}</td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
                @endforeach
            @endforeach
        @endif
        
        <!-- All in destination table -->
        @if($quote->pdf_option->grouped_destination_charges==1 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            <br>
            @foreach($destination_charges_grouped as $destination=>$detail)
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$destination}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en destino - {{$destination}}</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$destination}}</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
                            @if($quote->type=='LCL')
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                            @else
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                            @endif
                            <th ><b>Total</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($detail as $v)
                        @foreach($v as $item)
                            <?php
                                $total_destination = 0;
                                $total_destination_units = 0;
                                $total_destination_rates = 0;
                                $total_destination_markups = 0;
                            ?>  
                            @foreach($item as $rate)

                                @foreach($rate->charge_lcl_air as $value)
                                    <?php
                                        if($value->type_id==2){
                                            $total_destination+=$value->total_destination;
                                            $total_destination_units+=$value->units;
                                            $total_destination_rates+=$value->price_per_unit*$value->units;
                                            $total_destination_markups+=$value->markup;
                                        }                            
                                    ?>
                                @endforeach
                            @endforeach
                            <tr class="text-center color-table">
                                <td colspan="2">Total Destination Charges</td>
                                @if($quote->type=='LCL')
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                @else
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                @endif
                                <td >{{@$total_destination}}</td>
                                <td >{{$quote->pdf_option->destination_charges_currency}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endforeach
            @endforeach
        @endif
                
        <!-- Destination detailed -->
        @if($quote->pdf_option->grouped_destination_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            <br>
            @foreach($destination_charges_grouped as $destination => $value)
                @foreach($value as $carrier => $item)
                    <div>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en destino - {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$destination}}</p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1">
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Detail</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Detalle</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Detalhe</b></th>
                                @if($quote->type=='LCL')
                                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                @else
                                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Airline @elseif($quote->pdf_option->language=='Spanish') Línea aérea @else Linha aérea @endif</b></th>
                                @endif
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Units</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Unidades</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Unidades</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Rate</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Tarifa</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Taxa</b></th>
                                <th ><b>Total</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($item as $rate)
                            <?php
                                $total_destination= 0;
                                $show_inland = 'hide';
                            ?>
                            @foreach($rate as $r)
                                @foreach($r->charge_lcl_air as $v)
                                    @if($v->type_id==2)
                                        <?php
                                            $total_destination+=@$v->total_destination;
                                        ?>
                                        <tr class="text-center color-table">
                                            <td>{{$v->surcharge->name}}</td>
                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>
                                                @php
                                                    echo str_replace("Per", "Por", $v->calculation_type->name); 
                                                @endphp
                                            </td>
                                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{$v->calculation_type->name}}</td>
                                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{$v->calculation_type->name}}</td>
                                            @if($quote->type=='LCL')
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                            @else
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->airline->name}}</td>
                                            @endif
                                            <td >{{$v->units}}</td>
                                            <td >{{$v->rate}}</td>
                                            <td >{{$v->units*$v->rate}}</td>
                                            <td>{{$v->currency->alphacode}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$r->automaticInlandLclAir->isEmpty()){
                                    @php
                                        $total_inland=0;
                                    @endphp
                                    @foreach($r->automaticInlandLclAir as $v)
                                        @if($v->type=='Destination')
                                            @if($r->automaticInlandLclAir->where('type', 'Destination')->count()==1)
                                                <?php
                                                    //if($r->automaticInlandLclAir->where('type', 'Destination')->count()==1){
                                                        $total_inland+=@$v->total_inland_destination;
                                                    //}
                                                ?>
                                                <tr class="text-center color-table">
                                                    <td>{{$v->provider}}</td>
                                                    <td>-</td>
                                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                                    <td >{{$v->units}}</td>
                                                    <td >{{$v->rate_amount}}</td>
                                                    <td >{{$v->units*$v->rate_amount}}</td>
                                                    <td>{{$v->currency->alphacode}}</td>
                                                </tr>
                                            @else
                                                <?php
                                                    $show_inland = '';
                                                ?>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endforeach
                        <tr>
                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total local charges</b></td>
                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos de destino</b></td>
                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                            <td ><b>{{number_format(@$total_destination+@$total_inland, 2, '.', '')}}</b></td>
                            @if($quote->pdf_option->grouped_destintion_charges==1)
                                <td><b>{{$quote->pdf_option->destination_charges_currency}}</b></td>
                            @else
                                <td><b>{{$currency_cfg->alphacode}}</b></td>
                            @endif     
                        </tr>
                        <tr class="{{$show_inland}}" style="background-color: white !important;">
                            <td  style="background-color: white !important;" colspan="2">
                                <br>
                                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination inlands - {{$destination}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Inlands de destino - {{$destination}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Inlands de destino - {{$destination}}</p>
                            </td>
                        </tr>
                        @if(!$r->automaticInlandLclAir->isEmpty()){
                            @php
                                $total_inland=0;
                            @endphp
                            @foreach($r->automaticInlandLclAir as $v)
                                @if($v->type=='Destination')
                                    @if($r->automaticInlandLclAir->where('type', 'Destination')->count()>1)
                                        <?php
                                            //if($r->automaticInlandLclAir->where('type', 'Destination')->count()==1){
                                                $total_inland+=@$v->total_inland_destination;
                                            //}
                                        ?>
                                        <tr class="text-center color-table">
                                            <td>{{$v->provider}}</td>
                                            <td>-</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                            <td >{{$v->units}}</td>
                                            <td >{{$v->rate_amount}}</td>
                                            <td >{{$v->units*$v->rate_amount}}</td>
                                            <td>{{$v->currency->alphacode}}</td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        @endif                            
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
            <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
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
                <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
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
                <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
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
                    <img src="{{Storage::disk('s3_upload')->url($user->companyUser->footer_image)}}" class="img img-fluid" style="max-width:100%;">
                </div>
            @endif
        @else
            {!!$user->companyUser->footer_text!!}
        @endif
    </footer>        
</body>
</html>