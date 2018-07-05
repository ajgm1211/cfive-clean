<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->id}}</title>
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
</head>
<body style="background-color: white; font-size: 11px;">
    <header class="clearfix">
        <div id="logo">
            <img src="logo.png">
        </div>
        <div id="company">
            <div><b>Quotation Id</b> <span style="color: gold"><b>#{{$quote->id}}</b></span></div>
            <div><b>Date of issue:</b> {{$quote->created_at}}</div>
            <div><b>Validity: </b>{{$quote->created_at}}</div>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix details">
            <div class="client">
                <div class="panel panel-default" style="width: 300px;">
                    <div class="panel-heading title" style="color:#000">From:</div>
                    <div class="panel-body">
                        <span id="destination_input" style="line-height: 0.4">
                            <p>{{$user->name}}</p>
                            <p><b>{{$user->companyUser->name}}</b></p>
                            <p>{{$user->companyUser->address}}</p>
                            <p>{{$user->companyUser->phone}}</p>
                        </span>
                    </div>
                </div>
            </div>
            <div class="company text-right" style="float: right; width: 150px;">
                <div class="panel panel-default" >
                    <div class="panel-heading title" style="color:#000">To:</div>
                    <div class="panel-body">
                        <span id="destination_input" style="line-height: 0.4">
                            <p>Alejandro Gonzalez</p>
                            <p><b>Maersk</b></p>
                            <p>Caracas, Venezuela</p>
                            <p>+580424156820</p>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div id="details" class="clearfix details">
            <div class="client" style="width: 350px;">
                <div >
                    <table border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="unit" ><b>Origin</b></th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr class="text-center">
                                <td>{{$origin_harbor->name}}</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
            <div class="company" style="float: right; width: 350px;">
                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th class="unit"><b>Destination</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>{{$destination_harbor->name}}</td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>
        </div>
        <div id="details" class="clearfix details">
            <hr>
            <div class="company">
                <h3 class="title"><b>Cargo details:</b></h3>
                <p>{!! $quote->qty_20 != '' ? $quote->qty_20.' x 20\' container':'' !!}</p>
                <p>{!! $quote->qty_40 != '' ? $quote->qty_40.' x 40\' container':'' !!}</p>
                <p>{!! $quote->qty_40_hc != '' ? $quote->qty_40_hc.' x 40\' HC container':'' !!}</p>
            </div>
        </div>
        <hr>
        <h3 class="title">Origin ammounts</h3>
        <br>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead >
                <tr >
                    <th class="unit"><b>Charge</b></th>
                    <th class="unit"><b>Detail</b></th>
                    <th class="unit"><b>Units</b></th>
                    <th class="unit"><b>Price per units</b></th>
                    <th class="unit"><b>Total</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($origin_ammounts as $origin_ammount)
                <tr class="text-center">
                    <td class="white">{{$origin_ammount->charge}}</td>
                    <td class="white">{{$origin_ammount->detail}}</td>
                    <td class="white">{{$origin_ammount->units}}</td>
                    <td class="white">{{$origin_ammount->price_per_unit}}</td>
                    <td class="white">{{$origin_ammount->total_ammount}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="text-center subtotal">
                    <td colspan="4"></td>
                    <td style="font-size: 12px;"><b>SUBTOTAL</b></td>
                    <td style="font-size: 12px;">{{$quote->sub_total_origin}} USD</td>
                </tr>
            </tfoot>
        </table>
        <h3 class="title">Freight ammounts</h3>
        <br>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead class="text-center">
                <tr >
                    <th class="unit"><b>Charge</b></th>
                    <th class="unit"><b>Detail</b></th>
                    <th class="unit"><b>Units</b></th>
                    <th class="unit"><b>Price per units</b></th>
                    <th class="unit"><b>Total</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($freight_ammounts as $freight_ammount)
                <tr class="text-center">
                    <td>{{$freight_ammount->charge}}</td>
                    <td>{{$freight_ammount->detail}}</td>
                    <td>{{$freight_ammount->units}}</td>
                    <td>{{$freight_ammount->price_per_unit}}</td>
                    <td>{{$freight_ammount->total_ammount}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="text-center" style="font-size: 12px;">
                    <td colspan="4"></td>
                    <td style="font-size: 12px;"><b>SUBTOTAL</b></td>
                    <td style="font-size: 12px;">{{$quote->sub_total_freight}} USD</td>
                </tr>
            </tfoot>
        </table>
        <h3 class="title">Destination ammounts</h3>
        <br>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="unit"><b>Charge</b></th>
                    <th class="unit"><b>Detail</b></th>
                    <th class="unit"><b>Units</b></th>
                    <th class="unit"><b>Price per units</b></th>
                    <th class="unit"><b>Total</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach($destination_ammounts as $destination_ammount)
                <tr class="text-center">
                    <td>{{$destination_ammount->charge}}</td>
                    <td>{{$destination_ammount->detail}}</td>
                    <td>{{$destination_ammount->units}}</td>
                    <td>{{$destination_ammount->price_per_unit}}</td>
                    <td>{{$destination_ammount->total_ammount}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <td colspan="4"></td>
                    <td style="font-size: 12px;"><b>SUBTOTAL</b></td>
                    <td style="font-size: 12px;">{{$quote->sub_total_destination}} USD</td>
                </tr>
            </tfoot>
        </table>
    </main>
    <div class="clearfix details">
        <hr>
        <div class="company">
            <h3 class="title"><b>TOTAL: {{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}} USD</b></h3>
        </div>
    </div>
    <footer>
        Cargofive &copy; {{date('Y')}}
    </footer>
</body>
</html>
