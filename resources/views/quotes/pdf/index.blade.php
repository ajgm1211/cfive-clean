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
                    <div class="panel-heading title">Origin</div>
                    <div class="panel-body">
                        <span id="origin_input" style="color: #1D3A6E;">
                            {{$origin_harbor->name}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="company" style="float: right; width: 350px;">
                <div class="panel panel-default" style="width: 350px; height: 83px;">
                    <div class="panel-heading title">Destination</div>
                    <div class="panel-body">
                        <span id="destination_input" style="color: #1D3A6E;">
                            {{$destination_harbor->name}}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div id="details" class="clearfix details">
            <hr>
            <div class="company" style="color: #1D3A6E;">
                <h3 class="title"><b>Cargo details:</b></h3>
                <p>{!! $quote->qty_20 != '' ? $quote->qty_20.' x 20\' container':'' !!}</p>
                <p>{!! $quote->qty_40 != '' ? $quote->qty_40.' x 40\' container':'' !!}</p>
                <p>{!! $quote->qty_40_hc != '' ? $quote->qty_40_hc.' x 40\' HC container':'' !!}</p>
            </div>
        </div>
        <hr>
        <h3 class="title">Origin charges:</h3>
        <br>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead >
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
                    <td class="white">{{$origin_ammount->price_per_unit}}</td>
                    <td class="white">{{$origin_ammount->total_ammount + ($origin_ammount->markup/$origin_ammount->units)}} {{$origin_ammount->currency->alphacode}}</td>
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
        <h3 class="title">Freight charges:</h3>
        <br>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead class="text-center">
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
                    <td>{{$freight_ammount->price_per_unit}}</td>
                    <td>{{$freight_ammount->total_ammount + ($freight_ammount->markup/$freight_ammount->units)}} {{$freight_ammount->currency->alphacode}}</td>
                    <td>{{$freight_ammount->total_ammount_2}}</td>
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
        <h3 class="title">Destination charges:</h3>
        <br>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
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
                    <td>{{$destination_ammount->price_per_unit}}</td>
                    <td>{{$destination_ammount->total_ammount + ($destination_ammount->markup/$destination_ammount->units)}} {{$destination_ammount->currency->alphacode}}</td>
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
    </main>
    <div class="clearfix details">
        <hr>
        <div class="company">
            <h3 class="title text-right" style="color: #01194F;"><b>TOTAL: {{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}} &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></h3>
        </div>
    </div>
    <footer>
        Cargofive &copy; {{date('Y')}}
    </footer>
</body>
</html>
