@switch($quote->pdf_option->show_type)
@case('total in')
    <div {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
        <p class="title"><b>{{__('pdf.total_estimated')}}</b></p>
        <br>
    </div>
    @break

@case('detailed')
    <div {{$quote->pdf_option->grouped_total_currency==1 ? '':'hidden'}}>
        <p class="title">{{__('pdf.total_estimated')}}</p>
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
            <th class="unit"><b>{{__('pdf.pol')}}</b></th>
            <th class="unit"><b>{{__('pdf.pod')}}</b></th>
            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
            @foreach ($equipmentHides as $key=>$hide)
                @foreach ($containers as $c)
                    @if($c->code == $key)
                        <th {{$hide}}><b>{{$key}}</b></th>
                    @endif
                @endforeach
            @endforeach
            @if($quote->pdf_option->show_schedules==1)
                <th ><b>{{__('pdf.type')}}</b></th>
                <th ><b>{{__('pdf.tt')}}</b></th>
                <th ><b>{{__('pdf.via')}}</b></th>
            @endif
            <th class="unit"><b>{{__('pdf.currency')}}</b></th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($containers as $c){ 
                ${'sum_total_origin'.$c->code} = 0;
                ${'sum_total_destination'.$c->code} = 0;
                ${'sum_total_inland_origin'.$c->code} = 0;
                ${'sum_total_inland_destination'.$c->code} = 0;
                ${'sum_'.$c->code} = 'sum_'.$c->code;
            }
        ?>
        @foreach($rates as $rate)
            <?php 

                foreach ($containers as $c){ 
                    ${'total_'.$c->code} = 0;
                    ${'sum_total_'.$c->code} = 0;
                    ${'sum_inland_'.$c->code} = 0;
                    ${'sum_inland_origin'.$c->code} = 0;
                    ${'sum_inland_destination'.$c->code} = 0;
                    ${'total_c'.$c->code} = 'total_c'.$c->code;
                    ${'total_m'.$c->code} = 'total_m'.$c->code;
                }

                $array = array();
                
                foreach($rate->charge as $value){
                    array_push($array, $value->type_id);
                    if($quote->pdf_option->show_type=='charges'){
                        if($value->type_id!=3){
                            foreach ($containers as $c){
                                /*${'total_'.$c->code}=$value->${'total_c'.$c->code}+$value->${'total_m'.$c->code};
                                ${'sum_total_'.$c->code} += ${'total_'.$c->code};*/
                                if($sale_terms_origin->count()>0){
                                    if($value->type_id!=1){
                                        ${'total_'.$c->code}=$value->${'total_c'.$c->code}+$value->${'total_m'.$c->code};
                                        ${'sum_total_'.$c->code} += ${'totalized_'.$c->code};
                                    }
                                }elseif($sale_terms_destination->count()>0){
                                    if($value->type_id!=2){
                                        ${'total_'.$c->code}=$value->${'total_c'.$c->code}+$value->${'total_m'.$c->code};
                                        ${'sum_total_'.$c->code} += ${'totalized_'.$c->code};
                                    }
                                }
                            }
                        }
                    }else{
                        foreach ($containers as $c){ 
                            if($sale_terms_origin->count()>0){
                                if($value->type_id!=1){
                                    ${'total_'.$c->code}=$value->${'total_c'.$c->code}+$value->${'total_m'.$c->code};
                                    ${'sum_total_'.$c->code} += $value->{'totalized_'.$c->code};
                                }
                            }elseif($sale_terms_destination->count()>0){
                                if($value->type_id!=2){
                                    ${'total_'.$c->code}=$value->${'total_c'.$c->code}+$value->${'total_m'.$c->code};
                                    ${'sum_total_'.$c->code} += $value->{'totalized_'.$c->code};
                                }
                            }else{
                                ${'total_'.$c->code}=$value->${'total_c'.$c->code}+$value->${'total_m'.$c->code};
                                ${'sum_total_'.$c->code} += $value->{'totalized_'.$c->code};
                            }
                        }
                    }
                }

                if($sale_terms_destination->count()>0){
                    foreach($sale_terms_destination as $destination=>$v){
                        foreach($v as $values){
                            foreach($values->charge as $item){
                                foreach ($containers as $c){
                                    ${'sum_total_destination'.$c->code} += $item->${'sum_'.$c->code};
                                }
                            }
                        }
                    }
                }

                if($sale_terms_origin->count()>0){
                    foreach($sale_terms_origin as $origin=>$v){
                        foreach($v as $value){
                            foreach($value->charge as $item){
                                foreach ($containers as $c){
                                    ${'sum_total_origin'.$c->code} += $item->${'sum_'.$c->code};
                                }
                            }
                        }
                    }
                }
                
                if(!$rate->inland->isEmpty()){
                    foreach($rate->inland as $inland){
                        if($rate->inland->where('port_id', $inland->port_id)->count()==1){
                            if($inland->type == 'Origin' && !$sale_terms_origin->count()>0){
                                foreach ($containers as $c){
                                    ${'sum_inland_origin'.$c->code}=$inland->${'total_c'.$c->code}+$inland->${'total_m'.$c->code};
                                    ${'sum_total_inland_origin'.$c->code}+=${'sum_inland_origin'.$c->code};
                                }
                            }
                            if($inland->type == 'Destination'){
                                foreach ($containers as $c){
                                    ${'sum_inland_destination'.$c->code}=$inland->${'total_c'.$c->code}+$inland->${'total_m'.$c->code};
                                    ${'sum_total_inland_destination'.$c->code}+=${'sum_inland_destination'.$c->code};
                                }
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
                @foreach ($equipmentHides as $key=>$hide)
                    @foreach ($containers as $c)
                        @if($c->code == $key)
                            @if($sale_terms_origin->count()>0 && $sale_terms_destination->count()>0)
                                <td {{ $hide }}>{{isDecimal(@${'sum_total_'.$c->code}+@${'sum_total_origin'.$c->code}+@${'sum_total_destination'.$c->code}+@${'sum_total_inland_origin'.$c->code}+@${'sum_total_inland_destination'.$c->code})}}</td>
                            @elseif($sale_terms_origin->count()>0 && $sale_terms_destination->count()==0)
                                <td {{ $hide }}>{{isDecimal(@${'sum_total_'.$c->code}+@${'sum_total_origin'.$c->code}+@${'sum_total_inland_origin'.$c->code}+@${'sum_total_inland_destination'.$c->code})}}</td>
                            @elseif($sale_terms_origin->count()==0 && $sale_terms_destination->count()>0)
                                <td {{ $hide }}>{{isDecimal(@${'sum_total_'.$c->code}+@${'sum_total_destination'.$c->code}+@${'sum_total_inland_origin'.$c->code}+@${'sum_total_inland_destination'.$c->code})}}</td>
                            @elseif($sale_terms_origin->count()==0 && $sale_terms_destination->count()==0)
                                <td {{ $hide }}>{{isDecimal(@${'sum_total_'.$c->code}+@${'sum_total_inland_origin'.$c->code}+@${'sum_total_inland_destination'.$c->code})}}</td>
                            @endif
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