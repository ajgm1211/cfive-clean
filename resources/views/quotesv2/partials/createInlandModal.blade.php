<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="createInlandModal" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="min-width: 900px; right: 200px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Create Inland</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'quotes-v2.inlands.store', 'class' => 'form-group m-form__group dfw']) !!}
                <div class="row">
                    <input type="hidden" name="quote_id" value="{{$quote->id}}"
                        class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <input type="hidden" name="quote_type" value="{{$quote->type}}"
                        class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <input type="hidden" name="automatic_rate_id" value=""
                        class="btn btn-sm btn-default btn-bold btn-upper automatic_rate_id">
                    <div class="col-md-4">
                        <label>Type</label>
                        {{ Form::select('type',['Origin'=>'Origin','Destination'=>'Destination'],null,['class'=>'m-select2-general form-control','required'=>'true','placeholder' => 'Select an option','id'=>'inland_type']) }}
                    </div>
                    <div class="col-md-4" class="" id="carrier_label">
                        <label>Detail</label>
                        {!! Form::text('provider', null, ['id' => 'provider','class'=>'form-control','required']) !!}
                    </div>
                    <div class="col-md-4" class="" id="carrier_label">
                        <label>Currency</label>
                        {{ Form::select('currency_id',$currencies,null,['placeholder' => 'Select an option', 'class'=>'form-control m-select2-general','required'=>'true']) }}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <div id="origin_harbor_label">
                            <div class="origin_port hide">
                                <label>Origin Port</label>
                                {{ Form::select('port_id',$rate_origin_ports,null,['class'=>'m-select2-general form-control origin_port_select','placeholder'=>'Select an option']) }}
                            </div>
                            <div class="destination_port hide">
                                <label>Destination Port</label>
                                {{ Form::select('port_id',$rate_destination_ports,null,['class'=>'m-select2-general form-control destination_port_select','placeholder'=>'Select an option']) }}
                            </div>
                        </div>
                        <div id="origin_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                            <label>Airport</label>
                            <select id="origin_airport" name="origin_airport_id" class="form-control"
                                {{$quote->type=='AIR' ? 'required':''}}></select>
                        </div>
                    </div>
                    <div class="input-group date">
                        {!! Form::hidden('date',null, ['id' => 'm_daterangepicker_1' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off','required'=>'true']) !!}
                        {!! Form::hidden('date_hidden', null, ['id' => 'date_hidden','hidden'  => 'true']) !!}
                    </div>
                </div>
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" data-toggle="modal" data-target="#createInlandModal"
                        class="btn btn-danger">Cancel</button>
                </div>
                <br>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>