                <!-- Freight charges detailed -->
                

                        @foreach($freight_charges_detailed as $origin => $value)
                            @foreach($value as $destination => $item)
                                
                                <!-- Section Title -->
                                <div>
                                    
                                    <p class="title" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.freight_charges')}} - {{$origin}} | {{$destination}}</b></p>
                                    
                                    <br>

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
                                                            $amount = json_decode($v->amount);
                                                            $markup = json_decode($v->markups);
                                                            
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

                                                                <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}>{{@$r->carrier->name}}</td>

                                                                @foreach ($equipmentHides as $key=>$hide)
                                                                    @foreach ($containers as $c)
                                                                        @if($c->code == $key)

                                                                            <!--<td {{ $hide }}>{{isDecimal($v->${'total_sum_'.$c->code}, true)}}</td>-->
                                                                            <td {{ $hide }}>{{ @$v->${'sum_amount_markup_'.$c->code} == null ? 0 : @$v->${'sum_amount_markup_'.$c->code} . ' ' .$v->currency->alphacode}}</td>

                                                                        @endif
                                                                    @endforeach
                                                                @endforeach

                                                                @if($freight_charges->contains('transit_time', '!=', ''))

                                                                    <td>{{@$r->transit_time!='' ? @$r->transit_time:'-'}}</td>

                                                                    <td>{{@$r->via!='' ? @$r->via:'-'}}</td>

                                                                @endif

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

                                                                    <td {{@$quote->pdf_options['showCarrier'] ? '':'hidden'}}>{{@$r->carrier->name}}</td>

                                                                    @foreach ($equipmentHides as $key=>$hide)
                                                                        @foreach ($containers as $c)
                                                                            @if($c->code == $key)

                                                                                <td {{ $hide }}>{{ $v->${'total_sum_'.$c->code} .' '.$currency_cfg->alphacod }}</td>

                                                                            @endif
                                                                        @endforeach
                                                                    @endforeach

                                                                    @if($freight_charges->contains('transit_time', '!=', ''))

                                                                        <td>{{@$r->transit_time!='' ? @$r->transit_time:'-'}}</td>
                                                                        <td>{{@$r->via!='' ? @$r->via:'-'}}</td>

                                                                    @endif

                                                                </tr>

                                                            @endif
                                                        @endif
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
                                                        <td {{$hide}}><b>{{ @$total_freight->${'c'.$c->code} .' '. @$r->currency->alphacode }}</b></td>
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