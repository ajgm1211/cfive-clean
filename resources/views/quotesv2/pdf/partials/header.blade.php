        @switch($quote->pdf_option->language)
            @case('English')
                {{ App::setLocale('en') }}
                @break
            @case('Spanish')
                {{ App::setLocale('es') }}
                @break
            @case('Portuguese')
                {{ App::setLocale('pt') }}
                @break
            @default
                {{ App::setLocale('en') }}
        @endswitch
        <header class="clearfix" style="margin-top:-25px; margin-bottom:-10px">
            <div id="logo">
                @if($user->companyUser->logo!='')
                    <img src="{{Storage::disk('s3_upload')->url($user->companyUser->logo)}}" class="img img-fluid" style="width: 150px; height: auto; margin-bottom:0">
                @endif
            </div>
            <div id="company">
                <div>
                    <span class="color-title"><b>@if($quote->pdf_option->language=='English')Quotation Id:@elseif($quote->pdf_option->language=='Spanish') Cotización: @else Numero de cotação: @endif</b></span> 
                    <span style="color: #20A7EE"><b>{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</b></span>
                </div>
                <div>
                    <span class="color-title"><b>@if($quote->pdf_option->language=='English')Date of issue:@elseif($quote->pdf_option->language=='Spanish') Fecha creación: @else Data de emissão: @endif</b></span> {{date_format($quote->created_at, 'M d, Y H:i')}}
                </div>
                @if($quote->validity_start!='' && $quote->validity_end!='')
                    <div>
                        <span class="color-title"><b>@if($quote->pdf_option->language=='English')Validity:@elseif($quote->pdf_option->language=='Spanish') Validez: @else Validade: @endif </b></span>{{\Carbon\Carbon::parse( $quote->validity_start)->format('d M Y') }} -  {{\Carbon\Carbon::parse( $quote->validity_end)->format('d M Y') }}
                    </div>
                @endif
            </div>
            <hr>
        </header>