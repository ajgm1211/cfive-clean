        <!-- Freight charges all in -->
        @if($quote->pdf_option->show_type=='detailed' && $rates->count()>1)

            <div {{$quote->pdf_option->show_type=='detailed' ? '':'hidden'}}>

                <p class="title" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}}</b></p>

               

            </div>

            <table border="0" cellspacing="1" cellpadding="1" >

                <thead class="title-quote text-center header-table">

                    <tr >

                        <th class="unit"><b>{{__('pdf.pol')}}</b></th>

                        <th class="unit"><b>{{__('pdf.pod')}}</b></th>
                       
                        @if($quote->type=='LCL')
                        
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                       
                        @else
                       
                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.airline')}}</b></th>
                       
                        @endif
                       
                        @if($quote->pdf_option->replace_total_title==1)
                        
                            <th ><b>TON/M3</b></th>
                        
                        @else
                        
                            <th ><b>Total</b></th>
                       
                        @endif
                       
                        @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
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
                                    $total_freight = 0;
                                    $total_freight_units = 0;
                                    $total_freight_rates = 0;
                                    $total_freight_markups = 0; 
                                    foreach($item as $rate){
                                        foreach($rate->charge_lcl_air as $value){
                                            if($value->type_id==3){
                                                $total_freight+=$value->total_freight;
                                                $total_freight_units+=$value->units;
                                                $total_freight_rates+=$value->price_per_unit*$value->units;
                                                $total_freight_markups+=$value->markup;
                                            }
                                        }
                                    }
                                ?>
                                <tr class="text-center color-table">
                                    <td >{{$quote->type=='LCL' ? @$rate->origin_port->name.', '.@$rate->origin_port->code:@$rate->origin_airport->name.', '.@$rate->origin_airport->code}}</td>
                                    <td >{{$quote->type=='LCL' ? @$rate->destination_port->name.', '.@$rate->destination_port->code:@$rate->destination_airport->name.', '.@$rate->destination_airport->code}}</td>
                                    @if($quote->type=='LCL')
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                    @else
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                    @endif
                                    <td >{{@$total_freight}}</td>
                                    @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0) 
                                        <td>{{$rate->transit_time!='' ? $rate->transit_time:'-'}}</td>
                                        <td>{{$rate->via!='' ? $rate->via:'-'}}</td>
                                    @endif
                                    <td >{{$currency_cfg->alphacode}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
           
            </table>
        @endif

        <!-- Freigth charges detailed -->
        @if($quote->pdf_option->show_type=='detailed' && $rates->count()==1)
            @if($quote->pdf_option->grouped_freight_charges==0)
                @foreach($freight_charges_grouped as $origin => $value)
                    @foreach($value as $destination => $item)
                       
                        <div>
                        
                            <p class="title" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}} - {{$origin}} | {{$destination}}</b></p>
                        
                       
                        </div>
                       
                        <table border="0" cellspacing="1" cellpadding="1" >
                           
                            <thead class="title-quote text-center header-table">
                             
                                <tr >
                                
                                    <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                
                                    <th class="unit"><b>{{__('pdf.detail')}}</b></th>
                                
                                    @if($quote->type=='LCL')
                                
                                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                
                                    @else
                                
                                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.airline')}}</b></th>
                                
                                    @endif
                                
                                    <th class="unit"><b>{{__('pdf.units')}}</b></th>
                                 
                                    <th class="unit"><b>{{__('pdf.rate')}}</b></th>
                                 
                                    @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                
                                       <th class="unit"><b>{{__('pdf.tt')}}</b></th>
                                
                                        <th class="unit"><b>{{__('pdf.via')}}</b></th>
                                
                                    @endif
                                
                                    @if($quote->pdf_option->replace_total_title==1)
                                
                                        <th ><b>TON/M3</b></th>
                               
                                    @else
                                
                                        <th ><b>Total</b></th>
                               
                                  
                                    @endif
                                  
                                    <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                               
                                </tr>
                            
                            </thead>
                           
                            <tbody>

                                @foreach($item as $rate)
                                    <?php
                                        $total_freight= 0;
                                    ?>
                                    
                                    @forelse($rate as $r)
                                        @foreach($r->charge_lcl_air as $v)
                                            @if($v->type_id==3)
                                                <?php
                                                    $total_freight+=@$v->total_freight;
                                                ?>
                                                <tr class="text-center color-table">
                                                    @if($v->surcharge_id!='')
                                                        <td>{{$v->surcharge->name}}</td>
                                                    @else
                                                        <td>{{$quote->type=='LCL' ? 'Ocean Freight':'Freight'}}</td>
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
                                                        <td>{{$quote->type=='LCL' ? 'TON/M3':'CW'}}</td>
                                                    @endif
                                                    @if($quote->type=='LCL')
                                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                    @else
                                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->airline->name}}</td>
                                                    @endif
                                                    <td >{{$v->units}}</td>
                                                    <td >{{$v->rate}}</td>
                                                    @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0) 
                                                        <td>{{@$r->transit_time!='' ? @$r->transit_time:'-'}}</td>
                                                        <td>{{@$r->via!='' ? @$r->via:'-'}}</td>
                                                    @endif
                                                    <td >{{$v->units*$v->rate}}</td>
                                                    <td>{{$v->currency->alphacode}}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @empty

                                    @endforelse
                                @endforeach
                                
                                <tr>

                                    <td><b>{{__('pdf.total_local')}}</b></td>
                                    
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                   
                                    @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)
                                        <td></td>
                                        <td></td>
                                    
                                    @endif
                                   
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                   
                                    @if($quote->pdf_option->show_total_freight_in==1)
                                        @if($quote->pdf_option->show_total_freight_in_currency=='USD')
                                          
                                            <td ><b>{{isDecimal(@$total_freight/$currency_cfg->rates, true)}}</b></td>
                                        
                                        @else
                                           
                                            <td ><b>{{isDecimal(@$total_freight/$currency_cfg->rates_eur, true)}}</b></td>
                                       
                                        @endif
                                   
                                    @else
                                       
                                        <td ><b>{{isDecimal(@$total_freight, true)}}</b></td>
                                   
                                    @endif
                                   
                                    @if($quote->pdf_option->show_total_freight_in==1)
                                       
                                        <td >{{$quote->pdf_option->show_total_freight_in_currency}}</td>
                                   
                                    @else
                                       
                                        <td><b>{{@$r->currency->alphacode}}</b></td>
                                   
                                    @endif
                               
                                </tr>
                         
                            </tbody>
                  
                        </table>
                   
                    @endforeach
                @endforeach
            @else

                <div>

                    <p class="title" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}}</b></p>


                </div>

                <table border="0" cellspacing="1" cellpadding="1" >

                    <thead class="title-quote text-center header-table">

                        <tr >

                            <th class="unit"><b>{{__('pdf.pol')}}</b></th>

                            <th class="unit"><b>{{__('pdf.pod')}}</b></th>

                            @if($quote->type=='LCL')

                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>

                            @else

                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.airline')}}</b></th>

                            @endif

                            @if($quote->pdf_option->replace_total_title==1)

                                <th ><b>TON/M3</b></th>

                            @else

                                <th ><b>Total</b></th>

                            @endif

                            @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0)

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
                                    $total_freight = 0;
                                    $total_freight_units = 0;
                                    $total_freight_rates = 0;
                                    $total_freight_markups = 0;
                                  ?>  
                                    @foreach($item as $rate)
                                        @foreach($rate->charge_lcl_air as $value)
                                            <?php
                                                if($value->type_id==3){
                                                    if($quote->pdf_option->show_type=='charges'){
                                                        if($value->surcharge_id!=''){
                                                            $total_freight+=$value->total_freight;
                                                            $total_freight_units+=$value->units;
                                                            $total_freight_rates+=$value->price_per_unit*$value->units;
                                                            $total_freight_markups+=$value->markup;
                                                        }
                                                    }else{
                                                        $total_freight+=$value->total_freight;
                                                        $total_freight_units+=$value->units;
                                                        $total_freight_rates+=$value->price_per_unit*$value->units;
                                                        $total_freight_markups+=$value->markup;
                                                    }
                                                }
                                            ?>
                                        @endforeach
                                    @endforeach

                                    <tr class="text-center color-table">

                                        <td >{{$quote->type=='LCL' ? @$rate->origin_port->name.', '.@$rate->origin_port->code:@$rate->origin_airport->name.', '.@$rate->origin_airport->code}}</td>
                                        
                                        <td >{{$quote->type=='LCL' ? @$rate->destination_port->name.', '.@$rate->destination_port->code:@$rate->destination_airport->name.', '.@$rate->destination_airport->code}}</td>
                                        
                                        @if($quote->type=='LCL')
                                       
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                       
                                        @else
                                       
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                    
                                        @endif
                                       
                                        <td >{{@$total_freight}}</td>
                                      
                                        @if($quote->pdf_option->show_schedules==1 && $quote->pdf_option->grouped_total_currency==0) 
                                        
                                            <td>{{@$rate->transit_time!='' ? @$rate->transit_time:'-'}}</td>
                                      
                                            <td>{{@$rate->via!='' ? @$rate->via:'-'}}</td>
                                      
                                        @endif                                         
                                        
                                        @if($quote->pdf_option->grouped_freight_charges==1)
                                       
                                            <td>{{$quote->pdf_option->freight_charges_currency}}</td>
                                       
                                        @else
                                       
                                            <td>{{$currency_cfg->alphacode}}</td>
                                       
                                        @endif 

                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    </tbody>
                
                </table>                
            @endif
        @endif

        <br>