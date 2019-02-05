<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Quote #{{$quote->company_quote}}</title>
        <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
        <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
    </head>
    <body style="background-color: white; font-size: 11px;">
        <header class="clearfix">
            <div id="logo">
                <img src="{{Storage::disk('s3_upload')->url($user->companyUser->logo)}}" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:25px">
            </div>
            <div id="company">
                <div><span class="color-title"><b>Quotation Id:</b></span> <span style="color: #20A7EE"><b>#{{$quote->custom_id == '' ? $quote->company_quote:$quote->custom_id}}</b></span></div>
                <div><span class="color-title"><b>Date of issue:</b></span> {{date_format($quote->created_at, 'M d, Y H:i')}}</div>
                @if($quote->validity!=''&&$quote->since_validity!='')
                <div><span class="color-title"><b>Validity: </b></span> {{\Carbon\Carbon::parse( $quote->since_validity)->format('d M Y') }} -  {{\Carbon\Carbon::parse( $quote->validity)->format('d M Y') }}</div>
                @endif
            </div>
        </header>
        <main>
            <div id="details" class="clearfix details">
                <div class="client">
                    <p><b>From:</b></p>
                    <span id="destination_input" style="line-height: 0.5">
                        <p>{{$user->name}} {{$user->lastname}}</p>
                        <p><span style="color: #031B4E"><b>{{$user->companyUser->name}}</b></span></p>
                        <p>{{$user->companyUser->address}}</p>
                        <p>{{$user->phone}}</p>
                        <p>{{$user->email}}</p>
                    </span>

                </div>
                <div class="company text-right" style="float: right; width: 350px;">
                    <p><b>To:</b></p>
                    <span id="destination_input" style="line-height: 0.5">
                        <!--<img src="{{$quote->company->logo}}" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px">-->
                        <p>{{$quote->contact->first_name.' '.$quote->contact->last_name}}</p>
                        <p><span style="color: #031B4E"><b>{{$quote->company->business_name}}</b></span></p>
                        <p>{{$quote->company->address}}</p>
                        <p>{{$quote->contact->phone}}</p>
                        <p>{{$quote->contact->email}}</p>
                    </span>
                </div>
            </div>
            <br>
            <div class="clearfix">
                <div class="">
                    <table class="" style="width: 45%; float: left;">
                        <thead class="title-quote text-center header-table">
                            <tr >
                                <th class="unit"><b>Origin</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span id="origin_input" style="color: #787878;">
                                        @if($quote->origin_harbor_id!='')
                                        Port: {{$quote->origin_harbor->name}}, {{$quote->origin_harbor->code}}
                                        @endif
                                        @if($quote->origin_airport_id!='')
                                        Airport: {{$quote->origin_airport->name}}
                                        @endif
                                        <br>
                                        @if($quote->origin_address!='')
                                        Address: {{$quote->origin_address}}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="" style="width: 45%; float: right;">
                        <thead class="title-quote text-center ">
                            <tr>
                                <th class="unit"><b>Destination</b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span id="destination_input" style="color: #787878;">
                                        @if($quote->destination_harbor_id!='')
                                        Port: {{$quote->destination_harbor->name}}, {{$quote->destination_harbor->code}}
                                        @endif
                                        @if($quote->destination_airport_id!='')
                                        Airport: {{$quote->destination_airport->name}}
                                        @endif
                                        <br>
                                        @if($quote->destination_address!='')
                                        Address: {{$quote->destination_address}}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @if($quote->hide_carrier==false)
            <br>
            <div class="clearfix">
                <div class="client" style="color: #525F7F;">
                    <p class="title"><b>{{$quote->type==3 ? ' Airline':' Carrier'}}</b></p>
                    <hr style="margin-bottom:5px;margin-top:1px;">
                    @if($quote->carrier_id!='')
                    <p>{{$quote->carrier->name}}</p>
                    @endif
                    @if($quote->airline_id!='')
                    <p>{{$quote->airline->name}}</p>
                    @endif
                </div>
            </div>
            @endif
            <br>
            <div id="details" class="clearfix details">
                <div class="company" style="color: #1D3A6E;">
                    <p class="title"><b>Cargo details</b></p>
                    <hr style="margin-bottom:5px;margin-top:1px;border:1px solid #f1f1f1">
                    
                    @if($quote->qty_20 != '' || $quote->qty_40 != '' || $quote->qty_40_hc != '' || $quote->qty_45_hc != '' || $quote->qty_20_reefer != '' || $quote->qty_40_reefer != '' || $quote->qty_40_hc_reefer != '' || $quote->qty_20_open_top != '' || $quote->qty_40_open_top != '')
                        <table style="text-align: left !important;">
                            @if($quote->qty_20 != '' || $quote->qty_40 != '' || $quote->qty_40_hc != '' || $quote->qty_45_hc != '')
                                <tr>
                                    @if($quote->qty_20 != '')<td>{!! $quote->qty_20 != '' && $quote->qty_20 > 0 ? $quote->qty_20.' x 20\' container':'' !!}</td>@endif
                                    @if($quote->qty_40 != '')<td>{!! $quote->qty_40 != '' && $quote->qty_40 > 0 ? $quote->qty_40.' x 40\' container':'' !!}</td>@endif
                                    @if($quote->qty_40_hc != '')<td>{!! $quote->qty_40_hc != '' && $quote->qty_40_hc > 0 ? $quote->qty_40_hc.' x 40\' HC container':'' !!}</td>@endif
                                    @if($quote->qty_45_hc != '')<td>{!! $quote->qty_45_hc != '' && $quote->qty_45_hc > 0 ? $quote->qty_45_hc.' x 45\' HC container':'' !!}</td>@endif
                                </tr>
                            @endif
                            @if($quote->qty_20_reefer != '' || $quote->qty_40_reefer != '' || $quote->qty_40_hc_reefer != '')
                                <tr>
                                    @if($quote->qty_20_reefer != '')<td>{!! $quote->qty_20_reefer != '' &&  $quote->qty_20_reefer > 0 ? $quote->qty_20_reefer.' x 20\' Reefer container':'' !!}</td>@endif
                                    @if($quote->qty_40_reefer != '')<td>{!! $quote->qty_40_reefer != '' &&  $quote->qty_40_reefer > 0 ? $quote->qty_40_reefer.' x 40\' Reefer container':'' !!}</td>@endif
                                    @if($quote->qty_40_hc_reefer != '')<td>{!! $quote->qty_40_hc_reefer != '' &&  $quote->qty_40_hc_reefer > 0 ? $quote->qty_40_hc_reefer.' x 40\' HC Reefer container':'' !!}</td>@endif
                                </tr>
                            @endif
                            @if($quote->qty_20_open_top != '' || $quote->qty_40_open_top != '')
                                <tr>
                                    @if($quote->qty_20_open_top != '')<td>{!! $quote->qty_20_open_top != '' &&  $quote->qty_20_open_top > 0 ? $quote->qty_20_open_top.' x 20\' Open Top container':'' !!}</td>@endif
                                    @if($quote->qty_40_open_top != '')<td>{!! $quote->qty_40_open_top != '' &&  $quote->qty_40_open_top > 0 ? $quote->qty_40_open_top.' x 40\' Open Top container':'' !!}</td>@endif
                                </tr>
                            @endif
                        </table>
                    @endif

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
                    <br>
                    <div class="row">
                        <div class="col-md-12 pull-right">
                            <b>Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                        </div>
                    </div>
                    @endif
                    @if($quote->chargeable_weight!='' && $quote->chargeable_weight>0)
                        <div class="row">
                            <div class="col-md-12 ">
                                <b>Chargeable weight:</b> {{$quote->chargeable_weight}} kg
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <br>
            @if($charges_type==1)
            <table class="page-break" border="0" cellspacing="1" cellpadding="1">
                <thead class="title-quote text-center header-table">
                    <tr >
                        @if($quote->sub_total_origin!='')
                        <th class="unit"><b>Origin charges</b></th>
                        @endif
                        @if($quote->sub_total_freight!='')
                        <th class="unit"><b>Freight charges</b></th>
                        @endif
                        @if($quote->sub_total_destination!='')
                        <th class="unit"><b>Destination charges</b></th>
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
            <p class="title">Origin charges</p>
            <br>
            <table border="0" cellspacing="1" cellpadding="1">
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit"><b>Charge</b></th>
                        <th class="unit"><b>Detail</b></th>
                        <th class="unit"><b>Units</b></th>
                        <th class="unit"><b>Price per units</b></th>
                        <th class="unit"><b>Total </b></th>
                        <th class="unit"><b>Total &nbsp;{{$quote->currencies->alphacode}}</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($origin_ammounts as $origin_ammount)
                    <tr class="text-center color-table">
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
                        <td style="font-size: 12px; color: #01194F"><b>Total ammount</b></td>
                        <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_origin}} &nbsp;{{$quote->currencies->alphacode}}</b></td>
                    </tr>
                </tfoot>
            </table>
            @endif
            @if(count($freight_ammounts)>0)
            <p class="title">Freight charges</p>
            <br>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr >
                        <th class="unit"><b>Charge</b></th>
                        <th class="unit"><b>Detail</b></th>
                        <th class="unit"><b>Units</b></th>
                        <th class="unit"><b>Price per units</b></th>
                        <th class="unit"><b>Total</b></th>
                        <th class="unit"><b>Total &nbsp;{{$quote->currencies->alphacode}}</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($freight_ammounts as $freight_ammount)
                    <tr class="text-center color-table">
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
                        <td style="font-size: 12px; color: #01194F"><b>Total Ammount</b></td>
                        <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_freight}} &nbsp;{{$quote->currencies->alphacode}}</b></td>
                    </tr>
                </tfoot>
            </table>
            @endif
            @if(count($destination_ammounts)>0)
            <p class="title">Destination charges</p>
            <br>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Charge</b></th>
                        <th class="unit"><b>Detail</b></th>
                        <th class="unit"><b>Units</b></th>
                        <th class="unit"><b>Price per units</b></th>
                        <th class="unit"><b>Total</b></th>
                        <th class="unit"><b>Total &nbsp;{{$quote->currencies->alphacode}}</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($destination_ammounts as $destination_ammount)
                    <tr class="text-center color-table">
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
                        <td style="font-size: 12px; color: #01194F"><b>Total Ammount</b></td>
                        <td style="font-size: 12px; color: #01194F"><b>{{$quote->sub_total_destination}} 
                            &nbsp; {{$quote->currencies->alphacode}}</b></td>
                    </tr>
                </tfoot>
            </table>
            @endif
            @endif
        </main>
        @if($charges_type!=1)
        <div class="clearfix details page-break">
            <div class="company">
                <p class="title text-center pull-right total"><b>Total: {{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}} &nbsp;{{$quote->currencies->alphacode}}</b></p>
            </div>
        </div>
        @endif
        <div class="clearfix">
            <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote header-table">
                    <tr>
                        <th class="text-left"><b>&nbsp;&nbsp;&nbsp;Terms and Conditions</b></th>
                    </tr>
                </thead>
                <tbody>

                    @if($quote->term!='')
                        <tr>
                            <td style="padding:20px;">
                                <span class="text-justify">                                
                                    {!! $quote->term !!}
                                </span>
                            </td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>
        @if($quote->payment_conditions!='')
        <div class="clearfix">
            <table class="table-border" border="0" cellspacing="0" cellpadding="0">
                <thead class="title-quote header-table">
                    <tr>
                        <th class="unit text-left"><b>&nbsp;&nbsp;&nbsp;Payment conditions</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:20px;">
                            <span class="text-justify">{!! $quote->payment_conditions!!}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        <!--<footer>
Cargofive &copy; {{date('Y')}}
</footer>-->
    </body>
</html>
