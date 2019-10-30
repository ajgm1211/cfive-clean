<div id="details" class="clearfix details">
                <div class="client" style="line-height: 10px; width:300px;">
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>From:</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>De:</p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>A partir de:</p>
                    <span id="destination_input" style="line-height: 0.5">
                        <p style="line-height:10px;">{{@$quote->user->name}} {{@$quote->user->lastname}}</p>
                        <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                        <p style="line-height:10px;">{{@$user->companyUser->address}}</p>
                        <p style="line-height:10px;">{{@$user->phone}}</p>
                        <p style="line-height:10px;">{{@$quote->user->email}}</p>
                    </span>
                </div>
                <div class="company text-right" style="float: right; width: 350px; line-height: 10px;">
                    @if($quote->company_id!='')
                    <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>To:</p>
                    <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Para:</b></p>
                    <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Para:</b></p>
                    @endif
                    <span id="destination_input" style="line-height: 0.5">
                        @if($quote->pdf_option->show_logo==1)
                        @if(isset($quote->company) && $quote->company->logo!='')
                        <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px">
                        @endif
                        @endif
                        <p style="line-height:10px;">{{@$quote->contact->first_name.' '.@$quote->contact->last_name}}</p>
                        @if(strlen(@$quote->company->business_name)>49)
                            <p style="line-height:12px; text-align:justify;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                        @else
                            <p style="line-height:10px;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                        @endif
                        @if(strlen(@$quote->company->address)>49)
                            <p style="line-height:12px; text-align:justify;">{{@$quote->company->address}}</p>
                        @else
                            <p style="line-height:10px;">{{@$quote->company->address}}</p>
                        @endif
                        <p style="line-height:10px;">{{@$quote->contact->phone}}</p>
                        <p style="line-height:10px;">{{@$quote->contact->email}}</p>
                    </span>
                </div>
            </div>
            @if($quote->incoterm!='' || $quote->kind_of_cargo!='' || $quote->commodity!='' || $quote->risk_level!='')
                <div style="margin-top: 25px;">
                    @if($quote->incoterm_id!='')<p><span class="title" >Incoterm: </span>{{@$quote->incoterm->name}}</p>@endif
                    <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Kind of cargo:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Commodity:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Risk level:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                    <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercancía:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nivel de riesgo:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                    <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercadoria:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nível de risco:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                </div>
            @endif
            <br>