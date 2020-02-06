@extends('layouts.app')
@section('css')
@parent
<link href="/css/quote.css" rel="stylesheet" type="text/css" />

<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
<style>
  .btn-search__quotes {
    top: 50px;
    font-size: 18px; 
    position: relative; 
    padding: 13px 30px; 
    border-radius: 50px !important;
  }
  .q-one, .q-two, .q-three {
    display: flex;
    flex-flow: wrap;
    justify-content: space-between;
  }
  .q-two {
    justify-content: flex-start;
  }
  .q-one div:nth-child(1), 
  .q-one div:nth-child(2), 
  .q-one div:nth-child(3), 
  .q-one div:nth-child(4) {
    overflow: hidden;
  }
  .q-one div:nth-child(1), 
  .q-one div:nth-child(2), 
  .q-one div:nth-child(3) {
    width: 18% !important;
  }
  .q-one div:nth-child(4) {
    width: 38% !important;
  }
  .q-one div:nth-child(5), .q-two div:nth-child(3) {
    width: 100%;
  }
  .q-one div:nth-child(1) label {
    white-space: nowrap;
  }
  .q-two div:nth-child(1) {
    width: 66%;
    margin-right: 10px;
  }
  .q-three div:nth-child(3) {
    width: 100%;
  }
  .q-three div:nth-child(1) {
    width: 50%;
  }
  .dfw {
    width: 100%;
  }
  .no-shadow{
    box-shadow: none;
  }
  .filter-table__quotes, .card-p__quotes, .card__quote-manual {
    padding: 25px;
    box-shadow: 0px 1px 15px 1px rgba(69, 65, 78, 0.08);
  }
  .card__quote-manual {
    margin: 0 15px;
    border: 2px;
  }
  .no-padding {
    padding: 0px !important;
  }
  .card-p__quotes {
    padding-top: 0px !important;
    padding-bottom: 0px !important;
    margin: 0px;
    border-radius: 5px;
    border: 2px solid transparent;
    transition: all 300ms linear;
  }
  .card-p__quotes:hover {
    border-color: #0072fc;
  }
  .btn-detail__quotes {
    width: 140px;
    height: 30px;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    border: 1px solid #ececec;
    transition: all 300ms ease;
  }
  .btn-detail__quotes:hover {
    border-color: #0072fc;
    background-color: #0072fc;    
  }
  .btn-detail__quotes:hover span,.btn-detail__quotes:hover a i {
    color: #fff;
  }
  .btn-detail__quotes span {
    font-size: 12px;
    color: #0072fc;
  }
  .btn-detail__quotes a {
    height: 0px !important;
  }
  .btn-detail__quotes a i {
    color: #a4a2bb;
  }
  .btn-input__select, .btn-input__select-add {
    position: relative;
    left: 25px;
    width: 120px;
    display: flex;
    align-items:center;
    justify-content:center;
    color: #cecece;
    cursor: pointer;
    font-size: 12px;
    padding: 3px 0px;
    border-radius: 5px;
    border: 2px solid #cecece;
    transition: all 300ms ease;
  }
  .btn-input__select:hover, .btn-input__select-add:hover {
    border-color: #0072fc; 
  }

  .input-select[type="checkbox"]{
    display: none; 
  }
  .input-select[type="checkbox"]:checked + .btn-input__select {
    color: #fff;
    display: flex;
    width: 120px;
    border-color: #0072fc;
    justify-content: center;
    background-color: #0072fc;
  }
  .style__select-add {
    color: #fff;
    border-color: #0072fc;
    background-color: #0072fc;
  }
  .add-click {
    color: #cecece !important;
  }
  .input-select[type="checkbox"]:checked + .btn-input__select span {
    display :none;
  }
  .btn-input__select-add {
    width: 60px !important;
    left: 60px;
    visibility: hidden;
  }
  .btn-input__select-gen {
    width: 60px !important;
    left: 60px;
    visibility: hidden;
  }

  .hidden-general{
    display:none !important;
  }
  .visible__select-add {
    visibility: visible;
  }
  .col-txt {
    font-weight: 600;
    color: #0072fc;
    font-size: 18px;
  }
  .btn-d {
    width: 130px;
  }
  .padding {
    padding: 0 25px;
  }
  .padding-v2 {
    padding: 25px;
  }
  .no-margin {
    margin: 0 !important;
  }
  .freight__quotes {
    border-top: none !important;
    border: 3px solid #0072fc; 
    border-radius: 0px 0px 3px 3px;
  }
  .add-class__card-p {
    box-shadow: none;
    border: 3px solid #0072fc; 
    border-bottom: 1px solid #ececec !important;
    border-radius: 3px 3px 0px 0px !important;
  }
  .bg-light {
    padding: 5px 25px;
    border-radius: 3px;
    background-color: #f4f3f8 !important;
  }
  .portalphacode {
    color: #1d3b6e !important;
  }
  .colorphacode {
    color: #7c83b3;
  }
  .bg-rates {
    padding: 2px 5px;
    border-radius: 3px;
    text-align: center;
    background-color: #ececec;
  }
  .wth {
    width: 22%;
  }
  .table-r__quotes {
    height: 100%;
    display: flex;
    justify-content: space-between;
  }
  .table-r__quotes div {
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .b-top {
    border-top: 1px solid #ececec;
  }
  .padding-min {
    padding: 10px !important;
  }
  .b-left {
    border-left: 1px solid #ececec;
  }
  .padding-min-col {
    padding: 45px 10px !important;
  }
  .pos-btn {
    position: relative;
    right: 40px;
  }
  .padding-right-table {
    padding-right: 50px !important;
  }
  .btn-date {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
  }
  .data-rates {
    padding: 5px 25px;
  }
  .arrow-down {
    top: 4px;
    position: relative;
  }
  .monto-down {
    top: 2px;
    position: relative;
  }
  .min-width-filter span {
    min-width: 50px !important;
  }
  .min-width-filter .select2-search--dropdown {
    padding: 0px !important;
  }
  .margin-card {
    margin-top: 50px !important;
    margin-bottom: 50px !important;
  }
  .no-check {
    display: none !important;
  }
  .border-bottom {
    border-bottom: 1px solid #ececec;
  }
  .border-card {
    border-color: #0072fc !important;
  }
  .btn-manual__quotes {
    background-color: transparent;
    color: #0072fc !important;
    border-width: 2px;
    font-weight: 600;
    padding: 0.35rem 1rem;
  }
  .btn-manual__quotes span {
    top: 2px;
    position: relative;
  }
  .warning-p {
    color: #575962;
    font-size: 14px;
    font-weight: 600;

  }
  .warning-p span {
    color: #e74c3c;
  }
  .warning-p i {
    font-size: 33px;
    top: 7px;
    margin-right: 5px;
    position: relative;
    transform: rotate(180deg);
  }
  .btn-remarks {
    width: 95px;
  }
  .full-width {
    width: 100% !important;
  }
  .create-manual {
    background-color: transparent !important;
    color: #36a3f7 !important;
    border-width: 2px;
    border-color: #36a3f7 !important;
  }
  .create-manual:hover {
    background-color: #36a3f7 !important;
    border-color: #36a3f7 !important;
  }
  .select-class::before {
    content:'Select ->';
    font-size: 13px;
  }
  .selected-class:before {
    content: 'Selected';
    font-size: 13px;
  }
  .workgreen {
    color: #6ee99e !important;
    font-size: 12px;
    font-weight: bold !important;
  }
  .downexcel {
    border-color: #6ee99e !important;
  }
  .downexcel a {
    text-decoration: none;
  }
  .downexcel:hover {
    background-color: transparent !important;
  } 
  .downexcel i {
    color: #6ee99e !important;
  }
  .btn-plus__form {
    position: relative;
    top: 8px;
  }
  .include-checkbox[type="checkbox"] {
    display: none;
  }
  .for-check {
    display: flex;
    padding-left: 40px;
    padding-right: 0px;
  }
  .label-check {
    position: relative;
  }
  .label-check::before {
    content: '';
    position: absolute;
    top: -1px;
    left: -25px;
    width: 15px;
    height: 15px;
    background: transparent;
    border: 2px solid #0000ff;
    border-radius: 3px;
    display: flex;
    /*align-items: center*/
    justify-content: center;
  }
  .include-checkbox[type="checkbox"]:checked + .label-check::before {
    content: '✔';
    color: #0000ff;
    line-height: 15px;
  }
  /* estilos */
</style>
@endsection

@section('title', 'Quotes')
@section('content')
<br>

<div class="padding">
  {!! Form::open(['id'=>'FormQuote' , 'class' => 'form-group m-form__group dfw']) !!}

  <div class="col-lg-12">


    <div class="m-portlet">
      <div class="m-portlet__body">
        <div class="tab-content">
          <div>
            <div class="row">
              <div class="col-lg-2">
                <label>Quote Type</label>
                {{ Form::select('type',['1' => 'FCL','2' => 'LCL','3'=>'AIR'],@$form['type'],['id'=>'quoteType','class'=>'m-select2-general form-control']) }}
              </div>

              <div class="col-lg-2" id="equipment_id">
                <label>Equipment</label>
                {{ Form::select('equipment[]',['20' => '20\'','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],@$form['equipment'],['class'=>'m-select2-general form-control','id'=>'equipment','multiple' => 'multiple','required' => 'true']) }}
              </div>

              <div class="col-lg-2">
                <label>Company</label>

                <div class="m-input-icon m-input-icon--right">
                  {{ Form::select('company_id_quote', $companies,@$form['company_id_quote'],['class'=>'m-select2-general form-control','id' => 'm_select2_2_modal']) }} 
                  <span class="m-input-icon__icon m-input-icon__icon--right">
                    <span>
                      <a   onclick="AbrirModal('add',0)" data-container="body" data-toggle="m-tooltip" data-placement="top" title="" data-original-title="Add Company if not exist"> <i class="la  la-plus-circle btn-plus__form" style="color:blue; font-size: 18px;" ></i> </a>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Contact</label>
                <div class="m-input-icon m-input-icon--right">
                  {{ Form::select('contact_id',[],null,['id' => 'contact_id', 'class'=>'m-select2-general form-control']) }}
                  {{  Form::hidden('contact_id_num', @$form['contact_id'] , ['id' => 'contact_id_num'  ])  }}
                  <span class="m-input-icon__icon m-input-icon__icon--right">
                    <span>
                      <a    onclick="AbrirModal('addContact',0)" data-container="body" data-toggle="m-tooltip" data-placement="top" title="" data-original-title="Add Contact if not exist">   <i class="la  la-plus-circle btn-plus__form" style="color:blue; font-size: 18px;"></i></a>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Price level</label>
                {{ Form::select('price_id',[],null,['id' => 'price_id' ,'class'=>'form-control m-select2-general']) }}
                {{  Form::hidden('price_id_num', @$form['price_id'] , ['id' => 'price_id_num'  ])  }}
              </div>
              <div class="col-lg-2">
                <label>Direction</label>
                {{ Form::select('mode',['1' => 'Export','2' => 'Import'],@$form['mode'],['id'=>'mode','placeholder'=>'Select','class'=>'m-select2-general form-control','required' => 'true']) }}
              </div>

            </div><br>
            <div class="row">
              <div class="col-lg-2" >
                <div id="origin_harbor_label">
                  <label>Origin port</label>
                  {{ Form::select('originport[]',$harbors,@$form['originport'],['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor','required' => 'true']) }}
                </div>

                <div id="origin_airport_label" style="display:none;">
                  <label>Origin airport</label>
                  <select id="origin_airport" name="origin_airport_id" class="form-control"></select>
                </div>

              </div>
              <div class="col-lg-2">
                <div  id="destination_harbor_label">
                  <label>Destination port</label>
                  {{ Form::select('destinyport[]',$harbors,@$form['destinyport'],['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor','required' => 'true']) }}
                </div>
                <div id="destination_airport_label" style="display:none;">
                  <label>Destination airport</label>
                  <select id="destination_airport" name="destination_airport_id" class="form-control"></select>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Date</label>
                <div class="input-group date">
                  {!! Form::text('date', @$form['date'], ['id' => 'm_daterangepicker_1' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off']) !!}
                  {!! Form::text('date_hidden', null, ['id' => 'date_hidden','hidden'  => 'true']) !!}

                  <div class="input-group-append">
                    <span class="input-group-text">
                      <i class="la la-calendar-check-o"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-lg-2" id="delivery_type_label">
                <label>Delivery type</label>
                {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],@$form['delivery_type'],['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
              </div>
              <div class="col-lg-2" id="delivery_type_air_label" style="display: none;">
                <label>Delivery type</label>
                {{ Form::select('delivery_type_air',['5' => 'AIRPORT(Origin) To AIRPORT(Destination)','6' => 'AIRPORT(Origin) To DOOR(Destination)','7'=>'DOOR(Origin) To AIRPORT(Destination)','8'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type_air']) }}
              </div>
              <div class="col-lg-2 {{$hideO}}" id="origin_address_label">
                <label>Origin address</label>
                {!! Form::text('origin_address',@$form['origin_address'], ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
              </div>
              <div class="col-lg-2 {{$hideD}}" id="destination_address_label">
                <label>Destination address</label>
                {!! Form::text('destination_address',@$form['destination_address'] , ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
              </div>

            </div>
            <div class="row">
              <div class="col-lg-2 for-check" >   
                {{ Form::checkbox('chargeOrigin',null,@$chargeOrigin,['id'=>'mode1', 'class' => 'include-checkbox']) }}
                <label for="mode1" class="label-check">Include origin charges</label></div>
              <div class="col-lg-2 for-check" >
                {{ Form::checkbox('chargeDestination',null,@$chargeDestination,['id'=>'mode2', 'class' => 'include-checkbox']) }}
                <label for="mode2" class="label-check">Include destination charges</label></div>
              <div class="col-lg-2 for-check" >
                {{ Form::checkbox('chargeFreight',null,@$chargeFreight,['id'=>'mode3', 'class' => 'include-checkbox']) }}
                <label for="mode3" class="label-check">Include freight charges</label></div>
            </div>

          </div>
        </div><br>
        <div class="row">


        </div>
        <div class="form-group m-form__group row" id="lcl_air_load" style="display: none; margin-top:25px;">
          <div class="col-lg-2">
            <label>
              <b>LOAD</b>
            </label>
          </div>
          <div class="col-lg-10">
            <ul class="nav nav-tabs" role="tablist" style="text-transform: uppercase; letter-spacing: 1px;">
              <li class="nav-item">
                <a href="#tab_1_1" class="nav-link {{ @$simple }}" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(1)"> Calculate by total shipment </a>
              </li>
              <li class="nav-item">
                <a href="#tab_1_2" class="nav-link {{ @$paquete }}" data-toggle="tab" style=" font-weight: bold;" onclick="change_tab(2)"> Calculate by packaging </a>
              </li>
            </ul>
            <div class="tab-content">
              <!-- Simple -->
              <div class="tab-pane fade  {{ @$simple }}"   id="tab_1_1"  >
                <div class="row">
                  <div class="col-md-4">
                    <label>
                      Packages
                    </label>
                    <div class="m-bootstrap-touchspin-brand">
                      <div class="input-group">
                        <input type="number" id="total_quantity" value="{{ @$form['total_quantity'] }}" name="total_quantity" min="0" step="0.0001" class="total_quantity form-control" placeholder="" aria-label="...">
                        <div class="input-group-btn">
                          <select class="form-control" id="type_cargo" name="cargo_type" style="height:2.5em">
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
                        <input type="number" id="total_weight" value="{{ @$form['total_weight'] }}" name="total_weight" min="0" step="0.0001" class="total_weight form-control" placeholder="" aria-label="...">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height:2.5em">KG <span class="caret"></span></button>
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
                        <input type="number" id="total_volume" value="{{ @$form['total_volume'] }}" name="total_volume" min="0" step="0.0001" class="total_volume form-control" placeholder="" aria-label="...">
                        <div class="input-group-btn">
                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height:2.5em">M<sup>3</sup> <span class="caret"></span></button>
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
                    <span id="chargeable_weight_total"> {{ @$form['chargeable_weight'] }} m<sup>3</sup></span>
                  </div>
                </div>
              </div>
              <!-- By packing -->
              <div class="tab-pane fade {{ $paquete }} " id="tab_1_2"  >
                <input type="hidden" value="@$form['total_pallets']" id="total_pallets" name="total_pallets"/>
                <input type="hidden" value="@$form['total_packages']" id="total_packages" name="total_packages"/>

                @foreach(@$form['quantity'] as $key => $var)

                @if($var != null )
                <div class="template">
                  <div class="row">
                    <div class="col-md-2" style="padding-right:0px;">
                      <select name="type_load_cargo[]" class="type_cargo type_cargo_2 form-control size-12px" style="height: 2.7em">
                        <option {{ ($form['type_load_cargo'][$key] == "" ) ? 'selected' : ""}} value="">Choose an option</option>
                        <option {{ ($form['type_load_cargo'][$key] == "1" ) ? 'selected' : ""}} value="1">Pallets</option>
                        <option {{ ($form['type_load_cargo'][$key] == "2" ) ? 'selected' : ""}} value="2">Packages</option>
                      </select>

                    </div>
                    <div class="col-md-2" style="padding-right:0px;">
                      <input id="quantity" min="1" value="{{ @$form['quantity'][$key] }}" name="quantity[]" class="quantity quantity_2 form-control size-12px" type="number" placeholder="quantity" />
                    </div>
                    <div class="col-md-5" style="padding-right:0px;">
                      <div class="btn-group btn-group-justified" role="group" aria-label="...">
                        <div class="btn-group" role="group" style="margin-right: 15px;">
                          <input class="height form-control size-12px height_2" value="{{ @$form['height'][$key] }}" min="0" name="height[]"  type="number" placeholder="H"/>
                        </div>
                        <div class="btn-group" role="group" style="margin-right: 15px;">
                          <input class="width form-control size-12px width_2" min="0" value="{{ @$form['width'][$key] }}" name="width[]" type="number" placeholder="W"/>
                        </div>
                        <div class="btn-group" role="group" style="margin-right: 15px;">
                          <input class="large form-control size-12px large_2" min="0" value="{{ @$form['large'][$key] }}" name="large[]"  type="number" placeholder="L"/>
                        </div>
                        <div class="btn-group" role="group" >
                          <div class="input-group-btn">
                            <div class="btn-group">
                              <button class="btn btn-default dropdown-toggle dropdown-button" type="button" data-toggle="dropdown" style="padding:0.45em 0.65em">
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
                        <input type="number"  value="{{ @$form['weight'][$key] }}" name="weight[]" min="0" step="0.0001" class="weight weight_2 form-control size-12px" placeholder="Weight" aria-label="...">
                      </div><!-- /input-group -->
                    </div>
                    <div class="col-md-1">
                      <input type="hidden" class="volume_input"  value="{{ @$form['volume'][$key] }}" name="volume[]"/>

                      <input type="hidden" value="{{ @$form['quantity'][$key]  }}" class="quantity_input" id="quantity_input" name="total_quantity_pkg"/>
                      <input type="hidden"  value="{{ @$form['weight'][$key] * @$form['quantity'][$key] }}"   class="weight_input" id="weight_input" name="total_weight_pkg"/>
                    </div>
                    <div class="col-md-4">

                      <p class=""><span class="quantity">{{ @$form['quantity'][$key] }} un</span> <span class="volume"> {{ @$form['volume'][$key] }} m<sup>3</sup></span> <span class="weight"> {{ @$form['weight'][$key] * @$form['quantity'][$key] }}kg</span></p>
                    </div>
                  </div>
                </div>
                @endif

                @endforeach

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
                        <input type="number" name="weight[]" min="0" step="0.0001" class="weight form-control size-12px" placeholder="Weight" aria-label="...">
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
                <br>
                <div class="row">
                  <div class="col-md-4">
                    <b>Total: </b>
                    <span id="total_quantity_pkg">{{ @$form['total_quantity_pkg'] }} un</span>
                    <span id="total_volume_pkg">{{ @$form['total_volume_pkg'] }} m<sup>3</sup></span>
                    <span id="total_weight_pkg">{{ @$form['total_weight_pkg'] }} kg</span>
                    <input type="hidden" id="total_quantity_pkg_input"  value="{{ @$form['total_quantity_pkg'] }}"  name="total_quantity_pkg"/>
                    <input type="hidden" id="total_volume_pkg_input"  value="{{ @$form['total_volume_pkg'] }}"  name="total_volume_pkg"/>
                    <input type="hidden" id="total_weight_pkg_input"  value="{{ @$form['total_weight_pkg'] }}"  name="total_weight_pkg"/>
                  </div>
                  <div class="col-md-8">
                    <b>Chargeable weight:</b>
                    <span id="chargeable_weight_pkg"> {{ @$form['chargeable_weight'] }} m<sup>3</sup></span>
                    <input type="hidden" id="chargeable_weight_pkg_input" value="{{ @$form['chargeable_weight'] }}" name="chargeable_weight"/>
                  </div>
                </div>

              </div>

            </div>
          </div>
        </div><br>

        <div class="row">
          <div class="col-lg-12 no-padding">
            <div class="row  justify-content-between">
              <div class="col-lg-10 d-flex message  align-items-end align-self-end">
                @if(isset($arreglo))
                @if($arreglo->isEmpty())
                <p class="warning-p"><span><i class="la la-info-circle"></i>No freight rates founded for this tradelane.</span> You can create a quote manually.</p>
                @endif
                @endif
              </div>
              <div class="col-lg-2 d-flex justify-content-star align-items-end" align='right'> 
                <!--  <button type="button" class="btn m-btn--pill  btn-info quote_man">Create Manual Quote<span class="la la-arrow-right"></span>
</button> -->
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">   
            <center>
              <button type="button" class="btn m-btn--pill  btn-search__quotes  btn-info quote_search">Search</button>
              <button type="button" class="btn m-btn--pill  btn-search__quotes btn-info quote_man create-manual">Create Manual</span></button>
            </center>
        </div>
      </div>
    </div>
  </div>      
</div>
</div>
</div>

{!! Form::close() !!}


</div>


@if(isset($arreglo))
@if(!$arreglo->isEmpty())
<div class="row padding search">
  <div class="col-lg-12"><br><br><span class="col-txt">Results</span><br><br></div>
</div>
<div class="row padding search"  ><!-- Tabla de muestreo de las cotizaciones -->

  {!! Form::open(['route' => 'quotes-v2.storeLCL','class' => 'form-group m-form__group']) !!}
  <input  type="hidden" name="forma" value="aaaaaa" >
  <input  type="hidden" name="form" value="{{ json_encode($form) }}" class="btn btn-sm btn-default btn-bold btn-upper formu">
  <div class="col-lg-12">
    <div class="m-portlet no-shadow">
      <div class="m-portlet__body no-padding">
        <div class="tab-content">
          <div>
            <!-- Empieza el card de filtro -->
            <div class="filter-table__quotes">
              <div class="row">
                <div class="col-lg-6">
                  <!--<label>Sort By</label>-->
                  <div class="input-group m-input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="la la-filter"></i></span>
                    </div>
                    <div >
                      <select class="form-control m-select2-general ">
                        <option value="AK">Asc</option>
                        <option value="AK">Desc</option>
                        <option value="AK">Minnor Price</option>
                        <option value="AK">Mayor Price</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6" align='right'> <button type="submit" class="btn m-btn--pill    btn-info">Create Quote</button></div>
              </div>

              <div class="row">
                <div class="col-lg-12"><hr></div>
              </div>
              <!-- Empieza columna titulos de la tabla -->

              <div class="row" >
                <div class="col-lg-2" >  <span class="portcss"> Carrier</span></div>
                <div class="col-lg-10">
                  <div class="row">
                    <div class="col-lg-8">
                      <div class="row col-lg-12">
                        <div class="col-lg-8" style="padding-left: 2px;"><span class="portcss">Origin</span></div>
                        <div class="col-lg-4" ><span class="portcss">Destination</span></div>
                      </div>
                    </div>
                    <div class="col-lg-3 padding-right-table" style="padding-left: 0;">
                      <div style="display:flex; justify-content:space-between">
                        <span class="portcss">Price</span>
                      </div>
                    </div>
                    <div class="col-lg-1" ></div>
                  </div>
                </div>
              </div>

            </div>
            <!-- Termina el card de filtro -->
            <div class="row">
              <div class="col-lg-12"><br><br></div>
            </div>
            @foreach($arreglo as $arr)
            <!-- Empieza tarjeta de cotifzacion -->

            <div class="card-p__quotes input-select{{$loop->iteration}}"  style="margin-bottom: 50px;">
              <div class="row initial-card" id='principal{{$loop->iteration}}' >
                <div class="col-lg-2 d-flex align-items-center img-bottom-border">            
                  <div class="m-widget5">
                    <div class="m-widget5__item no-padding no-margin">
                      <div class="m-widget5__pic"> 
                        <img src="{{ url('imgcarrier/'.$arr->carrier->image) }}" alt="" title="" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-10 b-left info-bottom-border">
                  <div class="row">
                    <div class="col-lg-8 no-padding padding-min-col d-flex justify-content-start">
                      <div class="row col-lg-12">
                        <div class="col-lg-4">
                          <span class="portcss"> {{$arr->port_origin->name  }}</span><br>
                          <span class="portalphacode"> {{$arr->port_origin->code  }}</span>
                        </div>
                        <div class="col-lg-4 d-flex flex-column justify-content-center">
                          <div class="progress m-progress--sm">
                            <div class="progress-bar " role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                          Contract: {{ $arr->contract->name }} / {{ $arr->contract->number }}
                        </div>
                        <div class="col-lg-4 d-flex align-items-center flex-column">
                          <span class="portcss"> {{$arr->port_destiny->name  }}</span>
                          <span class="portalphacode"> {{$arr->port_destiny->code  }}</span>
                        </div>
                      </div>
                      <br>
                    </div><!-- aqui -->

                    <div class="col-lg-3" style="padding-right: 35px;">
                      <div class="table-r__quotes">
                        <div class="wth " ><span class="darkblue validate">{{$arr->totalQuote  }}  {{ $arr->quoteCurrency }}</span> </div>
                      </div>
                    </div>
                    <div class="col-lg-1 no-padding d-flex align-items-center pos-btn">
                      <input type="checkbox" id="input-select{{$loop->iteration}}" class="input-select no-check btnrate" rate-id ='{{$arr->id }} infocheck' name="info[]" value="{{ json_encode($arr) }}">
                      <label for="input-select{{$loop->iteration}}"  class="btn-input__select btnrate select-class selected"  rate-id ='{{$arr->id }}' Select></label>
                    </div>
                    <div class="col-lg-12 b-top no-padding padding-min">
                      <div class="row justify-content-between">

                        @if(!empty($arr->remarks) )
                        <div class="col-lg-2">
                          <div class="btn-detail__quotes btn-remarks">
                            <a  id='display_r{{$loop->iteration}}' onclick="display_r({{$loop->iteration}})" class="l"  title="Cancel" >
                              <span class="workblue">Remarks</span>  
                              <i  class="la la-angle-down blue"></i></a>
                          </div>
                        </div>
                        @endif

                        @if(isset($arr->sheduleType))
                        <div class="col-lg-3 d-flex align-items-center">
                          <span class="portalphacode" style="margin-right:15px;">Validity: </span> {{   \Carbon\Carbon::parse($arr->contract->validity)->format('d M Y') }} - {{   \Carbon\Carbon::parse($arr->contract->expire)->format('d M Y') }}
                        </div>
                        @else
                        <div class="col-lg-6 d-flex align-items-center">
                          <span class="portalphacode" style="margin-right:15px;" >Validity:   </span>  {{   \Carbon\Carbon::parse($arr->contract->validity)->format('d M Y') }} - {{   \Carbon\Carbon::parse($arr->contract->expire)->format('d M Y') }}

                        </div>
                        @endif

                        @if(isset($arr->sheduleType))
                        <div class="col-lg-2 d-flex align-items-center">
                          <span class="portalphacode" style="margin-right:5px;">Schedule Type: </span> {{ $arr->sheduleType  }}
                        </div>
                        <div class="col-lg-1 d-flex align-items-center">
                          <span class="portalphacode" style="margin-right:15px; white-space:nowrap">  TT:  </span>  {{ $arr->transit_time   }}
                        </div>
                        <div class="col-lg-1 d-flex align-items-center">
                          <span class="portalphacode" style="margin-right:15px;"> Via: </span> {{  $arr->via }}
                        </div>
                        @endif
                        <div class="col-lg-3 no-padding d-flex justify-content-end">
                          @if(($arr->excelRequest  !="0") || ($arr->excelRequestLCL !="0") )
                          <div class="downexcel" style="margin-right: 10px;">
<!--
                            <a  id='excel_l{{$loop->iteration}}' href="/v2/quotes/excelLcl/{{ $arr->excelRequest }}/{{ $arr->excelRequestLCL }}"  class="l detailed-cost"  title="Cancel" >
                              <span class="workgreen"><i class="icon-excel"></i></span>

                              <i class="la la-file-excel-o"></i>
                            </a>
-->
							  <a  id='excel_l{{$loop->iteration}}' href="#" onclick="downlodRequest({{ $arr->excelRequest }},{{ $arr->excelRequestLCL }})" class="l detailed-cost"  title="Cancel" >
                              <span class="workgreen"><i class="icon-excel"></i></span>

                              <i class="la la-file-excel-o"></i>
                            </a>
                          </div>
                          @endif
                          <div class="btn-detail__quotes btn-d">
                            <a  id='display_l{{$loop->iteration}}' onclick="display({{$loop->iteration}})" class="l detailed-cost"  title="Cancel" >
                              <span class="workblue">Detailed Cost</span>  
                              <i  class="la la-angle-down blue"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Termina tarjeta de cotifzacion -->

              <!-- Gastos Origen-->


              @if(!$arr->localOrig->isEmpty())

              <div class="row no-margin margin-card" id='origin{{$loop->iteration}}' hidden='true' >
                <div class="col-lg-12">
                  <div class="row">
                    <span class="darkblue cabezeras">Origin</span><br><br>
                  </div>

                  <div class="row bg-light">
                    <div class="col-lg-2"><span class="portalphacode">Charge</span></div>
                    <div class="col-lg-2"><span class="portalphacode">Detail</span></div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="wth" ><span class="portalphacode">Units</span></div>
                        <div class="wth" ><span class="portalphacode">Price Per Units</span></div>
                        <div class="wth"><span class="portalphacode">Ammount</span></div>
                        <div class="wth"><span class="portalphacode">Markup</span></div>
                        <div class="wth"><span class="portalphacode">Currency</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">Total</span></div>
                  </div>

                  @foreach($arr->localOrig as $localO)   
                  @foreach($localO as $localorig)
                  @foreach($localorig as $localorigin)
                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode">{{ $localorigin['surcharge_name']}}</div>
                    <div class="col-lg-2 colorphacode">{{ $localorigin['calculation_name']}}</div>
                    <div class="col-lg-7 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="wth" >
                          <span class="bg-rates"> {{ $localorigin['cantidad']}} </span> 
                        </div>
                        <div class="wth" >
                          <span class="bg-rates"> {{ number_format($localorigin['monto'] /   $localorigin['cantidad'], 2, '.', '') }} </span> 
                        </div>
                        <div class="wth" >
                          <span class="bg-rates"> {{ $localorigin['monto']}} </span> 
                        </div>

                        <div class="wth" >  
                          <span class="bg-rates">{{ $localorigin['markup']}} </span>
                        </div>
                        <div class="wth" >     
                          <span class="bg-rates"> {{ $localorigin['currency']}} </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode">{{ $localorigin['montoMarkup']}}</span></div>

                  </div>
                  @endforeach
                  @endforeach
                  @endforeach
                  <div class="row bg-light">
                    <div class="col-lg-4 col-lg-offset-" ><span class="portalphacode">Subtotal Origin Charges: </span> {{ $arr->totalOrigin  }} {{ $arr->quoteCurrency }}</div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="wth" ><span class="portalphacode"> </span></div>

                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode"></span></div>
                  </div>
                </div>
              </div>
              @endif
              <!-- Gastos Freight-->
              <div class="row no-margin margin-card" id='freight{{$loop->iteration}}'  hidden='true' >
                <div class="col-lg-12">
                  <div class="row">
                    <span class="darkblue cabezeras">Freight</span><br><br>
                  </div>
                  <div class="row bg-light">
                    <div class="col-lg-2"><span class="portalphacode">Charge</span></div>
                    <div class="col-lg-2"><span class="portalphacode">Detail</span></div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="wth" ><span class="portalphacode">Units</span></div>
                        <div class="wth" ><span class="portalphacode">Price Per Units</span></div>
                        <div class="wth"><span class="portalphacode">Ammount</span></div>
                        <div class="wth"><span class="portalphacode">Markup</span></div>
                        <div class="wth"><span class="portalphacode">Currency</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">Total</span></div>
                  </div>
                  @foreach($arr->rates as $rates)
                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode">{{ $rates['type'] }}  </div>
                    <div class="col-lg-2 colorphacode"> {{ $rates['detail'] }} </div>
                    <div class="col-lg-7 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="wth" >
                          <span class="bg-rates">  {{ $rates['cantidad'] }}</span> 
                        </div>
                        <div class="wth" >
                          <span class="bg-rates">  {{ $rates['price'] }}</span> 
                        </div>
                        <div class="wth" >
                          <span class="bg-rates"> {{ $rates['subtotal'] }} </span> 
                        </div>

                        <div class="wth" >  
                          <span class="bg-rates">{{ $rates['markup'] }}  </span>
                        </div>
                        <div class="wth" >     
                          <span class="bg-rates"> {{ $rates['currency'] }} </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode"> {{ $rates['total'] }}</span></div>

                  </div>
                  @endforeach

                  @if(!$arr->localFreight->isEmpty())
                  @foreach($arr->localFreight as $localF)   
                  @foreach($localF as $localfre)
                  @foreach($localfre as $localfreight)
                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode">{{ $localfreight['surcharge_name']}}</div>
                    <div class="col-lg-2 colorphacode">{{ $localfreight['calculation_name']}}</div>
                    <div class="col-lg-7 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="wth" >
                          <span class="bg-rates"> {{ $localfreight['cantidad']}} </span> 
                        </div>

                        <div class="wth" >
                          <span class="bg-rates"> {{ number_format($localfreight['monto'] /   $localfreight['cantidad'], 2, '.', '') }}  </span> 
                        </div> 
                        <div class="wth" >
                          <span class="bg-rates"> {{ $localfreight['monto']}} </span> 
                        </div>

                        <div class="wth" >  
                          <span class="bg-rates">{{ $localfreight['markup']}} </span>
                        </div>
                        <div class="wth" >     
                          <span class="bg-rates"> {{ $localfreight['currency']}} </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode">{{ $localfreight['montoMarkup']}}</span></div>
                  </div>
                  @endforeach
                  @endforeach
                  @endforeach
                  @endif
                  <div class="row bg-light">
                    <div class="col-lg-4 col-lg-offset-" ><span class="portalphacode">Subtotal Freight Charges :  </span>{{ $arr->totalFreight  }} {{ $arr->quoteCurrency }}</div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="wth" ><span class="portalphacode"></span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">{{ $arr->typeCurrency }}</span></div>
                  </div>
                </div>
              </div>

              <!-- Gastos Destino-->
              @if(!$arr->localDest->isEmpty())
              <div class="row no-margin margin-card" id='destiny{{$loop->iteration}}'  hidden='true' >
                <div class="col-lg-12">
                  <div class="row">
                    <span class="darkblue cabezeras">Destination</span><br><br>
                  </div>
                  <div class="row bg-light">
                    <div class="col-lg-2"><span class="portalphacode">Charge</span></div>
                    <div class="col-lg-2"><span class="portalphacode">Detail</span></div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="wth" ><span class="portalphacode">Units</span></div>
                        <div class="wth" ><span class="portalphacode">Price Per Units</span></div>
                        <div class="wth"><span class="portalphacode">Ammount</span></div>
                        <div class="wth"><span class="portalphacode">Markup</span></div>
                        <div class="wth"><span class="portalphacode">Currency</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">Total</span></div>
                  </div>
                  @foreach($arr->localDest as $local)   
                  @foreach($local as $localA)
                  @foreach($localA as $localdestiny)
                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode">{{ $localdestiny['surcharge_name']}}</div>
                    <div class="col-lg-2 colorphacode">{{ $localdestiny['calculation_name']}}</div>
                    <div class="col-lg-7 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="wth" >
                          <span class="bg-rates"> {{ $localdestiny['cantidad']}} </span> 
                        </div>
                        <div class="wth" >
                          <span class="bg-rates">  {{ number_format($localdestiny['monto'] /   $localdestiny['cantidad'], 2, '.', '') }}  </span> 
                        </div> 
                        <div class="wth" >
                          <span class="bg-rates"> {{ $localdestiny['monto']}} </span> 
                        </div>

                        <div class="wth" >  
                          <span class="bg-rates">{{ $localdestiny['markup']}} </span>
                        </div>
                        <div class="wth" >     
                          <span class="bg-rates"> {{ $localdestiny['currency']}} </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode">{{ $localdestiny['montoMarkup']}}</span></div>

                  </div>
                  @endforeach
                  @endforeach
                  @endforeach


                  <div class="row bg-light">
                    <div class="col-lg-4 col-lg-offset-" ><span class="portalphacode">Subtotal Destination Charges: </span> {{ $arr->totalDestiny  }} {{ $arr->quoteCurrency }}</div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="wth" ><span class="portalphacode"> </span></div>

                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode"></span></div>
                  </div>
                </div>
              </div>
              @endif


              @if(isset($arr->remarks))
              <div class="row no-margin margin-card" id='remark{{$loop->iteration}}'  hidden='true' >
                <div class="col-lg-12">
                  <div class="row">
                    <span class="darkblue cabezeras">Remarks</span><br><br>
                  </div>
                  <div class="row">
                    <div class="col-lg-6"><span class="monto-down">{!! $arr->remarks !!} </span></div>

                  </div>
                </div>

              </div>

              @endif


            </div>
            @endforeach
          </div>      
        </div>
      </div>
    </div>

  </div>

  {!! Form::close() !!}
</div>
@endif
@endif


@include('contacts.partials.contactsModal')
@include('companies.partials.companiesModal')

@endsection


@section('js')
@parent


<script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>
@if(empty($arreglo))
<script>

  $('select[name="contact_id"]').prop("disabled",true);
  $("select[name='company_id_quote']").val('');
  $('#select2-m_select2_2_modal-container').text('Please an option');
</script>
@else

<script>
  precargar();
  precargarLCL();
</script>


@endif
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>
<script type="application/x-javascript" src="/js/toarts-config.js"></script>
<script>
	
	function downlodRequest(id,id2){
		url='{!! route("quotes-v2.excel-lcl",[":id","*id"]) !!}';
		url = url.replace(':id', id);
		url = url.replace('*id', id2);
		$.ajax({
			url:url,
			method:'get',
			success: function(response){
				if(response.success == true){
					window.location = response.url;
				}else {
					toastr.error('File not found');
				}
				//console.log(response);
			}
		});
	}
	
  $('.selected').on('click', function(){
    $(this).toggleClass('selected-class');

    if($('.selected').hasClass('selected-class') ) {
      $('.create-manual').prop( "disabled", true );
    }else{
      $('.create-manual').prop( "disabled", false );
    }
  });

  $(document).ready(function() {
    var divRow = document.getElementsByClassName('data-rates');
    var numDivRow = divRow.length;
    var count = 0;
    //console.log(numDivRow);

    for(var i = 1; i < numDivRow; i++){
      if(i%2 == 1){
        var clase = divRow[i];
        //console.log(clase);
        $(clase).css({
          'background-color' : '#fafafa'
        });      
        //console.log(clase);
      }
    }

  });


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

  $valor =   $('#date_hidden').val();

  if($valor != 0){
    $('#m_datepicker_2').val($valor);
  }
  function setdateinput(){
    var date = $('#m_datepicker_2').val();
    $('#date_hidden').val(date);
  }


  $('.m-select3-general').select2();

  $('.select2-selection__arrow').remove();



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