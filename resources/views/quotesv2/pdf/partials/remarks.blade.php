<?php
    $i=0;
?>
@foreach($rates as $rate)
    @if(($rate->remarks != '' && $rate->remarks!='<br>') || ($rate->remarks_english!= '' && $rate->remarks_english!='<br>') || ($rate->remarks_portuguese!= '' && $rate->remarks_portuguese!='<br>') || ($rate->remarks_spanish!= '' && $rate->remarks_spanish!='<br>'))
        <?php
            $i++;
        ?>
    @endif
@endforeach

@if($i>0)    
    <br>
    <div class="clearfix">
        <span class="color-title text-left"><b>{{__('pdf.remarks')}}</b><br><br/></span>

        @foreach($rates as $rate)
            @if(trim(strip_tags($rate->remarks)) !== '')
                <span>{!! str_replace('&nbsp;', ' ',$rate->remarks) !!}</span><br/>
            @endif
            @switch($quote->pdf_option->language)
                @case("English")
                    @if(trim(strip_tags($rate->remarks_english)) !== '')
                        <span>{!! str_replace('&nbsp;', ' ', $rate->remarks_english) !!}</span>
                    @endif
                    @break
                @case("Portuguese")
                    @if(trim(strip_tags($rate->remarks_portuguese)) !== '')
                        <span>{!! str_replace('&nbsp;', ' ', $rate->remarks_portuguese) !!}</span>
                    @endif
                    @break
                @case("Spanish")
                    @if(trim(strip_tags($rate->remarks_spanish)) !== '')
                        <span>{!! str_replace('&nbsp;', ' ',$rate->remarks_spanish) !!}</span>
                    @endif
                    @break
            @endswitch
        @endforeach
    </div>
@endif
<br>