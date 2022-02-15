<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 04/06/2018
 * Time: 05:45 PM
 */
?>
<div class="modal fade" id="SendQuoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content" style="min-width: 700px; right: 100px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Send Quote</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>To</b></label>
                    {{ Form::text('addresse',@$quote->contact->email,['placeholder' => 'Please choose a addresse','class'=>'form-control','id'=>'addresse']) }}
                    <br>
                    <h6><label><small>To send to several recipients, separate them with a semicolon (;)</small></label></h6>
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Email template</b></label>
                    {{ Form::select('email_template_id',$email_templates,null,['placeholder' => 'Please choose a template','class'=>'custom-select form-control','id' => 'email_template']) }}
                </div>
               <input type="hidden" id="emaildimanicdata" value="{{@$emaildimanicdata}}"/>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Preview:</b></label>
                    <div class="jumbotron">
                        <div id="subject-box">                            
                            
                        </div>
                        <div id="textarea-box" style="display: none;">
                            <label><b>Body:</b></label>
                            <br>
                            <textarea class="form-control editor" id="email-body"></textarea>
                        </div>
                    </div>
                    <div id="btn_area">
                        
                    </div>
                </div>
                <hr>
                <div class="form-group m-form__group">
                    @if($quote->type=='FCL')
                        <button id="send-pdf-quotev2" class="btn btn-success">Send</button>
                    @elseif($quote->type=='LCL')
                        <button id="send-pdf-quotev2-lcl-air" class="btn btn-success">Send</button>
                    @else
                        <button id="send-pdf-quotev2-air" class="btn btn-success">Send</button>
                    @endif
                    <button id="send-pdf-quote-sending" class="btn btn-success" style="display:none" disabled><i class="fa fa-spinner fa-spin"></i> &nbsp; Sending</button>
                    <button data-toggle="modal" data-target="#SendQuoteModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>