<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->id}}</title>
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
</head>
<body style="background-color: white;">
<header class="clearfix">
    <div id="logo">
        <img src="logo.png">
    </div>
    <div id="company">
        <h2 class="name">{{$quote->company->business_name}}</h2>
        <div>{{$quote->company->address}}</div>
        <div>{{$quote->company->phone}}</div>
        <div><a href="mailto:{{$quote->company->email}}">{{$quote->company->email}}</a></div>
    </div>
</header>
<main>
    <div id="details" class="clearfix details">
        <div class="client">
            <div class="panel panel-default" style="width: 300px;">
                <div class="panel-heading"><b>Origin</b></div>
                <div class="panel-body">
                    <span id="destination_input">
                        {{$origin_harbor->name}}
                    </span>
                </div>
            </div>
        </div>
        <div class="company" style="float: right; width: 300px;">
            <div class="panel panel-default" style="width: 300px;">
                <div class="panel-heading"><b>Destination</b></div>
                <div class="panel-body">
                    <span id="destination_input">
                        {{$destination_harbor->name}}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div id="details" class="clearfix details">
        <hr>
        <div class="client">
            <p><b>Quote #:</b> {{$quote->id}}</p>
            <p><b>Type:</b> {{$quote->type}}</p>
            <p><b>Incoterm:</b> {{$quote->incoterm}}</p>
            <p><b>Created:</b> {{$quote->created_at}}</p>
        </div>
        <div class="company" style="float: right; width: 300px;">
            <p><b>Cargo details:</b></p>
            <p>{!! $quote->qty_20 != '' ? $quote->qty_20.' x 20\' container':'' !!}</p>
            <p>{!! $quote->qty_40 != '' ? $quote->qty_40.' x 40\' container':'' !!}</p>
            <p>{!! $quote->qty_40_hc != '' ? $quote->qty_40_hc.' x 40\' HC container':'' !!}</p>
        </div>
    </div>
    <hr>
    <h1>Origin ammounts</h1>
    <br>
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="unit"><b>Charge</b></th>
            <th class="unit"><b>Detail</b></th>
            <th class="unit"><b>Units</b></th>
            <th class="unit"><b>Price per units</b></th>
            <th class="unit"><b>Markup</b></th>
            <th class="unit"><b>Total</b></th>
        </tr>
        </thead>
        <tbody>
        @foreach($origin_ammounts as $origin_ammount)
        <tr class="text-center">
            <td>{{$origin_ammount->charge}}</td>
            <td>{{$origin_ammount->detail}}</td>
            <td>{{$origin_ammount->units}}</td>
            <td>{{$origin_ammount->price_per_unit}}</td>
            <td>{{$origin_ammount->markup}}</td>
            <td>{{$origin_ammount->total_ammount}}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="text-center">
            <td colspan="4"></td>
            <td><b>SUBTOTAL</b></td>
            <td>$5,200.00</td>
        </tr>
        </tfoot>
    </table>
    <h1>Freight ammounts</h1>
    <br>
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="unit"><b>Charge</b></th>
            <th class="unit"><b>Detail</b></th>
            <th class="unit"><b>Units</b></th>
            <th class="unit"><b>Price per units</b></th>
            <th class="unit"><b>Markup</b></th>
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
                <td>{{$freight_ammount->markup}}</td>
                <td>{{$freight_ammount->total_ammount}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="text-center">
            <td colspan="4"></td>
            <td><b>SUBTOTAL</b></td>
            <td>$5,200.00</td>
        </tr>
        </tfoot>
    </table>
    <h1>Destination ammounts</h1>
    <br>
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="unit"><b>Charge</b></th>
            <th class="unit"><b>Detail</b></th>
            <th class="unit"><b>Units</b></th>
            <th class="unit"><b>Price per units</b></th>
            <th class="unit"><b>Markup</b></th>
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
                <td>{{$destination_ammount->markup}}</td>
                <td>{{$destination_ammount->total_ammount}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="text-center">
            <td colspan="4"></td>
            <td><b>SUBTOTAL</b></td>
            <td>$5,200.00</td>
        </tr>
        </tfoot>
    </table>
</main>
<footer>
    Cargofive &copy; {{date('Y')}}
</footer>
</body>
</html>
