            <div id="details" class="clearfix details">

                <!-- Company -->
                <div class="company" style="float: left; width: 350px; line-height: 10px;">

                    <div style="visibility: hidden">

                        @if($quote->pdf_option->show_logo==1)
                            @if(isset($quote->company) && $quote->company->logo!='')

                                <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive img-fluid" style="width: 150px; height: auto; margin-bottom:20px">
                            
                            @endif
                        @endif

                    </div>
                   
                    <p><b>{{__('pdf.from')}}: </b>{{@$quote->user->name}} {{@$quote->user->lastname}}</p>
                   
                    <p style="line-height:10px;">{{@$quote->user->email}}</p>
                   
                    <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                    
                    <p style="line-height:10px;">{{@$user->companyUser->address}}</p>
                    
                    <p style="line-height:10px;">{{@$user->phone}}</p>
                   

                </div>
                <!-- End Company -->

                <!-- Client -->
                <div class="client" style="line-height: 10px; width:350px; float:right">

                    <!-- Logo -->   
                    @if($quote->pdf_option->show_logo==1)
                        @if(isset($quote->company) && $quote->company->logo!='')
                
                            <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive img-fluid" style="width: 150px; height: auto; margin-bottom:20px">
                                        
                        @endif
                    @endif
                    <!-- End Logo -->   
                
                    <!-- Client Name -->
                    @if($quote->company_id!='')
                                        
                        <p><b>{{__('pdf.to')}}:</b> {{@$quote->contact->first_name.' '.@$quote->contact->last_name}}</p>
                                    
                    @endif
                
                    <!-- Client Email -->
                    <p style="line-height:10px;">{{@$quote->contact->email}}</p>
                
                    <!-- Company Name -->
                    @if(strlen(@$quote->company->business_name)>49)
                                    
                        <p style="line-height:12px; text-align:justify;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                                    
                    @else
                                       
                        <p style="line-height:10px;"><span style="color: #4e4e4e"><b>{{@$quote->company->business_name}}</b></span></p>
                                    
                    @endif
                
                    <!-- Company Address -->
                    @if(strlen(@$quote->company->address)>49)
                
                                        
                        <p style="line-height:12px; text-align:justify;">{{@$quote->company->address}}</p>
                                   
                    @else
                                       
                        <p style="line-height:10px;">{{@$quote->company->address}}</p>
                                    
                    @endif
                                    
                    <!-- Company Phone -->
                    <p style="line-height:10px;">{{@$quote->contact->phone}}</p>

                
                </div>
                <!-- End Client -->

            </div>


            @if($quote->incoterm !='' || $quote->kind_of_cargo !='' || $quote->commodity !='' || $quote->risk_level !='' || $quote->validity_end != '')

                <div style="margin-top: 25px; height: 50px" class="incoterm" >

                    <div style="float: left">
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

                    <div style="float: right">

                        <p class="color-title" ><b class="uppercase">{{__('pdf.validity')}}: </b>{{\Carbon\Carbon::parse( $quote->validity_end)->format('d/m/Y') }}</p>

                    </div>

                </div>

            @endif

            <br>

            @if(($quote->delivery_type==2 || $quote->delivery_type==3 || $quote->delivery_type==4) && ($quote->origin_address!='' || $quote->destination_address!=''))

                <div  class="incoterm">

                    @if($quote->origin_address!='')
                    
                        <p><span><b>{{__('pdf.origin_address')}}: </b></span>{{@$quote->origin_address}}</p>
                    
                    @endif
                    
                    @if($quote->destination_address!='')
                    
                        <p><span><b>{{__('pdf.destination_address')}}: </b></span>{{@$quote->destination_address}}</p>
                        
                    @endif
                
                </div>
            
            @endif

            <br>