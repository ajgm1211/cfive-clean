        <!-- HEADER -->
        @include('quote.pdf.partials.header')
        <!-- DETAILS -->
        @include('quote.pdf.partials.details_fcl')
        <!-- TOTALS -->
        @if($freight_charges->count()>=1 && @$quote->pdf_options['showTotals'])
            @include('quote.pdf.partials.totals_fcl')
        @endif
        <!-- FREIGHTS -->
        @if($freight_charges->count()>1 || ($freight_charges->count()==1 && @$quote->pdf_options['allIn']))
            @include('quote.pdf.partials.freights_fcl')
        @else
            @include('quote.pdf.partials.detail_freights_fcl')
        @endif
        <!-- REMARKS -->
        @include('quote.pdf.partials.remarks')
        <!-- ORIGIN -->
        @include('quote.pdf.partials.origins_fcl')
        <!-- LOCALCHARGE REMARKS -->
        @if($destination_charges->count()==0)
            @include('quote.pdf.partials.localcharge_remarks')
        @endif        
        <!-- DESTINY -->
        @include('quote.pdf.partials.destinations_fcl')
        <!-- LOCALCHARGE REMARKS -->
        @if($destination_charges->count()>0)
            @include('quote.pdf.partials.localcharge_remarks')
        @endif
        <!-- EXCHANGE RATE -->
        @if($freight_charges->count()>=1 && @$quote->pdf_options['showTotals'])
            @include('quote.pdf.partials.exchange')
        @endif
        <!-- TERMS -->
        @include('quote.pdf.partials.terms')