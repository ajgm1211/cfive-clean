                <!-- SALE TERMS ORIGIN -->
                @if($quote->pdf_option->grouped_origin_charges==0 && ($quote->pdf_option->show_type=='detailed' || $quote->pdf_option->show_type=='charges'))
                    @if($sale_terms_origin->count()>0)
                        @foreach($sale_terms_origin as $origin=>$v)
                            @foreach($v as $value)
                                <div>
                                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en origen - {{$origin}}</p>
                                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
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
                                                <td >{{$item->charge!='' ? $item->charge:'-'}}</td>
                                                <td >{{$item->detail!='' ? $item->detail:'-'}}</td>
                                                <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                                <td {{ @$equipmentHides['20'] }}>{{round(@$item->c20)}}</td>
                                                <td {{ @$equipmentHides['40'] }}>{{round(@$item->c40)}}</td>
                                                <td {{ @$equipmentHides['40hc'] }}>{{round(@$item->c40hc)}}</td>
                                                <td {{ @$equipmentHides['40nor'] }}>{{round(@$item->c40nor)}}</td>
                                                <td {{ @$equipmentHides['45'] }}>{{round(@$item->c45)}}</td>
                                                <td >{{@$item->currency->alphacode}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total origin charges</b></td>
                                            <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos en origen</b></td>
                                            <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                                            <td></td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                            <td {{ @$equipmentHides['20'] }}><b>{{round(@$sum20)}}</b></td>
                                            <td {{ @$equipmentHides['40'] }}><b>{{round(@$sum40)}}</b></td>
                                            <td {{ @$equipmentHides['40hc'] }}><b>{{round(@$sum40hc)}}</b></td>
                                            <td {{ @$equipmentHides['40nor'] }}><b>{{round(@$sum40nor)}}</b></td>
                                            <td {{ @$equipmentHides['45'] }}><b>{{round(@$sum45)}}</b></td>
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
                        <div>
                            <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                            <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en origen - {{$origin}}</p>
                            <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
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
                                @if($sale_terms_origin->count()>0)
                                @foreach($sale_terms_origin as $v)
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
                                        $sum_origin_20= 0;
                                        $sum_origin_40= 0;
                                        $sum_origin_40hc= 0;
                                        $sum_origin_40nor= 0;
                                        $sum_origin_45= 0;
                                        $inland_origin_20= 0;
                                        $inland_origin_40= 0;
                                        $inland_origin_40hc= 0;
                                        $inland_origin_40nor= 0;
                                        $inland_origin_45= 0;
                                    ?>  
                                    @foreach($item as $rate)
                                        @foreach($rate->charge as $value)
                                            <?php
                                                $sum_origin_20+=$value->total_20;
                                                $sum_origin_40+=$value->total_40;
                                                $sum_origin_40hc+=$value->total_40hc;
                                                $sum_origin_40nor+=$value->total_40nor;
                                                $sum_origin_45+=$value->total_45;                                
                                            ?>
                                        @endforeach
                                        @foreach($rate->inland as $value)
                                            @if($value->type=='Origin')
                                                <?php
                                                    $inland_origin_20+=$value->total_20;
                                                    $inland_origin_40+=$value->total_40;
                                                    $inland_origin_40hc+=$value->total_40hc;
                                                    $inland_origin_40nor+=$value->total_40nor;
                                                    $inland_origin_45+=$value->total_45;                                
                                                ?>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    <tr class="text-left color-table">
                                        <td colspan="2" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total origin charges</td>
                                        <td colspan="2" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Total gastos en origen</td>
                                        <td colspan="2" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Total de cobranças locais</td>
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{$rate->carrier_id!='' ? $rate->carrier->name:'-'}}</td>
                                        <td {{ @$equipmentHides['20'] }}>{{@$sum_origin_20+@$inland_origin_20+@$sum_sale20}}</td>
                                        <td {{ @$equipmentHides['40'] }}>{{@$sum_origin_40+@$inland_origin_40+@$sum_sale40}}</td>
                                        <td {{ @$equipmentHides['40hc'] }}>{{@$sum_origin_40hc+@$inland_origin_40hc+@$sum_sale40hc}}</td>
                                        <td {{ @$equipmentHides['40nor'] }}>{{@$sum_origin_40nor+@$inland_origin_40nor+@$sum_sale40nor}}</td>
                                        <td {{ @$equipmentHides['45'] }}>{{@$sum_origin_45+@$inland_origin_45+@$sum_sale45}}</td>
                                        @if($quote->pdf_option->grouped_origin_charges==1)
                                            <td >{{$quote->pdf_option->origin_charges_currency}}</td>
                                        @else
                                            <td >{{$currency_cfg->alphacode}}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @empty
                        @if($sale_terms_origin->count()>0)
                            @foreach($sale_terms_origin as $origin=>$v)
                            @foreach($v as $value)
                            <div>
                                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en origen - {{$origin}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
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
                                            <td colspan="2" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Total origin charges</td>
                                            <td colspan="2" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Total gastos en origen</td>
                                            <td colspan="2" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Total de cobranças locais</td>
                                            <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>-</td>
                                            <td {{ @$equipmentHides['20'] }}>{{round(@$sum_sale20)}}</td>
                                            <td {{ @$equipmentHides['40'] }}>{{round(@$sum_sale40)}}</td>
                                            <td {{ @$equipmentHides['40hc'] }}>{{round(@$sum_sale40hc)}}</td>
                                            <td {{ @$equipmentHides['40nor'] }}>{{round(@$sum_sale40nor)}}</td>
                                            <td {{ @$equipmentHides['45'] }}>{{round(@$sum_sale45)}}</td>
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
                        @foreach($value as $origin => $item)
                            <div>
                                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin charges - {{$origin}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Costos en origen - {{$origin}}</p>
                                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Encargos de origem - {{$origin}}</p>
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
                                            $sum_origin_20= 0;
                                            $sum_origin_40= 0;
                                            $sum_origin_40hc= 0;
                                            $sum_origin_40nor= 0;
                                            $sum_origin_45= 0;
                                            $inland_20= 0;
                                            $inland_40= 0;
                                            $inland_40hc= 0;
                                            $inland_40nor= 0;
                                            $inland_45= 0;
                                            $show_inland = 'hide';
                                        ?>
                                        @foreach($rate as $r)
                                            @foreach($r->charge as $v)
                                                @if($v->type_id==1)
                                                    <?php
                                                        $total_origin_20= 0;
                                                        $total_origin_40= 0;
                                                        $total_origin_40hc= 0;
                                                        $total_origin_40nor= 0;
                                                        $total_origin_45= 0;                                   
                                                        $sum_origin_20+=$v->total_20;
                                                        $sum_origin_40+=$v->total_40;
                                                        $sum_origin_40hc+=$v->total_40hc;
                                                        $sum_origin_40nor+=$v->total_40nor;
                                                        $sum_origin_45+=$v->total_45;
                                                    ?>
                                                    <tr class="text-left color-table">
                                                        <td>{{@$v->surcharge->name}}</td>
                                                        <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                        <td {{$quote->pdf_option->language=='English' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                        <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>{{@$v->calculation_type->display_name}}</td>
                                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}>{{@$r->carrier->name}}</td>
                                                        <td {{ @$equipmentHides['20'] }}>{{round($v->total_20)}}</td>
                                                        <td {{ @$equipmentHides['40'] }}>{{round($v->total_40)}}</td>
                                                        <td {{ @$equipmentHides['40hc'] }}>{{round($v->total_40hc)}}</td>
                                                        <td {{ @$equipmentHides['40nor'] }}>{{round($v->total_40nor)}}</td>
                                                        <td {{ @$equipmentHides['45'] }}>{{round($v->total_45)}}</td>
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
                                                    @if($v->type=='Origin')
                                                        @if($r->inland->where('type', 'Origin')->count()==1)
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
                                                                <td {{ @$equipmentHides['20'] }}>{{round($v->total_20)}}</td>
                                                                <td {{ @$equipmentHides['40'] }}>{{round($v->total_40)}}</td>
                                                                <td {{ @$equipmentHides['40hc'] }}>{{round($v->total_40hc)}}</td>
                                                                <td {{ @$equipmentHides['40nor'] }}>{{round($v->total_40nor)}}</td>
                                                                <td {{ @$equipmentHides['45'] }}>{{round($v->total_45)}}</td>
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
                                        <td {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Total gastos en origen</b></td>
                                        <td {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Total de cobranças locais</b></td>
                                        <td></td>
                                        <td {{$quote->pdf_option->show_carrier==1 ? '':'hidden'}}></td>
                                        <td {{ @$equipmentHides['20'] }}><b>{{round(@$sum_origin_20+@$inland_20)}}</b></td>
                                        <td {{ @$equipmentHides['40'] }}><b>{{round(@$sum_origin_40+@$inland_40)}}</b></td>
                                        <td {{ @$equipmentHides['40hc'] }}><b>{{round(@$sum_origin_40hc+@$inland_40hc)}}</b></td>
                                        <td {{ @$equipmentHides['40nor'] }}><b>{{round(@$sum_origin_40nor+@$inland_40nor)}}</b></td>
                                        <td {{ @$equipmentHides['45'] }}><b>{{round(@$sum_origin_45+@$inland_45)}}</b></td>
                                        @if($quote->pdf_option->grouped_origin_charges==1)
                                            <td><b>{{$quote->pdf_option->origin_charges_currency}}</b></td>
                                        @else
                                            <td><b>{{$currency_cfg->alphacode}}</b></td>
                                        @endif     
                                    </tr>
                                    <tr class="{{$show_inland}}" style="background-color: white !important;">
                                        <td  style="background-color: white !important;" colspan="2">
                                            <br>
                                            <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>Origin inlands - {{$origin}}</p>
                                            <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>Inlands de origen - {{$origin}}</p>
                                            <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>Inlands de origem - {{$origin}}</p>
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
                                            @if($v->type=='Origin')
                                                @if($r->inland->where('type', 'Origin')->count()>1)
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
                                                        <td {{ @$equipmentHides['20'] }}>{{round($v->total_20)}}</td>
                                                        <td {{ @$equipmentHides['40'] }}>{{round($v->total_40)}}</td>
                                                        <td {{ @$equipmentHides['40hc'] }}>{{round($v->total_40hc)}}</td>
                                                        <td {{ @$equipmentHides['40nor'] }}>{{round($v->total_40nor)}}</td>
                                                        <td {{ @$equipmentHides['45'] }}>{{round($v->total_45)}}</td>
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
                    <br>
                @endif      