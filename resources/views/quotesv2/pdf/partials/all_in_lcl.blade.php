         @if($quote->pdf_option->show_type=='total in')
            <div {{$quote->pdf_option->show_type=='total in' ? '':'hidden'}}>
                <p class="title">{{__('pdf.total_estimated')}}</p>
                <br>
            </div>
        @else
            <div {{$quote->pdf_option->grouped_total_currency==1 ? '':'hidden'}}>
                <p class="title">{{__('pdf.total_estimated')}}</p>
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
                    <th class="unit"><b>{{__('pdf.pol')}}</b></th>
                    <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                    @if($quote->type=='LCL')
                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                    @else
                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.airline')}}</b></th>
                    @endif
                    <th ><b>W/M</b></th>
                    <th ><b>Total</b></th>
                    @if($quote->pdf_option->show_schedules==1)
                        <th ><b>{{__('pdf.type')}}</b></th>
                        <th ><b>TT</b></th>
                        <th ><b>Via</b></th>
                    @endif
                    <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $total_freight= 0;
                    $total_origin= 0;
                    $total_destination= 0;
                    $total_inland= 0;
                    $total_chargeable= 0;
                    $array = array();
                ?>
                @foreach($rates as $rate)
                    <?php 
                        foreach($rate->charge_lcl_air as $value){
                            array_push($array, $value->type_id);
                            if($quote->pdf_option->show_type!='charges'){
                                $total_freight+=$value->total_freight;
                            }
                            $total_origin+=$value->total_origin;
                            $total_destination+=$value->total_destination;
                        }
                        foreach($rate->automaticInlandLclAir as $inland){
                            $total_inland+=$inland->total_inland;
                        }

                        $total_chargeable = (@$total_freight+@$total_origin+@$total_destination)/$quote->chargeable_weight;

                        if($total_chargeable>=1){
                            $total_chargeable = round($total_chargeable);
                        }else{
                            $total_chargeable = number_format($total_chargeable, 2, '.', '');
                        }

                        $total_sum_inland = @$total_freight+@$total_origin+@$total_destination+@$total_inland;

                        if($total_sum_inland>=1){
                            $total_sum_inland = round($total_sum_inland);
                        }else{
                            $total_sum_inland = number_format($total_sum_inland, 2, '.', '');
                        }
                                        
                    ?>
                    <tr class="text-center color-table"> 
                        <td >
                            @if($quote->type=='LCL') {{@$rate->origin_port->name}}, {{@$rate->origin_port->code}} @else {{@$rate->origin_airport->name}}, {{@$rate->origin_airport->code}} @endif
                        </td>
                        <td >
                            @if($quote->type=='LCL') {{$rate->destination_port->name}}, {{$rate->destination_port->code}} @else {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}} @endif
                        </td> 
                        @if($quote->type=='LCL') <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td> @else <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td> @endif
                        <td >{{$total_chargeable}}</td>
                        <td >{{$total_sum_inland}}</td>
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