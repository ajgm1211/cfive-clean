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
                            <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                            <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                            <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                            <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                            <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
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
                                $array = array();
                                foreach($rate->charge as $value){
                                    array_push($array, $value->type_id);
                                    if($quote->pdf_option->show_type=='charges'){
                                        if($value->type_id!=3){
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
                                        }
                                    }else{
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
                                    }
                                }
                                /*if(!in_array(1, $array)){
                                    foreach($sale_terms_origin_grouped as $sale_origin){
                                        foreach($sale_origin->charge as $v){
                                            $sum_total20 += $v->sum20;
                                            $sum_total40 += $v->sum40;
                                            $sum_total40hc += $v->sum40hc;
                                            $sum_total40nor += $v->sum40nor;
                                            $sum_total45 += $v->sum45;   
                                        }
                                    }
                                }
                                if(!in_array(2, $array)){
                                    foreach($sale_terms_destination_grouped as $sale_destination){
                                        foreach($sale_destination->charge as $v){
                                            $sum_total20 += $v->sum20;
                                            $sum_total40 += $v->sum40;
                                            $sum_total40hc += $v->sum40hc;
                                            $sum_total40nor += $v->sum40nor;
                                            $sum_total45 += $v->sum45;
                                        }
                                    }
                                }*/
                                if(!$rate->inland->isEmpty()){
                                    foreach($rate->inland as $inland){
                                        if($rate->inland->where('port_id', $inland->port_id)->count()==1){
                                            $sum_i20=$inland->total_20+$inland->total_m20;
                                            $sum_i40=$inland->total_40+$inland->total_m40;
                                            $sum_i40hc=$inland->total_40hc+$inland->total_m40hc;
                                            $sum_i40nor=$inland->total_40nor+$inland->total_m40nor;
                                            $sum_i45=$inland->total_45+$inland->total_m45;
                                            
                                            $sum_inland20+=$sum_i20;
                                            $sum_inland40+=$sum_i40;
                                            $sum_inland40hc+=$sum_i40hc;
                                            $sum_inland40nor+=$sum_i40nor;
                                            $sum_inland45+=$sum_i45;
                                        }
                                    }
                                }
                            ?>
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