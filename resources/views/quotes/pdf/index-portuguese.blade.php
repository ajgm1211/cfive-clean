<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Quote #{{$quote->id}}</title>
        <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
        <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
    </head>
    <body style="background-color: white; font-size: 11px;">
        <header class="clearfix">
            <div id="logo">
                <img src="{{$user->companyUser->logo}}" class="img img-responsive" style="width: 225px; height: auto; margin-bottom:25px">
            </div>
            <div id="company">
                <div><b>Citar</b> <span style="color: #D0AD67"><b>#{{$quote->company_quote}}</b></span></div>
                <div><b>Data de emissão:</b> {{date_format($quote->created_at, 'M d, Y H:i')}}</div>
                @if($quote->validity!='')
                @php
                $date = DateTime::createFromFormat('Y-m-d', $quote->validity);
                $validity = $date->format('M d, Y');
                @endphp
                <div><b>Válido até: </b>{{$validity}}</div>
                @endif
            </div>
        </header>
        <main>
            <div id="details" class="clearfix details">
                <div class="client">
                    <p><b>A partir de:</b></p>
                    <br>
                    <span id="destination_input" style="line-height: 0.4">
                        <p>{{$user->name}}</p>
                        <p><b>{{$user->companyUser->name}}</b></p>
                        <p>{{$user->companyUser->address}}</p>
                        <p>{{$user->phone}}</p>
                        <p>{{$user->email}}</p>
                    </span>

                </div>
                <div class="company text-right" style="float: right; width: 350px;">
                    <p><b>Para:</b></p>
                    <span id="destination_input" style="line-height: 0.4">
                        <img src="{{$quote->company->logo}}" class="img img-responsive" width="110" height="auto" style="margin-bottom:20px">
                        <p>{{$quote->contact->first_name.' '.$quote->contact->last_name}}</p>
                        <p><b>{{$quote->company->business_name}}</b></p>
                        <p style="line-height: 1.2" >{{$quote->company->address}}</p>
                        <p>{{$quote->contact->phone}}</p>
                        <p>{{$quote->contact->email}}</p>
                    </span>
                </div>
            </div>
            <br>
            <div id="" class="clearfix">
                <div class="client" style="width: 150px;">
                    <div class="panel panel-default" style="width: 350px; border: none; border-radius:none;">
                        <div class="panel-heading title" style="border-radius:none; border: 1px solid #dddddd">{{$quote->type==3 ? 'Aeroporto':'Porto'}} de origem</div>
                        <div class="panel-body" style="border: 1px solid #dddddd">
                            <span id="origin_input" style="color: #1D3A6E;">
                                @if($quote->origin_harbor_id!='')
                                {{$quote->origin_harbor->name}}, {{$quote->origin_harbor->code}}
                                @endif
                                @if($quote->origin_airport_id!='')
                                {{$quote->origin_airport->name}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="company" style="float: right; width: 350px;">
                    <div class="panel panel-default" style="width: 350px; height: 83px; border: none; border-radius:none;">
                        <div class="panel-heading title" style="border-radius:none; border: 1px solid #dddddd">{{$quote->type==3 ? 'Aeroporto':'Porto'}} de destino</div>
                        <div class="panel-body" style="border: 1px solid #dddddd; height: 18px;">
                            <span id="destination_input" style="color: #1D3A6E;">
                                @if($quote->destination_harbor_id!='')
                                {{$quote->destination_harbor->name}}, {{$quote->destination_harbor->code}}
                                @endif
                                @if($quote->destination_airport_id!='')
                                {{$quote->destination_airport->name}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @if($quote->origin_address || $quote->destination_address)
            <div id="" class="clearfix">
                @if($quote->origin_address!='')
                <div class="client" style="width: 150px;">
                    <div class="panel panel-default" style="width: 350px;">
                        <div class="panel-heading title">Endereço de origem</div>
                        <div class="panel-body">
                            <span id="origin_input" style="color: #1D3A6E;">
                                {{$quote->origin_address}}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
                @if($quote->destination_address!='')
                <div class="company" style="float: right; width: 350px;">
                    <div class="panel panel-default" style="width: 350px; height: 83px;">
                        <div class="panel-heading title">Endereço de destino</div>
                        <div class="panel-body">
                            <span id="destination_input" style="color: #1D3A6E;">
                                {{$quote->destination_address}}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
            <hr>
            <div class="clearfix">
                <div class="client">
                    <p class="title"><b>{{$quote->type==3 ? 'Companhia aérea':'Transportadora'}}</b></p>
                    @if($quote->carrier_id!='')
                    <p>{{$quote->carrier->name}}</p>
                    @endif
                    @if($quote->airline_id!='')
                    <p>{{$quote->airline->name}}</p>
                    @endif
                </div>
            </div>
            <br>
            <div id="details" class="clearfix details">
                <hr>
                <div class="company" style="color: #1D3A6E;">
                    <p class="title"><b>Detalhes da carga</b></p>
                    <p>{!! $quote->qty_20 != '' && $quote->qty_20 > 0 ? $quote->qty_20.' x 20\' container':'' !!}</p>
                    <p>{!! $quote->qty_40 != '' && $quote->qty_40 > 0 ? $quote->qty_40.' x 40\' container':'' !!}</p>
                    <p>{!! $quote->qty_40_hc != '' &&  $quote->qty_40_hc > 0 ? $quote->qty_40_hc.' x 40\' HC container':'' !!}</p>
                    <p>{!! $quote->qty_45_hc != '' &&  $quote->qty_45_hc > 0 ? $quote->qty_45_hc.' x 45\' HC container':'' !!}</p>
                    @if($quote->total_quantity!='' && $quote->total_quantity>0)
                    <div class="row">
                        <div class="col-md-3">
                            <div id="cargo_details_cargo_type_p"><b>Tipo de carga:</b> {{$quote->type_cargo == 1 ? 'Pallets' : 'Packages'}}</div>
                        </div>
                        <div class="col-md-3">
                            <div id="cargo_details_total_quantity_p"><b>Quantidade total:</b> {{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</div>
                        </div>
                        <div class="col-md-3">
                            <div id="cargo_details_total_weight_p"><b>Peso total: </b> {{$quote->total_weight != '' ? $quote->total_weight.'Kg' : ''}}</div>
                        </div>
                        <div class="col-md-3">
                            <p id="cargo_details_total_volume_p"><b>Volume total: </b> {!!$quote->total_volume != '' ? $quote->total_volume.'m<sup>3</sup>' : ''!!}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($package_loads) && count($package_loads)>0)
                    <table class="table table-bordered color-blue">
                        <thead class="title-quote text-center header-table">
                            <tr>
                                <th class="unit"><b>Tipo de carga</b></th>
                                <th class="unit"><b>Quantidade</b></th>
                                <th class="unit"><b>Altura</b></th>
                                <th class="unit"><b>Width</b></th>
                                <th class="unit"><b>Largura</b></th>
                                <th class="unit"><b>Peso</b></th>
                                <th class="unit"><b>Peso total</b></th>
                                <th class="unit"><b>Volume</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($package_loads as $package_load)
                            <tr class="text-center">
                                <td>{{$package_load->type_cargo==1 ? 'Pallets':'Packages'}}</td>
                                <td>{{$package_load->quantity}}</td>
                                <td>{{$package_load->height}} cm</td>
                                <td>{{$package_load->width}} cm</td>
                                <td>{{$package_load->large}} cm</td>
                                <td>{{$package_load->weight}} kg</td>
                                <td>{{$package_load->total_weight}} kg</td>
                                <td>{{$package_load->volume}} m<sup>3</sup></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-12 pull-right">
                            <b>Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <hr>
            @if($charges_type==1)
                <table class="table table-bordered color-blue page-break" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote text-center header-table">
                        <tr >
                            @if($quote->sub_total_origin!='')
                                <th class="unit"><b>Encargos de origem</b></th>
                            @endif
                            @if($quote->sub_total_freight!='')
                                <th class="unit"><b>Taxas de frete</b></th>
                            @endif
                            @if($quote->sub_total_destination!='')
                                <th class="unit"><b>Custos de destino</b></th>
                            @endif
                            <th class="unit"><b>Total</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @if($quote->sub_total_origin!='')
                                <td>
                                    {{$quote->sub_total_origin}} &nbsp;{{$quote->currencies->alphacode}}
                                </td>
                            @endif
                            @if($quote->sub_total_freight!='')
                                <td>
                                    {{$quote->sub_total_freight}} &nbsp;{{$quote->currencies->alphacode}}
                                </td>
                            @endif
                            @if($quote->sub_total_destination!='')
                                <td>
                                    {{$quote->sub_total_destination}} &nbsp;{{$quote->currencies->alphacode}}
                                </td>
                            @endif
                            <td>
                                {{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}}&nbsp;{{$quote->currencies->alphacode}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            @else
                @if(count($origin_ammounts)>0)
                <p class="title">Encargos de origem</p>
                <br>
                <table class="table table-bordered color-blue" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit"><b>Carga</b></th>
                            <th class="unit"><b>Detalhe</b></th>
                            <th class="unit"><b>Unidades</b></th>
                            <th class="unit"><b>Preço por unidades</b></th>
                            <th class="unit"><b>Total </b></th>
                            <th class="unit"><b>Total &nbsp;{{$quote->currencies->alphacode}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($origin_ammounts as $origin_ammount)
                        <tr class="text-center">
                            <td class="white">{{$origin_ammount->charge}}</td>
                            <td class="white">{{$origin_ammount->detail}}</td>
                            <td class="white">{{$origin_ammount->units}}</td>
                            @if($origin_ammount->currency->alphacode!=$quote->currencies->alphacode)
                                @if($ammounts_type==1)
                                    <td>{{number_format((float)$origin_ammount->total_ammount_2 / $origin_ammount->units, 2,'.', '')}} {{$quote->currencies->alphacode}}</td>
                                @else
                                    @php
                                    $markup_per_unit=$origin_ammount->markup_converted/$origin_ammount->units
                                    @endphp
                                    <td>{{number_format((float)$markup_per_unit+$origin_ammount->price_per_unit, 2,'.', '')}} {{$origin_ammount->currency->alphacode}}</td>
                                @endif
                            @else
                                <td>{{number_format((float)$origin_ammount->total_ammount_2 / $origin_ammount->units, 2,'.', '')}} {{$origin_ammount->currency->alphacode}}</td>
                            @endif
                            @if($origin_ammount->currency->alphacode!=$quote->currencies->alphacode)
                                @if($ammounts_type==1)
                                    <td>{{$origin_ammount->total_ammount_2}} &nbsp;{{$quote->currencies->alphacode}}</td>
                                @else   
                                    <td>{{number_format((float)$origin_ammount->total_ammount + $origin_ammount->markup_converted, 2,'.', '')}} {{$origin_ammount->currency->alphacode}}</td>
                                @endif
                            @else
                                <td>{{$origin_ammount->total_ammount + $origin_ammount->markup}} {{$origin_ammount->currency->alphacode}}</td>
                            @endif
                            <td class="white">{{$origin_ammount->total_ammount_2}} &nbsp;{{$quote->currencies->alphacode}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center subtotal">
                            <td colspan="4"></td>
                            <td style="font-size: 12px; color: #01194F"><b>Subtotal</b></td>
                            <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_origin}} &nbsp;{{$quote->currencies->alphacode}}</b></td>
                        </tr>
                    </tfoot>
                </table>
                @endif
                @if(count($freight_ammounts)>0)
                <p class="title">Taxas de frete</p>
                <br>
                <table class="table table-bordered color-blue" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote text-center header-table">
                        <tr >
                            <th class="unit"><b>Carga</b></th>
                            <th class="unit"><b>Detalhe</b></th>
                            <th class="unit"><b>Unidades</b></th>
                            <th class="unit"><b>Preço por unidades</b></th>
                            <th class="unit"><b>Total </b></th>
                            <th class="unit"><b>Total &nbsp;{{$quote->currencies->alphacode}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($freight_ammounts as $freight_ammount)
                        <tr class="text-center">
                            <td>{{$freight_ammount->charge}}</td>
                            <td>{{$freight_ammount->detail}}</td>
                            <td>{{$freight_ammount->units}}</td>
                            @if($freight_ammount->currency->alphacode!=$quote->currencies->alphacode)
                                @if($ammounts_type==1)
                                    <td>{{number_format((float)$freight_ammount->total_ammount_2 / $freight_ammount->units, 2,'.', '')}} {{$quote->currencies->alphacode}}</td>
                                @else
                                    @php
                                    $markup_per_unit=$freight_ammount->markup_converted/$freight_ammount->units
                                    @endphp
                                    <td>{{number_format((float)$markup_per_unit+$freight_ammount->price_per_unit, 2,'.', '')}} {{$freight_ammount->currency->alphacode}}</td>
                                @endif
                            @else
                                <td>{{number_format((float)$freight_ammount->total_ammount_2 / $freight_ammount->units, 2,'.', '')}} {{$freight_ammount->currency->alphacode}}</td>
                            @endif
                            @if($freight_ammount->currency->alphacode!=$quote->currencies->alphacode)
                                @if($ammounts_type==1)
                                    <td>{{$freight_ammount->total_ammount_2}} &nbsp;{{$quote->currencies->alphacode}}</td>
                                @else   
                                    <td>{{number_format((float)$freight_ammount->total_ammount + $freight_ammount->markup_converted, 2,'.', '')}} {{$freight_ammount->currency->alphacode}}</td>
                                @endif
                            @else
                                <td>{{$freight_ammount->total_ammount + $freight_ammount->markup}} {{$freight_ammount->currency->alphacode}}</td>
                            @endif
                            <td>{{$freight_ammount->total_ammount_2}} &nbsp;{{$quote->currencies->alphacode}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center" style="font-size: 12px;">
                            <td colspan="4"></td>
                            <td style="font-size: 12px; color: #01194F"><b>Subtotal</b></td>
                            <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_freight}} &nbsp;{{$quote->currencies->alphacode}}</b></td>
                        </tr>
                    </tfoot>
                </table>
                @endif
                @if(count($destination_ammounts)>0)
                <p class="title">Custos de destino</p>
                <br>
                <table class="table table-bordered color-blue" border="0" cellspacing="0" cellpadding="0">
                    <thead class="title-quote text-center header-table">
                        <tr>
                            <th class="unit"><b>Carga</b></th>
                            <th class="unit"><b>Detalhe</b></th>
                            <th class="unit"><b>Unidades</b></th>
                            <th class="unit"><b>Preço por unidades</b></th>
                            <th class="unit"><b>Total </b></th>
                            <th class="unit"><b>Total &nbsp;{{$quote->currencies->alphacode}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($destination_ammounts as $destination_ammount)
                        <tr class="text-center">
                            <td>{{$destination_ammount->charge}}</td>
                            <td>{{$destination_ammount->detail}}</td>
                            <td>{{$destination_ammount->units}}</td>                  
                            @if($destination_ammount->currency->alphacode!=$quote->currencies->alphacode)
                                @if($ammounts_type==1)
                                    <td>{{number_format((float)$destination_ammount->total_ammount_2 / $destination_ammount->units, 2,'.', '')}} {{$quote->currencies->alphacode}}</td>
                                @else
                                    @php
                                    $markup_per_unit=$destination_ammount->markup_converted/$destination_ammount->units
                                    @endphp
                                    <td>{{number_format((float)$markup_per_unit+$destination_ammount->price_per_unit, 2,'.', '')}} {{$destination_ammount->currency->alphacode}}</td>
                                @endif
                            @else
                                <td>{{number_format((float)$destination_ammount->total_ammount_2 / $destination_ammount->units, 2,'.', '')}} {{$destination_ammount->currency->alphacode}}</td>
                            @endif                    
                            @if($destination_ammount->currency->alphacode!=$quote->currencies->alphacode)
                                @if($ammounts_type==1)
                                    <td>{{$destination_ammount->total_ammount_2}} &nbsp;{{$quote->currencies->alphacode}}</td>
                                @else   
                                    <td>{{number_format((float)$destination_ammount->total_ammount + $destination_ammount->markup_converted, 2,'.', '')}} {{$destination_ammount->currency->alphacode}}</td>
                                @endif
                            @else
                                <td>{{$destination_ammount->total_ammount + $destination_ammount->markup}} {{$destination_ammount->currency->alphacode}}</td>
                            @endif                    
                            <td>{{$destination_ammount->total_ammount_2}} &nbsp;{{$quote->currencies->alphacode}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-center">
                            <td colspan="4"></td>
                            <td style="font-size: 12px; color: #01194F"><b>Subtotal</b></td>
                            <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_destination}} 
                                &nbsp; {{$quote->currencies->alphacode}}</b></td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            @endif
        </main>
        @if($charges_type==2)
            <div class="clearfix details page-break">
                <div class="company">
                    <p class="title text-right" style="color: #01194F;"><b>Total: {{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}} &nbsp;{{$quote->currencies->alphacode}}</b></p>
                </div>
            </div>    
        @endif
        <div class="clearfix">
            <br>
            <p class="title">Termos e Condições</p>
            <hr>
            @if(isset($terms_all) && $terms_all->count()>0)
            <div class=" row">
                <div class="col-md-12">
                    @foreach($terms_all as $v)
                    {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                    @endforeach
                </div>
            </div>
            <br>
            @endif
            @if(isset($terms_origin) && $terms_origin->count()>0)
            <div class=" row">
                <div class="col-md-12">
                    <p class="title">Origem</p>
                    @foreach($terms_origin as $v)
                    {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                    @endforeach
                </div>
            </div>
            @endif
            <br>
            @if(isset($terms_destination) && $terms_destination->count()>0)
            <div class="row">
                <div class="col-md-12">
                    <p class="title">Destino</p>
                    @foreach($terms_destination as $v)
                    {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <!--<footer>
            Cargofive &copy; {{date('Y')}}
        </footer>-->
    </body>
</html>
