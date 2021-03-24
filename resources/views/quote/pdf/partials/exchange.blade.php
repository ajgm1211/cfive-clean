<div class="clearfix">

    <span class="title text-left" style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{__('pdf.exchange')}}</b><br><br/></span>

    <div style="width:50%">
        <table border="0" cellspacing="1" cellpadding="1">

            <!-- Table Header -->
            <thead class="title-quote text-left header-table" >

                <tr>

                    <th class="unit" ><b>{{__('pdf.currency')}}</b></th>

                    <th class="unit"><b>{{__('pdf.exchange')}} {{ @$quote->pdf_options['totalsCurrency']['alphacode'] }}</b></th>
                    
                </tr>

            </thead>
            <!-- End Table Header -->

            <!-- Table Body -->
            <tbody>
                
                @foreach($quote->pdf_options['exchangeRates'] as $exchange)
                    @if($exchange['alphacode'] != $quote->pdf_options['totalsCurrency']['alphacode'])
                        <tr class="text-left color-table">

                            <td>{{ $exchange['alphacode'] }}</td>

                            <td>{{ $quote->pdf_options['totalsCurrency']['alphacode'] == 'EUR' ? $exchange['exchangeEUR'] : $exchange['exchangeUSD'] }}</td>

                        </tr>
                    @endif
                @endforeach

            </tbody>
            <!-- End Table Body -->

            </table>
    </div>  

</div>
<br>
