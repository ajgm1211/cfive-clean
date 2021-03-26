@if($quote->terms_and_conditions!='' || $quote->terms_english!='' || $quote->terms_portuguese!='')
    <div class="clearfix">

        <span class="text-left" style="font-size: 14px !important; color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.terms')}}</b></span>

        <div style="width:100%">
            @switch($quote->language_id)
            
                @case(1)
                    <span style="word-wrap: break-word; text-align: justify;">{!! trim(@$quote->terms_english) !!}</span>
                @break

                @case(2)
                    <span style="word-wrap: break-word; text-align: justify;">{!! trim(@$quote->terms_and_conditions)  !!}</span>
                @break

                @case(3)
                    <span style="word-wrap: break-word; text-align: justify;">{!! trim(@$quote->terms_portuguese) !!}</span>
                @break

                @default
                    <span style="word-wrap: break-word; text-align: justify;">{!! trim(@$quote->terms_and_conditions)  !!}</span>

            @endswitch
        </div>  
          
    </div>
    <br>
@endif
