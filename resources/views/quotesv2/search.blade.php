@extends('layouts.app')
@section('css')
    @parent
    <link href="/css/quote.css" rel="stylesheet" type="text/css" />

    <link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: #f6f6f6;
        }

        .tooltip-inner {
            background-color: #031B4E;
            color: #FFFFFF;
            border: 1px solid rgb(0, 85, 128);
            padding: 5px;
            font-size: 11px;
        }

        .bg-manual {

            background-color: #969cc0;
        }

        .bg-api {

            background-color: #36A3F7;
        }

        .bg-express {
            background-color: #5ce4a4;
        }

        .m-portlet {
            box-shadow: none;
            border-radius: 5px;
            -webkit-border-radius: 5px;
        }

        .btn-search__quotes {
            top: 50px;
            font-size: 18px;
            position: relative;
            padding: 13px 30px;
            border-radius: 50px !important;
        }

        .q-one,
        .q-two,
        .q-three {
            display: flex;
            flex-flow: wrap;
            justify-content: space-between;
        }

        .q-two {
            justify-content: flex-start;
        }

        .q-one div:nth-child(1),
        .q-one div:nth-child(2),
        .q-one div:nth-child(3),
        .q-one div:nth-child(4) {
            overflow: hidden;
        }

        .q-one div:nth-child(1),
        .q-one div:nth-child(2),
        .q-one div:nth-child(3) {
            width: 18% !important;
        }

        .q-one div:nth-child(4) {
            width: 38% !important;
        }

        .q-one div:nth-child(5),
        .q-two div:nth-child(3) {
            width: 100%;
        }

        .q-one div:nth-child(1) label {
            white-space: nowrap;
        }

        .q-two div:nth-child(1) {
            width: 66%;
            margin-right: 10px;
        }

        .q-three div:nth-child(3) {
            width: 100%;
        }

        .q-three div:nth-child(1) {
            width: 50%;
        }

        .dfw {
            width: 100%;
        }

        .no-shadow {
            box-shadow: none;
        }

        .filter-table__quotes,
        .card-p__quotes,
        .card__quote-manual {
            padding: 25px;
            box-shadow: 0px 1px 15px 1px rgba(69, 65, 78, 0.08);
            background: #FFFFFF;
        }

        .card__quote-manual {
            margin: 0 15px;
            border: 2px;
        }

        .no-padding {
            padding: 0px !important;
        }

        .card-p__quotes {
            padding-top: 0px !important;
            padding-bottom: 0px !important;
            margin: 0px;
            border-radius: 5px;
            border: 2px solid transparent;
            transition: all 300ms linear;
        }

        .card-p__quotes:hover {
            border-color: #0072fc;
        }

        .btn-detail__quotes {
            width: 140px;
            height: 30px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            border: 1px solid #ececec;
            transition: all 300ms ease;
        }

        .bg-maersk {
            background-color: #41B0D5;
        }

        .bg-safmarine {
            background-color: #f99702;
        }

        .btn-detail__quotes:hover {
            border-color: #0072fc;
            background-color: #0072fc;
        }

        .btn-detail__quotes:hover span,
        .btn-detail__quotes:hover a i {
            color: #fff;
        }

        .btn-detail__quotes span {
            font-size: 12px;
            color: #0072fc;
        }

        .btn-detail__quotes a {
            height: 0px !important;
        }

        .btn-detail__quotes a i {
            color: #a4a2bb;
        }

        .btn-input__select,
        .btn-input__select-add {
            position: relative;
            left: 25px;
            width: 95px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cecece;
            cursor: pointer;
            font-size: 12px;
            padding: 3px 0px;
            border-radius: 5px;
            border: 2px solid #cecece;
            transition: all 300ms ease;
        }

        .btn-input__select:hover,
        .btn-input__select-add:hover {
            border-color: #0072fc;
        }

        .input-select[type="checkbox"] {
            display: none;
        }

        .input-select[type="checkbox"]:checked+.btn-input__select {
            color: #fff;
            display: flex;
            width: 120px;
            border-color: #0072fc;
            justify-content: center;
            background-color: #0072fc;
        }

        .style__select-add {
            color: #fff;
            border-color: #0072fc;
            background-color: #0072fc;
        }

        .add-click {
            color: #cecece !important;
        }

        .input-select[type="checkbox"]:checked+.btn-input__select span {
            display: none;
        }

        .btn-input__select-add {
            width: 60px !important;
            left: 60px;
            visibility: hidden;
        }

        .btn-input__select-gen {
            width: 60px !important;
            left: 60px;
            visibility: hidden;
        }

        .hidden-general {
            display: none !important;
        }

        .visible__select-add {
            visibility: visible;
        }

        .col-txt {
            font-weight: 600;
            color: #0072fc;
            font-size: 18px;
        }

        .btn-d {
            width: 130px;
        }

        .padding {
            padding: 0 25px;
        }

        .padding-v2 {
            padding: 25px;
        }

        .no-margin {
            margin: 0 !important;
        }

        .freight__quotes {
            border-top: none !important;
            border: 3px solid #0072fc;
            border-radius: 0px 0px 3px 3px;
        }

        .add-class__card-p {
            box-shadow: none;
            border: 3px solid #0072fc;
            border-bottom: 1px solid #ececec !important;
            border-radius: 3px 3px 0px 0px !important;
        }

        .bg-light {
            padding: 5px 25px;
            border-radius: 3px;
            background-color: #f4f3f8 !important;
        }

        .portalphacode {
            color: #1d3b6e !important;
        }

        .colorphacode {
            color: #7c83b3;
        }

        .bg-rates {
            padding: 2px 5px;
            border-radius: 3px;
            text-align: center;
            background-color: #ececec;
        }

        .wth {
            width: 25%;
        }

        .table-r__quotes {
            height: 100%;
            display: flex;
            justify-content: space-between;
        }

        .table-r__quotes div {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .b-top {
            border-top: 1px solid #ececec;
        }

        .padding-min {
            padding: 10px !important;
        }

        .b-left {
            border-left: 1px solid #ececec;
        }

        .padding-min-col {
            padding: 45px 10px !important;
        }

        .pos-btn {
            position: relative;
            right: 40px;
        }

        .padding-right-table {
            padding-right: 50px !important;
        }

        .btn-date {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
        }

        .data-rates {
            padding: 5px 25px;
        }

        .arrow-down {
            top: 4px;
            position: relative;
        }

        .monto-down {
            top: 2px;
            position: relative;
        }

        .min-width-filter span {
            min-width: 50px !important;
        }

        .min-width-filter .select2-search--dropdown {
            padding: 0px !important;
        }

        .margin-card {
            margin-top: 50px !important;
            margin-bottom: 50px !important;
        }

        .no-check {
            display: none !important;
        }

        .border-bottom {
            border-bottom: 1px solid #ececec;
        }

        .border-card {
            border-color: #0072fc !important;
        }

        .btn-manual__quotes {
            background-color: transparent;
            color: #0072fc !important;
            border-width: 2px;
            font-weight: 600;
            padding: 0.35rem 1rem;
        }

        .btn-manual__quotes span {
            top: 2px;
            position: relative;
        }

        .warning-p {
            color: #575962;
            font-size: 14px;
            font-weight: 600;
        }

        .warning-p span {
            color: #e74c3c;
        }

        .warning-p i {
            font-size: 33px;
            top: 7px;
            margin-right: 5px;
            position: relative;
            transform: rotate(180deg);
        }

        .btn-remarks {
            width: 95px;
        }

        .btn-nowrap {
            white-space: nowrap;
        }

        .select-class::before {
            content: 'Select ->';
            font-size: 13px;
        }

        .selected-class:before {
            content: 'Selected';
            font-size: 13px;
        }

        .full-width {
            width: 100% !important;
        }

        .create-manual {
            background-color: #fff !important;
            color: #36a3f7 !important;
            border-width: 2px;
            border-color: #36a3f7 !important;
        }

        .create-manual:hover {
            background-color: #36a3f7 !important;
            border-color: #36a3f7 !important;
        }

        .workgreen {
            color: #6ee99e !important;
            font-weight: bold !important;
        }

        .downexcel {
            border-color: #6ee99e !important;
        }

        .downexcel a {
            text-decoration: none;
        }

        .downexcel:hover {
            background-color: transparent !important;
        }

        .downexcel i {
            margin-top: 8px !important;
            font-size: 24px;
            color: #6ee99e !important;
        }

        .btn-plus__form {
            position: relative;
            top: 8px;
        }

        .include-checkbox[type="checkbox"] {
            display: none;
        }

        .for-check {
            display: flex;
            align-items: flex-end;
            padding-left: 40px;
            padding-right: 0px;
        }

        .label-check {
            position: relative;
        }

        .label-check::before {
            content: '';
            position: absolute;
            top: -1px;
            left: -25px;
            width: 15px;
            height: 15px;
            background: transparent;
            border: 2px solid #0000ff;
            border-radius: 3px;
            display: flex;
            /*align-items: center*/
            justify-content: center;
        }

        .include-checkbox[type="checkbox"]:checked+.label-check::before {
            content: '???';
            color: #0000ff;
            line-height: 15px;
        }

        /* c5 select */
        .c5-select-multiple {
            display: none;
        }

        .c5-select-multiple-dropdown {
            width: 100%;
            height: 37px;
        }

        .c5-select-multiple-container {
            width: 100%;
            height: auto;
            padding: 15px;
            display: none;
            border: 1px solid #eee;
            -webkit-border: 1px solid #eee;
            background: #fff;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            position: absolute;
            margin-top: 5px;
            z-index: 100;
            box-shadow: 0px 1px 15px 1px rgba(0, 0, 0, 0.1);
            -webkit-box-shadow: 0px 1px 15px 1px rgba(0, 0, 0, 0.1);
        }

        .c5-select-container-close {
            position: absolute;
            top: 12px;
            right: 10px;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 100px;
            -webkit-border-radius: 100px;
        }

        .c5-select-container-close:hover {
            background: #f9f9f9;
        }

        .c5-select-header,
        .c5-select-multiple-switch {
            font-size: 14px;
            color: #333;
            padding: 0px 0px 15px 0px;
            display: block;
        }

        .c5-select-list {
            height: 165px;
            padding-left: 0px;
            margin-bottom: 0px;
            overflow-y: scroll;
        }

        .list-types-carriers {
            height: auto !important;
        }

        /* width */
        .c5-select-list::-webkit-scrollbar {
            width: 8px;
        }

        /* Track */
        .c5-select-list::-webkit-scrollbar-track {
            border-radius: 18px;
        }

        /* Handle */
        .c5-select-list::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 18px;
        }

        /* Handle on hover */
        .c5-select-list::-webkit-scrollbar-thumb:hover {
            background: #064acb;
        }

        .c5-select-list li {
            margin-bottom: 10px;
            cursor: pointer;
            list-style: none;
        }

        .c5-select-dropdown-list {
            padding: 8px 10px 8px 10px;
            border: 1px solid #eee;
            -webkit-border: 1px solid #eee;
            border-radius: 3px;
            -webkit-border-radius: 3px;
            cursor: pointer;
            margin-bottom: 0px;
            display: flex;
            justify-content: flex-start;
            overflow: hidden;
            white-space: nowrap;
            height: 35px;
            background-color: #f6f6f6;
        }

        .m-input.date {
            background-color: #f6f6f6;
        }

        .c5-select-dropdown-list li {
            list-style: none;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            float: right;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 5px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .switch input:checked+.slider {
            background-color: #2196F3;
        }

        .switch input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        .switch input:checked+.slider:before {
            -webkit-transform: translateX(18px);
            -ms-transform: translateX(18px);
            transform: translateX(18px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .c5-label {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 14px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default checkbox */
        .c5-label input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            -webkit-border-radius: 3px;
        }

        /* On mouse-over, add a grey background color */
        .c5-label:hover input~.checkmark {
            background-color: #eee;
        }

        /* When the checkbox is checked, add a blue background */
        .c5-label input:checked~.checkmark {
            background-color: #2196F3;
            border: 1px solid #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .c5-label input:checked~.checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .c5-label .checkmark:after {
            left: 7px;
            top: 4px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .c5-case:hover {
            background: #fbfbfb;
        }

        .hidden-air {
            display: none;
        }

        .list-group2 {
            height: auto !important;
        }

        .h-hidden {
            display: none;
        }

        .border-select {
            border-color: #716aca;
        }

        .select2-selection,
        .pac-target-input {
            background-color: #f6f6f6 !important;
        }

        .hida {
            display: none;
        }

        .m-wizard.m-wizard--1.m-wizard--success .m-wizard__steps .m-wizard__step.m-wizard__step--done .m-wizard__step-info .m-wizard__step-number>span {
            background-color: #0072fc;
        }

        .m-wizard.m-wizard--1.m-wizard--success .m-wizard__steps .m-wizard__step.m-wizard__step--current .m-wizard__step-info .m-wizard__step-number>span {
            background-color: rgba(0, 114, 252, 0.70);
        }

        .m-wizard.m-wizard--1 .m-wizard__head .m-wizard__nav .m-wizard__steps .m-wizard__step .m-wizard__step-info .m-wizard__step-number>span {
            width: 3rem;
            height: 3rem;
        }

        .m-wizard.m-wizard--1 .m-wizard__head .m-wizard__nav .m-wizard__steps .m-wizard__step .m-wizard__step-info .m-wizard__step-number>span>span {
            font-size: 16px;
        }

        .m-wizard.m-wizard--1.m-wizard--success .m-wizard__progress .progress .progress-bar:after {
            background-color: #0172fc;
        }

        /* estilos */

    </style>
@endsection

@section('title', 'Quotes')
@section('content')
    <br>
    {!! Form::open(['id' => 'FormQuote', 'class' => 'form-group m-form__group dfw']) !!}
    <div class="padding">
        <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <br>
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-12 order-2 order-xl-1">
                                <div class="alert alert-info" role="alert">
                                    You are using the deprecated search module, will be available until 2021-10-22 , a new experience is now available. <a
                                        href="{{ url('/api/search') }}" class="alert-link" style="color:#5E696E ">Try
                                        out
                                        the new module.</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div>
                            <div class="row">
                                <div class="col-lg-1">
                                    <label>Type</label>
                                    {{ Form::select('type', ['1' => 'FCL', '2' => 'LCL'], null, ['id' => 'quoteType', 'class' => 'm-select2-general form-control']) }}
                                </div>
                                <div class="col-lg-1">
                                    <label>Direction</label>
                                    {{ Form::select('mode', ['1' => 'Export', '2' => 'Import'], @$form['mode'], ['id' => 'mode', 'placeholder' => 'Select', 'class' => 'm-select2-general form-control', 'required' => 'true']) }}
                                </div>
                                <div class="col-lg-2" id="equipment_id">
                                    <label>Equipment</label>
                                    {{ Form::select('equipment[]', $contain, @$form['equipment'], ['class' => 'c5-select-multiple select-group', 'id' => 'equipment', 'multiple' => 'multiple', 'required' => 'true', 'select-type' => 'groupLabel']) }}
                                    <!-- {{ Form::select('equipment[]', ['Types' => $group_contain, 'Equipment List' => $contain], @$form['equipment'], ['class' => 'c5-select-multiple select-group', 'id' => 'equipment', 'multiple' => 'multiple', 'required' => 'true', 'select-type' => 'groupLabel']) }} -->
                                </div>


                                <div class="col-lg-2">
                                    <label>Company</label>
                                    {{ Form::hidden('company_id_num', @$form['company_id_quote'], ['id' => 'company_id_num']) }}
                                    <div class="m-input-icon m-input-icon--right">
                                        <select id="company_dropdown" name="company_id_quote"
                                            class="company_dropdown form-control">
                                            @if ($company_dropdown)
                                                @foreach ($company_dropdown as $key => $value)
                                                    <option value="{{ $key }}" selected>{{ $value }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="m-input-icon__icon m-input-icon__icon--right">
                                            <span>
                                                <a onclick="AbrirModal('add',0)" data-container="body"
                                                    data-toggle="m-tooltip" data-placement="top" title=""
                                                    data-original-title="Add new company"> <i
                                                        class="la  la-plus-circle btn-plus__form"
                                                        style="color:blue; font-size: 18px;"></i> </a>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label>Contact</label>
                                    <div class="m-input-icon m-input-icon--right">
                                        {{ Form::select('contact_id', [], null, ['id' => 'contact_id', 'class' => 'm-select2-general form-control']) }}
                                        {{ Form::hidden('contact_id_num', @$form['contact_id'], ['id' => 'contact_id_num']) }}
                                        <span class="m-input-icon__icon m-input-icon__icon--right">
                                            <span>
                                                <a onclick="AbrirModal('addContact',0)" data-container="body"
                                                    data-toggle="m-tooltip" data-placement="top" title=""
                                                    data-original-title="Add new contact"> <i
                                                        class="la  la-plus-circle btn-plus__form"
                                                        style="color:blue; font-size: 18px;"></i></a>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <label>Price level</label>
                                    {{ Form::select('price_id', @$pricesG, null, ['id' => 'price_', 'placeholder' => 'Select at option', 'class' => 'form-control m-select2-general']) }}
                                    {{ Form::hidden('price_id_num', @$form['price_id'], ['id' => 'price_id_num']) }}
                                </div>
                                <div class="col-lg-2" id="delivery_type_label">
                                    <label>Delivery type</label>
                                    {{ Form::select('delivery_type', ['1' => 'PORT To PORT', '2' => 'PORT To DOOR', '3' => 'DOOR To PORT', '4' => 'DOOR To DOOR'], @$form['delivery_type'], ['class' => 'm-select2-general form-control', 'id' => 'delivery_type']) }}
                                </div>
                                <div class="col-lg-4 hidden-air" id="delivery_type_air_label">
                                    <label>Delivery type</label>
                                    {{ Form::select('delivery_type_air', ['5' => 'AIRPORT To AIRPORT', '6' => 'AIRPORT To DOOR', '7' => 'DOOR To AIRPORT', '8' => 'DOOR To DOOR'], null, ['class' => 'm-select2-general form-control', 'id' => 'delivery_type_air']) }}
                                </div>


                            </div><br>
                            <div class="row">
                                <div class="{{ $origenClass }}" id="origin_port">
                                    <div id="origin_harbor_label">
                                        <label>Origin port</label>
                                        <select id="origin_harbor" name="originport[]" class="portharbors form-control"
                                            multiple="true" required>
                                            @if (@$form['originport'] != null)
                                                @foreach (@$form['originport'] as $origin)

                                                    <option value="{{ $origin }}" selected="selected">
                                                        {{ $harbors[$origin] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <!-- {{ Form::select('originport[]', $harbors, @$form['originport'], ['class' => 'm-select2-general form-control', 'multiple' => 'multiple', 'id' => 'origin_harbor', 'required' => 'true']) }} -->

                                    </div>

                                    <div id="origin_airport_label" style="display:none;">
                                        <label>Origin airport</label>
                                        <select id="origin_airport" name="origin_airport_id"
                                            class="form-control"></select>
                                    </div>

                                </div>
                                <div class="col-lg-2 {{ $hideO }}" id="origin_address_label">
                                    <label>Origin address</label>
                                    {{ Form::hidden('origComb', @$form['originA'], ['id' => 'origComb']) }}
                                    <span id='selectA' class='{{ @$origA[ocultarorigComb] }}'>
                                        {{ Form::select('originA', [], null, ['id' => 'originA', 'placeholder' => 'Select', 'class' => 'm-select2-general form-control']) }}</span>
                                    <span id='textA'
                                        class='{{ @origA[ocultarDestA] }} hide'>{!! Form::text('origin_address', @$form['origin_address'], [
    'placeholder' => 'Please enter a origin address',
    'class' => 'form-control m-input
                                    ',
    'id' => 'origin_address',
]) !!}</span>

                                </div>
                                <div class="{{ $destinationClass }}" id="destination_port">
                                    <div id="destination_harbor_label">
                                        <label>Destination port</label>
                                        <select id="destination_harbor" name="destinyport[]"
                                            class="portharbors form-control" multiple="true" required>
                                            @if (@$form['destinyport'] != null)
                                                @foreach (@$form['destinyport'] as $origin)
                                                    <option value="{{ $origin }}" selected="selected">
                                                        {{ $harbors[$origin] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <!-- {{ Form::select('destinyport[]', $harbors, @$form['destinyport'], ['class' => 'm-select2-general form-control', 'multiple' => 'multiple', 'id' => 'destination_harbor', 'required' => 'true']) }} -->
                                    </div>
                                    <div id="destination_airport_label" style="display:none;">
                                        <label>Destination airport</label>
                                        <select id="destination_airport" name="destination_airport_id"
                                            class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-lg-2 {{ $hideD }}" id="destination_address_label">
                                    <label>Destination address</label>
                                    {{ Form::hidden('destComb', @$form['destinationA'], ['id' => 'destComb']) }}
                                    <span id='selectD'
                                        class='{{ @$destA[ocultarDestComb] }}'>{{ Form::select('destinationA', [], null, ['id' => 'destinationA', 'placeholder' => 'Select', 'class' => 'm-select2-general form-control']) }}</span>
                                    <span id='textD'
                                        class='{{ @$destA[ocultarDestA] }}hide'>{!! Form::text('destination_address', @$form['destination_address'], [
    'placeholder' => 'Please enter a destination address',
    'class' => 'form-control
                                    m-input',
    'id' => 'destination_address',
]) !!}</span>

                                </div>

                                <div class="col-lg-2">
                                    <label>Date</label>
                                    <div class="input-group date">
                                        {!! Form::text('date', @$form['date'], ['id' => 'm_daterangepicker_1', 'placeholder' => 'Select date', 'class' => 'form-control m-input date', 'required' => 'true', 'autocomplete' => 'off']) !!}
                                        {!! Form::text('date_hidden', null, ['id' => 'date_hidden', 'hidden' => 'true']) !!}

                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <br>


                                </div>

                                <div class="col-lg-2" id="carriers">
                                    <label>Carriers</label>
                                    {{ Form::select('carriers[]', ['CMA' => @$chargeAPI, 'MAERSK' => @$chargeAPI_M, 'SAFMARINE' => $chargeAPI_SF, 'Carriers' => $carrierMan], $carriersSelected, ['class' => 'c5-select-multiple select-normal', 'id' => 'carrier_select', 'multiple' => 'multiple', 'select-type' => 'multiple']) }}
                                </div>

                                <div class="col-lg-4 for-check">
                                    {{ Form::checkbox('chargeOrigin', null, @$chargeOrigin, ['id' => 'mode1', 'class' => 'include-checkbox']) }}
                                    <label for="mode1" class="label-check">Include origin charges</label>
                                </div>
                                <div class="col-lg-4 for-check">
                                    {{ Form::checkbox('chargeDestination', null, @$chargeDestination, ['id' => 'mode2', 'class' => 'include-checkbox']) }}
                                    <label for="mode2" class="label-check">Include destination charges</label>
                                </div>
                                <!-- <div class="col-lg-2 for-check">
                        {{ Form::checkbox('chargeFreight', null, @$chargeFreight, ['id' => 'mode3', 'class' => 'include-checkbox']) }}
                        <label for="mode3" class="label-check">Include freight charges</label>
                      </div> -->
                            </div>
                            <div class="row">
                                <!--VEEEEEEEEEEER AQUIIIIIIIIIIIIIIIIIIII -->

                                <!--<div class="col-lg-2 for-check" id="cmadiv">
                        {{ Form::checkbox('chargeAPI', null, @$chargeAPI, ['id' => 'mode4', 'class' => 'include-checkbox']) }}
                        <label for="mode4" class="label-check">Include CMA CGM Price Finder</label>
                      </div>
                      <div class="col-lg-2 for-check" id="maerskdiv">
                        {{ Form::checkbox('chargeAPI_M', null, @$chargeAPI_M, ['id' => 'mode5', 'class' => 'include-checkbox']) }}
                        <label for="mode5" class="label-check">Include MAERSK Spot</label>
                      </div>
                      <div class="col-lg-2 for-check" id="safmarinediv">
                {{ Form::checkbox('chargeAPI_SF', null, @$chargeAPI_SF, ['id' => 'mode6', 'class' => 'include-checkbox']) }}
                <label for="mode6" class="label-check">Include SAFMARINE Price Finder</label>
               </div>-->

                            </div><br>
                            <div class="form-group m-form__group row" id="lcl_air_load"
                                style="display: none; margin-top:25px;">
                                <div class="col-lg-2">
                                    <label>
                                        <b>LOAD</b>
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <ul class="nav nav-tabs" role="tablist"
                                        style="text-transform: uppercase; letter-spacing: 1px;">
                                        <li class="nav-item">
                                            <a href="#tab_1_1" class="nav-link active" data-toggle="tab"
                                                style=" font-weight: bold;" onclick="change_tab(1)"> Calculate by total
                                                shipment </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#tab_1_2" class="nav-link" data-toggle="tab"
                                                style=" font-weight: bold;" onclick="change_tab(2)"> Calculate by packaging
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="tab_1_1">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>
                                                        Packages
                                                    </label>
                                                    <div class="m-bootstrap-touchspin-brand">
                                                        <div class="input-group">
                                                            <input type="number" id="total_quantity" value="1"
                                                                name="total_quantity" min="0" step="0.0001"
                                                                class="total_quantity form-control" placeholder=""
                                                                aria-label="...">
                                                            <div class="input-group-btn">
                                                                <select class="form-control" id="type_cargo"
                                                                    name="cargo_type" style="height:2.5em">
                                                                    <option value="1">Pallets</option>
                                                                    <option value="2">Packages</option>
                                                                </select>
                                                            </div><!-- /btn-group -->
                                                        </div><!-- /input-group -->
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>
                                                        Total weight
                                                    </label>
                                                    <div class="m-bootstrap-touchspin-brand">
                                                        <div class="input-group">
                                                            <input type="number" id="total_weight" value="1"
                                                                name="total_weight" min="0" step="0.0001"
                                                                class="total_weight form-control" placeholder=""
                                                                aria-label="...">
                                                            <div class="input-group-btn">
                                                                <button type="button"
                                                                    class="btn btn-default dropdown-toggle"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false" style="height:2.5em">KG <span
                                                                        class="caret"></span></button>
                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                </ul>
                                                            </div><!-- /btn-group -->
                                                        </div><!-- /input-group -->
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>
                                                        Total volume
                                                    </label>
                                                    <div class="m-bootstrap-touchspin-brand">
                                                        <div class="input-group">
                                                            <input type="number" id="total_volume" value="1"
                                                                name="total_volume" min="0" step="0.0001"
                                                                class="total_volume form-control" placeholder=""
                                                                aria-label="...">
                                                            <div class="input-group-btn">
                                                                <button type="button"
                                                                    class="btn btn-default dropdown-toggle"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false" style="height:2.5em">M<sup>3</sup>
                                                                    <span class="caret"></span></button>
                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                </ul>
                                                            </div><!-- /btn-group -->
                                                        </div><!-- /input-group -->
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <br>
                                                    <br>
                                                    <b>Chargeable weight:</b>
                                                    <span id="chargeable_weight_total"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab_1_2">
                                            <div class="template">
                                                <div class="row">
                                                    <div class="col-md-2" style="padding-right:0px;">
                                                        <select name="type_load_cargo[]"
                                                            class="type_cargo type_cargo_2 form-control size-12px"
                                                            style="height: 2.7em">
                                                            <option value="">Choose an option</option>
                                                            <option value="1">Pallets</option>
                                                            <option value="2">Packages</option>
                                                        </select>
                                                        <input type="hidden" id="total_pallets" name="total_pallets" />
                                                        <input type="hidden" id="total_packages" name="total_packages" />
                                                    </div>
                                                    <div class="col-md-2" style="padding-right:0px;">
                                                        <input id="quantity" min="1" value="" name="quantity[]"
                                                            class="quantity quantity_2 form-control size-12px" type="number"
                                                            placeholder="quantity" />
                                                    </div>
                                                    <div class="col-md-5" style="padding-right:0px;">
                                                        <div class="btn-group btn-group-justified" role="group"
                                                            aria-label="...">
                                                            <div class="btn-group" role="group"
                                                                style="margin-right: 15px;">
                                                                <input class="height form-control size-12px height_2"
                                                                    min="0" name="height[]" id="height" type="number"
                                                                    placeholder="H" />
                                                            </div>
                                                            <div class="btn-group" role="group"
                                                                style="margin-right: 15px;">
                                                                <input class="width form-control size-12px width_2" min="0"
                                                                    name="width[]" id="width" type="number"
                                                                    placeholder="W" />
                                                            </div>
                                                            <div class="btn-group" role="group"
                                                                style="margin-right: 15px;">
                                                                <input class="large form-control size-12px large_2" min="0"
                                                                    name="large[]" id="large" type="number"
                                                                    placeholder="L" />
                                                            </div>
                                                            <div class="btn-group" role="group">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <button
                                                                            class="btn btn-default dropdown-toggle dropdown-button"
                                                                            type="button" data-toggle="dropdown"
                                                                            style="padding:0.45em 0.65em">
                                                                            <span class="xs-text size-12px">CM</span> <span
                                                                                class="caret"></span>
                                                                        </button>
                                                                        <ul class="dropdown-menu" role="menu">

                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="input-group">
                                                            <input type="number" id="weight" name="weight[]" min="0"
                                                                step="0.0001"
                                                                class="weight weight_2 form-control size-12px"
                                                                placeholder="Weight" aria-label="...">
                                                        </div><!-- /input-group -->
                                                    </div>
                                                    <div class="col-md-1">
                                                        <input type="hidden" class="volume_input" id="volume_input"
                                                            name="volume[]" />
                                                        <input type="hidden" class="quantity_input" id="quantity_input"
                                                            name="total_quantity_pkg" />
                                                        <input type="hidden" class="weight_input" id="weight_input"
                                                            name="total_weight_pkg" />
                                                    </div>
                                                    <div class="col-md-4">

                                                        <p class=""><span class="quantity"></span>
                                                            <span class="volume"></span> <span
                                                                class="weight"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="template hide" id="lcl_air_load_template">
                                                <div class="row" style="padding-top: 15px;">
                                                    <div class="col-md-2" style="padding-right:0px;">
                                                        <select name="type_load_cargo[]"
                                                            class="type_cargo form-control size-12px"
                                                            style="height: 2.7em">>
                                                            <option value="">Choose an option</option>
                                                            <option value="1">Pallets</option>
                                                            <option value="2">Packages</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2" style="padding-right:0px;">
                                                        <input id="quantity" min="1" value="" name="quantity[]"
                                                            class="quantity form-control size-12px" type="number"
                                                            placeholder="quantity" />
                                                    </div>
                                                    <div class="col-md-5" style="padding-right:0px;">
                                                        <div class="btn-group btn-group-justified" role="group"
                                                            aria-label="...">
                                                            <div class="btn-group" role="group"
                                                                style="margin-right: 15px;">
                                                                <input class="height form-control size-12px" min="0"
                                                                    name="height[]" id="al" type="number" placeholder="H" />
                                                            </div>
                                                            <div class="btn-group" role="group"
                                                                style="margin-right: 15px;">
                                                                <input class="width form-control size-12px" min="0"
                                                                    name="width[]" id="an" type="number" placeholder="W" />
                                                            </div>
                                                            <div class="btn-group" role="group"
                                                                style="margin-right: 15px;">
                                                                <input class="large form-control size-12px" min="0"
                                                                    name="large[]" id="la" type="number" placeholder="L" />
                                                            </div>
                                                            <div class="btn-group" role="group">
                                                                <div class="input-group-btn">
                                                                    <div class="btn-group">
                                                                        <button
                                                                            class="btn btn-default dropdown-toggle dropdown-button"
                                                                            type="button" data-toggle="dropdown"
                                                                            style="padding:0.45em 0.65em">
                                                                            <span class="xs-text size-12px">CM</span> <span
                                                                                class="caret"></span>
                                                                        </button>
                                                                        <ul class="dropdown-menu" role="menu">

                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="input-group">
                                                            <input type="number" name="weight[]" min="0" step="0.0001"
                                                                class="weight form-control size-12px" placeholder="Weight"
                                                                aria-label="...">
                                                        </div><!-- /input-group -->
                                                    </div>
                                                    <div class="col-md-1">
                                                        <a class="remove_lcl_air_load" style="cursor: pointer;"><i
                                                                class="fa fa-trash"></i></a>
                                                        <input type="hidden" class="volume_input" id="volume_input"
                                                            name="volume[]" />
                                                        <input type="hidden" class="quantity_input" id="quantity_input"
                                                            name="total_quantity_pkg" />
                                                        <input type="hidden" class="weight_input" id="weight_input"
                                                            name="total_weight_pkg" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <br>
                                                        <p class=""><span class="quantity"></span>
                                                            <span class="volume"></span> <span
                                                                class="weight"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">

                                                    <div id="saveActions" class="form-group">
                                                        <div class="btn-group">
                                                            <button type="button" id="add_load_lcl_air"
                                                                class="add_load_lcl_air btn btn-info btn-sm">
                                                                <span class="fa fa-plus" role="presentation"
                                                                    aria-hidden="true"></span> &nbsp;
                                                                <span>Add load</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">

                                                    <b>Total: </b>
                                                    <span id="total_quantity_pkg"></span>
                                                    <span id="total_volume_pkg"></span>
                                                    <span id="total_weight_pkg"></span>
                                                    <input type="hidden" id="total_quantity_pkg_input"
                                                        name="total_quantity_pkg" />
                                                    <input type="hidden" id="total_volume_pkg_input"
                                                        name="total_volume_pkg" />
                                                    <input type="hidden" id="total_weight_pkg_input"
                                                        name="total_weight_pkg" />
                                                </div>
                                                <br>
                                                <br>
                                                <div class="col-md-8">
                                                    <b>Chargeable weight:</b>
                                                    <span id="chargeable_weight_pkg"></span>
                                                    <input type="hidden" id="chargeable_weight_pkg_input"
                                                        name="chargeable_weight" />
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div><br>

                            <div class="row">
                                <div class="col-lg-12 no-padding">
                                    <div class="row  justify-content-between">
                                        <div class="col-lg-9 d-flex message  align-items-end align-self-end">
                                            @if (isset($validateEquipment))
                                                @if ($validateEquipment['count'] > 1)
                                                    <p class="warning-p"><span><i class="la la-info-circle"></i>The
                                                            equipemts do not
                                                            belong to the same group.</span> You can create a quote
                                                        manually.</p>
                                                @endif
                                            @endif
                                            @if (isset($arreglo) && isset($validateEquipment))
                                                @if ($arreglo->isEmpty() && $validateEquipment['count'] < 2)
                                                    <p class="warning-p"><span><i class="la la-info-circle"></i>No
                                                            freight rates
                                                            were found for this trade route.</span> You can create a quote
                                                        manually.
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-lg-3 d-flex justify-content-end align-items-end" align='right'>
                                            <!-- <button type="button" class="btn m-btn--pill  btn-info quote_man">Create Manual Quote<span class="la la-arrow-right"></span>
        </button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <center>
                                        <button type="button"
                                            class="btn m-btn--pill  btn-search__quotes  btn-info quote_search"
                                            id="quote_search"><i class="flaticon-search-magnifier-interface-symbol"></i>
                                            &nbsp;Search</button>
                                        <button type="button"
                                            class="btn m-btn--pill  btn-search__quotes  btn-info quote_searching hide"
                                            id="quote_searching">Searching &nbsp;<i
                                                class="fa fa-spinner fa-spin"></i></button>
                                        <button type="button"
                                            class="btn m-btn--pill  btn-info btn-search__quotes create-manual"
                                            data-toggle="modal" data-target="#createContractModal" data-type="1"><i
                                                class="fa fa-plus"></i> Add Contract</span></button>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="padding">
        <div class="col-lg-12">
            <div class="tab-content">
                <div class="row">
                    <div class="col-lg-12" align='right'>
                        <button type="button" class="btn btn-link quote_man"><i class="fa fa-plus"></i> Create Quote
                            Manually</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}



    @if (isset($arreglo))
        @if (!$arreglo->isEmpty())
            <div class="row padding search">
                <div class="col-lg-12"><br><br><span class="col-txt">Results</span><br><br></div>
            </div>
            <div class="row padding search">
                <!-- Tabla de muestreo de las cotizaciones -->
                {!! Form::open(['class' => 'form-group m-form__group full-width', 'id' => 'rateForm']) !!}
                <!-- {!! Form::open(['route' => 'quote.store', 'class' => 'form-group m-form__group full-width']) !!} -->
                <input type="hidden" id="isDecimal" value="{{ $isDecimal }}">
                <input type="hidden" id="oculto" value="no">
                <input type="hidden" name="form" value="{{ json_encode($form) }}"
                    class="btn btn-sm btn-default btn-bold btn-upper formu">
                <div class="col-lg-12">
                    <div class="m-portlet no-shadow">
                        <div class="m-portlet__body no-padding">
                            <div class="tab-content">
                                <div>
                                    <!-- Empieza el card de filtro -->
                                    <div class="filter-table__quotes">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <!--<label>Sort By</label>-->
                                                <div class="input-group m-input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="la la-filter"></i></span>
                                                    </div>
                                                    <div>
                                                        <select class="form-control m-select2-general ">
                                                            <option value="AK">Asc</option>
                                                            <option value="AK">Desc</option>
                                                            <option value="AK">Minnor Price</option>
                                                            <option value="AK">Mayor Price</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" align='right'>
                                                <!--<button type="button" class="btn m-btn--pill btn-link" onclick="submitForm(1, 'FCL')"><b>Create Quote</b></button>-->
                                                <button type="button" id="button_new_quote"
                                                    class="btn m-btn--pill btn-info tool_tip" data-toggle="tooltip"
                                                    data-placement="top" onclick="submitForm(2, 'FCL')">
                                                    Create Quote
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <hr>
                                            </div>
                                        </div>
                                        <!-- Empieza columna titulos de la tabla -->

                                        <div class="row">
                                            <div class="col-lg-2"> <span class="portcss"> Carrier</span>
                                            </div>
                                            <div class="col-lg-10">
                                                <div class="row">
                                                    <div class="{{ $equipmentHides['head_1'] }}">
                                                        <div class="row">
                                                            <div class="col-lg-8" style="padding-left: 30px;"><span
                                                                    class="portcss">Origin</span></div>
                                                            <div class="col-lg-3" style="text-align: right"><span
                                                                    class="portcss">Destination</span></div>
                                                        </div>
                                                    </div>
                                                    <div class="{{ $equipmentHides['head_2'] }} padding-right-table"
                                                        style="padding-left: 0;">
                                                        <div style="display:flex; justify-content:flex-end">
                                                            @foreach ($containers as $container)
                                                                <div class="wth"
                                                                    style="display:flex; justify-content:center;"
                                                                    {{ $equipmentHides[$container->code] }}><span
                                                                        class="portcss">{{ $container->code }}</span>
                                                                </div>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Termina el card de filtro -->
                                    <div class="row">
                                        <div class="col-lg-12"><br><br></div>
                                    </div>
                                    @foreach ($arreglo as $arr)
                                        <!-- Empieza tarjeta de cotifzacion -->
                                        <div class="card-p__quotes input-select{{ $loop->iteration }}"
                                            style="margin-bottom: 50px;">
                                            <div class="row initial-card" id='principal{{ $loop->iteration }}'>
                                                <div class="col-lg-2 d-flex align-items-center img-bottom-border">
                                                    <div class="m-widget5">
                                                        <div class="m-widget5__item no-padding no-margin">
                                                            <div class="m-widget5__pic">
                                                                <img src="http://cargofive-production-21.s3.eu-central-1.amazonaws.com/imgcarrier/{{ $arr->carrier->image }}"
                                                                    alt="" title="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-10 b-left info-bottom-border">
                                                    <div class="row">
                                                        <div
                                                            class="{{ $equipmentHides['head_1'] }} no-padding padding-min-col ">
                                                            <div class="row">
                                                                <div class="col-lg-4">
                                                                    <span class="portcss">
                                                                        {{ $arr->port_origin->name }}</span><br>
                                                                    <span class="portalphacode">
                                                                        {{ $arr->port_origin->code }}</span>
                                                                </div>
                                                                <div
                                                                    class="col-lg-4 d-flex flex-column justify-content-center">
                                                                    @if ($arr->via != '')
                                                                        <span style="color:#2e0084; text-align: center">Via
                                                                            :</span>
                                                                        <span
                                                                            style="color:#1d3b6e; text-align: center">{{ $arr->via }}</span>

                                                                    @endif
                                                                    <div class="progress m-progress--sm">
                                                                        <div class="progress-bar {{ $arr->contract_color }} "
                                                                            role="progressbar" style="width: 100%;"
                                                                            aria-valuenow="100" aria-valuemin="0"
                                                                            aria-valuemax="100"></div>
                                                                    </div>
                                                                    @if ($arr->transit_time != '')
                                                                        <span
                                                                            style="color:#2e0084; text-align: center">Transit
                                                                            Time:</span>
                                                                        <span
                                                                            style="color:#1d3b6e; text-align: center">{{ $arr->transit_time }}
                                                                            Days</span>

                                                                    @endif


                                                                </div>
                                                                <div
                                                                    class="col-lg-4 d-flex align-items-center flex-column">
                                                                    <span class="portcss">
                                                                        {{ $arr->port_destiny->name }}</span>
                                                                    <span class="portalphacode">
                                                                        {{ $arr->port_destiny->code }}</span>
                                                                </div>
                                                            </div>
                                                            <br>
                                                        </div>

                                                        <div class="{{ $equipmentHides['head_2'] }}"
                                                            style="padding-right: 35px;">
                                                            <div class="table-r__quotes"
                                                                style="display: flex; justify-content: flex-end">
                                                                @foreach ($containers as $container)
                                                                    <div class="wth "
                                                                        {{ $equipmentHides[$container->code] }}><span
                                                                            class="darkblue validate tot{{ $container->code }}-{{ $arr->id }}">
                                                                            {{ isDecimal($arr->{'totalT' . $container->code}) }}
                                                                        </span><span class="currency"
                                                                            style="margin-left:5px">
                                                                            {{ $arr->typeCurrency }}</span></div>
                                                                    <input type='hidden'
                                                                        id='tot{{ $container->code }}-{{ $arr->id }}'
                                                                        value="{{ $arr->{'totalT' . $container->code} }}">

                                                                @endforeach


                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-lg-1 no-padding d-flex align-items-center justify-content-end pos-btn">
                                                            <input type="checkbox"
                                                                id="input-select{{ $loop->iteration }}"
                                                                class="input-select no-check btnrate checkboxx"
                                                                rate-id='{{ $arr->id }} infocheck' name="info[]"
                                                                value="{{ json_encode($arr) }}">
                                                            <label for="input-select{{ $loop->iteration }}"
                                                                class="btn-input__select btnrate select-class selected"
                                                                rate-id='{{ $arr->id }}' Select></label>
                                                        </div>
                                                        <div class="col-lg-12 b-top no-padding padding-min">
                                                            <div class="row justify-content-between">

                                                                @if (!empty($arr->remarks) || (!empty($arr->remarksG) && $arr->remarksG != '    ' && $arr->remarksG != '<br>    '))
                                                                    <div class="col-lg-1">
                                                                        <div class="btn-detail__quotes btn-remarks">
                                                                            <a id='display_r{{ $loop->iteration }}'
                                                                                onclick="display_r({{ $loop->iteration }})"
                                                                                class="l btn-nowrap" title="Remarks">
                                                                                <span class="workblue">Remarks</span>
                                                                                <i class="la la-angle-down blue"></i></a>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if (isset($arr->sheduleType))
                                                                    <div class="col-lg-9 d-flex align-items-center justify-content-start"
                                                                        style="padding-left: 60px;">
                                                                        <span class="portalphacode"
                                                                            style="margin-right:15px;">Validity:
                                                                        </span>
                                                                        {{ \Carbon\Carbon::parse($arr->contract->validity)->format('d M Y') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($arr->contract->expire)->format('d M Y') }}
                                                                    </div>
                                                                @else
                                                                    <div class="col-lg-9 d-flex align-items-center">
                                                                        <span class="portalphacode"
                                                                            style="margin-right:15px;">Validity:
                                                                        </span>
                                                                        {{ \Carbon\Carbon::parse($arr->contract->validity)->format('d M Y') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($arr->contract->expire)->format('d M Y') }}

                                                                        @if ($arr->contratoFuturo)
                                                                            &nbsp;<img
                                                                                src="{{ url('images/error.svg') }}"
                                                                                width="20" height="20"
                                                                                title="Attention! This rate is valid for a date range later than the one selected">
                                                                        @endif

                                                                        <span class="portalphacode"
                                                                            style="margin-left: 20px; margin-right: 10px; text-align: right">Contract:
                                                                        </span> <span
                                                                            title="{{ $arr->contract->name }}">{{ $arr->contract->name }}</span>
                                                                        {{ $arr->contract->number }}
                                                                    </div>
                                                                @endif



                                                                @if (isset($arr->sheduleType))
                                                                    <!--<div class="col-lg-2 d-flex align-items-center">
                                  <span class="portalphacode" style="margin-right:5px;">Schedule Type: </span> {{ $arr->sheduleType }}
                                </div>
                                <div class="col-lg-1 d-flex align-items-center">
                                  <span class="portalphacode" style="margin-right:15px; white-space:nowrap">  TT:  </span>  {{ $arr->transit_time }}
                                </div>
                                <div class="col-lg-2 d-flex align-items-center">
                                  <span class="portalphacode" style="margin-right:15px; white-space:nowrap"> >  Via: </span> {{ $arr->via }}
                                </div>-->
                                                                @endif
                                                                <!-- <div class="col-lg-3 d-flex align-items-center justify-content-start">
                                                            
                                                        </div> -->

                                                                <div
                                                                    class="col-lg-1 no-padding d-flex justify-content-end align-items-center">
                                                                    @if ($arr->excelRequest != '0' || $arr->excelRequestFCL != '0' || $arr->totalItems != '0')
                                                                        <div class="downexcel"
                                                                            style="margin-right: 10px;">

                                                                            <a id='excel_l{{ $loop->iteration }}'
                                                                                href="#"
                                                                                onclick="downlodRequest({{ $arr->excelRequest }},{{ $arr->excelRequestFCL }},{{ $arr->idContract }})"
                                                                                class="l detailed-cost" title="Download">
                                                                                <span class="workgreen"><i
                                                                                        class="icon-excel"></i></span>

                                                                                <i class="la la-file-excel-o"></i>
                                                                            </a>

                                                                        </div>
                                                                    @endif
                                                                    <div class="btn-detail__quotes btn-d">
                                                                        <a id='display_l{{ $loop->iteration }}'
                                                                            onclick="display({{ $loop->iteration }})"
                                                                            class="l detailed-cost btn-nowrap"
                                                                            title="Details">
                                                                            <span class="workblue">Detailed
                                                                                Cost</span>
                                                                            <i class="la la-angle-down blue"></i></a>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Termina tarjeta de cotifzacion -->

                                            <!-- Gastos Origen-->

                                            @if (!$arr->localorigin->isEmpty())

                                                <div class="row no-margin margin-card" id='origin{{ $loop->iteration }}'
                                                    hidden='true'>
                                                    <div class="col-lg-12">
                                                        <div class="row">
                                                            <span class="darkblue cabezeras">Origin</span><br><br>
                                                        </div>
                                                        <div class="row bg-light">
                                                            <div class="col-lg-2"><span
                                                                    class="portalphacode">Charge</span></div>
                                                            <div class="col-lg-2"><span class="portalphacode"
                                                                    style="margin-left: 20px">Detail</span></div>
                                                            <div class="col-lg-7">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)
                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}><span
                                                                                class="portalphacode">{{ $container->code }}
                                                                            </span></div>
                                                                    @endforeach

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode">Currency</span></div>
                                                        </div>

                                                        @foreach ($arr->localorigin as $localorigin)

                                                            <div class="row data-rates">
                                                                <div class="col-lg-2 colorphacode">
                                                                    {{ str_replace(['[', ']', "\""], ' ', $localorigin['99']->pluck('surcharge_name')) }}
                                                                </div>
                                                                <div class="col-lg-2 colorphacode"
                                                                    style="padding-left: 35px">
                                                                    {{ str_replace(['[', ']', "\""], ' ', $localorigin['99']->pluck('calculation_name')) }}
                                                                </div>
                                                                <div class="col-lg-7 colorphacode">
                                                                    <div class="d-flex justify-content-end">
                                                                        @foreach ($containers as $container)
                                                                            <div class="wth"
                                                                                {{ $equipmentHides[$container->code] }}>
                                                                                <span class="bg-rates">
                                                                                    {{ isset($localorigin[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localorigin[$container->code]->pluck('monto'))) : isDecimal('0.00') }}</span>
                                                                                <span class="bg-rates">+
                                                                                    {{ isset($localorigin[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localorigin[$container->code]->pluck('markup'))) : isDecimal('0.00') }}
                                                                                </span><i
                                                                                    class="la la-caret-right arrow-down"></i>
                                                                                <b class="monto-down">
                                                                                    {{ isset($localorigin[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localorigin[$container->code]->pluck('montoMarkup'))) : isDecimal('0.00') }}</b>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-1"><span
                                                                        class="colorphacode">{{ str_replace(['[', ']', "\""], ' ', $localorigin['99']->pluck('currency')) }}</span>
                                                                </div>
                                                                <div class="col-lg-1"></div>

                                                            </div>
                                                        @endforeach
                                                        <div class="row bg-light">
                                                            <div class="col-lg-4 col-lg-offset-"><span
                                                                    class="portalphacode">Subtotal Origin
                                                                    Charges</span></div>
                                                            <div class="col-lg-7">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)

                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}>
                                                                            <span
                                                                                class="portalphacode">{{ isDecimal($arr->{'tot' . $container->code . 'O'}) }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode">{{ $arr->typeCurrency }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <!-- Gastos Freight-->
                                            <div class="row no-margin margin-card" id='freight{{ $loop->iteration }}'
                                                hidden='true'>
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        <span class="darkblue cabezeras">Freight</span><br><br>
                                                    </div>
                                                    <div class="row bg-light">
                                                        <div class="col-lg-2"><span
                                                                class="portalphacode">Charge</span></div>
                                                        <div class="col-lg-2"><span class="portalphacode"
                                                                style="margin-left: 20px">Detail</span></div>
                                                        <div class="col-lg-7">
                                                            <div class="d-flex justify-content-end">
                                                                @foreach ($containers as $container)
                                                                    <div class="wth"
                                                                        {{ $equipmentHides[$container->code] }}><span
                                                                            class="portalphacode">{{ $container->code }}
                                                                        </span></div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-1"><span
                                                                class="portalphacode">Currency</span></div>
                                                    </div>
                                                    @foreach ($arr->rates as $rates)
                                                        <div class="row data-rates">
                                                            <div class="col-lg-2 colorphacode">{{ $rates['type'] }}
                                                            </div>
                                                            <div class="col-lg-2 colorphacode"
                                                                style="white-space: nowrap; padding-left: 35px">
                                                                {{ $rates['detail'] }}</div>
                                                            <div class="col-lg-7 colorphacode">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)
                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}>
                                                                            <span
                                                                                class="bg-rates">{{ isDecimal(@$rates['price' . $container->code]) }}</span>
                                                                            <span
                                                                                class="bg-rates">+{{ isDecimal(number_format(@$rates['markup' . $container->code], 2, '.', '')) }}</span>
                                                                            <i class="la la-caret-right arrow-down"></i> <b
                                                                                class="monto-down">{{ isDecimal(@$rates['monto' . $container->code]) }}</b>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1 colorphacode">
                                                                {{ $rates['currency_rate'] }}</div>
                                                        </div>

                                                    @endforeach
                                                    @foreach ($arr->localfreight as $localfreight)

                                                        <div class="row data-rates">
                                                            <div class="col-lg-2 colorphacode">
                                                                {{ str_replace(['[', ']', "\""], ' ', $localfreight['99']->pluck('surcharge_name')) }}
                                                            </div>
                                                            <div class="col-lg-2 colorphacode" style="padding-left: 35px">
                                                                {{ str_replace(['[', ']', "\""], ' ', $localfreight['99']->pluck('calculation_name')) }}
                                                            </div>
                                                            <div class="col-lg-7 colorphacode">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)
                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}>
                                                                            <span class="bg-rates">
                                                                                {{ isset($localfreight[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localfreight[$container->code]->pluck('monto'))) : isDecimal('0.00') }}</span><span
                                                                                class="bg-rates">+
                                                                                {{ isset($localfreight[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localfreight[$container->code]->pluck('markup'))) : isDecimal('0.00') }}</span>
                                                                            <i class="la la-caret-right arrow-down"></i> <b
                                                                                class="monto-down">
                                                                                {{ isset($localfreight[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localfreight[$container->code]->pluck('montoMarkup'))) : isDecimal('0.00') }}
                                                                            </b>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1 colorphacode">
                                                                {{ str_replace(['[', ']', "\""], ' ', $localfreight['99']->pluck('currency')) }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <div class="row bg-light">
                                                        <div class="col-lg-4 col-lg-offset-"><span
                                                                class="portalphacode">Subtotal
                                                                Freight Charges</span></div>
                                                        <div class="col-lg-7">
                                                            <div class="d-flex justify-content-end">
                                                                @foreach ($containers as $container)
                                                                    <div class="wth"
                                                                        {{ $equipmentHides[$container->code] }}><span
                                                                            class="portalphacode">{{ isDecimal($arr->{'tot' . $container->code . 'F'}) }}
                                                                        </span></div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-1"><span
                                                                class="portalphacode">{{ $rates['currency_rate'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Gastos Destino-->
                                            @if (!$arr->localdestiny->isEmpty())
                                                <div class="row no-margin margin-card"
                                                    id='destiny{{ $loop->iteration }}' hidden='true'>
                                                    <div class="col-lg-12">
                                                        <div class="row">
                                                            <span class="darkblue cabezeras">Destination</span><br><br>
                                                        </div>
                                                        <div class="row bg-light">
                                                            <div class="col-lg-2"><span
                                                                    class="portalphacode">Charge</span></div>
                                                            <div class="col-lg-2"><span class="portalphacode"
                                                                    style="margin-left: 20px">Detail</span></div>
                                                            <div class="col-lg-7">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)
                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}>
                                                                            <span
                                                                                class="portalphacode">{{ $container->code }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode">Currency</span></div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode"></span></div>
                                                        </div>
                                                        @foreach ($arr->localdestiny as $localdestiny)

                                                            <div class="row data-rates">
                                                                <div class="col-lg-2 colorphacode">
                                                                    {{ str_replace(['[', ']', "\""], ' ', $localdestiny['99']->pluck('surcharge_name')) }}
                                                                </div>
                                                                <div class="col-lg-2 colorphacode"
                                                                    style="padding-left: 35px">
                                                                    {{ str_replace(['[', ']', "\""], ' ', $localdestiny['99']->pluck('calculation_name')) }}
                                                                </div>
                                                                <div class="col-lg-7 colorphacode">
                                                                    <div class="d-flex justify-content-end">
                                                                        @foreach ($containers as $container)
                                                                            <div class="wth"
                                                                                {{ $equipmentHides[$container->code] }}>
                                                                                <span class="bg-rates">
                                                                                    {{ isset($localdestiny[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localdestiny[$container->code]->pluck('monto'))) : isDecimal('0.00') }}
                                                                                </span><span class="bg-rates"> +
                                                                                    {{ isset($localdestiny[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localdestiny[$container->code]->pluck('markup'))) : isDecimal('0.00') }}
                                                                                </span> <i
                                                                                    class="la la-caret-right arrow-down"></i>
                                                                                <b class="monto-down">{{ isset($localdestiny[$container->code]) ? isDecimal(str_replace(['[', ']', "\""], ' ', $localdestiny[$container->code]->pluck('montoMarkup'))) : isDecimal('0.00') }}
                                                                                </b>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-1"><span
                                                                        class="colorphacode">{{ str_replace(['[', ']', "\""], ' ', $localdestiny['99']->pluck('currency')) }}</span>
                                                                </div>
                                                                <div class="col-lg-1"></div>
                                                            </div>
                                                        @endforeach
                                                        <div class="row bg-light">
                                                            <div class="col-lg-4 col-lg-offset-"><span
                                                                    class="portalphacode">Subtotal
                                                                    Destination Charges</span></div>
                                                            <div class="col-lg-7">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)
                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}>
                                                                            <span
                                                                                class="portalphacode">{{ isDecimal($arr->{'tot' . $container->code . 'D'}) }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode">{{ $arr->typeCurrency }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif


                                            <!-- Gastos Inlands-->
                                            @if (!$arr->inlandDestiny->isEmpty() || !$arr->inlandOrigin->isEmpty())
                                                <div class="row no-margin margin-card"
                                                    id='inland{{ $loop->iteration }}' hidden='true'>
                                                    <div class="col-lg-12">
                                                        <div class="row">
                                                            <span class="darkblue cabezeras">Inlands</span><br><br>
                                                        </div>
                                                        <div class="row bg-light">
                                                            <div class="col-lg-2"><span
                                                                    class="portalphacode">Provider</span></div>
                                                            <div class="col-lg-1"><span class="portalphacode"
                                                                    style="margin-left: 20px">Distance</span></div>
                                                            <div class="col-lg-7">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)
                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}>
                                                                            <span
                                                                                class="portalphacode">{{ $container->code }}
                                                                                '</span>
                                                                        </div>
                                                                    @endforeach

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode">Currency</span></div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode">Select</span></div>
                                                        </div><br>
                                                        @if (!$arr->inlandDestiny->isEmpty())
                                                            <div class="row data-rates">
                                                                <div class="col-lg-12"> <span
                                                                        class="darkblue">Destiny</span><br><br></div>

                                                            </div>
                                                        @endif
                                                        @foreach ($arr->inlandDestiny as $inlandDestiny)

                                                            <div class="row data-rates">
                                                                <div class="col-lg-2 colorphacode">
                                                                    {{ $inlandDestiny['providerName'] }}</div>
                                                                <div class="col-lg-1 colorphacode"
                                                                    style="padding-left: 35px">
                                                                    {{ $inlandDestiny['km'] }} KM</div>
                                                                <div class="col-lg-7 colorphacode">
                                                                    <div class="d-flex justify-content-end">
                                                                        @foreach ($containers as $container)

                                                                            <div class="wth"
                                                                                {{ $equipmentHides[$container->code] }}>
                                                                                {{ $equipmentHides[$container->code] }}
                                                                                {{ @isDecimal($inlandDestiny['inlandDetails'][$container->code]['amount']) }}
                                                                                &nbsp;+<b
                                                                                    class="monto-down">{{ @isDecimal($inlandDestiny['inlandDetails'][$container->code]['markup']) }}</b>
                                                                                <i class="la la-caret-right"></i>

                                                                                <span class="bg-rates">
                                                                                    {{ isDecimal(@$inlandDestiny['inlandDetails'][$container->code]['amount'], 2, '.', '') }}
                                                                                </span>

                                                                                <span class="bg-rates hide"
                                                                                    id='valor-d{{ $container->code }}{{ $loop->parent->iteration }}-{{ $arr->id }}'>
                                                                                    {{ isDecimal(@$inlandDestiny['inlandDetails'][$container->code]['montoInlandT'], 2, '.', '') }}
                                                                                </span>
                                                                            </div>


                                                                        @endforeach


                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-1"><span
                                                                        class="colorphacode">{{ @$inlandDestiny['type_currency'] }}</span>
                                                                </div>
                                                                <div
                                                                    class="col-lg-1 no-padding d-flex align-items-center pos-btn">

                                                                    <label tabindex="0" role="button"
                                                                        data-toggle="m-tooltip" data-trigger="focus"
                                                                        title="You have to select the Rate in order to choose an Inland."
                                                                        data-content="You have to select the Rate in order to choose an Inland."
                                                                        data-inland="{{ $loop->iteration }}"
                                                                        data-rate="{{ $arr->id }}"
                                                                        class="btn-input__select-add d-flex  labelSelectDest{{ $arr->id }}  justify-content-center align-items-center visible__select-add add-click"
                                                                        style="background-color:transparent !important; color: #cecece !important;">Add</label>

                                                                    <input type="checkbox"
                                                                        id="inputID-select{{ $loop->iteration }}-{{ $arr->id }}"
                                                                        data-inland="{{ $loop->iteration }}"
                                                                        data-rate='{{ $arr->id }}'
                                                                        class="input-select inlands no-check "
                                                                        name="inlandD{{ $arr->id }}[]"
                                                                        value="{{ json_encode($inlandDestiny) }} ">

                                                                    <label
                                                                        for="inputID-select{{ $loop->iteration }}-{{ $arr->id }}"
                                                                        data-inland="{{ $loop->iteration }}"
                                                                        data-rate='{{ $arr->id }}'
                                                                        class="btn-input__select-add d-flex labelDest{{ $arr->id }} labelI labelI{{ $arr->id }}-{{ $loop->iteration }} justify-content-center align-items-center">Add</label>

                                                                </div>

                                                            </div><br>
                                                        @endforeach
                                                        @if (!$arr->inlandOrigin->isEmpty())
                                                            <div class="row data-rates">
                                                                <div class="col-lg-12"> <span
                                                                        class="darkblue">Origin</span><br><br></div>

                                                            </div>
                                                        @endif
                                                        @foreach ($arr->inlandOrigin as $inlandOrigin)

                                                            <div class="row data-rates">
                                                                <div class="col-lg-2 colorphacode">
                                                                    {{ $inlandOrigin['providerName'] }}</div>
                                                                <div class="col-lg-1 colorphacode"
                                                                    style="padding-left: 35px">
                                                                    {{ $inlandOrigin['km'] }} KM</div>

                                                                <div class="col-lg-7 colorphacode">
                                                                    <div class="d-flex justify-content-end">

                                                                        @foreach ($containers as $container)
                                                                            <div class="wth"
                                                                                {{ $equipmentHides[$container->code] }}>
                                                                                {{ $equipmentHides[$container->code] }}
                                                                                {{ @isDecimal($inlandOrigin['inlandDetails'][$container->code]['amount']) }}
                                                                                <i class="la la-caret-right"></i> <b
                                                                                    class="monto-down">
                                                                                    {{ @isDecimal($inlandOrigin['inlandDetails'][$container->code]['markup']) }}
                                                                                </b> <i class="la la-caret-right"></i>
                                                                                <span class="bg-rates">
                                                                                    {{ isDecimal(@$inlandOrigin['inlandDetails'][$container->code]['amount'], 2, '.', '') }}
                                                                                </span>

                                                                                <span class="bg-rates hide"
                                                                                    id='valor-o{{ $container->code }}{{ $loop->parent->iteration }}-{{ $arr->id }}'>
                                                                                    {{ isDecimal(@$inlandOrigin['inlandDetails'][$container->code]['montoInlandT'], 2, '.', '') }}
                                                                                </span>
                                                                            </div>

                                                                        @endforeach

                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-1"><span
                                                                        class="colorphacode">{{ @$inlandOrigin['type_currency'] }}</span>
                                                                </div>
                                                                <div
                                                                    class="col-lg-1 no-padding d-flex align-items-center pos-btn">
                                                                    <label tabindex="0" role="button"
                                                                        data-toggle="m-tooltip" data-trigger="focus"
                                                                        title="You have to select the Rate in order to choose an Inland."
                                                                        data-content="You have to select the Rate in order to choose an Inland."
                                                                        data-inland="{{ $loop->iteration }}"
                                                                        data-rate="{{ $arr->id }}"
                                                                        class="btn-input__select-add d-flex  labelSelectDest{{ $arr->id }}  justify-content-center align-items-center visible__select-add add-click"
                                                                        style="background-color:transparent !important; color: #cecece !important;">Add</label>

                                                                    <input type="checkbox"
                                                                        id="inputIO-select{{ $loop->iteration }}-{{ $arr->id }}"
                                                                        data-inland="{{ $loop->iteration }}"
                                                                        data-rate='{{ $arr->id }}'
                                                                        class="input-select inlandsO no-check"
                                                                        name="inlandO{{ $arr->id }}[]"
                                                                        value="{{ json_encode($inlandOrigin) }}">

                                                                    <label
                                                                        for="inputIO-select{{ $loop->iteration }}-{{ $arr->id }}"
                                                                        data-inland="{{ $loop->iteration }}"
                                                                        data-rate='{{ $arr->id }}'
                                                                        class="btn-input__select-add d-flex labelOrig{{ $arr->id }} labelO labelO{{ $arr->id }}-{{ $loop->iteration }} justify-content-center align-items-center">Add</label>

                                                                </div>
                                                            </div><br>
                                                        @endforeach
                                                        <br>

                                                        <div class="row bg-light">
                                                            @foreach ($containers as $container)
                                                                <input type='hidden'
                                                                    id='sub_inland_{{ $container->code }}_o{{ $arr->id }}'
                                                                    value="0">
                                                                <input type='hidden'
                                                                    id='sub_inland_{{ $container->code }}_d{{ $arr->id }}'
                                                                    value="0">
                                                            @endforeach
                                                            <div class="col-lg-3 col-lg-offset-"><span
                                                                    class="portalphacode">Subtotal
                                                                    Inlands Charges</span></div>
                                                            <div class="col-lg-7">
                                                                <div class="d-flex justify-content-end">
                                                                    @foreach ($containers as $container)
                                                                        <div class="wth"
                                                                            {{ $equipmentHides[$container->code] }}>
                                                                            <span class="portalphacode">
                                                                                <div
                                                                                    id='sub_inland_{{ $container->code }}{{ $arr->id }}'>
                                                                                    {{ isDecimal(0.0) }}
                                                                                </div>
                                                                            </span>
                                                                        </div>
                                                                    @endforeach

                                                                </div>
                                                            </div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode">{{ $arr->typeCurrency }}</span>
                                                            </div>
                                                            <div class="col-lg-1"><span
                                                                    class="portalphacode"></span></div>
                                                        </div>

                                                    </div>
                                                </div>
                                            @endif

                                            @if (!empty($arr->remarks) || !empty($arr->remarksG))
                                                <div class="row no-margin margin-card"
                                                    id='remark{{ $loop->iteration }}' hidden='true'>
                                                    <div class="col-lg-12">
                                                        <div class="row">
                                                            <span class="darkblue cabezeras">Remarks</span><br><br>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12"><span
                                                                    class="monto-down">{!! $arr->remarks !!} <br>
                                                                    {!! $arr->remarksG !!}</span></div>

                                                        </div>
                                                    </div>

                                                </div>

                                            @endif


                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                {!! Form::close() !!}
            </div>
        @endif
    @endif


    <div class="modal fade" id="contactModalSearch" role="dialog" aria-labelledby="contactModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLongTitle">
                        Contacts

                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>



    @include('companies.partials.companiesModal')
    @include('quotesv2.partials.createContractModal')






@endsection


@section('js')
    @parent


    <script src="{{ asset('js/quotes-v2.js') }}" type="text/javascript"></script>
    @if (empty($arreglo))
        <script>
            //mensaje();
            $('select[name="contact_id"]').prop("disabled", true);
            $("select[name='company_id_quote']").val('');
            $('#select2-m_select2_2_modal-container').text('Please an option');
        </script>
    @else

        <script>
            precargar()
        </script>


    @endif




    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript">
    </script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript">
    </script>
    <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript">

    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete"
        async defer></script>
    <script type="application/x-javascript" src="/js/toarts-config.js"></script>
    <script>
        function downlodRequest(id, id2, id3) {
            url = '{!! route('quotes-v2.excel', [':id', '*id', '#id']) !!}';
            url = url.replace(':id', id);
            url = url.replace('*id', id2);
            url = url.replace('#id', id3);
            $.ajax({
                url: url,
                method: 'get',
                success: function(response) {
                    if (response.success == true) {
                        //window.location = response.url;
                        var link = document.createElement("a");
                        link.href = response.url;
                        link.download = 'example.csv';
                        link.click();
                    } else {
                        toastr.error('File not found');
                    }
                    ///console.log(response);
                }
            });
        }
        $('.selected').on('click', function() {
            $(this).toggleClass('selected-class');
            if ($('.selected').hasClass('selected-class')) {
                $('.create-manual').prop("disabled", true);
            } else {
                $('.create-manual').prop("disabled", false);
            }
        });
        $(document).ready(function() {
            var divRow = document.getElementsByClassName('data-rates');
            var numDivRow = divRow.length;
            var count = 0;
            console.log(numDivRow);
            for (var i = 1; i < numDivRow; i++) {
                if (i % 2 == 1) {
                    var clase = divRow[i];
                    console.log(clase);
                    $(clase).css({
                        'background-color': '#fafafa'
                    });
                    //console.log(clase);
                }
            }
            $('#carrier_select').selectC5();
            $('#equipment').selectC5();
            var data = '{{ $allCarrier }}';
            if (data == true) {
                $('.c5-switch').prop('checked', true);
                $('.c5-switch').trigger('change');
            }
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            if (urlParams.has('opt')) {
                $('#quoteType').val('2');
                $('#quoteType').trigger('change');
            }



        });
        /*** GOOGLE MAPS API ***/
        var autocomplete;

        function initAutocomplete() {
            var geocoder = new google.maps.Geocoder();
            var autocomplete = new google.maps.places.Autocomplete((document.getElementById('origin_address')));
            var autocomplete_destination = new google.maps.places.Autocomplete((document.getElementById(
                'destination_address')));
            //autocomplete.addListener('place_changed', fillInAddress);
        }

        function codeAddress(address) {
            var geocoder;
            geocoder.geocode({
                'address': address
            }, function(results, status) {
                if (status == 'OK') {
                    alert(results[0].geometry.location);
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        }
        $valor = $('#date_hidden').val();
        if ($valor != 0) {
            $('#m_datepicker_2').val($valor);
        }

        function setdateinput() {
            var date = $('#m_datepicker_2').val();
            $('#date_hidden').val(date);
        }
        $('.m-select3-general').select2();
        $('.select2-selection__arrow').remove();

        function AbrirModal(action, id) {
            if (action == "add") {
                var url = '{{ route('companies.addM') }}';
                $('#modal-body').load(url, function() {
                    $('#companyModal').modal({
                        show: true
                    });
                });
            }
            if (action == "addContact") {
                var url = '{{ route('contacts.addCM') }}';
                $('.modal-body').load(url, function() {
                    $('#contactModalSearch').modal({
                        show: true
                    });
                });
            }
            if (action == "addContract") {
                var url = '{{ route('quotesv2.addContract') }}';
                $('.modal-body').load(url, function() {
                    $('#createContractModal').modal({
                        show: true
                    });
                });
            }
        }
        $('#delivery_type').on('change', function() {
            var value = $(this).val();
            if (value == 1) {
                $('#destination_port').removeClass('col-lg-2').addClass('col-lg-4');
                $('#origin_port').removeClass('col-lg-2').addClass('col-lg-4');
            } else if ((value == 2) || (value == 3)) {
                $('#destination_port').removeClass('col-lg-4').addClass('col-lg-2');
                $('#origin_port').removeClass('col-lg-2').addClass('col-lg-4');
            } else if (value == 4) {
                $('#destination_port').removeClass('col-lg-4').addClass('col-lg-2');
                $('#origin_port').removeClass('col-lg-4').addClass('col-lg-2');
            }
        });
        $('#delivery_type_air').on('change', function() {
            var value = $(this).val();
            console.log(value);
            if (value == 5) {
                $('#destination_port').removeClass('col-lg-2').addClass('col-lg-4');
                $('#origin_port').removeClass('col-lg-2').addClass('col-lg-4');
            } else if ((value == 6) || (value == 7)) {
                $('#destination_port').removeClass('col-lg-4').addClass('col-lg-2');
                $('#origin_port').removeClass('col-lg-2').addClass('col-lg-4');
            } else if (value == 8) {
                $('#destination_port').removeClass('col-lg-4').addClass('col-lg-2');
                $('#origin_port').removeClass('col-lg-4').addClass('col-lg-2');
            }
        });
        $('#quoteType').on('change', function() {
            var value = $(this).val();
            if (value == 3) {
                $('#delivery_type_air_label').css({
                    'display': 'block'
                });
            }
        });
        //C5 Select
        (function($) {
            $.fn.selectC5 = function() {

                var clickOnID = '' + $(this).attr('id') + '';
                var optionSelect = '#' + $(this).attr('id') + ' option';
                var selectType = '' + $(this).attr('select-type') + '';
                var selectContainer = $('select#' + clickOnID + ' option').val();
                var multiSelect = '<span class="c5-select-multiple-dropdown ' + clickOnID + '">' +
                    '<ul class="c5-select-dropdown-list select-list">' +
                    '</ul>' +
                    '</span>' +
                    '<span class="c5-select-multiple-container ' + clickOnID + '">' +
                    /*  '<span class="c5-select-header">Types</span>' +
                     '<ul class="c5-select-list list-types-carriers">' +
                     '<li class="c5-case"><label class="c5-label">CMA CGM Spot' +
                     '<input id="mode4" type="checkbox" class="c5-check" value="CMA" title="CMA">' +
                     '<span class="checkmark"></span></label></li>' +
                     '<li class="c5-case"><label class="c5-label">MAERSK Spot' +
                     '<input id="mode5" type="checkbox" class="c5-check" value="MAERSK" title="MAERSK">' +
                     '<span class="checkmark"></span></label></li>' +
                     '<li class="c5-case"><label class="c5-label">SAFMARINE Spot' +
                     '<input id="mode6" type="checkbox" class="c5-check" value="SAFMARINE" title="SAFMARINE">' +
                     '<span class="checkmark"></span></label></li>' +
                     '</ul>' + */
                    '<span class="c5-select-header">Carriers</span>' +
                    '<span class="c5-select-container-close">' +
                    '<i class="fa fa-times" aria-hidden="true"></i>' +
                    '</span>' +
                    '<span class="c5-select-multiple-switch">' +
                    'Select All ' +
                    '<label class="switch">' +
                    '<input type="checkbox" class="c5-switch">' +
                    '<span class="slider round"></span>' +
                    '</label>' +
                    '</span>' +
                    '<ul class="c5-select-list select-normal"></ul>' +
                    '</span>';
                var multiSelectGroup = '<span class="c5-select-multiple-dropdown ' + clickOnID + '">' +
                    '<ul class="c5-select-dropdown-list select-list">' +
                    '</ul>' +
                    '</span>' +
                    '<span class="c5-select-multiple-container ' + clickOnID + '">' +
                    '<span class="c5-select-container-close">' +
                    '<i class="fa fa-times" aria-hidden="true"></i>' +
                    '</span>' +
                    '<span class="c5-select-header">Types</span>' +
                    '<ul class="c5-select-list list-group1"></ul>' +
                    '<span class="c5-select-header h-hidden">Equipment List</span>' +
                    '<ul class="c5-select-list list-group2"></ul>' +
                    '</span>';
                // Select Multiple con swicth
                if (selectType == 'multiple') {
                    var data = '{{ $carrierMan }}';
                    var carriersList = JSON.parse(data.replace(/&quot;/g, '"'));
                    var defaultValuesCarriers = $('#' + clickOnID + '').val();
                    $(this).after(multiSelect);
                    $(optionSelect).each(function() {
                        var list = '<li class="c5-case"><label class="c5-label">' + $(this).text() +
                            '<input type="checkbox" title="' + $(this).text() +
                            '" class="c5-check" value="' + $(this).val() +
                            '"><span class="checkmark"></span></label></li>';
                        $('.c5-select-list.select-normal').append(list);
                    });
                    for (var i in defaultValuesCarriers) {
                        var ident = defaultValuesCarriers[i];
                        var name = $('.select-normal .c5-case input[value="' + ident + '"]').attr('title');
                        //console.log(name);
                        $('.' + clickOnID + ' .select-list').append('<li title="' + name + '">' + name + ', </li>');
                        //$('.'+clickOnID+' .select-list').append('<li title="'+nameAPI+'">'+nameAPI+', </li>');
                        $('.select-normal .c5-case input[value="' + ident + '"]').attr('checked', true);
                        $('.list-types-carriers .c5-case input[value="' + ident + '"]').attr('checked', true);
                    }
                    $('.' + clickOnID + ' .select-list li[title="1"]').remove();
                    $('#' + clickOnID + '').val(defaultValuesCarriers);
                    $('.' + clickOnID + ' .c5-check').on("click", function() {
                        var checkSelected = [];
                        var valCheckSelected = $(this).val();
                        $('.' + clickOnID + ' .c5-check').each(function() {
                            if (this.checked) {
                                checkSelected.push($(this).val());
                            }
                        });
                        $('#' + clickOnID + '.select-normal').val(checkSelected);
                        /*var valor1 = $('#'+clickOnID+'.select-normal').val();
                        console.log(valor1);*/
                    });
                    $('.' + clickOnID + ' .c5-select-multiple-switch .c5-switch').on('change', function() {
                        var allSelected = [];
                        $('.' + clickOnID + ' .c5-check').prop('checked', $(this).is(':checked'));
                        $('.' + clickOnID + ' .c5-check').each(function() {
                            if (this.checked) {
                                allSelected.push($(this).val());
                            }
                        });
                        $('#' + clickOnID + '.select-normal').val(allSelected);
                        /*var valor = $('#'+clickOnID+'.select-normal').val();
                        console.log(valor);*/
                        if ($('.' + clickOnID + ' .c5-select-dropdown-list').html() == 'All Selected') {
                            $('.' + clickOnID + ' .c5-select-dropdown-list').html('');
                            $('.' + clickOnID + ' .c5-select-dropdown-list').append(
                                '<li class="hida">Select an option</li>');
                        } else {
                            $('.' + clickOnID + ' .c5-select-dropdown-list').html('All Selected');
                        }
                    });
                    $('.' + clickOnID + ' .select-normal .c5-check').on('change', function() {
                        var allCarriers = [];
                        var allOptions = $('.' + clickOnID + ' .c5-check').length;
                        $('.' + clickOnID + ' .select-normal .c5-check').each(function() {
                            if (this.checked) {
                                allCarriers.push($(this).val());
                            }
                        });
                        $('.' + clickOnID + ' .select-normal').val(allCarriers);
                        /*var valor = $('.'+clickOnID+' .select-normal').val();
                        console.log(valor);*/
                        var allCarriersLength = allCarriers.length;
                        if (allCarriers.length > 0) {
                            $('.' + clickOnID + ' .c5-select-dropdown-list').html('');
                            $('.' + clickOnID + ' .c5-select-dropdown-list').html('' + allCarriers.length +
                                ' has been Selected');
                        } else if (allCarriers.length == 0) {
                            $('.' + clickOnID + ' .c5-select-dropdown-list').html('');
                            $('.' + clickOnID + ' .c5-select-dropdown-list').html('Select an option');
                        }
                        $('.' + clickOnID + ' .c5-switch').prop('checked', false);
                    });
                    $('.select-normal .c5-case:nth-child(1)').remove();
                    $('.select-normal .c5-case:nth-child(2)').remove();
                    $('.select-normal .c5-case:nth-child(1)').remove();
                }
                // Select Multiple con Lables
                if (selectType == 'groupLabel') {
                    $(this).after(multiSelectGroup);
                    var showEquip = $('.select-list li.hida');
                    var data = '{{ $group_contain }}';
                    var newData = JSON.parse(data.replace(/&quot;/g, '"'));
                    var defaultValues = $('#' + clickOnID + '').val();
                    var containerType = '{{ $containerType }}';
                    getContainerByGroup('' + containerType + '');
                    for (var i in newData) {
                        var code = `${newData[i]}`;
                        //console.log(i, code);
                        var list2 = '<li class="c5-case"><label class="c5-label">' + code +
                            '<input type="radio" name="container_type" onclick="getContainerByGroup(' + i +
                            ')" class="c5-check" title="' + code + '" value="' + i +
                            '"><span class="checkmark"></span></label></li>';
                        $('.list-group1').append(list2);
                    }
                    $('.list-group1 .c5-case:nth-child(' + containerType + ') input').attr('checked', true);
                }
                $('.c5-select-multiple-dropdown.' + clickOnID + '').on('click', function() {
                    $('.c5-select-multiple-container.' + clickOnID + '').toggle();
                    $('.' + clickOnID + ' .c5-select-dropdown-list').css({
                        'border-color': '#716aca'
                    });
                });
                $('.select2').on('click', function() {
                    $('.c5-select-multiple-container.' + clickOnID + '').css({
                        'display': 'none'
                    });
                    $('.' + clickOnID + ' .c5-select-dropdown-list').css({
                        'border-color': '#eee'
                    });
                });
                $('.' + clickOnID + ' .c5-select-container-close').on('click', function() {
                    $('.c5-select-multiple-container.' + clickOnID + '').toggle();
                    $('.' + clickOnID + ' .c5-select-dropdown-list').css({
                        'border-color': '#716aca'
                    });
                });
            }
        })(jQuery);

        function getContainerByGroup(id_group) {
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '/Container/getContainer/',
                data: {
                    'id_group': id_group
                },
                success: function(data) {
                    //console.log(data);
                    var selectValues = $('select#equipment').val();
                    var containerType = '{{ $containerType }}';
                    const defaultValuesController = <?php echo json_encode($form['equipment']); ?>;
                    $('.h-hidden').css({
                        'display': 'block'
                    });
                    $('.list-group2 li').remove();
                    //Opciones de equipment list
                    for (const equip in data) {
                        var code = `${data[equip].code}`;
                        var idEquip = `${data[equip].id}`;
                        var list2 = '<li class="c5-case"><label class="c5-label">' + code +
                            '<input type="checkbox"  class="c5-check" title="' + code + '" value="' + idEquip +
                            '"><span class="checkmark"></span></label></li>';
                        $('.equipment .select-list').append('<li title="' + code + '">' + code + ', </li>');
                        $('.list-group2').append(list2);
                    }
                    $('.equipment .select-list').html('');
                    //Cargamos valores de DRY predeterminados final
                    if (id_group == containerType) {
                        for (var i in defaultValuesController) {
                            var ident = defaultValuesController[i];
                            var name = $('.list-group2 .c5-case input[value="' + ident + '"]').attr('title');
                            $('.equipment .select-list').append('<li title="' + name + '">' + name + ', </li>');
                            $('.list-group2 .c5-case input[value="' + ident + '"]').attr('checked', true);
                            defaultValuesController.push(ident);
                            //console.log(ident);
                        }
                        $('.equipment .select-list li[title="undefined"]').remove();
                        $('#equipment.select-group').val(defaultValuesController);
                        //console.log($('#equipment.select-group').val());
                        //console.log($('#equipment.select-group').val());
                    } else {
                        var valueArray = [];
                        for (const equip in data) {
                            var code = `${data[equip].code}`;
                            var idEquip = `${data[equip].id}`;
                            $('.equipment .select-list').append('<li title="' + code + '">' + code + ', </li>');
                            $('.list-group2 .c5-case input[value="' + idEquip + '"]').attr('checked', true);
                            valueArray.push(idEquip);
                            //console.log(idEquip);
                        }
                        $('#equipment.select-group').val(valueArray);
                        //console.log($('#equipment.select-group').val());
                    }
                    //Cargamos valores al click de equipment list
                    $('.equipment .list-group2 .c5-check').on("click", function() {
                        var valueEquipment = [];
                        var title = $(this).attr('title');
                        $('.equipment .list-group2 .c5-check').each(function() {
                            if (this.checked) {
                                valueEquipment.push($(this).val());
                                //console.log(valueEquipment);
                            }
                        });
                        $('#equipment.select-group').val(valueEquipment);
                        var countEquip = valueEquipment.length;
                        if ($(this).is(':checked')) {
                            var html = '<li title="' + title + '">' + title + ', </li>';
                            $('.equipment .select-list').append(html);
                            $(".select-list .hida").hide();
                        } else {
                            var listLength = $('.equipment .list-group2 .c5-check:checked').length;
                            $('li[title="' + title + '"]').remove();
                            if (countEquip == 0) {
                                $(".select-list .hida").show();
                            }
                        }
                    });
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                }
            });
        }

        $('#mySelect2').select2({
            // ...
            templateSelection: function(data, container) {
                // Add custom attributes to the <option> tag for the selected option
                $(data.element).attr('data-custom-attribute', data.customValue);
                return data.text;
            }
        });

        var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
            url: '{{ route('contracts.storeMedia') }}',
            maxFilesize: 2, // MB
            timeout: 18000,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(file, response) {
                $('#m_form').append('<input type="hidden" name="document[]" value="' + response.name + '">');
                uploadedDocumentMap[file.name] = response.name
                $('#m_form').find('input[name="existsFile"]').val(1);
            },
            removedfile: function(file) {
                file.previewElement.remove()
                var name = ''
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name
                } else {
                    name = uploadedDocumentMap[file.name]
                }
                $
                $('#m_form').find('input[name="document[]"][value="' + name + '"]').remove();
                $('#m_form').find('input[name="existsFile"]').val('');
            },
            init: function() {
                @if (isset($project) && $project->document)
                    var files =
                    {!! json_encode($project->document) !!}
                    for (var i in files) {
                    var file = files[i]
                    this.options.addedfile.call(this, file)
                    file.previewElement.classList.add('dz-complete')
                    $('m_form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
                    }
                @endif
            }
        }
    </script>

@stop
