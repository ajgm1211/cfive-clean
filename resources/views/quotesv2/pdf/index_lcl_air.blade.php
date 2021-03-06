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
            @include('quotesv2.pdf.partials.header')

            <!-- DETAILS -->
            @include('quotesv2.pdf.partials.details_lcl')

            <!-- ALL IN -->
            @include('quotesv2.pdf.partials.all_in_lcl')

            <!-- FREIGHTS -->
            @include('quotesv2.pdf.partials.freights_lcl')

            <!-- ORIGINS -->
            @include('quotesv2.pdf.partials.origins_lcl')

            <!-- DESTINATIONS -->
            @include('quotesv2.pdf.partials.destinations_lcl')

            <!-- REMARKS -->
            @include('quotesv2.pdf.partials.remarks')

            <!-- TERMS AND CONDITIONS -->
            @include('quotesv2.pdf.partials.terms_and_conditions')

            <!-- PAYMENTS CONDITIONS -->
            @include('quotesv2.pdf.partials.payments_conditions')

            <!-- FOOTER -->
            @include('quotesv2.pdf.partials.footer')       
        </main>

        <div class="footer-page" style="background: {{ $user->companyUser->colors_pdf }}"></div>
        
    </body>
</html>