        <!-- HEADER -->
        @include('quote.pdf.partials.header')
        <!-- DETAILS -->
        @include('quote.pdf.partials.details_lcl')
        <!-- ORIGIN -->
        @include('quote.pdf.partials.origins_lcl')
        <!-- LOCALCHARGE REMARKS -->
        @if($destination_charges->count()==0)
            @include('quote.pdf.partials.localcharge_remarks')
        @endif
        <!-- FREIGHTS -->
        @if($freight_charges->count()>1 || ($freight_charges->count()==1 && @$quote->pdf_options['allIn']))
            @include('quote.pdf.partials.freights_lcl')
        @else
            @include('quote.pdf.partials.detail_freights_lcl')
        @endif
        <!-- REMARKS -->
        @include('quote.pdf.partials.remarks')
        <!-- DESTINY -->
        @include('quote.pdf.partials.destinations_lcl')
        <!-- LOCALCHARGE REMARKS -->
        @if($destination_charges->count()>0)
            @include('quote.pdf.partials.localcharge_remarks')
        @endif
        <!-- TERMS -->
        @include('quote.pdf.partials.terms')
