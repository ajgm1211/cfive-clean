                <!-- ALL in destination table -->
                @if($quote->pdf_option->grouped_destination_charges==1 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                    @forelse($destination_charges_grouped as $origin=>$detail)
                        <div>
                            <p class="title">{{__('pdf.destination_charges')}} - {{$origin}}</p>
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
                                                <th class="unit" {{$hide}}><b>{{$key}}</b></th>
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
                                @if($sale_terms_destination->count()>0)
                                    @foreach($sale_terms_destination as $v)
                                        @foreach($v as $value)
                                            @foreach($value->charge as $item)
                                                @php
                                                    ${'sum_sale'.$c->code} += $item->${'total_'.$c->code};
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endif
                                @foreach($detail as $item)
                                    <?php
                                        foreach ($containers as $c) {
                                            ${'destination_'.$c->code} = 0;
                                            ${'destination_inland_'.$c->code} = 0;
                                            ${'sum_total_'.$c->code} = 'sum_total_'.$c->code;
                                            ${'total_inland'.$c->code} = 0;
                                        }
                                    ?>  
                                @foreach($item as $rate)
                                    @foreach($rate->charge as $value)
                                        <?php
                                            foreach ($containers as $c) {
                                                ${'destination_'.$c->code}+=$value->${'sum_total_'.$c->code};
                                            }                      
                                        ?>
                                    @endforeach
                                    @foreach($rate->inland as $value)
                                        @if($value->type=='Destination')
                                            <?php
                                                foreach ($containers as $c) {
                                                    ${'destination_inland_'.$c->code}+=$value->${'sum_total_'.$c->code};
                                                }                              
                                            ?>
                                        @endif
                                    @endforeach
                                @endforeach
                                <tr class="text-left color-table">
                                    <td colspan="2">{{__('pdf.total_local')}}</td>
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                    @foreach ($equipmentHides as $key=>$hide)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)
                                                <td {{ $hide }}>{{ @${'destination_'.$c->code}+@${'destination_inland_'.$c->code}+@${'sum_sale'.$c->code} }}</td>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    @if($quote->pdf_option->grouped_destination_charges==1)
                                        <td >{{$quote->pdf_option->destination_charges_currency}}</td>
                                    @else
                                        <td >{{$currency_cfg->alphacode}}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @empty
                        @if($sale_terms_destination->count()>0)
                            @foreach($sale_terms_destination as $destination=>$v)
                                @foreach($v as $value)
                                    <div>
                                        <p class="title">{{__('pdf.destination_charges')}} - {{$destination}}</p>
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
                                                            <th class="unit" {{$hide}}><b>{{$key}}</b></th>
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
                                            @foreach($value->charge as $item)
                                                @php
                                                    foreach ($containers as $c) {
                                                        ${'sum_sale'.$c->code} += $item->${'total_'.$c->code};
                                                    }
                                                @endphp
                                            @endforeach
                                            <tr class="text-left color-table">
                                                <td colspan="2" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{__('pdf.total_destination')}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                                @foreach ($equipmentHides as $key=>$hide)
                                                    @foreach ($containers as $c)
                                                        @if($c->code == $key)
                                                            <td {{ $hide }}>{{number_format(@${'sum_sale'.$c->code}, 2, '.', '')}}</td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                @if($quote->pdf_option->grouped_destination_charges==1)
                                                    <td >{{$quote->pdf_option->destination_charges_currency}}</td>
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
                    
                <!-- SALE TERMS DESTINATION-->
                @if($quote->pdf_option->grouped_destination_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                   @if($sale_terms_destination->count()>0)
                    @foreach($sale_terms_destination as $destination=>$v)
                        @foreach($v as $value)
                        <div>
                            <p class="title">{{__('pdf.destination_charges')}} - {{$destination}}</p>
                            <br>
                        </div>

                        <table border="0" cellspacing="1" cellpadding="1" >
                            <thead class="title-quote text-left header-table">
                                <tr >
                                    <th class="unit"><b>{{__('pdf.charge')}}</b></th>
                                    <th class="unit"><b>{{__('pdf.detail')}}</b></th>
                                    <td class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>{{__('pdf.carrier')}}</b></td>
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
                                        ${'sum_'.$c->code}=0;
                                        ${'total_'.$c->code}='total_'.$c->code;
                                    }
                                @endphp
                                @foreach($value->charge as $item)
                                    @php
                                        foreach ($containers as $c){
                                            ${'sum_'.$c->code} += $item->${'total_'.$c->code};
                                        }
                                    @endphp
                                    <tr class="text-left color-table">
                                        <td >
                                            {{$item->charge!='' ? $item->charge:'-'}}
                                        </td>
                                        <td >
                                            {{$item->detail!='' ? $item->detail:'-'}}
                                        </td>
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                        @foreach ($equipmentHides as $key=>$hide)
                                            @foreach ($containers as $c)
                                                @if($c->code == $key)
                                                    <td {{ $hide }}>{{number_format(@$item->${'total_'.$c->code}, 2, '.', '')}}</td>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        <td >{{@$item->currency->alphacode}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>{{__('pdf.total_destination')}}</b></td>
                                    <td></td>
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                    @foreach ($equipmentHides as $key=>$hide)
                                        @foreach ($containers as $c)
                                            @if($c->code == $key)
                                                <td {{ $hide }}>{{number_format(@$item->${'total_'.$c->code}, 2, '.', '')}}</td>
                                                <td {{ $hide }}><b>{{number_format(@${'sum_'.$c->code}, 2, '.', '')}}</b></td>
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

                <!-- Estoy Aquí -->
                
                <!-- Destinations detailed -->
                @if($quote->pdf_option->grouped_destination_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                        @foreach($destination_charges_detailed as $carrier => $value)
                            @foreach($value as $destination => $destiny)
                                <div>
                                    <p class="title">{{__('pdf.destination_charges')}} - {{$destination}}</p>
                                    <br>
                                </div>
                                <table border="0" cellspacing="1" cellpadding="1">
                                    <thead class="title-quote text-left header-table">
                                        <tr >
                                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Detail</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Detalle</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Detalhe</b></th>
                                            <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                            <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                                            <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                                            <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                                            <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                                            <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                                            <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($destiny as $rate)
                                            <?php
                                                $sum_destination_20= 0;
                                                $sum_destination_40= 0;
                                                $sum_destination_40hc= 0;
                                                $sum_destination_40nor= 0;
                                                $sum_destination_45= 0;
                                                $inland_20= 0;
                                                $inland_40= 0;
                                                $inland_40hc= 0;
                                                $inland_40nor= 0;
                                                $inland_45= 0;
                                                $show_inland='hide';
                                            ?>
                                            @foreach($rate as $r)
                                                @foreach($r->charge as $v)
                                                    @if($v->type_id==2)
                                                        <?php
                                                            $total_destination_20= 0;
                                                            $total_destination_40= 0;
                                                            $total_destination_40hc= 0;
                                                            $total_destination_40nor= 0;
                                                            $total_destination_45= 0;                                   
                                                            $sum_destination_20+=$v->total_20;
                                                            $sum_destination_40+=$v->total_40;
                                                            $sum_destination_40hc+=$v->total_40hc;
                                                            $sum_destination_40nor+=$v->total_40nor;
                                                            $sum_destination_45+=$v->total_45;
                                                        ?>
                                                        <tr class="text-left color-table">
                                                            <td>{{@$v->surcharge->name}}</td>
                                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                            <td {{ @$equipmentHides['20'] }}>{{$v->total_20}}</td>
                                                            <td {{ @$equipmentHides['40'] }}>{{$v->total_40}}</td>
                                                            <td {{ @$equipmentHides['40hc'] }}>{{$v->total_40hc}}</td>
                                                            <td {{ @$equipmentHides['40nor'] }}>{{$v->total_40nor}}</td>
                                                            <td {{ @$equipmentHides['45'] }}>{{$v->total_45}}</td>
                                                            @if($quote->pdf_option->grouped_destination_charges==1)
                                                                <td>{{$quote->pdf_option->destination_charges_currency}}</td>
                                                            @else
                                                                <td>{{$currency_cfg->alphacode}}</td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                @if(!$r->inland->isEmpty())
                                                @foreach($r->inland as $v)
                                                    @if($v->type=='Destination')
                                                        @if($r->inland->where('type', 'Destination')->count()==1)
                                                            <?php
                                                                $inland_20+=$v->total_20;
                                                                $inland_40+=$v->total_40;
                                                                $inland_40hc+=$v->total_40hc;
                                                                $inland_40nor+=$v->total_40nor;
                                                                $inland_45+=$v->total_45;
                                                            ?>
                                                            <tr class="text-left color-table">
                                                                <td>{{$v->provider}}</td>
                                                                <td>-</td>
                                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                                <td {{ @$equipmentHides['20'] }}>{{$v->total_20}}</td>
                                                                <td {{ @$equipmentHides['40'] }}>{{$v->total_40}}</td>
                                                                <td {{ @$equipmentHides['40hc'] }}>{{$v->total_40hc}}</td>
                                                                <td {{ @$equipmentHides['40nor'] }}>{{$v->total_40nor}}</td>
                                                                <td {{ @$equipmentHides['45'] }}>{{$v->total_45}}</td>
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
                                    <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total local charges</b></td>
                                    <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos en destino</b></td>
                                    <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                                    <td></td>
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                    <td {{ @$equipmentHides['20'] }}><b>{{number_format(@$sum_destination_20+@$inland_20, 2, '.', '')}}</b></td>
                                    <td {{ @$equipmentHides['40'] }}><b>{{number_format(@$sum_destination_40+@$inland_40, 2, '.', '')}}</b></td>
                                    <td {{ @$equipmentHides['40hc'] }}><b>{{number_format(@$sum_destination_40hc+@$inland_40hc, 2, '.', '')}}</b></td>
                                    <td {{ @$equipmentHides['40nor'] }}><b>{{number_format(@$sum_destination_40nor+@$inland_40nor, 2, '.', '')}}</b></td>
                                    <td {{ @$equipmentHides['45'] }}><b>{{number_format(@$sum_destination_45+@$inland_45, 2, '.', '')}}</b></td>
                                    @if($quote->pdf_option->grouped_destination_charges==1)
                                        <td><b>{{$quote->pdf_option->destination_charges_currency}}</b></td>
                                    @else
                                        <td><b>{{$currency_cfg->alphacode}}</b></td>
                                    @endif     
                                </tr>
                                <tr class="{{$show_inland}}">
                                    <td colspan="2">
                                        <br>
                                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination inlands - {{$destination}}</p>
                                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Inlands de destino - {{$destination}}</p>
                                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Inlands de destino - {{$destination}}</p>
                                    </td>
                                </tr>
                                    @if(!$r->inland->isEmpty())
                                        @foreach($r->inland as $v)
                                            @if($v->type=='Destination')
                                                @if($r->inland->where('type', 'Destination')->count()>1)
                                                    <?php
                                                        $inland_20+=$v->total_20;
                                                        $inland_40+=$v->total_40;
                                                        $inland_40hc+=$v->total_40hc;
                                                        $inland_40nor+=$v->total_40nor;
                                                        $inland_45+=$v->total_45;
                                                    ?>
                                                    <tr class="text-left color-table">
                                                        <td>{{$v->provider}}</td>
                                                        <td>-</td>
                                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                        <td {{ @$equipmentHides['20'] }}>{{$v->total_20}}</td>
                                                        <td {{ @$equipmentHides['40'] }}>{{$v->total_40}}</td>
                                                        <td {{ @$equipmentHides['40hc'] }}>{{$v->total_40hc}}</td>
                                                        <td {{ @$equipmentHides['40nor'] }}>{{$v->total_40nor}}</td>
                                                        <td {{ @$equipmentHides['45'] }}>{{$v->total_45}}</td>
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
                    @endforeach
                @endif