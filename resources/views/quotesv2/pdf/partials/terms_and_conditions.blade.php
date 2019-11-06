@if($quote->terms_and_conditions!='' || $quote->terms_english!='' || $quote->terms_portuguese!='')
    <div class="clearfix">
        
        @switch($quote->pdf_option->language)
            @case("English")
                <span class="color-title text-left"><b>Terms and conditions</b><br><br/></span>
                @break
            @case("Portuguese")
                <span class="color-title text-left"><b>Termos e Condições</b><br><br/></span><br/>
                @break
            @case("Spanish")
                <span class="color-title text-left"><b>Términos y condiciones</b><br><br/></span><br/>
                @break
            @default
                <span class="color-title text-left"><b>Terms and conditions</b><br><br/></span><br/>
                @break
        @endswitch
        
        @switch($quote->pdf_option->language)
            @case("English")
                <span class="text-justify">{!! @$quote->terms_english != "" ? @$quote->terms_english:@$quote->terms_and_conditions !!}</span>
                @break

            @case("Portuguese")
                <span class="text-justify">{!! @$quote->terms_portuguese != "" ? @$quote->terms_portuguese:@$quote->terms_and_conditions !!}</span>
                @break

            @case("Spanish")
                <span class="text-justify">{!! @$quote->terms_and_conditions  !!}</span>
                @break

            @default
                <span class="text-justify">{!! @$quote->terms_and_conditions  !!}</span>
                @endswitch
                
    </div>
@endif
<br>