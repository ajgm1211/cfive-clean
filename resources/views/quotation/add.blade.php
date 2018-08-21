@extends('layouts.app')
@section('title', 'Add Quote')
@section('content')
@php 

$subtotalOrigin = 0;

$subtotalDestiny = 0;
@endphp

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


  <div class="tab-pane" id="m_portlet_tab_1_2">
    {!! Form::open(['route' => 'quotes.store','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
    <br>
    <div class="row">


      <div class="col-md-2 col-xs-4" >
        @if($email_templates)
        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#SendQuoteModal">
          Save and send
        </button>
        @endif

      </div>
      <div class="col-md-2 col-xs-4">
        <a href="#"  class="btn btn-primary btn-block">PDF</a>
      </div>
      <div class="col-md-2 col-xs-4" >
        <button id="duplicate-quote" type="submit" class="btn btn-primary btn-block">Save</button>

      </div>

    </div>
    <hr><br><br>
    <div class="row">
      <div class="col-lg-10">
        <div class="row">
          <div class="col-md-12">
            <div class="m-portlet__body">
              <div class="m-portlet__head" style="min-height: 100px;">
                <div class="m-portlet__head-tools">
                  <div class="col-md-12" style="margin-top: 20px;">
                    <div class="pull-left text-left" style="line-height: .5;">
                      <img src="/uploads/logos/{{$user->companyUser->logo}}" class="img img-responsive" width="300px" ><br>
                    </div>
                    <div class="pull-right text-right" style="line-height: .5">                                

                      <p><b>Date of issue:</b> {{ $form->date }} </p>
                      <p><b>Validity: </b>  {{ $info->contract->expire }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="m-portlet__body">
              <div class="m-portlet__head" style="min-height: 150px;">
                <div class="m-portlet__head-tools">
                  <div class="col-md-12">
                    <div class="pull-left text-left" style="line-height: .5">
                      <br><br>
                      <p><b>From:</b></p>
                      <p>{{$user->name}}</p>
                      <p><b>{{$user->companyUser->name}}</b></p>
                      <p>{{$user->companyUser->address}}</p>
                      <p>{{$user->companyUser->phone}}</p>
                    </div>
                    <div class="pull-right text-right" style="line-height: .5">
                      <p><b>To:</b></p>
                      <p class="name size-12px">{{$contactInfo->first_name.' '.$contactInfo->last_name}}</p>
                      <p><b>{{$companyInfo->business_name}}</b></p>
                      <p>{{$companyInfo->address}}</p>
                      <p>{{$companyInfo->phone}}</p>
                      <p><a href="mailto:{{$companyInfo->email}}">{{$companyInfo->email}}</a></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="">
              <div class="row">
                <div class="container">
                  <br>
                  <br>
                  <div class="m-portlet__body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="panel panel-default">
                          <div class="panel-heading title-quote size-14px"><b>Origin</b></div>
                          <div class="panel-body">

                            <b>Port: </b><span id="origin_input">  {{ $info->port_origin->name }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="panel panel-default">
                          <div class="panel-heading title-quote size-14px"><b>Destination</b></div>
                          <div class="panel-body">
                            <b>Port: </b><span id="destination_input">{{ $info->port_destiny->name }}</span>
                          </div>
                        </div>
                      </div>
                      @if($form->origin_address != "")
                      <div class="col-md-6">
                        <div class="panel panel-default" id="destination_address_panel">
                          <div class="panel-heading title-quote size-14px"><b>Origin Address</b></div>
                          <div class="panel-body">
                            <span id="destinationA_input">
                              {{$form->origin_address}}
                            </span>
                          </div>
                        </div>
                      </div>
                      @endif                                            
                      @if($form->destination_address != "")
                      <div class="col-md-6">
                        <div class="panel panel-default" id="destination_address_panel">
                          <div class="panel-heading title-quote size-14px"><b>Destination Address</b></div>
                          <div class="panel-body">
                            <span id="destinationA_input">
                              {{$form->destination_address}}
                            </span>
                          </div>
                        </div>
                      </div>
                      @endif               
                    </div>
                    <div class="row" style="padding-top: 20px; padding-bottom: 20px;">
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-12">
                            <h5 class="title-quote size-14px">Cargo details</h5>
                            <hr>
                          </div>
                        </div>
                        @if($form->twuenty > 0)
                        <p id="cargo_details_20_p" ><span id="cargo_details_20"></span> {{ $form->twuenty }} x 20' Containers</p>
                        @endif
                        @if($form->forty > 0)
                        <p id="cargo_details_40_p" ><span id="cargo_details_40"></span> {{ $form->forty }} x 40' Containers</p>
                        @endif
                        @if($form->fortyhc > 0)
                        <p id="cargo_details_40_hc_p" ><span id="cargo_details_40_hc"></span> {{ $form->fortyhc }} x 40' HC Containers</p>
                        @endif
                        <p id="totRat" ><span id="totRat"></span><b>Total Rates {{ $info->totalrates }} </b> </p>

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
                                <td >Markup</td>
                                <td >Total</td>
                                <td >Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($info->localOrig as $origin)
                              @php 
                              $total = explode(" ",$origin->origin->totalAmmount);
                              @endphp       
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="{{ $origin->origin->surcharge_terms }} " />
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="{{ $origin->origin->calculation_name }}"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0" value="{{ $origin->origin->cantidadT }}"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="{{ $origin->origin->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('origin_ammount_currency[]',$currency,$origin->origin->idCurrency,['class'=>'form-control origin_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" type="number" step=".01" min="0" value="{{ $origin->origin->markup }}"/>
                                </td>
                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="{{ $origin->origin->subtotal_local }}"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="{{ $total[0]  }}">
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              @foreach($info->globalOrig as $origin)
                              @php 
                              $total = explode(" ",$origin->origin->totalAmmount);
                              @endphp           
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="{{ $origin->origin->surcharge_terms }} " />
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="{{ $origin->origin->calculation_name }}"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0" value="{{ $origin->origin->cantidadT }}"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="{{ $origin->origin->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('origin_ammount_currency[]',$currency,$origin->origin->idCurrency,['class'=>'form-control origin_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" type="number" step=".01" min="0" value="{{ $origin->origin->markup }}"/>
                                </td>
                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="{{ $origin->origin->subtotal_global }}"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="{{ $total[0]  }}">
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              @foreach($info->inlandOrigin as $origin)
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="{{ $origin->provider }} " />
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="{{ $origin->type }}"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0" value="1" readonly/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="{{ $origin->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('origin_ammount_currency[]',$currency,$info->idCurrency,['class'=>'form-control origin_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" step="0.01"  type="number" min="0" value="{{ $origin->markup }}"/>
                                </td>
                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="{{ $origin->monto  }}"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="{{ $origin->monto }}">
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              @if((empty($info->localOrig)) && (empty($info->globalOrig)))
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
                                        {{ Form::select('origin_ammount_currency[]',$currency,null,['class'=>'form-control origin_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" min="0"/>
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
                              @endif
                              <tr class="hide"  id="origin_ammounts">
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
                                        {{ Form::select('origin_ammount_currency[]',$currency,null,['class'=>'form-control origin_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" min="0"/>
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
                          Sub-Total:<span id="sub_total_origin">{{ $info->totalChargeOrig }}</span>@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif 
                          <input type="hidden" id="total_origin_ammount" name="sub_total_origin" class="form-control" value="{{ $info->totalChargeOrig }}"/>
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
                                <td >Markup</td>
                                <td >Total</td>
                                <td >Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                              </tr>
                            </thead>
                            <tbody>
                              @php 
                              $totalF = explode(" ",$info->totalFreight);
                              @endphp
                              @foreach($info->rates as $freight)
                              @php 
                              $total = explode(" ",$freight->total);
                              @endphp
                              <tr>
                                <td>


                                  <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="{{ $freight->type }}"  readonly='true'/>
                                </td>
                                <td>
                                  <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="{{ $freight->detail }}"  readonly='true' />
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" max="99" type="number" value="{{ $freight->cantidad }}" data-container="body" data-toggle="m-popover" data-placement="bottom" data-content="Im sorry " data-original-title=""  readonly='true'/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="{{ $freight->price }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('freight_ammount_currency[]',$currency,$freight->idCurrency,['class'=>'form-control freight_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" step="0.01" min="0" type="number" value="{{ $freight->markup }}"/>
                                </td>
                                <td>
                                  <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="{{ $freight->subtotal }}">
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2"  step="0.01"  min="0" type="number" value="{{ $total[0] }}"/>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              @foreach($info->localFreight as $freight)
                              @php 
                              $total = explode(" ",$freight->freight->totalAmmount);
                              @endphp    
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="{{ $freight->freight->surcharge_terms }}" />
                                </td>
                                <td>
                                  <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="{{ $freight->freight->calculation_name }}" />
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" max="99" type="number" value="{{ $freight->freight->cantidadT }}"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="{{ $freight->freight->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('freight_ammount_currency[]',$currency,$freight->freight->idCurrency,['class'=>'form-control freight_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" min="0" step=".01" type="number" value="{{ $freight->freight->markup }}"/>
                                </td>
                                <td>
                                  <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="{{ $freight->freight->subtotal_local  }}">
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2"  step="0.01"  min="0" type="number" value="{{ $total[0] }}"/>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              @foreach($info->globalFreight as $freight)
                              @php 
                              $total = explode(" ",$freight->freight->totalAmmount);
                              @endphp  
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="{{ $freight->freight->surcharge_terms }}" />
                                </td>
                                <td>
                                  <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="{{ $freight->freight->calculation_name }}" />
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" max="99" type="number" value="{{ $freight->freight->cantidadT }}"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="{{ $freight->freight->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('freight_ammount_currency[]',$currency,$freight->freight->idCurrency,['class'=>'form-control freight_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" step=".01" min="0" type="number" value="{{ $freight->freight->markup }}"/>
                                </td>
                                <td>
                                  <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="{{ $freight->freight->subtotal_global  }}">
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2"  step="0.01"  min="0" type="number" value="{{ $total[0] }}"/>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              <tr class="hide"  id="freight_ammounts">
                                <td>
                                  <input type="text" class="form-control" id="freight_ammount_charge" value="" name="freight_ammount_charge[]"/>
                                </td>
                                <td>
                                  <input name="freight_ammount_detail[]" value="" class="form-control" type="text"/>
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number"  name="freight_price_per_unit[]" value="" min="1" step="0.01" class="freight_price_per_unit form-control" aria-label="...">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('freight_ammount_currency[]',$currency,null,['class'=>'form-control freight_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input  name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <input  name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" min="0"/>
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
                          Sub-Total:<span id="sub_total_freight">{{ $totalF[0]   }}</span>&nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                          <input type="hidden" id="total_freight_ammount" name="sub_total_freight"  class="form-control" value = '{{  $totalF[0]  }}'/>
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
                                <td >Markup</td>
                                <td >Total</td>
                                <td >Total @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</td>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($info->localDest as $destiny)
                              @php 
                              $total = explode(" ",$destiny->destiny->totalAmmount);
                              @endphp 
                              <tr>
                                <td>
                                  <input type="text" class="form-control"  id="destination_ammount_charge"  name="destination_ammount_charge[]"  value="{{ $destiny->destiny->surcharge_terms }}" />
                                </td>
                                <td>
                                  <input name="destination_ammount_detail[]" id="destination_ammount_detail"  class="form-control" type="text" value="{{ $destiny->destiny->calculation_name }}"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0" value="{{ $destiny->destiny->cantidadT }}"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" name="destination_price_per_unit[]" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="{{ $destiny->destiny->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('destination_ammount_currency[]',$currency,$destiny->destiny->idCurrency,['class'=>'form-control destination_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" type="number" step="0.01" min="0" value="{{ $destiny->destiny->markup }}" />
                                </td>
                                <td>
                                  <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="{{ $destiny->destiny->subtotal_local }}"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="{{ $total[0] }}">
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              @foreach($info->globalDest as $destiny)
                              @php 
                              $total = explode(" ",$destiny->destiny->totalAmmount);
                              @endphp 
                              <tr>
                                <td>
                                  <input type="text" class="form-control"  id="destination_ammount_charge"  name="destination_ammount_charge[]"  value="{{ $destiny->destiny->surcharge_terms }}" />
                                </td>
                                <td>
                                  <input name="destination_ammount_detail[]" id="destination_ammount_detail"  class="form-control" type="text" value="{{ $destiny->destiny->calculation_name }}"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0" value="{{ $destiny->destiny->cantidadT }}"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" name="destination_price_per_unit[]" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="{{ $destiny->destiny->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('destination_ammount_currency[]',$currency,$destiny->destiny->idCurrency,['class'=>'form-control destination_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" type="number" step="0.01" min="0" value="{{ $destiny->destiny->markup }}" />
                                </td>
                                <td>
                                  <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="{{ $destiny->destiny->subtotal_global }}"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="{{ $total[0] }}">
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach

                              @foreach($info->inlandDestiny as $destiny)
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="destination_ammount_charge[]"  value="{{ $destiny->provider }} " />
                                </td>
                                <td>
                                  <input name="destination_ammount_detail[]" class="form-control" type="text" value="{{ $destiny->type }}"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0" value="1" readonly='true'/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" name="destination_price_per_unit[]" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="{{ $destiny->monto }}">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        {{ Form::select('destination_ammount_currency[]',$currency,$info->idCurrency,['class'=>'form-control destination_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" step=".01" type="number" min="0" value="{{ $destiny->markup }}" />
                                </td>
                                <td>
                                  <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="{{  $destiny->monto }}"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="{{ $destiny->monto }}">
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endforeach
                              @if(empty($info->localDest) && (empty($info->globalDest)))
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
                                        {{ Form::select('destination_ammount_currency[]',$currency,null,['class'=>'form-control destination_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  value="" class="destination_total_ammount_2 form-control" aria-label="...">
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              @endif

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
                                        {{ Form::select('destination_ammount_currency[]',$currency,null,['class'=>'form-control destination_ammount_currency']) }}              
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0"/>
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
                          Sub-Total:<span id="sub_total_destination">{{ $info->totalChargeDest }} </span>@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif&nbsp;
                          <input type="hidden" id="total_destination_ammount" name="sub_total_destination" class="form-control"  value="{{ $info->totalChargeDest  }}"/>
                          <a class="btn addButtonDestination" style="vertical-align: middle">
                            <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                          </a>
                        </h5>
                      </div>
                    </div><br>
                    <hr>
                    <div class='row'>
                      <div class="col-md-12">
                        @php
                        $totalQ = explode(" ",$info->totalQuote);
                        @endphp

                        <div class="form-group text-right">
                          <h3 class="size-16px color-blue"><button id="total" class="btn btn-primary"><b>Total: <span id="total">{{$totalQ[0]}}</span> &nbsp;@if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></button></h3>
                        </div>
                      </div>
                    </div>
                    <div class = 'row'>  
                      <div class="col-md-12">
                        <div class="form-group text-left">
                          <p>Exchange rate: @if($currency_cfg->alphacode=='EUR') 1 EUR = {{$exchange->rates}} USD @else 1 USD = {{$exchange->rates_eur}} EUR @endif</p>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-12">      
                            <div class="header-table title-quote size-14px" style="padding-left: 10px;">
                              <b>Terms & conditions</b>                
                            </div>
                          </div>
                        </div>
                        <br/>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group ">

                              @if(isset($terms_origin) && $terms_origin->count()>0)                             
                              <h5 class="title-quote">Origin harbor</h5>
                              @foreach($terms_origin as $v)
                              {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                              @endforeach
                              @endif
                              @if(isset($terms_destination) && $terms_destination->count()>0)
                              <h5 class="title-quote">Destination harbor</h5>
                              @foreach($terms_destination as $v)
                              {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                              @endforeach
                              @endif
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
        </div>
      </div>

      <div class="col-md-2">
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

    @if(isset($form->price_id ))
    @php
    $priceID = $form->price_id;
    @endphp
    @else
    @php
    $priceID = "";
    @endphp
    @endif
    <input type="hidden" class="form-control" id="incoterm" name="incoterm" value="{{ $form->incoterm }} ">
    <input type="hidden" class="form-control" id="modality" name="modality" value="{{ $form->modality }} ">
    <input type="hidden" class="form-control" id="validity" name="validity" value="{{ $info->contract->expire }}">
    <input type="hidden" class="form-control" id="origin_address" name="origin_address" value="{{ $form->origin_address }} ">
    <input type="hidden" class="form-control" id="destination_address" name="destination_address" value="{{ $form->destination_address }} ">
    <input type="hidden" class="form-control" id="company_id" name="company_id" value="{{ $form->company_id }} ">
    <input type="hidden" class="form-control" id="origin_harbor_id" name="origin_harbor_id" value="{{ $info->origin_port }} ">
    <input type="hidden" class="form-control" id="destination_harbor_id" name="destination_harbor_id" value="{{ $info->destiny_port }} ">
    <input type="hidden" class="form-control" id="price_id" name="price_id" value="{{ $priceID }} ">
    <input type="hidden" class="form-control" id="contact_id" name="contact_id" value="{{ $form->contact_id }} ">
    <input type="hidden" class="form-control" id="qty_20" name="qty_20" value="{{ $form->twuenty }} ">
    <input type="hidden" class="form-control" id="qty_40" name="qty_40" value="{{ $form->forty }} ">
    <input type="hidden" class="form-control" id="qty_40_hc" name="qty_40_hc" value="{{ $form->fortyhc }} ">
    <input type="hidden" class="form-control" id="pick_up_date" name="pick_up_date" value="{{ $form->date }} ">
    <input type="hidden" class="form-control" id="status_id" name="status_id" value="1">
    <input type="hidden" class="form-control" id="delivery_type" name="delivery_type" value="{{ $form->delivery_type }} ">
    <input type="hidden" class="form-control" id="type" name="type" value="{{ $form->type }} ">
    <input type="hidden" class="form-control" id="schedule" name="schedule" value="{{ json_encode($schedules) }}">

    <input type="hidden" class="form-control" id="quantity" name="quantity[]" >
    <input type="hidden" class="form-control" id="height" name="height[]">
    <input type="hidden" class="form-control" id="width" name="width[]">
    <input type="hidden" class="form-control" id="large" name="large[]">
    <input type="hidden" class="form-control" id="weight" name="weight[]">
    <input type="hidden" class="form-control" id="volume" name="volume[]">

    <input type="hidden" class="form-control" id="type_load_cargo" name="type_load_cargo[]">


    @if($email_templates)
    @include('quotes.partials.submitQuoteEmailModal');
    @endif
    {!! Form::close() !!}  
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
