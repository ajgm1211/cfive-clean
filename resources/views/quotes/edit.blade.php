@extends('layouts.app')
@section('title', 'Edit Quote')
@section('content')

    <div class="m-content">
        <div class="row">
            <div class="col-md-1">
                <button class="btn btn-primary btn-block">Save</button>
            </div>
            <div class="col-md-1">
                <a href="{{route('quotes.pdf',$quote->id)}}" class="btn btn-primary btn-block">PDF</a>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-block">Schedules</button>
            </div>
            <div class="col-md-1">
                <button class="btn btn-info btn-block">Send</button>
            </div>
        </div>
        <br>
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
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    {!! Form::model($quote,['route' => array('quotes.update', $quote->id), 'method' => 'PUT','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="m-portlet m-portlet--tabs">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-tools">
                                        <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
                                            <li class="nav-item m-tabs__item" style="padding-top: 20px;padding-bottom: 20px;">
                                                <a class="btn btn-primary" id="create-quote" data-toggle="tab" href="#m_portlet_tab_1_2" role="tab">
                                                    Edit Manual Quote
                                                </a>
                                                <a class="btn btn-primary" id="create-quote-back" style="display: none;" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                                    Back
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="m-portlet__body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="m_portlet_tab_1_1">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-lg-2">
                                                            <label>
                                                                <b>MODE</b>
                                                            </label>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class='row'>
                                                                <div class="col-md-4">
                                                                    <label class="m-option">
                                                                            <span class="m-option__control">
                                                                                <span class="m-radio m-radio--brand m-radio--check-bold">
                                                                                    <input name="type" value="1" type="radio" {!! $quote->type == 1 ? 'checked':'' !!}>
                                                                                    <span></span>
                                                                                </span>
                                                                            </span>
                                                                            <span class="m-option__label">
                                                                                <span class="m-option__head">
                                                                                    <span class="m-option__title">
                                                                                        FCL
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="m-option">
                                                                            <span class="m-option__control">
                                                                                <span class="m-radio m-radio--brand m-radio--check-bold">
                                                                                    <input name="type" value="2" type="radio" {!! $quote->type == 2 ? 'checked':'' !!}>
                                                                                    <span></span>
                                                                                </span>
                                                                            </span>
                                                                        <span class="m-option__label">
                                                                                <span class="m-option__head">
                                                                                    <span class="m-option__title">
                                                                                        LCL
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                    </label>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="m-option">
                                                                            <span class="m-option__control">
                                                                                <span class="m-radio m-radio--brand m-radio--check-bold">
                                                                                    <input name="type" value="3" type="radio" {!! $quote->type == 3 ? 'checked':'' !!}>
                                                                                    <span></span>
                                                                                </span>
                                                                            </span>
                                                                        <span class="m-option__label">
                                                                                <span class="m-option__head">
                                                                                    <span class="m-option__title">
                                                                                        AIR
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div class='row'>
                                                                <div class="col-md-4">
                                                                    <label>
                                                                        20' :
                                                                    </label>
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        {!! Form::text('qty_20', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_20']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label>
                                                                        40' :
                                                                    </label>
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        {!! Form::text('qty_40', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_40']) !!}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label>
                                                                        40' HC :
                                                                    </label>
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        {!! Form::text('qty_40_hc', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_40_hc']) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-lg-2">
                                                            <label>
                                                                <b>SERVICES</b>
                                                            </label>
                                                        </div>
                                                        <div class="col-lg-10">
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <label>Modality</label>
                                                                    {{ Form::select('modality',['1' => 'Export','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Incoterm</label>
                                                                    {{ Form::select('incoterm',['1' => 'FOB','2' => 'ECX'],null,['class'=>'m-select2-general form-control']) }}
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label>Delivery type</label>
                                                                    {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label>Pick up date</label>
                                                                    <div class="input-group date">
                                                                        {!! Form::text('pick_up_date', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select date','class' => 'form-control m-input']) !!}
                                                                        <div class="input-group-append">
                                                                                <span class="input-group-text">
                                                                                    <i class="la la-calendar-check-o"></i>
                                                                                </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div class="row">
                                                                <div class="col-md-4" id="origin_harbor_label">
                                                                    <label>Origin port</label>
                                                                    {{ Form::select('origin_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor']) }}
                                                                </div>
                                                                <div class="col-md-8" id="origin_address_label" style="display: none;">
                                                                    <label>Origin address</label>
                                                                    {!! Form::text('destination_address', null, ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'origin_address']) !!}
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div class="row">
                                                                <div class="col-md-4" id="destination_harbor_label">
                                                                    <label>Destination port</label>
                                                                    {{ Form::select('destination_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor']) }}
                                                                </div>
                                                                <div class="col-md-8" id="destination_address_label" style="display: none;">
                                                                    <label>Destination address</label>
                                                                    {!! Form::text('destination_address', null, ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-lg-2">
                                                            <label>
                                                                <b>CLIENT</b>
                                                            </label>
                                                        </div>
                                                        <div class="col-lg-10">
                                                            <br>
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                                    <label>Company</label>
                                                                    {{ Form::select('company_id',$companies,$quote->company_id,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal']) }}
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 ol-xs-12">
                                                                    <label>Client</label>
                                                                    {{ Form::select('contact_id',$contacts,$quote->contact_id,['class'=>'m-select2-general form-control']) }}
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                                    <label>Price level</label>
                                                                    {{ Form::select('price_id',$prices,$quote->price_id,['class'=>'m-select2-general form-control']) }}
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="m_portlet_tab_1_2">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <!--<div class="m-portlet m-portlet--responsive-tablet-and-mobile">
                                                                        <div class="m-portlet__head">
                                                                            <div class="m-portlet__head-caption">
                                                                                <div class="m-portlet__head-title">
                                                                                    <h5 class="m-portlet__head-text">
                                                                                        Origin
                                                                                    </h5>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="m-portlet__body">
                                                                            <span id="origin_input"></span>
                                                                        </div>
                                                                    </div>-->
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading"><b>Origin</b></div>
                                                                        <div class="panel-body">
                                                                            <span id="origin_input">
                                                                                {{$origin_harbor->name}}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading"><b>Destination</b></div>
                                                                        <div class="panel-body">
                                                                            <span id="destination_input">
                                                                                {{$destination_harbor->name}}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row" style="padding-top: 20px; padding-bottom: 20px;">
                                                                <div class="col-md-12">
                                                                    <h5>Cargo details</h5>
                                                                    <hr>
                                                                    <p id="cargo_details_20_p" class="hide"><span id="cargo_details_20"></span> x 20' Containers</p>
                                                                    <p id="cargo_details_40_p" class="hide"><span id="cargo_details_40"></span> x 40' Containers</p>
                                                                    <p id="cargo_details_40_hc_p" class="hide"><span id="cargo_details_40_hc"></span> x 40' HC Containers</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <h5>Origin ammounts</h5>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">Charge</div>
                                                                <div class="col-md-2">Detail</div>
                                                                <div class="col-md-1">Units</div>
                                                                <div class="col-md-3">Price per unit</div>
                                                                <div class="col-md-1">Markup</div>
                                                                <div class="col-md-1">Total</div>
                                                                <div class="col-md-1">Total EUR</div>
                                                            </div>
                                                            <hr>
                                                            @foreach($origin_ammounts as $origin_ammount)
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input type="text" class="form-control" id="origin_ammount_charge" value="{{$origin_ammount->charge}}" name="origin_ammount_charge[]"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="{{$origin_ammount->detail}}" class="form-control" type="text"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_ammount_units" name="origin_ammount_units[]" value="{{$origin_ammount->units}}" class="form-control origin_ammount_units" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="input-group">
                                                                            <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="{{$origin_ammount->price_per_unit}}" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="...">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    <select class="btn btn-default origin_ammount_currency" name="origin_ammount_currency[]">
                                                                                        <option value="">Currency</option>
                                                                                        <option value="1" {!! $origin_ammount->currency_id == 1 ? 'selected':'' !!}>USD</option>
                                                                                        <option value="2" {!! $origin_ammount->currency_id == 2 ? 'selected':'' !!}>CLP</option>
                                                                                        <option value="3" {!! $origin_ammount->currency_id == 3 ? 'selected':'' !!}>ARS</option>
                                                                                        <option value="4" {!! $origin_ammount->currency_id == 4 ? 'selected':'' !!}>EUR</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="{{$origin_ammount->markup}}" class="form-control origin_ammount_markup" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_total_ammount" name="origin_total_ammount[]" value="{{$origin_ammount->total_ammount}}" class="form-control origin_total_ammount" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1" >
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                                <input type="text" name="origin_total_ammount_2[]"  value="{{$origin_ammount->total_ammount_2}}" class="form-control" aria-label="...">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class='row hide' id="origin_ammounts">
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="input-group">
                                                                            <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="...">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    <select class="btn btn-default origin_ammount_currency" name="origin_ammount_currency[]">
                                                                                        <option value="">Currency</option>
                                                                                        <option value="1">USD</option>
                                                                                        <option value="2">CLP</option>
                                                                                        <option value="3">ARS</option>
                                                                                        <option value="4">EUR</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1" >
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                                <input type="text" name="origin_total_ammount_2[]"  class="form-control" aria-label="...">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="">
                                                                                <a class="btn removeOriginButton">
                                                                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class='row'>
                                                                <div class="col-md-9"></div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                            <span>
                                                                                <h5>
                                                                                    Sub-Total: <span id="sub_total_origin">{{$quote->sub_total_origin}}</span>&nbsp;
                                                                                    <input type="hidden" id="total_origin_ammount" name="sub_total_origin" value="{{$quote->sub_total_origin}}" class="form-control"/>
                                                                                </h5>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                            <span>
                                                                                <a class="btn addButtonOrigin" style="vertical-align: middle">
                                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <h5>Freight ammounts</h5>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">Charge</div>
                                                                <div class="col-md-2">Detail</div>
                                                                <div class="col-md-1">Units</div>
                                                                <div class="col-md-3">Price per unit</div>
                                                                <div class="col-md-1">Markup</div>
                                                                <div class="col-md-1">Total</div>
                                                                <div class="col-md-1">Total EUR</div>
                                                            </div>
                                                            <hr>
                                                            @foreach($freight_ammounts as $freight_ammount)
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input type="text" class="form-control" id="freight_ammount_charge" value="{{$freight_ammount->charge}}" name="freight_ammount_charge[]"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="{{$freight_ammount->detail}}" class="form-control" type="text"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_ammount_units" name="freight_ammount_units[]" value="{{$freight_ammount->units}}" class="form-control freight_ammount_units" min="0" max="99" type="number"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="input-group">
                                                                            <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="{{$freight_ammount->price_per_unit}}" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="...">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    <select class="btn btn-default freight_ammount_currency" name="freight_ammount_currency[]">
                                                                                        <option value="">Currency</option>
                                                                                        <option value="1" {!! $freight_ammount->currency_id == 1 ? 'selected':'' !!}>USD</option>
                                                                                        <option value="2" {!! $freight_ammount->currency_id == 2 ? 'selected':'' !!}>CLP</option>
                                                                                        <option value="3" {!! $freight_ammount->currency_id == 3 ? 'selected':'' !!}>ARS</option>
                                                                                        <option value="4" {!! $freight_ammount->currency_id == 4 ? 'selected':'' !!}>EUR</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="{{$freight_ammount->markup}}" class="form-control freight_ammount_markup" min="0" type="number"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1" >
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                                <input type="text" name="freight_total_ammount[]" value="{{$freight_ammount->total_ammount}}" class="form-control freight_total_ammount" aria-label="...">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" value="{{$freight_ammount->total_ammount_2}}" class="form-control" min="0" type="number"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                            <div class='row hide' id="freight_ammounts">
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_ammount_units" name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" max="99" type="number"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="input-group">
                                                                            <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="...">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    <select class="btn btn-default freight_ammount_currency" name="freight_ammount_currency[]">
                                                                                        <option value="">Currency</option>
                                                                                        <option value="1">USD</option>
                                                                                        <option value="2">CLP</option>
                                                                                        <option value="3">ARS</option>
                                                                                        <option value="4">EUR</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" min="0" type="number"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1" >
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                                <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount" aria-label="...">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control" min="0" type="number"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="">
                                                                                <a class="btn removeButton">
                                                                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class='row'>
                                                                <div class="col-md-9"></div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                            <span>
                                                                                <h5>
                                                                                    Sub-Total: <span id="sub_total_freight">{{$quote->sub_total_freight}}</span>&nbsp;
                                                                                    <input type="hidden" id="total_freight_ammount" name="sub_total_freight" value="{{$quote->sub_total_freight}}"  class="form-control"/>
                                                                                </h5>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                            <span>
                                                                                <a class="btn addButton" style="vertical-align: middle">
                                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group m-form__group row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <h5>Destination ammounts</h5>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-2">Charge</div>
                                                                <div class="col-md-2">Detail</div>
                                                                <div class="col-md-1">Units</div>
                                                                <div class="col-md-3">Price per unit</div>
                                                                <div class="col-md-1">Markup</div>
                                                                <div class="col-md-1">Total</div>
                                                                <div class="col-md-1">Total EUR</div>
                                                            </div>
                                                            <hr>
                                                            @foreach($destination_ammounts as $destination_ammount)
                                                            <div class="row">
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input type="text" class="form-control" id="destination_ammount_charge" value="{{$destination_ammount->charge}}" name="destination_ammount_charge[]"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_ammount_detatil" name="destination_ammount_detail[]" value="{{$destination_ammount->detail}}" class="form-control" type="text"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_ammount_units" name="destination_ammount_units[]" value="{{$destination_ammount->units}}" class="form-control destination_ammount_units" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="input-group">
                                                                            <input type="number" id="destination_ammount" name="destination_price_per_unit[]" value="{{$destination_ammount->price_per_unit}}" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="...">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    <select class="btn btn-default destination_ammount_currency" name="destination_ammount_currency[]">
                                                                                        <option value="">Currency</option>
                                                                                        <option value="1" {!! $destination_ammount->currency_id == 1 ? 'selected':'' !!}>USD</option>
                                                                                        <option value="2" {!! $destination_ammount->currency_id == 2 ? 'selected':'' !!}>CLP</option>
                                                                                        <option value="3" {!! $destination_ammount->currency_id == 3 ? 'selected':'' !!}>ARS</option>
                                                                                        <option value="4" {!! $destination_ammount->currency_id == 4 ? 'selected':'' !!}>EUR</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_ammount_markup" name="destination_ammount_markup[]"  value="{{$destination_ammount->markup}}" class="form-control destination_ammount_markup" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_total_ammount" name="destination_total_ammount[]"  value="{{$destination_ammount->total_ammount}}" class="form-control destination_total_ammount" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1" >
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                                <input type="text" name="destination_total_ammount_2[]"  value="{{$destination_ammount->total_ammount_2}}" class="form-control" aria-label="...">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                            <div class='row hide' id="destination_ammounts">
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input type="text" class="form-control" id="destination_ammount_charge" name="destination_ammount_charge[]"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_ammount_detatil" name="destination_ammount_detail[]" class="form-control" type="text"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_ammount_units" name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="input-group">
                                                                            <input type="number" id="destination_ammount" name="destination_price_per_unit[]" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="...">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    <select class="btn btn-default destination_ammount_currency" name="destination_ammount_currency[]">
                                                                                        <option value="">Currency</option>
                                                                                        <option value="1">USD</option>
                                                                                        <option value="2">CLP</option>
                                                                                        <option value="3">ARS</option>
                                                                                        <option value="4">EUR</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_ammount_markup" name="destination_ammount_markup[]" class="form-control destination_ammount_markup" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <input id="destination_total_ammount" name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number" min="0"/>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1" >
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="input-group">
                                                                                <input type="text" name="destination_total_ammount_2[]"  class="form-control" aria-label="...">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        <div class="form-group">
                                                                            <div class="">
                                                                                <a class="btn removeButtonDestination">
                                                                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class='row'>
                                                                <div class="col-md-9"></div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                            <span>
                                                                                <h5>
                                                                                    Sub-Total: <span id="sub_total_destination">{{$quote->sub_total_destination}}</span>&nbsp
                                                                                    <input type="hidden" id="total_destination_ammount" name="sub_total_destination" value="{{$quote->sub_total_destination}}" class="form-control"/>
                                                                                </h5>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <div class="form-group">
                                                                            <span>
                                                                                <a class="btn addButtonDestination" style="vertical-align: middle">
                                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group text-right">
                                                                        <h3><b>Total:</b> <span id="total">{{$quote->sub_total_origin + $quote->sub_total_freight + $quote->sub_total_destination }}</span></h3>
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
                                <hr>
                                <div class="form-group m-form__group row">
                                    <div class="row">
                                        <div class="col-lg-4 col-lg-offset-4">
                                            <button type="submit" class="btn btn-primary">
                                                Update Quote
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @parent
    <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/js/quote.js"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
    <script src="s/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>

@stop
