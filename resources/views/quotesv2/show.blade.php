@extends('layouts.app')
@section('css')
  @parent
  <link href="/css/quote.css" rel="stylesheet" type="text/css" />

  <link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Quotes')
@section('content')
  <br>
  <div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <div class="m-portlet">
        <div class="m-portlet__head">
          <div class="row" style="padding-top: 20px;">
            <h3 class="title-quote size-16px">Quote info</h3>
          </div>
        </div>
        <div class="m-portlet__body">
          <div class="tab-content">
            <div class="row">
              <div class="col-md-4">
                <label class="title-quote"><b>Quotation ID:&nbsp;&nbsp;</b></label>
                {{$quote->quote_id}}
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                {{$quote->type}}
              </div>
              <div class="col-md-4">
                <label class="title-quote"><b>Company:&nbsp;&nbsp;</b></label>
                {{$quote->company->business_name}}
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <label class="title-quote"><b>Status:&nbsp;&nbsp;</b></label>
                {{$quote->status}}
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
    <div class="col-md-1"></div>

  </div>

  <div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <div class="m-portlet">

        <div class="m-portlet__body">



          <div class="tab-content">


          </div>
        </div>
      </div>
    </div>
    <div class="col-md-1"></div>

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