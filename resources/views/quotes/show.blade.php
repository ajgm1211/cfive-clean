
@extends('layouts.app')
@section('title', 'Details Quote #'. $quote->company_quote)
@section('content')

<div class="m-content">
    <div class="row">
        <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
        <div class="col-md-2 col-12" style="padding-bottom:10px;">
            <a href="{{route('quotes.edit',setearRouteKey($quote->id))}}" class="btn btn-primary btn-block"  title="Edit ">
                Edit
            </a>
        </div>
        <div class="col-md-2 col-12" style="padding-bottom:10px;">
            <a href="{{route('quotes.pdf',setearRouteKey($quote->id))}}" target="_blank" class="btn btn-primary btn-block">PDF</a>
        </div>
        <div class="col-md-2 col-12" style="padding-bottom:10px;">
            <a href="{{route('quotes.duplicate',setearRouteKey($quote->id))}}" class="btn btn-primary btn-block">Duplicate</a>
        </div>
        <div class="col-md-2 col-12" style="padding-bottom:10px;">
            <button data-toggle="modal" data-target="#SendQuoteModal" class="btn btn-primary btn-block">Send</button>
        </div>
        <div class="col-md-2 col-12" style="padding-bottom:10px;">
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
        <div class="col-lg-10 col-md-10 col-sm-10">
            <div class="row">
                <div class="col-md-12">
                    <div class="m-portlet__body">
                        <div class="m-portlet__head" style="min-height: 100px;">
                            <div class="m-portlet__head-tools">
                                <div class="col-md-12" style="margin-top: 20px;">
                                    @if($user->companyUser->logo!='')
                                        <div class="pull-left text-left" style="line-height: .5;">
                                            <img src="/{{$user->companyUser->logo}}" class="img img-responsive" style="width: 200px; height: auto; margin-bottom:35px">
                                        </div>
                                    @endif
                                    <div class="pull-right text-right" style="line-height: .5">
                                        <p><b>Quotation ID: <span style="color: #CFAC6C">#{{$quote->company_quote}}</span></b></p>
                                        <p><b>Date of issue:</b> {{date_format($quote->created_at, 'M d, Y H:i')}}</p>
                                        @if($quote->validity!='')
                                        @php
                                        $date = DateTime::createFromFormat('Y-m-d', $quote->validity);
                                        $validity = $date->format('M d, Y');
                                        @endphp
                                        <p><b>Validity:</b>  Valid up to {{$validity}}</p>
                                        @endif
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
                                        <br>
                                        <p>{{$user->name}}</p>
                                        <p><b>{{$user->companyUser->name}}</b></p>
                                        <p>{{$user->companyUser->address}}</p>
                                        <p>{{$user->phone}}</p>
                                        <p><a href="mailto:{{$user->email}}">{{$user->email}}</a></p>
                                    </div>
                                    <div class="pull-right text-right" style="line-height: .5">
                                        <p><b>To:</b></p>
                                        @if($quote->company->logo!='')
                                        <img src="/{{$quote->company->logo}}" class="img img-responsive" width="120" height="auto" style="margin-bottom:20px">
                                        @endif
                                        <p class="name size-12px">{{$quote->contact->first_name.' '.$quote->contact->last_name}}</p>
                                        <p><b>{{$quote->company->business_name}}</b></p>
                                        <p>{{$quote->company->address}}</p>
                                        <p>{{$quote->contact->phone}}</p>
                                        <p><a href="mailto:{{$quote->contact->email}}">{{$quote->contact->email}}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group m-form__group row">
                        <div class="col-md-6">
                            <div class="panel panel-default" >
                                <div class="panel-heading title-quote size-14px"><b>Origin {{$quote->type==3 ? ' Airport':' Port'}}</b></div>
                                <div class="panel-body">
                                    <span id="origin_input" class="color-blue">
                                        @if($quote->origin_harbor_id!='')
                                            {{$quote->origin_harbor->name}}, {{$quote->origin_harbor->code}}
                                        @endif
                                        @if($quote->origin_airport_id!='')
                                            {{$quote->origin_airport->name}}, {{$quote->origin_airport->code}}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading title-quote size-14px"><b>Destination {{$quote->type==3 ? ' Airport':' Port'}}</b></div>
                                <div class="panel-body">
                                    <span id="destination_input" class="color-blue">
                                        @if($quote->destination_harbor_id!='')
                                            {{$quote->destination_harbor->name}}, {{$quote->destination_harbor->code}}
                                        @endif
                                        @if($quote->destination_airport_id!='')
                                            {{$quote->destination_airport->name}}, {{$quote->destination_airport->code}}
                                        @endif                                        
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($quote->origin_address || $quote->destination_address)
            <div class="row">
                @if($quote->origin_address!='')
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading title-quote size-14px"><b>Origin address</b></div>
                        <div class="panel-body">
                            <span id="origin_input" class="color-blue">
                                {{$quote->origin_address}}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
                @if($quote->destination_address!='')
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading title-quote size-14px"><b>Destination address</b></div>
                        <div class="panel-body">
                            <span id="destination_input" class="color-blue">
                                {{$quote->destination_address}}                                     
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
            <div style="padding-top: 20px; padding-bottom: 20px;">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="title-quote size-14px">{{$quote->type==3 ? ' Airline':' Carrier'}}</h5>
                        <hr>
                    </div>
                    <div class="col-md-12">
                        @if($quote->carrier_id!='')
                        <p>{{$quote->carrier->name}}</p>
                        @endif
                        @if($quote->airline_id!='')
                        <p>{{$quote->airline->name}}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div style="padding-top: 20px; padding-bottom: 20px;">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="title-quote size-14px">Cargo details</h5>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p id="cargo_details_20_p">{{$quote->qty_20 != '' &&  $quote->qty_20 > 0 ? $quote->qty_20.' x 20\' Containers':''}}</p>
                        <p id="cargo_details_40_p">{{$quote->qty_40 != '' &&  $quote->qty_40 > 0 ? $quote->qty_40.' x 40\' Containers':''}}</p>
                        <p id="cargo_details_40_hc_p">{{$quote->qty_40_hc != '' &&  $quote->qty_40_hc > 0 ? $quote->qty_40_hc.' x 40\' HC container':''}}</p>
                        <p id="cargo_details_45_hc_p">{{$quote->qty_45_hc != '' &&  $quote->qty_45_hc > 0 ? $quote->qty_45_hc.' x 45\' HC container':''}}</p>
                        <p id="cargo_details_20_reefer_p">{{$quote->qty_20_reefer != '' &&  $quote->qty_20_reefer > 0 ? $quote->qty_20_reefer.' x 20\' Reefer container':''}}</p>
                        <p id="cargo_details_40_reefer_p">{{$quote->qty_40_reefer != '' &&  $quote->qty_40_reefer > 0 ? $quote->qty_40_reefer.' x 40\' Reefer container':''}}</p>
                        <p id="cargo_details_40_hc_reefer_p">{{$quote->qty_40_hc_reefer != '' &&  $quote->qty_40_hc_reefer > 0 ? $quote->qty_40_hc_reefer.' x 40\' HC Reefer container':''}}</p>
                        <p id="cargo_details_20_open_top_p">{{$quote->qty_20_open_top != '' &&  $quote->qty_20_open_top > 0 ? $quote->qty_20_open_top.' x 20\' Open Top container':''}}</p>
                        <p id="cargo_details_40_open_top_p">{{$quote->qty_40_open_top != '' &&  $quote->qty_40_open_top > 0 ? $quote->qty_40_open_top.' x 40\' Open Top container':''}}</p>
                        <p id="cargo_details_40_hc_open_top_p">{{$quote->qty_40_hc_open_top != '' &&  $quote->qty_40_hc_open_top > 0 ? $quote->qty_40_hc_open_top.' x 40\' HC Open Top container':''}}</p>                        
                    </div>
                </div>
                @if($quote->total_quantity!='' && $quote->total_quantity>0)
                <div class="row">
                    <div class="col-md-3">
                        <div id="cargo_details_cargo_type_p"><b>Cargo type:</b> {{$quote->type_cargo == 1 ? 'Pallets' : 'Packages'}}</div>
                    </div>
                    <div class="col-md-3">
                        <div id="cargo_details_total_quantity_p"><b>Total quantity:</b> {{$quote->total_quantity != '' ? $quote->total_quantity : ''}}</div>
                    </div>
                    <div class="col-md-3">
                        <div id="cargo_details_total_weight_p"><b>Total weight: </b> {{$quote->total_weight != '' ? $quote->total_weight.'Kg' : ''}}</div>
                    </div>
                    <div class="col-md-3">
                        <p id="cargo_details_total_volume_p"><b>Total volume: </b> {!!$quote->total_volume != '' ? $quote->total_volume.'m<sup>3</sup>' : ''!!}</p>
                    </div>
                </div>
                @endif
                @if(!empty($package_loads) && count($package_loads)>0)
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered color-blue">
                            <thead class="title-quote text-center header-table">
                                <tr>
                                    <td >Cargo type</td>
                                    <td >Quantity</td>
                                    <td >Height</td>
                                    <td >Width</td>
                                    <td >Large</td>
                                    <td >Weight</td>
                                    <td >Total weight</td>
                                    <td >Volume</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($package_loads as $package_load)
                                <tr class="text-center">
                                    <td>{{$package_load->type_cargo==1 ? 'Pallets':'Packages'}}</td>
                                    <td>{{$package_load->quantity}}</td>
                                    <td>{{$package_load->height}} cm</td>
                                    <td>{{$package_load->width}} cm</td>
                                    <td>{{$package_load->large}} cm</td>
                                    <td>{{$package_load->weight}} kg</td>
                                    <td>{{$package_load->total_weight}} kg</td>
                                    <td>{{$package_load->volume}} m<sup>3</sup></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>                    
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12 ">
                        <span class="pull-right">
                            <b>Total:</b> {{$package_loads->sum('quantity')}} un {{$package_loads->sum('volume')}} m<sup>3</sup> {{$package_loads->sum('total_weight')}} kg
                        </span>
                    </div>
                </div>
                @endif
            </div>
            @if(count($origin_ammounts)>0)
            <div class="row">
                <div class="col-md-12">
                    <h5 class="title-quote size-14px">Origin ammounts</h5>
                    <hr>
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
                                    <td >Total</td>
                                    <td >Markup</td>
                                    <td >Total {{$quote->currencies->alphacode}}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($origin_ammounts as $origin_ammount)
                                <tr class="text-center">
                                    <td>{{$origin_ammount->charge}}</td>
                                    <td>{{$origin_ammount->detail}}</td>
                                    <td>{{$origin_ammount->units}}</td>
                                    <td>{{$origin_ammount->price_per_unit}} {{$origin_ammount->currency->alphacode}}</td>
                                    <td>{{$origin_ammount->total_ammount}} {{$origin_ammount->currency->alphacode}}</td>
                                    <td>{{$origin_ammount->markup!='' ? $origin_ammount->markup:'0'}} {{$quote->currencies->alphacode}}</td>
                                    <td>{{$origin_ammount->total_ammount_2}} {{$quote->currencies->alphacode}}</td>
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
                        {{$quote->currencies->alphacode}}
                    </h5>
                </div>
            </div>
            @endif
            <br/>
            @if(count($freight_ammounts)>0)
            <div class="row">
                <div class="col-md-12">
                    <h5 class="title-quote size-14px">Freight ammounts</h5>
                    <hr>
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
                                    <td >Total</td>
                                    <td >Markup</td>
                                    <td >Total {{$quote->currencies->alphacode}}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($freight_ammounts as $freight_ammount)
                                <tr class="text-center">
                                    <td>{{$freight_ammount->charge}}</td>
                                    <td>{{$freight_ammount->detail}}</td>
                                    <td>{{$freight_ammount->units}}</td>
                                    <td>{{$freight_ammount->price_per_unit}} {{$freight_ammount->currency->alphacode}}</td>
                                    <td>{{$freight_ammount->total_ammount}} {{$freight_ammount->currency->alphacode}}</td>
                                    <td>{{$freight_ammount->markup!='' ? $freight_ammount->markup:'0'}} {{$quote->currencies->alphacode}}</td>
                                    <td>{{$freight_ammount->total_ammount_2}} {{$quote->currencies->alphacode}}</td>
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
                        {{$quote->currencies->alphacode}}
                    </h5>
                </div>
            </div>
            @endif
            <br/>
            @if(count($destination_ammounts)>0)
            <div class="row">
                <div class="col-md-12">
                    <h5 class="title-quote size-14px">Destination ammounts</h5>
                    <hr>
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
                                    <td >Total</td>
                                    <td >Markup</td>
                                    <td >Total {{$quote->currencies->alphacode}}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($destination_ammounts as $destination_ammount)
                                <tr class="text-center">
                                    <td>{{$destination_ammount->charge}}</td>
                                    <td>{{$destination_ammount->detail}}</td>
                                    <td>{{$destination_ammount->units}}</td>
                                    <td>{{$destination_ammount->price_per_unit}} {{$destination_ammount->currency->alphacode}}</td>
                                    <td>{{$destination_ammount->total_ammount}} {{$destination_ammount->currency->alphacode}}</td>
                                    <td>{{$destination_ammount->markup!='' ? $destination_ammount->markup:'0'}} {{$quote->currencies->alphacode}}</td>
                                    <td>{{$destination_ammount->total_ammount_2}} {{$quote->currencies->alphacode}}</td>
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
                        {{$quote->currencies->alphacode}}
                    </h5>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <hr>
                    <div class="form-group text-right">
                        <h3 class="size-16px color-blue"><button id="total" class="btn btn-primary"><b>Total: {{$quote->sub_total_origin + $quote->sub_total_freight + $quote->sub_total_destination }} {{$quote->currencies->alphacode}}</b></button></h3>
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
                    <div class="row">
                        <div class="col-md-12">      
                            <div class="header-table title-quote size-14px" style="padding-left: 10px;">
                                <b>Terms & conditions</b>                
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group terms-and-conditions">
                                @if($quote->type!=3)
                                @if(isset($terms_all) && $terms_all->count()>0)
                                @foreach($terms_all as $v)
                                {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                                @endforeach
                                @endif
                                @if(isset($terms_origin) && $terms_origin->count()>0)
                                @foreach($terms_origin as $v)
                                {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                                @endforeach
                                @endif
                                @if(isset($terms_destination) && $terms_destination->count()>0)
                                @foreach($terms_destination as $v)
                                {!! $quote->modality==1 ? $v->term->import : $v->term->export!!}
                                @endforeach
                                @endif
                                @endif
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2">
            <h3 class="title-quote size-16px">Settings</h3>
            <hr>
            <p class="title-quote size-14px" data-toggle="collapse" data-target="#main_currency" style="cursor: pointer">Main currency <i class="fa fa-angle-down pull-right"></i></p>

            <p class="settings size-12px" id="main_currency" class="collapse" style="font-weight: lighter">  @if(isset($currency_cfg->alphacode)){{$currency_cfg->alphacode}}@endif </p>
            <hr>
            <p class="title-quote title-quote size-14px" data-toggle="collapse" data-target="#exchange_rate" style="cursor: pointer">Exchange rate <i class="fa fa-angle-down pull-right"></i></p>
            <p class="settings size-12px" id="exchange_rate" style="font-weight: 100">@if($currency_cfg->alphacode=='EUR') 1 EUR = {{$exchange->rates}} USD @else 1 USD = {{$exchange->rates_eur}} EUR @endif</p>
            <hr>
            <label class="title-quote title-quote size-14px">PDF type</label>
            {!! Form::select('pdf_type', [1=>'All in',2=>'Detailed'],$user->companyUser->type_pdf, ['placeholder' => 'Please choose a option','class' => 'form-control','required' => 'required','id'=>'pdf_type']) !!}
            <hr>
            <label class="title-quote title-quote size-14px">PDF Ammounts</label>
            {!! Form::select('pdf_ammounts', [1=>'Main Currency',2=>'Original ammounts'],$user->companyUser->pdf_ammounts, ['placeholder' => 'Please choose a option','class' => 'form-control','required' => 'required','id'=>'pdf_ammounts']) !!}
        </div>
    </div>
</div>
@include('quotes.partials.sendQuoteModal')
@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/js/quote.js"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
<script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
@if(isset($pdf))
<script>window.open("{{ route('quotes.pdf', $quote->id) }}");</script>
@endif


@if(session('pdf'))

<script>

    window.open('{{ route('quotes.pdf', ['id' => setearRouteKey($quote->id)]) }}');

</script>
@endif

@stop











