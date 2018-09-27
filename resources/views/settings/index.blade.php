<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 29/05/2018
 * Time: 10:50 PM
 */
?>
@extends('layouts.app')
@section('title', 'Settings')
@section('content')
<div class="m-content">
    <div class="row">
        <div class="col-md-4">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    Company's Profile
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                        @if(!isset($company->companyUser))
                        <form id="default-currency" enctype="multipart/form-data">
                            <div class="row text-left" style="font-size: 11px !important;">
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="name">Name</label>
                                        <input type="text" placeholder="Company's name" id="name" name="name" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="phone">Phone</label>
                                        <input type="text" placeholder="Company's phone" id="phone" name="phone" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" name="address"  placeholder="Company's address" cols="4"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="currency_id">Currency</label>
                                        {{ Form::select('currency_id',$currencies,null,['placeholder' => 'Please choose a currency','class'=>'custom-select form-control','id' => 'currency_id']) }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="currency_id">Logo</label>
                                        <input type="file" class="form-control-file" name="image">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <button type="button" id="default-currency-submit" class="btn btn-primary btn-block">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @else
                        <form id="default-currency" enctype="multipart/form-data">
                            <div class="row text-left" style="font-size: 12px !important;">
                                <div class="col-md-12">
                                    <div class="form-group m-form__group ">
                                        <label for="name">Name</label>
                                        <input type="hidden" value="{{$company->companyUser->id}}" id="company_id" name="company_id" class="form-control"/>
                                        <input type="text" value="{{$company->companyUser->name}}" id="name" name="name" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group text-left">
                                        <label for="phone">Phone</label>
                                        <input type="text" value="{{$company->companyUser->phone}}" id="phone" name="phone" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" name="address" cols="4">{{$company->companyUser->address}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="currency_id">Currency</label>
                                        {{ Form::select('currency_id',$currencies,$company->companyUser->currency_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'currency_id']) }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="pdf_language">PDF language</label>
                                        {{ Form::select('pdf_language',['1'=>'Enligsh','2'=>'Spanish','3'=>'Portuguese'],$company->companyUser->pdf_language,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language']) }}
                                    </div>
                                </div>                                        
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <label for="currency_id">Logo</label>
                                        <input type="file" class="form-control-file" name="image">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group m-form__group">
                                        <button type="button" id="default-currency-submit" class="btn btn-primary btn-block">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script>
    $('#currency_id').select2({
        placeholder: "Select an option"
    });
    $('#pdf_language').select2({
        placeholder: "Select an option"
    });
</script>
@stop