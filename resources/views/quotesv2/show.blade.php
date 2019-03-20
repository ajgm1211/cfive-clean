@extends('layouts.app')
@section('css')
  @parent
  <link href="/css/quote.css" rel="stylesheet" type="text/css" />

  <link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Quotes')
@section('content')
  <br>
  <div class="m-content">
  <div class="row">
    <div class="col-md-10 offset-md-1">
      <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right" role="tablist" style="border-bottom: none;">
        <li class="nav-item m-tabs__item" >
          <a class="btn btn-primary-v2" id="create-quote-back" data-toggle="tab" href="#" role="tab">
            Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
          </a>
        </li>
        <li class="nav-item m-tabs__item" >
          <a class="btn btn-primary-v2" id="create-quote-back" data-toggle="tab" href="#" role="tab">
            PDF
          </a>
        </li>
        <li class="nav-item m-tabs__item" >
          <a class="btn btn-primary-v2" id="create-quote-back" data-toggle="tab" href="#" role="tab">
            Duplicate
          </a>
        </li>
      </ul>
    </div>
    <div class="col-md-10 offset-md-1">
      <div class="m-portlet">
        <div class="m-portlet__head">
          <div class="row" style="padding-top: 20px;">
            <h3 class="title-quote size-16px">Quote info</h3>
          </div>
          <div class="m-portlet__head-tools">
            <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
              <li class="nav-item m-tabs__item" >
                <a class="btn btn-primary-v2" id="create-quote-back" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                  <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Edit
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="m-portlet__body">
          <div class="tab-content">
            <div class="row">
              <div class="col-md-4">
                <label class="title-quote"><b>Quotation ID:&nbsp;&nbsp;</b></label>
                <span id="quote_id_span" onclick="display_quote_id()" >{{$quote->quote_id}}</span>
                <input type="text" class="" id="quote_id_input" value="{{$quote->quote_id}}" hidden>
                <a  id='save_quote_id' onclick="save_quote_id({{$quote->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                  <i class="la la-save"></i>
                </a>
                <a  id='cancel_quote_id' onclick="cancel_quote_id()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                  <i  class="la la-reply"></i>
                </a>
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                <a href="#" id="type" data-type="select" data-pk="1" data-url="/post" data-title="Select status"></a>

              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Company:&nbsp;&nbsp;</b></label>
                {{$quote->company->business_name}}
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <label class="title-quote"><b>Status:&nbsp;&nbsp;</b></label>
                <span class="Status_{{$quote->status}}" style="border-radius: 10px;">{{$quote->status}} <i class="fa fa-check"></i></span>
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Destination type:&nbsp;&nbsp;</b></label>
                {{$quote->delivery_type}}
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Contact:&nbsp;&nbsp;</b></label>
                {{$quote->contact->first_name}} {{$quote->contact->last_name}}
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <label class="title-quote"><b>Date issued:&nbsp;&nbsp;</b></label>
                {{date_format($quote->created_at, 'd M Y')}}
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Equipment:&nbsp;&nbsp;</b></label>

              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Price level:&nbsp;&nbsp;</b></label>
                {{$quote->price->name}}
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <label class="title-quote"><b>Validity:&nbsp;&nbsp;</b></label>
                {{$quote->quote_validity}}
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Incoterm:&nbsp;&nbsp;</b></label>
                {{$quote->incoterm->name}}
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                {{$quote->user->name}} {{$quote->user->lastname}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-10 offset-md-1">
      <div class="m-portlet">
        <div class="m-portlet__body">
          <div class="tab-content">

          </div>
        </div>
      </div>
    </div>

  </div>
  </div>

@endsection

@section('js')
  @parent
  <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
  <script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>
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