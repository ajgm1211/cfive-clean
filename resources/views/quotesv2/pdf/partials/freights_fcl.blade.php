                <!-- Freights table all in-->
                @if($quote->pdf_option->show_type=='detailed' && $rates->count()>1)
                    <div>
                        <p class="title">{{__('pdf.freight_charges')}}</p>
                        <br>
                    </div>

                    <table border="0" cellspacing="1" cellpadding="1" >
                        <thead class="title-quote text-left header-table">
                            <tr >
                                <th class="unit"><b>{{__('pdf.pol')}}</b></th>
                                <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                @foreach ($equipmentHides as $key=>$hide)
                                    @foreach ($containers as $c)
                                        @if($c->code == $key)
                                            <th class="unit" {{$hide}}><b>{{$key}}</b></th>
                                        @endif
                                    @endforeach
                                @endforeach
                                @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                    <th class="unit"><b>{{__('pdf.type')}}</b></th>
                                    <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                                    <th class="unit"><b>{{__('pdf.via')}}</b></th>                                
                                @endif
                                <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                foreach ($containers as $c){
                                    ${'freight_'.$c->code} = 'freight_'.$c->code;
                                    ${'inland_freight_'.$c->code} = 'inland_freight_'.$c->code;
                                    ${'total_sum_'.$c->code} = 'total_sum_'.$c->code;
                                }
                            @endphp
                            @foreach($freight_charges_grouped as $origin=>$freight)
                                @foreach($freight as $destination=>$detail)
                                    @foreach($detail as $item)
                                        <?php
                                            foreach ($containers as $c){
                                                ${'freight_'.$c->code} = 0;  
                                                ${'inland_freight_'.$c->code} = 0;
                                            }
                                            foreach($item as $rate){
                                                foreach($rate->charge as $value){
                                                    foreach ($containers as $c){
                                                        ${'freight_'.$c->code}+=$value->${'total_sum_'.$c->code};
                                                    }
                                                }
                                            }
                                        ?>
                                        <tr class="text-left color-table">
                                            <td >@if($rate->origin_address=='' && $rate->origin_port_id!='') {{$rate->origin_port->name}}, {{$rate->origin_port->code}} @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}} @else  {{$rate->origin_address}} @endif</td>
                                            <td >@if($rate->destination_address=='' && $rate->destination_port_id!='') {{$rate->destination_port->name}}, {{$rate->destination_port->code}} @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}@else {{$rate->destination_address}} @endif</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <td {{$hide}}>{{ @${'freight_'.$c->code} }}</td>
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
                                    <p class="title">{{__('pdf.freight_charges')}} - {{$origin}} | {{$destination}}</p>
                                    <br>
                                </div>
                                <table border="0" cellspacing="1" cellpadding="1">
                                    <thead class="title-quote text-left header-table">
                                        <tr >
                                            <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                            <th class="unit"><b>{{__('pdf.detail')}}</b></th>
                                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <th class="unit" {{$hide}}><b>{{$key}}</b></th>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                                <th class="unit"><b>{{__('pdf.type')}}</b></th>
                                                <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                                                <th class="unit"><b>{{__('pdf.via')}}</b></th>
                                            @endif                                            
                                            <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($item as $rate)
                                        <?php
                                            foreach ($containers as $c){
                                                ${'sum_freight_'.$c->code} = 0;
                                            }
                                        ?>
                                        @forelse($rate as $r)
                                            @foreach($r->charge as $v)
                                                @if($v->type_id==3)
                                                    <?php
                                                        $amount = json_decode($v->amount);
                                                        $markup = json_decode($v->markup);
                                                        
                                                        foreach ($containers as $c){
                                                            ${'total_freight_'.$c->code} = 0;
                                                            ${'total_sum_'.$c->code} = 'total_sum_'.$c->code;
                                                            ${'sum_amount_markup_'.$c->code} = 'sum_amount_markup_'.$c->code;
                                                        }
                                                    ?>
                                                    @if($quote->pdf_option->show_type!='charges')
                                                        <?php
                                                            foreach ($containers as $c){
                                                                ${'sum_freight_'.$c->code}+=$v->${'total_sum_'.$c->code};
                                                            }
                                                        ?>
                                                        <tr class="text-left color-table">
                                                            @if($v->surcharge_id!='')
                                                                <td>{{$v->surcharge->name}}</td>
                                                            @else
                                                                <td>Ocean freight</td>
                                                            @endif
                                                            @if($v->surcharge_id!='')
                                                                <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>{{@$v->calculation_type->name}}</td>
                                                                <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->name}}</td>
                                                                <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->name}}</td>
                                                            @else
                                                                <td>{{__('pdf.per_container')}}</td>
                                                            @endif
                                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                            @foreach ($equipmentHides as $key=>$hide)
                                                                @foreach ($containers as $c)
                                                                    @if($c->code == $key)
                                                                        <!--<td {{ $hide }}>{{isDecimal($v->${'total_sum_'.$c->code}, true)}}</td>-->
                                                                        <td {{ $hide }}>{{ @$v->${'sum_amount_markup_'.$c->code} == null ? 0 : @$v->${'sum_amount_markup_'.$c->code} }}</td>
                                                                    @endif
                                                                @endforeach
                                                            @endforeach
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
                                                    @else
                                                        @if($v->surcharge_id!='')
                                                            <?php
                                                                foreach ($containers as $c){
                                                                    ${'sum_freight_'.$c->code}+=$v->${'total_sum_'.$c->code};
                                                                }
                                                            ?>                            
                                                            <tr class="text-left color-table">
                                                                <td>{{$v->surcharge->name}}</td>
                                                                <td>{{$v->calculation_type->name}}</td>
                                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                                @foreach ($equipmentHides as $key=>$hide)
                                                                    @foreach ($containers as $c)
                                                                        @if($c->code == $key)
                                                                            <td {{ $hide }}>{{ $v->${'total_sum_'.$c->code} }}</td>
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
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
                                        <td><b>{{__('pdf.total_local')}}</b></td>
                                        <td></td>
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                        @foreach ($equipmentHides as $key=>$hide)
                                            @foreach ($containers as $c)
                                                @if($c->code == $key)
                                                    <td {{ $hide }}><b>{{ @${'sum_freight_'.$c->code} }}</b></td>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                        <td><b>{{@$r->currency->alphacode}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                            @endforeach
                        @endforeach
                        <br>
                    @else
                        @if($quote->pdf_option->show_type=='detailed')
                            <div>
                                <p class="title">{{__('pdf.freight_charges')}}</p>
                                <br>
                            </div>
                        @endif
                        <table border="0" cellspacing="1" cellpadding="1" >
                            <thead class="title-quote text-left header-table">
                                <tr >
                                    <th class="unit"><b>{{__('pdf.pol')}}</b></th>
                                    <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{ __('pdf.carrier') }}</b></th>
                                    @foreach ($equipmentHides as $key=>$hide)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)
                                                <th class="unit" {{$hide}}><b>{{$key}}</b></th>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                        <th class="unit"><b>{{__('pdf.type')}}</b></th>
                                        <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                                        <th class="unit"><b>{{__('pdf.via')}}</b></th>                                
                                    @endif
                                    <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($freight_charges_grouped as $origin=>$freight)
                                    @foreach($freight as $destination=>$detail)
                                        @foreach($detail as $item)
                                        <?php
                                            foreach ($containers as $c){
                                                ${'sum_freight_'.$c->code} = 0;
                                                ${'inland_freight_'.$c->code} = 0;
                                                ${'total_sum_'.$c->code} = 'total_sum_'.$c->code;
                                            }
                                        ?>  
                                        @foreach($item as $rate)
                                            @foreach($rate->charge as $value)
                                                <?php
                                                    foreach ($containers as $c){
                                                        ${'sum_freight_'.$c->code}+=$value->${'total_sum_'.$c->code};
                                                    }                               
                                                ?>
                                            @endforeach
                                        @endforeach
                                        <tr class="text-left color-table">
                                            <td >@if($rate->origin_address=='' && $rate->origin_port_id!=''){{$rate->origin_port->name}}, {{$rate->origin_port->code}} @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}}@else {{$rate->origin_address}} @endif</td>
                                            <td >@if($rate->destination_address=='' && $rate->destination_port_id!='') {{$rate->destination_port->name}}, {{$rate->destination_port->code}} @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}@else {{$rate->destination_address}} @endif</td>                           
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <td {{ $hide }}>{{ @${'sum_freight_'.$c->code} }}</td>
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
                @endif