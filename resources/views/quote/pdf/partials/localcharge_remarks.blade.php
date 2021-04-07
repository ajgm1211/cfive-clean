
@if(!empty($quote->localcharge_remarks) && $quote->localcharge_remarks!=='<br>')

    <div class="clearfix">
        <p class="text-left" style="font-size: 12px !important;"><b>{{__('pdf.localcharge_remarks')}}</b></p>
        @if(trim(strip_tags($quote->localcharge_remarks)) !== '')
            <span style="text-align: justify;">{!! str_replace('&nbsp;', ' ', $quote->localcharge_remarks) !!}</span>
        @endif

    </div>
    <br>
@endif