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
                        PDF document &nbsp;&nbsp;<i class="fa fa-book"></i>
                    </a>
                </li>
                <li class="nav-item m-tabs__item" >
                    <a class="btn btn-primary-v2" href="{{route('quotes-v2.duplicate',setearRouteKey($quote->id))}}">
                        Duplicate &nbsp;&nbsp;<i class="fa fa-plus"></i>
                    </a>
                </li>
            </ul>
        </div>
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
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet custom-portlet">
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
<<<<<<< HEAD
                                    <img src="{{ url('imgcarrier/'.$rate->carrier->image) }}"  class="img img-responsive" width="100" height="auto" style="margin-top: -20px;" />
=======
                                    <img src="{{ url('imgcarrier/'.$rate->carrier->image) }}"  class="img img-responsive" width="80" height="auto" style="margin-top: -15px;" />
>>>>>>> merge-quote-v2
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
<<<<<<< HEAD
                        <hr>
=======
>>>>>>> merge-quote-v2
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
<<<<<<< HEAD
                                                $i=0;
=======
                                                    $i=0;
>>>>>>> merge-quote-v2
                                                @endphp
                                                @foreach($rate->charge as $item)
                                                    @if($item->type_id==3)
                                                        @php
<<<<<<< HEAD
                                                        $freight_amounts = $item->amount['amount'];
                                                        $freight_markups = $item->markups['markups'];
                                                        $freight_total = $item->total['total'];
=======

                                                            $rate_id=$item->automatic_rate_id;
                                                            $freight_amounts = $item->amount['amount'];
                                                            $freight_markups = $item->markups['markups'];
>>>>>>> merge-quote-v2
                                                        
                                                        @endphp
                                                        <tr >
                                                            <td>
                                                                <a href="#" class="editable" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="editable" data-source="{{$calculation_types}}" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <a href="#" class="editable-amount-20 amount_20"data-type="text" data-name="amount->amount->20" data-value="{{@$freight_amounts['20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <a href="#" class="editable-markup-20 markup_20"data-type="text" data-name="markups->markups->20" data-value="{{@$freight_markups['20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
<<<<<<< HEAD
                                                                <span class="total_freight_20">{{@$freight_amounts['20']+@$freight_markups['20']}}</span>
=======
                                                                <span class="total_20">{{@$freight_amounts['20']+@$freight_markups['20']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <a href="#" class="editable-amount-40 amount_40"data-type="text" data-name="amount->amount->40" data-value="{{@$freight_amounts['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->markups->40" data-value="{{@$freight_markups['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
<<<<<<< HEAD
                                                                <span class="total_freight_40">{{@$freight_amounts['40']+@$freight_markups['40']}}</span>
=======
                                                                <span class="total_40">{{@$freight_amounts['40']+@$freight_markups['40']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->amount->40hc" data-value="{{@$freight_amounts['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <a href="#" class="editable-markup-40hc markup_40hc"data-type="text" data-name="markups->markups->40hc" data-value="{{@$freight_markups['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
<<<<<<< HEAD
                                                                <span class="total_freight_40hc">{{@$freight_amounts['40hc']+@$freight_markups['40hc']}}</span>
=======
                                                                <span class="total_40hc">{{@$freight_amounts['40hc']+@$freight_markups['40hc']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <a href="#" class="editable-amount-40nor amount_40nor "data-type="text" data-name="amount->amount->40nor" data-value="{{@$freight_amounts['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->markups->40nor" data-value="{{@$freight_markups['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
<<<<<<< HEAD
                                                                <span class="total_freight_40nor">{{@$freight_amount['40nor']+@$freight_markups['40nor']}}</span>
=======
                                                                <span class="total_40nor">{{@$freight_amount['40nor']+@$freight_markups['40nor']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->amount->45" data-value="{{@$freight_amounts['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->markups->45" data-value="{{@$freight_markups['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
<<<<<<< HEAD
                                                                <span class="total_freight_45">{{@$freight_amount['45']+@$freight_markups['45']}}</span>
=======
                                                                <span class="total_45">{{@$freight_amount['45']+@$freight_markups['45']}}</span>
>>>>>>> merge-quote-v2
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

<<<<<<< HEAD
                                                <tr class="hide" id="freight_charges_{{$i}}">
=======
                                                <tr class="hide" id="freight_charges_{{$v}}">
>>>>>>> merge-quote-v2
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
<<<<<<< HEAD
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
=======
                                                
                                                @if($rate->id == $rate_id )
                                                <tr>
                                                    <td></td>
                                                    <td class="title-quote size-12px">Total</td>
                                                    <td {{ $equipmentHides['20'] }} class="total_freight_20" colspan="3">{{@$rate->total['total']['freight']['20']}}</td>
                                                    <td {{ $equipmentHides['40'] }} colspan="3">{{@$rate->total['total']['freight']['40']}}</td>
                                                    <td {{ $equipmentHides['40hc'] }} colspan="3">{{@$rate->total['total']['freight']['40hc']}}</td>
                                                    <td {{ $equipmentHides['40nor'] }} colspan="3">{{@$rate->total['total']['freight']['40nor']}}</td>
                                                    <td {{ $equipmentHides['45'] }} colspan="3">{{@$rate->total['total']['freight']['45']}}</td>
                                                    <td >USD</td>
                                                </tr>
                                                @endif

                                            </tbody>
                                        </table>
                                    </div>
>>>>>>> merge-quote-v2
                                </div>
                            </div>
                            <div class='row'>
                                <div class="col-md-12">
                                    <h5 class="title-quote pull-right">
<<<<<<< HEAD
                                        Sub-Total: <span id="sub_total_freight"></span>&nbsp;
=======
                                        <b>Add freight charge</b><a class="btn" onclick="addFreightCharge({{$v}})" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                        </a>
>>>>>>> merge-quote-v2
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
<<<<<<< HEAD
                                                        $origin_amounts = $item->amount['amount'];
                                                        $origin_markups = $item->markups['markups'];
                                                        $origin_total = $item->total['total'];
=======
                                                            $origin_amounts = $item->amount['amount'];
                                                            $origin_markups = $item->markups['markups'];
                                                            $origin_total = $item->total['total'];
>>>>>>> merge-quote-v2
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <a href="#" class="editable surcharge_id" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                            </td>
                                                            <td>
                                                                <a href="#" class="editable calculation_type_id" data-source="{{$calculation_types}}" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <a href="#" class="editable-amount-20 amount_20"data-type="text" data-name="amount->amount->20" data-value="{{@$origin_amounts['20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <a href="#" class="editable-markup-20 markup_20"data-type="text" data-name="markups->markups->20" data-value="{{@$origin_markups['20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_20">{{@$origin_amounts['20']+@$origin_markups['20']}}</span>
=======
                                                                <span class="total_20">{{@$origin_amounts['20']+@$origin_markups['20']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <a href="#" class="editable-amount-40 amount_40"data-type="text" data-name="amount->amount->40" data-value="{{@$origin_amounts['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->markups->40" data-value="{{@$origin_markups['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_40">{{@$origin_amounts['40']+@$origin_markups['40']}}</span>
=======
                                                                <span class="total_40">{{@$origin_amounts['40']+@$origin_markups['40']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->amount->40hc" data-value="{{@$origin_amounts[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <a href="#" class="editable-amount-40hc markup_40hc"data-type="text" data-name="markups->markups->40hc" data-value="{{@$origin_markups[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_40hc">{{@$origin_amounts['40hc']+@$origin_markups['40hc']}}</span>
=======
                                                                <span class="total_40hc">{{@$origin_amounts['40hc']+@$origin_markups['40hc']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <a href="#" class="editable-amount-40nor amount_40nor"data-type="text" data-name="amount->amount->40nor" data-value="{{@$origin_amounts[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->markups->40nor" data-value="{{@$origin_markups[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_40nor">{{@$origin_amounts['40nor']+@$origin_markups['40nor']}}</span>
=======
                                                                <span class="total_40nor">{{@$origin_amounts['40nor']+@$origin_markups['40nor']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->amount->45" data-value="{{@$origin_amounts[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="amount->markups->45" data-value="{{@$origin_markups[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_45">{{@$origin_amounts['45']+@$origin_markups['45']}}</span>
=======
                                                                <span class="total_45">{{@$origin_amounts['45']+@$origin_markups['45']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td>
                                                                <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $a++;
                                                        @endphp
<<<<<<< HEAD

=======
>>>>>>> merge-quote-v2
                                                    @endif
                                                @endforeach

                                                <!-- Hide origin charges-->

<<<<<<< HEAD
                                                <tr class="hide" id="origin_charges_{{$i}}">
=======
                                                <tr class="hide" id="origin_charges_{{$v}}">
>>>>>>> merge-quote-v2
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
<<<<<<< HEAD
=======
                                                @if($rate->id == $rate_id )
                                                <tr>
                                                    <td></td>
                                                    <td class="title-quote size-12px">Total</td>
                                                    <td {{ $equipmentHides['20'] }} class="total_origin_20" colspan="3">{{@$rate->total['total']['origin']['20']}}</td>
                                                    <td {{ $equipmentHides['40'] }} colspan="3">{{@$rate->total['total']['origin']['40']}}</td>
                                                    <td {{ $equipmentHides['40hc'] }} colspan="3">{{@$rate->total['total']['origin']['40hc']}}</td>
                                                    <td {{ $equipmentHides['40nor'] }} colspan="3">{{@$rate->total['total']['origin']['40nor']}}</td>
                                                    <td {{ $equipmentHides['45'] }} colspan="3">{{@$rate->total['total']['origin']['45']}}</td>
                                                    <td >USD</td>
                                                </tr>
                                                @endif
>>>>>>> merge-quote-v2
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class='row'>
                                <div class="col-md-12">
                                    <h5 class="title-quote pull-right">
                                        <b>Add origin charge</b>
<<<<<<< HEAD
                                        <a class="btn" onclick="addOriginCharge({{$i}})" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                        </a>
                                    </h5>
                                </div>
                            </div>
                            <div class='row'>
                                <div class="col-md-12">
                                    <h5 class="title-quote pull-right">
                                        Sub-Total: <span id="sub_total_origin"></span>&nbsp;
                                    </h5>
=======
                                        <a class="btn" onclick="addOriginCharge({{$v}})" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                        </a>
                                    </h5>
>>>>>>> merge-quote-v2
                                </div>
                            </div>

                            <!-- Destination charges -->

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
<<<<<<< HEAD
                                                                <span class="total_destination_20">{{@$origin_amounts['20']+@$origin_markups['20']}}</span>
=======
                                                                <span class="total_20">{{@$origin_amounts['20']+@$origin_markups['20']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>          
                                                                <a href="#" class="editable destination_amount_40"data-type="text" data-name="amount->40" data-value="{{@$destination_amounts[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <a href="#" class="editable destination_markup_40"data-type="text" data-name="markups->40" data-value="{{@$destination_markups[$i]['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_40">{{@$origin_amounts['40']+@$origin_markups['40']}}</span>
=======
                                                                <span class="total_40">{{@$origin_amounts['40']+@$origin_markups['40']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <a href="#" class="editable destination_amount_40hc"data-type="text" data-name="amount->40hc" data-value="{{@$destination_amounts[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <a href="#" class="editable destination_markup_40hc"data-type="text" data-name="markups->40hc" data-value="{{@$destination_markups[$i]['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_40hc">{{@$origin_amounts['40hc']+@$origin_markups['40hc']}}</span>
=======
                                                                <span class="total_40hc">{{@$origin_amounts['40hc']+@$origin_markups['40hc']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <a href="#" class="editable destination_amount_40nor"data-type="text" data-name="amount->40nor" data-value="{{@$destination_amounts[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <a href="#" class="editable destination_markup_40nor"data-type="text" data-name="markups->40hc" data-value="{{@$destination_markups[$i]['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_40nor">{{@$origin_amounts['40nor']+@$origin_markups['40nor']}}</span>
=======
                                                                <span class="total_40nor">{{@$origin_amounts['40nor']+@$origin_markups['40nor']}}</span>
>>>>>>> merge-quote-v2
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <a href="#" class="editable destination_amount_45" data-type="text" data-name="amount->45" data-value="{{@$destination_amounts[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <a href="#" class="editable destination_markup_45" data-type="text" data-name="markups->45" data-value="{{@$destination_markups[$i]['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
<<<<<<< HEAD
                                                                <span class="total_origin_45">{{@$origin_amounts['45']+@$origin_markups['45']}}</span>
=======
                                                                <span class="total_45">{{@$origin_amounts['45']+@$origin_markups['45']}}</span>
>>>>>>> merge-quote-v2
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

<<<<<<< HEAD
                                                <tr class="hide" id="destination_charges_{{$i}}">
=======
                                                <tr class="hide" id="destination_charges_{{$v}}">
>>>>>>> merge-quote-v2
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
<<<<<<< HEAD
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
=======
                                                @if($rate->id == $rate_id )
                                                <tr>
                                                    <td></td>
                                                    <td class="title-quote size-12px">Total</td>
                                                    <td {{ $equipmentHides['20'] }} class="total_destination_20" colspan="3">{{@$rate->total['total']['destination']['20']}}</td>
                                                    <td {{ $equipmentHides['40'] }} colspan="3">{{@$rate->total['total']['destination']['40']}}</td>
                                                    <td {{ $equipmentHides['40hc'] }} colspan="3">{{@$rate->total['total']['destination']['40hc']}}</td>
                                                    <td {{ $equipmentHides['40nor'] }} colspan="3">{{@$rate->total['total']['destination']['40nor']}}</td>
                                                    <td {{ $equipmentHides['45'] }} colspan="3">{{@$rate->total['total']['destination']['45']}}</td>
                                                    <td >USD</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
>>>>>>> merge-quote-v2
                                </div>
                            </div>
                            <div class='row'>
                                <div class="col-md-12">
                                    <h5 class="title-quote pull-right">
<<<<<<< HEAD
                                        Sub-Total: <span id="sub_total_destination"></span>&nbsp;
=======
                                        <b>Add destination charge</b>
                                        <a class="btn" onclick="addDestinationCharge({{$v}})" style="vertical-align: middle">
                                            <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                        </a>
>>>>>>> merge-quote-v2
                                    </h5>
                                </div>
                            </div>
                        </div>
<<<<<<< HEAD
                    </div>
                    <br>
                    <br>
                    @php
                        $v++;
                    @endphp
                    @endforeach
                    <div class='row'>
                        <div class="col-md-12">
                            <h5 class="title-quote pull-right size-18px">
                                Total: <span id="total"></span>&nbsp;
                            </h5>
                        </div>
=======
>>>>>>> merge-quote-v2
                    </div>
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
            <div class="m-portlet custom-portlet">
                <div class="m-portlet__body">
                    @php
                    $x=0;
                    @endphp
                    @foreach($inlands as $inland)
                    <div class="tab-content">
                        <div class="flex-list">
                            <ul >
                                <li ><i class="fa fa-truck" style="font-size: 2rem"></i></li>
                                <li class="size-14px">From: {{$quote->origin_address != '' ? $quote->origin_address:$quote->origin_port->name}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($quote->origin_port->code, 0, 2))}}.svg"></li>
                                <li class="size-14px">To: {{$quote->destination_address != '' ? $quote->destination_address:$quote->destination_port->name}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($quote->destination_port->code, 0, 2))}}.svg"></li>
                                <li class="size-14px">Contract: {{$inland->contract}}</li>
                                <li class="size-14px">
                                    <div onclick="show_hide_element('details_inland_{{$x}}')"><i class="down"></i></div>
                                </li>
                            </ul>
                        </div>
                        <div class="details_inland_{{$x}} hide">
                            <h5 class="title-quote">Inland charges</h5>
                            <br>
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
                                    <tr >
                                        <td>
                                            <a href="#" class="editable" data-source="{{$surcharges}}" data-type="text" data-value="{{$inland->provider}}" data-pk="{{$item->id}}" data-title="Charge"></a>
                                        </td>
                                        <td>
                                            <a href="#" class="editable-amount-20 amount_20" data-type="text" data-name="amount->amount->20" data-value="{{@$inland->distance}}" data-pk="{{$item->id}}" data-title="Amount"></a> &nbsp;km
                                        </td>
                                        <td {{ $equipmentHides['20'] }}>                
                                            <a href="#" class="editable-amount-20 amount_20" data-type="text" data-name="amount->amount->20" data-value="{{@$inland->rate['amount']['20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                        </td>
                                        <td {{ $equipmentHides['20'] }}>
                                            <a href="#" class="editable-markup-20 markup_20" data-type="text" data-name="markups->markups->20" data-value="{{@$inland->markup['markup']['20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                        </td>
                                        <td {{ $equipmentHides['20'] }}>
                                            <span class="total_20">{{@$inland->rate['amount']['20']+@$inland->markup['markup']['20']}}</span>
                                        </td>
                                        <td {{ $equipmentHides['40'] }}>
                                            <a href="#" class="editable-amount-40 amount_40"data-type="text" data-name="amount->amount->40" data-value="{{@$inland->rate['amount']['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['40'] }}>
                                            <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->markups->40" data-value="{{@$inland->markup['markup']['40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['40'] }}>
                                            <span class="total_40">{{@$inland->rate['amount']['40']+@$inland->markup['markup']['40']}}</span>
                                        </td>
                                        <td {{ $equipmentHides['40hc'] }}>
                                            <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->amount->40hc" data-value="{{@$freight_amounts['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['40hc'] }}>
                                            <a href="#" class="editable-markup-40hc markup_40hc"data-type="text" data-name="markups->markups->40hc" data-value="{{@$freight_markups['40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['40hc'] }}>
                                            <span class="total_40hc">{{@$freight_amounts['40hc']+@$freight_markups['40hc']}}</span>
                                        </td>
                                        <td {{ $equipmentHides['40nor'] }}>
                                            <a href="#" class="editable-amount-40nor amount_40nor "data-type="text" data-name="amount->amount->40nor" data-value="{{@$freight_amounts['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['40nor'] }}>
                                            <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->markups->40nor" data-value="{{@$freight_markups['40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['40nor'] }}>
                                            <span class="total_40nor">{{@$freight_amount['40nor']+@$freight_markups['40nor']}}</span>
                                        </td>
                                        <td {{ $equipmentHides['45'] }}>
                                            <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->amount->45" data-value="{{@$freight_amounts['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['45'] }}>
                                            <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->markups->45" data-value="{{@$freight_markups['45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                        </td>
                                        <td {{ $equipmentHides['45'] }}>
                                            <span class="total_45">{{@$freight_amount['45']+@$freight_markups['45']}}</span>
                                        </td>
                                        <td>
                                            <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
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
            <div class="m-portlet">
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