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
                <img src="{{$user->companyUser->logo}}" class="img img-responsive" width="200">
            </div>
            <div id="company">
                <div><b>Quotation Id</b> <span style="color: #D0AD67"><b>#{{$quote->id}}</b></span></div>
                <div><b>Date of issue:</b> {{$quote->created_at}}</div>
                <div><b>Validity: </b>{{$quote->validity}}</div>
            </div>
        </header>
        <main>
            <div id="details" class="clearfix details">
                <div class="client">
                    <p>From:</p>
                    <span id="destination_input" style="line-height: 0.4">
                        <p>{{$user->name}}</p>
                        <p><b>{{$user->companyUser->name}}</b></p>
                        <p>{{$user->companyUser->address}}</p>
                        <p>{{$user->companyUser->phone}}</p>
                    </span>

                </div>
                <div class="company text-right" style="float: right; width: 350px;">
                    <p>To:</p>
                    <span id="destination_input" style="line-height: 0.4">
                        <p>{{$quote->contact->first_name.' '.$quote->contact->last_name}}</p>
                        <p><b>{{$quote->company->business_name}}</b></p>
                        <p>{{$quote->company->address}}</p>
                        <p>{{$quote->contact->phone}}</p>
                    </span>

                </div>
            </div>
            <div id="" class="clearfix">
                <div class="client" style="width: 150px;">
                    <div class="panel panel-default" style="width: 350px;">
                        <div class="panel-heading title">Origin {{$quote->type==3 ? ' Airport':' Port'}}</div>
                        <div class="panel-body">
                            <span id="origin_input" style="color: #1D3A6E;">
                                @if($quote->origin_harbor_id!='')
                                Port: {{$quote->origin_harbor->name}}
                                @endif
                                @if($quote->origin_airport_id!='')
                                Airport: {{$quote->origin_airport->name}}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                <div class="company" style="float: right; width: 350px;">
                    <div class="panel panel-default" style="width: 350px; height: 83px;">
                        <div class="panel-heading title">Destination {{$quote->type==3 ? ' Airport':' Port'}}</div>
                        <div class="panel-body">
                            <span id="destination_input" style="color: #1D3A6E;">
                                @if($quote->destination_harbor_id!='')
                                Port: {{$quote->destination_harbor->name}}
                                @endif
                                @if($quote->destination_airport_id!='')
                                Airport: {{$quote->destination_airport->name}}
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
                        <div class="panel-heading title">Origin address</div>
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
                        <div class="panel-heading title">Destination address</div>
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
            <div id="details" class="clearfix details">
                <hr>
                <div class="company" style="color: #1D3A6E;">
                    <p class="title"><b>Cargo details:</b></p>
                    <p>{!! $quote->qty_20 != '' ? $quote->qty_20.' x 20\' container':'' !!}</p>
                    <p>{!! $quote->qty_40 != '' ? $quote->qty_40.' x 40\' container':'' !!}</p>
                    <p>{!! $quote->qty_40_hc != '' ? $quote->qty_40_hc.' x 40\' HC container':'' !!}</p>

                    @if($quote->total_quantity!='' && $quote->total_quantity>0)
                    <div class="row">
                        <div class="col-md-3">
                            <div id="cargo_details_cargo_type_p"><b>Cargo type:</b> {{$quote->type_cargo == 1 ? 'Pallets' : 'Packages'}}</div>
                        </div>
                        <div class="col-md-3">
                            <div id="cargo_details_total_quantity_p"><b>Total quantity:</b> {{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</div>
                        </div>
                        <div class="col-md-3">
                            <div id="cargo_details_total_weight_p"><b>Total weight: </b> {{$quote->total_weight != '' ? $quote->total_weight.'Kg' : ''}}</div>
                        </div>
                        <div class="col-md-3">
                            <p id="cargo_details_total_volume_p"><b>Total volume: </b> {!!$quote->total_volume != '' ? $quote->total_volume.'m<sup>3</sup>' : ''!!}</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($package_loads) && count($package_loads)>0)
                    <table class="table table-bordered color-blue">
                        <thead class="title-quote text-center header-table">
                            <tr>
                                <th class="unit"><b>Cargo type</b></th>
                                <th class="unit"><b>Quantity</b></th>
                                <th class="unit"><b>Height</b></th>
                                <th class="unit"><b>Width</b></th>
                                <th class="unit"><b>Large</b></th>
                                <th class="unit"><b>Weight</b></th>
                                <th class="unit"><b>Total weight</b></th>
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
                    @endif
                </div>
            </div>
            <hr>
            @if(count($origin_ammounts)>0)
            <p class="title">Origin charges:</p>
            <br>
            <table class="table table-bordered color-blue" border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit"><b>Charge</b></th>
                        <th class="unit"><b>Detail</b></th>
                        <th class="unit"><b>Units</b></th>
                        <th class="unit"><b>Price per units</b></th>
                        <th class="unit"><b>Total </b></th>
                        <th class="unit"><b>Total &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($origin_ammounts as $origin_ammount)
                    <tr class="text-center">
                        <td class="white">{{$origin_ammount->charge}}</td>
                        <td class="white">{{$origin_ammount->detail}}</td>
                        <td class="white">{{$origin_ammount->units}}</td>
                        @if($origin_ammount->currency->alphacode!=$currency_cfg->alphacode)
                        @php
                        if($currency_cfg->alphacode=='USD'){
                        $rate=$currency_cfg->rates_eur;
                        }else{
                        $rate=$currency_cfg->rates;
                        }
                        $markup_currency=$origin_ammount->markup / $rate;
                        @endphp                    
                        <td class="white">{{number_format((float)($origin_ammount->price_per_unit+$markup_currency)/$origin_ammount->units, 2,'.', '')}}</td>
                        @else
                        <td>{{$origin_ammount->total_ammount_2 / $origin_ammount->units}}</td>
                        @endif
                        @if($origin_ammount->currency->alphacode!=$currency_cfg->alphacode)
                        <td>{{number_format((float)$origin_ammount->total_ammount  + $markup_currency, 2,'.', '')}} {{$origin_ammount->currency->alphacode}}</td>
                        @else
                        <td>{{$origin_ammount->total_ammount + $origin_ammount->markup}} {{$origin_ammount->currency->alphacode}}</td>
                        @endif
                        <td class="white">{{$origin_ammount->total_ammount_2}} &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-center subtotal">
                        <td colspan="4"></td>
                        <td style="font-size: 12px; color: #01194F"><b>Subtotal</b></td>
                        <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_origin}} &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></td>
                    </tr>
                </tfoot>
            </table>
            @endif
            @if(count($freight_ammounts)>0)
            <p class="title">Freight charges:</p>
            <br>
            <table class="table table-bordered color-blue" border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit"><b>Charge</b></th>
                        <th class="unit"><b>Detail</b></th>
                        <th class="unit"><b>Units</b></th>
                        <th class="unit"><b>Price per units</b></th>
                        <th class="unit"><b>Total</b></th>
                        <th class="unit"><b>Total &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($freight_ammounts as $freight_ammount)
                    <tr class="text-center">
                        <td>{{$freight_ammount->charge}}</td>
                        <td>{{$freight_ammount->detail}}</td>
                        <td>{{$freight_ammount->units}}</td>
                        @if($freight_ammount->currency->alphacode!=$currency_cfg->alphacode)
                        @php
                        if($currency_cfg->alphacode=='USD'){
                        $rate=$currency_cfg->rates_eur;
                        }else{
                        $rate=$freight_ammount->currency->rates;
                        }
                        $markup_currency=$freight_ammount->markup / $rate;
                        @endphp
                        <td>{{number_format((float)($freight_ammount->price_per_unit + $markup_currency) / $freight_ammount->units, 2,'.', '')}}</td>
                        @else
                        <td>{{$freight_ammount->total_ammount_2 / $freight_ammount->units}}</td>
                        @endif
                        @if($freight_ammount->currency->alphacode!=$currency_cfg->alphacode)
                        <td>{{number_format((float)$freight_ammount->total_ammount + $markup_currency, 2,'.', '')}} {{$freight_ammount->currency->alphacode}}</td>
                        @else
                        <td>{{$freight_ammount->total_ammount + $freight_ammount->markup}} {{$freight_ammount->currency->alphacode}}</td>
                        @endif
                        <td>{{$freight_ammount->total_ammount_2}} @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif<</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-center" style="font-size: 12px;">
                        <td colspan="4"></td>
                        <td style="font-size: 12px; color: #01194F"><b>Subtotal</b></td>
                        <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_freight}} &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></td>
                    </tr>
                </tfoot>
            </table>
            @endif
            @if(count($destination_ammounts)>0)
            <p class="title">Destination charges:</p>
            <br>
            <table class="table table-bordered color-blue" border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Charge</b></th>
                        <th class="unit"><b>Detail</b></th>
                        <th class="unit"><b>Units</b></th>
                        <th class="unit"><b>Price per units</b></th>
                        <th class="unit"><b>Total</b></th>
                        <th class="unit"><b>Total &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($destination_ammounts as $destination_ammount)
                    <tr class="text-center">
                        <td>{{$destination_ammount->charge}}</td>
                        <td>{{$destination_ammount->detail}}</td>
                        <td>{{$destination_ammount->units}}</td>                  
                        @if($destination_ammount->currency->alphacode!=$currency_cfg->alphacode)
                        @php
                        if($currency_cfg->alphacode=='USD'){
                        $rate=$currency_cfg->rates_eur;
                        }else{
                        $rate=$currency_cfg->rates;
                        }
                        $markup_currency=$destination_ammount->markup / $rate;
                        @endphp
                        <td>{{($destination_ammount->price_per_unit  + $markup_currency) / $destination_ammount->units }}</td>
                        @else
                        <td>{{$destination_ammount->total_ammount_2 / $destination_ammount->units}}</td>
                        @endif                    
                        @if($destination_ammount->currency->alphacode!=$currency_cfg->alphacode)
                        <td>{{$destination_ammount->total_ammount }} {{$destination_ammount->currency->alphacode}}</td>
                        @else
                        <td>{{$destination_ammount->total_ammount + $destination_ammount->markup}} {{$destination_ammount->currency->alphacode}}</td>
                        @endif                    
                        <td>{{$destination_ammount->total_ammount_2}} &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-center">
                        <td colspan="4"></td>
                        <td style="font-size: 12px; color: #01194F"><b>Subtotal</b></td>
                        <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_destination}} 
                            &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </main>
        <div class="clearfix details">
            <div class="company">
                <p class="title text-right" style="color: #01194F;"><b>TOTAL: {{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}} &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></p>
            </div>
            <hr>
        </div>
        <div class="clearfix">
            <br>
            <p class="title">Terms and conditions</p>
            <hr>            
            @if(isset($terms_origin) && $terms_origin->count()>0)
            <div class=" row">
                <div class="col-md-12">
                    <p class="title">Origin</p>
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
                    <p class="title">Destination</p>
                    @foreach($terms_destination as $v)
                    {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <footer>
            Cargofive &copy; {{date('Y')}}
        </footer>
    </body>
</html>
