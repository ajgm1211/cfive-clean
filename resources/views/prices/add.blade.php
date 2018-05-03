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
@section('title', 'New Price Level')
@section('content')
    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
            <!--<div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Price levels
                        </h3>
                    </div>
                </div>
            </div>-->
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
                {!! Form::open(['route' => 'prices.store','class' => 'form-group m-form__group']) !!}
                    <div class="row">
                        <div class="col-md-3">
                            <label>Name</label>
                            <div class="form-group m-form__group align-items-center">
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Description</label>
                            <div class="form-group m-form__group align-items-center">
                                <input type="text" class="form-control" name="description" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>Companies</label>
                            <div class="form-group m-form__group align-items-center">
                                {{ Form::select('companies[]',$companies,null,['multiple','class'=>'custom-select form-control','id' => 'select-2']) }}
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
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_3" role="tab">
                                            Air Freights
                                        </a>
                                    </li>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Freight Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Freight rate markup</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <span><b>Local Charges</b></span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <span><b>Percent Markup</b></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span><b>Fixed Markup</b></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Import</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row" style="font-size: 11px !important;">
                                                        <div class="col-md-3">
                                                            <span>Export</span>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="percent_markup"/>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="input-group">
                                                                <input type="number" id="destination_exp_amount" name="destination_exp_amount[]" min="1" step="0.01" class="destination_exp_amount form-control" placeholder="" aria-label="...">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <select class="btn btn-default destination_exp_amount_currency" name="destination_currency[]">
                                                                            <option value="usd">USD</option>
                                                                            <option value="clp">CLP</option>
                                                                            <option value="ars">ARS</option>
                                                                            <option value="eur">EUR</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                            <button type="reset" class="btn btn-success">
                                Cancel
                            </button>
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
    </script>
@stop


