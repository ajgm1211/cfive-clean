        <!-- HEADER -->
        @include('quote.pdf.partials.header')
        <!-- DETAILS -->
        @include('quote.pdf.partials.details_lcl')
        <!-- TOTALS -->
        @if($freight_charges->count()>=1 && @$quote->pdf_options['showTotals'])
            @include('quote.pdf.partials.totals_lcl')
        @endif
        <!-- FREIGHTS -->
        @if($freight_charges->count()>1 || ($freight_charges->count()==1 && @$quote->pdf_options['allIn']))
            @include('quote.pdf.partials.freights_lcl')
        @else
            @include('quote.pdf.partials.detail_freights_lcl')
        @endif
        <!-- REMARKS -->
        @include('quote.pdf.partials.remarks')
        <!-- ORIGIN -->
        @include('quote.pdf.partials.origins_lcl')
        <!-- DESTINY -->
        @include('quote.pdf.partials.destinations_lcl')
        <!-- LOCALCHARGE REMARKS -->
        @include('quote.pdf.partials.localcharge_remarks')
        <!-- EXCHANGE RATE -->
        @if($freight_charges->count()>=1 && @$quote->pdf_options['showTotals'])
            @include('quote.pdf.partials.exchange')
        @endif
        <!-- TERMS -->
        @include('quote.pdf.partials.terms')
