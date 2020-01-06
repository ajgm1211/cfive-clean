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
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto embarque</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto descarga</b></th>
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
                                
                                @forelse($rate as $r)
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
                                                            //echo str_replace("Per", "Por", $v->calculation_type->name); 
                                                        @endphp
                                                        {{@$v->calculation_type->display_name}}
                                                    </td>
                                                    <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                    <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                @else
                                                    <td>{{$quote->type=='LCL' ? 'TON/M3':'CW'}}</td>
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
                                                        @if(@$r->schedule_type=='Transfer')
                                                            <td>Transbordo</td>
                                                        @elseif(@$r->schedule_type=='Direct')
                                                            <td>Directo</td>
                                                        @else
                                                            <td>-</td>
                                                        @endif
                                                    @else
                                                        <td>{{@$r->schedule_type!='' ? @$r->schedule_type:'-'}}</td>
                                                    @endif  
                                                    <td>{{@$r->transit_time!='' ? @$r->transit_time:'-'}}</td>
                                                    <td>{{@$r->via!='' ? @$r->via:'-'}}</td>
                                                @endif                                                
                                                <td>{{$v->currency->alphacode}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @empty

                                @endforelse
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
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto embarque</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto descarga</b></th>
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
                                            <td>{{@$r->transit_time!='' ? @$r->transit_time:'-'}}</td>
                                            <td>{{@$r->via!='' ? @$r->via:'-'}}</td>
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