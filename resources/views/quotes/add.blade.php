@extends('layouts.app')
@section('title', 'Add Quote')
@section('content')

<div class="m-content">
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

    @include('contacts.partials.contactsModal')
    @include('companies.partials.companiesModal')

    <!-- input with currency id -->
    <input type="hidden" id="currency_id" value="{{$currency_cfg->alphacode}}"/>

    <div class="row">
        <div class="col-lg-10 col-md-10 col-sm-10 col-12">
            @if(count($company_user->company_user_id)>0)
            <div class="row">
                <div class="col-md-12 col-12">
                    {!! Form::open(['route' => 'quotes.store','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
                    <div class="row">
                        <ul class="nav" role="tablist">
                            <li class="nav-item m-tabs__item" style="padding-top: 20px;padding-bottom: 20px;">
                                <button type="submit" class="btn btn-primary">
                                    Save
                                </button>
                                @if($email_templates)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#SendQuoteModal">
                                    Save and send
                                </button>
                                @endif
                                <button type="submit" class="btn btn-primary" formaction="/quotes/store/pdf">
                                    Save and PDF
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="m-portlet m-portlet--tabs">

                                <div class="m-portlet__body">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="m_portlet_tab_1_1">
                                            <br>
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
                                                                                <input name="type" value="1" id="fcl_type" type="radio">
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
                                                                                <input name="type" value="2" id="lcl_type" type="radio">
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
                                                                                <input name="type" value="3" id="air_type" type="radio">
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
                                                        </div>
                                                    </div>
                                                    <div class="form-group m-form__group row" id="fcl_load" style="display: none;">
                                                        <div class="col-lg-2">
                                                            <label>
                                                                <b>LOAD</b>
                                                            </label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <ul class="nav nav-tabs" role="tablist" style="text-transform: uppercase; letter-spacing: 1px;">
                                                                <li class="nav-item">
                                                                    <a href="#tab_2_1" class="nav-link active" data-toggle="tab" style=" font-weight: bold;"> Regular Containers </a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a href="#tab_2_2" class="nav-link" data-toggle="tab" style=" font-weight: bold;"> Reefer Containers </a>
                                                                </li>                                                         <li class="nav-item">
                                                                    <a href="#tab_2_3" class="nav-link" data-toggle="tab" style=" font-weight: bold;"> Special Equipment </a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane fade active show" id="tab_2_1">
                                                                    <br>
                                                                    <div class='row'>
                                                                        <div class="col-md-3">
                                                                            <label>
                                                                                <b>20' :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_20', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_20']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label>
                                                                                <b>40' :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_40', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_40']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label>
                                                                                <b>40' HC :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_40_hc', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_40_hc']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label>
                                                                                <b>45' HC :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_45_hc', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_45_hc']) !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="tab_2_2">
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <b>20' Reefer :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_20_reefer', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_20_reefer']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <b>40' Reefer :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_40_reefer', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_40_reefer']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <b>40' HC Reefer:</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_40_hc_reefer', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_40_hc_reefer']) !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="tab_2_3">
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <b>20' Open top :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_20_open_top', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_20_open_top']) !!}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                <b>40' Open top :</b>
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                {!! Form::text('qty_40_open_top', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control qty_40_open_top']) !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group m-form__group row" id="lcl_air_load" style="display: none;">
                                                        <div class="col-lg-2">
                                                            <label>
                                                                <b>LOAD</b>
                                                            </label>
                                                        </div>
                                                        <div class="col-lg-10">
                                                            <ul class="nav nav-tabs" role="tablist" style="text-transform: uppercase; letter-spacing: 1px;">
                                                                <li class="nav-item">
                                                                    <a href="#tab_1_1" class="nav-link active" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(1)"> Calculate by total shipment </a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a href="#tab_1_2" class="nav-link" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(2)"> Calculate by packaging </a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane fade active show" id="tab_1_1">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                Packages
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                <div class="input-group">
                                                                                    <input type="number" id="total_quantity" name="total_quantity" min="0" step="0.01" class="total_quantity form-control" placeholder="" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <select class="form-control" id="type_cargo" name="type_cargo">
                                                                                            <option value="1">Pallets</option>
                                                                                            <option value="2">Packages</option>
                                                                                        </select>
                                                                                    </div><!-- /btn-group -->
                                                                                </div><!-- /input-group -->
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                Total weight
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                <div class="input-group">
                                                                                    <input type="number" id="total_weight" name="total_weight" min="0" step="0.01" class="total_weight form-control" placeholder="" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">KG <span class="caret"></span></button>
                                                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                                                        </ul>
                                                                                    </div><!-- /btn-group -->
                                                                                </div><!-- /input-group -->
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label>
                                                                                Total volume
                                                                            </label>
                                                                            <div class="m-bootstrap-touchspin-brand">
                                                                                <div class="input-group">
                                                                                    <input type="number" id="total_volume" name="total_volume" min="0" step="0.01" class="total_volume form-control" placeholder="" aria-label="...">
                                                                                    <div class="input-group-btn">
                                                                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">CM <span class="caret"></span></button>
                                                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                                                        </ul>
                                                                                    </div><!-- /btn-group -->
                                                                                </div><!-- /input-group -->
                                                                            </div>
                                                                        </div>                                                                 
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="tab_1_2">
                                                                    <div class="template">
                                                                        <div class="row">
                                                                            <div class="col-md-2">
                                                                                <select name="type_load_cargo[]" class="type_cargo form-control size-12px">
                                                                                    <option value="">Choose an option</option>
                                                                                    <option value="1">Pallets</option>
                                                                                    <option value="2">Packages</option>
                                                                                </select>
                                                                                <input type="hidden" id="total_pallets" name="total_pallets"/>
                                                                                <input type="hidden" id="total_packages" name="total_packages"/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input id="quantity" min="1" value="" name="quantity[]" class="quantity form-control size-12px" type="number" placeholder="quantity" />
                                                                            </div>
                                                                            <div class="col-md-5" >
                                                                                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                                                                    <div class="btn-group" role="group">
                                                                                        <input class="height form-control size-12px" min="0" name="height[]" id="height" type="number" placeholder="H"/>
                                                                                    </div>
                                                                                    <div class="btn-group" role="group">
                                                                                        <input class="width form-control size-12px" min="0" name="width[]" id="width" type="number" placeholder="W"/>
                                                                                    </div>
                                                                                    <div class="btn-group" role="group">
                                                                                        <input class="large form-control size-12px" min="0" name="large[]" id="large" type="number" placeholder="L"/>
                                                                                    </div>
                                                                                    <div class="btn-group" role="group">
                                                                                        <div class="input-group-btn">
                                                                                            <div class="btn-group">
                                                                                                <button class="btn btn-default dropdown-toggle dropdown-button" type="button" data-toggle="dropdown">
                                                                                                    <span class="xs-text size-12px">CM</span> <span class="caret"></span>
                                                                                                </button>
                                                                                                <ul class="dropdown-menu" role="menu">

                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="input-group">
                                                                                    <input type="number" id="weight" name="weight[]" min="0" step="0.01" class="weight form-control size-12px" placeholder="Weight" aria-label="...">
                                                                                </div><!-- /input-group -->
                                                                            </div>
                                                                            <div class="col-md-1">
                                                                                <input type="hidden" class="volume_input" id="volume_input" name="volume[]"/>
                                                                                <input type="hidden" class="quantity_input" id="quantity_input" name="total_quantity_pkg"/>
                                                                                <input type="hidden" class="weight_input" id="weight_input" name="total_weight_pkg"/>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <br>
                                                                                <p class=""><span class="quantity"></span> <span class="volume"></span> <span class="weight"></span></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="template hide" id="lcl_air_load_template">
                                                                        <div class="row" style="padding-top: 15px;">
                                                                            <div class="col-md-2">
                                                                                <select name="type_load_cargo[]" class="type_cargo form-control size-12px">
                                                                                    <option value="">Choose an option</option>
                                                                                    <option value="1">Pallets</option>
                                                                                    <option value="2">Packages</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input id="quantity" min="1" value="" name="quantity[]" class="quantity form-control size-12px" type="number" placeholder="quantity" />
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                                                                    <div class="btn-group" role="group">
                                                                                        <input class="height form-control size-12px" min="0" name="height[]" id="al" type="number" placeholder="H"/>
                                                                                    </div>
                                                                                    <div class="btn-group" role="group">
                                                                                        <input class="width form-control size-12px" min="0" name="width[]" id="an" type="number" placeholder="W"/>
                                                                                    </div>
                                                                                    <div class="btn-group" role="group">
                                                                                        <input class="large form-control size-12px" min="0" name="large[]" id="la" type="number" placeholder="L"/>
                                                                                    </div>
                                                                                    <div class="btn-group" role="group">
                                                                                        <div class="input-group-btn">
                                                                                            <div class="btn-group">
                                                                                                <button class="btn btn-default dropdown-toggle dropdown-button" type="button" data-toggle="dropdown">
                                                                                                    <span class="xs-text size-12px">CM</span> <span class="caret"></span>
                                                                                                </button>
                                                                                                <ul class="dropdown-menu" role="menu">

                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="input-group">
                                                                                    <input type="number" name="weight[]" min="0" step="0.01" class="weight form-control size-12px" placeholder="Weight" aria-label="...">
                                                                                </div><!-- /input-group -->
                                                                            </div>
                                                                            <div class="col-md-1">                     
                                                                                <a class="remove_lcl_air_load" style="cursor: pointer;"><i class="fa fa-trash"></i></a>
                                                                                <input type="hidden" class="volume_input" id="volume_input" name="volume[]"/>
                                                                                <input type="hidden" class="quantity_input" id="quantity_input" name="total_quantity_pkg"/>
                                                                                <input type="hidden" class="weight_input" id="weight_input" name="total_weight_pkg"/>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <br>
                                                                                <p class=""><span class="quantity"></span> <span class="volume"></span> <span class="weight"></span></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <br>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <b>Total: </b>
                                                                            <span id="total_quantity_pkg"></span>
                                                                            <span id="total_volume_pkg"></span>
                                                                            <span id="total_weight_pkg"></span>
                                                                            <input type="hidden" id="total_quantity_pkg_input" name="total_quantity_pkg"/>
                                                                            <input type="hidden" id="total_volume_pkg_input" name="total_volume_pkg"/>
                                                                            <input type="hidden" id="total_weight_pkg_input" name="total_weight_pkg"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <br>
                                                                            <div id="saveActions" class="form-group">
                                                                                <div class="btn-group">
                                                                                    <button type="button" id="add_load_lcl_air" class="add_load_lcl_air btn btn-info btn-sm">
                                                                                        <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                        <span>Add load</span>
                                                                                    </button>
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
                                            <br>
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
                                                        <div class="col-md-5" id="delivery_type_label">
                                                            <label>Delivery type</label>
                                                            {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
                                                        </div>
                                                        <div class="col-md-5" id="delivery_type_air_label" style="display: none;">
                                                            <label>Delivery type</label>
                                                            {{ Form::select('delivery_type',['5' => 'AIRPORT(Origin) To AIRPORT(Destination)','6' => 'AIRPORT(Origin) To DOOR(Destination)','7'=>'DOOR(Origin) To AIRPORT(Destination)','8'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type_air']) }}
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>Pick up date</label>
                                                            <div class="input-group date">
                                                                {!! Form::text('pick_up_date', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select a date','class' => 'form-control m-input pick_up_date','required'=>'true','autocomplete'=>'off']) !!}
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
                                                        <div class="col-md-4">
                                                            <div id="origin_harbor_label">
                                                                <label>Origin port</label>
                                                                {{ Form::select('origin_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'origin_harbor','placeholder'=>'Select an option']) }}
                                                            </div>
                                                            <div id="origin_airport_label" style="display:none;">
                                                                <label>Origin airport</label>
                                                                <select id="origin_airport" name="origin_airport_id" class="form-control"></select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 hide" id="origin_address_label">
                                                            <label>Origin address</label>
                                                            {!! Form::text('origin_address', null, ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div id="destination_harbor_label">
                                                                <label>Destination port</label>
                                                                {{ Form::select('destination_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'destination_harbor','placeholder'=>'Select an option']) }}
                                                            </div>
                                                            <div id="destination_airport_label" style="display:none;">
                                                                <label>Destination airport</label>
                                                                <select id="destination_airport" name="destination_airport_id" class="form-control"></select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8 hide" id="destination_address_label">
                                                            <label>Destination address</label>
                                                            {!! Form::text('destination_address', null, ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <br>
                                                            <label>Validity</label>
                                                            <div class="input-group date">
                                                                {!! Form::text('validity', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select a date','class' => 'form-control m-input validity','required'=>'true','autocomplete'=>'off']) !!}
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">
                                                                        <i class="la la-calendar-check-o"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4" id="carrier_label">
                                                            <br>
                                                            <label>Carrier</label>
                                                            <div class="form-group">
                                                                {{ Form::select('carrier_id',$carriers,null,['class'=>'custom-select form-control','id' => 'carrier_id','placeholder'=>'Choose an option']) }}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4" id="airline_label" style="display:none;">
                                                            <br>
                                                            <label>Airline</label>
                                                            <div class="form-group">
                                                                {{ Form::select('airline_id',$airlines,null,['class'=>'custom-select form-control','id' => 'airline_id','placeholder'=>'Choose an option']) }}
                                                            </div>
                                                        </div>                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
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
                                                            {{ Form::select('company_id',$companies,null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control company_id','id' => 'm_select2_2_modal','required'=>true]) }}<br><br>
                                                            <a  class="btn btn-primary btn-sm m-btn m-btn--icon" onclick="AbrirModal('addCompany',0)">
                                                                <span style="color: white;">
                                                                    <i class="la la-plus"></i>
                                                                    <span>Add Company</span>
                                                                </span>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-4 col-sm-4 ol-xs-12">
                                                            <label>Client</label>
                                                            {{ Form::select('contact_id',[],null,['class'=>'m-select2-general form-control contact_id','required'=>true]) }}<br><br>
                                                            <a  class="btn btn-sm btn-primary m-btn m-btn--icon" onclick="AbrirModal('addContact',0)">
                                                                <span style="color: white;">
                                                                    <i class="la la-plus"></i>
                                                                    <span>Add Contact</span>
                                                                </span>
                                                            </a>
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
                                                                <div class="panel-heading title-quote size-14px"><b>Origin</b></div>
                                                                <div class="panel-body">
                                                                    <span id="origin_input"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="panel panel-default">
                                                                <div class="panel-heading title-quote size-14px"><b>Destination</b></div>
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
                                                    <div style="padding-top: 20px; padding-bottom: 20px;">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5 class="title-quote size-14px">Cargo details</h5>
                                                                <hr>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <p id="cargo_details_20_p" class="hide"><span id="cargo_details_20"></span> x 20' Containers</p>
                                                                <p id="cargo_details_40_p" class="hide"><span id="cargo_details_40"></span> x 40' Containers</p>
                                                                <p id="cargo_details_40_hc_p" class="hide"><span id="cargo_details_40_hc"></span> x 40' HC Containers</p>
                                                                <p id="cargo_details_45_hc_p" class="hide"><span id="cargo_details_45_hc"></span> x 45' HC Containers</p> 
                                                                <p id="cargo_details_20_reefer_p" class="hide"><span id="cargo_details_20_reefer"></span> x 20' Reefer Containers</p>
                                                                <p id="cargo_details_40_reefer_p" class="hide"><span id="cargo_details_40_reefer"></span> x 40' Reefer Containers</p>
                                                                <p id="cargo_details_40_hc_reefer_p" class="hide"><span id="cargo_details_40_hc_reefer"></span> x 40' HC Reefer Containers</p>
                                                                <p id="cargo_details_20_open_top_p" class="hide"><span id="cargo_details_20_open_top"></span> x 20' Open Top Containers</p> 
                                                                <p id="cargo_details_40_open_top_p" class="hide"><span id="cargo_details_40_open_top"></span> x 40' Open Top Containers</p> <p id="cargo_details_40_hc_open_top_p" class="hide"><span id="cargo_details_40_hc_open_top"></span> x 40' HC Open Top Containers</p>           
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div id="cargo_details_cargo_type_p" class="hide"><b>Cargo type:</b> <span id="cargo_details_cargo_type"></span></div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div id="cargo_details_total_quantity_p" class="hide">  <b>Total quantity:</b> <span id="cargo_details_total_quantity"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div id="cargo_details_total_weight_p" class="hide"><b>Total weight: </b> <span id="cargo_details_total_weight"></span> KG</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <p id="cargo_details_total_volume_p" class="hide"><b>Total volume: </b> <span id="cargo_details_total_volume"></span> m<sup>3</sup></p>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12" id="label_package_loads">

                                                            </div>
                                                        </div>
                                                        <div class="row pull-right">
                                                            <div class="col-md-12">
                                                                <div id="cargo_details_total_pkg_p" class="hide">  <b>Total:</b> 
                                                                    <span id="cargo_details_total_quantity_pkg"></span> un&nbsp;
                                                                    <span id="cargo_details_total_volume_pkg"></span> m<sup>3</sup>&nbsp;
                                                                    <span id="cargo_details_total_weight_pkg"></span> km
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
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
                                                                                <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" step="0.01" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" type="number" step="0.01" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="number" name="origin_total_ammount_2[]" step="0.01" min="0" value="" class="origin_total_ammount_2 form-control" aria-label="...">
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="hide" id="origin_ammounts">
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
                                                                                <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" step="0.01" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" type="number" step="0.01" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="text" name="origin_total_ammount_2[]" step="0.01" min="0" value="" class="origin_total_ammount_2 form-control" aria-label="...">
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
                                                                                <input id="freight_ammount_units" name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0" step="0.01" required/>
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
                                                                                <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" type="number" step="0.01" min="0"/> 
                                                                            </td>                      
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="number" name="freight_total_ammount_2[]" step="0.01" min="0" value="" class="freight_total_ammount_2 form-control" aria-label="..." required>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="hide" id="freight_ammounts">
                                                                            <td>
                                                                                <input type="text" class="form-control" id="freight_ammount_charge" value="" name="freight_ammount_charge[]"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="" class="form-control" type="text"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_units" name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0" step="0.01"/>
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
                                                                                <input id="freight_total_ammount" name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" step="0.01" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" type="number" step="0.01" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="number" name="freight_total_ammount_2[]"  value="" class="freight_total_ammount_2 form-control" aria-label="..." step="0.01" min="0"/>
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
                                                                                <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0" step="0.01"/>
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
                                                                                <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" step="0.01" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" type="number" step="0.01" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="number" name="destination_total_ammount_2[]" step="0.01" min="0" value="" class="destination_total_ammount_2 form-control" aria-label="...">
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                        <tr class="hide" id="destination_ammounts">
                                                                            <td>
                                                                                <input type="text" class="form-control" id="destination_ammount_charge" value="" name="destination_ammount_charge[]"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="" class="form-control" type="text"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0" step="0.01"/>
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
                                                                                <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" step="0.01" min="0"/>
                                                                            </td>
                                                                            <td>
                                                                                <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" type="number" step="0.01" min="0"/> 
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group">
                                                                                    <div class="input-group">
                                                                                        <input type="number" name="destination_total_ammount_2[]" step="0.01" min="0" value="" class="destination_total_ammount_2 form-control" aria-label="...">
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
                                                    <!-- Schedules -->
                                                    <div id="infoschedule" class="row" hidden="true">
                                                        <div class="col-md-3">
                                                            <h5 class="title-quote size-14px">Schedules</h5>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table id="schetable" class="table table-bordered color-blue">
                                                                    <thead class="title-quote text-center header-table">
                                                                        <tr>
                                                                            <th><span class="">Vessel</span></th>
                                                                            <th><span class="">ETD</span></th>
                                                                            <th><span class=""><center>Transit Time</center></span>  </th>
                                                                            <th><span class="">ETA</span></th>

                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="scheduleBody">


                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" class="form-control" id="schedule" name="schedule_manual" value="">
                                                    </div>                                          
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <a class="btn btn-primary btn-sm m-btn m-btn--icon" onclick="AbrirModal('add')">
                                                        <span style="color: white;">
                                                            <i class="la la-plus"></i>
                                                            <span>Schedules </span>
                                                        </span>
                                                    </a>
                                                    <br>
                                                    <br>
                                                    <a class="btn btn-outline-danger btn-sm m-btn m-btn--icon removesche" hidden="true" >
                                                        <span>
                                                            <i class="la la-remove"></i>
                                                            <span>Remove</span>
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>                                            
                                            <hr>
                                            <div class="row pull-right">
                                                <ul class="nav" role="tablist">
                                                    <li class="nav-item m-tabs__item" style="padding-top: 20px;padding-bottom: 20px;">
                                                        <button type="submit" class="btn btn-primary">
                                                            Save
                                                        </button>
                                                        @if($email_templates)
                                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#SendQuoteModal">
                                                            Save and send
                                                        </button>
                                                        @endif
                                                        <button type="submit" class="btn btn-primary" formaction="/quotes/store/pdf">
                                                            Save and PDF
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    @if($email_templates)
                    @include('quotes.partials.submitQuoteEmailModal')
                    @endif
                    <div class="row pull-right">
                        <ul class="nav" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="btn btn-primary" id="create-quote" data-toggle="tab" href="#m_portlet_tab_1_2" role="tab">
                                    Next
                                </a>
                                <a class="btn btn-primary" id="create-quote-back" style="display: none;" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                    Back
                                </a>
                            </li>
                        </ul>
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
        <div class="col-lg-2 col-md-2 col-sm-2 col-12 desktop">
            <h3 class="title-quote size-16px">Settings</h3>
            <hr>
            <p class="title-quote size-14px" data-toggle="collapse" data-target="#main_currency" style="cursor: pointer">Main currency <i class="fa fa-angle-down pull-right"></i></p>
            @if(isset($currency_cfg->alphacode))
            <input type="hidden" value="{{$currency_cfg->alphacode}}" id="currency_id">
            @endif
            <p class="settings size-12px" id="main_currency" class="collapse" style="font-weight: lighter">  @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif </p>
            <hr>
            <p class="title-quote title-quote size-14px" data-toggle="collapse" data-target="#exchange_rate" style="cursor: pointer">Exchange rate <i class="fa fa-angle-down pull-right"></i></p>
            @if(isset($currency_cfg->alphacode))
            <p class="settings size-12px" id="exchange_rate" style="font-weight: 100">@if($currency_cfg->alphacode=='EUR') 1 EUR = {{$exchange->rates}} USD @else 1 USD = {{$exchange->rates_eur}} EUR @endif</p>
            @endif
        </div>        
    </div>
</div>

@include('quotes.partials.schedulesModal')


@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="{{asset('/js/quote.js')}}"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>
<script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>
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

    function AbrirModal(action){
        if(action == "add"){
            var orig_p = $('#origin_harbor').val();
            var dest_p = $('#destination_harbor').val();
            var date_p = $('.pick_up_date').val();
            if(orig_p ==""){
                msg('Sorry the origin port is empty');
                return;
            }
            if(dest_p ==""){
                msg('Sorry the destination port is empty');
                return;
            }
            if(date_p ==""){
                msg('Sorry the date is empty');
                return;
            }
            var url = '{{ route("quotes.schedule", "orig_port/dest_port/date_p") }}';

            url = url.replace('orig_port', orig_p).replace('dest_port', dest_p).replace('date_p', date_p);

            $('#spinner').show();
            $('#scheduleModal').modal({show:true});
            $('.modal-body').load(url, function (response, status, xhr) {

                $('#scheduleModal').modal({show:true});
                $('#spinner').hide();
            });
        }
        if(action == "addCompany"){
            var url = '{{ route("companies.addM") }}';
            $('#modal-body').load(url,function(){
                $('#companyModal').modal({show:true});
            });
        }
        if(action == "addContact"){
            var url = '{{ route("contacts.addCM") }}';
            $('.modal-body').load(url,function(){
                $('#contactModal').modal({show:true});
            });
        }
    }
</script>
@stop