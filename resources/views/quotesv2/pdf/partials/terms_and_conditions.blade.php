@if($quote->terms_and_conditions!='' || $quote->terms_english!='' || $quote->terms_portuguese!='')
    <div class="clearfix terms-conditions">

        <span class="title text-left" style="color: {{ $user->companyUser->colors_pdf }}"><b>{{__('pdf.terms')}}</b></span>

        <div style="width:100%">
            @switch($quote->pdf_option->language)
            
                @case("English")
                    <span style="word-wrap: break-word;">{!! @$quote->terms_english != "" ? trim(@$quote->terms_english):trim(@$quote->terms_and_conditions) !!}</span>
                @break

                @case("Portuguese")
                    <span style="word-wrap: break-word;">{!! @$quote->terms_portuguese != "" ? trim(@$quote->terms_portuguese):trim(@$quote->terms_and_conditions) !!}</span>
                @break

                @case("Spanish")
                    <span style="word-wrap: break-word;" >{!! trim(@$quote->terms_and_conditions)  !!}</span>
                @break

                @case("Italian")
                    <span style="word-wrap: break-word;" >{!! trim(@$quote->terms_italian)  !!}</span>
                @break

                @case("Catalan")
                    <span style="word-wrap: break-word;" >{!! trim(@$quote->terms_catalan)  !!}</span>
                @break

                @case("French")
                    <span style="word-wrap: break-word;" >{!! trim(@$quote->terms_french)  !!}</span>
                @break

                @default

                    <span style="word-wrap: break-word;">{!! trim(@$quote->terms_and_conditions)  !!}</span>

            @endswitch
        </div>  
          
    </div>
@endif
<br>