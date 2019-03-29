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
                        <a class="btn btn-primary-v2" href="#">
                            Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                        </a>
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
                                        <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Edit
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
                                <!--<a href="#" id="quote_id" class="editable" data-tpl="<input type='text' style='width:100px;'>" data-type="text" data-value="{{$quote->quote_id}}" data-pk="{{$quote->id}}" data-title="Quote id">{{$quote->quote_id}}</a>-->
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="{{$quote->quote_id}}" class="form-control" hidden >
                                    {{ Form::select('type',['FCL'=>'FCL','LCL'=>'LCL'],$quote->type,['class'=>'form-control type select2','hidden','disabled']) }}
                                    <span class="type_span">{{$quote->type}}</span>
                                <!--<a href="#" id="type" class="editable" data-source="[{value: 'FCL', text: 'FCL'},{value: 'LCL', text: 'LCL'}]" data-type="select" data-value="{{$quote->type}}" data-pk="{{$quote->id}}" data-title="Select type"></a>-->
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Company:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('company_id',$companies,$quote->company_id,['class'=>'form-control company_id select2','hidden']) }}
                                    <span class="company_span">{{$quote->company->business_name}}</span>
                                <!--<a href="#" id="company" class="editable" data-type="select" data-value="{{$quote->company_id}}" data-pk="{{$quote->id}}" data-title="Select type"></a>-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Status:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('status',['Draft'=>'Draft','Win'=>'Win','Sent'=>'Sent'],$quote->status,['class'=>'form-control status select2','hidden','']) }}
                                    <span class="status_span Status_{{$quote->status}}" style="border-radius: 10px;">{{$quote->status}} <i class="fa fa-check"></i></span>
                                <!--<a href="#" id="status" class="editable Status_{{$quote->status}}" data-source="[{value: 'Draft', text: 'Draft'},{value: 'Win', text: 'Win'},{value: 'Sent', text: 'Sent'}]" data-value="{{$quote->status}}" data-type="select" data-pk="{{$quote->id}}" data-title="Select type" style="border-radius: 10px;">{{$quote->status}} <i class="fa fa-check"></i></a>-->
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
                                <!--<a href="#" id="delivery_type" class="editable" data-source="[{value: '1', text: 'Port to Port'},{value: '2', text: 'Port to Door'},{value: '3', text: 'Door to Port'},{value: '4', text: 'Door to Door'}]" data-type="select" data-value="{{$quote->delivery_type}}" data-pk="{{$quote->id}}" data-title="Select delivery type"></a>-->
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
                                <!--<a href="#" id="created_at" data-type="date" data-pk="{{$quote->id}}" data-title="Select date">{{date_format($quote->created_at, 'd M Y')}}</a>-->
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
                                <!--<a href="#" id="incoterm_id" class="editable" data-source="[{value: '1', text: 'EWX'},{value: '2', text: 'FAS'},{value: '3', text: 'FCA'},{value: '4', text: 'FOB'},{value: '5', text: 'CFR'},{value: '6', text: 'CIF'},{value: '7', text: 'CIP'},{value: '8', text: 'DAT'},{value: '9', text: 'DAP'},{value: '10', text: 'DDP'}]" data-type="select" data-value="{{$quote->incoterm_id}}" data-pk="{{$quote->id}}" data-title="Select delivery type"></a>-->
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
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet">
                    <div class="m-portlet__body">
                        <div class="tab-content">
                            <div class="flex-list">
                                <ul >
                                    <li style="max-height: 20px;"><img class="img img-fluid" src="{{asset('imgcarrier/maersk.png')}}" width="80" style="margin-top: -15px;"></li>
                                    <li class="size-14px">POL: {{$quote->origin_port->name}}, {{$quote->origin_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/ae.svg"/></li>
                                    <li class="size-14px">POD: {{$quote->destination_port->name}}, {{$quote->destination_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/ae.svg"/></li>
                                    <li class="size-14px">Contract: Spot rate - CA-134</li>
                                    <li class="size-14px"><button type="button" class="btn btn-default pull-right" data-toggle="collapse" data-target=".amount_details"><i class="la la-angle-down"></i></button></li>
                                </ul>
                            </div>

                            <div class="amount_details collapse">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5 class="title-quote size-14px">Freight charges</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered color-blue">
                                                <thead class="title-quote text-center header-table">
                                                <tr>
                                                    <td >Charge</td>
                                                    <td >Detail</td>
                                                    <td {{ $equipmentHides['20'] }}>20'</td>
                                                    <td {{ $equipmentHides['40'] }}>40'</td>
                                                    <td {{ $equipmentHides['40hc'] }}>40HC'</td>
                                                    <td {{ $equipmentHides['40nor'] }}>40NOR'</td>
                                                    <td {{ $equipmentHides['45'] }}>45'</td>
                                                    <td >Currency</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control-sm" id="freight_amount_charge" value="" name="freight_amount_charge[]" style="width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        {{ Form::select('freight_ammount_currency[]',[1=>'PER CONTAINER'],1,['class'=>'form-control-sm freight_ammount_currency select2-freight select-2-width','required'=>true]) }}
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('freight_amount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control-sm freight_amount_currency select2-freight select-2-width']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="hide" id="freight_charges">
                                                    <td>
                                                        <input type="text" class="form-control-sm" id="freight_amount_charge" value="" name="origin_ammount_charge[]" style="width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        {{ Form::select('freight_ammount_currency[]',[1=>'PER CONTAINER'],1,['class'=>'form-control-sm freight_ammount_currency','required'=>true]) }}
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control-sm origin_ammount_currency select-2-width']) }}
                                                                    <a class="btn removeFreightCharge">
                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                    </a>
                                                                </div>
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
                                            <a class="btn addFreightCharge" style="vertical-align: middle">
                                                <b>Add freight charge</b> &nbsp;<span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                            </a>
                                        </h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5 class="title-quote size-14px">Origin charges</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered color-blue">
                                                <thead class="title-quote text-center header-table">
                                                <tr>
                                                    <td >Charge</td>
                                                    <td >Detail</td>
                                                    <td {{ $equipmentHides['20'] }}>20'</td>
                                                    <td {{ $equipmentHides['40'] }}>40'</td>
                                                    <td {{ $equipmentHides['40hc'] }}>40HC'</td>
                                                    <td {{ $equipmentHides['40nor'] }}>40NOR'</td>
                                                    <td {{ $equipmentHides['45'] }}>45'</td>
                                                    <td >Currency</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control-sm" id="origin_ammount_charge" value="" name="origin_ammount_charge[]" style="width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        {{ Form::select('freight_ammount_currency[]',[1=>'PER CONTAINER'],1,['class'=>'form-control-sm freight_ammount_currency select2-freight select-2-width','required'=>true]) }}
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control-sm origin_ammount_currency select2-origin select-2-width']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="hide" id="origin_charges">
                                                    <td>
                                                        <input type="text" class="form-control-sm" id="freight_amount_charge" value="" name="origin_ammount_charge[]" style="width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        {{ Form::select('freight_ammount_currency[]',[1=>'PER CONTAINER'],1,['class'=>'form-control-sm freight_ammount_currency','required'=>true]) }}
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control-sm origin_ammount_currency select-2-width']) }}
                                                                    <a class="btn removeOriginCharge">
                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                    </a>
                                                                </div>
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
                                            <a class="btn addOriginCharge" style="vertical-align: middle">
                                                <b>Add origin charge</b> &nbsp;<span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
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
                                            <table class="table table-bordered color-blue">
                                                <thead class="title-quote text-center header-table">
                                                <tr>
                                                    <td >Charge</td>
                                                    <td >Detail</td>
                                                    <td {{ $equipmentHides['20'] }}>20'</td>
                                                    <td {{ $equipmentHides['40'] }}>40'</td>
                                                    <td {{ $equipmentHides['40hc'] }}>40HC'</td>
                                                    <td {{ $equipmentHides['40nor'] }}>40NOR'</td>
                                                    <td {{ $equipmentHides['45'] }}>45'</td>
                                                    <td >Currency</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control-sm" id="origin_ammount_charge" value="" name="origin_ammount_charge[]" style="width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        {{ Form::select('freight_ammount_currency[]',[1=>'PER CONTAINER'],1,['class'=>'form-control-sm freight_ammount_currency select2-freight select-2-width','required'=>true]) }}
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control-sm origin_ammount_currency select2-origin select-2-width']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="hide" id="destination_charges">
                                                    <td>
                                                        <input type="text" class="form-control-sm" id="freight_amount_charge" value="" name="origin_ammount_charge[]" style="width: 120px;"/>
                                                    </td>
                                                    <td>
                                                        {{ Form::select('freight_ammount_currency[]',[1=>'PER CONTAINER'],1,['class'=>'form-control-sm freight_ammount_currency','required'=>true]) }}
                                                    </td>
                                                    <td {{ $equipmentHides['20'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="origin_ammount_units" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40hc'] }}>
                                                        <div class="">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['40nor'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td {{ $equipmentHides['45'] }}>
                                                        <div class="input-group">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <input id="freight_rate" name="freight_rate[]" value="" class="form-control-sm freight_rate" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_surcharge" name="freight_surcharge[]" value="" class="form-control-sm freight_surcharge" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input id="freight_total" name="freight_total[]" value="" class="form-control-sm freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control-sm origin_ammount_currency select-2-width']) }}
                                                                    <a class="btn removeDestinationCharge">
                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                    </a>
                                                                </div>
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
                                            <a class="btn addDestinationCharge" style="vertical-align: middle">
                                                <b>Add destination charge</b> &nbsp;<span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                            </a>
                                        </h5>
                                    </div>
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
                            <h3 class="title-quote size-14px">Payments conditions</h3>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="card card-body bg-light">
                            <li>Cash</li>
                            <li>Bank transfer</li>
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
                    </div>
                    <div class="m-portlet__body">
                        <div class="card card-body bg-light">
                            Cargofive is an online solution that empowers freight forwarders to be part of the digital era. 
With our technology freight forwarders can manage contracts, automate the quotation process, reduce operational costs and have access to actionable data to increase sales.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @parent
    <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function(){
  $(".show-hide").click(function(){
    $(this).closest("row").toggle();
  });
});
    </script>
@stop