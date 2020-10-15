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

<body style="background-color: white; font-size: 11px;">


    <main style="padding: 50px 50px 30px 50px">
        <!-- HEADER -->
        @include('quote.pdf.partials.header')
        <!-- DETAILS -->
        @include('quote.pdf.partials.details_fcl')
        <!-- FREIGHTS -->
        @if($freight_charges->count()>1)
            @include('quote.pdf.partials.freights_fcl')
        @else
            @include('quote.pdf.partials.detail_freights_fcl')
        @endif
        <!-- ORIGIN -->
        @include('quote.pdf.partials.origins_fcl')
        <!-- DESTINY -->
        @include('quote.pdf.partials.destinations_fcl')
        <!-- TERMS -->
        @include('quote.pdf.partials.terms')
        <!-- REMARKS -->
        @include('quote.pdf.partials.remarks')
    </main>


    <div class="footer-page" style="background: {{ @$user->companyUser->colors_pdf }}"></div>

</body>

</html>