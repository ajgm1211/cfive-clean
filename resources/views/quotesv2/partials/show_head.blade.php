        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left" role="tablist" style="border-bottom: none;">
                    <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
                    <li class="nav-item m-tabs__item size-14px" >
                        <a  href="{{url('/v2/quotes/search')}}" class="btn-backto"><span class="fa fa-arrow-left"></span> Back to search</a>
                    </li>                    
                </ul>                
                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right" role="tablist" style="border-bottom: none;">
                    <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
                    <li class="nav-item m-tabs__item" >
                        <button class="btn btn-primary-v2" data-toggle="modal" data-target="#SendQuoteModal">
                            Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                        </button>
                    </li>
                    <li class="nav-item m-tabs__item" >
                        @if($quote->type=='FCL')
                          <a class="btn btn-primary-v2" href="{{route('quotes-v2.pdf',setearRouteKey($quote->id))}}" target="_blank">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                          </a>
                        @else
                          <a class="btn btn-primary-v2" href="{{route('quotes-v2.pdf.lcl.air',setearRouteKey($quote->id))}}" target="_blank">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                          </a>
                        @endif
                    </li>
                    <li class="nav-item m-tabs__item" >
                        <a class="btn btn-primary-v2" href="{{route('quotes-v2.duplicate',setearRouteKey($quote->id))}}">
                            Duplicate &nbsp;&nbsp;<i class="fa fa-plus"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Quote details -->
            <div class="col-md-12">
                <div class="m-portlet custom-portlet no-border">
                    <div class="m-portlet__head">
                      <div class="row" style="padding-top: 20px;">
                        <h3 class="title-quote size-14px">Quote info</h3>
                      </div>
                      <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                          <li class="nav-item m-tabs__item" id="edit_li">
                            <a class="btn btn-primary-v2 btn-edit" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                              Edit &nbsp;&nbsp;<i class="fa fa-pencil"></i>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content">
                            <div class="row quote-info-mb">
                                <div class="col-md-4">
                                    <input type="text" value="{{$quote->id}}" class="form-control id" hidden >
                                    <input type="text" id="currency_id" value="{{$currency_cfg->alphacode}}" class="form-control id" hidden >
                                    <label class="title-quote"><b>Quotation ID:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}" class="form-control quote_id" hidden >
                                    <span class="quote_id_span">{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="{{$quote->quote_id}}" class="form-control" hidden >
                                    {{ Form::select('type',['FCL'=>'FCL','LCL'=>'LCL','AIR'=>'AIR'],$quote->type,['class'=>'form-control quote-type select2','hidden','disabled']) }}
                                    <span class="type_span">{{$quote->type}}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Company:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('company_id',$companies,$quote->company_id,['class'=>'form-control company_id select2','hidden']) }}
                                    <span class="company_span">{{$quote->company->business_name}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Status:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('status',['Draft'=>'Draft','Win'=>'Win','Sent'=>'Sent'],$quote->status,['class'=>'form-control status select2','hidden','']) }}
                                    <span class="status_span Status_{{$quote->status}}" style="border-radius: 10px;">{{$quote->status}} <i class="fa fa-check"></i></span>
                                </div>
                                <div class="col-md-4" {{$quote->type!='AIR' ? '':'hidden'}}>
                                    <br>
                                    <label class="title-quote"><b>Destination type:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('status',[1=>'Port to Port',2=>'Port to Door',3=>'Door to Port',4=>'Door to Door'],$quote->delivery_type,['class'=>'form-control delivery_type select2','hidden','']) }}
                                    <span class="delivery_type_span">
                                        @if($quote->delivery_type==1)
                                            Port to Port
                                        @elseif($quote->delivery_type==2)
                                            Port to Door
                                        @elseif($quote->delivery_type==3)
                                            Door to Port
                                        @else
                                            Door to Door
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-4" {{$quote->type=='AIR' ? '':'hidden'}}>
                                    <br>
                                    <label class="title-quote"><b>Destination type:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('status',[1=>'Airport to Airport',2=>'Airport to Door',3=>'Door to Airport',4=>'Door to Door'],$quote->delivery_type,['class'=>'form-control delivery_type select2','hidden','']) }}
                                    <span class="delivery_type_span">
                                        @if($quote->delivery_type==1)
                                            Airport to Airport
                                        @elseif($quote->delivery_type==2)
                                            Airport to Door
                                        @elseif($quote->delivery_type==3)
                                            Door to Airport
                                        @else
                                            Door to Door
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Contact:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('contact_id',$contacts,$quote->contact_id,['class'=>'form-control contact_id select2','hidden','id'=>'contact_id']) }}
                                    <span class="contact_id_span">{{@$quote->contact->first_name}} {{@$quote->contact->last_name}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Date issued:&nbsp;&nbsp;</b></label>
                                    @php
                                        $date = date_create($quote->date_issued);
                                    @endphp
                                    <span class="date_issued_span">{{date_format($date, 'M d, Y H:i')}}</span>
                                    {!! Form::text('created_at', date_format($date, 'Y-m-d H:i'), ['placeholder' => 'Validity','class' => 'form-control m-input date_issued','readonly'=>true,'required' => 'required','hidden']) !!}
                                </div>                              
                                <div class="col-md-4" >
                                    <br>
                                    <label class="title-quote"><b>Equipment:&nbsp;&nbsp;</b></label>
                                    <span class="equipment_span">
                                        @if($quote->type=='FCL')
                                            @if($quote->equipment!='')
                                                <?php
                                                    $equipment=json_decode($quote->equipment);
                                                ?>
                                                @foreach($equipment as $item)
                                                    {{$item}}@unless($loop->last),@endunless
                                                @endforeach
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                    {{ Form::select('equipment[]',['20' => '20','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],@$equipment,['class'=>'form-control equipment','multiple' => 'multiple','required' => 'true','hidden','disabled']) }}
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Price level:&nbsp;&nbsp;</b></label>
                                    <span class="price_level_span">{{@$quote->price->name}}</span>
                                    {{ Form::select('price_id',$prices,@$quote->price_id,['class'=>'form-control price_id select2','hidden']) }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Validity:&nbsp;&nbsp;</b></label>
                                    <span class="validity_span">{{$quote->validity_start}} / {{$quote->validity_end}}</span>
                                    @php
                                        $validity = $quote->validity_start ." / ". $quote->validity_end;
                                    @endphp
                                    {!! Form::text('validity_date', $validity, ['placeholder' => 'Validity','class' => 'form-control m-input validity','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required','hidden']) !!}
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Incoterm:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('incoterm_id',$incoterms,$quote->incoterm_id,['class'=>'form-control incoterm_id select2','hidden','']) }}
                                    <span class="incoterm_id_span">{{$quote->incoterm->name}}</span>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('user_id',$users,$quote->user_id,['class'=>'form-control user_id select2','hidden']) }}
                                    <span class="user_id_span">{{$quote->user->name}} {{$quote->user->lastname}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Kind of cargo:&nbsp;&nbsp;</b></label>
                                    <span class="kind_of_cargo_span">{{$quote->kind_of_cargo}}</span>
                                    {{ Form::select('user_id',[''=>'Select an option','General'=>'General','Perishable'=>'Perishable','Dangerous'=>'Dangerous','Valuable Cargo'=>'Valuable Cargo','All Live Animals'=>'All Live Animals','Human Remains'=>'Human Remains','Pharma'=>'Pharma'],$quote->kind_of_cargo,['class'=>'form-control kind_of_cargo select2','hidden']) }}
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Commodity:&nbsp;&nbsp;</b></label>
                                    <span class="commodity_span">{{$quote->commodity}}</span>
                                    {!! Form::text('commodity', $quote->commodity, ['placeholder' => 'Commodity','class' => 'form-control m-input commodity','hidden']) !!}
                                </div>
                                <div class="col-md-4 div_gdp" {{$quote->kind_of_cargo=='Pharma' ? '':'hidden'}}>
                                    <br>
                                    <label class="title-quote" ><b>GDP:&nbsp;&nbsp;</b></label>
                                    <span class="gdp_span">{{$quote->gdp==1 ? 'Yes':'No'}}</span>
                                    {{ Form::select('gdp',['1'=>'Yes','2'=>'No'],$quote->gdp,['class'=>'form-control gdp select2','hidden','placeholder'=>'Select an option']) }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 div_risk_level" {{$quote->gdp==1 ? '':'hidden'}}>
                                    <br>
                                    <label class="title-quote"><b>Risk level:&nbsp;&nbsp;</b></label>
                                    <span class="risk_level_span">{{$quote->risk_level}}</span>
                                    {!! Form::text('risk_level', $quote->risk_level, ['placeholder' => 'Risk Level','class' => 'form-control m-input risk_level','hidden']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center" id="update_buttons" hidden>
                                    <br>
                                    <hr>
                                    <br>
                                    <a class="btn btn-danger" id="cancel" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </a>
                                    <a class="btn btn-primary" id="update" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         @if($quote->type!='FCL')
            <div class="row">
                <div class="col-md-12">
                    <div class="m-portlet custom-portlet no-border">
                        <div class="m-portlet__body " style="color: #1d3a6e !important;">
                            @if(!empty($package_loads) && count($package_loads)>0)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover table color-blue text-center">
                                                <thead class="title-quote text-center header-table">
                                                    <tr style="height: 40px;">
                                                      <td class="td-table" style="padding-left: 30px">Cargo type</td>
                                                      <td class="td-table">Quantity</td>
                                                      <td class="td-table">Height</td>
                                                      <td class="td-table">Width</td>
                                                      <td class="td-table">Large</td>
                                                      <td class="td-table">Weight</td>
                                                      <td class="td-table">Total weight</td>
                                                      <td class="td-table">Volume</td>
                                                    </tr>
                                                </thead>
                                                <tbody style="background-color: white;">
                                                    @foreach($package_loads as $package_load)
                                                    <tr class="text-center">
                                                      <td class="tds">{{$package_load->type_cargo==1 ? 'Pallets':'Packages'}}</td>
                                                      <td class="tds">{{$package_load->quantity}}</td>
                                                      <td class="tds">{{$package_load->height}} cm</td>
                                                      <td class="tds">{{$package_load->width}} cm</td>
                                                      <td class="tds">{{$package_load->large}} cm</td>
                                                      <td class="tds">{{$package_load->weight}} kg</td>
                                                      <td class="tds">{{$package_load->total_weight}} kg</td>
                                                      <td class="tds">{{$package_load->volume}} m<sup>3</sup></td>
                                                  </tr>
                                                  @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row ">
                                    @if($quote->chargeable_weight!='' && $quote->chargeable_weight>0)
                                        <div class="col-md-6 ">
                                            <b>Chargeable weight:</b> {{$quote->chargeable_weight}} kg
                                        </div>
                                    @else
                                        <div class="col-md-6 "></div>
                                    @endif
                                    <div class="col-md-6 ">
                                        <span class="pull-right">
                                            <b>Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                                        </span>
                                    </div>
                                </div>
                            @else
                                @if($quote->total_quantity!='' && $quote->total_quantity>0)
                                    <div class="row">
                                        <div class="col-md-2">
                                          <div id="cargo_details_cargo_type_p"><b>Cargo type:</b> {{$quote->cargo_type == 1 ? 'Pallets' : 'Packages'}}</div>
                                      </div>
                                      <div class="col-md-2">
                                          <div id="cargo_details_total_quantity_p"><b>Total quantity:</b> {{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</div>
                                      </div>
                                      <div class="col-md-2">
                                          <div id="cargo_details_total_weight_p"><b>Total weight: </b> {{$quote->total_weight != '' ? $quote->total_weight.' Kg' : ''}}</div>
                                      </div>
                                      <div class="col-md-2">
                                          <p id="cargo_details_total_volume_p"><b>Total volume: </b> {!!$quote->total_volume != '' ? $quote->total_volume.' m<sup>3</sup>' : ''!!}</p>
                                      </div>
                                      <div class="col-md-2">
                                          <p id="cargo_details_total_volume_p"><b>Chargeable weight: </b> {!!$quote->chargeable_weight != '' ? $quote->chargeable_weight.' kg' : ''!!}</p>
                                      </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary-v2 btn-edit pull-right" data-toggle="modal" data-target="#createRateModal">
                    Add rate &nbsp;&nbsp;<i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <br>
