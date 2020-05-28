        <div id="details" class="clearfix details">
            <div class="client" style="line-height: 10px; width:300px;">
                <p class="title">{{__('pdf.from')}}:</p>
                <span id="destination_input" style="line-height: 0.5">
                    <p style="line-height:10px;">{{$quote->user->name}} {{$quote->user->lastname}}</p>
                    <p style="line-height:12px;"><span style="color: #4e4e4e"><b>{{$user->companyUser->name}}</b></span></p>
                    <p style="line-height:10px;">{{$user->companyUser->address}}</p>
                    <p style="line-height:10px;">{{$user->phone}}</p>
                    <p style="line-height:10px;">{{$quote->user->email}}</p>
                </span>
            </div>
            <div class="company text-right" style="float: right; width: 350px; line-height: 10px;">
                @if($quote->company_id!='')
                <p class="title">{{__('pdf.to')}}:</p>
                @endif
                <span id="destination_input" style="line-height: 0.5">
                    @if($quote->pdf_option->show_logo==1)
                      @if($quote->company->logo!='')
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
                @if($quote->incoterm_id!='')<p><span class="title" >Incoterm: </span>{{$quote->incoterm->name}}</p>@endif
                <p>@if($quote->kind_of_cargo!='')<span class="title" >{{__('pdf.kind_cargo')}}:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >{{__('pdf.commodity')}}:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >{{__('pdf.risk_level')}}:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
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
        <div class="company" style="color: #1D3A6E;">
            <p class="title"><b>{{__('pdf.cargo_details')}}</b></p>
            <br>
            @if(!empty($package_loads) && count($package_loads)>0)
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>{{__('pdf.cargo_type')}}</b></th>
                        <th class="unit"><b>{{__('pdf.quantity')}}</b></th>
                        <th class="unit"><b>{{__('pdf.height')}}</b></th>
                        <th class="unit"><b>{{__('pdf.width')}}</b></th>
                        <th class="unit"><b>{{__('pdf.large')}}</b></th>
                        <th class="unit"><b>{{__('pdf.weight')}}</b></th>
                        <th class="unit"><b>{{__('pdf.total_weight')}}</b></th>
                        <th class="unit"><b>{{__('pdf.volume')}}</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($package_loads as $package_load)
                        <tr class="text-center">
                            <td>{{$package_load->type_cargo==1 ? 'Pallets':'Packages'}}</td>
                            <td>{{$package_load->quantity}}</td>
                            <td>{{$package_load->height}} cm</td>
                            <td>{{$package_load->width}} cm</td>
                            <td>{{$package_load->large}} cm</td>
                            <td>{{$package_load->weight}} kg</td>
                            <td>{{$package_load->total_weight}} kg</td>
                            <td>{{$package_load->volume}} m<sup>3</sup></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <br>
                <div class="row">
                    <div class="col-md-12 pull-right">
                        <b class="title">{{__('pdf.total')}}:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                    </div>
                </div>
                @if($quote->chargeable_weight!='' && $quote->chargeable_weight>0)
                  <div class="row">
                      <div class="col-md-12 ">
                          <b class="title">{{__('pdf.chargeable_weight')}}:</b> {{$quote->chargeable_weight}} m<sup>3</sup>
                      </div>
                  </div>
                @endif
            @else
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>{{__('pdf.cargo_type')}}</b></th>
                        <th class="unit"><b>{{__('pdf.total_quantity')}}</b></th>
                        <th class="unit"><b>{{__('pdf.total_weight')}}</b></th>
                        <th class="unit"><b>{{__('pdf.total_volume')}}</b></th>
                        <th class="unit"><b>{{__('pdf.chargeable_weight')}}</b></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="text-center">
                      <td>{{$quote->cargo_type == 1 ? 'Pallets' : 'Packages'}}</td>
                      <td>{{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</td>
                      <td>{{$quote->total_weight != '' ? $quote->total_weight.' Kg' : ''}}</td>
                      <td>{!!$quote->total_volume != '' ? $quote->total_volume.' m<sup>3</sup>' : ''!!}</td>
                      <td>{{$quote->chargeable_weight}} m<sup>3</sup></td>
                    </tr>
                  </tbody>
                </table>
            @endif 
        </div>
        <br>