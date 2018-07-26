@extends('layouts.app')
@section('title', 'Add Quote')
@section('content')

<div class="m-content">
    <div class="row">
        <div class="col-md-2">
            <button class="btn btn-primary btn-block">Schedules</button>
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
    <div class="row">
        <div class="col-md-10">
            @if(count($company_user->company_user_id)>0)
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
                                                    Next
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
                                                                    {{ Form::select('modality',['1' => 'Export','2' => 'Import'],null,['class'=>'m-select2-general form-control','required'=>'true']) }}
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Incoterm</label>
                                                                    {{ Form::select('incoterm',$incoterm,null,['class'=>'m-select2-general form-control','required'=>'true']) }}
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label>Delivery type</label>
                                                                    {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label>Pick up date</label>
                                                                    <div class="input-group date">
                                                                        {!! Form::text('pick_up_date', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select a date','class' => 'form-control m-input','required'=>'true']) !!}
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
                                                                    {{ Form::select('origin_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'origin_harbor','placeholder'=>'Select an option']) }}
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
                                                                    {{ Form::select('destination_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'destination_harbor','placeholder'=>'Select an option']) }}
                                                                </div>
                                                                <div class="col-md-8" id="destination_address_label" style="display: none;">
                                                                    <label>Destination address</label>
                                                                    {!! Form::text('destination_address', null, ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <br>
                                                                    <label>Validity</label>
                                                                    <div class="input-group date">
                                                                        {!! Form::text('validity', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select a date','class' => 'form-control m-input','required'=>'true']) !!}
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">
                                                                                <i class="la la-calendar-check-o"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <br>
                                                                    <label>Sale terms</label>
                                                                    <div class="form-group">
                                                                        {{ Form::select('sale_term_id',$saleterms,null,['class'=>'custom-select form-control','id' => 'sale_term_id','placeholder'=>'Choose an option']) }}
                                                                    </div>
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
                                                            <div class="row">
                                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                                    <label>Company</label>
                                                                    {{ Form::select('company_id',$companies,null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal','required'=>true]) }}
                                                                </div>
                                                                <div class="col-md-4 col-sm-4 ol-xs-12">
                                                                    <label>Client</label>
                                                                    {{ Form::select('contact_id',[],null,['class'=>'m-select2-general form-control','required'=>true]) }}
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
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-12">

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="panel panel-default">
                                                                <div class="panel-heading title-quote size-14px"><b>Origin Port</b></div>
                                                                <div class="panel-body">
                                                                    <span id="origin_input"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="panel panel-default">
                                                                <div class="panel-heading title-quote size-14px"><b>Destination Port</b></div>
                                                                <div class="panel-body">
                                                                    <span id="destination_input"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="panel panel-default hide" id="origin_address_panel">
                                                                <div class="panel-heading title-quote size-14px"><b>Origin Address</b></div>
                                                                <div class="panel-body">
                                                                    <span id="origin_address_p"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="panel panel-default hide" id="destination_address_panel">
                                                                <div class="panel-heading title-quote size-14px"><b>Destination Address</b></div>
                                                                <div class="panel-body">
                                                                    <span id="destination_address_p"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="row" style="padding-top: 20px; padding-bottom: 20px;">
                                                        <div class="col-md-12">
                                                            <h5 class="title-quote size-14px">Cargo details</h5>
                                                            <hr>
                                                            <p id="cargo_details_20_p" class="hide"><span id="cargo_details_20"></span> x 20' Containers</p>
                                                            <p id="cargo_details_40_p" class="hide"><span id="cargo_details_40"></span> x 40' Containers</p>
                                                            <p id="cargo_details_40_hc_p" class="hide"><span id="cargo_details_40_hc"></span> x 40' HC Containers</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <h5 class="title-quote size-14px">Origin ammounts</h5>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered color-blue">
                                                                    <thead class="title-quote text-center header-table">
                                                                        <tr>
                                                                            <td >Charge</td>
                                                                            <td >Detail</td>
                                                                            <td >Units</td>
                                                                            <td >Price per unit</td>
                                                                            <td >Total</td>
                                                                            <td >Markup</td>
                                                                            <td >Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control" id="origin_ammount_charge" value="" name="origin_ammount_charge[]"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="" class="form-control" type="text"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_units" name="origin_ammount_units[]" value="" class="form-control origin_ammount_units" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <div class="btn-group">
                                                                                            {{ Form::select('origin_ammount_currency[]',$currencies,null,['class'=>'form-control origin_ammount_currency']) }}              
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" type="number" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="text" name="origin_total_ammount_2[]"  value="" class="origin_total_ammount_2 form-control" aria-label="...">
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        
                                                                        <tr class="hide"  id="origin_ammounts">
                                                                            <td>
                                                                                <input type="text" class="form-control" id="origin_ammount_charge" value="" name="origin_ammount_charge[]"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="" class="form-control" type="text"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_units" name="origin_ammount_units[]" value="" class="form-control origin_ammount_units" type="number" min="0" step="0.01"/>
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <div class="btn-group">
                                                                                            {{ Form::select('origin_ammount_currency[]',$currencies,null,['class'=>'form-control origin_ammount_currency']) }}              
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" type="number" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="text" name="origin_total_ammount_2[]"  value="" class="origin_total_ammount_2 form-control" aria-label="...">
                                                                                        <a class="btn removeOriginButton">
                                                                                            <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
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
                                                                Sub-Total: <span id="sub_total_origin"></span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                                                <input type="hidden" id="total_origin_ammount" name="sub_total_origin" value=""  class="form-control"/>
                                                                <a class="btn addButtonOrigin" style="vertical-align: middle">
                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                </a>
                                                            </h5>                                                            
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <h5 class="title-quote size-14px">Freight ammounts</h5>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered color-blue">
                                                                    <thead class="title-quote text-center header-table">
                                                                        <tr>
                                                                            <td >Charge</td>
                                                                            <td >Detail</td>
                                                                            <td >Units</td>
                                                                            <td >Price per unit</td>
                                                                            <td >Total</td>
                                                                            <td >Markup</td>
                                                                            <td >Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control" id="freight_ammount_charge" value="" name="freight_ammount_charge[]" required />
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="" class="form-control" type="text" required/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_units" name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0" required/>
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="" min="1" step="0.01" class="freight_price_per_unit form-control" aria-label="..." required>
                                                                                    <div class="input-group-btn">
                                                                                        <div class="btn-group">
                                                                                            {{ Form::select('freight_ammount_currency[]',$currencies,null,['class'=>'form-control freight_ammount_currency','required'=>true]) }}              
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_total_ammount" name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" min="0"   step="0.01" required/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" type="number" min="0" required/> 
                                                                            </td>                      
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="text" name="freight_total_ammount_2[]"  value="" class="freight_total_ammount_2 form-control" aria-label="..." required>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        
                                                                        <tr class="hide"  id="freight_ammounts">
                                                                            <td>
                                                                                <input type="text" class="form-control" id="freight_ammount_charge" value="" name="freight_ammount_charge[]"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="" class="form-control" type="text"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_units" name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="" min="1" step="0.01" class="freight_price_per_unit form-control" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <div class="btn-group">
                                                                                            {{ Form::select('freight_ammount_currency[]',$currencies,null,['class'=>'form-control freight_ammount_currency']) }}              
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_total_ammount" name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" type="number" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="text" name="freight_total_ammount_2[]"  value="" class="freight_total_ammount_2 form-control" aria-label="...">
                                                                                        <a class="btn removeButton">
                                                                                            <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
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
                                                                Sub-Total: <span id="sub_total_freight"></span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                                                <input type="hidden" id="total_freight_ammount" name="sub_total_freight" value=""  class="form-control"/>
                                                                <a class="btn addButton" style="vertical-align: middle">
                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                </a>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <h5 class="title-quote size-14px">Destination ammounts</h5>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered color-blue">
                                                                    <thead class="title-quote text-center header-table">
                                                                        <tr>
                                                                            <td >Charge</td>
                                                                            <td >Detail</td>
                                                                            <td >Units</td>
                                                                            <td >Price per unit</td>
                                                                            <td >Total</td>
                                                                            <td >Markup</td>
                                                                            <td >Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control" id="destination_ammount_charge" value="" name="destination_ammount_charge[]"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="" class="form-control" type="text"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <div class="btn-group">
                                                                                            {{ Form::select('destination_ammount_currency[]',$currencies,null,['class'=>'form-control destination_ammount_currency']) }}              
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" type="number" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="text" name="destination_total_ammount_2[]"  value="" class="destination_total_ammount_2 form-control" aria-label="...">
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        
                                                                        <tr class="hide"  id="destination_ammounts">
                                                                            <td>
                                                                                <input type="text" class="form-control" id="destination_ammount_charge" value="" name="destination_ammount_charge[]"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="" class="form-control" type="text"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <div class="input-group">
                                                                                    <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <div class="btn-group">
                                                                                            {{ Form::select('destination_ammount_currency[]',$currencies,null,['class'=>'form-control destination_ammount_currency']) }}              
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" type="number" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="text" name="destination_total_ammount_2[]"  value="" class="destination_total_ammount_2 form-control" aria-label="...">
                                                                                        <a class="btn removeButtonDestination">
                                                                                            <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
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
                                                                Sub-Total: <span id="sub_total_destination"></span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                                                <input type="hidden" id="total_destination_ammount" name="sub_total_destination" value=""  class="form-control"/>
                                                                <a class="btn addButtonDestination" style="vertical-align: middle">
                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                </a>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class='row'>
                                                        <div class="col-md-12">
                                                            <h5 class="title-quote pull-right size-16px">
                                                                Total: <span id="total"></span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-lg-4 col-lg-offset-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        Save
                                                    </button>
                                                    <button type="button" class="btn btn-success">
                                                        Save and send
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>

            </div>
            @else
            <div class="row">
                <div class="col-md-12">
                    <div class="m-portlet m-portlet--tabs">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-tools">
                                <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
                                    <li class="nav-item m-tabs__item" style="padding-top: 20px;padding-bottom: 20px;">
                                        <a href="{{url('settings')}}" class="btn btn-primary" role="tab">
                                            Go to settings
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-portlet__body">
                                <div class="tab-content">
                                    <div class="tab-pane active text-center" id="m_portlet_tab_1_1">
                                        <h5>If you are account admin, you must configure company's preferences in the settings section. If not, please request that to account admin</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-2">
            <h3 class="title-quote size-16px">Settings</h3>
            <hr>
            <p class="title-quote size-14px" data-toggle="collapse" data-target="#main_currency" style="cursor: pointer">Main currency <i class="fa fa-angle-down pull-right"></i></p>
            <input type="hidden" value="{{$currency_cfg->alphacode}}" id="currency_id">
            <p class="settings size-12px" id="main_currency" class="collapse" style="font-weight: lighter">  @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif </p>
            <hr>
            <p class="title-quote title-quote size-14px" data-toggle="collapse" data-target="#exchange_rate" style="cursor: pointer">Exchange rate <i class="fa fa-angle-down pull-right"></i></p>
            <p class="settings size-12px" id="exchange_rate" style="font-weight: 100">@if($currency_cfg->alphacode=='EUR') 1 EUR = {{$exchange->rates}} USD @else 1 USD = {{$exchange->rates_eur}} EUR @endif</p>
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
