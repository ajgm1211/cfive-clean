
@if($quote->remarks_english != '' || $quote->remarks_spanish != '' || $quote->remarks_portuguese != '')

    <div class="clearfix">

        <span class="title text-left" style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.remarks')}}</b><br><br/></span>

            @switch($quote->language_id)

                @case(1)
                    @if(trim(strip_tags($quote->remarks_english)) !== '')
                        <span>{!! str_replace('&nbsp;', ' ', $quote->remarks_english) !!}</span>
                    @endif
                @break

                @case(2)
                    @if(trim(strip_tags($quote->remarks_spanish)) !== '')
                        <span>{!! str_replace('&nbsp;', ' ',$quote->remarks_spanish) !!}</span>
                    @endif
                @break

                @case(3)
                    @if(trim(strip_tags($quote->remarks_portuguese)) !== '')
                        <span>{!! str_replace('&nbsp;', ' ', $quote->remarks_portuguese) !!}</span>
                    @endif
                @break
            @endswitch

    </div>
@endif
<br>