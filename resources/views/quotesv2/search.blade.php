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
    display: flex;
    justify-content: center;
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
  .input-select[type="checkbox"]:checked + .btn-input__select span {
    display :none;
  }
  .btn-input__select-add {
    width: 60px !important;
    left: 60px;
    visibility: hidden;
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
  .width {
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

  /* estilos */
</style>
@endsection

@section('title', 'Quotes')
@section('content')
<br>

<div class="row padding">

  <div class="col-lg-12">
    {!! Form::open(['id'=>'FormQuote' , 'class' => 'form-group m-form__group']) !!}

    <div class="m-portlet">
      <div class="m-portlet__body">
        <div class="tab-content">
          <div>
            <div class="row">
              <div class="col-lg-2">
                <label>Quote Type</label>
                {{ Form::select('type',['1' => 'FCL','2' => 'LCL'],null,['id'=>'quoteType','class'=>'m-select2-general form-control']) }}
              </div>
              <div class="col-lg-2">
                <label>Equipment</label>
                {{ Form::select('equipment[]',['20' => '20\'','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],@$form['equipment'],['class'=>'m-select2-general form-control','id'=>'equipment','multiple' => 'multiple','required' => 'true']) }}
              </div>
              <div class="col-lg-2">
                <label>Company</label>

                <div class="m-input-icon m-input-icon--right">
                  {{ Form::select('company_id_quote', $companies,@$form['company_id_quote'],['class'=>'m-select2-general form-control','id' => 'm_select2_2_modal','required'=>'true']) }} 
                  <span class="m-input-icon__icon m-input-icon__icon--right">
                    <span>
                      <i class="la 	la-plus-circle" style="color:blue; font-size: 18px;"></i>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Contact</label>
                <div class="m-input-icon m-input-icon--right">
                  {{ Form::select('contact_id',[],null,['id' => 'contact_id', 'class'=>'m-select2-general form-control','required'=>'true']) }}
                  {{  Form::hidden('contact_id_num', @$form['contact_id'] , ['id' => 'contact_id_num'  ])  }}
                  <span class="m-input-icon__icon m-input-icon__icon--right">
                    <span>
                      <i class="la 	la-plus-circle" style="color:blue; font-size: 18px;"></i>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Price level</label>
                {{ Form::select('price_id',[],null,['id' => 'price_id' ,'class'=>'form-control']) }}
                {{  Form::hidden('price_id_num', @$form['price_id'] , ['id' => 'price_id_num'  ])  }}
              </div>
            </div><br>
            <div class="row">
              <div class="col-lg-2" id="origin_harbor_label">
                <label>Origin port</label>
                {{ Form::select('originport[]',$harbors,@$form['originport'],['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor','required' => 'true']) }}
              </div>
              <div class="col-lg-2" id="destination_harbor_label">
                <label>Destination port</label>
                {{ Form::select('destinyport[]',$harbors,@$form['destinyport'],['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor','required' => 'true']) }}
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
              <div class="col-lg-2">
                <label>Delivery type</label>
                {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
              </div>
              <div class="col-lg-2 hide" id="origin_address_label">
                <label>Origin address</label>
                {!! Form::text('origin_address', '', ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
              </div>
              <div class="col-lg-2 hide" id="destination_address_label">
                <label>Destination address</label>
                {!! Form::text('destination_address', '', ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
              </div>

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
                            <input type="number" id="total_quantity" name="total_quantity" min="0" step="0.0001" class="total_quantity form-control" placeholder="" aria-label="...">
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
                            <input type="number" id="total_weight" name="total_weight" min="0" step="0.0001" class="total_weight form-control" placeholder="" aria-label="...">
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
                            <input type="number" id="total_volume" name="total_volume" min="0" step="0.0001" class="total_volume form-control" placeholder="" aria-label="...">
                            <div class="input-group-btn">
                              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">M<sup>3</sup> <span class="caret"></span></button>
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
                            <input type="number" id="weight" name="weight[]" min="0" step="0.0001" class="weight form-control size-12px" placeholder="Weight" aria-label="...">
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
                      <br>
                      <br>
                      <div class="col-md-12">
                        <b>Chargeable weight:</b>
                        <span id="chargeable_weight_pkg"></span>
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



            <div class ="row">  <div class="col-lg-12"> <center><button type="button" class="btn m-btn--pill  btn-search__quotes  btn-info quote_search">Search</button></center> </div>  </div>
          </div>
        </div>      
      </div>
    </div>

    {!! Form::close() !!}
  </div>


  <div class="col-lg-12 no-padding">
    <div class="row card__quote-manual">
      <div class="col-lg-2" class=""> 
        <label>Carrier Manual</label>
        {{ Form::select('carrieManual',$carrierMan,@$form['carrieManual'],['class'=>'m-select2-general form-control','id'=>'carrieManual','required' => 'true']) }}
      </div>
      <div class="col-lg-2 d-flex justify-content-star align-items-end" align='right'> 
        <button type="button" class="btn m-btn--pill btn-manual__quotes btn-info quote_man">Quote Manual <span class="la la-arrow-right"></span>
        </button>
      </div>
      <div class="col-lg-6 d-flex align-items-end align-self-end">
        @if(isset($arreglo))
        @if($arreglo->isEmpty())
        <p class="warning-p"><span><i class="la la-info-circle"></i>No freight rates founded for this tradelane.</span> You can create a quote manually.</p>
        @endif
        @endif
      </div><!-- aqui -->
    </div>
  </div>



</div>



@if(isset($arreglo))
@if(!$arreglo->isEmpty())
<div class="row padding">
  <div class="col-lg-12"><br><br><span class="col-txt">Results</span><br><br></div>
</div>
<div class="row padding" ><!-- Tabla de muestreo de las cotizaciones -->
  {!! Form::open(['route' => 'quotes-v2.store','class' => 'form-group m-form__group dfw']) !!}
  <input  type="hidden" name="form" value="{{ json_encode($form) }}" class="btn btn-sm btn-default btn-bold btn-upper">
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
                <div class="col-lg-6" align='right'> <button type="submit" class="btn m-btn--pill    btn-info">Quote</button></div>
              </div>

              <div class="row">
                <div class="col-lg-12"><hr></div>
              </div>
              <!-- Empieza columna titulos de la tabla -->

              <div class="row" >
                <div class="col-lg-2" >  <span class="portcss"> Carrier</span></div>
                <div class="col-lg-10">
                  <div class="row">
                    <div class="col-lg-5">
                      <div class="row">
                        <div class="col-lg-8" style="padding-left: 30px;"><span class="portcss">Origin</span></div>
                        <div class="col-lg-3" ><span class="portcss">Destination</span></div>
                      </div>
                    </div>
                    <div class="col-lg-6 padding-right-table" style="padding-left: 0;">
                      <div style="display:flex; justify-content:space-between">
                        <div class="width" style="display:flex; justify-content:center;" {{ $equipmentHides['20'] }} ><span class="portcss">20'</span></div>
                        <div class="width" style="display:flex; justify-content:center;" {{ $equipmentHides['40'] }} ><span class="portcss">40</span></div>
                        <div class="width" style="display:flex; justify-content:center;" {{ $equipmentHides['40hc'] }} ><span class="portcss">40HC'</span></div>
                        <div class="width" style="display:flex; justify-content:center;" {{ $equipmentHides['40nor'] }} ><span class="portcss">40NOR'</span></div>
                        <div class="width" style="display:flex; justify-content:center;" {{ $equipmentHides['45'] }} ><span class="portcss">45'</span></div>
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
              <div class="row" id='principal{{$loop->iteration}}' >
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
                    <div class="col-lg-5 no-padding padding-min-col d-flex justify-content-center">
                      <div class="row">
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
                        <div class="col-lg-4 d-flex align-items-center">
                          <span class="portcss"> {{$arr->port_destiny->name  }}</span><br>
                          <span class="portalphacode"> {{$arr->port_destiny->code  }}</span>
                        </div>
                      </div>
                      <br>
                    </div>

                    <div class="col-lg-6" style="padding-right: 35px;">
                      <div class="table-r__quotes">
                        <div class="width " {{ $equipmentHides['20'] }}><span class="darkblue validate">{{$arr->total20  }} </span><span class="currency"> $USD</span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="darkblue validate">{{$arr->total40  }} </span><span class="currency">$USD </span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="darkblue validate">{{$arr->total40hc  }} </span><span class="currency">$USD </span></div>
                        <div class="width" {{ $equipmentHides['40nor'] }}><span class="darkblue validate">{{$arr->total40nor  }} </span> <span class="currency">$USD </span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="darkblue validate">{{$arr->total45  }} </span><span class="currency">$USD </span></div>
                      </div>
                    </div>
                    <div class="col-lg-1 no-padding d-flex align-items-center pos-btn">
                      <input type="checkbox" id="input-select{{$loop->iteration}}" class="input-select no-check btnrate" rate-id ='{{$arr->id }}' name="info[]" value="{{ json_encode($arr) }}">
                      <label for="input-select{{$loop->iteration}}"  class="btn-input__select btnrate"  rate-id ='{{$arr->id }}'>Select <span class="la la-arrow-right"></span></label>
                    </div>
                    <div class="col-lg-12 b-top no-padding padding-min">
                      <div class="row justify-content-between">
                        <div class="col-lg-2">
                          <div class="btn-detail__quotes">
                            <span class="workblue">Salling Schedule</span>  
                            <a  id='display_l{{$loop->iteration}}' onclick="display({{$loop->iteration}})" class="l"  title="Cancel" ><i  class="la la-angle-down blue"></i></a>
                          </div>
                        </div>
                        @if(isset($arr->contract->remarks))
                        <div class="col-lg-2">
                          <div class="btn-detail__quotes">
                            <span class="workblue">Remarks</span>  
                            <a  id='display_r{{$loop->iteration}}' onclick="display_r({{$loop->iteration}})" class="l"  title="Cancel" ><i  class="la la-angle-down blue"></i></a>
                          </div>
                        </div>
                        @endif
                        <div class="col-lg-6 d-flex align-items-center">
                          <span class="portcss">Validity: {{   \Carbon\Carbon::parse($arr->contract->validity)->format('d M Y') }} - {{   \Carbon\Carbon::parse($arr->contract->expire)->format('d M Y') }}</span>
                        </div>
                        <div class="col-lg-2 no-padding d-flex justify-content-end">
                          <div class="btn-detail__quotes btn-d">
                            <span class="workblue">Detailetd cost</span>  
                            <a  id='display_l{{$loop->iteration}}' onclick="display({{$loop->iteration}})" class="l detailed-cost"  title="Cancel" ><i  class="la la-angle-down blue"></i></a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Termina tarjeta de cotifzacion -->

              <!-- Gastos Origen-->
              @if(!$arr->localorigin->isEmpty())
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
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode">20'</span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode">40'</span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode">40HC'</span></div>
                        <div class="width"  {{ $equipmentHides['40nor'] }}><span class="portalphacode">40NOR'</span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode">45'</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">Currency</span></div>
                  </div><br>
                  @foreach($arr->localorigin as $localorigin)

                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode">{{  str_replace(["[","]","\""], ' ', $localorigin['99']->pluck('surcharge_name')  ) }}</div>
                    <div class="col-lg-2 colorphacode">{{  str_replace(["[","]","\""], ' ', $localorigin['99']->pluck('calculation_name')  ) }}</div>
                    <div class="col-lg-7 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="width"><span class="bg-rates"> {{ isset($localorigin['20']) ?   str_replace(["[","]","\""], ' ', $localorigin['20']->pluck('monto')) : '0.00' }}</span> <span class="bg-rates">+ {{ isset($localorigin['20']) ?   str_replace(["[","]","\""], ' ', $localorigin['20']->pluck('markup')) : '0.00' }}  </span><i class="la la-caret-right arrow-down"></i>  <b class="monto-down">  {{ isset($localorigin['20']) ?   str_replace(["[","]","\""], ' ', $localorigin['20']->pluck('montoMarkup')) : '0.00' }}     </b>      
                        </div>      
                        <div class="width">
                          <span class="bg-rates"> {{ isset($localorigin['40']) ?  str_replace(["[","]","\""], ' ', $localorigin['40']->pluck('monto')) :'0.00' }}</span> <span class="bg-rates">+ {{ isset($localorigin['40']) ?   str_replace(["[","]","\""], ' ', $localorigin['40']->pluck('markup')) : '0.00' }}     </span><i class="la la-caret-right arrow-down"></i>    <b class="monto-down">  {{ isset($localorigin['40']) ?   str_replace(["[","]","\""], ' ', $localorigin['40']->pluck('montoMarkup')) : '0.00' }}            </b>       
                        </div>
                        <div class="width" {{ $equipmentHides['40hc'] }}>  
                          {{ isset($localorigin['40hc']) ?  str_replace(["[","]","\""], ' ', $localorigin['40hc']->pluck('monto')) :'0.00' }} + {{ isset($localorigin['40hc']) ?   str_replace(["[","]","\""], ' ', $localorigin['40hc']->pluck('markup')) : '0.00' }}     <i class="la la-caret-right arrow-down"></i>     <b class="monto-down"> {{ isset($localorigin['40hc']) ?   str_replace(["[","]","\""], ' ', $localorigin['40hc']->pluck('montoMarkup')) : '0.00' }}     </b> 
                        </div>
                        <div class="width" {{ $equipmentHides['40nor'] }}>  
                          <span class="bg-rates"> {{ isset($localorigin['40nor']) ?  str_replace(["[","]","\""], ' ', $localorigin['40nor']->pluck('monto')) :'0.00' }}</span><span class="bg-rates"> + {{ isset($localorigin['40nor']) ?   str_replace(["[","]","\""], ' ', $localorigin['40nor']->pluck('markup')) : '0.00' }} </span>    <i class="la la-caret-right arrow-down"></i>    <b class="monto-down">  {{ isset($localorigin['40nor']) ?   str_replace(["[","]","\""], ' ', $localorigin['40nor']->pluck('montoMarkup')) : '0.00' }}    </b>  
                        </div>
                        <div class="width" {{ $equipmentHides['45'] }}>     
                          <span class="bg-rates"> {{ isset($localorigin['45']) ?  str_replace(["[","]","\""], ' ', $localorigin['45']->pluck('monto')) :'0.00' }}</span> <span class="bg-rates">+ {{ isset($localorigin['45']) ?   str_replace(["[","]","\""], ' ', $localorigin['45']->pluck('markup')) : '0.00' }}     </span><i class="la la-caret-right arrow-down"></i>     <b class="monto-down"> {{ isset($localorigin['45']) ?   str_replace(["[","]","\""], ' ', $localorigin['45']->pluck('montoMarkup')) : '0.00' }}     </b>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode">USD</span></div>

                  </div><br>
                  @endforeach
                  <div class="row bg-light">
                    <div class="col-lg-4 col-lg-offset-" ><span class="portalphacode">Subtotal Origin Charges</span></div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode">{{ $arr->tot20O  }} </span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode">{{ $arr->tot40O  }}</span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode">{{ $arr->tot40hcO  }}</span></div>
                        <div class="width" {{ $equipmentHides['40nor'] }}><span class="portalphacode">{{ $arr->tot40norO  }}</span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode">{{ $arr->tot45O  }}</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">USD</span></div>
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
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode">20'</span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode">40'</span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode">40HC'</span></div>
                        <div class="width"  {{ $equipmentHides['40nor'] }}><span class="portalphacode">40NOR'</span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode">45'</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">Currency</span></div>
                  </div><br>
                  @foreach($arr->rates as $rates)
                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode">{{ $rates['type'] }}</div>
                    <div class="col-lg-2 colorphacode">{{ $rates['detail'] }}</div>
                    <div class="col-lg-7 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}> <span class="bg-rates">{{ @$rates['price20'] }}</span> <span class="bg-rates">+{{ @$rates['markup20'] }}</span> <i class="la la-caret-right arrow-down"></i> <b class="monto-down">{{  @$rates['monto20'] }}</b>  </div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="bg-rates">{{ @$rates['price40'] }}</span> <span class="bg-rates">+{{ @$rates['markup40'] }}</span> <i class="la la-caret-right arrow-down"></i> <b class="monto-down">{{  @$rates['monto40'] }}</b>  </div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="bg-rates">{{ @$rates['price40hc'] }}</span> <span class="bg-rates">+{{ @$rates['markup40hc'] }}</span> <i class="la la-caret-right arrow-down"></i> <b class="monto-down">{{  @$rates['monto40hc'] }}</b>  </div>
                        <div class="width" {{ $equipmentHides['40nor'] }}><span class="bg-rates">{{ @$rates['price40nor'] }}</span> <span class="bg-rates">+{{ @$rates['markup40nor'] }}</span> <i class="la la-caret-right arrow-down"></i> <b class="monto-down">{{  @$rates['monto40nor'] }}</b>  </div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="bg-rates">{{ @$rates['price45'] }}</span> <span class="bg-rates">+{{ @$rates['markup45'] }}</span> <i class="la la-caret-right arrow-down"></i> <b class="monto-down">{{  @$rates['monto45'] }}</b></div>
                      </div>
                    </div>
                    <div class="col-lg-1 colorphacode" >{{$rates['currency_rate']}}</div>
                  </div>

                  @endforeach
                  @foreach($arr->localfreight as $localfreight)

                  <div class="row">
                    <div class="col-lg-2">{{  str_replace(["[","]","\""], ' ', $localfreight['99']->pluck('surcharge_name')  ) }}</div>
                    <div class="col-lg-2">{{  str_replace(["[","]","\""], ' ', $localfreight['99']->pluck('calculation_name')  ) }}</div>
                    <div class="col-lg-1">
                      {{ isset($localfreight['20']) ?   str_replace(["[","]","\""], ' ', $localfreight['20']->pluck('monto')) : '0.00' }}  + {{ isset($localfreight['20']) ?   str_replace(["[","]","\""], ' ', $localfreight['20']->pluck('markup')) : '0.00' }}  <i class="la la-caret-right"></i>    {{ isset($localfreight['20']) ?   str_replace(["[","]","\""], ' ', $localfreight['20']->pluck('montoMarkup')) : '0.00' }}          
                    </div>      
                    <div class="col-lg-1">
                      {{ isset($localfreight['40']) ?  str_replace(["[","]","\""], ' ', $localfreight['40']->pluck('monto')) :'0.00' }} + {{ isset($localfreight['40']) ?   str_replace(["[","]","\""], ' ', $localfreight['40']->pluck('markup')) : '0.00' }}     <i class="la la-caret-right"></i>      {{ isset($localfreight['40']) ?   str_replace(["[","]","\""], ' ', $localfreight['40']->pluck('montoMarkup')) : '0.00' }}                  
                    </div>
                    <div class="col-lg-1" {{ $equipmentHides['40hc'] }}>  
                      {{ isset($localfreight['40hc']) ?  str_replace(["[","]","\""], ' ', $localfreight['40hc']->pluck('monto')) :'0.00' }} + {{ isset($localfreight['40hc']) ?   str_replace(["[","]","\""], ' ', $localfreight['40hc']->pluck('markup')) : '0.00' }}     <i class="la la-caret-right"></i>      {{ isset($localfreight['40hc']) ?   str_replace(["[","]","\""], ' ', $localfreight['40hc']->pluck('montoMarkup')) : '0.00' }}     
                    </div>
                    <div class="col-lg-1" {{ $equipmentHides['40nor'] }}>  
                      {{ isset($localfreight['40nor']) ?  str_replace(["[","]","\""], ' ', $localfreight['40nor']->pluck('monto')) :'0.00' }} + {{ isset($localfreight['40nor']) ?   str_replace(["[","]","\""], ' ', $localfreight['40nor']->pluck('markup')) : '0.00' }}     <i class="la la-caret-right"></i>      {{ isset($localfreight['40nor']) ?   str_replace(["[","]","\""], ' ', $localfreight['40nor']->pluck('montoMarkup')) : '0.00' }}     
                    </div>
                    <div class="col-lg-1" {{ $equipmentHides['45'] }}>     
                      {{ isset($localfreight['45']) ?  str_replace(["[","]","\""], ' ', $localfreight['45']->pluck('monto')) :'0.00' }} + {{ isset($localfreight['45']) ?   str_replace(["[","]","\""], ' ', $localfreight['45']->pluck('markup')) : '0.00' }}     <i class="la la-caret-right"></i>      {{ isset($localfreight['45']) ?   str_replace(["[","]","\""], ' ', $localfreight['45']->pluck('montoMarkup')) : '0.00' }}     
                    </div>
                    <div class="col-lg-1" ><span class="">USD</span></div>
                    <div class="col-lg-1" ></div>
                  </div><br>
                  @endforeach
                  <br>

                  <div class="row bg-light">
                    <div class="col-lg-4 col-lg-offset-" ><span class="portalphacode">Subtotal Freight Charges</span></div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode">{{ $arr->tot20F  }} </span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode">{{ $arr->tot40F  }}</span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode">{{ $arr->tot40hcF  }}</span></div>
                        <div class="width" {{ $equipmentHides['40nor'] }}><span class="portalphacode">{{ $arr->tot40norF  }}</span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode">{{ $arr->tot45F  }}</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">USD</span></div>
                  </div>

                </div>
              </div>

              <!-- Gastos Destino-->
              @if(!$arr->localdestiny->isEmpty())
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
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode">20'</span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode">40'</span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode">40HC'</span></div>
                        <div class="width"  {{ $equipmentHides['40nor'] }}><span class="portalphacode">40NOR'</span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode">45'</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">Currency</span></div>
                  </div><br>
                  @foreach($arr->localdestiny as $localdestiny)

                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode">{{  str_replace(["[","]","\""], ' ', $localdestiny['99']->pluck('surcharge_name')  ) }}</div>
                    <div class="col-lg-2 colorphacode">{{  str_replace(["[","]","\""], ' ', $localdestiny['99']->pluck('calculation_name')  ) }}</div>
                    <div class="col-lg-7 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="width">
                          <span class="bg-rates">   {{ isset($localdestiny['20']) ?   str_replace(["[","]","\""], ' ', $localdestiny['20']->pluck('monto')) : '0.00' }} </span><span class="bg-rates"> + {{ isset($localdestiny['20']) ?   str_replace(["[","]","\""], ' ', $localdestiny['20']->pluck('markup')) : '0.00' }} </span> <i class="la la-caret-right arrow-down"></i>    <b class="monto-down">{{ isset($localdestiny['20']) ?   str_replace(["[","]","\""], ' ', $localdestiny['20']->pluck('montoMarkup')) : '0.00' }}   </b>       
                        </div>      
                        <div class="width">
                          <span class="bg-rates"> {{ isset($localdestiny['40']) ?  str_replace(["[","]","\""], ' ', $localdestiny['40']->pluck('monto')) :'0.00' }}</span><span class="bg-rates"> + {{ isset($localdestiny['40']) ?   str_replace(["[","]","\""], ' ', $localdestiny['40']->pluck('markup')) : '0.00' }} </span>    <i class="la la-caret-right arrow-down"></i>      <b class="monto-down">{{ isset($localdestiny['40']) ?   str_replace(["[","]","\""], ' ', $localdestiny['40']->pluck('montoMarkup')) : '0.00' }}      </b>            
                        </div>
                        <div class="width" {{ $equipmentHides['40hc'] }}>  
                          <span class="bg-rates">   {{ isset($localdestiny['40hc']) ?  str_replace(["[","]","\""], ' ', $localdestiny['40hc']->pluck('monto')) :'0.00' }}</span> <span class="bg-rates"> + {{ isset($localdestiny['40hc']) ?   str_replace(["[","]","\""], ' ', $localdestiny['40hc']->pluck('markup')) : '0.00' }}</span>     <i class="la la-caret-right"></i>      <b class="monto-down">{{ isset($localdestiny['40hc']) ?   str_replace(["[","]","\""], ' ', $localdestiny['40hc']->pluck('montoMarkup')) : '0.00' }}   </b>  
                        </div>
                        <div class="width" {{ $equipmentHides['40nor'] }}>  
                          <span class="bg-rates">  {{ isset($localdestiny['40nor']) ?  str_replace(["[","]","\""], ' ', $localdestiny['40nor']->pluck('monto')) :'0.00' }}</span> <span class="bg-rates"> + {{ isset($localdestiny['40nor']) ?   str_replace(["[","]","\""], ' ', $localdestiny['40nor']->pluck('markup')) : '0.00' }}</span>     <i class="la la-caret-right"></i>      <b class="monto-down">{{ isset($localdestiny['40nor']) ?   str_replace(["[","]","\""], ' ', $localdestiny['40nor']->pluck('montoMarkup')) : '0.00' }}     </b>
                        </div>
                        <div class="width" {{ $equipmentHides['45'] }}>     
                          <span class="bg-rates"> {{ isset($localdestiny['45']) ?  str_replace(["[","]","\""], ' ', $localdestiny['45']->pluck('monto')) :'0.00' }}</span> <span class="bg-rates"> + {{ isset($localdestiny['45']) ?   str_replace(["[","]","\""], ' ', $localdestiny['45']->pluck('markup')) : '0.00' }}    </span> <i class="la la-caret-right"></i>      <b class="monto-down">{{ isset($localdestiny['45']) ?   str_replace(["[","]","\""], ' ', $localdestiny['45']->pluck('montoMarkup')) : '0.00' }}     </b>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode">USD</span></div>
                    <div class="col-lg-1" ></div>
                  </div>
                  @endforeach
                  <br>

                  <div class="row bg-light">
                    <div class="col-lg-4 col-lg-offset-" ><span class="portalphacode">Subtotal Destination Charges</span></div>
                    <div class="col-lg-7">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode">{{ $arr->tot20D  }} </span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode">{{ $arr->tot40D  }}</span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode">{{ $arr->tot40hcD  }}</span></div>
                        <div class="width" {{ $equipmentHides['40nor'] }}><span class="portalphacode">{{ $arr->tot40norD  }}</span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode">{{ $arr->tot45D  }}</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">USD</span></div>
                  </div>
                </div>
              </div>
              @endif
              <!-- Gastos Inlands-->
              @if(!$arr->inlandDestiny->isEmpty() || !$arr->inlandOrigin->isEmpty() )
              <div class="row no-margin margin-card" id='inland{{$loop->iteration}}'  hidden='true' >
                <div class="col-lg-12">
                  <div class="row">
                    <span class="darkblue cabezeras">Inlands</span><br><br>
                  </div>
                  <div class="row bg-light">
                    <div class="col-lg-2"><span class="portalphacode">Provider</span></div>
                    <div class="col-lg-2"><span class="portalphacode">Distance</span></div>
                    <div class="col-lg-6">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode">20'</span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode">40'</span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode">40HC'</span></div>
                        <div class="width"  {{ $equipmentHides['40nor'] }}><span class="portalphacode">40NOR'</span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode">45'</span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">Currency</span></div>
                    <div class="col-lg-1" ><span class="portalphacode"></span></div>
                  </div><br>
                  @if(!$arr->inlandDestiny->isEmpty())
                  <div class="row data-rates">
                    <div class="col-lg-12"> <span class="darkblue">Destiny</span><br><br></div>

                  </div>
                  @endif
                  @foreach($arr->inlandDestiny as $inlandDestiny)

                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode" >{{ $inlandDestiny['providerName']  }}</div>
                    <div class="col-lg-2 colorphacode">{{ $inlandDestiny['km']  }} KM</div>
                    <div class="col-lg-6 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}>{{ $equipmentHides['20'] }}<span class="bg-rates" id ='valor-d20{{$loop->iteration}}-{{$arr->id}}'>{{ @$inlandDestiny['inlandDetails']['i20']['sub_in']  }}</span><i class="la la-caret-right"></i><b class="monto-down">{{ @$inlandDestiny['inlandDetails']['i20']['markup']  }}</b>
                        </div>

                        <div class="width" {{ $equipmentHides['40'] }}>{{ $equipmentHides['40'] }}
                          <span class="bg-rates" id = 'valor-d40{{$loop->iteration}}-{{$arr->id}}' >{{ @$inlandDestiny['inlandDetails']['i40']['sub_in']  }}</span> 
                          <i class="la la-caret-right"></i><b class="monto-down"> {{ @$inlandDestiny['inlandDetails']['i40']['markup']  }} </b>
                        </div>

                        <div class="width" {{ $equipmentHides['40hc'] }}>{{ $equipmentHides['40hc'] }}
                          <span class="bg-rates" id = 'valor-d40h{{$loop->iteration}}-{{$arr->id}}'>{{ @$inlandDestiny['inlandDetails']['i40HC']['sub_in']  }} </span>
                          <i class="la la-caret-right"></i> <b class="monto-down"> {{ @$inlandDestiny['inlandDetails']['i40HC']['markup']  }}    </b>
                        </div>

                        <div class="width"  {{ $equipmentHides['40nor'] }}>N/A</div>
                        <div class="width" {{ $equipmentHides['45'] }}>N/A</div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode">USD</span></div>
                    <div class="col-lg-1 no-padding d-flex align-items-center pos-btn">
                      <input type="checkbox" id="inputID-select{{$loop->iteration}}-{{$arr->id}}" data-inland="{{$loop->iteration}}" data-rate='{{$arr->id}}'   class="input-select inlands no-check " name="inlandD{{$arr->id}}[]" value="{{ json_encode($inlandDestiny) }} ">

                      <label for="inputID-select{{$loop->iteration}}-{{$arr->id}}" data-inland="{{$loop->iteration}}" data-rate='{{$arr->id}}'  class="btn-input__select-add d-flex justify-content-center align-items-center"  >Add</label>

                    </div>
                    <!-- aqui -->
                  </div><br>
                  @endforeach
                  @if(!$arr->inlandOrigin->isEmpty())
                  <div class="row data-rates">
                    <div class="col-lg-12"> <span class="darkblue">Origin</span><br><br></div>

                  </div>
                  @endif
                  @foreach($arr->inlandOrigin as $inlandOrigin)

                  <div class="row data-rates">
                    <div class="col-lg-2 colorphacode" >{{ $inlandOrigin['providerName']  }}</div>
                    <div class="col-lg-2 colorphacode" >{{ $inlandOrigin['km']  }} KM</div>

                    <div class="col-lg-6 colorphacode">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}>{{ $equipmentHides['20'] }}
                          <span class="bg-rates" id ='valor-o20{{$loop->iteration}}-{{$arr->id}}'>{{ @$inlandOrigin['inlandDetails']['i20']['sub_in']  }} </span>
                          <i class="la la-caret-right"></i>     <b class="monto-down"> {{ @$inlandOrigin['inlandDetails']['i20']['markup']  }}      </b>  
                        </div>

                        <div class="width" {{ $equipmentHides['40'] }}>{{ $equipmentHides['40'] }}
                          <span class="bg-rates" id = 'valor-o40{{$loop->iteration}}-{{$arr->id}}'>{{ @$inlandOrigin['inlandDetails']['i40']['sub_in']  }} </span>
                          <i class="la la-caret-right"></i> <b class="monto-down">{{ @$inlandOrigin['inlandDetails']['i40']['markup']  }} </b>
                        </div>

                        <div class="width" {{ $equipmentHides['40hc'] }}>{{ $equipmentHides['40hc'] }}
                          <span class="bg-rates" id = 'valor-o40h{{$loop->iteration}}-{{$arr->id}}'>{{ @$inlandOrigin['inlandDetails']['i40HC']['sub_in']  }} </span>
                          <i class="la la-caret-right"></i>   <b class="monto-down">      {{ @$inlandOrigin['inlandDetails']['i40HC']['markup']  }}   </b>
                        </div>

                        <div class="width"  {{ $equipmentHides['40nor'] }}>N/A</div>
                        <div class="width" {{ $equipmentHides['45'] }}>N/A</div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="colorphacode">USD</span></div>
                    <div class="col-lg-1 no-padding d-flex align-items-center pos-btn">
                      <input type="checkbox" id="inputIO-select{{$loop->iteration}}-{{$arr->id}}" data-inland="{{$loop->iteration}}" data-rate='{{$arr->id}}'   class="input-select inlandsO no-check" name="inlandO{{$arr->id}}[]" value="{{ json_encode($inlandOrigin) }}">

                      <label for="inputIO-select{{$loop->iteration}}-{{$arr->id}}" data-inland="{{$loop->iteration}}" data-rate='{{$arr->id}}'  class="btn-input__select-add d-flex justify-content-center align-items-center"  >Add</label>

                    </div>
                  </div><br>
                  @endforeach
                  <br>

                  <div class="row bg-light">
                    <div class="col-lg-4 col-lg-offset-" ><span class="portalphacode">Subtotal Inlands Charges</span></div>
                    <div class="col-lg-6">
                      <div class="d-flex justify-content-between">
                        <div class="width" {{ $equipmentHides['20'] }}><span class="portalphacode"><div id='sub_inland_20{{ $arr->id }}'>0.00</div> </span></div>
                        <div class="width" {{ $equipmentHides['40'] }}><span class="portalphacode"><div id='sub_inland_40{{ $arr->id }}'>0.00</div></span></div>
                        <div class="width" {{ $equipmentHides['40hc'] }}><span class="portalphacode"><div id='sub_inland_40h{{ $arr->id }}'>0.00</div></span></div>
                        <div class="width" {{ $equipmentHides['40nor'] }}><span class="portalphacode"><div  >N/A</div></span></div>
                        <div class="width" {{ $equipmentHides['45'] }}><span class="portalphacode"><div>N/A</div></span></div>
                      </div>
                    </div>
                    <div class="col-lg-1" ><span class="portalphacode">USD</span></div>
                    <div class="col-lg-1" ><span class="portalphacode"></span></div>
                  </div>

                </div>
              </div>
              @endif

              @if(isset($arr->contract->remarks))
              <div class="row no-margin margin-card" id='remark{{$loop->iteration}}'  hidden='true' >
                <div class="col-lg-12">
                  <div class="row">
                    <span class="darkblue cabezeras">Remarks</span><br><br>
                  </div>
                  <div class="row">
                    <div class="col-lg-6"><span class="monto-down">{{ $arr->contract->remarks }} </span></div>

                  </div>
                </div>

              </div><br>

              @endif


            </div>
            @endforeach
          </div>      
        </div>
      </div>
    </div>

  </div>

  {!! Form::close() !!}
  @endif
  @endif

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
    precargar()
  </script>


  @endif
  <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>
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