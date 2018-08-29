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

    <div class="container-fluid">
        <div class="row">
            <!-- filter -->
            @if( Auth::user()->type == 'company')

                <div class="container m-portlet--mobile">
                    <div class="m-portlet__body ">
                        {!! Form::open(['route' => 'dashboard.store','class' => 'form-group m-form__group']) !!}
                            <div class="filter">
                                @include('dashboard.partials.form_filter')
                            </div>
                        {!! Form::close() !!}
                    </div>
                    <hr class="separator">
                </div>
            @endif
            <!-- end-filter -->
            <div class="col-md-4 col-lg-4 col-xl-4">

                <div class="m-widget24 card">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Quotes Sent:
                        </h4>
                        <br>
                        <span class="m-widget24__desc">

                        </span>
                        <span class="m-widget24__stats m--font-brand">
								{{ $sent }}
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-brand" role="progressbar" style="width: {{ round(($sent * 100) / $totalQuotes) }}%;"></div>
                        </div>
                        <span class="m-widget24__change">
								Change
                        </span>
                        <span class="m-widget24__number">
                            {{ round(($sent * 100) / $totalQuotes) }}%
                        </span>
                    </div>
                </div>

            </div>
            <div class="col-md-4 col-lg-4 col-xl-4">

                <div class="m-widget24 card">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Quotes Acepted:
                        </h4>
                        <br>
                        <span class="m-widget24__desc">

                        </span>
                        <span class="m-widget24__stats m--font-info">
								{{ $acepted }}
						</span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-info" role="progressbar" style="width: {{ round(($acepted * 100) / $totalQuotes) }}%;"></div>
                        </div>
                        <span class="m-widget24__change">
								Change
                        </span>
                        <span class="m-widget24__number">
								{{ round(($acepted * 100) / $totalQuotes) }}%
                        </span>
                    </div>
                </div>

            </div>
            <div class="col-md-4 col-lg-4 col-xl-4">

                <div class="m-widget24 card">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Quotes Lost:
                        </h4>
                        <br>
                        <span class="m-widget24__desc">

                        </span>
                        <span class="m-widget24__stats m--font-danger">
								{{ $lost }}
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-danger" role="progressbar" style="width: {{ round(($lost * 100) / $totalQuotes) }}%;"></div>
                        </div>
                        <span class="m-widget24__change">
								Change
                        </span>
                        <span class="m-widget24__number">
								{{ round(($lost * 100) / $totalQuotes) }}%
                        </span>
                    </div>
                </div>

            </div>
            <!-- Total section -->
            <div class="col-md-4 col-lg-4 col-xl-4">

                <div class="m-widget24 card">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Total:
                        </h4>
                        <br>
                        <span class="m-widget24__desc">

                        </span>
                        <span class="m-widget24__stats m--font-danger">
								{{ $totalSent }} {{ $currency }}
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-danger" role="progressbar" style="width: {{ round(($totalSent * 100) / $total) }}%;"></div>
                        </div>
                        <span class="m-widget24__change">
								Change
                        </span>
                        <span class="m-widget24__number">
								{{ round(($totalSent * 100) / $total) }}%
                        </span>
                    </div>
                </div>

            </div>
            <div class="col-md-4 col-lg-4 col-xl-4">

                <div class="m-widget24 card">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Total:
                        </h4>
                        <br>
                        <span class="m-widget24__desc">

                        </span>
                        <span class="m-widget24__stats m--font-danger">
								{{ $totalWin }} {{ $currency }}
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-danger" role="progressbar" style="width: {{ round(($totalWin * 100) / $total) }}%;"></div>
                        </div>
                        <span class="m-widget24__change">
								Change
                        </span>
                        <span class="m-widget24__number">
								{{ round(($totalWin * 100) / $total) }}%
                        </span>
                    </div>
                </div>

            </div>
            <div class="col-md-4 col-lg-4 col-xl-4">

                <div class="m-widget24 card">
                    <div class="m-widget24__item">
                        <h4 class="m-widget24__title">
                            Total:
                        </h4>
                        <br>
                        <span class="m-widget24__desc">

                        </span>
                        <span class="m-widget24__stats m--font-danger">
                            {{ $totalLost }} {{ $currency }}
                        </span>
                        <div class="m--space-10"></div>
                        <div class="progress m-progress--sm">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50" class="progress-bar m--bg-danger" role="progressbar" style="width: {{ round(($totalLost * 100) / $total) }}%;"></div>
                        </div>
                        <span class="m-widget24__change">
								Change
                        </span>
                        <span class="m-widget24__number">
								{{ round(($totalLost * 100) / $total) }}%
                        </span>
                    </div>
                </div>

            </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-lg-8 col-xl-8">
                <div class="col-lg-12">

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
        </div>
    </div>
     <div class="row">
         <div class="col-xl-6 col-lg-12">
             <!--Begin::Portlet-->
             <div class="m-portlet  m-portlet--full-height ">
                 <div class="m-portlet__head">
                     <div class="m-portlet__head-caption">
                         <div class="m-portlet__head-title">
                             <h3 class="m-portlet__head-text">
                                 Recent Activities
                             </h3>
                         </div>
                     </div>
                     <div class="m-portlet__head-tools">
                         <ul class="m-portlet__nav">
                             <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover" aria-expanded="true">
                                 <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
                                     <i class="la la-ellipsis-h m--font-brand"></i>
                                 </a>
                                 <div class="m-dropdown__wrapper">
                                     <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                     <div class="m-dropdown__inner">
                                         <div class="m-dropdown__body">
                                             <div class="m-dropdown__content">
                                                 <ul class="m-nav">
                                                     <li class="m-nav__section m-nav__section--first">
																			<span class="m-nav__section-text">
																				Quick Actions
																			</span>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-share"></i>
                                                             <span class="m-nav__link-text">
																					Activity
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-chat-1"></i>
                                                             <span class="m-nav__link-text">
																					Messages
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-info"></i>
                                                             <span class="m-nav__link-text">
																					FAQ
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__item">
                                                         <a href="" class="m-nav__link">
                                                             <i class="m-nav__link-icon flaticon-lifebuoy"></i>
                                                             <span class="m-nav__link-text">
																					Support
																				</span>
                                                         </a>
                                                     </li>
                                                     <li class="m-nav__separator m-nav__separator--fit"></li>
                                                     <li class="m-nav__item">
                                                         <a href="#" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm">
                                                             Cancel
                                                         </a>
                                                     </li>
                                                 </ul>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </li>
                         </ul>
                     </div>
                 </div>
                 <div class="m-portlet__body">
                     <div class="m-scrollable mCustomScrollbar _mCS_5 mCS-autoHide _mCS_4" data-scrollbar-shown="true" data-scrollable="true" data-max-height="380" style="overflow: visible; height: 380px; max-height: 380px; position: relative;"><div id="mCSB_4" class="mCustomScrollBox mCS-minimal-dark mCSB_vertical mCSB_outside" tabindex="0" style="max-height: none;"><div id="mCSB_4_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                 <!--Begin::Timeline 2 -->
                                 <div class="m-timeline-2">
                                     <div class="m-timeline-2__items  m--padding-top-25 m--padding-bottom-30">
                                         <div class="m-timeline-2__item">
														<span class="m-timeline-2__item-time">
															10:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-danger"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text  m--padding-top-5">
                                                 Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                                 <br>
                                                 incididunt ut labore et dolore magna
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															12:45
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-success"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m-timeline-2__item-text--bold">
                                                 AEOL Meeting With
                                             </div>
                                             <div class="m-list-pics m-list-pics--sm m--padding-left-20">
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_4.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_13.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_11.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                                 <a href="#">
                                                     <img src="assets/app/media/img/users/100_14.jpg" title="" class="mCS_img_loaded">
                                                 </a>
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															14:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-brand"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Make Deposit
                                                 <a href="#" class="m-link m-link--brand m--font-bolder">
                                                     USD 700
                                                 </a>
                                                 To ESL.
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															16:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-warning"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                                 <br>
                                                 incididunt ut labore et dolore magna elit enim at minim
                                                 <br>
                                                 veniam quis nostrud
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															17:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-info"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Placed a new order in
                                                 <a href="#" class="m-link m-link--brand m--font-bolder">
                                                     SIGNATURE MOBILE
                                                 </a>
                                                 marketplace.
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															16:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-brand"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
                                                 <br>
                                                 incididunt ut labore et dolore magna elit enim at minim
                                                 <br>
                                                 veniam quis nostrud
                                             </div>
                                         </div>
                                         <div class="m-timeline-2__item m--margin-top-30">
														<span class="m-timeline-2__item-time">
															17:00
														</span>
                                             <div class="m-timeline-2__item-cricle">
                                                 <i class="fa fa-genderless m--font-danger"></i>
                                             </div>
                                             <div class="m-timeline-2__item-text m--padding-top-5">
                                                 Received a new feedback on
                                                 <a href="#" class="m-link m-link--brand m--font-bolder">
                                                     FinancePro App
                                                 </a>
                                                 product.
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <!--End::Timeline 2 -->
                             </div></div><div id="mCSB_4_scrollbar_vertical" class="mCSB_scrollTools mCSB_4_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical" style="display: block;"><div class="mCSB_draggerContainer"><div id="mCSB_4_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 50px; display: block; height: 221px; max-height: 360px; top: 0px;"><div class="mCSB_dragger_bar" style="line-height: 50px;"></div></div><div class="mCSB_draggerRail"></div></div></div></div>
                 </div>
             </div>
             <!--End::Portlet-->
         </div>
         <div class="col-xl-6 col-lg-12">
             <!--Begin::Portlet-->
             <div class="m-portlet m-portlet--full-height ">
                 <div class="m-portlet__head">
                     <div class="m-portlet__head-caption">
                         <div class="m-portlet__head-title">
                             <h3 class="m-portlet__head-text">
                                 Recent Notifications
                             </h3>
                         </div>
                     </div>
                     <div class="m-portlet__head-tools">
                         <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
                             <li class="nav-item m-tabs__item">
                                 <a class="nav-link m-tabs__link active show" data-toggle="tab" href="#m_widget2_tab1_content" role="tab" aria-selected="true">
                                     Today
                                 </a>
                             </li>
                             <li class="nav-item m-tabs__item">
                                 <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget2_tab2_content" role="tab" aria-selected="false">
                                     Month
                                 </a>
                             </li>
                         </ul>
                     </div>
                 </div>
                 <div class="m-portlet__body">
                     <div class="tab-content">
                         <div class="tab-pane active" id="m_widget2_tab1_content">
                             <!--Begin::Timeline 3 -->
                             <div class="m-timeline-3">
                                 <div class="m-timeline-3__items">
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time">
																09:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit,consectetur eiusmdd tempor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Bob
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--warning">
															<span class="m-timeline-3__item-time">
																10:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Sean
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time">
																11:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit eiusmdd tempor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--success">
															<span class="m-timeline-3__item-time">
																12:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--danger">
															<span class="m-timeline-3__item-time">
																14:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit,consectetur eiusmdd
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Derrick
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time">
																15:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit amit,consectetur
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Iman
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time">
																17:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem ipsum dolor sit consectetur eiusmdd tempor
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Aziko
																	</a>
																</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <!--End::Timeline 3 -->
                         </div>
                         <div class="tab-pane" id="m_widget2_tab2_content">
                             <!--Begin::Timeline 3 -->
                             <div class="m-timeline-3">
                                 <div class="m-timeline-3__items">
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time m--font-focus">
																09:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Contrary to popular belief, Lorem Ipsum is not simply random text.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Bob
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--warning">
															<span class="m-timeline-3__item-time m--font-warning">
																10:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	There are many variations of passages of Lorem Ipsum available.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Sean
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time m--font-primary">
																11:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Contrary to popular belief, Lorem Ipsum is not simply random text.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--success">
															<span class="m-timeline-3__item-time m--font-success">
																12:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	The standard chunk of Lorem Ipsum used since the 1500s is reproduced.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By James
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--danger">
															<span class="m-timeline-3__item-time m--font-warning">
																14:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Latin words, combined with a handful of model sentence structures.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Derrick
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--info">
															<span class="m-timeline-3__item-time m--font-info">
																15:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Contrary to popular belief, Lorem Ipsum is not simply random text.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Iman
																	</a>
																</span>
                                         </div>
                                     </div>
                                     <div class="m-timeline-3__item m-timeline-3__item--brand">
															<span class="m-timeline-3__item-time m--font-danger">
																17:00
															</span>
                                         <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
																	Lorem Ipsum is therefore always free from repetition, injected humour.
																</span>
                                             <br>
                                             <span class="m-timeline-3__item-user-name">
																	<a href="#" class="m-link m-link--metal m-timeline-3__item-link">
																		By Aziko
																	</a>
																</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <!--End::Timeline 3 -->
                         </div>
                     </div>
                 </div>
             </div>
             <!--End::Portlet-->
         </div>
     </div>
     </div>
@endsection

@section('js')
    @parent
        <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>

    @stop
