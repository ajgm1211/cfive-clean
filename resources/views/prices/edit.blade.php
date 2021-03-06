<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 07:15 PM
 */
?>

<!--<div class="m-portlet">
    {!! Form::open(['route' => 'prices.store','class' => 'form-group m-form__group']) !!}
        <div class="m-portlet__body">
            <div class="m-form__section m-form__section--first">
                <div class="form-group m-form__group">
                    @include('prices.partials.form_add_prices')
        </div>
    </div>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <br>
        <div class="m-form__actions m-form__actions">
{!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
        <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">Cancel</span>
        </button>
    </div>
</div>
</div>
{!! Form::close() !!}
        </div>

        <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
        <script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>-->

        @extends('layouts.app')
        @section('title', 'Edit Price Level')
        @section('content')
        <div class="m-content">
            <div class="m-portlet m-portlet--mobile">
                @if(Session::has('message.nivel'))
                <div class="col-md-12">
                    <br>
                    <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
                        <div class="m-alert__icon">
                            <i class="la la-warning"></i>
                        </div>
                        <div class="m-alert__text">
                            <strong>
                                {{ session('message.title') }}
                            </strong>
                            {{ session('message.content') }}
                        </div>
                        <div class="m-alert__close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                @endif
                <div class="m-portlet__body">
                    {{ Form::model($price, array('route' => array('prices.update', $price->id), 'method' => 'PUT')) }}
                    <div class="row">
                        <div class="col-md-3">
                            <label>Name</label>
                            <div class="form-group m-form__group align-items-center">
                                {!! Form::text('name', null, ['placeholder' => 'Please enter level pricing name','class' => 'form-control m-input','required' => 'required']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Description</label>
                            <div class="form-group m-form__group align-items-center">
                                {!! Form::text('description', null, ['placeholder' => 'Please enter level pricing description','class' => 'form-control m-input','required' => 'required']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Companies</label>
                            <div class="form-group m-form__group align-items-center">
                                {{ Form::select('companies[]',$companies,$selected_companies,['multiple','class'=>'custom-select form-control','id' => 'select-2']) }}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="">
                                <ul class="nav nav-tabs m-tabs-line m-tabs-line--2x m-tabs-line--success" role="tablist">
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                            Sea Freights FCL
                                        </a>
                                    </li>
                                    <li class="nav-item dropdown m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_2" role="button" aria-haspopup="true" aria-expanded="false">
                                            Sea Freights LCL
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_3" role="tab">
                                            Air Freights
                                        </a>
                                    </li> -->
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Freight Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->freight_markup as $item)
                                                    @if($item->price_type_id==1)
                                                    @if($item->percent_markup!=0 || $item->fixed_markup != 0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_freight_markup_1">
                                                                <option value="1" {!! $item->percent_markup != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $item->fixed_markup != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_1" {!! $item->percent_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_1" {!! $item->fixed_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_1" {!! $item->percent_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <input type="hidden" class="form-control" value="1" name="freight_type[]"/>
                                                            <input type="hidden" class="form-control" value="3" name="subtype_3[]"/>
                                                            <input type="number" class="form-control" id="freight_percent_markup_1" value="{{$item->percent_markup}}" name="freight_percent_markup[]"/>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_1" {!! $item->fixed_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="freight_fixed_markup_1" name="freight_fixed_markup[]" value="{{$item->fixed_markup}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="freight_markup_currency_1" name="freight_markup_currency[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $item->currency == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_freight_markup_1">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_1">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_1" style="display: none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_1">
                                                            <input type="hidden" class="form-control" value="1" name="freight_type[]"/>
                                                            <input type="hidden" class="form-control" value="3" name="subtype_3[]"/>
                                                            <input type="number" class="form-control" id="freight_percent_markup_1" value="0" name="freight_percent_markup[]"/>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_1" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="freight_fixed_markup_1" name="freight_fixed_markup[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="freight_markup_currency_1" name="freight_markup_currency[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Local Charges Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->local_markup as $local)
                                                    @if($local->price_type_id==1)
                                                    @if($local->percent_markup_import!=0 || $local->fixed_markup_import != 0 || $local->fixed_markup_export != 0 || $local->percent_markup_export!=0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_local_markup_1">
                                                                <option value="1" {!! $local->percent_markup_import != 0 || $local->percent_markup_export != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $local->fixed_markup_import != 0 || $local->fixed_markup_export != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_1" {!! $local->percent_markup_import == 0 && $local->percent_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_1" {!! $local->fixed_markup_import == 0 && $local->fixed_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_1" {!! $local->percent_markup_export == 0 &&  $local->percent_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <input type="hidden" class="form-control" value="1" name="local_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype[]"/>
                                                            <input type="number" class="form-control"  id="local_percent_markup_1" value="{{$local->percent_markup_import}}" name="local_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_1" {!! $local->fixed_markup_import == 0 && $local->fixed_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_1" name="local_fixed_markup_import[]" value="{{$local->fixed_markup_import}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_1" name="local_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $local->currency_import == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_1_2" {!! $local->percent_markup_export == 0 &&  $local->percent_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_1_2" value="{{$local->percent_markup_export}}" name="local_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_1_2" {!! $local->fixed_markup_export == 0 &&  $local->fixed_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_1_2" name="local_fixed_markup_export[]" value="{{$local->fixed_markup_export}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_1_2" name="local_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $local->currency_export == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_local_markup_1">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_1">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_1" style="display: none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_1">
                                                            <input type="hidden" class="form-control" value="1" name="local_type[]"/>
                                                            <input type="hidden" class="form-control" value="2" name="subtype[]"/>
                                                            <input type="text" class="form-control"  id="local_percent_markup_1" value="0" name="local_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_1" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_1" name="local_fixed_markup_import[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_1" name="local_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_1_2">
                                                            <input type="hidden" class="form-control" value="2" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_1_2" value="0" name="local_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_1_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_1_2" name="local_fixed_markup_export[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_1_2" name="local_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!--end::Portlet-->
                                        </div>
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Inland Charges Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->inland_markup as $inland)
                                                    @if($inland->price_type_id==1)
                                                    @if($inland->percent_markup_import!=0 || $inland->fixed_markup_import != 0 || $inland->fixed_markup_export != 0 || $inland->percent_markup_export!=0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_inland_markup_1">
                                                                <option value="1" {!! $inland->percent_markup_import != 0 || $inland->percent_markup_export != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $inland->fixed_markup_import != 0 || $inland->fixed_markup_export != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Inland Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_1" {!! $inland->percent_markup_import == 0 && $inland->percent_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_1" {!! $inland->fixed_markup_import == 0 && $inland->fixed_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_1" {!! $inland->percent_markup_import == 0 && $inland->percent_markup_export == 0? 'style="display:none"':'' !!}>
                                                            <input type="hidden" class="form-control" value="1" name="inland_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_1" value="{{$inland->percent_markup_import}}" name="inland_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_1" {!! $inland->fixed_markup_import == 0 && $inland->fixed_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_1" name="inland_fixed_markup_import[]" value="{{$inland->fixed_markup_import}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_1" name="inland_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $inland->currency_import == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_1_2" {!! $inland->percent_markup_export == 0 && $inland->percent_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_1_2" value="{{$inland->percent_markup_export}}" name="inland_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_1_2" {!! $inland->fixed_markup_import == 0 && $inland->fixed_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_1_2" name="inland_fixed_markup_export[]" value="{{$inland->fixed_markup_export}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_1_2" name="inland_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $inland->currency_export == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_inland_markup_1">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Inland Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_1">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_1" style="display: none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_1">
                                                            <input type="hidden" class="form-control" value="1" name="inland_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_1" value="0" name="inland_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_1" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_1" name="inland_fixed_markup_import[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_1" name="inland_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_1_2">
                                                            <input type="hidden" class="form-control" value="2" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_1_2" value="0" name="inland_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_1_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_1_2" name="inland_fixed_markup_export[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_1_2" name="inland_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!--end::Portlet-->
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="m_tabs_6_2" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Freight Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->freight_markup as $item)
                                                    @if($item->price_type_id==2)
                                                    @if($item->percent_markup!=0 || $item->fixed_markup != 0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_freight_markup_2">
                                                                <option value="1" {!! $item->percent_markup != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $item->fixed_markup != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_1" {!! $item->percent_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_1" {!! $item->fixed_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_2" {!! $item->percent_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="freight_type[]"/>
                                                            <input type="hidden" class="form-control" value="3" name="subtype_3[]"/>
                                                            <input type="number" class="form-control" id="freight_percent_markup_2" value="{{$item->percent_markup}}" name="freight_percent_markup[]"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group freight_fixed_markup_2" {!! $item->fixed_markup == 0 ? 'style="display: none;"':'' !!}>
                                                                <input type="number" id="freight_fixed_markup_2" name="freight_fixed_markup[]" value="{{$item->fixed_markup}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="freight_markup_currency_2" name="freight_markup_currency[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $item->currency == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_freight_markup_2">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_2">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_2" style="display:none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_2">
                                                            <input type="hidden" class="form-control" value="2" name="freight_type[]"/>
                                                            <input type="hidden" class="form-control" value="3" name="subtype_3[]"/>
                                                            <input type="number" class="form-control" id="freight_percent_markup_2" value="0" name="freight_percent_markup[]"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group freight_fixed_markup_2" style="display: none;">
                                                                <input type="number" id="freight_fixed_markup_2" name="freight_fixed_markup[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="freight_markup_currency_2" name="freight_markup_currency[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Local Charges Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->local_markup as $local)
                                                    @if($local->price_type_id==2)
                                                    @if($local->percent_markup_import!=0 || $local->fixed_markup_import != 0 || $local->fixed_markup_export != 0 || $local->percent_markup_export!=0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_local_markup_2">
                                                                <option value="1" {!! $local->percent_markup_import != 0 || $local->percent_markup_export != 0? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $local->fixed_markup_import != 0 || $local->fixed_markup_export != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2" {!! $local->percent_markup_import == 0 && $local->percent_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2" {!! $local->fixed_markup_import == 0 && $local->fixed_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2" {!! $local->percent_markup_import == 0 && $local->percent_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="local_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_2" value="{{$local->percent_markup_import}}" name="local_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2" {!! $local->fixed_markup_import == 0 &&  $local->fixed_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_2" name="local_fixed_markup_import[]" value="{{$local->fixed_markup_import}}" min="0" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_2" name="local_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $local->currency_import == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2_2" {!! $local->percent_markup_export == 0 && $local->percent_markup_import == 0 ? 'style="display:none;"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_2_2" value="{{$local->percent_markup_export}}" name="local_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2_2" {!! $local->fixed_markup_export == 0 && $local->fixed_markup_import == 0? 'style="display:none;"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_2_2" name="local_fixed_markup_export[]" value="{{$local->fixed_markup_export}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_2_2" name="local_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $inland->currency_export == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_local_markup_2">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2" style="display: none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2">
                                                            <input type="hidden" class="form-control" value="2" name="local_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_2" value="0" name="local_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_2" name="local_fixed_markup_import[]" value="0" min="0" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_2" name="local_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2_2" >
                                                            <input type="hidden" class="form-control" value="2" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_2_2" value="0" name="local_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_2_2" name="local_fixed_markup_export[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_2_2" name="local_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!--end::Portlet-->
                                        </div>
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Inland Charges Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->inland_markup as $inland)
                                                    @if($inland->price_type_id==2)
                                                    @if($inland->percent_markup_import!=0 || $inland->fixed_markup_import != 0 || $inland->fixed_markup_export != 0 || $inland->percent_markup_export!=0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_inland_markup_2">
                                                                <option value="1" {!! $inland->percent_markup_import != 0 || $inland->percent_markup_export != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $inland->fixed_markup_import != 0 ||  $inland->fixed_markup_export != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Inland Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_1" {!! $inland->percent_markup_import == 0 && $inland->percent_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_1" {!! $inland->fixed_markup_import == 0 &&  $inland->fixed_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2" {!! $inland->percent_markup_import == 0 && $inland->percent_markup_export == 0? 'style="display:none"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="inland_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_2" value="{{$inland->percent_markup_import}}" name="inland_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2" {!! $inland->fixed_markup_import == 0 && $inland->fixed_markup_export == 0? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_2" name="inland_fixed_markup_import[]" value="{{ $inland->fixed_markup_import}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_2" name="inland_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $inland->currency_import == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2_2"  {!! $inland->percent_markup_export == 0 && $inland->percent_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_2_2" value="{{$inland->percent_markup_export}}" name="inland_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2_2"  {!! $inland->fixed_markup_export == 0 && $inland->fixed_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_2_2" name="inland_fixed_markup_export[]" value="{{$inland->fixed_markup_export}}" min="0" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_2_2" name="inland_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $inland->currency_export == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_inland_markup_2">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Inland Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2" style="display: none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2">
                                                            <input type="hidden" class="form-control" value="2" name="inland_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_2" value="0" name="inland_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_2" name="inland_fixed_markup_import[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_2" name="inland_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2_2">
                                                            <input type="hidden" class="form-control" value="2" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_2_2" value="0" name="inland_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_2_2" name="inland_fixed_markup_export[]" value="0" min="0" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_2_2" name="inland_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!--end::Portlet-->
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="m_tabs_6_3" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Freight Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->freight_markup as $item)
                                                    @if($item->price_type_id==3)
                                                    @if($item->percent_markup!=0 || $item->fixed_markup != 0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_freight_markup_3">
                                                                <option value="1" {!! $item->percent_markup != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $item->fixed_markup != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_3" {!! $item->percent_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_3" {!! $item->fixed_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_3" {!! $item->percent_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <input type="hidden" class="form-control" value="3" name="freight_type[]"/>
                                                            <input type="number" class="form-control" id="freight_percent_markup_3" value="{{$item->percent_markup}}" name="freight_percent_markup[]"/>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_3" {!! $item->fixed_markup == 0 ? 'style="display: none;"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="freight_fixed_markup_3" name="freight_fixed_markup[]" value="{{$item->fixed_markup}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="freight_currency_markup_3" name="freight_markup_currency[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $item->currency == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_freight_markup_2">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_2">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 freight_fixed_markup_2" style="display:none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-6 freight_percent_markup_2">
                                                            <input type="hidden" class="form-control" value="3" name="freight_type[]"/>
                                                            <input type="hidden" class="form-control" value="3" name="subtype_3[]"/>
                                                            <input type="number" class="form-control" id="freight_percent_markup_2" value="0" name="freight_percent_markup[]"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group freight_fixed_markup_2" style="display: none;">
                                                                <input type="number" id="freight_fixed_markup_2" name="freight_fixed_markup[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="freight_markup_currency_2" name="freight_markup_currency[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Local Charges Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->local_markup as $local)
                                                    @if($local->price_type_id==3)
                                                    @if($local->percent_markup_import!=0 || $local->fixed_markup_import != 0 || $local->fixed_markup_export != 0 || $local->percent_markup_export!=0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_local_markup_3">
                                                                <option value="1" {!! $local->percent_markup_import != 0 || $local->percent_markup_export != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $local->fixed_markup_import != 0 || $local->fixed_markup_export != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_3" {!! $local->percent_markup_import == 0 && $local->percent_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_3" {!! $local->fixed_markup_import == 0 && $local->fixed_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_3" {!! $local->percent_markup_import == 0 && $local->percent_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <input type="hidden" class="form-control" value="3" name="local_type[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_3" value="{{$local->percent_markup_import}}" name="local_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_3" {!! $local->fixed_markup_import == 0 && $local->fixed_markup_export == 0 ? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_3" name="local_fixed_markup_import[]" value="{{$local->fixed_markup_import}}" min="0" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_3" name="local_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $local->currency_import == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_3_2" {!! $local->percent_markup_export == 0 && $local->percent_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <input type="number" class="form-control" id="local_percent_markup_3_2" value="{{$local->percent_markup_export}}" name="local_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_3_2" {!! $local->fixed_markup_export == 0 && $local->fixed_markup_import == 0 ? 'style="display:none"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_3_2" name="local_fixed_markup_export[]" value="{{$local->fixed_markup_export}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_3_2" name="local_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}" {!! $local->currency_export == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_local_markup_2">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2" style="display: none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2">
                                                            <input type="hidden" class="form-control" value="3" name="local_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_2" value="0" name="local_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_2" name="local_fixed_markup_import[]" value="0" min="0" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_2" name="local_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach 
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 local_percent_markup_2_2" >
                                                            <input type="hidden" class="form-control" value="2" name="subtype[]"/>
                                                            <input type="number" class="form-control" id="local_percent_markup_2_2" value="0" name="local_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 local_fixed_markup_2_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="local_fixed_markup_2_2" name="local_fixed_markup_export[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="local_currency_markup_2_2" name="local_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach       
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!--end::Portlet-->
                                        </div>
                                        <div class="col-md-4">
                                            <!--begin::Portlet-->
                                            <div class="m-portlet m-portlet--default m-portlet--head-solid-bg" style="min-height: 290px !important;">
                                                <div class="m-portlet__head">
                                                    <div class="m-portlet__head-caption">
                                                        <div class="m-portlet__head-title">
                                                            <h3 class="m-portlet__head-text">
                                                                Define Inland Charges Markups
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="m-portlet__body text-center" style="font-size: 11px !important;">
                                                    @foreach($price->inland_markup as $inland)
                                                    @if($inland->price_type_id==3)
                                                    @if($inland->percent_markup_import!=0 || $inland->fixed_markup_import != 0 || $inland->fixed_markup_export != 0 || $inland->percent_markup_export!=0)
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_inland_markup_3">
                                                                <option value="1" {!! $inland->percent_markup_import != 0 || $inland->percent_markup_export != 0 ? 'selected':'' !!}>Percent Markup</option>
                                                                <option value="2" {!! $inland->fixed_markup_import != 0 || $inland->fixed_markup_export != 0 ? 'selected':'' !!}>Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Inland Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_3" {!! $inland->percent_markup_import == 0 && $inland->percent_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_3" {!! $inland->fixed_markup_import == 0 && $inland->fixed_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_3" {!! $inland->percent_markup_import == 0 && $inland->percent_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <input type="hidden" class="form-control" value="3" name="inland_type[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_3" value="{{$inland->percent_markup_import}}" name="inland_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_3" {!! $inland->fixed_markup_import == 0 && $inland->fixed_markup_export == 0 ? 'style="display:none;"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_3" name="inland_fixed_markup_import[]" value="{{$inland->fixed_markup_import}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_3" name="inland_currency_import[]">                                              
                                                                            @foreach($currencies as $currency)
                                                                            <option value="{{$currency->id}}" {!! $inland->currency_import == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_3_2" {!! $inland->percent_markup_export == 0 && $inland->percent_markup_import == 0 ? 'style="display:none;"':'' !!}>
                                                            <input type="hidden" class="form-control" value="2" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_3_2" value="{{$inland->percent_markup_export}}" name="inland_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_3_2" {!! $inland->fixed_markup_export == 0 && $inland->fixed_markup_import == 0 ? 'style="display:none;"':'' !!}>
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_3_2" name="inland_fixed_markup_export[]" value="{{$inland->fixed_markup_export}}" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_3_2" name="inland_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                            <option value="{{$currency->id}}" {!! $inland->currency_export == $currency->id ? 'selected':'' !!}>{{$currency->alphacode}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <select class="form-control" id="type_inland_markup_2">
                                                                <option value="1">Percent Markup</option>
                                                                <option value="2">Fixed Markup</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span><b>Inland Charges</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2" style="display: none;">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2">
                                                            <input type="hidden" class="form-control" value="3" name="inland_type[]"/>
                                                            <input type="hidden" class="form-control" value="1" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_2" value="0" name="inland_percent_markup_import[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_2" name="inland_fixed_markup_import[]" value="0" min="0" step="0.01" class="form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_2" name="inland_currency_import[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach 
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-6">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-6 inland_percent_markup_2_2">
                                                            <input type="hidden" class="form-control" value="2" name="subtype_2[]"/>
                                                            <input type="number" class="form-control" id="inland_percent_markup_2_2" value="0" name="inland_percent_markup_export[]"/>
                                                        </div>
                                                        <div class="col-md-6 inland_fixed_markup_2_2" style="display: none;">
                                                            <div class="input-group">
                                                                <input type="number" id="inland_fixed_markup_2_2" name="inland_fixed_markup_export[]" value="0" min="0" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default" id="inland_currency_markup_2_2" name="inland_currency_export[]">
                                                                            @foreach($currencies as $currency)
                                                                                <option value="{{$currency->id}}">{{$currency->alphacode}}</option>
                                                                            @endforeach 
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!--end::Portlet-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m-portlet__foot--fit">
                        <br>
                        <div class="m-form__actions">
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                            <a href="javascript:history.back()" class="btn btn-danger">
                                Cancel
                            </a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        @endsection

        @section('js')
        @parent
        <script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
        <script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
        <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
        <script>
            function AbrirModal(action,id){
                if(action == "edit"){
                    var url = '{{ route("prices.edit", ":id") }}';
                    url = url.replace(':id', id);
                    $('.modal-body').load(url,function(){
                        $('#priceModal').modal({show:true});
                    });
                }if(action == "add"){
                    var url = '{{ route("prices.add") }}';
                    $('.modal-body').load(url,function(){
                        $('#priceModal').modal({show:true});
                    });
                }
                if(action == "delete"){
                    var url = '{{ route("prices.delete", ":id") }}';
                    url = url.replace(':id', id);
                    $('.modal-body').load(url,function(){
                        $('#deletePriceModal').modal({show:true});
                    });
                }
            }

            $(document).ready(function() {
                $('#select-2').select2();
            });
        /*$(document).on('change', '#type_freight_markup_1', function (e) {
            if($(this).val()==1){
                $("#freight_fixed_markup_1").prop('disabled', true);
                $("#freight_markup_currency_1").prop('disabled', true);
                $("#freight_percent_markup_1").prop('disabled', false);
            }else{
                $("#freight_fixed_markup_1").prop('disabled', false);
                $("#freight_markup_currency_1").prop('disabled', false);
                $("#freight_percent_markup_1").prop('disabled', true);
            }
        });*/
    </script>
    @stop