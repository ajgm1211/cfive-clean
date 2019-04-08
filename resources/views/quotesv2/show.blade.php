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
                <li class="nav-item m-tabs__item" >
                    <button class="btn btn-primary-v2" data-toggle="modal" data-target="#SendQuoteModal">
                        Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                    </button>
                </li>
                <li class="nav-item m-tabs__item" >
                    <a class="btn btn-primary-v2" href="#">
                        PDF
                    </a>
                </li>
                <li class="nav-item m-tabs__item" >
                    <a class="btn btn-primary-v2" href="{{route('quotes-v2.duplicate',setearRouteKey($quote->id))}}">
                        Duplicate
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="row" style="padding-top: 20px;">
                        <h3 class="title-quote size-14px">Quote info</h3>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                            <li class="nav-item m-tabs__item" id="edit_li">
                                <a class="btn btn-primary-v2" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit
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
                                <input type="text" value="{{$quote->quote_id}}" class="form-control quote_id" hidden >
                                <span class="quote_id_span">{{$quote->quote_id}}</span>
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
                                {{ Form::select('price_id',$prices,$quote->price_id,['class'=>'form-control price_id select2','hidden']) }}
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
                                    <i class="fa fa-close"></i>&nbsp;&nbsp;&nbsp;Cancel
                                </a>
                                <a class="btn btn-primary" id="update" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Update
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Charges -->
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    @php
                    $v=0;
                    @endphp
                    @foreach($rates as $rate)
                    <div class="tab-content">
                        <div class="flex-list">
                            <ul >
                                <li style="max-height: 20px;">
                                    @if(isset($rate->carrier->image) && $rate->carrier->image!='')
                                    <img src="{{ url('imgcarrier/'.$rate->carrier->image) }}"  class="img img-responsive" width="100" height="auto" style="margin-top: -20px;" />
                                    @endif
                                </li>
                                <li class="size-14px">POL: {{$rate->origin_port->name}}, {{$rate->origin_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->origin_country_code}}.svg"/></li>
                                <li class="size-14px">POD: {{$rate->destination_port->name}}, {{$rate->destination_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->destination_country_code}}.svg"/></li>
                                <li class="size-14px">Contract: {{$rate->contract}}</li>
                                <li class="size-14px">
                                    <div onclick="show_hide_element('details_{{$v}}')"><i class="down"></i></div>
                                </li>
                            </ul>
                        </div>
                        <hr>
                        <br>
                        <div class="details_{{$v}} hide">
                            <!-- Freight charges -->
                            <div class="row">
                                <div class="col-md-3">
                                    <h5 class="title-quote size-14px">Freight charges</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table color-blue text-center">
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
                                            <tbody>
                                                @php
                                                $i=0;
                                                @endphp
                                                @foreach($rate->charge as $item)
                                                @if($item->type_id==3)
                                                @php

                                                $freight_amounts = $item->amount['amount'];
                                                $freight_markups = $item->markups['markups'];
                                                $freight_total = $item->total['total'];

                                                @endphp
                                                <tr >
                                                    <td>
                                                        <a href="#" id="surcharge_id" class="editable" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                    </td>
                                                    <td>
                                                        <a href="#" id="calculation_type_id" class="editable" data-source="{{$calculation_types}}" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>                
                                                        <a href="#" class="editable freight_amount_20"data-type="text" data-name="amount->amount->20" data-value="{{@$freight_amounts['20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable freight_markup_20"data-type="text" data-name="markups->markups->20" data-value="{{@$freight_markups['20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable freight_total_20"data-type="text" data-name="total->total->20" data-value="{{@$freight_total['20']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>

                                                        <a href="#" class="editable freight_amount_40"data-type="text" data-name="amount->amount->40" data-value="{{@$freight_amounts['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <a href="#" class="editable freight_markup_40"data-type="text" data-name="markups->markups->40" data-value="{{@$freight_markups['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <a href="#" class="editable freight_total_40"data-type="text" data-name="total->total->40" data-value="{{@$freight_total['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable freight_amount_40hc"data-type="text" data-name="amount->40hc" data-value="{{@$freight_amounts[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable freight_markup_40hc"data-type="text" data-name="markups->40hc" data-value="{{@$freight_markups[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable freight_total_40hc"data-type="text" data-name="total->40hc" data-value="{{@$freight_total[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable freight_amount_40nor"data-type="text" data-name="amount->40nor" data-value="{{@$freight_amounts[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable freight_markup_40nor"data-type="text" data-name="markups->40nor" data-value="{{@$freight_markups[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable freight_total_40nor"data-type="text" data-name="total->40hc" data-value="{{@$freight_total[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable freight_amount_45" data-type="text" data-name="amount->45" data-value="{{@$freight_amounts[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable freight_markup_45" data-type="text" data-name="markups->45" data-value="{{@$freight_markups[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable freight_total_45" data-type="text" data-name="total->45" data-value="{{@$freight_total[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
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

                                                <tr class="hide" id="freight_charges_{{$i}}">
                                                    <td>
                                                        <input type="text" class="form-control" id="freight_amount_charge" value="" name="origin_ammount_charge[]" />
                                                    </td>
                                                    <td>
                                                        {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
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
                                                                <a class="btn btn-xs btn-primary-plus removeFreightCharge">
                                                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> 
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class="col-md-12">
                                    <h5 class="title-quote pull-right">
                                        <b>Add freight charge</b><a class="btn" onclick="addFreightCharge({{$i}})" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                        </a>
                                    </h5>
                                </div>
                            </div>

                            <!-- Origin charges -->

                            <div class="row">
                                <div class="col-md-3">
                                    <h5 class="title-quote size-14px">Origin charges</h5>
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
                                            <tbody>
                                                @php
                                                $a=0;
                                                @endphp
                                                @foreach($rate->charge as $item)
                                                @if($item->type_id==1)
                                                @php
                                                $origin_amounts = $item->amount;
                                                $origin_markups = $item->markups;
                                                $origin_total = $item->total;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <a href="#" class="editable surcharge_id" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="editable calculation_type_id" data-source="{{$calculation_types}}" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable origin_amount_20"data-type="text" data-name="amount->20" data-value="{{@$origin_amounts[$i]['20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable origin_markup_20"data-type="text" data-name="markups->20" data-value="{{@$origin_markups[$i]['20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable origin_total_20"data-type="text" data-name="total->20" data-value="{{@$origin_total[$i]['20']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <a href="#" class="editable origin_amount_40"data-type="text" data-name="amount->40" data-value="{{@$origin_amounts[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <a href="#" class="editable origin_markup_40"data-type="text" data-name="markups->40" data-value="{{@$origin_markups[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <a href="#" class="editable origin_total_40"data-type="text" data-name="total->40" data-value="{{@$origin_total[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable origin_amount_40hc"data-type="text" data-name="amount->40hc" data-value="{{@$origin_amounts[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable origin_markup_40hc"data-type="text" data-name="markups->40hc" data-value="{{@$origin_markups[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable origin_total_40hc"data-type="text" data-name="total->40hc" data-value="{{@$origin_total[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable origin_amount_40nor"data-type="text" data-name="amount->40nor" data-value="{{@$origin_amounts[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable origin_markup_40nor"data-type="text" data-name="markups->40nor" data-value="{{@$origin_markups[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable origin_total_40nor"data-type="text" data-name="total->40nor" data-value="{{@$origin_total[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable origin_amount_45" data-type="text" data-name="amount->45" data-value="{{@$origin_amounts[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable origin_markup_45" data-type="text" data-name="amount->45" data-value="{{@$origin_markups[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable origin_total_45" data-type="text" data-name="amount->45" data-value="{{@$origin_total[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
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

                                                <tr class="hide" id="origin_charges_{{$i}}">
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
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Destination charges -->

                            <div class='row'>
                                <div class="col-md-12">
                                    <h5 class="title-quote pull-right">
                                        <b>Add origin charge</b>
                                        <a class="btn" onclick="addOriginCharge({{$i}})" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h5 class="title-quote size-14px">Destination charges</h5>
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
                                            <tbody>

                                                @php
                                                $a=0;
                                                @endphp
                                                @foreach($rate->charge as $item)
                                                @if($item->type_id==2)
                                                @php
                                                $destination_amounts = $item->amount;
                                                $destination_markups = $item->markups;
                                                $destination_total = $item->total;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <a href="#" class="editable surcharge_id" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="editable calculation_type_id" data-source="{{$calculation_types}}" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable destination_amount_20"data-type="text" data-name="amount->20" data-value="{{@$destination_amounts[$i]['20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable destination_markup_20"data-type="text" data-name="markups->20" data-value="{{@$destination_markups[$i]['20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <a href="#" class="editable destination_total_20"data-type="text" data-name="total->20" data-value="{{@$destination_total[$i]['20']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>                        
                                                        <a href="#" class="editable destination_amount_40"data-type="text" data-name="amount->40" data-value="{{@$destination_amounts[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <a href="#" class="editable destination_markup_40"data-type="text" data-name="markups->40" data-value="{{@$destination_markups[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <a href="#" class="editable destination_total_40"data-type="text" data-name="total->40" data-value="{{@$destination_total[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable destination_amount_40hc"data-type="text" data-name="amount->40hc" data-value="{{@$destination_amounts[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable destination_markup_40hc"data-type="text" data-name="markups->40hc" data-value="{{@$destination_markups[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <a href="#" class="editable destination_total_40hc"data-type="text" data-name="total->40hc" data-value="{{@$destination_total[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable destination_amount_40nor"data-type="text" data-name="amount->40nor" data-value="{{@$destination_amounts[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable destination_markup_40nor"data-type="text" data-name="markups->40hc" data-value="{{@$destination_markups[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <a href="#" class="editable destination_total_40nor"data-type="text" data-name="total->40nor" data-value="{{@$destination_total[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable destination_amount_45" data-type="text" data-name="amount->45" data-value="{{@$destination_amounts[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable destination_markup_45" data-type="text" data-name="markups->45" data-value="{{@$destination_markups[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <a href="#" class="editable destination_total_45" data-type="text" data-name="total->45" data-value="{{@$destination_total[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
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

                                                <tr class="hide" id="destination_charges_{{$i}}">
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
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class="col-md-12">
                                    <h5 class="title-quote pull-right">
                                        <b>Add destination charge</b>
                                        <a class="btn" onclick="addDestinationCharge({{$i}})" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    @php
                    $v++;
                    @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Inlands -->
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    @php
                    $x=0;
                    @endphp
                    @foreach($inlands as $inland)
                    <div class="tab-content">
                        <div class="flex-list">
                            <ul >
                                <li style="max-height: 20px;"><i class="fa fa-truck" style="font-size: 2.8rem"></i></li>
                                <li class="size-14px">From: Cordoba, Spain &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/es.svg"></li>
                                <li class="size-14px">To: Barcelona, Spain &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/es.svg"></li>
                                <li class="size-14px">Contract: {{$inland->contract}}</li>
                                <li class="size-14px">
                                    <div onclick="show_hide_element('details_inland_{{$x}}')"><i class="down"></i></div>
                                </li>
                            </ul>
                        </div>
                        <hr>
                        <br>
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
                                <tbody>
                                    <tr id="freight_charges_{{$i}}">
                                        <td>
                                            <input type="text" class="form-control" value="" name="inland_charge[]" />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="" name="distance[]" />
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
                                                    <a class="btn btn-xs btn-primary-plus removeFreightCharge">
                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> 
                                                    </a>
                                                </div>
                                            </div>
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
                    <br>
                    @php
                    $x++;
                    @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Payments and terms conditions -->
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="row" style="padding-top: 20px;">
                        <h3 class="title-quote size-14px">Payment conditions</h3>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                            <li class="nav-item m-tabs__item" id="edit_li">
                                <a class="btn btn-primary-v2" id="edit-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit
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
                                    <i class="fa fa-close"></i>&nbsp;&nbsp;&nbsp;Cancel
                                </a>
                                <a class="btn btn-primary" id="update-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Update
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
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="row" style="padding-top: 20px;">
                        <h3 class="title-quote size-14px">Terms & conditions</h3>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                            <li class="nav-item m-tabs__item" id="edit_li">
                                <a class="btn btn-primary-v2" id="edit-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit
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
                                    <i class="fa fa-close"></i>&nbsp;&nbsp;&nbsp;Cancel
                                </a>
                                <a class="btn btn-primary" id="update-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Update
                                </a>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('quotes.partials.sendQuoteModal')
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