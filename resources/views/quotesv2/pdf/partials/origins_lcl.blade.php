        <!-- ALL in origin table -->
        @if($quote->pdf_option->grouped_origin_charges==1 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            @forelse($origin_charges_grouped as $origin=>$detail)
            <br>
                <div>
                    <p class="title">{{__('pdf.origin_charges')}} - {{$origin}}</p>
                    <br>
                </div>
                <table border="0" cellspacing="1" cellpadding="1" >
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                            @if($quote->type=='LCL')
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                            @else
                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.airline')}}</b></th>
                            @endif
                            <th ><b>Total</b></th>
                            <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $total_sale_terms_origin = 0;
                    ?>
                    @if($sale_terms_origin->count()>0)
                        @foreach($sale_terms_origin as $v)
                            @foreach($v as $value)
                                @foreach($value->charge as $item)
                                @php
                                    $total_sale_terms_origin += $item->total_sale_origin;
                                @endphp
                                @endforeach
                            @endforeach
                        @endforeach
                    @endif
                    @foreach($detail as $v)
                        @foreach($v as $item)
                            <?php
                                $total_origin = 0;
                                $total_origin_units = 0;
                                $total_origin_rates = 0;
                                $total_origin_markups = 0;
                            ?>  
                            @foreach($item as $rate)

                                @foreach($rate->charge_lcl_air as $value)
                                    <?php
                                        if($value->type_id==1){
                                            $total_origin+=$value->total_origin;
                                            $total_origin_units+=$value->units;
                                            $total_origin_rates+=$value->price_per_unit*$value->units;
                                            $total_origin_markups+=$value->markup;
                                        }                            
                                    ?>
                                @endforeach
                            @endforeach
                            <tr class="text-center color-table">
                                <td colspan="">Total Origin Charges</td>
                                @if($quote->type=='LCL')
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                @else
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->airline->name}}</td>
                                @endif
                                <td >{{@$total_origin+@$total_sale_terms_origin}}</td>
                                <td >{{$quote->pdf_option->origin_charges_currency!="" ? $quote->pdf_option->origin_charges_currency:$currency_cfg->alphacode}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                @empty
                    @if($sale_terms_origin->count()>0)
                        @foreach($sale_terms_origin as $origin=>$sale_origin)
                            @foreach($sale_origin as $value)
                                <div>
                                    <p class="title">{{__('pdf.origin_charges')}} - {{$origin}}</p>
                                    <br>
                                </div>

                                <table border="0" cellspacing="1" cellpadding="1" >
                                    <thead class="title-quote text-left header-table">
                                        <tr >
                                            <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                            @if($quote->type=='LCL')
                                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                            @else
                                                <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.airline')}}</b></th>
                                            @endif
                                            <th ><b>Total</b></th>
                                            <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_origin=0;
                                        @endphp
                                        @foreach($value->charge as $item)
                                            @php
                                                $total_origin += $item->total_sale_origin;
                                            @endphp
                                        @endforeach
                                            <tr class="text-left color-table">
                                                <td>{{__('pdf.total_origin')}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                                <td >{{number_format(@$total_origin, 2, '.', '')}}</td>
                                                @if($quote->pdf_option->grouped_origin_charges==1)
                                                    <td >{{$quote->pdf_option->origin_charges_currency}}</td>
                                                @else
                                                    <td >{{$currency_cfg->alphacode}}</td>
                                                @endif
                                            </tr>
                                    </tbody>
                                </table>
                            @endforeach
                        @endforeach
                    @endif
                @endforelse
            @endif
        
        @if($quote->pdf_option->grouped_origin_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            @if($sale_terms_origin->count()>0)
                @foreach($sale_terms_origin as $origin=>$sale_origin)
                @foreach($sale_origin as $value)
                    <div>
                        <p class="title">{{__('pdf.origin_charges')}} - {{$origin}}</p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1">
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
                                <th ><b>Total</b></th>
                                <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($value->charge as $rate)
                            <?php
                                @$total_origin += @$rate->total_sale_origin;
                            ?>
                            <tr class="text-center color-table">
                                <td>{{$rate->charge}}</td>
                                <td>{{$rate->detail}}</td>
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                <td >{{$rate->units}}</td>
                                <td >{{$rate->rate}}</td>
                                <td >{{$rate->total}}</td>
                                <td>{{$rate->currency->alphacode}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endforeach
                @endforeach
            @endif
        @endif

        <!-- Origins detailed -->
        @if($quote->pdf_option->grouped_origin_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
            <br>
            @foreach($origin_charges_grouped as $origin => $value)
                @foreach($value as $carrier => $item)
                    <div>
                        <p class="title">{{__('pdf.origin_charges')}} - {{$origin}}</p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1">
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                <th class="unit"><b>{{__('pdf.detail')}}</b></th>
                                @if($quote->type=='LCL')
                                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                @else
                                    <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.airline')}} @endif</b></th>
                                @endif
                                <th class="unit"><b>{{__('pdf.units')}}</b></th>
                                <th class="unit"><b>{{__('pdf.rate')}}</b></th>
                                <th ><b>Total</b></th>
                                <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $total_origin = 0;
                            $total_inland_origin = 0;
                            $show_inland = 'hide';
                        ?>
                        @foreach($item as $rate)
                            @foreach($rate as $r)
                                @foreach($r->charge_lcl_air as $v)
                                    @if($v->type_id==1)
                                        <?php
                                            @$total_origin+=@$v->total_origin;
                                        ?>
                                        <tr class="text-center color-table">
                                            <td>{{$v->surcharge->name}}</td>
                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                            @if($quote->type=='LCL')
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                            @else
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->airline->name}}</td>
                                            @endif
                                            <td >{{$v->units}}</td>
                                            <td >{{$v->rate}}</td>
                                            <td >{{$v->units*$v->rate}}</td>
                                            <td>{{$v->currency->alphacode}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$r->automaticInlandLclAir->isEmpty()){
                                    @php
                                        $total_inland_origin=0;
                                    @endphp
                                    @foreach($r->automaticInlandLclAir as $v)
                                        @if($v->type=='Origin')
                                            @if($r->automaticInlandLclAir->where('type', 'Origin')->count()==1)
                                                <?php
                                                    $total_inland_origin+=$v->total_inland_origin;
                                                ?>
                                                <tr class="text-center color-table">
                                                    <td>{{$v->provider}}</td>
                                                    <td>-</td>
                                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                                    <td >{{$v->units}}</td>
                                                    <td >{{$v->rate_amount}}</td>
                                                    <td >{{$v->units*$v->rate_amount}}</td>
                                                    <td>{{$v->currency->alphacode}}</td>
                                                </tr>
                                            @else
                                                <?php
                                                    $show_inland = '';
                                                ?>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif                            
                            @endforeach
                        @endforeach
                        <tr>
                            <td><b>{{__('pdf.total_local')}}</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                            <td ><b>{{number_format(@$total_origin+@$total_inland_origin, 2, '.', '')}}</b></td>
                            @if($quote->pdf_option->grouped_origin_charges==1)
                                <td><b>{{$quote->pdf_option->origin_charges_currency}}</b></td>
                            @else
                                <td><b>{{$currency_cfg->alphacode}}</b></td>
                            @endif     
                        </tr>
                        <tr class="{{$show_inland}}" style="background-color: white !important;">
                            <td  style="background-color: white !important;" colspan="2">
                                <br>
                                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{__('pdf.origin_inland')}} - {{$origin}}</p>
                            </td>
                        </tr>
                        @if(!$r->automaticInlandLclAir->isEmpty()){
                            @php
                                $total_inland=0;
                            @endphp
                            @foreach($r->automaticInlandLclAir as $v)
                                @if($v->type=='Origin')
                                    @if($r->automaticInlandLclAir->where('type', 'Origin')->count()>1)
                                        <?php
                                            //if($r->automaticInlandLclAir->where('type', 'Destination')->count()==1){
                                                $total_inland+=@$v->total_inland_origin;
                                            //}
                                        ?>
                                        <tr class="text-center color-table">
                                            <td>{{$v->provider}}</td>
                                            <td>-</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>--</td>
                                            <td >{{$v->units}}</td>
                                            <td >{{$v->rate_amount}}</td>
                                            <td >{{$v->units*$v->rate_amount}}</td>
                                            <td>{{$v->currency->alphacode}}</td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
                @endforeach
            @endforeach
        @endif