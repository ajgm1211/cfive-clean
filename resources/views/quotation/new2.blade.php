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
    <div class="">
      <div class="row">
        <div class="col-xl-12">
          {!! Form::open(['route' => 'quotes.listRate','id' =>'formId', 'class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed','onsubmit' => 'setdateinput()']) !!}

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
                            <div class="col-lg-7">
                              <div class='row'>
                                <div class="col-md-4">
                                  <label class="m-option">
                                    <span class="m-option__control">
                                      <span class="m-radio m-radio--brand m-radio--check-bold">
                                        <input name="type" class='fcl_label'  checked='true' value="1" id="fcl_type"  required='true' type="radio">
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
                                        <input name="type"  class='lcl_label' value="2" id="lcl_type" type="radio">
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

                              </div>
                              <br>

                            </div>
                          </div>
                          <div class="form-group m-form__group row">

                            <div class="form-group m-form__group row" id="fcl_load">
                              <div class="col-lg-2">
                                <label>
                                  <b>LOAD</b>
                                </label>
                              </div>
                              <div class="col-lg-8">
                                <div class='row'>
                                  <div class="col-md-3">
                                    <label>
                                      20' :
                                    </label>
                                    <div class="m-bootstrap-touchspin-brand">
                                      {!! Form::text('twuenty', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <label>
                                      40' :
                                    </label>
                                    <div class="m-bootstrap-touchspin-brand">
                                      {!! Form::text('forty', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <label>
                                      40' HC :
                                    </label>
                                    <div class="m-bootstrap-touchspin-brand">
                                      {!! Form::text('fortyhc', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}

                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <label>
                                      40' NOR :
                                    </label>
                                    <div class="m-bootstrap-touchspin-brand">
                                      {!! Form::text('fortynor', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}

                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <label>
                                      <br>  45':
                                    </label>
                                    <div class="m-bootstrap-touchspin-brand">
                                      {!! Form::text('fortyfive', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}

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
                                  {{ Form::select('incoterm',$incoterm,null,['class'=>'m-select2-general form-control','required'=>'true']) }}
                                </div>
                                <div class="col-md-5">
                                  <label>Delivery type</label>
                                  {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
                                </div>
                                <div class="col-md-3">
                                  <label>Pick up date</label>
                                  <div class="input-group date">
                                    {!! Form::text('date', null, ['id' => 'picker' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off']) !!}
                                    {!! Form::text('date_hidden', null, ['id' => 'date_hidden','hidden'  => 'true']) !!}

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
                                <div class="col-md-8 hide" id="origin_address_label">
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
                                <div class="col-md-8 hide" id="destination_address_label">
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
                                    {{ Form::select('company_id_quote', $companies,null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal','required'=>'true']) }}<br><br>
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

@include('contacts.partials.contactsModal')
@include('companies.partials.companiesModal')

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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>
<script>


  $('#picker').daterangepicker({
    singleDatePicker: true,

    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary'
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
    $('#picker').val($valor);
  }
  function setdateinput(){
    var date = $('#picker').val();
    $('#date_hidden').val(date);
  }

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
