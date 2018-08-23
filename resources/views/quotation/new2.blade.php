@extends('layouts.app')
@section('title', 'Quotes Automatic')
@section('content')

<div class="m-content container">
  <div class="m-portlet--mobile">
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
    <div class="m-portlet__body">
      <div class="row">
        <div class="col-xl-12">
          {!! Form::open(['route' => 'quotes.listRate','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
          <div class="m-portlet__body">
            <div class="row">
              <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                  <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">

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
                                <b>MODE:</b>
                              </label>
                            </div>
                            <div class="col-lg-6">
                              <div class='row'>
                                <div class="col-md-4">
                                  <label class="m-option">
                                    <span class="m-option__control">
                                      <span class="m-radio m-radio--brand m-radio--check-bold">
                                        <input name="type"   checked='true' value="1" required='true' type="radio">
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
                                
                              </div>
                              <br>
                              <div class='row'>
                                <div class="col-md-4">
                                  <label>
                                    20' :
                                  </label>
                                  <div class="m-bootstrap-touchspin-brand">
                                    {!! Form::text('twuenty', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <label>
                                    40' :
                                  </label>
                                  <div class="m-bootstrap-touchspin-brand">
                                    {!! Form::text('forty', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <label>
                                    40' HC :
                                  </label>
                                  <div class="m-bootstrap-touchspin-brand">
                                    {!! Form::text('fortyhc', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}

                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <hr>
                          <div class="form-group m-form__group row">
                            <div class="col-lg-2">
                              <label>
                                <b>SERVICES:</b>
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
                                    {!! Form::text('date', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select date','class' => 'form-control m-input' ,'required' => 'true','autocomplete'=>'off']) !!}
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
                                  {{ Form::select('originport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor','required' => 'true']) }}
                                </div>
                                <div class="col-md-8" id="origin_address_label" style="display: none;">
                                  <label>Origin address</label>
                                  {!! Form::text('origin_address', '', ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
                                </div>
                              </div>
                              <br>
                              <div class="row">
                                <div class="col-md-4" id="destination_harbor_label">
                                  <label>Destination port</label>
                                  {{ Form::select('destinyport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor','required' => 'true']) }}
                                </div>
                                <div class="col-md-8" id="destination_address_label" style="display: none;">
                                  <label>Destination address</label>
                                  {!! Form::text('destination_address', '', ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
                                </div>
                              </div>
                            </div>
                          </div>
                          <hr>
                          <div class="form-group m-form__group row">
                            <div class="col-lg-2">
                              <label>
                                <b>CLIENT:</b>
                              </label>
                            </div>
                            <div class="col-lg-10">
                              <br>
                              <div class="col-lg-10">
                                <br>
                                <div class="row">
                                  <div class="col-md-4 col-sm-4 col-xs-12">
                                    <label>Company</label>
                                    {{ Form::select('company_id', $companies,null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal','required'=>'true']) }}<br><br>
                                    <a  class="btn btn-primary btn-sm m-btn m-btn--icon" onclick="AbrirModal('add',0)">
                                      <span style="color: white;">
                                        <i class="la la-plus"></i>
                                        <span>Add Company</span>
                                      </span>
                                    </a>
                                  </div>
                                  <div class="col-md-4 col-sm-4 ol-xs-12">
                                    <label>Contact</label>
                                    {{ Form::select('contact_id',[],null,['class'=>'m-select2-general form-control','required'=>'true']) }}<br><br>
                                    <a  class="btn btn-sm btn-primary m-btn m-btn--icon" onclick="AbrirModal('addContact',0)">
                                      <span style="color: white;">
                                        <i class="la la-plus"></i>
                                        <span>Add Contact</span>
                                      </span>
                                    </a>
                                  </div>
                                  <div class="col-md-4 col-sm-4 col-xs-12">
                                    <label>Price level</label>
                                    {{ Form::select('price_id',[],null,['class'=>'m-select2-general form-control']) }}
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
                    <!--<div class="tab-pane" id="m_portlet_tab_1_2">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="form-group m-form__group row">
                            <div class="col-md-12">
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="m-portlet m-portlet--responsive-tablet-and-mobile">
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
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="m-portlet m-portlet--responsive-tablet-and-mobile">
                                    <div class="m-portlet__head">
                                      <div class="m-portlet__head-caption">
                                        <div class="m-portlet__head-title">
                                          <h5 class="m-portlet__head-text">
                                            Destination
                                          </h5>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="m-portlet__body">
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
                                  <h5>Origin Amounts</h5>
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
                                    <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
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
                                    <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control" type="number" min="0"/>
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
                                    <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
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
                                    <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control" type="number" min="0"/>
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
                              <div class='row'>
                                <div class="col-md-9"></div>
                                <div class="col-md-2">
                                  <div class="form-group">
                                    <span>
                                      <h5>
                                        Sub-Total:<span id="sub_total_origin">0.00</span>&nbsp;
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
                                  <h5>Freight Amounts</h5>
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
                                    <input type="text" class="form-control" id="freight_ammount_description" name="freight_ammount_description[]"/>
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_units" name="freight_ammount_units[]" class="form-control" min="0" max="99" type="number"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control" aria-label="...">
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
                                    <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control" min="0" type="number"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="freight_total_ammount[]"  class="form-control" aria-label="...">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control" min="0" type="number"/>
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
                                    <input id="freight_ammount_units" name="freight_ammount_units[]" class="form-control" min="0" max="99" type="number"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control" aria-label="...">
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
                                    <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control" min="0" type="number"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="freight_total_ammount[]"  class="form-control" aria-label="...">
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
                                        Sub-Total:<span id="sub_total_origin">0.00</span>&nbsp;
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
                                  <h5>Destination Amounts</h5>
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
                                    <input id="destination_ammount_units" name="destination_ammount_units[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="destination_ammount" name="destination_price_per_unit[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
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
                                    <input id="destination_ammount_markup" name="destination_ammount_markup[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="destination_total_ammount" name="destination_total_ammount[]" class="form-control" type="number" min="0"/>
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
                                    <input id="destination_ammount_units" name="destination_ammount_units[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="destination_ammount" name="destination_price_per_unit[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
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
                                    <input id="destination_ammount_markup" name="destination_ammount_markup[]" class="form-control" type="number" min="0"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="destination_total_ammount" name="destination_total_ammount[]" class="form-control" type="number" min="0"/>
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
                                        Sub-Total:<span id="sub_total_origin">0.00</span>&nbsp;
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
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>-->
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
</div>
@include('contacts.partials.contactsModal');
@include('companies.partials.companiesModal');

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
<script>
  function AbrirModal(action,id){

    if(action == "add"){
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
