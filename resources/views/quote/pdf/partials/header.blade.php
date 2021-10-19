@switch($quote->language_id)
    @case(1)
        {{ App::setLocale('en') }}
    @break
    @case(2)
        {{ App::setLocale('es') }}
    @break
    @case(3)
        {{ App::setLocale('pt') }}
    @break
    @default
        {{ App::setLocale('en') }}
@endswitch
<header class="clearfix" style="margin-top:-25px; margin-bottom:-10px">
    @if(@$user->companyUser->pdf_template_id!=2)
        <!-- Info Date -->
        <div id="company" style="float: left;">

            <div>
                <span class="color-title uppercase"><b>{{__('pdf.quote_id')}}:</b></span>
                <span
                    style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</b></span>
            </div>

            <div>
                <span class="color-title uppercase"><b>{{__('pdf.date_issue')}}:</b></span>
                {{date_format($quote->created_at, 'd/m/Y')}}
            </div>
            <div>
                <span class="color-title uppercase" ><b>{{__('pdf.validity')}}: </b></span>{{\Carbon\Carbon::parse( $quote->validity_start)->format('d/m/Y') }} - {{\Carbon\Carbon::parse( $quote->validity_end)->format('d/m/Y') }}
            </div>

        </div>
    @endif 

    @if(@$user->companyUser->pdf_template_id==2)
        @if(@$user->companyUser->header_type=='image')
            <div class="clearfix">
                <img src="{{Storage::disk('s3_upload')->url(@$user->companyUser->header_image)}}" class="img img-fluid" style="max-width:100%;">
            </div>
            <br>
        @endif
        <div id="company" style="float: left;">
            <div>
                <span class="color-title uppercase"><b>{{__('pdf.quote_id')}}:</b></span>
                <span
                    style="color: {{ @$user->companyUser->colors_pdf }}"><b>{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</b></span>
            </div>

            <div>
                <span class="color-title uppercase"><b>{{__('pdf.date_issue')}}:</b></span>
                {{date_format($quote->created_at, 'd/m/Y')}}
            </div>
        </div>
        <br>
        <div id="company" style="float: right;">    
            <div>
                <span class="color-title uppercase" ><b>{{__('pdf.validity')}}: </b></span>{{\Carbon\Carbon::parse( $quote->validity_start)->format('d/m/Y') }} - {{\Carbon\Carbon::parse( $quote->validity_end)->format('d/m/Y') }}
            </div>
        </div>
    @endif    

    <!-- End Info Date -->

    <!-- Logo -->
    @if(@$user->companyUser->pdf_template_id!=2)
        <div id="logo">

<<<<<<< HEAD
        @if(@$user->companyUser->logo!='')
=======
            @if(@$user->companyUser->logo!='')
>>>>>>> master

            <img src="{{Storage::disk('s3')->url(@$user->companyUser->logo)}}" class="img img-fluid"
                style="width: 150px; height: auto; margin-bottom:0">

            @endif

        </div>
    @endif
    <!-- End Logo -->



</header>

<hr style="border-width: 2px; border-color: {{ @$user->companyUser->colors_pdf }};">