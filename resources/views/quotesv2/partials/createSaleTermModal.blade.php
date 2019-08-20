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
                            {{ Form::select('type',['origin'=>'Origin','destination'=>'Destination'],null,['placeholder'=>'Select an option','class'=>'m-select2-general form-control']) }}
                        </div>
                    </div>
                    <div class="col-md-4" >
                        <div id="origin_harbor_label" {{$quote->type=='AIR' ? 'hidden':''}}>
                            <label>Port</label>
                            {{ Form::select('port_id',$harbors,null,['class'=>'m-select2-general form-control','id'=>'origin_harbor','placeholder'=>'Select an option']) }}
                        </div>
                        <div id="origin_airport_label" {{$quote->type!='AIR' ? 'hidden':''}}>
                            <label>Airport</label>
                            <select id="origin_airport_create" name="airport_id" class="form-control"></select>
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
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button data-toggle="modal" data-target="#createSaleTermModal" class="btn btn-danger" type="button">Cancel</button>
                </div>
                <br>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>