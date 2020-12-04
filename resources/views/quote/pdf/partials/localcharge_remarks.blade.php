
@if(!empty($quote->localcharge_remarks) && $quote->localcharge_remarks!=='<br>')

    <div class="clearfix">

        <span class="title text-left" style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.localcharge_remarks')}}</b><br><br/></span>
        @if(trim(strip_tags($quote->localcharge_remarks)) !== '')
            <span>{!! str_replace('&nbsp;', ' ', $quote->localcharge_remarks) !!}</span>
        @endif

    </div>
    <br>
@endif