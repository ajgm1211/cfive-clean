                @if(@$quote->pdf_options['selectPDF']['id'] ==2 || @$quote->pdf_options['selectPDF']['id'] ==3 )
                    <div>
                        <p class="title" style="margin-bottom: 0px; color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}}</b></p>
                    </div>
                    
                    <!-- HEADER FREIGHTS GROUPED TABLE -->
                    <table border="0" cellspacing="1" cellpadding="1">
                        <thead class="title-quote text-left header-table">
                            <tr >
                                <th class="unit" style="{{isset($freight_charges->hasOriginAddress) && $freight_charges->hasOriginAddress == 1 ? '':'display:none;'}}">
                                    <b>{{__('pdf.origin')}}</b>
                                </th>
                                <th class="unit"><b>{{__('pdf.pol')}}</b></th>
                                <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                                <th class="unit" style="{{isset($freight_charges->hasDestinationAddress) && $freight_charges->hasDestinationAddress == 1 ? '':'display:none;'}}">
                                    <b>{{__('pdf.destination')}}</b>
                                </th>
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
                                //Setting dynamic variables
                                foreach ($containers as $c){
                                    ${'freight_'.$c->code} = 'freight_'.$c->code;
                                    ${'inland_freight_'.$c->code} = 'inland_freight_'.$c->code;
                                    ${'total_sum_'.$c->code} = 'total_sum_'.$c->code;
                                    ${'c'.$c->code} = 'c'.$c->code;
                                }
                            @endphp
                            @foreach($freight_charges as $rate)
                                <?php
                                    //Working on rate values
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
                                <!-- BODY GROUPED FREIGHTS -->
                                <tr class="text-left color-table">
                                    <td style="{{isset($freight_charges->hasOriginAddress) && $freight_charges->hasOriginAddress == 1 ? '':'display:none;'}}">
                                        {{$rate->origin_address ?? "--" }}
                                    </td>
                                    <td >{{$rate->origin_port->display_name}}</td>
                                    <td >{{$rate->destination_port->display_name}}</td>
                                    <td style="{{isset($freight_charges->hasDestinationAddress) && $freight_charges->hasDestinationAddress == 1 ? '':'display:none;'}}">
                                        {{$rate->destination_address ?? "--" }}
                                    </td>
                                    <td {{@$quote->pdf_options['showCarrier']==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                    @foreach ($equipmentHides as $key=>$hide)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)
                                                <td {{$hide}}>{{ isDecimal(@$total->${'c'.$c->code}, false, true) }}&nbsp;{{@$rate->currency->alphacode}}</td>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    @if($freight_charges->contains('transit_time', '!=', ''))
                                        <td>{{@$rate->transit_time!='' ? @$rate->transit_time:'-'}}</td>
                                        <td>{{@$rate->via!='' ? @$rate->via:'Direct'}}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>
                @endif