<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 29/05/2018
 * Time: 10:50 PM
 */
?>
<style>
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
        color: #006BFA !important;
        background-color: #F4F3F8 !important;
        border-left: 2px solid #006BFA !important;
        border-bottom: none !important;
        border-radius: 0;
    }
    .files input {
        outline: 2px dashed #92b0b3;
        outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear;
        padding: 50px 0px 50px 20px;
        text-align: center !important;
        margin: 0;
        width: 100% !important;
    }
    .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
        -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
        transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
    }
    .files{ position:relative}
    .files:after {  pointer-events: none;
        position: absolute;
        top: 60px;
        left: 0;
        width: 50px;
        right: 0;
        height: 56px;
        content: "";
        //background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);
        display: block;
        margin: 0 auto;
        background-size: 100%;
        background-repeat: no-repeat;
    }
    .color input{ background-color:#f1f1f1;}
    .files:before {
        position: absolute;
        bottom: 10px;
        left: 0;  pointer-events: none;
        width: 100%;
        right: 0;
        height: 57px;
        content: " ";
        display: block;
        margin: 0 auto;
        color: #2ea591;
        font-weight: 600;
        text-transform: capitalize;
        text-align: center;
    }
</style>
@extends('layouts.app')
@section('title', 'Settings')
@section('content')

<div class="container m-content">
    <div class="dropdown show" align="right" style="margin:20px;">
        <a class="dropdown-toggle" style="font-size:16px" href="#" role="button" id="helpOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            See how it works
        </a>

        <div 
            class="dropdown-menu" 
            aria-labelledby="helpOptions"
        >
            <a class="dropdown-item" target="_blank" href="https://support.cargofive.com/how-to-setup-your-freight-forwarder-on-cargofive/"> 
                How to setup your freight forwarder
            </a>
            <a class="dropdown-item" target="_blank" href="https://support.cargofive.com/how-to-modify-the-main-currency/"> 
                How to modify your main currency
            </a>
        </div>
    </div>
    <div class="m-portlet m-portlet--mobile" style="border-radius:10px;">
        <br>
        <br>
        <div class="container m-portlet__body">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <ul class="nav nav-tabs flex-column" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link color-black" aria-controls="home" aria-selected="true"><h6><b>Company profile</b></h6></a>
                        </li>
                        @if(\Auth::user()->type=='admin' || \Auth::user()->type=='company')
                            <li class="nav-item">
                                <a class="nav-link {{\Auth::user()->type!='subuser' ? 'active':''}}" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="home" aria-selected="true"><i class="fa fa-home"></i>&nbsp;Company info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pdf-tab" data-toggle="tab" href="#pdf" role="tab" aria-controls="profile" aria-selected="false"><i class="fa fa-edit"></i> &nbsp;PDF Settings</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{\Auth::user()->type=='subuser' ? 'active':''}}" id="emails-tab" data-toggle="tab" href="#emails" role="tab" aria-controls="profile" aria-selected="false"><i class="fa fa-envelope"></i> &nbsp;Emails</a>
                        </li>
                        @if(\Auth::user()->type=='admin' || \Auth::user()->type=='company')
                            <li class="nav-item">
                                <a class="nav-link {{\Auth::user()->type=='subuser' ? 'active':''}}" id="delegations-tab" data-toggle="tab" href="#delegation" role="tab" aria-controls="profile" aria-selected="false"><i class="  fa fa-sitemap"></i> &nbsp;Delegations</a>
                                <!-- <a class="nav-link {{\Auth::user()->type=='subuser' ? 'active':''}}" id="delegations-tab" data-toggle="tab" href="#delegation" role="tab" aria-controls="profile" aria-selected="false"><i class="  fa fa-users"></i> &nbsp;Delegations</a> -->
                            </li>
                        @endif
                        @if(\Auth::user()->type=='admin' || Session::has('impersonate'))
                            <li class="nav-item">
                                <a class="nav-link" id="quota-tab" data-toggle="tab" href="#quota" role="tab" aria-controls="profile" aria-selected="false"><i class="fa fa-calculator"></i> &nbsp;Quota</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <!-- /.col-md-4 -->
                <div class="col-md-10" >
                @if ($errors->any())
                 <div class="alert alert-danger">
                     <ul>
                         @foreach ($errors->all() as $error)
                             <li>{{ $error }}</li>
                         @endforeach
                     </ul>
                 </div>
                @endif
                    @if(!isset($company->companyUser))
                    <form id="default-currency" enctype="multipart/form-data">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group files">
                                            <label>Import logo (Max size 1 mb) </label>
                                            <input type="file" class="form-control-file" id="logo" name="image">
                                            <div id="logo-error" class="hide"><b>Image size can not be bigger than 1 mb</b></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-form__group">
                                            <label for="name">Name</label>
                                            <input type="text" placeholder="Company's name" id="name" name="name" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-form__group">
                                            <label for="phone">Phone</label>
                                            <input type="text" placeholder="Company's phone" id="phone" name="phone" class="form-control" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-form__group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" name="address" id="address" placeholder="Company's address" cols="4" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group m-form__group">
                                            <label for="currency_id">Main currency</label>
                                            {{ Form::select('currency_id',$currencies,null,['placeholder' => 'Please choose a currency','class'=>'custom-select form-control','id' => 'currency_id','required'=>'true']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group m-form__group">
                                            <label for="decimals">Decimals</label><br>
                                            <label class="m-radio m-radio--check-bold m-radio--state-success">
												<input   type="radio" name="decimals" value="1">														
													<span></span>True
                                            </label>
                                            <br>
                                            <label class="m-radio m-radio--check-bold m-radio--state-brand">
												<input  checked='false' type="radio" name="decimals" value="0">
	                                                <span></span>False
							                </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group m-form__group">
                                            <label for="future_dates">Future Dates</label><br>
                                            <label class="m-radio m-radio--check-bold m-radio--state-success">
												<input type="radio" name="future_dates" value="1">														
													<span></span>Yes
                                            </label>
                                            <br>
                                            <label class="m-radio m-radio--check-bold m-radio--state-brand">
												<input  checked='No' type="radio" name="future_dates" value="0">
	                                                <span></span>No
							                </label>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                        <div class="form-group m-form__group">
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="origincharge">Origin Charges</label>
											        <input  {{ @$IncludeOrigin}} type="checkbox"  checked='true' name="origincharge" value="1" >	
											            <span></span>
                                                </label>
                                            </label>    
                                            <br>
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="destinationcharge">Destination Charges</label>
                                                    <input {{ @$IncludeDestiny}} type="checkbox"  checked='true' name="destinationcharge" value="1" >
                                                        <span></span>
                                                </label>       
											</label>
                                            <br>
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="showfreightcurrency">Show search totals in freight currency</label>
                                                    <input {{ @$ShowFreightCurrency}} type="checkbox"  checked='true' name="showfreightcurrency" value="1" >
                                                        <span></span>
                                                </label>       
											</label>
                                            <br>
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="storehiddencharges">Store hidden Local Charges in Quote</label>
                                                    <input {{ @$StoreHiddenCharges}} type="checkbox"  checked='true' name="storehiddencharges" value="1" >
                                                        <span></span>
                                                </label>       
											</label>
                                        </div>
                                    </div> 
                                </div>                  
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group m-form__group">
                                            <button type="button" id="default-currency-submit" class="btn btn-primary" style="background-color: #006BFA !important;">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pdf" role="tabpanel" aria-labelledby="pdf-tab">
                                <div class="row">
                                    <div class="col-md-4" >
                                        <div class="form-group m-form__group">
                                            <label for="footer_type">PDF Template</label>
                                            {{ Form::select('pdf_template_id',$pdf_templates,@$company->companyUser->pdf_template_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_template','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="pdf_language">PDF language</label>
                                            {{ Form::select('pdf_language',['0'=>'Choose a language','1'=>'English','2'=>'Spanish','3'=>'Portuguese', '4'=>'Italian', '5'=>'Catalan', '6'=>'French'],null,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="haeder_pdf">
                                        <div class="form-group m-form__group">
                                            <label for="header_type">PDF Header</label>
                                            {{ Form::select('header_type',[''=>'Choose a type','image'=>'Image'],@$company->companyUser->header_type,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_header','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="footer_type">PDF Footer</label>
                                            {{ Form::select('footer_type',[''=>'Choose a type','Text'=>'Text','Image'=>'Image','Color'=>'Color'],@$company->companyUser->footer_type,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_footer','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group m-form__group">
                                            <label for="colors_PDF">Color</label><br>
                                            <input  type="color" name="colors_pdf" value="{{ @$color_pdf}}"> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 hide" id="header_image">
                                        <hr>
                                        <div class="form-group files">
                                            <label for="header_image">PDF Header image (1280 x 120 recommended)</label>
                                            <input type="file" class="form-control-file" name="header_image">
                                            @if(@$company->companyUser->header_image!='')
                                            <img src="{{Storage::disk('s3_upload')->url(@$company->companyUser->footer_image )}}" class="img img-thumbnail img-fluid" style="width:100%; height: 80px;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 hide" id="footer_image">
                                        <hr>
                                        <div class="form-group files">
                                            <label for="footer_image">PDF Footer image (1280 x 120 recommended)</label>
                                            <input type="file" class="form-control-file" name="footer_image">
                                            @if(@$company->companyUser->footer_image!='')
                                            <img src="{{Storage::disk('s3_upload')->url(@$company->companyUser->footer_image )}}" class="img img-thumbnail img-fluid" style="width:100%; height: 80px;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 hide" id="footer_text">
                                        <hr>
                                        <div class="form-group files">
                                            <label for="footer_text">PDF Footer text</label>
                                            <textarea name="footer_text" class="form-control footer_text editor" id="footer_text">{!!@$company->companyUser->footer_text!!}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group m-form__group">
                                            <button type="button" id="default-currency-submit" class="btn btn-primary" style="background-color: #006BFA !important;">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade {{\Auth::user()->type=='subuser' ? 'show active':''}}" id="emails" role="tabpanel" aria-labelledby="emails-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="footer_text">Email from (Requires verification)</label>
                                            <input type="email" value="{{@$email_settings->email_from}}" id="email_from" name="email_from" class="form-control"/>
                                            <span class="hide" id="email_from_error" style="color:red;">Enter a valid email</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="footer_text">Signature type</label>
                                            {{ Form::select('email_signature_type',[''=>'Choose a type','text'=>'Text','image'=>'Image'],@$email_settings->email_signature_type,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'signature_type']) }}
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6 {{@$email_settings->email_signature_type=='image' ? '':'hide'}}" id="signature_image">
                                        <div class="form-group files">
                                            <label for="footer_image">Email signature image</label>
                                            <input type="file" class="form-control-file" name="email_signature_image">
                                            @if(@$email_settings->email_signature_image!='')
                                            <img src="{{Storage::disk('s3_upload')->url($email_settings->email_signature_image )}}" class="img img-thumbnail img-fluid" style="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 {{@$email_settings->email_signature_type=='text' ? '':'hide'}}" id="signature_text">
                                        <div class="form-group files">
                                            <label for="footer_text">Email signature text</label>
                                            <textarea name="email_signature_text" class="form-control email_signature_text editor" id="email_signature_text">{!!@$email_settings->email_signature_text!!}</textarea>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <br>
                                        <div class="form-group m-form__group">
                                            <button type="button" id="default-currency-submit" class="btn btn-primary" style="background-color: #006BFA !important;">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @else
                    <form id="default-currency" enctype="multipart/form-data">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade {{\Auth::user()->type!='subuser' ? 'show active':''}}" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group files">
                                            <label>Import logo (Max size 1 mb) </label>
                                            <input type="file" class="form-control-file" id="logo" name="image">
                                            <div id="logo-error" class="hide"><b>Image size can not be bigger than 1 mb</b></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-form__group">
                                            <label>Logo preview</label><br>
                                            @if(@$company->companyUser->logo!='')
                                            <img src="{{Storage::disk('s3_upload')->url(@$company->companyUser->logo)}}" class="img img-thumbnail img-fluid" style="width:40%; height: auto">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-form__group ">
                                            <label for="name">Name</label>
                                            <input type="hidden" value="{{@$company->companyUser->id}}" id="company_id" name="company_id" class="form-control"/>
                                            <input type="text" value="{{@$company->companyUser->name}}" id="name" name="name" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group m-form__group text-left">
                                            <label for="phone">Phone</label>
                                            <input type="text" value="{{@$company->companyUser->phone}}" id="phone" name="phone" class="form-control" required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group m-form__group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" name="address" id="address" cols="4" required>{{@$company->companyUser->address}}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group m-form__group">
                                            <label for="currency_id">Main currency</label>
                                            {{ Form::select('currency_id',$currencies,@$company->companyUser->currency_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'currency_id','required'=>'true']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group m-form__group">
                                            <label for="decimals">Decimals</label><br>
                                            <label class="m-radio m-radio--check-bold m-radio--state-success">
											    <input  {{ $selectedTrue}} type="radio" name="decimals" value="1">	
											        <span></span>True
                                            </label>
                                            <br>
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <input {{ $selectedFalse}} type="radio" name="decimals" value="0">
                                                    <span></span>False
											</label>
                                        </div>
                                    </div>   
                                    <div class="col-md-2">
                                        <div class="form-group m-form__group">
                                            <label for="future_dates">Future Dates</label><br>
                                            <label class="m-radio m-radio--check-bold m-radio--state-success">
											    <input  {{ $selectedDatesTrue}} type="radio" name="future_dates" value="1">	
											        <span></span>Yes
                                            </label>
                                            <br>
                                            <label class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <input {{ $selectedDatesFalse}} type="radio" name="future_dates" value="0">
                                                    <span></span>No
											</label>
                                        </div>
                                    </div>   
                                    <div class="col-md-3">
                                        <div class="form-group m-form__group">
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="origincharge">Origin Charges</label>
											        <input  {{ @$IncludeOrigin}} type="checkbox" name="origincharge" value="1" >	
											            <span></span>
                                                </label>
                                            </label>    
                                            <br>
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="destinationcharge">Destination Charges</label>
                                                    <input {{ @$IncludeDestiny}} type="checkbox" name="destinationcharge" value="1" >
                                                        <span></span>
                                                </label>       
											</label>
                                            <br>
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="showfreightcurrency">Show Search totals in freight currency</label>
                                                    <input {{ @$ShowFreightCurrency}} type="checkbox" name="showfreightcurrency" value="1" >
                                                        <span></span>
                                                </label>       
											</label>
                                            <br>
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="storehiddencharges">Store hidden Local Charges in Quote</label>
                                                    <input {{ @$StoreHiddenCharges}} type="checkbox" name="storehiddencharges" value="1" >
                                                        <span></span>
                                                </label>       
											</label>
                                        </div>
                                    </div>                       

                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <br>
                                        <div class="form-group m-form__group">
                                            <button type="button" id="default-currency-submit" class="btn btn-primary btn-block" style="background-color: #006BFA !important;">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pdf" role="tabpanel" aria-labelledby="pdf-tab">
                                <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="footer_type">PDF Template</label>
                                            {{ Form::select('pdf_template_id',$pdf_templates,@$company->companyUser->pdf_template_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_template','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="pdf_language">PDF language</label>
                                            {{ Form::select('pdf_language',['0'=>'Choose a language','1'=>'English','2'=>'Spanish','3'=>'Portuguese', '4'=>'Italian', '5'=>'Catalan', '6'=>'French'],@$company->companyUser->pdf_language,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4 {{@$company->companyUser->pdf_template_id=='2' ? '':'hide'}} " id="haeder_pdf">
                                        <div class="form-group m-form__group">
                                            <label for="footer_type">PDF Header</label>
                                            {{ Form::select('header_type',[''=>'Choose a type','image'=>'Image'],@$company->companyUser->header_type,['class'=>'custom-select form-control','id' => 'pdf_header','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="footer_type">PDF Footer</label>
                                            {{ Form::select('footer_type',[''=>'Choose a type','Text'=>'Text','Image'=>'Image','Color'=>'Color'],@$company->companyUser->footer_type,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_footer','required'=>'true']) }}
                                        </div>
                                    </div>                          
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="colors_PDF">Color</label><br>
                                            <input  type="color" name="colors_pdf" value="{{ @$color_pdf}}"> 
                                        </div>
                                    </div>
                                    <div class="col-md-4 pt-4 pb-3">
                                        <div class="form-group m-form__group">
                                            <label  class="m-radio m-radio--check-bold m-radio--state-brand">
                                                <label for="disabledelegationpdf">Disable delegation in PDF</label>
                                                    <input {{ @$disableDelegationPdf}} type="checkbox"  name="disabledelegationpdf" value="true" >
                                                        <span></span>
                                                </label>       
											</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 {{@$company->companyUser->header_type=='image' ? '':'hide'}}" id="header_image">
                                        <hr>
                                        <div class="form-group files">
                                            <label for="header_image">PDF header image (1280 x 120 recommended)</label>
                                            <input type="file" class="form-control-file" name="header_image">
                                            @if(@$company->companyUser->header_image!='')
                                            <img src="{{Storage::disk('s3_upload')->url(@$company->companyUser->header_image )}}" class="img img-thumbnail img-fluid" style="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 {{@$company->companyUser->footer_type=='Image' ? '':'hide'}}" id="footer_image">
                                        <hr>
                                        <div class="form-group files">
                                            <label for="footer_image">PDF Footer image (1280 x 120 recommended)</label>
                                            <input type="file" class="form-control-file" name="footer_image">
                                            @if(@$company->companyUser->footer_image!='')
                                            <img src="{{Storage::disk('s3_upload')->url(@$company->companyUser->footer_image )}}" class="img img-thumbnail img-fluid" style="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 {{@$company->companyUser->footer_type=='Text' ? '':'hide'}}" id="footer_text">
                                        <hr>
                                        <div class="form-group files">
                                            <label for="footer_text">PDF Footer text</label>
                                            <textarea name="footer_text" class="form-control footer_text editor" id="footer_text">{!!@$company->companyUser->footer_text!!}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <br>
                                        <div class="form-group m-form__group">
                                            <button type="button" id="default-currency-submit" class="btn btn-primary btn-block" style="background-color: #006BFA !important;">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade {{\Auth::user()->type=='subuser' ? 'show active':''}}" id="emails" role="tabpanel" aria-labelledby="emails-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="footer_text">Email from (Requires verification)</label>
                                            <input type="email" value="{{@$email_settings->email_from}}" id="email_from" name="email_from" class="form-control"/>
                                            <span class="hide" id="email_from_error" style="color:red;">Enter a valid email</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="footer_text">Signature type</label>
                                            {{ Form::select('email_signature_type',[''=>'Choose a type','text'=>'Text','image'=>'Image'],@$email_settings->email_signature_type,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'signature_type']) }}
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6 {{@$email_settings->email_signature_type=='image' ? '':'hide'}}" id="signature_image">
                                        <div class="form-group files">
                                            <label for="footer_image">Email signature image</label>
                                            <input type="file" class="form-control-file" name="email_signature_image">
                                            @if(@$email_settings->email_signature_image!='')
                                            <img src="{{Storage::disk('s3_upload')->url($email_settings->email_signature_image )}}" class="img img-thumbnail img-fluid" style="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 {{@$email_settings->email_signature_type=='text' ? '':'hide'}}" id="signature_text">
                                        <div class="form-group files">
                                            <label for="footer_text">Email signature text</label>
                                            <textarea name="email_signature_text" class="form-control email_signature_text editor" id="email_signature_text">{!!@$email_settings->email_signature_text!!}</textarea>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <br>
                                        <div class="form-group m-form__group">
                                            <button type="button" id="default-currency-submit" class="btn btn-primary btn-block" style="background-color: #006BFA !important;">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="delegation" role="tabpanel" aria-labelledby="delegations-tab">
                                <div class="m--align-right">
                                    <a  id="newRate" class="">  
                                        <button type="button" class="btn btn-primary m--align-right" style="background-color: #006BFA !important;" data-toggle="modal"
                                            data-target="#AddDelegationModal">
                                            Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                </div>    
                                <br>
                                <table class="m-datatable" id="delegationstable" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($delegations as $items)
                                            <tr>
                                                <td>{{ $items->name }}</td>                                              
                                                <td>{{ $items->phone }}</td>
                                                <td>{{ $items->address }}</td>
                                                <td>
                                                <input name="del_id" type="hidden" value="{{$items->id}}" class="del_id"/>
                                                <a href="#" class="open_edit_modal" data-toggle="modal"
                                                data-target="#EditDelegationModal"><i class="fa fa-edit"></i></a> 
                                                &nbsp; 
                                                <a href="#" class="delete-delegation"><i class="fa fa-trash"></i></a>
                                                </td>
                                            </tr> 
                                        @endforeach
                                    </tbody>
                                </table>                               
                            </div>
                            <div class="tab-pane fade" id="quota" role="tabpanel" aria-labelledby="quota-tab">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="quota_type">Type</label>
                                            {{ Form::select('type',['limited' => 'Limited', 'unlimited' => 'Unlimited'],@$company->companyUser->quota->type,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_template','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="billing_period">Billing period</label>
                                            {{ Form::select('payment_type',['monthly'=>'Monthly','biannual'=>'Biannual','annual'=>'Annual'],@$company->companyUser->quota->payment_type,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="status">Status</label>
                                            {{ Form::select('status',[1=>'Active',0=>'Inactive'],@$company->companyUser->quota->status,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="quantity">Quantity per month</label>
                                            {{ Form::number('quota',@$company->companyUser->quota->quota,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="remaining_quantity">Remaining quantity</label>
                                            {{ Form::number('remaining_quota',@$company->companyUser->quota->remaining_quota,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="issued_date">Issued date</label>
                                            {{ Form::date('issued_date',@$company->companyUser->quota->issued_date,['placeholder' => 'Please choose a date','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group m-form__group">
                                            <label for="issued_date">Due date</label>
                                            {{ Form::date('due_date',@$company->companyUser->quota->due_date,['placeholder' => 'Please choose a date','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true', 'disabled'=>'true']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <br>
                                        <div class="form-group m-form__group">
                                            <button type="button" id="default-currency-submit" class="btn btn-primary btn-block" style="background-color: #006BFA !important;">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
                <!-- /.col-md-8 -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="{{asset('js/settings.js')}}" type="text/javascript"></script>
<script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<script>

    var editor_config = {
        path_absolute : "/",
        selector: "textarea.editor",
        plugins: ["template"],
        toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
        external_plugins: { "nanospell": "{{asset('js/tinymce/plugins/nanospell/plugin.js')}}" },
        nanospell_server:"php",
        browser_spellcheck: true,
        relative_urls: false,
        remove_script_host: false,
        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + "&type=Images";
            } else {
                cmsURL = cmsURL + "&type=Files";
            }

            tinymce.activeEditor.windowManager.open({
                file: '<?= route('elfinder.tinymce4') ?>',// use an absolute path!
                title: 'File manager',
                width: 900,
                height: 450,
                resizable: 'yes'
            }, {
                setUrl: function (url) {
                    win.document.getElementById(field_name).value = url;
                }
            });
        }
    };

    tinymce.init(editor_config);
</script>
@include('settings.add_D', ['company' => @$company])
@include('settings.edit_D')
@stop