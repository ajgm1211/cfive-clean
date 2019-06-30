{{ Form::model($rate, array('route' => array('quotes-v2.rates.update', $rate->id), 'method' => 'POST')) }}
<div class="row">
    <div class="col-md-4" >
      <div id="origin_harbor_label" {{$quote->type=='AIR' ? 'hidden':''}}>
        <label>Origin port</label>
        {{ Form::select('origin_port_id',$harbors,$rate->origin_port_id,['class'=>'m-select2-edit form-control origin_port_id',$quote->type!='AIR' ? 'required':'']) }}
      </div>
      <div id="origin_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
        <label>Origin airport</label>
        {{ Form::select('origin_airport_id',[@$rate->origin_airport_id=>@$rate->origin_airport->display_name],@$rate->origin_airport_id,['class'=>'form-control','id'=>'origin_airport_edit',$quote->type=='AIR' ? 'required':'']) }}
      </div>
    </div>
    <div class="col-md-4">
      <div  id="destination_harbor_label" {{$quote->type=='AIR' ? 'hidden':''}}>
        <label>Destination port</label>
        {{ Form::select('destination_port_id',$harbors,$rate->destination_port_id,['class'=>'m-select2-edit form-control destination_port_id',$quote->type!='AIR' ? 'required':'']) }}
      </div>
      <div id="destination_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
        <label>Destination airport</label>
        {{ Form::select('destination_airport_id',[@$rate->destination_airport_id=>@$rate->destination_airport->display_name],@$rate->destination_airport_id,['class'=>'form-control','id'=>'destination_airport_edit',$quote->type=='AIR' ? 'required':'']) }}
      </div>
    </div>
    <div class="col-md-4" class="" id="carrier_label" {{$quote->type=='AIR' ? 'hidden':''}}> 
        <label>Carrier</label>
        {{ Form::select('carrier_id',$carriers,$rate->carrier_id,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-edit carrier_id',$quote->type!='AIR' ? 'required':'']) }}
    </div>
    <div class="col-md-4" id="airline_label" {{$quote->type!='AIR' ? 'hidden':''}}>
        <label>Airline</label>
        <div class="form-group">
          {{ Form::select('airline_id',$airlines,null,['class'=>'custom-select form-control m-select2-edit ','id' => 'airline_id','placeholder'=>'Choose an option',$quote->type=='AIR' ? 'required':'']) }}
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4" class="" > 
        <label>Schedule type</label>
        {{ Form::select('schedule_type',[' '=>'Select an option','Direct'=>'Direct','Transfer'=>'Transfer'],$rate->schedule_type,['class'=>'form-control m-select2-edit schedule_type']) }}
    </div>
    <div class="col-md-4">
        <label>Transit time</label>
        <input type="number" name="transit_time" value="{{$rate->transit_time}}" class="form-control transit_time">
    </div>
    <div class="col-md-4"> 
        <label>Via</label>
        <input type="text" name="via" value="{{$rate->via}}" class="form-control via">
    </div>
</div>
<hr>
<div class="form-group m-form__group">
    <button type="submit" class="btn btn-primary">Update</button>
</div>
<br>
{!! Form::close() !!}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>
<script type="text/javascript">

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

    $('#origin_airport_edit').select2({
      dropdownParent: $('#editRateModal'),
      placeholder: "Select an option",
      minimumInputLength: 2,
      ajax: {
        url: '/quotes/airports/find',
        dataType: 'json',
        data: function (params) {
          return {
            q: $.trim(params.term)
          };
        },
        processResults: function (data) {
          return {
            results: data
          };
        },
      }
    });

    $('#destination_airport_edit').select2({
      dropdownParent: $('#editRateModal'),
      placeholder: "Select an option",
      minimumInputLength: 2,
      ajax: {
        url: '/quotes/airports/find',
        dataType: 'json',
        data: function (params) {
          return {
            q: $.trim(params.term)
          };
        },
        processResults: function (data) {
          return {
            results: data
          };
        },
      }
    });

    $('.m-select2-edit').select2({
        placeholder: "Select an option"
    }); 
</script>