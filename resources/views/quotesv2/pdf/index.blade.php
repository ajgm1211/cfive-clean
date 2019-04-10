<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->quote_id}}</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
</head>
<body style="background-color: white; font-size: 11px;">
<header class="clearfix">
    <div id="logo">
        @if($user->companyUser->logo!='')
            <img src="{{Storage::disk('s3_upload')->url($user->companyUser->logo)}}" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:25px">
        @endif
    </div>
    <div id="company">
        <div><span class="color-title"><b>Quotation Id:</b></span> <span style="color: #20A7EE"><b>#{{$quote->custom_id == '' ? $quote->quote_id:$quote->custom_quote_id}}</b></span></div>
        <div><span class="color-title"><b>Date of issue:</b></span> {{date_format($quote->created_at, 'M d, Y H:i')}}</div>
        @if($quote->validity_start!=''&&$quote->validity_end!='')
            <div><span class="color-title"><b>Validity: </b></span> {{\Carbon\Carbon::parse( $quote->validity_start)->format('d M Y') }} -  {{\Carbon\Carbon::parse( $quote->validity_end)->format('d M Y') }}</div>
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
        <!--<div class="">
            <table class="" style="width: 45%; float: left;">
                <thead class="title-quote text-center header-table">
                <tr >
                    <th class="unit"><b>Origin</b></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div id="origin_input" style="color: #787878;">
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
                        </div>
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
                        <div id="destination_input" style="color: #787878;">
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
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>-->
    </div>
    <br>
    <table border="0" cellspacing="1" cellpadding="1">
        <thead class="title-quote text-center header-table">
        <tr >
            <th class="unit"><b>POL</b></th>
            <th class="unit"><b>POD</b></th>
            <th class="unit"><b>Carrier</b></th>
            <th class="unit"><b>20'</b></th>
            <th class="unit"><b>40' </b></th>
            <th class="unit"><b>40' HC</b></th>
            <th class="unit"><b>Currency</b></th>
        </tr>
        </thead>
        <tbody>
        <tr class="text-center color-table">
            <td class="">Barcelona, ESBCN</td>
            <td class="">Shanghai, CNSHA</td>
            <td class="">Maersk</td>
            <td class="">1000</td>
            <td class="">1000</td>
            <td class="">1000</td>
            <td class="">USD</td>
        </tr>
        </tbody>
        <tfoot>
    </table>
    <br>
    <p class="title">Local charges - Barcelona, ESBCN</p>
    <br>
    <table border="0" cellspacing="1" cellpadding="1">
        <thead class="title-quote text-center header-table">
        <tr >
            <th class="unit"><b>Charge</b></th>
            <th class="unit"><b>Detail</b></th>
            <th class="unit"><b>Carrier</b></th>
            <th class="unit"><b>20'</b></th>
            <th class="unit"><b>40' </b></th>
            <th class="unit"><b>40' HC</b></th>
            <th class="unit"><b>Currency</b></th>
        </tr>
        </thead>
        <tbody>
        <tr class="text-center color-table">
            <td class="">THC</td>
            <td class="">Per Container</td>
            <td class="">Maersk</td>
            <td class="">70</td>
            <td class="">100</td>
            <td class="">100</td>
            <td class="">EUR</td>
        </tr>
        </tbody>
        <tfoot class="footer" style="border-color: yellow">
        <tr class="title-quote text-center header-table" style="background-color: #00b3ee">
            <td colspan="2" style="font-size: 12px; color: #01194F"><b>Total local charges</b></td>
            <td></td>
            <td style="font-size: 12px; color: #01194F"><b>70</b></td>
            <td style="font-size: 12px; color: #01194F"><b>100</b></td>
            <td style="font-size: 12px; color: #01194F"><b>100</b></td>
            <td style="font-size: 12px; color: #01194F"><b>EUR</b></td>
        </tr>
        </tfoot>
    </table>
    <br>
    <p class="title">Local charges - Barcelona, ESBCN</p>
    <br>
    <table border="0" cellspacing="1" cellpadding="1">
        <thead class="title-quote text-center header-table">
        <tr >
            <th class="unit"><b>Charge</b></th>
            <th class="unit"><b>Detail</b></th>
            <th class="unit"><b>Carrier</b></th>
            <th class="unit"><b>20'</b></th>
            <th class="unit"><b>40' </b></th>
            <th class="unit"><b>40' HC</b></th>
            <th class="unit"><b>Currency</b></th>
        </tr>
        </thead>
        <tbody>
        <tr class="text-center color-table">
            <td class="">THC</td>
            <td class="">Per Container</td>
            <td class="">Maersk</td>
            <td class="">70</td>
            <td class="">100</td>
            <td class="">100</td>
            <td class="">EUR</td>
        </tr>
        </tbody>
        <tfoot>
        <tr class="text-center subtotal header-table">
            <td colspan="2" style="font-size: 12px; color: #01194F"><b>Total local charges</b></td>
            <td></td>
            <td style="font-size: 12px; color: #01194F"><b>70</b></td>
            <td style="font-size: 12px; color: #01194F"><b>100</b></td>
            <td style="font-size: 12px; color: #01194F"><b>100</b></td>
            <td style="font-size: 12px; color: #01194F"><b>EUR</b></td>
        </tr>
        </tfoot>
    </table>
    <br>
    <p class="title">Total estimated costs</p>
    <br>
    <table border="0" cellspacing="1" cellpadding="1">
        <thead class="title-quote text-center header-table">
        <tr >
            <th class="unit"><b>POL</b></th>
            <th class="unit"><b>POD</b></th>
            <th class="unit"><b>Carrier</b></th>
            <th class="unit"><b>20'</b></th>
            <th class="unit"><b>40' </b></th>
            <th class="unit"><b>40' HC</b></th>
            <th class="unit"><b>45'</b></th>
            <th class="unit"><b>Currency</b></th>
        </tr>
        </thead>
        <tbody>
        <tr class="text-center color-table">
            <td class="">Barcelona, ESBCN</td>
            <td class="">Shanghai, CNSHA</td>
            <td class="">Maersk</td>
            <td class="">1010</td>
            <td class="">1040</td>
            <td class="">1040</td>
            <td class="">1040</td>
            <td class="">EUR</td>
        </tr>
        </tbody>
    </table>
    <br>
</main>

</body>
</html>