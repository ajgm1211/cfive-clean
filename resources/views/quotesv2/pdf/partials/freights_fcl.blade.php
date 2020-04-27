                <!-- Freights table all in-->
                @if($quote->pdf_option->show_type=='detailed' && $rates->count()>1)
                    <div>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Gastos de flete</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
                        <br>
                    </div>

                    <table border="0" cellspacing="1" cellpadding="1" >
                        <thead class="title-quote text-left header-table">
                            <tr >
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POL</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto embarque</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto descarga</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POD</b></th>
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                @foreach ($equipmentHides as $key=>$item)
                                    @foreach ($containers as $c)
                                        @if($c->code == $key)
                                            <th class="unit" {{$item}}><b>{{$key}}</b></th>
                                        @endif
                                    @endforeach
                                @endforeach
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
                            @php
                                foreach ($containers as $c){
                                    ${'freight_'.$c} = 'freight_'.$c;
                                    ${'inland_freight_'.$c} = 'inland_freight_'.$c;
                                    ${'total_sum_'.$c} = 'total_sum_'.$c;
                                }
                            @endphp
                            @foreach($freight_charges_grouped as $origin=>$freight)
                                @foreach($freight as $destination=>$detail)
                                    @foreach($detail as $item)
                                        <?php
                                            foreach ($containers as $c){
                                                ${'freight_'.$c} = 0;  
                                                ${'inland_freight_'.$c} = 0; 
                                            }
                                            foreach($item as $rate){
                                                foreach($rate->charge as $value){
                                                    foreach ($containers as $c){
                                                        ${'freight_'.$c}+=$value->${'total_sum_'.$c};
                                                    }
                                                }
                                            }
                                        ?>
                                        <tr class="text-left color-table">
                                            <td >@if($rate->origin_address=='' && $rate->origin_port_id!='') {{$rate->origin_port->name}}, {{$rate->origin_port->code}} @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}} @else  {{$rate->origin_address}} @endif</td>
                                            <td >@if($rate->destination_address=='' && $rate->destination_port_id!='') {{$rate->destination_port->name}}, {{$rate->destination_port->code}} @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}@else {{$rate->destination_address}} @endif</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                            @foreach ($equipmentHides as $key=>$item)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <td {{$item}}>{{number_format(@${'freight_'.$c}, 2, '.', '')}}</td>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                                @if($quote->pdf_option->language=='Spanish')
                                                    @if(@$rate->schedule_type=='Transfer')
                                                        <td>Transbordo</td>
                                                    @elseif(@$rate->schedule_type=='Direct')
                                                        <td>Directo</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                @else
                                                    <td>{{@$rate->schedule_type!='' ? @$rate->schedule_type:'-'}}</td>
                                                @endif
                                                <td>{{@$rate->transit_time!='' ? @$rate->transit_time:'-'}}</td>
                                                <td>{{@$rate->via!='' ? @$rate->via:'-'}}</td>
                                            @endif
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
                    <br>
                @endif
                
                <!-- Freight charges detailed -->
                @if($quote->pdf_option->show_type=='detailed' && $rates->count()==1)
                    @if($quote->pdf_option->grouped_freight_charges==0)
                        @foreach($freight_charges_grouped as $origin => $value)
                            @foreach($value as $destination => $item)
                                <div>
                                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges - {{$origin}} | {{$destination}}</p>
                                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Gastos de flete - {{$origin}} | {{$destination}}</p>
                                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete - {{$origin}} | {{$destination}}</p>
                                    <br>
                                </div>
                                <table border="0" cellspacing="1" cellpadding="1">
                                    <thead class="title-quote text-left header-table">
                                        <tr >
                                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Detail</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Detalle</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Detalhe</b></th>
                                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                            @foreach ($equipmentHides as $key=>$item)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <th class="unit" {{$item}}><b>{{$key}}</b></th>
                                                    @endif
                                                @endforeach
                                            @endforeach
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
                                            $sum_freight_20= 0;
                                            $sum_freight_40= 0;
                                            $sum_freight_40hc= 0;
                                            $sum_freight_40nor= 0;
                                            $sum_freight_45= 0;
                                        ?>
                                        @forelse($rate as $r)
                                            @foreach($r->charge as $v)
                                                @if($v->type_id==3)
                                                    <?php
                                                        $total_freight_20= 0;
                                                        $total_freight_40= 0;
                                                        $total_freight_40hc= 0;
                                                        $total_freight_40nor= 0;
                                                        $total_freight_45= 0;
                                                    ?>
                                                    @if($quote->pdf_option->show_type!='charges')
                                                        <?php
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
                                                                <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>
                                                                    @php
                                                                        //echo str_replace("Per", "Por", $v->calculation_type->name); 
                                                                    @endphp
                                                                    {{@$v->calculation_type->display_name}}
                                                                </td>
                                                                <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                                <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                            @else
                                                                <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Per container</td>
                                                                <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Por container</td>
                                                                <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Per container</td>
                                                            @endif
                                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                            <td {{ @$equipmentHides['20'] }}>{{number_format($v->total_20, 2, '.', '')}}</td>
                                                            <td {{ @$equipmentHides['40'] }}>{{number_format($v->total_40, 2, '.', '')}}</td>
                                                            <td {{ @$equipmentHides['40hc'] }}>{{number_format($v->total_40hc, 2, '.', '')}}</td>
                                                            <td {{ @$equipmentHides['40nor'] }}>{{number_format($v->total_40nor, 2, '.', '')}}</td>
                                                            <td {{ @$equipmentHides['45'] }}>{{number_format($v->total_45, 2, '.', '')}}</td>
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
                                                            <td>{{$currency_cfg->alphacode}}</td>
                                                        </tr>
                                                    @else
                                                        @if($v->surcharge_id!='')
                                                            <?php
                                                                $sum_freight_20+=$v->total_20;
                                                                $sum_freight_40+=$v->total_40;
                                                                $sum_freight_40hc+=$v->total_40hc;
                                                                $sum_freight_40nor+=$v->total_40nor;
                                                                $sum_freight_45+=$v->total_45;
                                                            ?>                                        
                                                            <tr class="text-left color-table">
                                                                <td>{{$v->surcharge->name}}</td>
                                                                <td>{{$v->calculation_type->name}}</td>
                                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                                <td {{ @$equipmentHides['20'] }}>{{number_format($v->total_20, 2, '.', '')}}</td>
                                                                <td {{ @$equipmentHides['40'] }}>{{number_format($v->total_40, 2, '.', '')}}</td>
                                                                <td {{ @$equipmentHides['40hc'] }}>{{number_format($v->total_40hc, 2, '.', '')}}</td>
                                                                <td {{ @$equipmentHides['40nor'] }}>{{number_format($v->total_40nor, 2, '.', '')}}</td>
                                                                <td {{ @$equipmentHides['45'] }}>{{number_format($v->total_45, 2, '.', '')}}</td>
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
                                                                <td >{{$currency_cfg->alphacode}}</td>
                                                            </tr>
                                                        @endif
                                                    @endif
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
                        <br>
                    @else
                        @if($quote->pdf_option->show_type=='detailed')
                            <div>
                                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Freight charges</p>
                                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Gastos de flete</p>
                                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de frete</p>
                                <br>
                            </div>
                        @endif
                        <table border="0" cellspacing="1" cellpadding="1" >
                            <thead class="title-quote text-left header-table">
                                <tr >
                                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POL</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto embarque</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto descarga</b></th>
                                    <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POD</b></th>
                                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                    @foreach ($equipmentHides as $key=>$item)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)
                                                <th class="unit" {{$item}}><b>{{$key}}</b></th>
                                            @endif
                                        @endforeach
                                    @endforeach
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
                                            <td >@if($rate->origin_address=='' && $rate->origin_port_id!=''){{$rate->origin_port->name}}, {{$rate->origin_port->code}} @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}}@else {{$rate->origin_address}} @endif</td>
                                            <td >@if($rate->destination_address=='' && $rate->destination_port_id!='') {{$rate->destination_port->name}}, {{$rate->destination_port->code}} @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}@else {{$rate->destination_address}} @endif</td>                           
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                            <td {{ @$equipmentHides['20'] }}>{{number_format(@$sum_freight_20, 2, '.', '')}}</td>
                                            <td {{ @$equipmentHides['40'] }}>{{number_format(@$sum_freight_40, 2, '.', '')}}</td>
                                            <td {{ @$equipmentHides['40hc'] }}>{{number_format(@$sum_freight_40hc, 2, '.', '')}}</td>
                                            <td {{ @$equipmentHides['40nor'] }}>{{number_format(@$sum_freight_40nor, 2, '.', '')}}</td>
                                            <td {{ @$equipmentHides['45'] }}>{{number_format(@$sum_freight_45, 2, '.', '')}}</td>
                                            @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                                @if($quote->pdf_option->language=='Spanish')
                                                    @if(@$rate->schedule_type=='Transfer')
                                                        <td>Transbordo</td>
                                                    @elseif(@$rate->schedule_type=='Direct')
                                                        <td>Directo</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                @else
                                                    <td>{{@$rate->schedule_type!='' ? @$rate->schedule_type:'-'}}</td>
                                                @endif
                                                <td>{{@$rate->transit_time!='' ? @$rate->transit_time:'-'}}</td>
                                                <td>{{@$rate->via!='' ? @$rate->via:'-'}}</td>
                                            @endif
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