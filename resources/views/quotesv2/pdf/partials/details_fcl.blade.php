<div id="details" class="clearfix details">
                <div class="client" style="line-height: 10px; width:300px;">
                    <p class="title">{{__('pdf.from')}}</p>
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
                    <p class="title">{{__('pdf.to')}}</p>
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
                    <p>@if($quote->kind_of_cargo!='')<span class="title" >{{__('pdf.kind_of_cargo')}}</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >{{__('pdf.commodity')}}:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >{{__('pdf.risk_level')}}:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                </div>
            @endif
            <br>
            @if(($quote->delivery_type==2 || $quote->delivery_type==3 || $quote->delivery_type==4) && ($quote->origin_address!='' || $quote->destination_address!=''))
                <div>
                    @if($quote->origin_address!='')<p><span class="title" >{{__('pdf.origin_address')}}: </span>{{@$quote->origin_address}}</p>@endif
                    @if($quote->destination_address!='')<p><span class="title" >{{__('pdf.destination_address')}}: </span>{{@$quote->destination_address}}</p>@endif
                </div>
            @endif
            <br>