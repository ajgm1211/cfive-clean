@extends('layouts.app')
@section('title', 'Details Quote #'.$quote->id)
@section('content')

<div class="m-content">
    <div class="row">
        <div class="col-md-1">
            <a href="{{route('quotes.edit',$quote->id)}}" class="btn btn-info btn-block"  title="Edit ">
                Edit
            </a>
        </div>
        <div class="col-md-1">
            <a href="{{route('quotes.pdf',$quote->id)}}" target="_blank" class="btn btn-primary btn-block">PDF</a>
        </div>
        <div class="col-md-1">
            <button data-toggle="modal" data-target="#SendQuoteModal" class="btn btn-info btn-block">Send</button>
            <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
        </div>
        <div class="col-md-1">
            <button id="duplicate-quote" class="btn btn-info ">Duplicate</button>
            <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
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
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet__body">
                    <div class="row">
                        <div class="m-portlet m-portlet--tabs">
                            <div class="m-portlet__head" style="min-height: 150px;">
                                <div class="m-portlet__head-tools">
                                    <div class="col-md-12" style="margin-top: 30px;">
                                        <div class="pull-left text-left" style="line-height: .5">
                                            <p><b>From:</b></p>
                                            <p>{{$user->name}}</p>
                                            <p><b>{{$user->companyUser->name}}</b></p>
                                            <p>{{$user->companyUser->address}}</p>
                                            <p>{{$user->companyUser->phone}}</p>
                                        </div>
                                        <div class="pull-right" style="line-height: .5">
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
                            <div class="m-portlet__body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_portlet_tab_1_1">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading"><b>Origin</b></div>
                                                                    <div class="panel-body">
                                                                        <span id="origin_input">
                                                                            {{$origin_harbor->name}}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading"><b>Destination</b></div>
                                                                    <div class="panel-body">
                                                                        <span id="destination_input">
                                                                            {{$destination_harbor->name}}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row" style="padding-top: 20px; padding-bottom: 20px;">
                                                            <div class="col-md-12">
                                                                <h5 class="title-quote">Cargo details</h5>
                                                                <hr>
                                                                <p id="cargo_details_20_p">{{$quote->qty_20 != '' ? $quote->qty_20.' x 20\' Containers':''}}</p>
                                                                <p id="cargo_details_20_p">{{$quote->qty_40 != '' ? $quote->qty_40.' x 20\' Containers':''}}</p>
                                                                <p id="cargo_details_20_p">{{$quote->qty_40_hc != '' ? $quote->qty_40_hc.' x 20\' Containers':''}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <h5 class="title-quote">Origin ammounts</h5>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-3">Charge</div>
                                                            <div class="col-md-3">Detail</div>
                                                            <div class="col-md-1">Units</div>
                                                            <div class="col-md-2">Price per unit</div>
                                                            <div class="col-md-1">Markup</div>
                                                            <div class="col-md-1">Total</div>
                                                            <div class="col-md-1">Total EUR</div>
                                                        </div>
                                                        <hr>
                                                        @foreach($origin_ammounts as $origin_ammount)
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input type="text" class="form-control" id="origin_ammount_charge" value="{{$origin_ammount->charge}}" name="origin_ammount_charge[]" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="{{$origin_ammount->detail}}" class="form-control" type="text" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="origin_ammount_units" name="origin_ammount_units[]" value="{{$origin_ammount->units}}" class="form-control origin_ammount_units" type="number" min="0" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <div class="input-group">
                                                                        <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="{{$origin_ammount->price_per_unit}}" min="1" step="0.01" class="origin_price_per_unit form-control" aria-label="..." readonly>
                                                                        <div class="input-group-btn">
                                                                            <div class="btn-group">
                                                                                {{ Form::select('origin_ammount_currency[]',$currencies,$origin_ammount->currency_id,['class'=>'m-select2-general form-control','disabled'=>true]) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="{{$origin_ammount->markup}}" class="form-control origin_ammount_markup" type="number" min="0" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="origin_total_ammount" name="origin_total_ammount[]" value="{{$origin_ammount->total_ammount}}" class="form-control origin_total_ammount" type="number" min="0" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1" >
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <input type="text" name="origin_total_ammount_2[]"  value="{{$origin_ammount->total_ammount_2}}" class="form-control" aria-label="..." readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        <div class='row'>                                                            
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <h5 class="title-quote pull-right">
                                                                        Sub-Total: <span id="sub_total_origin">{{$quote->sub_total_origin}}</span>&nbsp;
                                                                        @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                                                        <input type="hidden" id="total_origin_ammount" name="sub_total_origin" value="{{$quote->sub_total_origin}}" class="form-control" readonly/>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <h5 class="title-quote">Freight ammounts</h5>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-3">Charge</div>
                                                            <div class="col-md-3">Detail</div>
                                                            <div class="col-md-1">Units</div>
                                                            <div class="col-md-2">Price per unit</div>
                                                            <div class="col-md-1">Markup</div>
                                                            <div class="col-md-1">Total</div>
                                                            <div class="col-md-1">Total EUR</div>
                                                        </div>
                                                        <hr>
                                                        @foreach($freight_ammounts as $freight_ammount)
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input type="text" class="form-control" id="freight_ammount_charge" value="{{$freight_ammount->charge}}" name="freight_ammount_charge[]" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="freight_ammount_detail" name="freight_ammount_detail[]" value="{{$freight_ammount->detail}}" class="form-control" type="text" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="freight_ammount_units" name="freight_ammount_units[]" value="{{$freight_ammount->units}}" class="form-control freight_ammount_units" min="0" max="99" type="number" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <div class="input-group">
                                                                        <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" value="{{$freight_ammount->price_per_unit}}" min="1" step="0.01" class="form-control freight_price_per_unit" aria-label="..." readonly>
                                                                        <div class="input-group-btn">
                                                                            <div class="btn-group">
                                                                                {{ Form::select('freight_ammount_currency[]',$currencies,$freight_ammount->currency_id,['class'=>'m-select2-general form-control','disabled'=>true]) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="freight_ammount_markup" name="freight_ammount_markup[]" value="{{$freight_ammount->markup}}" class="form-control freight_ammount_markup" min="0" type="number" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1" >
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <input type="text" name="freight_total_ammount[]" value="{{$freight_ammount->total_ammount}}" class="form-control freight_total_ammount" aria-label="..." readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" value="{{$freight_ammount->total_ammount_2}}" class="form-control" min="0" type="number" readonly/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach                                                        
                                                        <div class='row'>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <h5 class="title-quote pull-right">
                                                                        Sub-Total: <span id="sub_total_freight">{{$quote->sub_total_freight}}</span>&nbsp;
                                                                        @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                                                        <input type="hidden" id="total_freight_ammount" name="sub_total_freight" value="{{$quote->sub_total_freight}}"  class="form-control"/>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group m-form__group row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <h5 class="title-quote">Destination ammounts</h5>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-3">Charge</div>
                                                            <div class="col-md-3">Detail</div>
                                                            <div class="col-md-1">Units</div>
                                                            <div class="col-md-2">Price per unit</div>
                                                            <div class="col-md-1">Markup</div>
                                                            <div class="col-md-1">Total</div>
                                                            <div class="col-md-1">Total EUR</div>
                                                        </div>
                                                        <hr>
                                                        @foreach($destination_ammounts as $destination_ammount)
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input type="text" class="form-control" id="destination_ammount_charge" value="{{$destination_ammount->charge}}" name="destination_ammount_charge[]" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="destination_ammount_detatil" name="destination_ammount_detail[]" value="{{$destination_ammount->detail}}" class="form-control" type="text" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="destination_ammount_units" name="destination_ammount_units[]" value="{{$destination_ammount->units}}" class="form-control destination_ammount_units" type="number" min="0" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <div class="input-group">
                                                                        <input type="number" id="destination_ammount" name="destination_price_per_unit[]" value="{{$destination_ammount->price_per_unit}}" min="1" step="0.01" class="destination_price_per_unit form-control" aria-label="..." readonly>
                                                                        <div class="input-group-btn">
                                                                            <div class="btn-group">
                                                                                {{ Form::select('destination_ammount_currency[]',$currencies,$destination_ammount->currency_id,['class'=>'m-select2-general form-control','disabled'=>true]) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="destination_ammount_markup" name="destination_ammount_markup[]"  value="{{$destination_ammount->markup}}" class="form-control destination_ammount_markup" type="number" min="0" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <input id="destination_total_ammount" name="destination_total_ammount[]"  value="{{$destination_ammount->total_ammount}}" class="form-control destination_total_ammount" type="number" min="0" readonly/>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1" >
                                                                <div class="m-bootstrap-touchspin-brand">
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <input type="text" name="destination_total_ammount_2[]"  value="{{$destination_ammount->total_ammount_2}}" class="form-control" aria-label="..." readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach                                                    
                                                        <div class='row'>                                                            
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <h5 class="title-quote pull-right">
                                                                        Sub-Total: <span id="sub_total_destination">{{$quote->sub_total_destination}}</span>&nbsp
                                                                        @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif
                                                                        <input type="hidden" id="total_destination_ammount" name="sub_total_destination" value="{{$quote->sub_total_destination}}" class="form-control"/>
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <br>
                                                                <br>
                                                                <div class="form-group text-right">
                                                                    <h3 class="size-16px color-blue"><b>Total:</b> <span id="total">{{$quote->sub_total_origin + $quote->sub_total_freight + $quote->sub_total_destination }} @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif</span></h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <br> 
                                                        @if($terms_destination->count()>0 || $terms_origin->count()>0)
                                                        <div class="form-group m-form__group row">
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <b>TERMS & CONDITIONS</b>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                            </div>
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
                                                        @endif                                                      
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
<script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>

@stop
