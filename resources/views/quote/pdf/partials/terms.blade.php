@if($quote->terms_and_conditions!='' || $quote->terms_english!='' || $quote->terms_portuguese!='')
    <div class="clearfix">

        <span class="title text-left" style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.terms')}}</b><br><br/></span>

        <div style="width:100%">
            @switch($quote->language_id)
            
                @case(1)
                    <span style="word-wrap: break-word;">{!! trim(@$quote->terms_english) !!}</span>
                @break

                @case(2)
                    <span style="word-wrap: break-word;">{!! trim(@$quote->terms_and_conditions)  !!}</span>
                @break

                @case(3)
                    <span style="word-wrap: break-word;">{!! trim(@$quote->terms_portuguese) !!}</span>
                @break

                @default
                    <span style="word-wrap: break-word;">{!! trim(@$quote->terms_and_conditions)  !!}</span>

            @endswitch
        </div>  
          
    </div>
    <br>
@endif
