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

<div class="m-content">
    <br>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-md-2 mb-3">
                <ul class="nav nav-tabs flex-column" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link color-black" aria-controls="home" aria-selected="true"><h6><b>Company profile</b></h6></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><i class="fa fa-home"></i>&nbsp;Company info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><i class="fa fa-edit"></i> &nbsp;PDF Settings</a>
                    </li>
                </ul>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-md-10" >
                @if(!isset($company->companyUser))
                <form id="default-currency" enctype="multipart/form-data">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
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
                                <div class="col-md-3">
                                    <div class="form-group m-form__group">
                                        <button type="button" id="default-currency-submit" class="btn btn-primary btn-block" style="background-color: #006BFA !important;">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="col-md-12">
                                <div class="form-group m-form__group">
                                    <label for="pdf_language">PDF language</label>
                                    {{ Form::select('pdf_language',['0'=>'Choose a language','1'=>'English','2'=>'Spanish','3'=>'Portuguese'],null,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-form__group">
                                    <button type="button" id="default-currency-submit" class="btn btn-primary btn-block" style="background-color: #006BFA !important;">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <form id="default-currency" enctype="multipart/form-data">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
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
                                        @if($company->companyUser->logo!='')
                                            <img src="{{Storage::disk('s3_upload')->url($company->companyUser->logo)}}" class="img img-thumbnail img-fluid" style="width:40%; height: auto">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group m-form__group ">
                                        <label for="name">Name</label>
                                        <input type="hidden" value="{{$company->companyUser->id}}" id="company_id" name="company_id" class="form-control"/>
                                        <input type="text" value="{{$company->companyUser->name}}" id="name" name="name" class="form-control" required/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group m-form__group text-left">
                                        <label for="phone">Phone</label>
                                        <input type="text" value="{{$company->companyUser->phone}}" id="phone" name="phone" class="form-control" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group m-form__group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" name="address" id="address" cols="4" required>{{$company->companyUser->address}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group m-form__group">
                                        <label for="currency_id">Main currency</label>
                                        {{ Form::select('currency_id',$currencies,$company->companyUser->currency_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'currency_id','required'=>'true']) }}
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
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="col-md-4">
                                <div class="form-group m-form__group">
                                    <label for="pdf_language">PDF language</label>
                                    {{ Form::select('pdf_language',['0'=>'Choose a language','1'=>'English','2'=>'Spanish','3'=>'Portuguese'],$company->companyUser->pdf_language,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true']) }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <br>
                                <div class="form-group m-form__group">
                                    <button type="button" id="default-currency-submit" class="btn btn-primary btn-block" style="background-color: #006BFA !important;">Update</button>
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
@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="{{asset('js/settings.js')}}" type="text/javascript"></script>
<script>
    $('#currency_id').select2({
        placeholder: "Select an option"
    });
    $('#pdf_language').select2({
        placeholder: "Select an option"
    });
</script>
@stop