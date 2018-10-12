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
                    {!! Form::open(['route' => 'quotes.listRate','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed','onsubmit' => 'setdateinput()']) !!}
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
                                                                        45':
                                                                    </label>
                                                                    <div class="m-bootstrap-touchspin-brand">
                                                                        {!! Form::text('fortyfive', 0, ['id' => 'm_touchspin_2_1' ,'class' => 'col-lg-12 form-control']) !!}

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
                                                                        {!! Form::text('date', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off']) !!}
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
