                <!-- SALE TERMS ORIGIN -->
                @if($quote->pdf_option->grouped_origin_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                    @if($sale_terms_origin->count()>0)
                        @foreach($sale_terms_origin as $origin=>$v)
                            @foreach($v as $value)
                                <div>
                                    <p class="title">{{__('pdf.origin_charges')}} - {{$origin}}</p>
                                    <br>
                                </div>

                                <table border="0" cellspacing="1" cellpadding="1" >
                                    <thead class="title-quote text-left header-table">
                                        <tr >
                                            <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                            <th class="unit"><b>{{__('pdf.detail')}}</b></th>
                                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <th class="unit" {{$hide}}><b>{{$key}}</b></th>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            foreach ($containers as $c){
                                                ${'sum_total_'.$c->code}=0;
                                                ${'sum_'.$c->code}='sum_'.$c->code;
                                            }
                                        @endphp
                                        @foreach($value->charge as $item)
                                            @php
                                                foreach ($containers as $c){
                                                    ${'sum_total_'.$c->code} += $item->${'sum_'.$c->code};
                                                }
                                            @endphp
                                            <tr class="text-left color-table">
                                                <td >{{$item->charge!='' ? $item->charge:'-'}}</td>
                                                <td >{{$item->detail!='' ? $item->detail:'-'}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                                @foreach ($equipmentHides as $key=>$hide)
                                                    @foreach ($containers as $c)
                                                        @if($c->code == $key)
                                                            <td {{ $hide }}>{{round(@$item->${'sum_'.$c->code})}}</td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                <td >{{@$item->currency->alphacode}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td><b>{{__('pdf.total_origin')}}</b></td>
                                            <td></td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <td {{ $hide }}><b>{{round(@${'sum_total_'.$c->code})}}</b></td>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            <td><b>{{$currency_cfg->alphacode}}</b></td>                    
                                        </tr>
                                    </tbody>
                                </table>
                            @endforeach
                        @endforeach
                    @endif               
                @endif              
                

                <!-- ALL in origin table -->
                @if($quote->pdf_option->grouped_origin_charges==1 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                    @forelse($origin_charges_grouped as $origin=>$detail)
                        @if($detail->charge_origin>=1 || $detail->inland_origin>=1)
                            <div>
                                <p class="title">{{__('pdf.origin_charges')}} - {{$origin}}</p>
                                <br>
                            </div>
                            
                            <table border="0" cellspacing="1" cellpadding="1" >
                                <thead class="title-quote text-left header-table">
                                    <tr >
                                        <th class="unit" colspan="2"><b>{{__('pdf.charge')}}</b></th>
                                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                        @foreach ($equipmentHides as $key=>$hide)
                                            @foreach ($containers as $c)
                                                @if($c->code == $key)
                                                    <th {{ $hide }}><b>{{ $key }}</b></th>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        foreach ($containers as $c) {
                                            ${'sum_sale'.$c->code} = 0;
                                            ${'total_'.$c->code} = 'total_'.$c->code;
                                        }
                                    @endphp
                                    @if($sale_terms_origin->count()>0)
                                        @foreach($sale_terms_origin as $v)
                                            @foreach($v as $value)
                                                @foreach($value->charge as $item)
                                                    @foreach ($containers as $c)
                                                        @php
                                                            ${'sum_sale'.$c->code} += $item->${'total_'.$c->code};
                                                        @endphp
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endif
                                    @foreach($detail as $item)
                                        <?php
                                            foreach ($containers as $c) {
                                                ${'origin_'.$c->code} = 0;
                                                ${'origin_inland_'.$c->code} = 0;
                                                ${'sum_total_'.$c->code} = 'sum_total_'.$c->code;
                                                ${'total_inland'.$c->code} = 0;
                                            }
                                        ?>
                                        @foreach($item as $rate)
                                            @foreach($rate->charge as $value)
                                                <?php
                                                    foreach ($containers as $c) {
                                                        ${'origin_'.$c->code}+=$value->${'sum_total_'.$c->code};
                                                    }                      
                                                ?>
                                            @endforeach
                                            @foreach($rate->inland as $value)
                                                @if($value->type=='Origin')
                                                    <?php
                                                        foreach ($containers as $c) {
                                                            ${'origin_inland_'.$c->code}+=$value->${'sum_total_'.$c->code};
                                                        }                              
                                                    ?>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        <tr class="text-left color-table">
                                            <td colspan="2">{{__('pdf.total_origin')}}</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier_id!='' ? $rate->carrier->name:'-'}}</td>
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <td {{ $hide }}>{{ @${'origin_'.$c->code}+@${'origin_inland_'.$c->code}+@${'sum_sale'.$c->code} }}</td>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            @if($quote->pdf_option->grouped_origin_charges==1)
                                                <td >{{$quote->pdf_option->origin_charges_currency}}</td>
                                            @else
                                                <td >{{$currency_cfg->alphacode}}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    @empty
                        @if($sale_terms_origin->count()>0)
                            @foreach($sale_terms_origin as $origin=>$v)
                                @foreach($v as $value)
                                <div>
                                    <p class="title">{{__('pdf.origin_charges')}} - {{$origin}}</p>
                                    <br>
                                </div>

                                <table border="0" cellspacing="1" cellpadding="1" >
                                    <thead class="title-quote text-left header-table">
                                        <tr >
                                            <th class="unit" colspan="2"><b>{{__('pdf.charge')}}</b></th>
                                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                            @foreach ($equipmentHides as $key=>$hide)
                                                @foreach ($containers as $c)
                                                    @if($c->code == $key)
                                                        <th {{ $hide }}><b>{{ $key }}</b></th>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @foreach ($containers as $c)
                                            @php
                                                ${'sum_sale'.$c->code} = 0;
                                                ${'sum_'.$c->code} = 'sum_'.$c->code;
                                            @endphp
                                        @endforeach
                                        
                                        @foreach($value->charge as $item)
                                            @php
                                                foreach ($containers as $c) {
                                                    ${'sum_sale'.$c->code} += $item->${'sum_'.$c->code};
                                                }
                                            @endphp
                                        @endforeach
                                            <tr class="text-left color-table">
                                                <td colspan="2">{{__('pdf.total_origin')}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                                @foreach ($equipmentHides as $key=>$hide)
                                                    @foreach ($containers as $c)
                                                        @if($c->code == $key)
                                                            <td {{ $hide }}>{{round(@${'sum_sale'.$c->code})}}</td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
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
                    <br>
                @endif
                
                <!-- Origins detailed -->
                @if($quote->pdf_option->grouped_origin_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                    @foreach($origin_charges_detailed as $carrier => $value)
                
                    @if($value->charge_origin>=1 || $value->inland_origin>=1)
                        @foreach($value as $origin => $item)
                            <div>
                                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{__('pdf.origin_charges')}} - {{$origin}}</p>
                                <br>
                            </div>
                            <table border="0" cellspacing="1" cellpadding="1">
                                <thead class="title-quote text-left header-table">
                                    <tr >
                                        <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                        <th class="unit"><b>{{__('pdf.detail')}}</b></th>
                                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></th>
                                        @foreach ($equipmentHides as $key=>$hide)
                                            @foreach ($containers as $c)
                                                @if($c->code == $key)
                                                    <th {{ $hide }}><b>{{ $key }}</b></th>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        <th class="unit"><b>{{__('pdf.currency')}}</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($item as $rate)
                                        <?php
                                            foreach($containers as $c){
                                                ${'sum_origin_'.$c->code} = 0;
                                                ${'total_c'.$c->code }= 'total_c'.$c->code;
                                                ${'total_inland'.$c->code} = 'total_inland'.$c->code;
                                                ${'sum_inland_'.$c->code} = 0;
                                                
                                            }
                                            $show_inland='hide';
                                        ?>
                                        @foreach($rate as $r)
                                            @foreach($r->charge as $v)
                                                @if($v->type_id==1)
                                                    <?php
                                                        foreach($containers as $c){
                                                            ${'sum_origin_'.$c->code} += $v->${'total_c'.$c->code};
                                                        }
                                                    ?>
                                                    <tr class="text-left color-table">
                                                        <td>{{@$v->surcharge->name}}</td>
                                                        <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                        <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                        <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                        @foreach ($equipmentHides as $key=>$hide)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td {{ $hide }}>{{ $v->${'total_c'.$c->code} }}</td>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        @if($quote->pdf_option->grouped_origin_charges==1)
                                                            <td>{{$quote->pdf_option->origin_charges_currency}}</td>
                                                        @else
                                                            <td>{{$currency_cfg->alphacode}}</td>
                                                        @endif
                                                    </tr>
                                                @endif
                                            @endforeach
                                            
                                            @if(!$r->inland->isEmpty())
                                                @foreach($r->inland as $v)
                                                    @if($v->type=='Origin' && $r->inland->where('type', 'Origin')->count()==1)
                                                        @if($r->inland->where('type', 'Origin')->count()==1)
                                                            <tr class="text-left color-table">
                                                                <td>{{$v->provider}}</td>
                                                                <td>-</td>
                                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                                @foreach ($equipmentHides as $key=>$hide)
                                                                    @foreach ($containers as $c)
                                                                        @if($c->code == $key)
                                                                            @php 
                                                                                ${'sum_inland_'.$c->code} += $v->${'total_inland'.$c->code}; 
                                                                            @endphp
                                                                            <td {{ $hide }}>{{ round($v->${'total_inland'.$c->code}) }}</td>
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                                <td>{{$currency_cfg->alphacode}}</td>
                                                            </tr>
                                                        @else
                                                            <?php
                                                                $show_inland='';
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
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                        @foreach ($equipmentHides as $key=>$hide)
                                            @foreach ($containers as $c)
                                                @if($c->code == $key)
                                                    <td {{ $hide }}><b>{{ round(@${'sum_origin_'.$c->code}+@${'sum_inland_'.$c->code}) }}</b></td>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        @if($quote->pdf_option->grouped_origin_charges==1)
                                            <td><b>{{$quote->pdf_option->origin_charges_currency}}</b></td>
                                        @else
                                            <td><b>{{$currency_cfg->alphacode}}</b></td>
                                        @endif     
                                    </tr>
                                    <tr class="{{$show_inland}}" style="background-color: white !important;">
                                        <td  style="background-color: white !important;" colspan="2">
                                            <br>
                                            <p class="title">{{__('pdf.origin_inland')}} - {{$origin}}</p>
                                        </td>
                                    </tr>
                                    @if(!$r->inland->isEmpty())
                                        @foreach($r->inland as $v)
                                            @if($v->type=='Origin')
                                                @if($r->inland->where('type', 'Origin')->count()>1)
                                                    <?php
                                                        foreach($containers as $container){
                                                            ${'inland_'.$c->code}+=$v->${'total_inland'.$c->code};
                                                        }
                                                    ?>
                                                    <tr class="text-left color-table">
                                                        <td>{{$v->provider}}</td>
                                                        <td>-</td>
                                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                        @foreach ($equipmentHides as $key=>$hide)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td {{ $hide }}>{{ $v->${'total_inland'.$c->code} }}</td>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td>{{$currency_cfg->alphacode}}</td>
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <br>
                        @endforeach
                        @endif
                    @endforeach
                    <br>
                @endif      