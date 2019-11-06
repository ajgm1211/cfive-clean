                <!-- ALL in destination table -->
                @if($quote->pdf_option->grouped_destination_charges==1 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                    @forelse($destination_charges_grouped as $origin=>$detail)
                    <div>
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$origin}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en destino - {{$origin}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$origin}}</p>
                        <br>
                    </div>
                    <table border="0" cellspacing="1" cellpadding="1" >
                        <thead class="title-quote text-left header-table">
                            <tr >
                                <th class="unit" colspan="2" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                <th class="unit" colspan="2" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                                <th class="unit" colspan="2" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
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
                            @php
                                $sum_sale20 = 0;
                                $sum_sale40 = 0;
                                $sum_sale40hc = 0;
                                $sum_sale40nor = 0;
                                $sum_sale45 = 0;
                            @endphp
                            @if($sale_terms_destination->count()>0)
                                @foreach($sale_terms_destination as $v)
                                    @foreach($v as $value)
                                        @foreach($value->charge as $item)
                                            @php
                                                $sum_sale20 += $item->c20;
                                                $sum_sale40 += $item->c40;
                                                $sum_sale40hc += $item->c40hc;
                                                $sum_sale40nor += $item->c40nor;
                                                $sum_sale45 += $item->c45;
                                            @endphp
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endif
                            @foreach($detail as $item)
                                <?php
                                    $sum_destination_20= 0;
                                    $sum_destination_40= 0;
                                    $sum_destionation_40hc= 0;
                                    $sum_destination_40nor= 0;
                                    $sum_destination_45= 0;
                                    $inland_destination_20=0;
                                    $inland_destination_40=0;
                                    $inland_destination_40hc=0;
                                    $inland_destination_40nor=0;
                                    $inland_destination_45=0;
                                ?>  
                            @foreach($item as $rate)
                                @foreach($rate->charge as $value)
                                    <?php
                                        $sum_destination_20+=$value->total_20;
                                        $sum_destination_40+=$value->total_40;
                                        $sum_destionation_40hc+=$value->total_40hc;
                                        $sum_destination_40nor+=$value->total_40nor;
                                        $sum_destination_45+=$value->total_45;                                
                                    ?>
                                @endforeach
                                @foreach($rate->inland as $value)
                                    @if($value->type=='Destination')
                                        <?php
                                            $inland_destination_20+=$value->total_20;
                                            $inland_destination_40+=$value->total_40;
                                            $inland_destination_40hc+=$value->total_40hc;
                                            $inland_destination_40nor+=$value->total_40nor;
                                            $inland_destination_45+=$value->total_45;                                
                                        ?>
                                    @endif
                                @endforeach
                            @endforeach
                            <tr class="text-left color-table">
                                <td colspan="2" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total destination charges</td>
                                <td colspan="2" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Total gastos en destino</td>
                                <td colspan="2" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Total de cobranças locais</td>
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$rate->carrier->name}}</td>
                                <td {{ @$equipmentHides['20'] }}>{{@$sum_destination_20+@$inland_destination_20+@$sum_sale20}}</td>
                                <td {{ @$equipmentHides['40'] }}>{{@$sum_destination_40+@$inland_destination_40+@$sum_sale40}}</td>
                                <td {{ @$equipmentHides['40hc'] }}>{{@$sum_destionation_40hc+@$inland_destination_40hc+@$sum_sale40hc}}</td>
                                <td {{ @$equipmentHides['40nor'] }}>{{@$sum_destination_40nor+@$inland_destination_40nor+@$sum_sale40nor}}</td>
                                <td {{ @$equipmentHides['45'] }}>{{@$sum_destination_45+@$inland_destination_45+@$sum_sale45}}</td>
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
                                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$destination}}</p>
                                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en destino - {{$destination}}</p>
                                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$destination}}</p>
                                        <br>
                                    </div>

                                    <table border="0" cellspacing="1" cellpadding="1" >
                                        <thead class="title-quote text-left header-table">
                                            <tr >
                                                <th class="unit" colspan="2" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                                <th class="unit" colspan="2" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Concepto</b></th>
                                                <th class="unit" colspan="2" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Conceito</b></th>
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
                                            @php
                                                $sum_sale20=0;
                                                $sum_sale40=0;
                                                $sum_sale40hc=0;
                                                $sum_sale40nor=0;
                                                $sum_sale45=0;
                                            @endphp
                                            @foreach($value->charge as $item)
                                                @php
                                                    $sum_sale20 += $item->sum20;
                                                    $sum_sale40 += $item->sum40;
                                                    $sum_sale40hc += $item->sum40hc;
                                                    $sum_sale40nor += $item->sum40nor;
                                                    $sum_sale45 += $item->sum45;
                                                @endphp

                                            @endforeach
                                                <tr class="text-left color-table">
                                                    <td colspan="2" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total destination charges</td>
                                                    <td colspan="2" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Total gastos en destino</td>
                                                    <td colspan="2" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Total de cobranças locais</td>
                                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                                    <td {{ @$equipmentHides['20'] }}>{{number_format(@$sum_sale20, 2, '.', '')}}</td>
                                                    <td {{ @$equipmentHides['40'] }}>{{number_format(@$sum_sale40, 2, '.', '')}}</td>
                                                    <td {{ @$equipmentHides['40hc'] }}>{{number_format(@$sum_sale40hc, 2, '.', '')}}</td>
                                                    <td {{ @$equipmentHides['40nor'] }}>{{number_format(@$sum_sale40nor, 2, '.', '')}}</td>
                                                    <td {{ @$equipmentHides['45'] }}>{{number_format(@$sum_sale45, 2, '.', '')}}</td>
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
                        <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en destino - {{$destination}}</p>
                        <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$destination}}</p>
                        <br>
                    </div>

                    <table border="0" cellspacing="1" cellpadding="1" >
                        <thead class="title-quote text-left header-table">
                            <tr >
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Charge</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Cargo</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Cargo</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Detail</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Detalle</b></th>
                                <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Detail</b></th>
                                <td class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></td>
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
                            @php
                                $sum20=0;
                                $sum40=0;
                                $sum40hc=0;
                                $sum40nor=0;
                                $sum45=0;
                            @endphp
                            @foreach($value->charge as $item)
                                @php
                            
                                    $sum20 += $item->sum20;
                                    $sum40 += $item->sum40;
                                    $sum40hc += $item->sum40hc;
                                    $sum40nor += $item->sum40nor;
                                    $sum45 += $item->sum45;
                                @endphp
                                <tr class="text-left color-table">
                                    <td >
                                        {{$item->charge!='' ? $item->charge:'-'}}
                                    </td>
                                    <td >
                                        {{$item->detail!='' ? $item->detail:'-'}}
                                    </td>
                                    <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                    <td {{ @$equipmentHides['20'] }}>{{number_format(@$item->c20, 2, '.', '')}}</td>
                                    <td {{ @$equipmentHides['40'] }}>{{number_format(@$item->c40, 2, '.', '')}}</td>
                                    <td {{ @$equipmentHides['40hc'] }}>{{number_format(@$item->c40hc, 2, '.', '')}}</td>
                                    <td {{ @$equipmentHides['40nor'] }}>{{number_format(@$item->c40nor, 2, '.', '')}}</td>
                                    <td {{ @$equipmentHides['45'] }}>{{number_format(@$item->c45, 2, '.', '')}}</td>
                                    <td >{{@$item->currency->alphacode}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total destination charges</b></td>
                                <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos en destino</b></td>
                                <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                                <td></td>
                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                <td {{ @$equipmentHides['20'] }}><b>{{number_format(@$sum20, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['40'] }}><b>{{number_format(@$sum40, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['40hc'] }}><b>{{number_format(@$sum40hc, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['40nor'] }}><b>{{number_format(@$sum40nor, 2, '.', '')}}</b></td>
                                <td {{ @$equipmentHides['45'] }}><b>{{number_format(@$sum45, 2, '.', '')}}</b></td>
                                <td><b>{{$currency_cfg->alphacode}}</b></td>                    
                            </tr>
                        </tbody>
                    </table>
                    @endforeach
                    @endforeach
                @endif
                @endif

                <!-- Destinations detailed -->
                @if($quote->pdf_option->grouped_destination_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                        @foreach($destination_charges as $carrier => $value)
                            @foreach($value as $destination => $item)
                                <div>
                                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Destination charges - {{$destination}}</p>
                                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en destino - {{$destination}}</p>
                                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de destino - {{$destination}}</p>
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
                                        @foreach($item as $rate)
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
                                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>
                                                                @php
                                                                    //echo str_replace("Per", "Por", @$v->calculation_type->display_name); 
                                                                @endphp
                                                                {{@$v->calculation_type->display_name}}
                                                            </td>
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
                                    <!--<tr class="{{$show_inland}}">
                                        <th class="unit"><b>Charge</b></th>
                                        <th class="unit"><b>Detail</b></th>
                                        <th class="unit" {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}><b>@if($quote->pdf_option->language=='English') Carrier @elseif($quote->pdf_option->language=='Spanish') Línea marítima @else Linha Maritima @endif</b></th>
                                        <th {{ @$equipmentHides['20'] }}><b>20'</b></th>
                                        <th {{ @$equipmentHides['40'] }}><b>40'</b></th>
                                        <th {{ @$equipmentHides['40hc'] }}><b>40' HC</b></th>
                                        <th {{ @$equipmentHides['40nor'] }}><b>40' NOR</b></th>
                                        <th {{ @$equipmentHides['45'] }}><b>45'</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Currency</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Moneda</b></th>
                                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Moeda</b></th>
                                    </tr>-->
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