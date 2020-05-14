@if($quote->terms_and_conditions!='' || $quote->terms_english!='' || $quote->terms_portuguese!='')
    <div class="clearfix">
        <span class="color-title text-left"><b>{{__('pdf.terms')}}</b><br><br/></span>

        <div style="width:100%">
            @switch($quote->pdf_option->language)
                @case("English")
                    <span class="text-justify" style="word-wrap: break-word;">{!! @$quote->terms_english != "" ? trim(@$quote->terms_english):trim(@$quote->terms_and_conditions) !!}</span>
                    @break

                @case("Portuguese")
                    <span class="text-justify" style="word-wrap: break-word;">{!! @$quote->terms_portuguese != "" ? trim(@$quote->terms_portuguese):trim(@$quote->terms_and_conditions) !!}</span>
                    @break

                @case("Spanish")
                    <span class="text-justify" style="word-wrap: break-word;">{!! trim(@$quote->terms_and_conditions)  !!}</span>
                    @break

                @default
                    <span class="text-justify" style="word-wrap: break-word;">{!! trim(@$quote->terms_and_conditions)  !!}</span>
            @endswitch
        </div>    
    </div>
@endif
<br>