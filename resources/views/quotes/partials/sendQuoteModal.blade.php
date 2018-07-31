<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="SendQuoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Send Quote</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group text-center" id="spin" style="display: none;">
                    <b>Sending</b> &nbsp;<i class="fa fa-spinner fa-spin"></i>
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Email template</b></label>
                    {{ Form::select('email_template_id',$email_templates,null,['placeholder' => 'Please choose a template','class'=>'custom-select form-control','id' => 'email_template']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Preview:</b></label>
                    <div class="jumbotron">
                        <div id="preview">

                        </div>
                    </div>
                    <div id="btn_area">
                        
                    </div>
                </div>
                <hr>
                <div class="form-group m-form__group">
                    <button id="send-pdf-quote" class="btn btn-success">Send</button>
                    <button data-toggle="modal" data-target="#SendQuoteModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>