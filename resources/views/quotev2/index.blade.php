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
      <div class="m-portlet__body">
        <div class="tab-content">
          <div>
            <div class="row">
              <div class="col-md-2">
                <label>Quote Type</label>
                {{ Form::select('modality',['1' => 'Export','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}
              </div>
              <div class="col-md-2">
                <label>Incoterm</label>
                {{ Form::select('incoterm',$incoterm,null,['class'=>'m-select2-general form-control','required'=>'true']) }}
              </div>

              <div class="col-md-2">
                <label>Equipment</label>
                {{ Form::select('equipment',['1' => '20\'','2' => '40','3'=>'40HC','4'=>'45'],null,['class'=>'m-select2-general form-control','id'=>'equipment']) }}
              </div>
              <div class="col-md-2">
                <label>Company</label>
                {{ Form::select('company_id_quote', $companies,null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal','required'=>'true']) }} 
              </div>
              <div class="col-md-2">
                <label>Contact</label>
                {{ Form::select('contact_id',[],null,['class'=>'m-select2-general form-control','required'=>'true']) }}
              </div>
              <div class="col-md-2">
                <label>Price level</label>
                {{ Form::select('price_id',[],null,['class'=>'m-select2-general form-control']) }}
              </div>
            </div><br>
            <div class="row">
              <div class="col-md-2" id="origin_harbor_label">
                <label>Origin port</label>
                {{ Form::select('originport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor','required' => 'true']) }}
              </div>
              <div class="col-md-2" id="destination_harbor_label">
                <label>Destination port</label>
                {{ Form::select('destinyport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor','required' => 'true']) }}
              </div>
              <div class="col-md-2">
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
              <div class="col-md-2">
                <label>Delivery type</label>
                {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
              </div>
              <div class="col-md-2 hide" id="origin_address_label">
                <label>Origin address</label>
                {!! Form::text('origin_address', '', ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
              </div>
              <div class="col-md-2 hide" id="destination_address_label">
                <label>Destination address</label>
                {!! Form::text('destination_address', '', ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
              </div>

            </div>
            <br>
            <div class ="row">  <div class="col-md-12"> <center><button type="button" class="btn m-btn--pill    btn-info">Search</button></center> </div>  </div>

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
          <div>
            <div class="row">
              <div class="col-md-6">
                <label>Sort By</label>
                <div class="input-group m-input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="la la-exclamation-triangle"></i></span>
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
              <div class="col-md-6" align='right'><button  class="btn-large" disabled='true'> Quote
               
                </button></div>
            </div><br><br>
            <div class="row">
              <div class="col-md-2">Carrier</div>
              <div class="col-md-2">Origin</div>
              <div class="col-md-1">Destination</div>
              <div class="col-md-2">Validity</div>
              <div class="col-md-1">20'</div>
              <div class="col-md-1">40</div>
              <div class="col-md-1">40HC'</div>
              <div class="col-md-1">40NOR'</div>
              <div class="col-md-1">45'</div>
            </div>
          </div>

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
