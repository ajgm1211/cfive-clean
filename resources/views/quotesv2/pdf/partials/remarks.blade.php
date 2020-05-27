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
            @if($rate->remarks != '')
                <span class="text-justify">{!! $rate->remarks !!}</span><br/>
            @endif
            @switch($quote->pdf_option->language)
                @case("English")
                    <span class="text-justify">{!! $rate->remarks_english !!}</span>
                    @break
                @case("Portuguese")
                    <span class="text-justify">{!! $rate->remarks_portuguese !!}</span>
                    @break
                @case("Spanish")
                    <span class="text-justify">{!! $rate->remarks_spanish !!}</span>
                    @break
            @endswitch
        @endforeach
    </div>
@endif
<br>