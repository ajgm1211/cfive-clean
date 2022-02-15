<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="createSaleTermModal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content" style="min-width: 900px; right: 200px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>New Sale Term</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'quotes-v2.saleterm.store', 'class' => 'form-group m-form__group dfw']) !!}
                <div class="row">
                    <input  type="hidden" name="quote_id" value="{{$quote->id}}" class="btn btn-sm btn-default btn-bold btn-upper formu">
                    <div class="col-md-4">
                        <label>Type</label>
                        <div class="input-group date">
                            {{ Form::select('type',['origin'=>'Origin','destination'=>'Destination'],null,['placeholder'=>'Select an option','class'=>'m-select2-general form-control','id'=>'saleterm_type']) }}
                        </div>
                    </div>
                    <div class="col-md-4" >
                        <div {{$quote->type=='AIR' ? 'hidden':''}}>
                            <div class="origin_port hide">
                                <label>Origin Port</label>
                                {{ Form::select('port_id',$rate_origin_ports,null,['class'=>'m-select2-general form-control origin_port_select','placeholder'=>'Select an option','id'=>'origin_port_select']) }}
                            </div>                        
                            <div class="destination_port hide">
                                <label>Destination Port</label>
                                {{ Form::select('port_id',$rate_destination_ports,null,['class'=>'m-select2-general form-control destination_port_select','placeholder'=>'Select an option','id'=>'destination_port_select']) }}
                            </div>
                        </div>
                        <div {{$quote->type!='AIR' ? 'hidden':''}}>
                            <div class="origin_airport hide">
                                <label>Origin Airport</label>
                                {{ Form::select('airport_id',$rate_origin_airports,null,['class'=>'m-select2-general form-control origin_airport_select','placeholder'=>'Select an option','id'=>'origin_airport_select']) }}
                            </div>                        
                            <div class="destination_airport hide">
                                <label>Destination Airport</label>
                                {{ Form::select('airport_id',$rate_destination_airports,null,['class'=>'m-select2-general form-control destination_airport_select','placeholder'=>'Select an option','id'=>'destination_airport_select']) }}
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-md-4" class="" id="carrier_label" {{$quote->type=='AIR' ? 'hidden':''}}> 
                        <label>Carrier</label>
                        {{ Form::select('carrieManual',$carrierMan,null,['placeholder' => 'Select at option', 'class'=>'form-control m-select2-general','id'=>'carrieManual']) }}
                    </div>
                    <div class="col-md-4" id="airline_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                        <label>Airline</label>
                        <div class="form-group">
                            {{ Form::select('airline_id',$airlines,null,['class'=>'m-select2-general form-control','id' => 'airline_id','placeholder'=>'Choose an option']) }}
                        </div>
                    </div>-->
                </div>
                <br>                
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary" id="saveSaleTerm">Save</button>
                    <button data-toggle="modal" data-target="#createSaleTermModal" class="btn btn-danger" type="button">Cancel</button>
                </div>
                <br>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>