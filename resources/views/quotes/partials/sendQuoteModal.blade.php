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
                    Send Quote
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group text-center" id="spin" style="display: none;">
                    <b>Enviando</b> &nbsp;<i class="fa fa-spinner fa-spin"></i>
                </div>
                <div class="form-group m-form__group">
                    {!! Form::label('email', 'Email') !!}
                    {!! Form::text('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input','required' => 'required','id'=>'quote_email']) !!}
                </div>
                <div class="form-group m-form__group">
                    <button id="send-pdf-quote" data-toggle="modal" data-target="#myModal" class="btn btn-info btn-block">Send</button>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>