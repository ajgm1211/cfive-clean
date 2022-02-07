        <!-- Freigth charges detailed -->
                @foreach($freight_charges_detailed as $origin => $value)
                    @foreach($value as $destination => $item)
                       
                        <div>
                        
                            <p class="title" style="margin-bottom: 0px; color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}} - {{$origin}} | {{$destination}}</b></p>
                        
                            
                       
                        </div>
                       
                        <table border="0" cellspacing="1" cellpadding="1" >
                           
                            <thead class="title-quote text-center header-table">
                             
                                <tr >
                                
                                    <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                
                                    <th class="unit"><b>{{__('pdf.detail')}}</b></th>
                                
                                    <th class="unit" {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                
                                    <th class="unit"><b>{{__('pdf.units')}}</b></th>

                                    <th class="unit"><b>{{__('pdf.minimum')}}</b></th>
                                 
                                    <th class="unit"><b>TON/M3</b></th>
                                
                                    <th ><b>{{__('pdf.total')}}</b></th>

                                    @if($service)
                                
                                        <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                                
                                        <th class="unit"><b>{{__('pdf.via')}}</b></th>
                             
                                    @endif
                               
                                </tr>
                            
                            </thead>
                           
                        <tbody>

                            @foreach($item as $rate)
                                <?php
                                    $total_freight= 0;                                   
                                ?>
                                    
                                @foreach($rate as $r)
                                    @foreach($r->charge_lcl_air as $v)
                                        @if($v->type_id==3)
                                            <?php
                                                $total_freight+=@$v->total_freight;

                                                if($v->surcharge_id==38){
                                                    $profit=$r->markups['per_unit']*$v->units;

                                                    $total_w_profit = $v->surcharge_id != '' ? $v->rate+$profit:$v->rate+$r->total_rate->markups['total'];
                                                    $price_w_profit = $v->surcharge_id != '' ? $v->price_per_unit+$r->markups['per_unit']:$total_w_profit/$v->units;
                                                }else{
                                                    $total_w_profit = $v->surcharge_id != '' ? $v->rate:$v->rate+$r->total_rate->markups['total'];
                                                    $price_w_profit = $v->surcharge_id != '' ? $v->price_per_unit:$total_w_profit/$v->units;
                                                }
                                            ?>
                                            <tr class="text-center color-table">
                                                <td>{{$v->surcharge->name ?? 'Ocean Freight'}}</td>
                                                <td>{{$v->surcharge_id!='' ? @$v->calculation_type->name:'TON/M3'}}</td>
                                                <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                <td >{{$v->units}}</td>
                                                <td >{{$v->minimum}}</td>
                                                <td >{{isDecimal($price_w_profit,false,true)}}</td>
                                                <td >{{isDecimal($total_w_profit,false,true).' '.$v->currency->alphacode}}</td>
                                                @if($service)
                                                    <td>{{@$r->transit_time!='' ? @$r->transit_time:'-'}}</td>
                                                    <td>{{@$r->via!='' ? @$r->via:'-'}}</td>
                                                @endif
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endforeach
                         
                        </tbody>
                  
                    </table>
                   
                @endforeach
            @endforeach
        <br>