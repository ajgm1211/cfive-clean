                    <!-- Section Title -->
                    <div>
                                    
                        <p class="title" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}}</b></p>
                        
                        <br>

                    </div>
                    
                    <!-- End Section Title -->
                    <table border="0" cellspacing="1" cellpadding="1">
                        <thead class="title-quote text-left header-table">
                            <tr >
                                <th class="unit"><b>{{__('pdf.pol')}}</b></th>
                                <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                                <th class="unit" {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                @foreach ($equipmentHides as $key=>$hide)
                                    @foreach ($containers as $c)
                                        @if($c->code == $key)
                                            <th class="unit" {{$hide}}><b>{{$key}}</b></th>
                                        @endif
                                    @endforeach
                                @endforeach
                                @if($freight_charges->contains('transit_time', '!=', ''))
                                    <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                                    <th class="unit"><b>{{__('pdf.via')}}</b></th>                                
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                foreach ($containers as $c){
                                    ${'freight_'.$c->code} = 'freight_'.$c->code;
                                    ${'inland_freight_'.$c->code} = 'inland_freight_'.$c->code;
                                    ${'total_sum_'.$c->code} = 'total_sum_'.$c->code;
                                    ${'c'.$c->code} = 'c'.$c->code;
                                }
                            @endphp
                            @foreach($freight_charges as $rate)
                                <?php
                                    if(!is_array($rate->total)){
                                        $total = json_decode($rate->total);
                                    }else{
                                        $total = $rate->total;
                                    }
                                    
                                    foreach ($containers as $c){
                                        ${'freight_'.$c->code} = 0;
                                        ${'inland_freight_'.$c->code} = 0;
                                    }
                                                        
                                    foreach($rate->charge as $value){
                                        foreach ($containers as $c){
                                            ${'freight_'.$c->code}+=$value->${'total_sum_'.$c->code};
                                        }
                                    }
                                ?>
                                <tr class="text-left color-table">
                                    <td >@if($rate->origin_address=='' && $rate->origin_port_id!='') {{$rate->origin_port->name}}, {{$rate->origin_port->code}} @elseif($rate->origin_address=='' && $rate->origin_airport_id!='') {{$rate->origin_airport->name}}, {{$rate->origin_airport->code}} @else  {{$rate->origin_address}} @endif</td>
                                    <td >@if($rate->destination_address=='' && $rate->destination_port_id!='') {{$rate->destination_port->name}}, {{$rate->destination_port->code}} @elseif($rate->destination_address=='' && $rate->destination_airport_id!='') {{$rate->destination_airport->name}}, {{$rate->destination_airport->code}}@else {{$rate->destination_address}} @endif</td>
                                    <td {{@$quote->pdf_options['showCarrier']==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                    @foreach ($equipmentHides as $key=>$hide)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)
                                                <td {{$hide}}>{{ @$total->${'c'.$c->code} }}&nbsp;{{@$rate->currency->alphacode}}</td>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    @if($freight_charges->contains('transit_time', '!=', ''))
                                        <td>{{@$rate->transit_time!='' ? @$rate->transit_time:'-'}}</td>
                                        <td>{{@$rate->via!='' ? @$rate->via:'-'}}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>