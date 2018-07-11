
@extends('layouts.app')
@section('title', 'Details Quote #'.$quote->id)
@section('content')

<div class="m-content">
    <div class="row">
        <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
        <div class="col-md-2 col-xs-4">
            <a href="{{route('quotes.edit',$quote->id)}}" class="btn btn-primary btn-block"  title="Edit ">
                Edit
            </a>
        </div>
        <div class="col-md-2 col-xs-4">
            <a href="{{route('quotes.pdf',$quote->id)}}" target="_blank" class="btn btn-primary btn-block">PDF</a>
        </div>
        <div class="col-md-2 col-xs-4" >
            <button id="duplicate-quote" class="btn btn-primary btn-block">Duplicate</button>
            
        </div>
        <div class="col-md-2 col-xs-4" >
            <button data-toggle="modal" data-target="#SendQuoteModal" class="btn btn-info btn-block">Send</button>
        </div>
        <div class="col-md-2 col-xs-12">
            {{ Form::select('status_quote_id',$status_quotes,$quote->status_quote_id,['class'=>'m-select2-general form-control','required'=>'true','id'=>'status_quote_id','placeholder'=>'Select an option']) }}           
        </div>
    </div>
    <br>
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
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet__body">
                <div class="m-portlet__head" style="min-height: 100px;">
                    <div class="m-portlet__head-tools">
                        <div class="col-md-12" style="margin-top: 30px;">
                            <div class="pull-left text-left" style="line-height: .5">
                                <img src="{{$user->companyUser->logo}}" class="img img-responsive">
                            </div>
                            <div class="pull-right text-right" style="line-height: .5">                                
                                <p><b>Quotation ID: <span style="color: #CFAC6C">#{{$quote->id}}</span></b></p>
                                <p><b>Date of issue:</b> {{$quote->created_at}}</p>
                                <p><b>Validity:</b> {{$quote->created_at}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet__body">
                <div class="m-portlet__head" style="min-height: 150px;">
                    <div class="m-portlet__head-tools">
                        <div class="col-md-12">
                            <div class="pull-left text-left" style="line-height: .5">
                                <p><b>From:</b></p>
                                <p>{{$user->name}}</p>
                                <p><b>{{$user->companyUser->name}}</b></p>
                                <p>{{$user->companyUser->address}}</p>
                                <p>{{$user->companyUser->phone}}</p>
                            </div>
                            <div class="pull-right text-right" style="line-height: .5">
                                <p><b>To:</b></p>
                                <p class="name size-12px">{{$quote->contact->first_name.' '.$quote->contact->last_name}}</p>
                                <p><b>{{$quote->company->business_name}}</b></p>
                                <p>{{$quote->company->address}}</p>
                                <p>{{$quote->company->phone}}</p>
                                <p><a href="mailto:{{$quote->company->email}}">{{$quote->company->email}}</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group m-form__group row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading title-quote size-14px"><b>Origin</b></div>
                        <div class="panel-body">
                            <span id="origin_input" class="color-blue">
                                {{$origin_harbor->name}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading title-quote size-14px"><b>Destination</b></div>
                        <div class="panel-body">
                            <span id="destination_input" class="color-blue">
                                {{$destination_harbor->name}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: 20px; padding-bottom: 20px;">
        <div class="col-md-12 color-blue">
            <h5 class="title-quote size-14px">Cargo details</h5>
            <hr>
            <p id="cargo_details_20_p">{{$quote->qty_20 != '' ? $quote->qty_20.' x 20\' Containers':''}}</p>
            <p id="cargo_details_20_p">{{$quote->qty_40 != '' ? $quote->qty_40.' x 20\' Containers':''}}</p>
            <p id="cargo_details_20_p">{{$quote->qty_40_hc != '' ? $quote->qty_40_hc.' x 20\' Containers':''}}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <h5 class="title-quote size-14px">Origin ammounts</h5>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered color-blue">
                    <thead class="title-quote text-center header-table">
                        <tr>
                            <td >Charge</td>
                            <td >Detail</td>
                            <td >Units</td>
                            <td >Price per unit</td>
                            <td >Markup</td>
                            <td >Total</td>
                            <td >Total EUR</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($origin_ammounts as $origin_ammount)
                        <tr class="text-center">
                            <td>{{$origin_ammount->charge}}</td>
                            <td>{{$origin_ammount->detail}}</td>
                            <td>{{$origin_ammount->units}}</td>
                            <td>{{$origin_ammount->price_per_unit}} {{$origin_ammount->currency->alphacode}}</td>
                            <td>{{$origin_ammount->markup}}</td>
                            <td>{{$origin_ammount->total_ammount}}</td>
                            <td>{{$origin_ammount->total_ammount_2}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <br/>
            <h5 class="title-quote pull-right">
                Sub-Total: <span id="sub_total_origin">{{$quote->sub_total_origin}}</span>&nbsp;
                @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
            </h5>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-3">
            <h5 class="title-quote size-14px">Freight ammounts</h5>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered color-blue">
                    <thead class="title-quote text-center header-table">
                        <tr>
                            <td >Charge</td>
                            <td >Detail</td>
                            <td >Units</td>
                            <td >Price per unit</td>
                            <td >Markup</td>
                            <td >Total</td>
                            <td >Total EUR</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($freight_ammounts as $freight_ammount)
                        <tr class="text-center">
                            <td>{{$freight_ammount->charge}}</td>
                            <td>{{$freight_ammount->detail}}</td>
                            <td>{{$freight_ammount->units}}</td>
                            <td>{{$freight_ammount->price_per_unit}} {{$freight_ammount->currency->alphacode}}</td>
                            <td>{{$freight_ammount->markup}}</td>
                            <td>{{$freight_ammount->total_ammount}}</td>
                            <td>{{$freight_ammount->total_ammount_2}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <br/>
            <h5 class="title-quote pull-right">
                Sub-Total: <span id="sub_total_origin">{{$quote->sub_total_freight}}</span>&nbsp;
                @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
            </h5>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-3">
            <h5 class="title-quote size-14px">Destination ammounts</h5>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered color-blue">
                    <thead class="title-quote text-center header-table">
                        <tr>
                            <td >Charge</td>
                            <td >Detail</td>
                            <td >Units</td>
                            <td >Price per unit</td>
                            <td >Markup</td>
                            <td >Total</td>
                            <td >Total EUR</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($destination_ammounts as $destination_ammount)
                        <tr class="text-center">
                            <td>{{$destination_ammount->charge}}</td>
                            <td>{{$destination_ammount->detail}}</td>
                            <td>{{$destination_ammount->units}}</td>
                            <td>{{$destination_ammount->price_per_unit}} {{$destination_ammount->currency->alphacode}}</td>
                            <td>{{$destination_ammount->markup}}</td>
                            <td>{{$destination_ammount->total_ammount}}</td>
                            <td>{{$destination_ammount->total_ammount_2}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <br/>
            <h5 class="title-quote pull-right">
                Sub-Total: <span id="sub_total_origin">{{$quote->sub_total_destination}}</span>&nbsp;
                @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
            </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <hr>
            <div class="form-group text-right">
                <h3 class="size-16px color-blue"><button id="total" class="btn btn-primary"><b>Total: {{$quote->sub_total_origin + $quote->sub_total_freight + $quote->sub_total_destination }} @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</b></button></h3>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group text-left">
                <p>Exchange rate: @if($currency_cfg->alphacode=='EUR') 1 EUR = {{$exchange->rates}} USD @else 1 USD = {{$exchange->rates_eur}} EUR @endif</p>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">            
            <div class="header-table title-quote size-14px" style="padding-left: 10px;">
                <b>Terms & conditions</b>
            </div>
            <div class="row">                                               
                <div class="col-lg-12">
                    <div class="form-group ">
                        @if(isset($terms_origin) && $terms_origin->count()>0)                             
                        <h5 class="title-quote">Origin harbor</h5>
                        @foreach($terms_origin as $v)
                        {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                        @endforeach
                        @endif
                        @if(isset($terms_destination) && $terms_destination->count()>0)
                        <h5 class="title-quote">Destination harbor</h5>
                        @foreach($terms_destination as $v)
                        {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                        @endforeach
                        @endif
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
@include('quotes.partials.sendQuoteModal');
@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/js/quote.js"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="s/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>

@stop











