<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="createInlandModal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content" style="min-width: 900px; right: 200px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>New Inland</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'quotes-v2.inlands.store', 'class' => 'form-group m-form__group dfw']) !!}
                <div class="row">
                    <input  type="hidden" name="quote_id" value="{{$quote->id}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <input  type="hidden" name="quote_type" value="{{$quote->type}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <input  type="hidden" name="automatic_rate_id" value="" class="btn btn-sm btn-default btn-bold btn-upper automatic_rate_id">
                    <div class="col-md-4" >
                        <div id="origin_harbor_label">
                          <label>Port</label>
                          {{ Form::select('port_id',$harbors,null,['class'=>'m-select2-general form-control','required'=>'true','placeholder' => 'Select at option',]) }}
                        </div>
                        <div id="origin_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                          <label>Airport</label>
                          <select id="origin_airport" name="origin_airport_id" class="form-control" {{$quote->type=='AIR' ? 'required':''}}></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="destination_harbor_label" >
                          <label>Type</label>
                          {{ Form::select('type',['Origin'=>'Origin','Destination'=>'Destination'],null,['class'=>'m-select2-general form-control','required'=>'true','placeholder' => 'Select at option']) }}
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div id="destination_harbor_label" >
                          <label>Provider</label>
                          <input  type="text" name="provider" value="" class="form-control" required>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <label>Date</label>
                        <div class="input-group date">
                            {!! Form::text('date',null, ['id' => 'm_daterangepicker_1' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off','required'=>'true']) !!}
                            {!! Form::text('date_hidden', null, ['id' => 'date_hidden','hidden'  => 'true']) !!}
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" class="" id="carrier_label"> 
                        <label>Currency</label>
                        {{ Form::select('currency_id',$currencies,null,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-general','required'=>'true']) }}
                    </div>
                </div>
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" data-toggle="modal" data-target="#createInlandModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>