@extends('layouts.app')
@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Rates Failed
                    </h3><br>

                </div>
            </div>
        </div>

        @if (count($errors) > 0)
        <div id="notificationError" class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(Session::has('message.nivel'))
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
        @endif

        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                <i class="la la-cog"></i>
                                Rates 
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                                <i class="la la-briefcase"></i>
                                Surchargers
                            </a>
                        </li>

                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                    <br>
                    <div class="m-portlet__head">
                        <label >
                            <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                            <strong >
                                Rates Failed: 
                            </strong>
                            <strong id="strfail">{{$countfailrates}}</strong>
                            <input type="hidden" value="{{$countfailrates}}" id="strfailinput" />
                        </label>
                        <br>
                        <label>
                            <i class="fa fa-dot-circle-o" style="color:green;"> </i>
                            <strong id="">
                                Good Rates: 
                            </strong>
                            <strong id="strgood">
                                {{$countrates}}
                            </strong>
                            <input type="hidden" value="{{$countrates}}" id="strgoodinput" />
                        </label>
                    </div>
                    <!--<button onclick="prueba()">prueba</button>-->
                    <div class="m-portlet__body">
                        <table class="table m-table m-table--head-separator-primary "  id="html_table" >
                            <thead >
                                <tr>
                                    <th width="5%" >
                                        Status
                                    </th >
                                    <th width="18%">
                                        Origin
                                    </th>
                                    <th width="18%">
                                        Destiny
                                    </th>

                                    <th width="10%">
                                        Carrier
                                    </th>
                                    <th >
                                        20'
                                    </th>
                                    <th >
                                        40'
                                    </th>
                                    <th >
                                        40'Hc
                                    </th>
                                    <th >
                                        Currency
                                    </th>
                                    <th >
                                        Options
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i=1 
                                @endphp
                                @foreach($failrates as $ratef)
                                <tr class="" id="{{'trR'.$i}}">
                                    <td>
                                        <i class="fa fa-dot-circle-o {{'icon'.$i}}" style="color:red;" id="" ></i>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'originlb'.$i}}" style="{{$ratef['classorigin']}}">
                                                {{$ratef['origin_portLb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('origin_port[]', $harbor,$ratef['origin_port'],['class'=>'m-select2-general form-control m-input lb'.$i,'style'=>$ratef['classorigin'],'id'=>'origin'.$i]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'destinylb'.$i}}" style="{{$ratef['classdestiny']}}">
                                                {{$ratef['destiny_portLb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('destiny_port', $harbor,$ratef['destiny_port'],['class'=>'m-select2-general m-input form-control lb'.$i,'style'=>$ratef['classdestiny'],'id'=>'destination'.$i]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'carrierlb'.$i}}" style="{{$ratef['classcarrier']}}">{{$ratef['carrierLb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('carrier_id', $carrierSelect,$ratef['carrierAIn'],['id' =>'carrier'.$i,'class'=>'m-select2-general form-control lb'.$i,'style' => $ratef['classcarrier']]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'twuentylb'.$i}}" style="{{$ratef['classtwuenty']}}">
                                                {{$ratef['twuenty']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="{{$ratef['classtwuenty']}}" name="twuenty" id="{{'twuenty'.$i}}" value="{{$ratef['twuenty']}}" class="form-control m-input {{'lb'.$i}}"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'fortylb'.$i}}" style="{{$ratef['classforty']}}">
                                                {{$ratef['forty']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="{{$ratef['classforty']}}" name="forty" id="{{'forty'.$i}}" value="{{$ratef['forty']}}" class="form-control m-input {{'lb'.$i}}"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'fortyhclb'.$i}}" style="{{$ratef['classfortyhc']}}">
                                                {{$ratef['fortyhc']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="{{$ratef['classfortyhc']}}" name="fortyhc" id="{{'fortyhc'.$i}}" value="{{$ratef['fortyhc']}}" class="form-control m-input {{'lb'.$i}}"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'currencylb'.$i}}" style="{{$ratef['classcurrency']}}">
                                                {{$ratef['currency_id']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden="hidden">
                                            {{ Form::select('currency_id', $currency,$ratef['currencyAIn'],['class'=>'m-select2-general m-input form-control lb'.$i,'style'=>$ratef['classcurrency'],'id'=>'currency'.$i]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <a  class=" {{'tdBTU'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill {{'tdAB'.$i}}" onclick="showbox({{$i}})" title="Edit ">
                                            <i class="la la-edit"></i>
                                        </a>

                                        <a class=" {{'tdAB'.$i}} {{'tdBTU'.$i}} DestroyRate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="DestroyRate({{$i}},{{$ratef['rate_id']}})" >
                                            <i class="la 	la-remove"></i>
                                        </a>

                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Close " onclick="hidebox({{$i}})" >
                                            <i class="la la-mail-reply"></i>
                                        </a>
                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Save " onclick="SaveCorrectRate({{$i}},{{$ratef['contract_id']}})" >
                                            <i class="la la-save"></i>
                                        </a>
                                        <input type="hidden" name="define" value="1" id="{{'accion'.$i}}" />
                                        <input type="hidden" name="idf" value="{{$ratef['rate_id']}}" id="{{'idf'.$i}}" />

                                    </td>

                                </tr>

                                @php
                                $i++
                                @endphp
                                @endforeach

                                @foreach ($rates as $rate)
                                <tr class="m-table__row--active" id="{{'trR'.$i}}">
                                    <td><i class="fa fa-dot-circle-o {{'icon'.$i}}" style="color:green; "></i></td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'originlb'.$i}}">
                                                {{$rate['port_origin']['name']}}   
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('origin_port',$harbor,$rate->origin_port,['class'=>'m-select2-general m-input form-control','id'=>'origin'.$i]) }}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'destinylb'.$i}}">
                                                {{$rate['port_destiny']['name']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('destiny_port',$harbor,$rate->destiny_port,['class'=>'m-select2-general m-input form-control','id'=>'destination'.$i])}}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}" id="{{'carrierlb'.$i}}">
                                            <label style="" id="{{'carrierlb'.$i}}">
                                                {{$rate->Carrier->name}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('carrier_id', $carrierSelect,$rate->carrier_id,['id' =>'carrier'.$i,'class'=>'m-select2-general form-control']) }}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'twuentylb'.$i}}">
                                                {{$rate->twuenty}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="" name="twuenty" id="{{'twuenty'.$i}}" value="{{$rate->twuenty}}" class="form-control m-input"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'fortylb'.$i}}">
                                                {{$rate->forty}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="" name="forty" id="{{'forty'.$i}}" value="{{$rate['forty']}}" class="form-control m-input"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'fortyhclb'.$i}}">
                                                {{$rate->fortyhc}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="" name="fortyhc" id="{{'fortyhc'.$i}}" value="{{$rate['fortyhc']}}" class="form-control m-input"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'currencylb'.$i}}">
                                                {{$rate->Currency->alphacode}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden="hidden">
                                            {{ Form::select('currency_id', $currency,$rate->currency_id,['class'=>'m-select2-general m-input form-control','id'=>'currency'.$i]) }}

                                        </div>
                                    </td>
                                    <td>
                                        <a  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill {{'tdAB'.$i}} {{'tdBTU'.$i}}" onclick="showbox({{$i}})" title="Edit ">
                                            <i class="la la-edit"></i>
                                        </a>

                                        <a class=" {{'tdAB'.$i}} {{'tdBTU'.$i}} DestroyRate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="DestroyRate({{$i}},{{$rate['id']}})" >
                                            <i class="la 	la-remove"></i>
                                        </a>

                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="hidebox({{$i}})" >
                                            <i class="la la-mail-reply"></i>
                                        </a>
                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Save " onclick="SaveCorrectRate({{$i}},{{$rate['contract_id']}})" >
                                            <i class="la la-save"></i>
                                        </a>
                                        <input type="hidden" name="define" value="2" id="{{'accion'.$i}}" />
                                        <input type="hidden" name="idf" value="{{$rate['id']}}" id="{{'idf'.$i}}" />
                                    </td>
                                    @php
                                    $i++
                                    @endphp
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--begin: Search Form -->
                    </div>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center"></div>
                    </div>

                </div>

                <!-- /////////////////////////////////////////////////////////////////////////////////////////// -->

                <div class="tab-pane " id="m_tabs_6_2" role="tabpanel">
                    <br>
                    <div class="m-portlet__head">
                        <label >
                            <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                            <strong >
                                Failed Surcharges: 
                            </strong>
                            <strong id="strfailSur">{{$countfailsurcharge}}</strong>
                            <input type="hidden" value="{{$countfailsurcharge}}" id="strfailinputSur" />
                        </label>
                        <br>
                        <label>
                            <i class="fa fa-dot-circle-o" style="color:green;"> </i>
                            <strong id="">
                                Good Surcharges: 
                            </strong>
                            <strong id="strgoodSur">
                                {{$countgoodsurcharge}}
                            </strong>
                            <input type="hidden" value="{{$countgoodsurcharge}}" id="strgoodinputSur" />
                        </label>
                    </div>
                    <!--<button onclick="prueba()">prueba</button>-->
                    <div class="m-portlet__body">
                        <table class="table m-table m-table--head-separator-primary "  id="html_table" >
                            <thead >
                                <tr>
                                    <th style="font-size: 12px;">
                                        Status
                                    </th>
                                    <th >
                                        Surcharge
                                    </th>
                                    <th >
                                        Origin
                                    </th>
                                    <th >
                                        Destiny
                                    </th>
                                    <th >
                                        Type Destiny
                                    </th>
                                    <th >
                                        Type Calculation 
                                    </th>
                                    <th >
                                        Amount
                                    </th>
                                    <th >
                                        Currency
                                    </th>
                                    <th >
                                        Carrier
                                    </th>
                                    <th >
                                        Options
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i=1 
                                @endphp
                                @foreach($failsurchargecoll as $surchargef)
                                <tr class="" id="{{'trRSur'.$i}}">
                                    <td>
                                        <i class="fa fa-dot-circle-o {{'icon'.$i}}" style="color:red;" id="" ></i>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'surchargelb'.$i}}" style="{{$surchargef['classsurcharge']}}">
                                                {{$surchargef['surchargelb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('surcharge_id', $surchargeSelect,$surchargef['surcharge_id'],['id' =>'surcharge'.$i,'class'=>'m-select2-general form-control lb'.$i,'style' => $surchargef['classsurcharge']]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'Suroriginlb'.$i}}" style="{{$surchargef['classorigin']}}">
                                                {{$surchargef['origin_portLb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('origin_port', $harbor,$surchargef['origin_port'],['class'=>'m-select2-general  form-control lb'.$i,'style'=>$surchargef['classorigin'],'id'=>'Surorigin'.$i,'multiple'=>'multiple']) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'Surdestinylb'.$i}}" style="{{$surchargef['classdestiny']}}">
                                                {{$surchargef['destiny_portLb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('destiny_port', $harbor,$surchargef['destiny_port'],['class'=>'m-select2-general form-control lb'.$i,'style'=>$surchargef['classdestiny'],'id'=>'Surdestination'.$i,'multiple'=>'multiple']) }}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'typedestinylb'.$i}}" style="{{$surchargef['classtypedestiny']}}">
                                                {{$surchargef['typedestinylb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('typedestiny_id', $typedestiny, $surchargef['typedestiny'],['id' =>'typedestiny'.$i,'class'=>'m-select2-general form-control lb'.$i,'style' => $surchargef['classdestiny']]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'calculationtypelb'.$i}}" style="{{$surchargef['classcalculationtype']}}">
                                                {{$surchargef['calculationtypelb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('calculationtype_id', $calculationtypeselect, $surchargef['calculationtype'],['id' =>'calculationtype'.$i,'class'=>'m-select2-general form-control lb'.$i,'style' => $surchargef['classcalculationtype']]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'ammountlb'.$i}}" style="{{$surchargef['classammount']}}">
                                                {{$surchargef['ammount']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="{{$surchargef['classammount']}}" name="forty" id="{{'ammount'.$i}}" value="{{$surchargef['ammount']}}" class="form-control m-input {{'lb'.$i}}"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'Surcurrencylb'.$i}}" style="{{$surchargef['classcurrency']}}">
                                                {{$surchargef['currencylb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden="hidden">
                                            {{ Form::select('currency_id', $currency,$surchargef['currency_id'],['class'=>'m-select2-general  form-control lb'.$i,'style'=>$surchargef['classcurrency'],'id'=>'Surcurrency'.$i]) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label class="{{'lb'.$i}}" id="{{'Surcarrierlb'.$i}}" style="{{$surchargef['classcarrier']}}">
                                                {{$surchargef['carrierlb']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('carrier_id', $carrierSelect,$surchargef['carrier_id'],['class'=>'m-select2-general form-control lb'.$i,'style'=>$surchargef['classcarrier'],'id'=>'Surcarrier'.$i,'multiple'=>'multiple']) }}
                                        </div>
                                    </td>
                                    <td>
                                        <a  class=" {{'tdBTU'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill {{'tdAB'.$i}}" onclick="showbox({{$i}})" title="Edit ">
                                            <i class="la la-edit"></i>
                                        </a>

                                        <a class=" {{'tdAB'.$i}} {{'tdBTU'.$i}} DestroyRate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="DestroySurcharge({{$i}})" >
                                            <i class="la la-remove"></i>
                                        </a>

                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Close " onclick="hidebox({{$i}})" >
                                            <i class="la la-mail-reply"></i>
                                        </a>
                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Save " onclick="SaveCorrectSurcharge({{$i}},{{$id}})" >
                                            <i class="la la-save"></i>
                                        </a>
                                        <input type="hidden" name="defineSur" value="1" id="{{'accionSur'.$i}}" /><input type="hidden" name="idfSur" value="{{$surchargef['failSrucharge_id']}}" id="{{'idfSur'.$i}}" />

                                    </td>

                                </tr>
                                <tr contextmenu="125"></tr>
                                @php
                                $i++
                                @endphp
                                @endforeach
                                @foreach($goodsurcharges as $goodsurcharge)
                                <tr class="m-table__row--active" id="{{'trRSur'.$i}}">
                                    <td><i class="fa fa-dot-circle-o {{'icon'.$i}}" style="color:green; "></i></td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'surchargelb'.$i}}">
                                                {{$goodsurcharge->Surcharge['name']}}   
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('surcharge_id',$surchargeSelect,$goodsurcharge['surcharge_id'],['class'=>'m-select2-general form-control','id'=>'surcharge'.$i]) }}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'Suroriginlb'.$i}}">

                                                {!! str_replace(["[","]","\""], ' ', $goodsurcharge->localcharports->pluck('portOrig')->unique()->pluck('name') ) !!}

                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>

                                            {{ Form::select('origin_port',$harbor,$goodsurcharge->localcharports->pluck('port_orig')->unique(),['class'=>'m-select2-general form-control','id'=>'Surorigin'.$i,'multiple'=>'multiple']) }}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'Surdestinylb'.$i}}">
                                                {!! str_replace(["[","]","\""], ' ', $goodsurcharge->localcharports->pluck('portDest')->unique()->pluck('name') ) !!}

                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('destiny_port[]',$harbor,$goodsurcharge->localcharports->pluck('port_dest')->unique(),['class'=>'form-control m-select2-general','id'=>'Surdestination'.$i,'multiple'=>'multiple'])}}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'typedestinylb'.$i}}">
                                                {{$goodsurcharge->TypeDestiny['description']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('typedestiny_id', $typedestiny, 
                                            $goodsurcharge['typedestiny_id'],['id' =>'typedestiny'.$i,'class'=>'m-select2-general form-control']) }} 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'calculationtypelb'.$i}}">
                                                {{$goodsurcharge->CalculationType['name']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            {{ Form::select('calculationtype_id', $calculationtypeselect,$goodsurcharge['calculationtype_id'],['id' =>'calculationtype'.$i,'class'=>'m-select2-general form-control']) }} 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'ammountlb'.$i}}">
                                                {{$goodsurcharge['ammount']}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>
                                            <input type="text" style="" name="ammount" id="{{'ammount'.$i}}" value="{{$goodsurcharge['ammount']}}" class="form-control m-input"> 
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}">
                                            <label style="" id="{{'Surcurrencylb'.$i}}">
                                                {{$goodsurcharge->Currency->alphacode}}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden="hidden">
                                            {{ Form::select('currency_id', $currency,$goodsurcharge->currency_id,['class'=>'m-select2-general form-control','id'=>'Surcurrency'.$i]) }}

                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{'tdAB'.$i}}" id="{{'Surcarrierlb'.$i}}">
                                            <label style="" id="{{'carrierlb'.$i}}">
                                                {!! str_replace(["[","]","\""], ' ', $goodsurcharge->localcharcarriers->pluck('carrier')->unique()->pluck('name') ) !!}
                                            </label>
                                        </div>
                                        <div class="in {{'tdIn'.$i}}" hidden>

                                            {{ Form::select('carrier_id', $carrierSelect,$goodsurcharge->localcharcarriers->pluck('carrier')->unique()->pluck('id'),['id' =>'Surcarrier'.$i,'class'=>'m-select2-general form-control','multiple'=>'multiple']) }}


                                        </div>
                                    </td>
                                    <td>
                                        <a  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill {{'tdAB'.$i}} {{'tdBTU'.$i}}" onclick="showbox({{$i}})" title="Edit ">
                                            <i class="la la-edit"></i>
                                        </a>

                                        <a class=" {{'tdAB'.$i}} {{'tdBTU'.$i}} DestroyRate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="DestroySurcharge({{$i}})" > 
                                            <i class="la 	la-remove"></i>
                                        </a>

                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Close    " onclick="hidebox({{$i}})" >
                                            <i class="la la-mail-reply"></i>
                                        </a>
                                        <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Save " onclick="SaveCorrectSurcharge({{$i}},{{$goodsurcharge['contract_id']}})" >
                                            <i class="la la-save"></i>
                                        </a>
                                        <input type="hidden" name="defineSur" value="2" id="{{'accionSur'.$i}}" />
                                        <input type="hidden" name="idfSur" value="{{$goodsurcharge['id']}}" id="{{'idfSur'.$i}}" />
                                    </td>
                                    @php
                                    $i++
                                    @endphp
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--begin: Search Form -->
                    </div>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-surcharge.js" type="text/javascript"></script>
<script src="{{asset('js/Contracts/RatesSuchargesNewContract.js')}}" type="text/javascript"></script>

@stop
