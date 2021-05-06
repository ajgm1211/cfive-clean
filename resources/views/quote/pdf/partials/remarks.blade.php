@php
$remarkG =  strip_tags($quote->remarks);
$remarkE =  strip_tags($quote->remarks_english);
$remarkS =  strip_tags($quote->remarks_spanish);
$remarkP =  strip_tags($quote->remarks_portuguese);

$remarkG = trim(str_replace("&nbsp;", '', $remarkG));
$remarkE = trim(str_replace("&nbsp;", '', $remarkE));
$remarkS = trim(str_replace("&nbsp;", '', $remarkS));
$remarkP = trim(str_replace("&nbsp;", '', $remarkP));

@endphp

@if((!empty($quote->remarkG) ) || (!empty($remarkE) ) || (!empty($remarkS)) || (!empty($remarkP) ))
    <div class="clearfix">
        <p class="text-left" style="font-size: 12px !important;"><b>{{__('pdf.remarks')}}</b></p>
            @switch($quote->language_id)
                @case(1)
                    @if(trim(strip_tags($quote->remarks_english)) !== '')
                        <span style="text-align: justify">{!! str_replace('&nbsp;', ' ', $quote->remarks_english) !!}</span>
                    @endif
                @break
                @case(2)
                    @if(trim(strip_tags($quote->remarks_spanish)) !== '')
                        <span style="text-align: justify">{!! str_replace('&nbsp;', ' ',$quote->remarks_spanish) !!}</span>
                    @endif
                @break
                @case(3)
                    @if(trim(strip_tags($quote->remarks_portuguese)) !== '')
                        <span style="text-align: justify">{!! str_replace('&nbsp;', ' ', $quote->remarks_portuguese) !!}</span>
                    @endif
                @break
            @endswitch
    </div>
@endif
