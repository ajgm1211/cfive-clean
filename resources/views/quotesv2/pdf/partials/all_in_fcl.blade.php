            @switch($quote->pdf_option->show_type)
                @case('total in')
                    <div {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total estimated costs</b></p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos totales estimados</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Custos totais estimados</p>
                        <br>
                    </div>
                    @break

                @case('detailed')
                    <div {{$quote->pdf_option->grouped_total_currency==1 ? '':'hidden'}}>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total estimated costs</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos totales estimados</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Custos totais estimados</p>
                        <br>
                    </div>
                    @break

            @endswitch
            @if($quote->pdf_option->show_type=='total in')
                <table border="0" cellspacing="1" cellpadding="1" {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
            @else
                <table border="0" cellspacing="1" cellpadding="1" {{$quote->pdf_option->grouped_total_currency==1 ? '':'hidden'}}>
            @endif
                    <thead class="title-quote text-left header-table">
                        <tr >
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POL</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto embarque</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POL</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>POD</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Puerto descarga</b></th>
                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>POD</b></th>
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea @else Linha Maritima @endif</b></th>
                            @foreach ($equipmentHides as $key=>$item)
                                @foreach ($containers as $c)
                                    @if($c->code == $key)
                                        <th {{$item}}><b>{{$key}}</b></th>
                                    @endif
                                @endforeach
                            @endforeach
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

                                foreach ($containers as $c){ 
                                    ${'total_'.$c} = 0;
                                    ${'sum_total_'.$c} = 0;
                                    ${'sum_inland_'.$c} = 0;
                                    ${'sum_total_inland_'.$c} = 0;
                                    ${'total_c'.$c} = 'total_c'.$c;
                                    ${'total_m'.$c} = 'total_m'.$c;
                                }

                                $array = array();

                                foreach($rate->charge as $value){
                                    array_push($array, $value->type_id);
                                    if($quote->pdf_option->show_type=='charges'){
                                        if($value->type_id!=3){
                                            foreach ($containers as $c){
                                                ${'total_'.$c}=$value->${'total_c'.$c}+$value->${'total_m'.$c};
                                                ${'sum_total_'.$c} += ${'total_'.$c};
                                            }
                                        }
                                    }else{
                                        foreach ($containers as $c){ 
                                            ${'total_'.$c}=$value->${'total_c'.$c}+$value->${'total_m'.$c};
                                            ${'sum_total_'.$c} += ${'total_'.$c};
                                        }
                                    }
                                }

                                if(!$rate->inland->isEmpty()){
                                    foreach($rate->inland as $inland){
                                        if($rate->inland->where('port_id', $inland->port_id)->count()==1){
                                            foreach ($containers as $c){
                                                ${'sum_inland_'.$c}=$inland->${'total_c'.$c}+$inland->${'total_m'.$c};
                                                ${'sum_total_inland_'.$c}+=${'sum_inland_'.$c};
                                            }
                                        }
                                    }
                                }
                            ?>
                            <tr class="text-left color-table">
                                <td >@if($rate->origin_address=='' && $rate->origin_port_id!='')  {{$rate->origin_port->name}}, {{$rate->origin_port->code}} @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}} @else {{$rate->origin_address}} @endif</td>
                                <td >@if($rate->destination_address=='' && $rate->destination_port_id!=''){{$rate->destination_port->name}}, {{$rate->destination_port->code}} @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}} @else {{$rate->destination_address}} @endif
                                </td>
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                @foreach ($equipmentHides as $key=>$item)
                                    @foreach ($containers as $c)
                                        @if($c->code == $key)
                                            <td {{ $item }}>{{number_format((float)@${'sum_total_'.$c}+@${'sum_total_inland_'.$c}, 2, '.', '')}}</td>
                                        @endif
                                    @endforeach
                                @endforeach
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