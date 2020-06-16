<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 29/05/2018
 * Time: 10:50 PM
 */

?>
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="m-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl-12 col-12" style="padding-top:20px;">
                <!-- filter -->

                <div class="m-portlet--mobile">
                    <div class="m-portlet__body ">
                        {!! Form::open(['route' => 'dashboard.filter','method'=>'GET','class' => 'form-group m-form__group']) !!}
                        <div class="filter">
                            @include('dashboard.partials.form_filter')
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <hr class="separator">
                </div>

                <!-- end-filter -->
            </div>
        </div>
        @if(isset($pick_up_dates))
            <div class="row">  
                <div class="col-md-4 col-lg-4 col-xl-4">
                    @if(isset($user))
                    <h4>{{$user->name}} {{$user->lastname}}</h4>
                    <br>
                    @endif
                    <h6><b>From:</b> {{$pick_up_dates['start_date']}}</h6>
                    <h6><b>Until:</b> {{$pick_up_dates['end_date']}}</h6>
                </div>
            </div>
            <hr class="separator">
        @endif
        <div class="row">
            <div class="col-md-4 col-lg-4 col-xl-4" style="padding-top:20px;">

                <div class="m-widget24 card" style="height: 220px;">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Draft Quotes:
                        </h4>
                        <br>
                        <span class="m-widget24__desc widget24__stats m--font-default" style="font-size:18px;">
                            <b>{{ $draft }}</b>
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-default " role="progressbar" style="width: {{ round(($draft * 100) / $totalQuotes) }}%;"></div>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>
                <br>
                <!--<div class="m-widget24 card" style="height: 220px; width: auto;">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Total:
                        </h4>
                        <br>
                        <span class="m-widget24__desc widget24__stats m--font-default" style="font-size:18px;">
                            <b>{{ $totalDraft}} {{$currency}}</b>
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-default" role="progressbar" style="width: {{ round(($totalDraft * 100) / $total) }}%;"></div>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>-->
            </div>
            <div class="col-md-4 col-lg-4 col-xl-4" style="padding-top:20px;">

                <div class="m-widget24 card" style="height: 220px;">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Sent Quotes:
                        </h4>
                        <br>
                        <span class="m-widget24__desc widget24__stats m--font-success" style="font-size:18px;">
                            <b>{{ $sent }}</b>
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-success" role="progressbar" style="width: {{ round(($sent * 100) / $totalQuotes) }}%;"></div>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>
                <br>
                <!--<div class="m-widget24 card" style="height: 220px;">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Total:
                        </h4>
                        <br>
                        <span class="m-widget24__desc widget24__stats m--font-success" style="font-size:18px;">
                            <b>{{ $totalSent }} {{$currency}}</b>
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-success" role="progressbar" style="width: {{ round(($totalSent * 100) / $total) }}%;"></div>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>-->
            </div>          
            <div class="col-md-4 col-lg-4 col-xl-4" style="padding-top:20px;">

                <div class="m-widget24 card" style="height: 220px;">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Won Quotes:
                        </h4>
                        <br>
                        <span class="m-widget24__desc widget24__stats m--font-info" style="font-size:18px;">
                            <b>{{ $won }}</b>
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-info" role="progressbar" style="width: {{ round(($won * 100) / $totalQuotes) }}%;"></div>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>
                <br>
                <!--<div class="m-widget24 card" style="height: 220px;">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Total:
                        </h4>
                        <br>
                        <span class="m-widget24__desc widget24__stats m--font-info" style="font-size:18px;">
                            <b>{{ $totalWon }} {{$currency}}</b>
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-info" role="progressbar" style="width: {{ round(($totalWon * 100) / $total) }}%;"></div>
                        </div>
                        <br>
                        <br>
                        <br>
                    </div>
                </div>-->

            </div>
        </div>
        <br>
        <hr>
        <div class="row">

            <!--<div class="row">
<div class="col-md-12 col-lg-12 col-xl-12">
<div class="m-portlet m-portlet--tab">
<div class="m-portlet__head">
<div class="m-portlet__head-caption">
<div class="m-portlet__head-title">
<span class="m-portlet__head-icon m--hide">
<i class="la la-gear"></i>
</span>
<h3 class="m-portlet__head-text">
Compare Rates
</h3>
</div>
</div>
</div>
<div class="m-portlet__body">
<div id="m_morris_3" style="height: 500px; position: relative; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
<svg height="500" version="1.1" width="889" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="overflow: hidden; position: relative; left: -0.593745px; top: -0.0116938px;">
<desc style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">Created with Raphaël 2.2.0</desc>
<defs style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></defs>
<text x="35" y="461.25" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">0</tspan>
</text>
<path fill="none" stroke="#aaaaaa" d="M47.5,461.25H864.062" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">

</path>
<text x="35" y="352.1875" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">$500</tspan>
</text>
<path fill="none" stroke="#aaaaaa" d="M47.5,352.1875H864.062" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">

</path>
<text x="35" y="243.125" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">$700</tspan>
</text>
<path fill="none" stroke="#aaaaaa" d="M47.5,243.125H864.062" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">

</path>
<text x="35" y="134.0625" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">$900</tspan>
</text>
<path fill="none" stroke="#aaaaaa" d="M47.5,134.0625H864.062" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">

</path>
<text x="35" y="25" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">$1100</tspan>
</text>
<path fill="none" stroke="#aaaaaa" d="M47.5,25H864.062" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">

</path>
<text x="805.7361428571429" y="473.75" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">June</tspan>
</text>
<text x="689.0844285714286" y="473.75" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">May</tspan>
</text>
<text x="572.4327142857143" y="473.75" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">April</tspan>
</text>
<text x="455.781" y="473.75" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">March</tspan>
</text>
<text x="339.1292857142857" y="473.75" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">February</tspan>
</text>
<text x="222.47757142857145" y="473.75" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">January</tspan>
</text>
<text x="105.82585714285715" y="473.75" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(1,0,0,1,0,6.875)">
<tspan dy="4.375" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">December</tspan>
</text>
<rect x="62.08146428571429" y="25" width="42.244392857142856" height="436.25" rx="0" ry="0" fill="#0b62a4" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="107.32585714285715" y="68.625" width="42.244392857142856" height="392.625" rx="0" ry="0" fill="#7a92a3" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="178.7331785714286" y="134.0625" width="42.244392857142856" height="327.1875" rx="0" ry="0" fill="#0b62a4" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="223.97757142857145" y="177.6875" width="42.244392857142856" height="283.5625" rx="0" ry="0" fill="#7a92a3" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="295.38489285714286" y="243.125" width="42.244392857142856" height="218.125" rx="0" ry="0" fill="#0b62a4" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="340.62928571428574" y="286.75" width="42.244392857142856" height="174.5" rx="0" ry="0" fill="#7a92a3" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="412.0366071428571" y="134.0625" width="42.244392857142856" height="327.1875" rx="0" ry="0" fill="#0b62a4" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="457.28099999999995" y="177.6875" width="42.244392857142856" height="283.5625" rx="0" ry="0" fill="#7a92a3" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="528.6883214285715" y="243.125" width="42.244392857142856" height="218.125" rx="0" ry="0" fill="#0b62a4" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="573.9327142857144" y="286.75" width="42.244392857142856" height="174.5" rx="0" ry="0" fill="#7a92a3" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="645.3400357142858" y="134.0625" width="42.244392857142856" height="327.1875" rx="0" ry="0" fill="#0b62a4" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="690.5844285714287" y="177.6875" width="42.244392857142856" height="283.5625" rx="0" ry="0" fill="#7a92a3" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="761.99175" y="25" width="42.244392857142856" height="436.25" rx="0" ry="0" fill="#0b62a4" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
<rect x="807.2361428571429" y="68.625" width="42.244392857142856" height="392.625" rx="0" ry="0" fill="#7a92a3" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect>
</svg>
<div class="morris-hover morris-default-style" style="left: 295.516px; top: 211px;">
<div class="morris-hover-row-label">2008</div>
<div class="morris-hover-point" style="color: #0b62a4">
Series A:
50
</div>
<div class="morris-hover-point" style="color: #7a92a3">
Series B:
40
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="row">
<div class="col-md-4 col-lg-4 col-xl-4">

<div class="m-portlet m-portlet--full-height">
<div class="m-portlet__head">
<div class="m-portlet__head-caption">
<div class="m-portlet__head-title">
<h3 class="m-portlet__head-text">
Quotes Per Country
</h3>
</div>
</div>
</div>
<div class="m-portlet__body">
<div class="m-widget6">
<div class="m-widget6__head">
<div class="m-widget6__item">
<span class="m-widget6__caption">
Country
</span>
<span class="m-widget6__caption">
Count
</span>
<span class="m-widget6__caption m--align-right">
Amount
</span>
</div>
</div>
<div class="m-widget6__body">
<div class="m-widget6__item">
<span class="m-widget6__text">
China
</span>
<span class="m-widget6__text">
67
</span>
<span class="m-widget6__text m--align-right m--font-boldest m--font-brand">
$14,740
</span>
</div>
<div class="m-widget6__item">
<span class="m-widget6__text">
Italy
</span>
<span class="m-widget6__text">
120
</span>
<span class="m-widget6__text m--align-right m--font-boldest m--font-brand">
$11,002
</span>
</div>
<div class="m-widget6__item">
<span class="m-widget6__text">
Spain
</span>
<span class="m-widget6__text">
32
</span>
<span class="m-widget6__text m--align-right m--font-boldest m--font-brand">
$10,900
</span>
</div>
<div class="m-widget6__item">
<span class="m-widget6__text">
USA
</span>
<span class="m-widget6__text">
130
</span>
<span class="m-widget6__text m--align-right m--font-boldest m--font-brand">
$14,740
</span>
</div>
<div class="m-widget6__item">
<span class="m-widget6__text">
Brazil
</span>
<span class="m-widget6__text">
5
</span>
<span class="m-widget6__text m--align-right m--font-boldest m--font-brand">
$18,540
</span>
</div>
<div class="m-widget6__item">
<span class="m-widget6__text">
Germany
</span>
<span class="m-widget6__text">
32
</span>
<span class="m-widget6__text m--align-right m--font-boldest m--font-brand">
$10,900
</span>
</div>
<div class="m-widget6__item">
<span class="m-widget6__text">
France
</span>
<span class="m-widget6__text">
201
</span>
<span class="m-widget6__text m--align-right m--font-boldest m--font-brand">
$25,609
</span>
</div>
</div>
<div class="m-widget6__foot">
<div class="m-widget6__action m--align-right">
<button class="btn m-btn--pill btn-secondary m-btn m-btn--hover-brand m-btn--custom" type="button">
Export
</button>
</div>
</div>
</div>
</div>
</div>
</div>
</div>-->
        </div>
    </div>
    @endsection

    @section('js')
    @parent
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/js/base.js"></script>
    <script src="/js/dashboard.js"></script>

    @stop
