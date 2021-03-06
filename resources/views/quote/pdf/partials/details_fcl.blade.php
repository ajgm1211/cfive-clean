            <div id="details" class="clearfix details">
                <!-- Company -->
                <div class="company" style="float: left; width: 350px; line-height: 10px;">

                    <!-- Logo -->
                    @if(isset($quote->company) && $quote->company->logo!='')
                
                        <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive img-fluid" style="width: 150px; height: auto; margin-bottom:20px">
                                        
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
                <!-- End Company -->

                <!--only Client -->
                @if(@$quote->company->business_name=='')
                    <div style="line-height: 10px; width:350px" class="incoterm" >

                        <div style="visibility: hidden">

                                @if(isset($quote->company) && $quote->company->logo!='')

                                    <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive img-fluid" style="width: 150px; height: auto; margin-bottom:20px">
                                    
                                @endif

                            </div>

                            <p><b>{{__('pdf.from')}}: </b>{{@$quote->user->name}} {{@$quote->user->lastname}}</p>

                            <p style="line-height:10px;">{{@$quote->user->email}}</p>

                            @if($delegation != null)
                                
                                <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{@$delegation->name}}</b></span></p>

                                @if(isset($user->companyUser->options['company_address_pdf']) && $user->companyUser->options['company_address_pdf']==1)

                                    <p style="line-height:10px;">{{@$delegation->address}}</p>

                                    <p style="line-height:10px;">{{@$delegation->phone}}</p>

                                @endif
                            @else
                                <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{@$user->companyUser->name}}</b></span></p>

                                @if(isset($user->companyUser->options['company_address_pdf']) && $user->companyUser->options['company_address_pdf']==1)

                                    <p style="line-height:10px;">{{@$user->companyUser->address}}</p>

                                    <p style="line-height:10px;">{{@$user->companyUser->phone}}</p>

                                @endif
                            @endif


                    </div>
                        <!-- End only Client -->
                @else
                    <!-- Client -->
                    <div class="client" style="line-height: 10px; width:350px; float:right">

                        <div style="visibility: hidden">

                            @if(isset($quote->company) && $quote->company->logo!='')

                                <img src="{{Storage::disk('s3_upload')->url($quote->company->logo)}}" class="img img-responsive img-fluid" style="width: 150px; height: auto; margin-bottom:20px">
                                
                            @endif

                        </div>
                    
                        <p><b>{{__('pdf.from')}}: </b>{{@$quote->user->name}} {{@$quote->user->lastname}}</p>
                    
                        <p style="line-height:10px;">{{@$quote->user->email}}</p>
                    
                        @if($delegation != null)
                                
                            <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{@$delegation->name}}</b></span></p>

                            @if(isset($user->companyUser->options['company_address_pdf']) && $user->companyUser->options['company_address_pdf']==1)

                                <p style="line-height:10px;">{{@$delegation->address}}</p>

                                <p style="line-height:10px;">{{@$delegation->phone}}</p>

                            @endif
                        @else
                            <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{@$user->companyUser->name}}</b></span></p>

                            @if(isset($user->companyUser->options['company_address_pdf']) && $user->companyUser->options['company_address_pdf']==1)
                                
                                <p style="line-height:10px;">{{@$user->companyUser->address}}</p>

                                <p style="line-height:10px;">{{@$user->companyUser->phone}}</p>

                            @endif
                        @endif
                    
                    </div>
                @endif
                <!-- End Client -->
            </div>

            <div class="row">
                <div style="float: left; margin-left:15px;">
                    <!-- <p class="color-title" ><b>{{__('pdf.validity')}}: </b>{{\Carbon\Carbon::parse( $quote->validity_start)->format('d/m/Y') }} - {{\Carbon\Carbon::parse( $quote->validity_end)->format('d/m/Y') }}</p> -->
                    <p class="color-title" style="text-align: justify"><b {{$quote->payment_conditions ? '':'hidden'}}>{{__('pdf.payment_conditions')}}: </b>{{ $quote->payment_conditions }}</p>
                </div>
            </div>

            @if($quote->incoterm_id !='' || $quote->custom_incoterm !='' || $quote->kind_of_cargo !='' || $quote->commodity !='' || $quote->risk_level !='')

                <div style="height: 30px" class="incoterm" >

                    <div style="float: left">
                        <p>

                            @if($quote->incoterm_id!='' || $quote->custom_incoterm!='')
                                
                                <span><b>Incoterm:</b> </span>{{@$quote->incoterm->name}} - {{$quote->custom_incoterm}}  |
                            
                            @endif
                            
                            @if($quote->kind_of_cargo!='')

                                <span><b>{{__('pdf.kind_of_cargo')}}:</b></span> {{$quote->kind_of_cargo}} |

                            @endif 
                            
                            @if($quote->commodity!='')
                            
                                <span><b>{{__('pdf.commodity')}}:</b></span> {{$quote->commodity}} |
                            
                            @endif 
                            
                            @if($quote->risk_level!='')
                            
                                <span><b>{{__('pdf.risk_level')}}:</b></span> {{$quote->risk_level}} |
                            
                            @endif  
                            
                            @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1)

                                <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> 
                            
                            @endif
                        </p>

                    </div>

                </div>
                
            @endif
            <br>