        <div id="details" class="clearfix details">
            <div class="client" style="line-height: 10px; width:300px;">
                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>From:</p>
                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>De:</p>
                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>A partir de:</p>
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
                <p class="title" {{$quote->pdf_option->language=='English' ? '':'hidden'}}>To:</p>
                <p class="title" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Para:</b></p>
                <p class="title" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Para:</b></p>
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
                <p {{$quote->pdf_option->language=='English' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Kind of cargo:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Commodity:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Risk level:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                <p {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercancía:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nivel de riesgo:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
                <p {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}>@if($quote->kind_of_cargo!='')<span class="title" >Tipo de carga:</span> {{$quote->kind_of_cargo}} @endif @if($quote->commodity!='')| <span class="title" >Mercadoria:</span> {{$quote->commodity}}@endif @if($quote->risk_level!='')| <span class="title" >Nível de risco:</span> {{$quote->risk_level}}@endif @if($quote->kind_of_cargo=='Pharma' && $quote->gdp==1) <img src="{{asset('images/logogdp.jpg')}}" class="img img-responsive" width="50" height="auto"> @endif</p>
            </div>
        @endif      
        <br>
        <div class="company" style="color: #1D3A6E;">
            <p class="title"><b>Cargo details</b></p>
            <br>
            @if(!empty($package_loads) && count($package_loads)>0)
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Cargo type</b></th>
                        <th class="unit"><b>Quantity</b></th>
                        <th class="unit"><b>Height</b></th>
                        <th class="unit"><b>Width</b></th>
                        <th class="unit"><b>Large</b></th>
                        <th class="unit"><b>Weight</b></th>
                        <th class="unit"><b>Total weight</b></th>
                        <th class="unit"><b>Volume</b></th>
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
                        <b class="title">Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                    </div>
                </div>
                @if($quote->chargeable_weight!='' && $quote->chargeable_weight>0)
                  <div class="row">
                      <div class="col-md-12 ">
                          <b class="title">Chargeable weight:</b> {{$quote->chargeable_weight}} kg
                      </div>
                  </div>
                @endif
            @else
                <table border="0" cellspacing="1" cellpadding="1">
                  <thead class="title-quote text-center header-table">
                    <tr>
                        <th class="unit"><b>Cargo type</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total quantity</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Cantidad total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Quantidade total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total weight</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Peso total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Peso total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Total volume</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Volumen total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Volume total</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='English' ? '':'hidden'}}><b>Chargeable weight</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Spanish' ? '':'hidden'}}><b>Peso tasable</b></th>
                        <th class="unit" {{$quote->pdf_option->language=='Portuguese' ? '':'hidden'}}><b>Peso carregável</b></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="text-center">
                      <td>{{$quote->cargo_type == 1 ? 'Pallets' : 'Packages'}}</td>
                      <td>{{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</td>
                      <td>{{$quote->total_weight != '' ? $quote->total_weight.' Kg' : ''}}</td>
                      <td>{!!$quote->total_volume != '' ? $quote->total_volume.' m<sup>3</sup>' : ''!!}</td>
                      <td>{{$quote->chargeable_weight}} kg</td>
                    </tr>
                  </tbody>
                </table>
            @endif 
        </div>
        <br>