@extends('layouts.app')
@section('title', 'Edit Quote')
@section('content')

  <div class="m-content">
    <div class="row">
    <!--<div class="col-md-1">
<a href="" class="btn btn-primary btn-block">PDF</a>
</div>
<div class="col-md-2">
<button class="btn btn-primary btn-block">Schedules</button>
</div>
<div class="col-md-1">
<button data-toggle="modal" data-target="#SendQuoteModal" class="btn btn-info btn-block">Send</button>
<input type="hidden" id="quote-id" value="{{$quote->id}}"/>
</div>-->
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
  <!-- input with currency id -->
    <input type="hidden" id="currency_id" value="{{$currency_cfg->alphacode}}"/>
    <div class="row">
      <div class="col-md-10">
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
                          <a class="btn btn-primary" id="create-quote" style="display: none;" data-toggle="tab" href="#m_portlet_tab_1_2" role="tab">
                            Back
                          </a>
                          <a class="btn btn-primary" id="create-quote-back" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                            Go to first page
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="m-portlet__body">
                    <div class="tab-content">
                      <div class="tab-pane" id="m_portlet_tab_1_1">
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="form-group m-form__group row">
                              <div class="col-lg-2">
                                <label>
                                  <b>MODE</b>
                                </label>
                              </div>
                              <div class="col-lg-10">
                                <div class='row'>
                                  <div class="col-md-4">
                                    <label class="m-option" >
                                    <span class="m-option__control">
                                      <span class="m-radio m-radio--brand m-radio--check-bold">
                                        <input name="type" value="1" id="fcl_type" type="radio" {!! $quote->type == 1 ? 'checked':'disabled' !!}>
                                        <span></span>
                                      </span>
                                    </span>
                                      <span class="m-option__label" >
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
                                        <input name="type" id="lcl_type" value="2" type="radio" {!! $quote->type == 2 ? 'checked':'disabled' !!}>
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
                                        <input name="type" value="3" id="air_type" type="radio" {!! $quote->type == 3 ? 'checked':'disabled' !!}>
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
                              </div>
                            </div>
                            <br>
                            <div class="form-group m-form__group row" id="fcl_load" {{$quote->type!=1 ? 'style=display:none':''}}>
                              <div class="col-lg-2">
                                <label>
                                  <b>LOAD</b>
                                </label>
                              </div>
                              <div class="col-lg-8">
                                <ul class="nav nav-tabs" role="tablist" style="text-transform: uppercase; letter-spacing: 1px;">
                                  <li class="nav-item">
                                    <a href="#tab_2_1" class="nav-link active" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(1)"> Basic Containers </a>
                                  </li>
                                  <li class="nav-item">
                                    <a href="#tab_2_2" class="nav-link" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(2)"> Reefer Containers </a>
                                  </li>                                                                <li class="nav-item">
                                    <a href="#tab_2_3" class="nav-link" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(2)"> Special Equipment </a>
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
                            <div class="form-group m-form__group row" id="lcl_air_load" {{$quote->type==1 ? 'style=display:none;':''}}>
                              <div class="col-lg-2">
                                <label>
                                  <b>LOAD</b>
                                </label>
                              </div>

                              <div class="col-lg-10">
                                <ul class="nav nav-tabs" role="tablist" style="text-transform: uppercase; letter-spacing: 1px;">
                                  <li class="nav-item">
                                    <a href="#tab_1_1" class="nav-link {{$quote->total_quantity!='' || $quote->total_weight!='' ? 'active':''}}" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(1)"> Calculate by total shipment </a>
                                  </li>
                                  <li class="nav-item">
                                    <a href="#tab_1_2" class="nav-link {{$quote->packages->count() > 0 ? 'active' : ''}}" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(2)"> Calculate by packaging </a>
                                  </li>
                                </ul>
                                <div class="tab-content">
                                  <div class="tab-pane fade {{$quote->total_quantity!='' || $quote->total_weight!='' ? 'active show':''}}" id="tab_1_1">
                                    <div class="row">
                                      <div class="col-md-4">
                                        <label>
                                          Packages
                                        </label>
                                        <div class="m-bootstrap-touchspin-brand">
                                          <div class="input-group">
                                            <input type="number" id="total_quantity" name="total_quantity" min="0" step="0.0001" class="total_quantity form-control" value="{{$quote->total_quantity}}" placeholder="" aria-label="...">
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
                                            <input type="number" id="total_weight" name="total_weight" min="0" step="0.0001" class="total_weight form-control" placeholder="" value="{{$quote->total_weight}}" aria-label="...">
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
                                            <input type="number" id="total_volume" name="total_volume" min="0" step="0.0001" class="total_volume form-control" placeholder="" value="{{$quote->total_volume}}" aria-label="...">
                                            <div class="input-group-btn">
                                              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">CM <span class="caret"></span></button>
                                              <ul class="dropdown-menu dropdown-menu-right">
                                              </ul>
                                            </div><!-- /btn-group -->
                                          </div><!-- /input-group -->
                                        </div>
                                      </div>
                                      <div class="col-md-12">
                                        <br>
                                        <br>
                                        <b>Chargeable weight:</b>
                                        <span id="chargeable_weight_total"></span>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="tab-pane fade {{$quote->packages->count() > 0 ? 'active show' : ''}}" id="tab_1_2">
                                    @php
                                      $i=0;
                                    @endphp
                                    @if($quote->packages->count() > 0)
                                      @foreach($quote->packages as $item)
                                        @php
                                          $i++;
                                        @endphp
                                        <div class="row template">
                                          <div class="col-md-2">
                                            <select name="type_load_cargo[]" class="type_cargo form-control size-12px">
                                              <option value="">Choose an option</option>
                                              <option value="1" {{$item->type_cargo == 1 ? 'selected':''}}>Pallets</option>
                                              <option value="2" {{$item->type_cargo == 2 ? 'selected':''}}>Packages</option>
                                            </select>
                                            <input type="hidden" id="total_pallets" name="total_pallets"/>
                                            <input type="hidden" id="total_packages" name="total_packages"/>
                                          </div>
                                          <div class="col-md-2">

                                            <input id="quantity" min="1" value="{{$item->quantity}}" name="quantity[]" class="quantity form-control size-12px" type="number" placeholder="quantity" />
                                          </div>
                                          <div class="col-md-5" >
                                            <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                              <div class="btn-group" role="group">
                                                <input class="height form-control size-12px" min="0" name="height[]" id="height" type="number" placeholder="H" value="{{$item->height}}"/>
                                              </div>
                                              <div class="btn-group" role="group">
                                                <input class="width form-control size-12px" min="0" name="width[]" id="width" type="number" value="{{$item->width}}" placeholder="W"/>
                                              </div>
                                              <div class="btn-group" role="group">
                                                <input class="large form-control size-12px" min="0" name="large[]" id="large" type="number" value="{{$item->large}}" placeholder="L"/>
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
                                              <input type="number" id="weight" name="weight[]" min="0" step="0.0001" class="weight form-control size-12px" placeholder="Weight" value="{{$item->weight}}" aria-label="...">
                                            </div><!-- /input-group -->
                                          </div>
                                          <div class="col-md-1">
                                            @if($i>1)
                                              <a class="remove_lcl_air_load" style="cursor: pointer;"><i class="fa fa-trash"></i></a>
                                            @endif
                                            <input type="hidden" class="quantity_input" id="volume_input" value="{{$item->quantity}}" name="total_quantity_pkg[]"/>
                                            <input type="hidden" class="volume_input" id="volume_input" value="{{$item->volume}}" name="volume[]"/>
                                            <input type="hidden" class="weight_input" id="volume_input" value="{{$item->weight*$item->quantity}}" name="total_weight_pkg[]"/>
                                          </div>
                                          <div class="col-md-4">
                                            <br>
                                            <p class=""><span class="quantity">{{$item->quantity}} un</span> <span class="volume"> {{$item->volume}} m<sup>3</sup></span> <span class="weight">{{$item->weight*$item->quantity}} kg</span></p>
                                          </div>
                                        </div>
                                      @endforeach
                                    @else
                                      <div class="row template">
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
                                            <input type="number" id="weight" name="weight[]" min="0" step="0.0001" class="weight form-control size-12px" placeholder="Weight" aria-label="...">
                                          </div><!-- /input-group -->
                                        </div>
                                        <div class="col-md-1">
                                          <input type="hidden" class="quantity_input" id="volume_input" value="" name="total_quantity_pkg[]"/>
                                          <input type="hidden" class="volume_input" id="volume_input" value="" name="volume[]"/>
                                          <input type="hidden" class="weight_input" id="volume_input" value="" name="total_weight_pkg[]"/>
                                        </div>
                                        <div class="col-md-4">
                                          <br>
                                          <p class=""><span class="quantity"></span> <span class="volume"></span> <span class="weight"></span></p>
                                        </div>
                                      </div>
                                    @endif
                                    <div class="row template hide" id="lcl_air_load_template" style="padding-top: 15px;">
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
                                          <input type="number" name="weight[]" min="0" step="0.0001" class="weight form-control size-12px" placeholder="Weight" aria-label="...">
                                        </div><!-- /input-group -->
                                      </div>
                                      <div class="col-md-1">
                                        <a class="remove_lcl_air_load" style="cursor: pointer;"><i class="fa fa-trash"></i></a>
                                        <input type="hidden" class="quantity_input" id="volume_input" value="" name="total_quantity_pkg[]"/>
                                        <input type="hidden" class="volume_input" id="volume_input" value="" name="volume[]"/>
                                        <input type="hidden" class="weight_input" id="volume_input" value="" name="total_weight_pkg[]"/>
                                      </div>
                                      <div class="col-md-4">
                                        <br>
                                        <p class=""><span class="quantity"></span> <span class="volume"></span> <span class="weight"></span></p>
                                      </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                      <div class="col-md-12">
                                        <b>Total: </b>
                                        <span id="total_quantity_pkg">{{$quote->packages->sum('quantity')}} un</span>
                                        <span id="total_volume_pkg">{{$quote->packages->sum('volume')}} m<sup>3</sup></span>
                                        <span id="total_weight_pkg">{{$quote->packages->sum('weight')*$quote->packages->sum('quantity')}} kg</span>
                                      </div>
                                      <br>
                                      <br>
                                      <div class="col-md-12">
                                        <b>Chargeable weight:</b>
                                        <span id="chargeable_weight_pkg">{{$quote->chargeable_weight}} </span>
                                        <input type="hidden" id="chargeable_weight_pkg_input" name="chargeable_weight"/>
                                      </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-md-12">
                                        <br>
                                        <div id="saveActions" class="form-group">
                                          <div class="btn-group">
                                            <button type="button" id="add_load_lcl_air" class="add_load_lcl_air btn btn-info btn-sm">
                                              <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                              <span data-value="save_and_back">Add load</span>
                                            </button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

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
                                    {{ Form::select('modality',['1' => 'Export','2' => 'Import'],null,['class'=>'m-select2-general form-control','id'=>'modality']) }}
                                  </div>
                                  <div class="col-md-2">
                                    <label>Incoterm</label>
                                    {{ Form::select('incoterm',$incoterm,$quote->incoterm,['class'=>'m-select2-general form-control']) }}
                                  </div>
                                  <div class="col-md-5" id="delivery_type_label" {{$quote->delivery_type>4 ? 'style=display:none;':''}}>
                                    <label>Delivery type</label>
                                    {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type',$quote->delivery_type>4 ? 'disabled':'']) }}
                                  </div>
                                  <div class="col-md-5" id="delivery_type_air_label" {{$quote->delivery_type<5 ? 'style=display:none;':''}}>
                                    <label>Delivery type</label>
                                    {{ Form::select('delivery_type',['5' => 'AIRPORT(Origin) To AIRPORT(Destination)','6' => 'AIRPORT(Origin) To DOOR(Destination)','7'=>'DOOR(Origin) To AIRPORT(Destination)','8'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type_air',$quote->delivery_type<5 ? 'disabled':'']) }}
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
                                  @if($quote->type!=3)
                                    <div class="col-md-4" id="origin_harbor_label">
                                      <label>Origin port</label>
                                      {{ Form::select('origin_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'origin_harbor']) }}
                                    </div>
                                  @endif
                                  @if($quote->type==3)
                                    <div class="col-md-4" id="origin_airport_label">
                                      <label>Origin airport</label>
                                      {{ Form::select('origin_airport_id',$airports,null,['class'=>'m-select2-general form-control','id'=>'origin_airport']) }}
                                    </div>
                                  @endif
                                  <div class="col-md-8 {{$quote->origin_address != '' ? '':'hide'}}" id="origin_address_label">
                                    <label>Origin address</label>
                                    {!! Form::text('origin_address', null, ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
                                  </div>
                                </div>
                                <br>
                                <div class="row">
                                  @if($quote->type!=3)
                                    <div class="col-md-4" id="destination_harbor_label">
                                      <label>Destination port</label>
                                      {{ Form::select('destination_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'destination_harbor']) }}
                                    </div>
                                  @endif
                                  @if($quote->type==3)
                                    <div class="col-md-4" id="destination_airport_label">
                                      <label>Destination airport</label>
                                      {{ Form::select('destination_airport_id',$airports,null,['class'=>'m-select2-general form-control','id'=>'destination_airport']) }}
                                    </div>
                                  @endif
                                  <div class="col-md-8 {{$quote->destination_address != '' ? '':'hide'}}" id="destination_address_label">
                                    <label>Destination address</label>
                                    {!! Form::text('destination_address', null, ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-12">
                                    <br>
                                    <label>Validity</label>
                                  </div>
                                  <div class="col-md-4">
                                    <div class="input-group date">
                                      @php
                                        $validity = $quote->since_validity ." / ". $quote->validity;
                                      @endphp
                                      {!! Form::text('validity_date', $validity, ['placeholder' => 'Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
                                      <div class="input-group-append">
                                        <span class="input-group-text">
                                          <i class="la la-calendar-check-o"></i>
                                        </span>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-4" id="carrier_label" {{$quote->type==3 ? 'style=display:none;':''}}>
                                    <br>
                                    <label>Carrier</label>
                                    <div class="form-group">
                                      {{ Form::select('carrier_id',$carriers,$quote->carrier_id,['class'=>'custom-select form-control','id' => 'carrier_id','placeholder'=>'Choose an option']) }}
                                    </div>
                                  </div>
                                  <div class="col-md-4" id="airline_label" {{$quote->type!=3 ? 'style=display:none;':''}}>
                                    <br>
                                    <label>Airline</label>
                                    <div class="form-group">
                                      {{ Form::select('airline_id',$airlines,$quote->airline_id,['class'=>'custom-select form-control','id' => 'airline_id','placeholder'=>'Choose an option']) }}
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
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
                                    {{ Form::select('company_id',$companies,$quote->company_id,['class'=>'select2-company_id form-control','required'=>true]) }}
                                  </div>
                                  <div class="col-md-4 col-sm-4 ol-xs-12">
                                    <label>Client</label>
                                    {{ Form::select('contact_id',$contacts,$quote->contact_id,['class'=>'m-select2-general form-control','required'=>true,'id'=>'contact_id']) }}
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane active" id="m_portlet_tab_1_2">
                        <br>
                        <div class="row">
                          <div class="col-md-12">
                            <div style="min-height: 100px;">
                              <div style="margin-top: 20px;">
                                <div class="pull-left text-left" style="line-height: .5;">
                                  <img src="/{{$user->companyUser->logo}}" class="img img-responsive" style="width: 100px; height: auto; margin-bottom:35px">
                                </div>
                                <div class="pull-right text-right" style="line-height: .5">
                                  <p><b>Quotation ID: <span style="color: #CFAC6C">#{{$quote->company_quote}}</span></b></p>
                                  <p><b>Date of issue:</b> {{date_format($quote->created_at, 'M d, Y H:i')}}</p>
                                  @if($quote->validity!=''&&$quote->since_validity!='')
                                    <p><b>Validity:</b>  {{   \Carbon\Carbon::parse( $quote->since_validity)->format('d M Y') }} -  {{   \Carbon\Carbon::parse( $quote->validity)->format('d M Y') }}</p>
                                    <p><b>Contract Ref: <span style="color: #CFAC6C">#{{$quote->contract_number}}</span></b></p>
                                  @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <br>
                        <div class="row">
                          <div class="col-md-12">
                            <div style="min-height: 150px;">
                              <div class="pull-left text-left" style="line-height: .5">
                                <p><b>From:</b></p>
                                <p>{{$user->name}}</p>
                                <p><b>{{$user->companyUser->name}}</b></p>
                                <p>{{$user->companyUser->address}}</p>
                                <p>{{$user->companyUser->phone}}</p>
                              </div>
                              <div class="pull-right text-right" style="line-height: .5">
                                <p><b>To:</b></p>
                                <p class="name size-12px">{{$quote->contact->first_name.' '.$quote->contact->last_name}}</p>
                                <p><b>{{$quote->company->business_name}}</b></p>
                                <p>{{$quote->company->address}}</p>
                                <p>{{$quote->company->phone}}</p>
                                <p><a href="mailto:{{$quote->company->email}}">{{$quote->company->email}}</a></p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="row">
                              <div class="col-md-12">
                                <div class="row">
                                  <div class="col-md-6">
                                    <div class="panel panel-default">
                                      <div class="panel-heading"><b>Origin</b></div>
                                      <div class="panel-body">
                                        <span id="origin_input">
                                          @if($quote->origin_harbor_id!='')
                                            {{$quote->origin_harbor->name}}
                                          @endif
                                          @if($quote->origin_airport_id!='')
                                            {{$quote->origin_airport->name}}
                                          @endif
                                        </span>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="panel panel-default">
                                      <div class="panel-heading"><b>Destination</b></div>
                                      <div class="panel-body">
                                        <span id="destination_input">
                                          @if($quote->destination_harbor_id!='')
                                            {{$quote->destination_harbor->name}}
                                          @endif
                                          @if($quote->destination_airport_id!='')
                                            {{$quote->destination_airport->name}}
                                          @endif
                                        </span>
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
                                      <div id="cargo_details_total_quantity_p" class="hide"><b>Total quantity:</b> <span id="cargo_details_total_quantity"></span></div>
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
                                      <br>
                                      <div id="chargeable_weight_div" class="hide">  <b>Chargeable weight:</b>
                                        <span id="chargeable_weight_span"></span> kg
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <div class="row">
                              <div class="col-md-12">
                                <h5 class="title-quote size-14px">Origin ammounts</h5>
                                <hr>
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
                                    @if(count($origin_ammounts)>0)
                                      <tbody>
                                      @php
                                        $i=1;
                                      @endphp
                                      @foreach($origin_ammounts as $origin_ammount)
                                        <tr>
                                          <td>
                                            <input type="text" class="form-control" id="origin_ammount_charge" value="{{$origin_ammount->charge}}" name="origin_ammount_charge[]"/>
                                          </td>
                                          <td>
                                            <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="{{$origin_ammount->detail}}" class="form-control" type="text"/>
                                          </td>
                                          <td>
                                            <input id="origin_ammount_units" name="origin_ammount_units[]" value="{{$origin_ammount->units}}" class="form-control origin_ammount_units" type="number" min="0" step="0.0001"/>
                                          </td>
                                          <td>
                                            <div class="input-group">
                                              <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="{{$origin_ammount->price_per_unit}}" min="1" step="0.0001" class="origin_price_per_unit form-control" aria-label="...">
                                              <div class="input-group-btn">
                                                <div class="btn-group">
                                                  {{ Form::select('origin_ammount_currency[]',$currencies,$origin_ammount->currency_id,['class'=>'origin_ammount_currency form-control select2-origin']) }}
                                                </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <input id="origin_total_ammount" name="origin_total_ammount[]" value="{{$origin_ammount->total_ammount}}" class="form-control origin_total_ammount" type="number" min="0" step="0.0001"/>
                                          </td>
                                          <td>
                                            <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="{{$origin_ammount->markup}}" class="form-control origin_ammount_markup" type="number" min="0" step="0.0001" />
                                          </td>
                                          <td>
                                            <div class="form-group">
                                              <div class="input-group">
                                                <input type="text" name="origin_total_ammount_2[]"  value="{{$origin_ammount->total_ammount_2}}" class="origin_total_ammount_2 form-control" aria-label="...">
                                                <a class="btn removeOriginButton">
                                                  <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                </a>
                                              </div>
                                            </div>
                                          </td>
                                        </tr>
                                        @php
                                          $i++;
                                        @endphp
                                      @endforeach
                                      <tr class="hide" id="origin_ammounts">
                                        <td>
                                          <input type="text" class="form-control" id="origin_ammount_charge" value="" name="origin_ammount_charge[]"/>
                                        </td>
                                        <td>
                                          <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="" class="form-control" type="text"/>
                                        </td>
                                        <td>
                                          <input id="origin_ammount_units" name="origin_ammount_units[]" value="" class="form-control origin_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="" min="1" step="0.0001" class="origin_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('origin_ammount_currency[]',$currencies,null,['class'=>'origin_ammount_currency form-control']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="origin_total_ammount" name="origin_total_ammount[]" value="" step="0.0001" class="form-control origin_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" type="number" min="0" step="0.0001" />
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
                                    @else
                                      <tbody>
                                      <tr>
                                        <td>
                                          <input type="text" class="form-control" id="origin_ammount_charge" value="" name="origin_ammount_charge[]"/>
                                        </td>
                                        <td>
                                          <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="" class="form-control" type="text"/>
                                        </td>
                                        <td>
                                          <input id="origin_ammount_units" name="origin_ammount_units[]" value="" class="form-control origin_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="" min="1" step="0.0001" class="origin_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('origin_ammount_currency[]',$currencies,null,['class'=>'form-control origin_ammount_currency select2-origin']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" type="number" min="0" step="0.0001" />
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
                                          <input id="origin_ammount_units" name="origin_ammount_units[]" value="" class="form-control origin_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="" min="1" step="0.0001" class="origin_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('origin_ammount_currency[]',$currencies,null,['class'=>'form-control origin_ammount_currency']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" type="number" min="0" step="0.0001"/>
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
                                    @endif
                                  </table>
                                </div>
                              </div>
                            </div>
                            <div class='row'>
                              <div class="col-md-12">
                                <h5 class="title-quote pull-right">
                                  Sub-Total: <span id="sub_total_origin">{{$quote->sub_total_origin}}</span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                  <input type="hidden" id="total_origin_ammount" name="sub_total_origin" value="{{$quote->sub_total_origin}}"  class="form-control"/>
                                  <a class="btn addButtonOrigin" style="vertical-align: middle">
                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                  </a>
                                </h5>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-12">
                                <h5 class="title-quote size-14px">Freight ammounts</h5>
                                <hr>
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
                                    @if(count($freight_ammounts)>0)
                                      <tbody>
                                      @php
                                        $i=1;
                                      @endphp
                                      @foreach($freight_ammounts as $freight_ammount)
                                        <tr>
                                          <td>
                                            <input type="text" class="form-control" id="freight_ammount_charge" value="{{$freight_ammount->charge}}" name="freight_ammount_charge[]"/>
                                          </td>
                                          <td>
                                            <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="{{$freight_ammount->detail}}" class="form-control" type="text"/>
                                          </td>
                                          <td>
                                            <input id="freight_ammount_units" name="freight_ammount_units[]" value="{{$freight_ammount->units}}" class="form-control freight_ammount_units" type="number" min="0" step="0.0001"/>
                                          </td>
                                          <td>
                                            <div class="input-group">
                                              <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="{{$freight_ammount->price_per_unit}}" min="1" step="0.0001" class="freight_price_per_unit form-control" aria-label="...">
                                              <div class="input-group-btn">
                                                <div class="btn-group">
                                                  {{ Form::select('freight_ammount_currency[]',$currencies,$freight_ammount->currency_id,['class'=>'freight_ammount_currency form-control select2-freight']) }}
                                                </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <input id="freight_total_ammount" name="freight_total_ammount[]" value="{{$freight_ammount->total_ammount}}" class="form-control freight_total_ammount" type="number" min="0" step="0.0001"/>
                                          </td>
                                          <td>
                                            <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="{{$freight_ammount->markup}}" class="form-control freight_ammount_markup" type="number" min="0" step="0.0001" />
                                          </td>
                                          <td>
                                            <div class="form-group">
                                              <div class="input-group">
                                                <input type="text" name="freight_total_ammount_2[]"  value="{{$freight_ammount->total_ammount_2}}" class="freight_total_ammount_2 form-control" aria-label="...">

                                                <a class="btn removeButton">
                                                  <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                </a>
                                              </div>
                                            </div>
                                          </td>
                                        </tr>
                                        @php
                                          $i++;
                                        @endphp
                                      @endforeach
                                      <tr class="hide" id="freight_ammounts">
                                        <td>
                                          <input type="text" class="form-control" id="freight_ammount_charge" value="" name="freight_ammount_charge[]"/>
                                        </td>
                                        <td>
                                          <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="" class="form-control" type="text"/>
                                        </td>
                                        <td>
                                          <input id="freight_ammount_units" name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="" min="1" step="0.0001" class="freight_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('freight_ammount_currency[]',$currencies,null,['class'=>'freight_ammount_currency form-control']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="freight_total_ammount" name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" type="number" min="0" step="0.0001" />
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
                                    @else
                                      <tbody>

                                      <tr>
                                        <td>
                                          <input type="text" class="form-control" id="freight_ammount_charge" value="" name="freight_ammount_charge[]" required />
                                        </td>
                                        <td>
                                          <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="" class="form-control" type="text" required/>
                                        </td>
                                        <td>
                                          <input id="freight_ammount_units" name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0" step="0.0001" required/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="" min="1" step="0.0001" class="freight_price_per_unit form-control" aria-label="..." required>
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('freight_ammount_currency[]',$currencies,null,['class'=>'form-control freight_ammount_currency select2-freight','required'=>true]) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="freight_total_ammount" name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" min="0"   step="0.0001" required/>
                                        </td>
                                        <td>
                                          <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" type="number" min="0" step="0.0001"  required/>
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
                                          <input id="freight_ammount_units" name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="" min="1" step="0.0001" class="freight_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('freight_ammount_currency[]',$currencies,null,['class'=>'form-control freight_ammount_currency']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="freight_total_ammount" name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" type="number" step="0.0001"  min="0"/>
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
                                    @endif
                                  </table>
                                </div>
                              </div>
                            </div>
                            <div class='row'>
                              <div class="col-md-12">
                                <h5 class="title-quote pull-right">
                                  Sub-Total: <span id="sub_total_freight">{{$quote->sub_total_freight}}</span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                  <input type="hidden" id="total_freight_ammount" name="sub_total_freight" value="{{$quote->sub_total_freight}}"  class="form-control"/>
                                  <a class="btn addButton" style="vertical-align: middle">
                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                  </a>
                                </h5>
                              </div>
                            </div>


                            <div class="row">
                              <div class="col-md-12">
                                <h5 class="title-quote size-14px">Destination ammounts</h5>
                                <hr>
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
                                    @if(count($destination_ammounts)>0)
                                      <tbody>
                                      @php
                                        $i=1;
                                      @endphp
                                      @foreach($destination_ammounts as $destination_ammount)
                                        <tr>
                                          <td>
                                            <input type="text" class="form-control" id="destination_ammount_charge" value="{{$destination_ammount->charge}}" name="destination_ammount_charge[]"/>
                                          </td>
                                          <td>
                                            <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="{{$destination_ammount->detail}}" class="form-control" type="text"/>
                                          </td>
                                          <td>
                                            <input id="destination_ammount_units" name="destination_ammount_units[]" value="{{$destination_ammount->units}}" class="form-control destination_ammount_units" type="number" min="0" step="0.0001"/>
                                          </td>
                                          <td>
                                            <div class="input-group">
                                              <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="{{$destination_ammount->price_per_unit}}" min="1" step="0.0001" class="destination_price_per_unit form-control" aria-label="...">
                                              <div class="input-group-btn">
                                                <div class="btn-group">
                                                  {{ Form::select('destination_ammount_currency[]',$currencies,$destination_ammount->currency_id,['class'=>'destination_ammount_currency form-control select2-destination']) }}
                                                </div>
                                              </div>
                                            </div>
                                          </td>
                                          <td>
                                            <input id="destination_total_ammount" name="destination_total_ammount[]" value="{{$destination_ammount->total_ammount}}" class="form-control destination_total_ammount" type="number" min="0" step="0.0001"/>
                                          </td>
                                          <td>
                                            <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="{{$destination_ammount->markup}}" class="form-control destination_ammount_markup" type="number" step="0.0001"  min="0"/>
                                          </td>
                                          <td>
                                            <div class="form-group">
                                              <div class="input-group">
                                                <input type="text" name="destination_total_ammount_2[]"  value="{{$destination_ammount->total_ammount_2}}" class="destination_total_ammount_2 form-control" aria-label="...">
                                                <a class="btn removeButtonDestination">
                                                  <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                </a>
                                              </div>
                                            </div>
                                          </td>
                                        </tr>
                                        @php
                                          $i++;
                                        @endphp
                                      @endforeach
                                      <tr class="hide"  id="destination_ammounts">
                                        <td>
                                          <input type="text" class="form-control" id="destination_ammount_charge" value="" name="destination_ammount_charge[]"/>
                                        </td>
                                        <td>
                                          <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="" class="form-control" type="text"/>
                                        </td>
                                        <td>
                                          <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="" min="1" step="0.0001" class="destination_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('destination_ammount_currency[]',$currencies,null,['class'=>'destination_ammount_currency form-control']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" type="number" step="0.0001"  min="0"/>
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
                                    @else
                                      <tbody>
                                      <tr>
                                        <td>
                                          <input type="text" class="form-control" id="destination_ammount_charge" value="" name="destination_ammount_charge[]"/>
                                        </td>
                                        <td>
                                          <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="" class="form-control" type="text"/>
                                        </td>
                                        <td>
                                          <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="" min="1" step="0.0001" class="destination_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('destination_ammount_currency[]',$currencies,null,['class'=>'form-control destination_ammount_currency select2-destination']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" type="number" step="0.0001"  min="0"/>
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
                                          <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <div class="input-group">
                                            <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="" min="1" step="0.0001" class="destination_price_per_unit form-control" aria-label="...">
                                            <div class="input-group-btn">
                                              <div class="btn-group">
                                                {{ Form::select('destination_ammount_currency[]',$currencies,null,['class'=>'form-control destination_ammount_currency']) }}
                                              </div>
                                            </div>
                                          </div>
                                        </td>
                                        <td>
                                          <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0" step="0.0001"/>
                                        </td>
                                        <td>
                                          <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" type="number" step="0.0001"  min="0"/>
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
                                    @endif
                                  </table>
                                </div>
                              </div>
                            </div>
                            <div class='row'>
                              <div class="col-md-12">
                                <h5 class="title-quote pull-right">
                                  Sub-Total: <span id="sub_total_destination">{{$quote->sub_total_destination}}</span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                  <input type="hidden" id="total_destination_ammount" name="sub_total_destination" value="{{$quote->sub_total_destination}}"  class="form-control"/>
                                  <a class="btn addButtonDestination" style="vertical-align: middle">
                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                  </a>
                                </h5>
                              </div>
                            </div>
                            <hr>
                            <div class="row">
                              <div class="col-lg-12">
                                <label class="size-14px">
                                  <b>Terms & Conditions</b>
                                </label>
                              </div>
                              <div class="col-lg-12">
                                <br>
                                <div style="margin-bottom:40px;">
                                  {!! Form::textarea('term', $quote->term, ['placeholder' => 'Please enter your export text','class' => 'form-control editor m-input editor','id'=>'terms_and_conditions_edit']) !!}
                                </div>
                              </div>
                            </div>
                            <hr>
                            <div class="row">
                              <div class="col-lg-12">
                                <label class="size-14px">
                                  <b>Payment conditions</b>
                                </label>
                              </div>
                              <div class="col-lg-12">
                                <br>
                                <div class="" style="margin-bottom:40px;">
                                  {!! Form::textarea('payment_conditions', $quote->payment_conditions, ['placeholder' => 'Please enter your payment conditions text','class' => 'form-control editor m-input','id'=>'payment_conditions']) !!}
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
                      <div class="col-lg-6">
                        <button type="submit" class="btn btn-primary">
                          Update Quote
                        </button>
                      </div>
                      <div class="col-lg-6">
                        <a href="javascript:history.back()" class="btn btn-danger">
                          Cancel
                        </a>
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
      <div class="col-md-2">
        <h3 class="title-quote size-16px">Settings</h3>
        <hr>
        <p class="title-quote size-14px" data-toggle="collapse" data-target="#main_currency" style="cursor: pointer">Main currency <i class="fa fa-angle-down pull-right"></i></p>

        <p class="settings size-12px" id="main_currency" class="collapse" style="font-weight: lighter">  @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif </p>
        <hr>
        <p class="title-quote title-quote size-14px" data-toggle="collapse" data-target="#exchange_rate" style="cursor: pointer">Exchange rate <i class="fa fa-angle-down pull-right"></i></p>
        <p class="settings size-12px" id="exchange_rate" style="font-weight: 100">@if($currency_cfg->alphacode=='EUR') 1 EUR = {{$exchange->rates}} USD @else 1 USD = {{$exchange->rates_eur}} EUR @endif</p>
      </div>
    </div>
  </div>

  @include('quotes.partials.sendQuoteModal');

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
  <script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
  <script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>
  <script>

    $('.select2-origin').select2({
      placeholder: "Currency"
    });
    $('.select2-freight').select2({
      placeholder: "Currency"
    });
    $('.select2-destination').select2({
      placeholder: "Currency"
    });

    $( document ).ready(function() {
      $('#contact_id').prop("disabled", false);
    });

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