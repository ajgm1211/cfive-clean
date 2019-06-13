{{ Form::model($rate, array('route' => array('quotes-v2.rates.update', $rate->id), 'method' => 'POST')) }}
<div class="row">
    <div class="col-md-4" >
        <div id="origin_harbor_label" {{$quote->type=='AIR' ? 'hidden':''}}>
          <label>Origin port</label>
          {{ Form::select('origin_port_id',$harbors,$rate->origin_port_id,['class'=>'m-select2-edit form-control origin_port_id',$quote->type!='AIR' ? 'required':'']) }}
        </div>
        <div id="origin_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
          <label>Origin airport</label>
          <select id="origin_airport" name="origin_airport_id" class="form-control m-select2-edit" {{$quote->type=='AIR' ? 'required':''}}></select>
        </div>
    </div>
    <div class="col-md-4">
        <div  id="destination_harbor_label" {{$quote->type=='AIR' ? 'hidden':''}}>
          <label>Destination port</label>
          {{ Form::select('destination_port_id',$harbors,$rate->destination_port_id,['class'=>'m-select2-edit form-control destination_port_id',$quote->type!='AIR' ? 'required':'']) }}
        </div>
        <div id="destination_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
          <label>Destination airport</label>
          <select id="destination_airport" name="destination_airport_id" class="form-control m-select2-edit" {{$quote->type=='AIR' ? 'required':''}}></select>
        </div>
    </div>
    <div class="col-md-4" id="delivery_type_label" {{$quote->type=='AIR' ? 'hidden':''}}>
        <label>Delivery type</label>
        {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-edit delivery_type form-control','disabled'=>'true']) }}
    </div>
    <div class="col-md-4" id="delivery_type_air_label" {{$quote->type!='AIR' ? 'hidden':''}}>
        <label>Delivery type</label>
        {{ Form::select('delivery_type_air',['5' => 'AIRPORT(Origin) To AIRPORT(Destination)','6' => 'AIRPORT(Origin) To DOOR(Destination)','7'=>'DOOR(Origin) To AIRPORT(Destination)','8'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-edit form-control','disabled'=>'true']) }}
    </div>                 
</div>
<br>
<div class="row">
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
    <div class="col-md-4" class="" > 
        <label>Schedule type</label>
        {{ Form::select('schedule_type',['Direct'=>'Direct','Transfer'=>'Transfer'],$rate->schedule_type,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-edit schedule_type',$quote->type!='AIR' ? 'required':'']) }}
    </div>
    <div class="col-md-4">
        <label>Transit time</label>
        <input type="number" name="transit_time" value="{{$rate->transit_time}}" class="form-control transit_time">
    </div>
</div>
<br>
<div class="row">
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
<script type="text/javascript">
    $('.m-select2-edit').select2({
      placeholder: "Select an option"
  });
</script>