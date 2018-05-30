@extends('layouts.app')
@section('title', 'Add Quote')
@section('content')

    <div class="m-content">
        <div class="row">
            <div class="col-md-1">
                <button class="btn btn-primary btn-block">Save</button>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary btn-block">PDF</button>
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
                    {!! Form::open(['route' => 'quotes.store','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="m-portlet m-portlet--tabs">
                                <div class="m-portlet__head">
                                    <div class="m-portlet__head-tools">
                                        <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
                                            <li class="nav-item m-tabs__item" style="padding-top: 20px;padding-bottom: 20px;">
                                                <a class="btn btn-primary" id="create-quote" data-toggle="tab" href="#m_portlet_tab_1_2" role="tab">
                                                    Create Manual Quote
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
                                                                                    <input name="type" value="1" type="radio">
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
                                                                                    <input name="type" value="2" type="radio">
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
                                                                                    <input name="type" value="3" type="radio">
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
                                                                    {{ Form::select('origin_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'origin_harbor']) }}
                                                                </div>
                                                                <div class="col-md-8" id="origin_address_label" style="display: none;">
                                                                    <label>Origin address</label>
                                                                    {!! Form::text('origin_address', null, ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <div class="row">
                                                                <div class="col-md-4" id="destination_harbor_label">
                                                                    <label>Destination port</label>
                                                                    {{ Form::select('destination_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'destination_harbor']) }}
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
                                                                    {{ Form::select('company_id',$companies,null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal']) }}
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 ol-xs-12">
                                                                    <label>Client</label>
                                                                    {{ Form::select('contact_id',[],null,['class'=>'m-select2-general form-control']) }}
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                                    <label>Price level</label>
                                                                    {{ Form::select('price_id',[],null,['class'=>'m-select2-general form-control']) }}
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
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading"><b>Origin</b></div>
                                                                        <div class="panel-body">
                                                                            <span id="origin_input"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="panel panel-default">
                                                                        <div class="panel-heading"><b>Destination</b></div>
                                                                        <div class="panel-body">
                                                                            <span id="destination_input"></span>
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
                                                            <div class="row">
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
                                                                                    Sub-Total:<span id="sub_total_origin">0.00</span>&nbsp;
                                                                                    <input type="hidden" id="total_origin_ammount" name="sub_total_origin" class="form-control"/>
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
                                                            <div class="row">
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
                                                            </div>
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
                                                                                    <!--<select name='currencies'>
                                                                                        <option value='AED' title='United Arab Emirates Dirham'>AED</option>
                                                                                        <option value='AFN' title='Afghan Afghani'>AFN</option>
                                                                                        <option value='ALL' title='Albanian Lek'>ALL</option>
                                                                                        <option value='AMD' title='Armenian Dram'>AMD</option>
                                                                                        <option value='ANG' title='Netherlands Antillean Guilder'>ANG</option>
                                                                                        <option value='AOA' title='Angolan Kwanza'>AOA</option>
                                                                                        <option value='ARS' title='Argentine Peso'>ARS</option>
                                                                                        <option value='AUD' title='Australian Dollar'>AUD</option>
                                                                                        <option value='AWG' title='Aruban Florin'>AWG</option>
                                                                                        <option value='AZN' title='Azerbaijani Manat'>AZN</option>
                                                                                        <option value='BAM' title='Bosnia-Herzegovina Convertible Mark'>BAM</option>
                                                                                        <option value='BBD' title='Barbadian Dollar'>BBD</option>
                                                                                        <option value='BDT' title='Bangladeshi Taka'>BDT</option>
                                                                                        <option value='BGN' title='Bulgarian Lev'>BGN</option>
                                                                                        <option value='BHD' title='Bahraini Dinar'>BHD</option>
                                                                                        <option value='BIF' title='Burundian Franc'>BIF</option>
                                                                                        <option value='BMD' title='Bermudan Dollar'>BMD</option>
                                                                                        <option value='BND' title='Brunei Dollar'>BND</option>
                                                                                        <option value='BOB' title='Bolivian Boliviano'>BOB</option>
                                                                                        <option value='BRL' title='Brazilian Real'>BRL</option>
                                                                                        <option value='BSD' title='Bahamian Dollar'>BSD</option>
                                                                                        <option value='BTC' title='Bitcoin'>BTC</option>
                                                                                        <option value='BTN' title='Bhutanese Ngultrum'>BTN</option>
                                                                                        <option value='BWP' title='Botswanan Pula'>BWP</option>
                                                                                        <option value='BYN' title='Belarusian Ruble'>BYN</option>
                                                                                        <option value='BYR' title='Belarusian Ruble'>BYR</option>
                                                                                        <option value='BZD' title='Belize Dollar'>BZD</option>
                                                                                        <option value='CAD' title='Canadian Dollar'>CAD</option>
                                                                                        <option value='CDF' title='Congolese Franc'>CDF</option>
                                                                                        <option value='CHF' title='Swiss Franc'>CHF</option>
                                                                                        <option value='CLF' title='Chilean Unit of Account (UF)'>CLF</option>
                                                                                        <option value='CLP' title='Chilean Peso'>CLP</option>
                                                                                        <option value='CNY' title='Chinese Yuan'>CNY</option>
                                                                                        <option value='COP' title='Colombian Peso'>COP</option>
                                                                                        <option value='CRC' title='Costa Rican Colón'>CRC</option>
                                                                                        <option value='CUC' title='Cuba Convertible Peso'>CRC</option>
                                                                                        <option value='CUP' title='Cuban Peso'>CUP</option>
                                                                                        <option value='CVE' title='Cape Verdean Escudo'>CVE</option>
                                                                                        <option value='CZK' title='Czech Republic Koruna'>CZK</option>
                                                                                        <option value='DJF' title='Djiboutian Franc'>DJF</option>
                                                                                        <option value='DKK' title='Danish Krone'>DKK</option>
                                                                                        <option value='DOP' title='Dominican Peso'>DOP</option>
                                                                                        <option value='DZD' title='Algerian Dinar'>DZD</option>
                                                                                        <option value='EEK' title='Estonian Kroon'>EEK</option>
                                                                                        <option value='EGP' title='Egyptian Pound'>EGP</option>
                                                                                        <option value='ERN' title='Eritrean Nakfa'>ERN</option>
                                                                                        <option value='ETB' title='Ethiopian Birr'>ETB</option>
                                                                                        <option value='EUR' title='Euro'>EUR</option>
                                                                                        <option value='FJD' title='Fijian Dollar'>FJD</option>
                                                                                        <option value='FKP' title='Falkland Islands Pound'>FKP</option>
                                                                                        <option value='GBP' title='British Pound Sterling'>GBP</option>
                                                                                        <option value='GEL' title='Georgian Lari'>GEL</option>
                                                                                        <option value='GGP' title='Guernsey Pound'>GGP</option>
                                                                                        <option value='GHS' title='Ghanaian Cedi'>GHS</option>
                                                                                        <option value='GIP' title='Gibraltar Pound'>GIP</option>
                                                                                        <option value='GMD' title='Gambian Dalasi'>GMD</option>
                                                                                        <option value='GNF' title='Guinean Franc'>GNF</option>
                                                                                        <option value='GTQ' title='Guatemalan Quetzal'>GTQ</option>
                                                                                        <option value='GYD' title='Guyanaese Dollar'>GYD</option>
                                                                                        <option value='HKD' title='Hong Kong Dollar'>HKD</option>
                                                                                        <option value='HNL' title='Honduran Lempira'>HNL</option>
                                                                                        <option value='HRK' title='Croatian Kuna'>HRK</option>
                                                                                        <option value='HTG' title='Haitian Gourde'>HTG</option>
                                                                                        <option value='HUF' title='Hungarian Forint'>HUF</option>
                                                                                        <option value='IDR' title='Indonesian Rupiah'>IDR</option>
                                                                                        <option value='ILS' title='Israeli New Sheqel'>ILS</option>
                                                                                        <option value='IMP' title='Manx pound'>IMP</option>
                                                                                        <option value='INR' title='Indian Rupee'>INR</option>
                                                                                        <option value='IQD' title='Iraqi Dinar'>IQD</option>
                                                                                        <option value='IRR' title='Iranian Rial'>IRR</option>
                                                                                        <option value='ISK' title='Icelandic Króna'>ISK</option>
                                                                                        <option value='JEP' title='Jersey Pound'>JEP</option>
                                                                                        <option value='JMD' title='Jamaican Dollar'>JMD</option>
                                                                                        <option value='JOD' title='Jordanian Dinar'>JOD</option>
                                                                                        <option value='JPY' title='Japanese Yen'>JPY</option>
                                                                                        <option value='KES' title='Kenyan Shilling'>KES</option>
                                                                                        <option value='KGS' title='Kyrgystani Som'>KGS</option>
                                                                                        <option value='KHR' title='Cambodian Riel'>KHR</option>
                                                                                        <option value='KMF' title='Comorian Franc'>KMF</option>
                                                                                        <option value='KPW' title='North Korean Won'>KPW</option>
                                                                                        <option value='KRW' title='South Korean Won'>KRW</option>
                                                                                        <option value='KWD' title='Kuwaiti Dinar'>KWD</option>
                                                                                        <option value='KYD' title='Cayman Islands Dollar'>KYD</option>
                                                                                        <option value='KZT' title='Kazakhstani Tenge'>KZT</option>
                                                                                        <option value='LAK' title='Laotian Kip'>LAK</option>
                                                                                        <option value='LBP' title='Lebanese Pound'>LBP</option>
                                                                                        <option value='LKR' title='Sri Lankan Rupee'>LKR</option>
                                                                                        <option value='LRD' title='Liberian Dollar'>LRD</option>
                                                                                        <option value='LSL' title='Lesotho Loti'>LSL</option>
                                                                                        <option value='LTL' title='Lithuanian Litas'>LTL</option>
                                                                                        <option value='LVL' title='Latvian Lats'>LVL</option>
                                                                                        <option value='LYD' title='Libyan Dinar'>LYD</option>
                                                                                        <option value='MAD' title='Moroccan Dirham'>MAD</option>
                                                                                        <option value='MDL' title='Moldovan Leu'>MDL</option>
                                                                                        <option value='MGA' title='Malagasy Ariary'>MGA</option>
                                                                                        <option value='MKD' title='Macedonian Denar'>MKD</option>
                                                                                        <option value='MMK' title='Myanma Kyat'>MMK</option>
                                                                                        <option value='MNT' title='Mongolian Tugrik'>MNT</option>
                                                                                        <option value='MOP' title='Macanese Pataca'>MOP</option>
                                                                                        <option value='MRO' title='Mauritanian Ouguiya'>MRO</option>
                                                                                        <option value='MUR' title='Mauritian Rupee'>MUR</option>
                                                                                        <option value='MVR' title='Maldivian Rufiyaa'>MVR</option>
                                                                                        <option value='MWK' title='Malawian Kwacha'>MWK</option>
                                                                                        <option value='MXN' title='Mexican Peso'>MXN</option>
                                                                                        <option value='MYR' title='Malaysian Ringgit'>MYR</option>
                                                                                        <option value='MZN' title='Mozambican Metical'>MZN</option>
                                                                                        <option value='NAD' title='Namibian Dollar'>NAD</option>
                                                                                        <option value='NGN' title='Nigerian Naira'>NGN</option>
                                                                                        <option value='NIO' title='Nicaraguan Córdoba'>NIO</option>
                                                                                        <option value='NOK' title='Norwegian Krone'>NOK</option>
                                                                                        <option value='NPR' title='Nepalese Rupee'>NPR</option>
                                                                                        <option value='NZD' title='New Zealand Dollar'>NZD</option>
                                                                                        <option value='OMR' title='Omani Rial'>OMR</option>
                                                                                        <option value='PAB' title='Panamanian Balboa'>PAB</option>
                                                                                        <option value='PEN' title='Peruvian Nuevo Sol'>PEN</option>
                                                                                        <option value='PGK' title='Papua New Guinean Kina'>PGK</option>
                                                                                        <option value='PHP' title='Philippine Peso'>PHP</option>
                                                                                        <option value='PKR' title='Pakistani Rupee'>PKR</option>
                                                                                        <option value='PLN' title='Polish Zloty'>PLN</option>
                                                                                        <option value='PYG' title='Paraguayan Guarani'>PYG</option>
                                                                                        <option value='QAR' title='Qatari Rial'>QAR</option>
                                                                                        <option value='RON' title='Romanian Leu'>RON</option>
                                                                                        <option value='RSD' title='Serbian Dinar'>RSD</option>
                                                                                        <option value='RUB' title='Russian Ruble'>RUB</option>
                                                                                        <option value='RWF' title='Rwandan Franc'>RWF</option>
                                                                                        <option value='SAR' title='Saudi Riyal'>SAR</option>
                                                                                        <option value='SBD' title='Solomon Islands Dollar'>SBD</option>
                                                                                        <option value='SCR' title='Seychellois Rupee'>SCR</option>
                                                                                        <option value='SDG' title='Sudanese Pound'>SDG</option>
                                                                                        <option value='SEK' title='Swedish Krona'>SEK</option>
                                                                                        <option value='SGD' title='Singapore Dollar'>SGD</option>
                                                                                        <option value='SHP' title='Saint Helena Pound'>SHP</option>
                                                                                        <option value='SLL' title='Sierra Leonean Leone'>SLL</option>
                                                                                        <option value='SOS' title='Somali Shilling'>SOS</option>
                                                                                        <option value='SRD' title='Surinamese Dollar'>SRD</option>
                                                                                        <option value='STD' title='São Tomé and Príncipe Dobra'>STD</option>
                                                                                        <option value='SVC' title='Salvadoran Colón'>SVC</option>
                                                                                        <option value='SYP' title='Syrian Pound'>SYP</option>
                                                                                        <option value='SZL' title='Swazi Lilangeni'>SZL</option>
                                                                                        <option value='THB' title='Thai Baht'>THB</option>
                                                                                        <option value='TJS' title='Tajikistani Somoni'>TJS</option>
                                                                                        <option value='TMT' title='Turkmenistani Manat'>TMT</option>
                                                                                        <option value='TND' title='Tunisian Dinar'>TND</option>
                                                                                        <option value='TOP' title='Tongan Pa?anga'>TOP</option>
                                                                                        <option value='TRY' title='Turkish Lira'>TRY</option>
                                                                                        <option value='TTD' title='Trinidad and Tobago Dollar'>TTD</option>
                                                                                        <option value='TWD' title='New Taiwan Dollar'>TWD</option>
                                                                                        <option value='TZS' title='Tanzanian Shilling'>TZS</option>
                                                                                        <option value='UAH' title='Ukrainian Hryvnia'>UAH</option>
                                                                                        <option value='UGX' title='Ugandan Shilling'>UGX</option>
                                                                                        <option value='1' title='United States Dollar'>USD</option>
                                                                                        <option value='UYU' title='Uruguayan Peso'>UYU</option>
                                                                                        <option value='UZS' title='Uzbekistan Som'>UZS</option>
                                                                                        <option value='VEF' title='Venezuelan Bolívar'>VEF</option>
                                                                                        <option value='VND' title='Vietnamese Dong'>VND</option>
                                                                                        <option value='VUV' title='Vanuatu Vatu'>VUV</option>
                                                                                        <option value='WST' title='Samoan Tala'>WST</option>
                                                                                        <option value='XAF' title='CFA Franc BEAC'>XAF</option>
                                                                                        <option value='XAG' title='Silver (troy ounce)'>XAG</option>
                                                                                        <option value='XAU' title='Gold (troy ounce)'>XAU</option>
                                                                                        <option value='XCD' title='East Caribbean Dollar'>XCD</option>
                                                                                        <option value='XDR' title='Special Drawing Rights'>XDR</option>
                                                                                        <option value='XOF' title='CFA Franc BCEAO'>XOF</option>
                                                                                        <option value='XPF' title='CFP Franc'>XPF</option>
                                                                                        <option value='YER' title='Yemeni Rial'>YER</option>
                                                                                        <option value='ZAR' title='South African Rand'>ZAR</option>
                                                                                        <option value='ZMK' title='Zambian Kwacha (pre-2013)'>ZMK</option>
                                                                                        <option value='ZMW' title='Zambian Kwacha'>ZWL</option>
                                                                                        <option value='ZWL' title='Zimbabwean Dollar'>ZWL</option>
                                                                                    </select>-->
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
                                                                                    Sub-Total:<span id="sub_total_freight">0.00</span>&nbsp;
                                                                                    <input type="hidden" id="total_freight_ammount" name="sub_total_freight" class="form-control"/>
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
                                                            <div class="row">
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
                                                            </div>
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
                                                                                    Sub-Total:<span id="sub_total_destination">0.00</span>&nbsp
                                                                                    <input type="hidden" id="total_destination_ammount" name="sub_total_destination" class="form-control"/>
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
                                                                        <h3><b>Total:</b> <span id="total"></span> </h3>
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
                                                Create Quote
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
    <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>
    <script>
        /*** GOOGLE MAPS API ***/

        var autocomplete;
        function initAutocomplete() {
            var geocoder = new google.maps.Geocoder();
            var autocomplete = new google.maps.places.Autocomplete((document.getElementById('origin_address')));
            var autocomplete_destination = new google.maps.places.Autocomplete((document.getElementById('destination_address')));
            //autocomplete.addListener('place_changed', fillInAddress);
        }

        function codeAddress(address) {
            var geocoder;
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == 'OK') {
                    alert(results[0].geometry.location);
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        }
    </script>
@stop
