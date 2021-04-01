
@if((!empty($quote->remarks) && $quote->remarks!=='<br>') || (!empty($quote->remarks_english) && $quote->remarks_english!=='<br>') || (!empty($quote->remarks_spanish) && $quote->remarks_spanish!=='<br>') || (!empty($quote->remarks_portuguese) && $quote->remarks_portuguese!=='<br>'))

    <div class="clearfix">

        <span class="text-left" style="font-size: 14px !important; color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.remarks')}}</b></span>

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
