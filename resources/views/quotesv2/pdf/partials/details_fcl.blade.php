            <div id="details" class="clearfix details">

                <!-- Company -->
                <div class="company" style="float: left; width: 350px; line-height: 10px;">

                    <!-- Logo -->   
                    @if($quote->pdf_option->show_logo==1)
                        @if(isset($quote->company) && $quote->company->logo!='')

                            <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive" width="115" height="auto" style="margin-bottom:20px">
                        
                        @endif
                    @endif
                    <!-- End Logo -->   

                    <!-- Client Name -->
                    @if($quote->company_id!='')
                        
                        <p><b>{{__('pdf.to')}}:</b> {{@$quote->contact->first_name.' '.@$quote->contact->last_name}}</p>
                    
                    @endif
                    <!-- End Client Name -->

                    <!-- Client Email -->
                    <p style="line-height:10px;">{{@$quote->contact->email}}</p>
                    <!-- End Client Email -->
                    <!-- Company Name -->
                    @if(strlen(@$quote->company->business_name)>49)
                        <p style="line-height:12px; text-align:justify;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                    @else
                        <p style="line-height:10px;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                    @endif
                    <!-- End Company Name -->

                    <!-- Company Address -->
                    @if(strlen(@$quote->company->address)>49)
                        <p style="line-height:12px; text-align:justify;">{{@$quote->company->address}}</p>
                    @else
                        <p style="line-height:10px;">{{@$quote->company->address}}</p>
                    @endif
                    <!-- End Company Address -->

                    <p style="line-height:10px;">{{@$quote->contact->phone}}</p>
                   

                </div>
                <!-- End Company -->

                <!-- Client -->
                <div class="client" style="line-height: 10px; width:300px; float:right">

                    <p><b>{{__('pdf.from')}}: </b>{{@$quote->user->name}} {{@$quote->user->lastname}}</p>
                    <p style="line-height:10px;">{{@$quote->user->email}}</p>
                    <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                    <p style="line-height:10px;">{{@$user->companyUser->address}}</p>
                    <p style="line-height:10px;">{{@$user->phone}}</p>
                    <p class="color-title"><b>{{__('pdf.validity')}}:</b> {{\Carbon\Carbon::parse( $quote->validity_start)->format('d/M/Y') }} -  {{\Carbon\Carbon::parse( $quote->validity_end)->format('d/M/Y') }}</p>
                
                </div>
                <!-- End Client -->

            </div>


            @if($quote->incoterm !='' || $quote->kind_of_cargo !='' || $quote->commodity !='' || $quote->risk_level !='')

                <div style="margin-top: 25px;">

                    @if($quote->incoterm_id!='')
                        <p><span><b>Incoterm:</b> </span>{{@$quote->incoterm->name}}</p>
                    @endif
                
                    <p>
                        @if($quote->kind_of_cargo!='')

                            <span><b>{{__('pdf.kind_of_cargo')}}:</b></span> {{$quote->kind_of_cargo}} 

                        @endif 
                        
                        @if($quote->commodity!='')
                        
                            | <span><b>{{__('pdf.commodity')}}:</b></span> {{$quote->commodity}}
                        
                        @endif 
                        
                        @if($quote->risk_level!='')
                        
                            | <span><b>{{__('pdf.risk_level')}}:</b></span> {{$quote->risk_level}}
                        
                        @endif 
                        
                        @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1)

                            <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> 
                        
                        @endif
                    </p>
                </div>

            @endif

            <br>

            @if(($quote->delivery_type==2 || $quote->delivery_type==3 || $quote->delivery_type==4) && ($quote->origin_address!='' || $quote->destination_address!=''))

                <div>

                    @if($quote->origin_address!='')
                    
                        <p><span class="title" >{{__('pdf.origin_address')}}: </span>{{@$quote->origin_address}}</p>
                    
                    @endif
                    
                    @if($quote->destination_address!='')
                    
                        <p><span class="title" >{{__('pdf.destination_address')}}: </span>{{@$quote->destination_address}}</p>
                        
                    @endif
                
                </div>
            
            @endif

            <br>