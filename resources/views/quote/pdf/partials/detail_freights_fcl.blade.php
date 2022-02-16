            <!-- Freight charges detailed -->
            @foreach($freight_charges_detailed as $origin => $value)
                @foreach($value as $destination => $item)
                    
                    <!-- Section Title -->
                    <div>
                        
                        <p class="title" style="margin-bottom: 0px; color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}} - {{$origin}} | {{$destination}}</b></p>
                        
                        

                    </div>
                    <!-- End Section Title -->

                    <!-- Table -->
                    <table border="0" cellspacing="1" cellpadding="1">

                        <!-- Table Header -->
                        <thead class="title-quote text-left header-table" >

                            <tr>

                                <th class="unit"><b>{{__('pdf.charge')}}</b></th>

                                <th class="unit"><b>{{__('pdf.detail')}}</b></th>
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
                        <!-- End Table Header -->

                        <!-- Table Body -->
                        <tbody>
                            
                            @foreach($item as $rate)
                                <?php
                                    
                                    foreach ($containers as $c){
                                        ${'sum_freight_'.$c->code} = 0;
                                        ${'c'.$c->code} = 'c'.$c->code;
                                    }
                                ?>
                                @forelse($rate as $r)
                                    <?php
                                        $total_freight = json_decode($r->total);
                                    ?>
                                    @foreach($r->charge as $v)
                                        @if($v->type_id==3)
                                            <?php
                                                $amount = json_decode($v->amount, true);
                                                $markup = json_decode($v->markups);
                                                
                                                foreach ($containers as $c){
                                                    ${'total_freight_'.$c->code} = 0;
                                                    ${'total_sum_'.$c->code} = 'total_sum_'.$c->code;
                                                    ${'sum_amount_markup_'.$c->code} = 'sum_amount_markup_'.$c->code;
                                                }
                                            
                                                foreach ($containers as $c){
                                                    ${'sum_freight_'.$c->code}+=$v->${'total_sum_'.$c->code};
                                                }
                                            ?>

                                            <tr class="text-left color-table">
                                            
                                                <td>{{$v->surcharge_id!='' ? $v->surcharge->name:__('pdf.ocean_freight')}}</td>

                                                <td>{{$v->surcharge_id!='' ? @$v->calculation_type->name:__('pdf.per_container')}}</td>

                                                <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}>{{@$r->carrier->name}}</td>

                                                @foreach ($equipmentHides as $key=>$hide)
                                                    @foreach ($containers as $c)
                                                        @if($c->code == $key)
                                                            <?php
                                                                if($v->surcharge->name == "Ocean Freight" && $v->surcharge->company_user_id==null){
                                                                    //$total = $v->surcharge_id != '' ? $v->${'sum_amount_markup_'.$c->code}:$v->${'sum_amount_markup_'.$c->code}+@$r->total_rate->markups['m'.$c->code];
                                                                    //foreach($r->total_rate['markups'] as $m=>$markups ){
                                                                        //$containerM=str_replace("m", "", $m);
                                                                        //if ($containerM==$c->code) {
                                                                        if(isset($amount["c". $c->code]) && isset($r->total_rate->markups["m" . $c->code])){
                                                                            $total_w_profit = $amount["c". $c->code] + $r->total_rate->markups["m" . $c->code];
                                                                        }elseif(isset($amount["c". $c->code])){
                                                                            $total_w_profit = $amount["c". $c->code];
                                                                        }
                                                                        //}
                                                                    //}
                                                                }else{
                                                                    if(isset($amount["c". $c->code])){
                                                                        $total_w_profit = $amount["c". $c->code];
                                                                    }
                                                                }
                                                            ?>
                                                            <!--<td {{ $hide }}>{{isDecimal($v->${'total_sum_'.$c->code}, true)}}</td>-->
                                                            <td {{ $hide }}>{{ @$total_w_profit == null ? 0 : isDecimal( @$total_w_profit, false, true) . ' ' .$v->currency->alphacode}}</td>

                                                        @endif
                                                    @endforeach
                                                @endforeach

                                                @if($freight_charges->contains('transit_time', '!=', ''))

                                                    <td>{{@$r->transit_time!='' ? @$r->transit_time:'-'}}</td>

                                                    <td>{{@$r->via!='' ? @$r->via:'-'}}</td>

                                                @endif

                                            </tr>
                                        @endif
                                    @endforeach                                
                                @empty

                                @endforelse
                            @endforeach

                            <tr>

                                <td><b>{{__('pdf.total')}}</b></td>

                                <td></td>

                                <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}></td>
                                
                                @foreach ($equipmentHides as $key=>$hide)
                                    @foreach ($containers as $c)
                                        @if($c->code == $key)
                                            <td {{$hide}}><b>{{ isDecimal(@$total_freight->${'c'.$c->code}, false, true) .' '. @$r->currency->alphacode }}</b></td>
                                        @endif
                                    @endforeach
                                @endforeach

                                @if($freight_charges->contains('transit_time', '!=', ''))

                                    <td></td>
                                    <td></td>

                                @endif

                            </tr>

                        </tbody>
                        <!-- End Table Body -->

                    </table>
                    <!-- End Table -->

                @endforeach
            @endforeach
            <br>