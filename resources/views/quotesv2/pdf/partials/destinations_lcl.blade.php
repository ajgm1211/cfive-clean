        <!-- All in destination table -->
        @if($quote->pdf_option->grouped_destination_charges==1 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            <br>
            @forelse($destination_charges_grouped as $destination=>$detail)
                <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>
                    @switch($quote->pdf_option->language)
                        @case("English")
                            <p class="title">Destination charges - {{$destination}}</p>
                            @break

                        @case("Portuguese")
                            <p class="title">Encargos de destino - {{$destination}}</p>
                            @break

                        @case("Spanish")
                            <p class="title">Costos en destino - {{$destination}}</p>
                            @break

                        @default
                            <p class="title">Destination charges - {{$destination}}</p>
                    @endswitch                    
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
                    <?php
                        $total_sale_terms_destination = 0;
                    ?>
                    @if($sale_terms_destination->count()>0)
                        @foreach($sale_terms_destination as $v)
                            @foreach($v as $value)
                                @foreach($value->charge as $item)
                                @php
                                    $total_sale_terms_destination += $item->total_sale_destination;
                                @endphp
                                @endforeach
                            @endforeach
                        @endforeach
                    @endif                        
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
                                <td colspan="">Total Destination Charges</td>
                                @if($quote->type=='LCL')
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                @else
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                @endif
                                <td >{{@$total_destination+@$total_sale_terms_destination}}</td>
                                <td >{{$quote->pdf_option->destination_charges_currency}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endforeach
            @empty
            @if($sale_terms_destination->count()>0)
                    @foreach($sale_terms_destination as $destination=>$sale_destination)
                        @foreach($sale_destination as $value)
                            <div>
                                @switch($quote->pdf_option->language)
                                    @case("English")
                                        <p class="title">Destination charges - {{$destination}}</p>
                                        @break

                                    @case("Portuguese")
                                        <p class="title">Encargos de destino - {{$destination}}</p>
                                        @break

                                    @case("Spanish")
                                        <p class="title">Costos en destino - {{$destination}}</p>
                                        @break

                                    @default
                                        <p class="title">Destination charges - {{$destination}}</p>
                                @endswitch                    
                                <br>
                            </div>

                            <table border="0" cellspacing="1" cellpadding="1" >
                                <thead class="title-quote text-left header-table">
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
                                    @php
                                        $total_destination=0;
                                    @endphp
                                    @foreach($value->charge_lcl_air as $item)
                                        @php
                                            $total_destination += $item->total_sale_destination;
                                        @endphp
                                    @endforeach
                                        <tr class="text-left color-table">
                                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total destination charges</td>
                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Total gastos en destino</td>
                                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Total de cobranças locais</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                            <td {{ @$equipmentHides['40hc'] }}>{{number_format(@$total_destination, 2, '.', '')}}</td>
                                            @if($quote->pdf_option->grouped_destination_charges==1)
                                                <td >{{$quote->pdf_option->origin_charges_currency}}</td>
                                            @else
                                                <td >{{$currency_cfg->alphacode}}</td>
                                            @endif
                                        </tr>
                                </tbody>
                            </table>
                            @endforeach
                        @endforeach
                    @endif
            @endforelse
            <br>
        @endif
                
        @if($quote->pdf_option->grouped_destination_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            @if($sale_terms_destination->count()>0)
                @foreach($sale_terms_destination as $destination=>$sale_destination)
                @foreach($sale_destination as $value)
                    <div>
                        @switch($quote->pdf_option->language)
                            @case("English")
                                <p class="title">Destination charges - {{$destination}}</p>
                                @break

                            @case("Portuguese")
                                <p class="title">Encargos de destino - {{$destination}}</p>
                                @break

                            @case("Spanish")
                                <p class="title">Costos en destino - {{$destination}}</p>
                                @break

                            @default
                                <p class="title">Destination charges - {{$destination}}</p>
                        @endswitch                    
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
                        @php
                            $total_destination = 0;
                        @endphp
                        @foreach($value->charge as $rate)
                            @php
                                $total_destination += @$rate->total_sale_destination;
                            @endphp
                            <tr class="text-center color-table">
                                <td>{{$rate->charge}}</td>
                                <td>{{$rate->detail}}</td>
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                <td >{{$rate->units}}</td>
                                <td >{{$rate->rate}}</td>
                                <td >{{$rate->total}}</td>
                                <td>{{$rate->currency->alphacode}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total local charges</b></td>
                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos en destino</b></td>
                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                            <td ><b>{{number_format(@$total_destination, 2, '.', '')}}</b></td>
                            @if($quote->pdf_option->grouped_destintion_charges==1)
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
        @endif
                
        <!-- Destination detailed -->
        @if($quote->pdf_option->grouped_destination_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            <br>
            @foreach($destination_charges_grouped as $destination => $value)
                @foreach($value as $carrier => $item)
                    <div>
                        @switch($quote->pdf_option->language)
                            @case("English")
                                <p class="title">Destination charges - {{$destination}}</p>
                                @break

                            @case("Portuguese")
                                <p class="title">Encargos de destino - {{$destination}}</p>
                                @break

                            @case("Spanish")
                                <p class="title">Costos en destino - {{$destination}}</p>
                                @break

                            @default
                                <p class="title">Destination charges - {{$destination}}</p>
                        @endswitch                    
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
                                $total_inland= 0;
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
                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
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