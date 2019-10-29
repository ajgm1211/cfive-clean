@if($quote->terms_and_conditions!='' || $quote->terms_english!='' || $quote->terms_portuguese!='')
    <div class="clearfix">
        <table class="table-border table-no-split" border="0" cellspacing="0" cellpadding="0">
            <thead class="title-quote header-table">
                <tr>
                    @switch($quote->pdf_option->language)
                        @case("English")
                            <th class="unit text-left" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Terms and conditions</b></th>
                        @break
                        @case("Portuguese")
                            <th class="unit text-left" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Termos e Condições</b></th>
                        @break
                        @case("Spanish")
                            <th class="unit text-left" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>&nbsp;&nbsp;&nbsp;Términos y condiciones</b></th>
                        @break
                    @endswitch
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:15px;">
                        @switch($quote->pdf_option->language)
                            @case("English")
                                <span class="text-justify">{!! @$quote->terms_english != "" ? @$quote->terms_english:@$quote->terms_and_conditions !!}</span>
                            @break

                            @case("Portuguese")
                                <span class="text-justify">{!! @$quote->terms_portuguese != "" ? @$quote->terms_portuguese:@$quote->terms_and_conditions !!}</span>
                            @break

                            @case("Spanish")
                                <span class="text-justify">{!! @$quote->terms_and_conditions  !!}</span>
                            @break

                            @default
                                <span class="text-justify">{!! @$quote->terms_and_conditions  !!}</span>
                        @endswitch
                    </td>
                </tr>                        
            </tbody>
        </table>
    </div>
@endif
<br>