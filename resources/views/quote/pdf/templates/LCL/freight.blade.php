        <!-- HEADER -->
        @include('quote.pdf.partials.header')
        <!-- DETAILS -->
        @include('quote.pdf.partials.details_lcl')
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
        <!-- TERMS -->
        @include('quote.pdf.partials.terms')
