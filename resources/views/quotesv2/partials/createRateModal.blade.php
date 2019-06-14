<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="createRateModal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content" style="min-width: 900px; right: 200px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>New Rate</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'quotes-v2.rates.store', 'class' => 'form-group m-form__group dfw']) !!}
                <div class="row">
                    <input  type="hidden" name="quote_id" value="{{$quote->id}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <div class="col-md-4" >
                        <div id="origin_harbor_label" {{$quote->type=='AIR' ? 'hidden':''}}>
                          <label>Origin port</label>
                          {{ Form::select('originport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor',$quote->type!='AIR' ? 'required':'']) }}
                        </div>
                        <div id="origin_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                          <label>Origin airport</label>
                          <select id="origin_airport" name="origin_airport_id" class="form-control" {{$quote->type=='AIR' ? 'required':''}}></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div  id="destination_harbor_label" {{$quote->type=='AIR' ? 'hidden':''}}>
                          <label>Destination port</label>
                          {{ Form::select('destinyport[]',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor',$quote->type!='AIR' ? 'required':'']) }}
                        </div>
                        <div id="destination_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                          <label>Destination airport</label>
                          <select id="destination_airport" name="destination_airport_id" class="form-control" {{$quote->type=='AIR' ? 'required':''}}></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Date</label>
                        <div class="input-group date">
                            {!! Form::text('date',null, ['id' => 'm_daterangepicker_1' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off']) !!}
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
                    <div class="col-md-4" class="" id="carrier_label" {{$quote->type=='AIR' ? 'hidden':''}}> 
                        <label>Carrier</label>
                        {{ Form::select('carrieManual',$carrierMan,null,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-general','id'=>'carrieManual',$quote->type!='AIR' ? 'required':'']) }}
                    </div>
                    <div class="col-md-4" id="airline_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                        <label>Airline</label>
                        <div class="form-group">
                          {{ Form::select('airline_id',$airlines,null,['class'=>'custom-select form-control','id' => 'airline_id','placeholder'=>'Choose an option',$quote->type=='AIR' ? 'required':'']) }}
                      </div>
                    </div>
                    <div class="col-md-4" class="" > 
                        <label>Schedule type</label>
                        {{ Form::select('schedule_type',['Direct'=>'Direct','Transfer'=>'Transfer'],null,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-edit',$quote->type!='AIR' ? 'required':'']) }}
                    </div>
                    <div class="col-md-4">
                        <label>Transit time</label>
                        <input type="number" name="transit_time" value="" class="form-control">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4"> 
                        <label>Via</label>
                        <input type="text" name="via" value="" class="form-control">
                    </div>
                </div>                
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button data-toggle="modal" data-target="#createRateModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>