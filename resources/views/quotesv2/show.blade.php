@extends('layouts.app')
@section('css')
    @parent
    <link href="/css/quote.css" rel="stylesheet" type="text/css" />

    <link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Quotes')
@section('content')
    <br>
    <div class="m-content">
        @if(Session::has('message.nivel'))
            <div class="col-md-12">
                <br>
                <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
                    <div class="m-alert__icon">
                        <i class="la la-warning"></i>
                    </div>
                    <div class="m-alert__text">
                        <strong>
                            {{ session('message.title') }}
                        </strong>
                        {{ session('message.content') }}
                    </div>
                    <div class="m-alert__close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
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
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">Quote info</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Edit &nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" value="{{$quote->id}}" class="form-control id" hidden >
                                    <label class="title-quote"><b>Quotation ID:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}" class="form-control quote_id" hidden >
                                    <span class="quote_id_span">{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="{{$quote->quote_id}}" class="form-control" hidden >
                                    {{ Form::select('type',['FCL'=>'FCL','LCL'=>'LCL'],$quote->type,['class'=>'form-control type select2','hidden','disabled']) }}
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
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Equipment:&nbsp;&nbsp;</b></label>
                                    <span class="equipment_span">
                                    @foreach($quote->equipment as $item)
                                            {{$item}}@unless($loop->last),@endunless
                                        @endforeach
                                </span>
                                    {{ Form::select('equipment[]',['20' => '20','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],@$quote->equipment,['class'=>'form-control equipment','id'=>'equipment','multiple' => 'multiple','required' => 'true','hidden']) }}
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Price level:&nbsp;&nbsp;</b></label>
                                    <span class="price_level_span">{{$quote->price->name}}</span>
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
                                    {{ Form::select('user_id',$users,$quote->user_id,['class'=>'form-control user_id select2','hidden','']) }}
                                    <span class="user_id_span">{{$quote->user->name}} {{$quote->user->lastname}}</span>
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
        <!-- Charges -->
        @php
            $v=0;
        @endphp
        @foreach($rates as $rate)
            <div class="row">
                <div class="col-md-12">
                    <div class="m-portlet custom-portlet">
                        <div class="m-portlet__body">
                            <div class="tab-content">
                                <div class="flex-list" style=" margin-bottom:-30px; margin-top: 0;">
                                    <ul >
                                        <li style="max-height: 20px;">                                            
                                            @if(isset($rate->carrier->image) && $rate->carrier->image!='')
                                                <img src="{{ url('imgcarrier/'.$rate->carrier->image) }}"  class="img img-responsive" width="80" height="auto" style="margin-top: -15px;" />
                                            @endif
                                        </li>
                                        <li class="size-12px">POL: {{$rate->origin_address != '' ? $rate->origin_address:$rate->origin_port->name.', '.$rate->origin_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->origin_country_code}}.svg"/></li>
                                        <li class="size-12px">POD: {{$rate->destination_address != '' ? $rate->destination_address:$rate->destination_port->name.', '.$rate->destination_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->destination_country_code}}.svg"/></li>
                                        <li class="size-12px">Contract: {{$rate->contract}}</li>
                                        <li class="size-12px">
                                            <div onclick="show_hide_element('details_{{$v}}')"><i class="down"></i></div>
                                        </li>
                                        <li class="size-12px">
                                            <div class="delete-rate" data-rate-id="{{$rate->id}}" style="cursor:pointer;"><i class="fa fa-trash fa-4x"></i></div>
                                        </li>
                                    </ul>
                                </div>
                                <br>
                                <div class="details_{{$v}} hide" style="background-color: white; padding: 20px; border-radius: 5px; margin-top: 20px;">
                                    <!-- Freight charges -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5 class="title-quote size-12px">Freight charges</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered table-hover table color-blue text-center">
                                                    <thead class="title-quote text-center header-table">
                                                    <tr>
                                                        <td >Charge</td>
                                                        <td >Detail</td>
                                                        <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                        <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                        <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                        <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                        <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                        <td >Currency</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody style="background-color: white;">
                                                    @php
                                                        $i=0;
                                                        $sum20=0;
                                                        $sum40=0;
                                                        $sum40hc=0;
                                                        $sum40nor=0;
                                                        $sum45=0;
                                                    @endphp
                                                    @foreach($rate->charge as $item)
                                                        @if($item->type_id==3)
                                                            <?php
                                                                $rate_id=$item->automatic_rate_id;
                                                                $freight_amounts = json_decode($item->amount,true);
                                                                $freight_markups = json_decode($item->markups,true);

                                                                //Calculating totals freights
                                                                if(isset($freight_amounts['c20']) && isset($freight_markups['c20'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total20=($freight_amounts['c20']+$freight_markups['c20'])/$item->currency_usd;
                                                                    }else{
                                                                        $total20=($freight_amounts['c20']+$freight_markups['c20'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($freight_amounts['c40']) && isset($freight_markups['c40'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40=($freight_amounts['c40']+$freight_markups['c40'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40=($freight_amounts['c40']+$freight_markups['c40'])/$item->currency_eur;
                                                                    }                                                                   
                                                                }
                                                                if(isset($freight_amounts['c40hc']) && isset($freight_markups['c40hc'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40hc=($freight_amounts['c40hc']+$freight_markups['c40hc'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40hc=($freight_amounts['c40hc']+$freight_markups['c40hc'])/$item->currency_eur;
                                                                    }                                                                    
                                                                }
                                                                if(isset($freight_amounts['c40nor']) && isset($freight_markups['c40nor'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40nor=($freight_amounts['c40nor']+$freight_markups['c40nor'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40nor=($freight_amounts['c40nor']+$freight_markups['c40nor'])/$item->currency_eur;
                                                                    }                                                                    
                                                                }
                                                                if(isset($freight_amounts['c45']) && isset($freight_markups['c45'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total45=($freight_amounts['c45']+$freight_markups['c45'])/$item->currency_usd;
                                                                    }else{
                                                                        $total45=($freight_amounts['c45']+$freight_markups['c45'])/$item->currency_eur;
                                                                    }                                                                    
                                                                }
                                                                $sum20+=@$total20;
                                                                $sum40+=@$total40;
                                                                $sum40hc+=@$total40hc;
                                                                $sum40nor+=@$total40nor;
                                                                $sum45+=@$total45;
                                                            ?>
                                                            <tr >
                                                                <td>
                                                                    <a href="#" class="editable" data-source="{{$surcharges}}" data-type="select" data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="editable" data-source="{{$calculation_types}}" data-type="select" data-name="calculation_type_id" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <a href="#" class="editable-amount-20 amount_20"data-type="text" data-name="amount->c20" data-value="{{@$freight_amounts['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <a href="#" class="editable-markup-20 markup_20"data-type="text" data-name="markups->c20" data-value="{{@$freight_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <span class="total_20">{{@$freight_amounts['c20']+@$freight_markups['c20']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <a href="#" class="editable-amount-40 amount_40" data-type="text" data-name="amount->c40" data-value="{{@$freight_amounts['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->c40" data-value="{{@$freight_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <span class="total_40">{{@$freight_amounts['c40']+@$freight_markups['c40']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$freight_amounts['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <a href="#" class="editable-markup-40hc markup_40hc"data-type="text" data-name="markups->c40hc" data-value="{{@$freight_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <span class="total_40hc">{{@$freight_amounts['c40hc']+@$freight_markups['c40hc']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <a href="#" class="editable-amount-40nor amount_40nor "data-type="text" data-name="amount->c40nor" data-value="{{@$freight_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->c40nor" data-value="{{@$freight_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <span class="total_40nor">c{{@$freight_amount['c40nor']+@$freight_markups['c40nor']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->c45" data-value="{{@$freight_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->c45" data-value="{{@$freight_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <span class="total_45">{{@$freight_amount['45']+@$freight_markups['45']}}</span>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $i++;
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    <!-- Hide Freight -->

                                                    <tr class="hide" id="freight_charges_{{$v}}">
                                                        <input name="type_id" value="3" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        <input name="type_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        <td>
                                                            {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                        </td>
                                                        <td>
                                                            {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="amount_c20" class="amount_c20 form-control" id="amount_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="markup_c20" class="form-control markup_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="total_c20" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="markup_c40" value="" class="form-control markup_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="total_c40" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="amount_c40hc" value="" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="markup_c40hc" value="" class="form-control markup_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />

                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="amount_c40nor" value="" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="markup_c40nor" value="" class="form-control markup_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="amount_c45" value="" class="form-control amount_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="markup_c45" value="" class="form-control markup_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled/>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        {{ Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
                                                                    </div>
                                                                    <a class="btn btn-xs btn-primary-plus store_charge">
                                                                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                                                    </a>
                                                                    <a class="btn btn-xs btn-primary-plus removeFreightCharge">
                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    @if($rate->id == @$rate_id )
                                                        <tr>
                                                            <td></td>
                                                            <td class="title-quote size-12px">Total</td>
                                                            <td {{ $equipmentHides['20'] }} colspan="3">{{number_format(@$sum20, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40'] }} colspan="3">{{number_format(@$sum40, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40hc'] }} colspan="3">{{number_format(@$sum40hc, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40nor'] }} colspan="3">{{number_format(@$sum40nor, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['45'] }} colspan="3">{{number_format(@$sum45, 2, '.', '')}}</td>
                                                            <td >{{$currency_cfg->alphacode}}</td>
                                                        </tr>
                                                    @endif

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-md-12">
                                            <h5 class="title-quote pull-right">
                                                <b>Add freight charge</b><a class="btn" onclick="addFreightCharge({{$v}})" style="vertical-align: middle">
                                                    <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                                </a>
                                            </h5>
                                        </div>
                                    </div>

                                    <!-- Origin charges -->

                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5 class="title-quote size-12px">Origin charges</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered color-blue text-center">
                                                    <thead class="title-quote text-center header-table">
                                                    <tr>
                                                        <td >Charge</td>
                                                        <td >Detail</td>
                                                        <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                        <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                        <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                        <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                        <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                        <td >Currency</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody style="background-color: white;">
                                                    @php
                                                        $a=0;
                                                        $sum_origin_20=0;
                                                        $sum_origin_40=0;
                                                        $sum_origin_40hc=0;
                                                        $sum_origin_40nor=0;
                                                        $sum_origin_45=0;
                                                    @endphp
                                                    @foreach($rate->charge as $item)
                                                        @if($item->type_id==1)
                                                            <?php
                                                                $rate_id=$item->automatic_rate_id;
                                                                $origin_amounts = json_decode($item->amount,true);
                                                                $origin_markups = json_decode($item->markups,true);
                                                        
                                                                //Calculating totals origins

                                                                if(isset($origin_amounts['c20']) && isset($origin_markups['c20'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total20=($origin_amounts['c20']+$origin_markups['c20'])/$item->currency_usd;
                                                                    }else{
                                                                        $total20=($origin_amounts['c20']+$origin_markups['c20'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($origin_amounts['c40']) && isset($origin_markups['c40'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40=($origin_amounts['c40']+$origin_markups['c40'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40=($origin_amounts['c40']+$origin_markups['c40'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($origin_amounts['c40hc']) && isset($origin_markups['c40hc'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40hc=($origin_amounts['c40hc']+$origin_markups['c40hc'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40hc=($origin_amounts['c40hc']+$origin_markups['c40hc'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($origin_amounts['c40nor']) && isset($origin_markups['c40nor'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40nor=($origin_amounts['c40nor']+$origin_markups['c40nor'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40nor=($origin_amounts['c40nor']+$origin_markups['c40nor'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($origin_amounts['c45']) && isset($origin_markups['c45'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total45=($origin_amounts['c45']+$origin_markups['c45'])/$item->currency_usd;
                                                                    }else{
                                                                        $total45=($origin_amounts['c45']+$origin_markups['c45'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                $sum_origin_20+=@$total20;
                                                                $sum_origin_40+=@$total40;
                                                                $sum_origin_40hc+=@$total40hc;
                                                                $sum_origin_40nor+=@$total40nor;
                                                                $sum_origin_45+=@$total45;
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="#" class="editable surcharge_id" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="editable calculation_type_id" data-source="{{$calculation_types}}" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <a href="#" class="editable-amount-20 amount_20"data-type="text" data-name="amount->c20" data-value="{{@$origin_amounts['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <a href="#" class="editable-markup-20 markup_20"data-type="text" data-name="markups->c20" data-value="{{@$origin_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <span class="total_20">{{@$origin_amounts['c20']+@$origin_markups['c20']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <a href="#" class="editable-amount-40 amount_40"data-type="text" data-name="amount->c40" data-value="{{@$origin_amounts['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->c40" data-value="{{@$origin_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <span class="total_40">{{@$origin_amounts['c40']+@$origin_markups['c40']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$origin_amounts['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <a href="#" class="editable-amount-40hc markup_40hc"data-type="text" data-name="markups->c40hc" data-value="{{@$origin_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <span class="total_40hc">{{@$origin_amounts['c40hc']+@$origin_markups['c40hc']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <a href="#" class="editable-amount-40nor amount_40nor"data-type="text" data-name="amount->c40nor" data-value="{{@$origin_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->40nor" data-value="{{@$origin_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <span class="total_40nor">{{@$origin_amounts['c40nor']+@$origin_markups['c40nor']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->c45" data-value="{{@$origin_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->c45" data-value="{{@$origin_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <span class="total_45">{{@$origin_amounts['c45']+@$origin_markups['c45']}}</span>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $a++;
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    <!-- Hide origin charges-->

                                                    <tr class="hide" id="origin_charges_{{$v}}">
                                                        <td>
                                                            <input type="text" class="form-control" value="" name="origin_ammount_charge[]"/>
                                                        </td>
                                                        <td>
                                                            {{ Form::select('freight_ammount_currency[]',$calculation_types,5,['class'=>'form-control freight_ammount_currency','required'=>true]) }}
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control origin_ammount_currency select-2-width']) }}
                                                                    </div>
                                                                    <a class="btn btn-xs btn-primary-plus removeOriginCharge">
                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @if($rate->id == @$rate_id )
                                                        <tr>
                                                            <td></td>
                                                            <td class="title-quote size-12px">Total</td>
                                                            <td {{ $equipmentHides['20'] }} colspan="3">{{number_format(@$sum_origin_20, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40'] }} colspan="3">{{number_format(@$sum_origin_40, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40hc'] }} colspan="3">{{number_format(@$sum_origin_40hc, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40nor'] }} colspan="3">{{number_format(@$sum_origin_40nor, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['45'] }} colspan="3">{{number_format(@$sum_origin_45, 2, '.', '')}}</td>
                                                            <td >{{$currency_cfg->alphacode}}</td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-md-12">
                                            <h5 class="title-quote pull-right">
                                                <b>Add origin charge</b>
                                                <a class="btn" onclick="addOriginCharge({{$v}})" style="vertical-align: middle">
                                                    <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                                </a>
                                            </h5>
                                        </div>
                                    </div>

                                    <!-- Destination charges -->

                                    <div class="row">
                                        <div class="col-md-3">
                                            <h5 class="title-quote size-12px">Destination charges</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered color-blue text-center">
                                                    <thead class="title-quote text-center header-table">
                                                    <tr>
                                                        <td >Charge</td>
                                                        <td >Detail</td>
                                                        <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                        <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                        <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                        <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                        <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                        <td >Currency</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody style="background-color: white;">
                                                    @php
                                                        $a=0;
                                                        $sum_destination_20=0;
                                                        $sum_destination_40=0;
                                                        $sum_destination_40hc=0;
                                                        $sum_destination_40nor=0;
                                                        $sum_destination_45=0;
                                                    @endphp

                                                    @foreach($rate->charge as $item)
                                                        @if($item->type_id==2)
                                                            <?php
                                                                $rate_id=$item->automatic_rate_id;
                                                                $destination_amounts = json_decode($item->amount,true);
                                                                $destination_markups = json_decode($item->markups,true);
                                                        
                                                                //Calculating totals destinations
                                                                if(isset($destination_amounts['c20']) && isset($destination_markups['c20'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total20=($destination_amounts['c20']+$destination_markups['c20'])/$item->currency_usd;
                                                                    }else{
                                                                        $total20=($destination_amounts['c20']+$destination_markups['c20'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($destination_amounts['c40']) && isset($destination_markups['c40'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40=($destination_amounts['c40']+$destination_markups['c40'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40=($destination_amounts['c40']+$destination_markups['c40'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($destination_amounts['c40hc']) && isset($destination_markups['c40hc'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40hc=($destination_amounts['c40hc']+$destination_markups['c40hc'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40hc=($destination_amounts['c40hc']+$destination_markups['c40hc'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($destination_amounts['c40nor']) && isset($destination_markups['c40nor'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total40nor=($destination_amounts['c40nor']+$destination_markups['c40nor'])/$item->currency_usd;
                                                                    }else{
                                                                        $total40nor=($destination_amounts['c40nor']+$destination_markups['c40nor'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                if(isset($destination_amounts['c45']) && isset($destination_markups['c45'])){
                                                                    if($currency_cfg->alphacode=='USD'){
                                                                        $total45=($destination_amounts['c45']+$destination_markups['c45'])/$item->currency_usd;
                                                                    }else{
                                                                        $total45=($destination_amounts['c45']+$destination_markups['c45'])/$item->currency_eur;
                                                                    }
                                                                }
                                                                $sum_destination_20+=@$total20;
                                                                $sum_destination_40+=@$total40;
                                                                $sum_destination_40hc+=@$total40hc;
                                                                $sum_destination_40nor+=@$total40nor;
                                                                $sum_destination_45+=@$total45;
                                                            ?>                                                     

                                                            <tr>
                                                                <td>
                                                                    <a href="#" class="editable surcharge_id" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="editable calculation_type_id" data-source="{{$calculation_types}}" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <a href="#" class="editable destination_amount_20"data-type="text" data-name="amount->c20" data-value="{{@$destination_amounts['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <a href="#" class="editable destination_markup_20"data-type="text" data-name="markups->c20" data-value="{{@$destination_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['20'] }}>
                                                                    <span class="total_20">{{@$destination_amounts['c20']+@$destination_markups['c20']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <a href="#" class="editable destination_amount_40"data-type="text" data-name="amount->c40" data-value="{{@$destination_amounts['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <a href="#" class="editable destination_markup_40"data-type="text" data-name="markups->c40" data-value="{{@$destination_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40'] }}>
                                                                    <span class="total_40">{{@$destination_amounts['c40']+@$destination_markups['c40']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <a href="#" class="editable destination_amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$destination_amounts['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <a href="#" class="editable destination_markup_40hc"data-type="text" data-name="markups->c40hc" data-value="{{@$destination_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                    <span class="total_40hc">{{@$destination_amounts['c40hc']+@$destination_markups['c40hc']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <a href="#" class="editable destination_amount_40nor"data-type="text" data-name="amount->c40nor" data-value="{{@$destination_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <a href="#" class="editable destination_markup_40nor"data-type="text" data-name="markups->c40hc" data-value="{{@$destination_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                    <span class="total_40nor">{{@$destination_amounts['c40nor']+@$destination_markups['c40nor']}}</span>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <a href="#" class="editable destination_amount_45" data-type="text" data-name="amount->c45" data-value="{{@$destination_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <a href="#" class="editable destination_markup_45" data-type="text" data-name="markups->c45" data-value="{{@$destination_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                </td>
                                                                <td {{ $equipmentHides['45'] }}>
                                                                    <span class="total_45">{{@$destination_amounts['c45']+@$destination_markups['c45']}}</span>
                                                                </td>
                                                                <td>
                                                                    <a href="#" class="editable" data-source="{{$currencies}}" data-name="markups->45" data-type="select" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $a++;
                                                            @endphp
                                                        @endif
                                                    @endforeach

                                                    <!-- Hide destination charges -->

                                                    <tr class="hide" id="destination_charges_{{$v}}">
                                                        <td>
                                                            <input type="text" class="form-control" value="" name="destination_ammount_charge[]"/>
                                                        </td>
                                                        <td>
                                                            {{ Form::select('freight_ammount_currency[]',$calculation_types,5,['class'=>'form-control freight_ammount_currency','required'=>true]) }}
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['20'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40hc'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['40nor'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="freight_rate[]" value="" class="form-control freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="freight_surcharge[]" value="" class="form-control freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td {{ $equipmentHides['45'] }}>
                                                            <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        {{ Form::select('destination_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control destination_ammount_currency select-2-width']) }}
                                                                    </div>
                                                                    <a class="btn btn-xs btn-primary-plus removeDestinationCharge">
                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @if($rate->id == @$rate_id )
                                                        <tr>
                                                            <td></td>
                                                            <td class="title-quote size-12px">Total</td>
                                                            <td {{ $equipmentHides['20'] }} colspan="3">{{number_format(@$sum_destination_20, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40'] }} colspan="3">{{number_format(@$sum_destination_40, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40hc'] }} colspan="3">{{number_format(@$sum_destination_40hc, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['40nor'] }} colspan="3">{{number_format(@$sum_destination_40nor, 2, '.', '')}}</td>
                                                            <td {{ $equipmentHides['45'] }} colspan="3">{{number_format(@$sum_destination_45, 2, '.', '')}}</td>
                                                            <td >{{$currency_cfg->alphacode}}</td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-md-12">
                                            <h5 class="title-quote pull-right">
                                                <b>Add destination charge</b>
                                                <a class="btn" onclick="addDestinationCharge({{$v}})" style="vertical-align: middle">
                                                    <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                                </a>
                                            </h5>
                                        </div>
                                    </div>
                                    <br>
                                    <!-- Inlands -->
                                    @if(!$rate->inland->isEmpty())
                                        <div class="row" >
                                            <div class="col-md-12">
                                                <br>
                                                <h5 class="title-quote size-12px">Inland charges</h5>
                                                <hr>
                                                <div class="">
                                                    <div class="">
                                                        @php
                                                            $x=0;
                                                        @endphp
                                                        @foreach($rate->inland as $inland)
                                                            <?php 
                                                                $inland_rates = json_decode($inland->rate,true);
                                                                $inland_markups = json_decode($inland->markup,true);
                                                            ?>
                                                            <div class="tab-content">
                                                                <div class="flex-list">
                                                                    <ul >
                                                                        <li ><i class="fa fa-truck" style="font-size: 2rem"></i></li>
                                                                        <li class="size-12px">From: {{$rate->origin_address != '' ? $rate->origin_address:$rate->origin_port->name}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($rate->origin_port->code, 0, 2))}}.svg"></li>
                                                                        <li class="size-12px">To: {{$rate->destination_address != '' ? $rate->destination_address:$rate->destination_port->name}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($rate->destination_port->code, 0, 2))}}.svg"></li>
                                                                        <li class="size-12px">Contract: {{$inland->contract}}</li>
                                                                        <li class="size-12px">
                                                                            <div onclick="show_hide_element('details_inland_{{$x}}')"><i class="down"></i></div>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="details_inland_{{$x}} hide">
                                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                                        <thead class="title-quote text-center header-table">
                                                                        <tr>
                                                                            <td >Charge</td>
                                                                            <td >Distance</td>
                                                                            <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                                            <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                                            <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                                            <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                                            <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                                            <td >Currency</td>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody style="background-color: white;">
                                                                        <tr >
                                                                            <td>
                                                                                <a href="#" class="editable" data-source="{{$surcharges}}" data-type="text" data-value="{{$inland->provider}}" data-pk="{{$item->id}}" data-title="Charge"></a>
                                                                            </td>
                                                                            <td>
                                                                                <a href="#" class="editable-amount-20 amount_20" data-type="text" data-name="amount->c20" data-value="{{@$inland->distance}}" data-pk="{{$item->id}}" data-title="Amount"></a> &nbsp;km
                                                                            </td>
                                                                            <td {{ $equipmentHides['20'] }}>
                                                                                <a href="#" class="editable-amount-20 amount_20" data-type="text" data-name="amount->c20" data-value="{{@$inland_rates['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['20'] }}>
                                                                                <a href="#" class="editable-markup-20 markup_20" data-type="text" data-name="markups->20" data-value="{{@$inland_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['20'] }}>
                                                                                <span class="total_20">{{@$inland_rates['c20']+@$inland_markups['c20']}}</span>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40'] }}>
                                                                                <a href="#" class="editable-amount-40 amount_40"data-type="text" data-name="amount->c40" data-value="{{@$inland_rates['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40'] }}>
                                                                                <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->40" data-value="{{@$inland_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40'] }}>
                                                                                <span class="total_40">{{@$inland_rates['c40']+@$inland_markups['c40']}}</span>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                                <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$inland_amounts['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                                <a href="#" class="editable-markup-40hc markup_40hc"data-type="text" data-name="markups->40hc" data-value="{{@$inland_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                                <span class="total_40hc">{{@$inland_amounts['c40hc']+@$inland_markups['c40hc']}}</span>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                                <a href="#" class="editable-amount-40nor amount_40nor "data-type="text" data-name="amount->c40nor" data-value="{{@$inland_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                                <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->40nor" data-value="{{@$inland_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                                <span class="total_40nor">{{@$inland_amounts['c40nor']+@$inland_markups['c40nor']}}</span>
                                                                            </td>
                                                                            <td {{ $equipmentHides['45'] }}>
                                                                                <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->45" data-value="{{@$inland_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['45'] }}>
                                                                                <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->45" data-value="{{@$inland_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td {{ $equipmentHides['45'] }}>
                                                                                <span class="total_45">{{@$inland_amounts['c45']+@$inland_markups['c45']}}</span>
                                                                            </td>
                                                                            <td>
                                                                                <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$inland->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>

                                                                    <div class='row'>
                                                                        <div class="col-md-12">
                                                                            <h5 class="title-quote pull-right">
                                                                                <b>Add inland charge</b>
                                                                                <a class="btn" onclick="addInlandCharge({{$i}})" style="vertical-align: middle">
                                                                                    <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                                                                </a>
                                                                            </h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <br>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                @endif

                                <!-- Remarks -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="m-portlet" style="box-shadow: none;">
                                                <div class="m-portlet__head">
                                                    <div class="row" style="padding-top: 20px;">
                                                        <h5 class="title-quote size-12px">Remarks</h5>
                                                    </div>
                                                    <div class="m-portlet__head-tools">
                                                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                                            <li class="nav-item m-tabs__item" id="edit_li">
                                                                <button class="btn btn-primary-v2 edit-remarks" onclick="edit_remark('remarks_span_{{$v}}','remarks_textarea_{{$v}}','update_remarks_{{$v}}')">
                                                                    Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body">
                                                    <div class="card card-body bg-light remarks_span_{{$v}}">
                                                        <span>{!! $rate->remarks !!}</span>
                                                    </div>
                                                    <div class="remarks_textarea_{{$v}}" hidden>
                                                        <textarea name="remarks_{{$v}}" class="form-control remarks_{{$v}} editor">{!!$rate->remarks!!}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 text-center update_remarks_{{$v}}"  hidden>
                                                            <br>
                                                            <button class="btn btn-danger cancel-remarks_{{$v}}" onclick="cancel_update('remarks_span_{{$v}}','remarks_textarea_{{$v}}','update_remarks_{{$v}}')">
                                                                Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                                            </button>
                                                            <button class="btn btn-primary update-remarks_{{$v}}" onclick="update_remark({{$rate->id}},'remarks_{{$v}}',{{$v}})">
                                                                Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                                            </button>
                                                            <br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @php
            $v++;
        @endphp
    @endforeach
    <!-- Payments and terms conditions -->
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">Payment conditions</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="card card-body bg-light">
                            <span class="payment_conditions_span">{!! $quote->payment_conditions !!}</span>
                            <div class="payment_conditions_textarea" hidden>
                                <textarea name="payment_conditions" class="form-control payment_conditions editor" id="payment_conditions">{!!$quote->payment_conditions!!}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center" id="update_payments" hidden>
                                    <br>
                                    <a class="btn btn-danger" id="cancel-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </a>
                                    <a class="btn btn-primary" id="update-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Update &nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">Terms & conditions</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="card card-body bg-light">
                            <span class="terms_and_conditions_span">{!! $quote->terms_and_conditions !!}</span>
                            <div class="terms_and_conditions_textarea" hidden>
                                <textarea name="terms_and_conditions" class="form-control terms_and_conditions editor" id="terms_and_conditions">{!!$quote->terms_and_conditions!!}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center" id="update_terms" hidden>
                                    <br>
                                    <a class="btn btn-danger" id="cancel-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </a>
                                    <a class="btn btn-primary" id="update-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">PDF Layout</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                                    </a>
                                </li>
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" href="{{route('quotes-v2.pdf',setearRouteKey($quote->id))}}" target="_blank" >
                                        PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content" id="show_detailed">
                            <div class="row" class="">
                                <div class="col-md-2 col-xs-12">
                                    {{ Form::select('show_type',['detailed'=>'Show detailed','total in'=>'Show total in'],$quote->pdf_option->show_type,['class'=>'form-control-sm type select2 pdf-feature','id'=>'show_hide_select','data-quote-id'=>$quote->id,'data-name'=>'show_type','data-type'=>'select']) }}
                                </div>
                                <div class="col-md-2 col-xs-12">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" type="checkbox" data-quote-id="{{$quote->id}}" name="grouped_total_currency" data-name="grouped_total_currency" data-type="checkbox" value="1" {{$quote->pdf_option->grouped_total_currency==1 ? 'checked':''}}>
                                        <label class="title-quote"><b>Show total in:</b></label>
                                        {{ Form::select('total_in_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->total_in_currency,['class'=>'form-control-sm type select2 pdf-feature','data-quote-id'=>$quote->id,'data-name'=>'total_in_currency','data-type'=>'select']) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <label class="title-quote"><b>Language:</b></label>
                                    {{ Form::select('language',['English'=>'English','Spanish'=>'Spanish','Portuguese'=>'Portuguese'],$quote->pdf_option->language,['class'=>'form-control-sm company_id select2 pdf-feature','id'=>'language','data-quote-id'=>$quote->id,'data-name'=>'language','data-type'=>'select']) }}
                                </div>
                                <div class="col-auto show_carrier">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" type="checkbox" name="show_carrier" value="1" id="show_carrier" data-quote-id="{{$quote->id}}" data-type="checkbox" data-name="show_carrier" {{$quote->pdf_option->show_carrier==1 ? 'checked':''}}>
                                        <label class="form-check-label title-quote" for="show_carrier">
                                            Show carrier
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-auto show_logo">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" type="checkbox" name="show_logo" value="1" id="show_logo" data-quote-id="{{$quote->id}}" data-name="show_logo" data-type="checkbox" {{$quote->pdf_option->show_logo==1 ? 'checked':''}}>
                                        <label class="form-check-label title-quote" for="show_logo">
                                            Show customer logo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" style="padding-top: 25px;">
                                <div class="col-md-3 group_freight_charges">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" data-quote-id="{{$quote->id}}" data-name="grouped_freight_charges" type="checkbox" data-type="checkbox" name="grouped_freight_charges" value="1" {{$quote->pdf_option->grouped_freight_charges==1 ? 'checked':''}}>
                                        <label class="title-quote"><b>Group Freight Charges in:</b></label>
                                        {{ Form::select('freight_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->freight_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'freight_charges_currency']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 group_origin_charges">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" data-quote-id="{{$quote->id}}" data-name="grouped_origin_charges" type="checkbox" data-type="checkbox" name="grouped_origin_charges" value="1" {{$quote->pdf_option->grouped_origin_charges==1 ? 'checked':''}}>
                                        <label class="title-quote"><b>Group Origin Charges in:</b></label>
                                        {{ Form::select('origin_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->origin_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'origin_charges_currency']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 group_destination_charges">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" data-quote-id="{{$quote->id}}" data-name="grouped_destination_charges" data-type="checkbox" type="checkbox" name="grouped_destination_charges" value="1" {{$quote->pdf_option->grouped_destination_charges==1 ? 'checked':''}}>
                                        <div class="form-group">
                                            <label class="title-quote"><b>Group Destination Charges in:</b></label>
                                            {{ Form::select('destination_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->destination_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'destination_charges_currency']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('quotesv2.partials.sendQuoteModal')
@endsection

@section('js')
    @parent
    <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
    <script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
    <script type="text/javascript">

        var editor_config = {
            path_absolute : "/",
            selector: "textarea.editor",
            plugins: ["template"],
            toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
            external_plugins: { "nanospell": "{{asset('js/tinymce/plugins/nanospell/plugin.js')}}" },
            nanospell_server:"php",
            browser_spellcheck: true,
            relative_urls: false,
            remove_script_host: false,
            file_browser_callback : function(field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                } else {
                    cmsURL = cmsURL + "&type=Files";
                }

                tinymce.activeEditor.windowManager.open({
                    file: '<?= route('elfinder.tinymce4') ?>',// use an absolute path!
                    title: 'File manager',
                    width: 900,
                    height: 450,
                    resizable: 'yes'
                }, {
                    setUrl: function (url) {
                        win.document.getElementById(field_name).value = url;
                    }
                });
            }
        };

        tinymce.init(editor_config);

    </script>
@stop