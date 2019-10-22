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
        <!-- HEADER -->
        @include('quotesv2.pdf.partials.header')

        <main>
            <!-- DETAILS -->
            @include('quotesv2.pdf.partials.details_fcl') 

            <!-- ALL IN  -->
            @include('quotesv2.pdf.partials.all_in_fcl')

            <!-- FREIGHTS -->
            @include('quotesv2.pdf.partials.freights_fcl')

            <!-- ORIGINS -->
            @include('quotesv2.pdf.partials.origins_fcl')

            <!-- DESTINATIONS -->
            @include('quotesv2.pdf.partials.destinations_fcl')

            <!-- REMARKS -->
            @include('quotesv2.pdf.partials.remarks')
            
            <!-- TERMS AND CONDITIONS -->
            @include('quotesv2.pdf.partials.terms_and_conditions')
            
            <!-- PAYMENTS CONDITIONS -->
            @include('quotesv2.pdf.partials.payments_conditions')
        </main>
        <!-- FOOTER -->
        @include('quotesv2.pdf.partials.footer')
    </body>
</html>