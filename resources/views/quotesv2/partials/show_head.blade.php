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
                        <a class="btn btn-primary-v2" href="#">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
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
                                    {{ Form::select('type',['FCL'=>'FCL','LCL'=>'LCL','AIR'],$quote->type,['class'=>'form-control type select2','hidden','disabled']) }}
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
                                    {{ Form::select('contact_id',$contacts,$quote->contact_id,['class'=>'form-control contact_id select2','hidden']) }}
                                    <span class="contact_id_span">{{$quote->contact->first_name}} {{$quote->contact->last_name}}</span>
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
                                <div class="col-md-4" {{$quote->type=='FCL' ? '':'hidden'}}>
                                    <br>
                                    <label class="title-quote"><b>Equipment:&nbsp;&nbsp;</b></label>
                                    <span class="equipment_span">
                                        @if($quote->equipment!='')
                                            <?php
                                                $equipment=json_decode($quote->equipment);
                                            ?>
                                            @foreach($equipment as $item)
                                                {{$item}}@unless($loop->last),@endunless
                                            @endforeach
                                        @endif
                                    </span>
                                    {{ Form::select('equipment[]',['20' => '20','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],@$equipment,['class'=>'form-control equipment','id'=>'equipment','multiple' => 'multiple','required' => 'true','hidden','disabled']) }}
                                </div>
                                <div class="col-md-4" {{$quote->type!='FCL' ? '':'hidden'}}>
                                    <br>
                                    <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('user_id',$users,$quote->user_id,['class'=>'form-control user_id select2','hidden','']) }}
                                    <span class="user_id_span">{{$quote->user->name}} {{$quote->user->lastname}}</span>
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
                                @if($quote->type=='FCL')
                                    <div class="col-md-4">
                                        <br>
                                        <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                                        {{ Form::select('user_id',$users,$quote->user_id,['class'=>'form-control user_id select2','hidden','']) }}
                                        <span class="user_id_span">{{$quote->user->name}} {{$quote->user->lastname}}</span>
                                    </div>
                                @endif
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