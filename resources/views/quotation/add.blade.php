@extends('layouts.app')
@section('title', 'Add Quote')
@section('content')
@php 

$subtotalOrigin = 0;
$subtotalFreight = 0;
$subtotalDestiny = 0;
@endphp

<div class="m-content">
  <div class="row">
    <div class="col-md-2">
      <button class="btn btn-primary btn-block">Schedules</button>
    </div>
    <div class="col-md-10" >
      @if(isset($company_user->companyUser->currency_id))
      <div class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
        <input type="text" id="currency_id" class="form-control " value="{{$currency_cfg->alphacode}}" disabled="true"/>
      </div>
      @endif
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
    <div class="col-md-12">
      @if(count($company_user->company_user_id)>0)
      <div class="row">
        <div class="col-md-12">
          {!! Form::open(['route' => 'quotes.store','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}

          <div class="m-portlet__body">
            <div class="row">
              <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__body">
                  <div class="tab-content">
                    <div class="tab-pane active" id="m_portlet_tab_1_2">
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
                                      {{ $info->port_origin->name     }}
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="panel panel-default">
                                    <div class="panel-heading"><b>Destination</b></div>
                                    <div class="panel-body">
                                      <span id="destination_input"></span>
                                      {{ $info->port_destiny->name     }}
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row" style="padding-top: 20px; padding-bottom: 20px;">
                                <div class="col-md-12">
                                  <h5>Cargo details</h5>
                                  <hr>
                                  <p id="cargo_details_20_p" ><span id="cargo_details_20"></span> {{ $form->twuenty }} x 20' Containers</p>
                                  <p id="cargo_details_40_p" ><span id="cargo_details_40"></span> {{ $form->forty }} x 40' Containers</p>
                                  <p id="cargo_details_40_hc_p" ><span id="cargo_details_40_hc"></span> {{ $form->fortyhc }} x 40' HC Containers</p>
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
                                <div class="col-md-1">Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</div>
                              </div>
                              <hr>

                              @foreach($info->localOrig as $origin)
                              @php 
                              $total = explode(" ",$origin->origin->totalAmmount);
                              $subtotalOrigin = explode(" ",$info->totalOrigin);
                              @endphp                                       
                              <div class="row">   
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="{{ $origin->origin->surcharge_name }} " />
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="{{ $origin->origin->calculation_name }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0" value="{{ $origin->origin->cantidad }}"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="{{ $origin->origin->monto }}">
                                      <div class="input-group-btn">
                                        <div class="btn-group">

                                          <select class="btn btn-default origin_ammount_currency" name="origin_ammount_currency[]">
                                            <option value="">Currency</option>

                                            @foreach($currencies as $currency)
                                            @if($currency->alphacode == $origin->origin->currency)
                                            @php
                                            $seleccionado = 'selected = true';
                                            @endphp
                                            @else
                                            @php
                                            $seleccionado = '';
                                            @endphp
                                            @endif
                                            <option {{ $seleccionado }}  value="{{$currency->id}}">{{$currency->alphacode}} </option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" type="number" min="0" value="{{ $origin->origin->markup }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="{{ $total[0] }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="{{ $subtotalOrigin[0]  }}">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endforeach
                              @foreach($info->globalOrig as $origin)
                              @php 
                              $total = explode(" ",$origin->origin->totalAmmount);
                              $subtotalOrigin = explode(" ",$info->totalOrigin);
                              @endphp                                       
                              <div class="row">   
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="{{ $origin->origin->surcharge_name }} " />
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="{{ $origin->origin->calculation_name }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0" value="{{ $origin->origin->cantidad }}"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="{{ $origin->origin->monto }}">
                                      <div class="input-group-btn">
                                        <div class="btn-group">

                                          <select class="btn btn-default origin_ammount_currency" name="origin_ammount_currency[]">
                                            <option value="">Currency</option>

                                            @foreach($currencies as $currency)
                                            @if($currency->alphacode == $origin->origin->currency)
                                            @php
                                            $seleccionado = 'selected = true';
                                            @endphp
                                            @else
                                            @php
                                            $seleccionado = '';
                                            @endphp
                                            @endif
                                            <option {{ $seleccionado }}  value="{{$currency->id}}">{{$currency->alphacode}} </option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" type="number" min="0" value="{{ $origin->origin->markup }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="{{ $total[0] }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="{{ $subtotalOrigin[0]  }}">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endforeach

                              @if((empty($info->localOrig)) && (empty($info->globalOrig)))
                              <div class="row">
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]"  />
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
                                            @foreach($currencies as $currency)
                                            <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
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
                                        <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="...">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endif
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
                                            @foreach($currencies as $currency)
                                            <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
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
                                        <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="...">
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
                                      <h5 class="size-12px">
                                        Sub-Total:<span id="sub_total_origin">{{ $info->totalOrigin }}</span>
                                        <input type="hidden" id="total_origin_ammount" name="sub_total_origin" class="form-control" value="{{ $subtotalOrigin[0] }}"/>
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
                                <div class="col-md-1">Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</div>
                              </div>

                              <hr>
                              @foreach($info->localFreight as $freight)
                              @php 
                              $total = explode(" ",$freight->freight->totalAmmount);
                              $subtotalFreight = explode(" ",$info->totalFreight);
                              @endphp    
                              <div class="row">
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="{{ $freight->freight->surcharge_name }}" />
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="{{ $freight->freight->calculation_name }}" />
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_units" name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" max="99" type="number" value="{{ $freight->freight->cantidad }}"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="{{ $freight->freight->monto }}">
                                      <div class="input-group-btn">
                                        <div class="btn-group">
                                          <select class="btn btn-default freight_ammount_currency" name="freight_ammount_currency[]">
                                            <option value="">Currency</option>
                                            @foreach($currencies as $currency)

                                            @if($currency->alphacode == $freight->freight->currency)
                                            @php
                                            $seleccionado = 'selected = true';
                                            @endphp
                                            @else
                                            @php
                                            $seleccionado = '';
                                            @endphp
                                            @endif

                                            <option {{ $seleccionado }} value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" min="0" type="number" value="{{ $freight->freight->markup }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="{{ $total[0] }}">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2" min="0" type="number" value="{{ $subtotalFreight[0] }}"/>
                                  </div>
                                </div>
                              </div>
                              @endforeach
                              @foreach($info->globalFreight as $freight)
                              @php 
                              $total = explode(" ",$freight->freight->totalAmmount);
                              $subtotalFreight = explode(" ",$info->totalFreight);
                              @endphp    
                              <div class="row">
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="{{ $freight->freight->surcharge_name }}" />
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="{{ $freight->freight->calculation_name }}" />
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_units" name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" max="99" type="number" value="{{ $freight->freight->cantidad }}"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="{{ $freight->freight->monto }}">
                                      <div class="input-group-btn">
                                        <div class="btn-group">
                                          <select class="btn btn-default freight_ammount_currency" name="freight_ammount_currency[]">
                                            <option value="">Currency</option>
                                            @foreach($currencies as $currency)
                                            @if($currency->alphacode == $freight->freight->currency)
                                            @php
                                            $seleccionado = 'selected = true';
                                            @endphp
                                            @else
                                            @php
                                            $seleccionado = '';
                                            @endphp
                                            @endif
                                            <option {{ $seleccionado  }} value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" min="0" type="number" value="{{ $freight->freight->markup }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="{{ $total[0] }}">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2" min="0" type="number" value="{{ $subtotalFreight[0] }}"/>
                                  </div>
                                </div>
                              </div>
                              @endforeach

                              @if((empty($info->localFreight)) &&  (empty($info->globalFreight)))
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
                                            @foreach($currencies as $currency)
                                            <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
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
                                        <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="...">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2" min="0" type="number"/>
                                  </div>
                                </div>
                              </div>
                              @endif
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
                                            @foreach($currencies as $currency)
                                            <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
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
                                    <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2" min="0" type="number"/>
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
                                        Sub-Total:<span id="sub_total_freight">{{$subtotalFreight[0]}}</span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                        <input type="hidden" id="total_freight_ammount" name="sub_total_freight"  class="form-control" value = '{{$subtotalFreight[0]}}'/>
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
                                <div class="col-md-1">Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</div>
                              </div>
                              <hr>
                              @foreach($info->localDest as $destiny)
                              @php 
                              $subtotalDestiny = explode(" ",$info->totalDestiny);
                              $total = explode(" ",$destiny->destiny->totalAmmount);
                              @endphp 
                              <div class="row">
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input type="text" class="form-control" name="destination_ammount_charge[]"  value="{{ $destiny->destiny->surcharge_name }}" />
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_ammount_detail[]" class="form-control" type="text" value="{{ $destiny->destiny->calculation_name }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0" value="{{ $destiny->destiny->cantidad }}"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" name="destination_price_per_unit[]" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="{{ $destiny->destiny->monto }}">
                                      <div class="input-group-btn">
                                        <div class="btn-group">
                                          <select class="btn btn-default destination_ammount_currency" name="destination_ammount_currency[]">
                                            <option value="">Currency</option>
                                            @foreach($currencies as $currency)
                                            @if($currency->alphacode == $destiny->destiny->currency)
                                            @php
                                            $seleccionado = 'selected = true';
                                            @endphp
                                            @else
                                            @php
                                            $seleccionado = '';
                                            @endphp
                                            @endif
                                            <option {{  $seleccionado  }} value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" type="number" step="0.01" min="0" value="{{ $destiny->destiny->markup }}" />
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="{{$total[0] }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="{{ $subtotalDestiny[0] }}">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endforeach
                              @foreach($info->globalDest as $destiny)
                              @php 
                              $subtotalDestiny = explode(" ",$info->totalDestiny);
                              $total = explode(" ",$destiny->destiny->totalAmmount);
                              @endphp 
                              <div class="row">
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input type="text" class="form-control" name="destination_ammount_charge[]"  value="{{ $destiny->destiny->surcharge_name }}" />
                                  </div>
                                </div>
                                <div class="col-md-2">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_ammount_detail[]" class="form-control" type="text" value="{{ $destiny->destiny->calculation_name }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0" value="{{ $destiny->destiny->cantidad }}"/>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="input-group">
                                      <input type="number" name="destination_price_per_unit[]" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="{{ $destiny->destiny->monto }}">
                                      <div class="input-group-btn">
                                        <div class="btn-group">
                                          <select class="btn btn-default destination_ammount_currency" name="destination_ammount_currency[]">
                                            <option value="">Currency</option>
                                            @foreach($currencies as $currency)
                                            @if($currency->alphacode == $destiny->destiny->currency)
                                            @php
                                            $seleccionado = 'selected = true';
                                            @endphp
                                            @else
                                            @php
                                            $seleccionado = '';
                                            @endphp
                                            @endif
                                            <option {{  $seleccionado  }} value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" type="number" min="0" value="{{ $destiny->destiny->markup }}" />
                                  </div>
                                </div>
                                <div class="col-md-1">
                                  <div class="m-bootstrap-touchspin-brand">
                                    <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="{{$total[0] }}"/>
                                  </div>
                                </div>
                                <div class="col-md-1" >
                                  <div class="m-bootstrap-touchspin-brand">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="{{ $subtotalDestiny[0] }}">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endforeach


                              @if(empty($info->localDest) && (empty($info->globalDest)))
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
                                            @foreach($currencies as $currency)
                                            <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
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
                                        <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="...">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              @endif
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
                                            @foreach($currencies as $currency)
                                            <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                            @endforeach
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
                                        <input type="text" name="destination_total_ammount_2[]"  class="form-control  destination_total_ammount_2" aria-label="...">
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
                                        Sub-Total:<span id="sub_total_destination">{{ $info->totalDestiny }} </span>&nbsp;
                                        <input type="hidden" id="total_destination_ammount" name="sub_total_destination" class="form-control"  value="{{ $subtotalDestiny[0]  }}"/>
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
                                    <h3><b>Total:
                                      <span id="total">     {{$info->totalQuote}} </span>  </b>  </h3>  

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
          <input type="hidden" class="form-control" id="incoterm" name="incoterm" value="{{ $form->incoterm }} ">
          <input type="hidden" class="form-control" id="modality" name="modality" value="{{ $form->modality }} ">
          <input type="hidden" class="form-control" id="validity" name="validity" value="{{ $form->date }}">
          <input type="hidden" class="form-control" id="origin_address" name="origin_address" value="{{ $form->origin_address }} ">
          <input type="hidden" class="form-control" id="destination_address" name="destination_address" value="{{ $form->destination_address }} ">
          <input type="hidden" class="form-control" id="company_id" name="company_id" value="{{ $form->company_id }} ">
          <input type="hidden" class="form-control" id="origin_harbor_id" name="origin_harbor_id" value="{{ $info->origin_port }} ">
          <input type="hidden" class="form-control" id="destination_harbor_id" name="destination_harbor_id" value="{{ $info->destiny_port }} ">
          <input type="hidden" class="form-control" id="price_id" name="price_id" value="{{ $form->price_id }} ">
          <input type="hidden" class="form-control" id="contact_id" name="contact_id" value="{{ $form->contact_id }} ">
          <input type="hidden" class="form-control" id="qty_20" name="qty_20" value="{{ $form->twuenty }} ">
          <input type="hidden" class="form-control" id="qty_40" name="qty_40" value="{{ $form->forty }} ">
          <input type="hidden" class="form-control" id="qty_40_hc" name="qty_40_hc" value="{{ $form->fortyhc }} ">
          <input type="hidden" class="form-control" id="pick_up_date" name="pick_up_date" value="{{ $form->date }} ">
          <input type="hidden" class="form-control" id="status_id" name="status_id" value="1">
          <input type="hidden" class="form-control" id="delivery_type" name="delivery_type" value="{{ $form->delivery_type }} ">
          <input type="hidden" class="form-control" id="type" name="type" value="{{ $form->type }} ">
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

@stop
