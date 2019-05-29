<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="createRateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content" style="min-width: 700px; right: 100px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>New Rate</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label>Quote Type</label>
                        {{ Form::select('type',['1' => 'FCL','2' => 'LCL','3'=>'AIR'],null,['id'=>'quoteType','class'=>'m-select2-general form-control']) }}
                    </div>
                    <div class="col-lg-3" id="equipment_id">
                        <label>Equipment</label>
                        {{ Form::select('equipment[]',['20' => '20\'','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],@$form['equipment'],['class'=>'m-select2-general form-control','id'=>'equipment','multiple' => 'multiple','required' => 'true']) }}
                    </div>
                    <div class="col-lg-3">
                        <label>Type</label>
                        {{ Form::select('mode',['1' => 'Export','2' => 'Import','3'=>'All'],@$form['mode'],['id'=>'mode','placeholder'=>'Select','class'=>'m-select2-general form-control','required' => 'true']) }}
                    </div>
                </div>
                <hr>
                <div class="form-group m-form__group">
                    <button id="send-pdf-quotev2" class="btn btn-primary">Save</button>
                    <button id="send-pdf-quote-sending" class="btn btn-success" style="display:none" disabled><i class="fa fa-spinner fa-spin"></i> &nbsp; Sending</button>
                    <button data-toggle="modal" data-target="#createRateModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>