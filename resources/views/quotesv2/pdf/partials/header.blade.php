        @switch($quote->pdf_option->language)
            @case('English')
                {{ App::setLocale('en') }}
                @break
            @case('Spanish')
                {{ App::setLocale('es') }}
                @break
            @case('Portuguese')
                {{ App::setLocale('pt') }}
                @break
            @case('Italian')
                {{ App::setLocale('it') }}
                @break
            @case('Catalan')
                {{ App::setLocale('ca') }}
                @break
            @case('French')
                {{ App::setLocale('fr') }}
                @break
            @default
                {{ App::setLocale('en') }}
        @endswitch
        <header class="clearfix" style="margin-top:-25px; margin-bottom:-10px">
            
            <!-- Info Date -->
            <div id="company" style="float: left;">

                <div>
                    <span class="color-title uppercase"><b>{{__('pdf.quote_id')}}:</b></span> 
                    <span style="color: {{ $user->companyUser->colors_pdf }}"><b>{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</b></span>
                </div>

                <div>
                    <span class="color-title uppercase"><b>{{__('pdf.date_issue')}}:</b></span> {{date_format($quote->created_at, 'd/M/Y')}}
                </div>

            </div>
            <!-- End Info Date -->

            <!-- Logo -->
            <div id="logo">

                @if($user->companyUser->logo!='')

                    <img src="{{Storage::disk('s3_upload')->url($user->companyUser->logo)}}" class="img img-fluid" style="width: 150px; height: auto; margin-bottom:0">
               
                @endif
                
            </div>
            <!-- End Logo -->

            

        </header>

        <hr style="border-width: 2px; border-color: {{ $user->companyUser->colors_pdf }};">