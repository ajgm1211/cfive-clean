
@if(!empty($quote->localcharge_remarks) && $quote->localcharge_remarks!=='<br>')

    <div class="clearfix">

        <span class="text-left" style="font-size: 14px !important; color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.localcharge_remarks')}}</b></span>
        @if(trim(strip_tags($quote->localcharge_remarks)) !== '')
            <span style="text-align: justify">{!! str_replace('&nbsp;', ' ', $quote->localcharge_remarks) !!}</span>
        @endif

    </div>
    <br>
@endif