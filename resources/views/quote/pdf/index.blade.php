<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quote #{{$quote->quote_id}}</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" media="all" />
    <link rel="stylesheet" href="{{asset('css/style-pdf.css')}}" media="all" />
    <style>

    </style>
</head>

<body style="background-color: white; font-size: 11px; margin: 50px 50px 50px 50px">


    <main>
        <!-- HEADER -->
        @include('quote.pdf.partials.header')
        <!-- DETAILS -->
        @include('quote.pdf.partials.details_fcl')
        <!-- TOTALS -->
        @if($freight_charges->count()>=1 && @$quote->pdf_options['showTotals'])
            @include('quote.pdf.partials.totals')
        @endif
        <!-- ORIGIN -->
        @include('quote.pdf.partials.origins_fcl')
        <!-- FREIGHTS -->
        @if($freight_charges->count()>1 || ($freight_charges->count()==1 && @$quote->pdf_options['allIn']))
            @include('quote.pdf.partials.freights_fcl')
        @else
            @include('quote.pdf.partials.detail_freights_fcl')
        @endif
        <!-- REMARKS -->
        @include('quote.pdf.partials.remarks')
        <!-- DESTINY -->
        @include('quote.pdf.partials.destinations_fcl')
        <!-- LOCALCHARGE REMARKS -->
        @include('quote.pdf.partials.localcharge_remarks')
        <!-- TERMS -->
        @include('quote.pdf.partials.terms')
    </main>


    <div class="footer-page" style="background: {{ @$user->companyUser->colors_pdf }}"></div>

</body>

</html>