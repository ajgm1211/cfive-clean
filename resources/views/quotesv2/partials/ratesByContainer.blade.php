@php
$v=0;
@endphp
@foreach($rates as $rate)
<div class="row">
    <div class="col-md-12">
        <div class="m-portlet custom-portlet no-border">
            <div class="m-portlet__body padding-portlet">
                <div class="tab-content">
                    <div class="flex-list" style=" margin-bottom:-30px; margin-top: 0;">
                        <ul >
                            <li class="m-width-120" style="border-left:none;">
                                @if(isset($rate->carrier->image) && $rate->carrier->image!='')
                                <img src="{{ url('imgcarrier/'.$rate->carrier->image) }}"  class="img img-responsive" width="45" height="auto" style="margin-top: 15px;" />
                                @endif
                            </li>
                            <li class="size-12px long-text m-width-200"><b>POL:</b> &nbsp;{{$rate->origin_port->name.', '.$rate->origin_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->origin_country_code}}.svg"/></li>
                            <li class="size-12px long-text m-width-200"><b>POD:</b> &nbsp;{{$rate->destination_port->name.', '.$rate->destination_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->destination_country_code}}.svg"/></li>
                            <li class="size-12px long-text m-width-200 desktop"><b>Contract:</b> &nbsp;{{$rate->contract}}</li>
                            <li class="size-12px long-text m-width-100 desktop"><b>Type:</b> &nbsp;{{$rate->schedule_type}}</li>
                            <li class="size-12px m-width-100 desktop"><b>TT:</b> &nbsp;{{$rate->transit_time}}</li>
                            <li class="size-12px m-width-100 desktop" style="text-overflow: scroll;"><b>Via:</b> &nbsp;{{$rate->via}}</li>
                            <li class="size-12px no-border-left d-flex justify-content-end m-width-50">
                                <div onclick="show_hide_element('details_{{$v}}')"><i class="fa fa-angle-down"></i></div>
                            </li>
                            <li class="size-12px m-width-100">
                                <button onclick="AbrirModal('edit',{{$rate->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit">
                                    <i class="la la-edit"></i>
                                </button>

                                <button class="delete-rate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" data-rate-id="{{$rate->id}}">
                                    <i class="la la-trash"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <br>
                    <div class="details_{{$v}} rates hide" style="background-color: white; border-radius: 5px; margin-top: 20px;">
                        <!-- Freight charges -->
                        <div class="row no-mg-row">
                            <div class="col-md-12 header-charges">
                                <h5 class="title-quote size-12px">Freight charges</h5>
                            </div>
                            <div class="col-md-12 thead">
                                <div class="table-responsive">
                                    <table class="table fc table-sm table-bordered table-hover table color-blue text-center freight-table">
                                        <thead class="title-quote text-center header-table">
                                            <tr style="height: 40px;">
                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                <td class="td-table">Detail</td>
                                                <td class="td-table" {{ @$equipmentHides['20'] }}>20'</td>
                                                <td class="td-table" {{ @$equipmentHides['40'] }}>40'</td>
                                                <td class="td-table" {{ @$equipmentHides['40hc'] }}>40HC'</td>
                                                <td class="td-table" {{ @$equipmentHides['40nor'] }}>40NOR'</td>
                                                <td class="td-table" {{ @$equipmentHides['45'] }}>45'</td>
                                                <td class="td-table" >Currency</td>
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: white;">
                                            @php

                                            $i=0;
                                            $sum20=0;
                                            $sum40=0;
                                            $sum40hc=0;
                                            $sum40nor=0;
                                            $sum45=0;

                                            $sum_m20=0;
                                            $sum_m40=0;
                                            $sum_m40hc=0;
                                            $sum_m40nor=0;
                                            $sum_m45=0;

                                            @endphp

                                            @foreach($rate->charge as $item)
                                            @if($item->type_id==3)
                                            <?php
                                            $rate_id=$item->automatic_rate_id;

                                            $freight_amounts = json_decode($item->amount,true);
                                            $freight_markups = json_decode($item->markups,true);

                                            if(isset($freight_amounts['c20'])){
                                                $sum20+=$item->total_20;
                                            }
                                            if(isset($freight_amounts['c40'])){
                                                $sum40+=@$item->total_40;
                                            }
                                            if(isset($freight_amounts['c40hc'])){
                                                $sum40hc+=@$item->total_40hc;
                                            }
                                            if(isset($freight_amounts['c40nor'])){
                                                $sum40nor+=@$item->total_40nor;
                                            }
                                            if(isset($freight_amounts['c45'])){
                                                $sum45+=@$item->total_45;
                                            }

                                            if(isset($freight_markups['m20'])){
                                                $sum_m20+=$item->total_markup20;
                                            }
                                            if(isset($freight_markups['m40'])){
                                                $sum_m40+=@$item->total_markup40;
                                            }
                                            if(isset($freight_markups['m40hc'])){
                                                $sum_m40hc+=@$item->total_markup40hc;
                                            }
                                            if(isset($freight_markups['m40nor'])){
                                                $sum_m40nor+=@$item->total_markup40nor;
                                            }
                                            if(isset($freight_markups['m45'])){
                                                $sum_m45+=@$item->total_markup45;
                                            }
                                            ?>
                                            <tr class="tr-freight" style="height:40px;">
                                                <td class="tds" style="padding-left: 30px">
                                                    <input name="charge_id" value="{{$item->id}}" class="form-control charge_id" type="hidden" />
                                                    @if($item->surcharge_id!='')
                                                    <input name="type" value="1" class="form-control type" type="hidden" />
                                                    <a href="#" class="editable td-a" data-source="{{$surcharges}}" data-type="select" data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                    @else
                                                    <input name="type" value="2" class="form-control type" type="hidden" />
                                                    Ocean freight
                                                    @endif
                                                </td>
                                                <td class="tds">
                                                    @if($item->surcharge_id!='')
                                                    <a href="#" class="editable td-a" data-source="{{$calculation_types}}" data-type="select" data-name="calculation_type_id" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                    @else
                                                    Per Container
                                                    @endif
                                                </td>
                                                <td {{ @$equipmentHides['20'] }} class="tds">
                                                    <a href="#" class="editable-amount-20 amount_20 td-a" data-type="text" data-name="amount->c20" data-value="{{@$freight_amounts['c20']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-20 markup_20  td-a" data-type="text" data-name="markups->m20" data-value="{{@$freight_markups['m20']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>
                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_20 td-a">{{@$freight_amounts['c20']+@$freight_markups['m20']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40'] }} class="tds">
                                                    <a href="#" class="editable-amount-40 amount_40  td-a" data-type="text" data-name="amount->c40" data-value="{{@$freight_amounts['c40']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40 markup_40  td-a" data-type="text" data-name="markups->m40" data-value="{{@$freight_markups['m40']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40 td-a">{{@$freight_amounts['c40']+@$freight_markups['m40']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds">
                                                    <a href="#" class="editable-amount-40hc amount_40hc  td-a" data-type="text" data-name="amount->c40hc" data-value="{{@$freight_amounts['c40hc']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40hc markup_40hc  td-a" data-type="text" data-name="markups->m40hc" data-value="{{@$freight_markups['m40hc']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>
                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40hc td-a">{{@$freight_amounts['c40hc']+@$freight_markups['m40hc']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds">
                                                    <a href="#" class="editable-amount-40nor amount_40nor  td-a" data-type="text" data-name="amount->c40nor" data-value="{{@$freight_amounts['c40nor']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40nor markup_40nor  td-a" data-type="text" data-name="markups->m40nor" data-value="{{@$freight_markups['m40nor']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40nor td-a">{{@$freight_amounts['c40nor']+@$freight_markups['m40nor']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['45'] }} class="tds">
                                                    <a href="#" class="editable-amount-45 amount_45  td-a" data-type="text" data-name="amount->c45" data-value="{{@$freight_amounts['c45']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-45 markup_45  td-a" data-type="text" data-name="markups->m45" data-value="{{@$freight_markups['m45']}}" data-pk="{{$item->id}}" data-cargo-type="freight" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_45 td-a">{{@$freight_amounts['c45']+@$freight_markups['m45']}}</span>
                                                </td>
                                                <td class="tds">
                                                    <a href="#" class="editable td-a local_currency" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                    &nbsp;
                                                    <a class="delete-charge" style="cursor: pointer;" title="Delete">
                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            @php
                                            $i++;
                                            @endphp
                                            @endif
                                            @endforeach

                                            <!-- Hide Freight -->

                                            <tr class="hide" id="freight_charges_{{$v}}">
                                                <input name="number" value="{{$v}}" class="form-control number" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <input name="type_id" value="3" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <td>
                                                    {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                </td>
                                                <td>
                                                    {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                </td>
                                                <td {{ @$equipmentHides['20'] }} style="width: 20%">
                                                    <div class="row ">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_20" value="{{ @$equipmentHides['20'] }}" class="form-control hide_20" type="hidden" min="0" step="0.0000001" />
                                                                <input name="amount_c20" class="amount_c20 form-control" type="number" min="0" step="0.0000001" placeholder="Rate"/>

                                                                <input name="markup_m20" class="form-control markup_m20" type="number" min="0" step="0.0000001" placeholder="Markup"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40" value="{{ @$equipmentHides['40'] }}" class="form-control hide_40" type="hidden" min="0" step="0.0000001"/>
                                                                <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40" class="form-control markup_m40" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40hc'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40hc" value="{{ @$equipmentHides['40hc'] }}" class="form-control hide_40hc" type="hidden" min="0" step="0.0000001" style=""/>
                                                                <input name="amount_c40hc" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" placeholder="Rate"/>
                                                                <input name="markup_m40hc" class="form-control markup_m40hc" type="number" min="0" step="0.0000001" placeholder="Markup"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40nor'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40nor" value="{{ @$equipmentHides['40nor'] }}" class="form-control hide_40nor" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c40nor" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40nor" class="form-control markup_m40nor" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['45'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <input name="hide_45" value="{{ @$equipmentHides['45'] }}" class="form-control hide_45" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <input name="amount_c45" class="form-control amount_c45" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                        </div>
                                                        <div class="col-6">
                                                            <input name="markup_m45" class="form-control markup_m45" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-btn">
                                                            <div class="btn-group">
                                                                {{ Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id local_currency select-2-width']) }}
                                                            </div>
                                                            <a class="btn btn-xs btn-primary-plus store_charge">
                                                                <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                            <a class="btn btn-xs btn-primary-plus removeFreightCharge">
                                                                <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($rate->id == @$rate_id )
                                            <tr class="total_freight_{{$v}}">
                                                <td></td>
                                                <td class="title-quote size-12px tds" colspan=""><span class="td-a">Total</span></td>
                                                <td {{ @$equipmentHides['20'] }} class="tds"><span class="td-a total_freight_20">{{number_format(@$sum20+@$sum_m20, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40'] }} class="tds"><span class="td-a total_freight_40">{{number_format(@$sum40+@$sum_m40, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds"><span class="td-a total_freight_40hc">{{number_format(@$sum40hc+@$sum_m40hc, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds"><span class="td-a total_freight_40nor">{{number_format(@$sum40nor+@$sum_m40nor, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['45'] }} class="tds"><span class="td-a total_freight_45">{{number_format(@$sum45+@$sum_m45, 2, '.', '')}}</span></td>
                                                <td class="tds"><span class="td-a">{{$currency_cfg->alphacode}}</span></td>
                                                <input type="hidden" name="subtotal_c20_freight" value="{{$sum20}}" class="subtotal_c20_freight"/>
                                                <input type="hidden" name="subtotal_c40_freight" value="{{$sum40}}" class="subtotal_c40_freight"/>
                                                <input type="hidden" name="subtotal_c40hc_freight" value="{{$sum40hc}}" class="subtotal_c40hc_freight"/>
                                                <input type="hidden" name="subtotal_c40nor_freight" value="{{$sum40nor}}" class="subtotal_c40nor_freight"/>
                                                <input type="hidden" name="subtotal_c45_freight" value="{{$sum45}}" class="subtotal_c45_freight"/>

                                                <input type="hidden" name="subtotal_m20_freight" value="{{$sum_m20}}" class="subtotal_m20_freight"/>
                                                <input type="hidden" name="subtotal_m40_freight" value="{{$sum_m40}}" class="subtotal_m40_freight"/>
                                                <input type="hidden" name="subtotal_m40hc_freight" value="{{$sum_m40hc}}" class="subtotal_m40hc_freight"/>
                                                <input type="hidden" name="subtotal_m40nor_freight" value="{{$sum_m40nor}}" class="subtotal_m40nor_freight"/>
                                                <input type="hidden" name="subtotal_m45_freight" value="{{$sum_m45}}" class="subtotal_m45_freight"/>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <h5 class="title-quote pull-right">
                                    <b>Add freight charge</b><a class="btn" onclick="addFreightCharge({{$v}})" style="vertical-align: middle">
                                    <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                    </a>
                                </h5>
                            </div>
                        </div>

                        <!-- Origin charges -->

                        <div class="row no-mg-row">
                            <div class="col-md-12 header-charges">
                                <h5 class="title-quote size-12px">Origin charges</h5>
                            </div>
                            <div class="col-md-12 thead">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered color-blue text-center">
                                        <thead class="title-quote text-center header-table">
                                            <tr style="height: 40px;">
                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                <td class="td-table">Detail</td>
                                                <td class="td-table" {{ @$equipmentHides['20'] }}>20'</td>
                                                <td class="td-table" {{ @$equipmentHides['40'] }}>40'</td>
                                                <td class="td-table" {{ @$equipmentHides['40hc'] }}>40HC'</td>
                                                <td class="td-table" {{ @$equipmentHides['40nor'] }}>40NOR'</td>
                                                <td class="td-table" {{ @$equipmentHides['45'] }}>45'</td>
                                                <td class="td-table" >Currency</td>
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: white;">
                                            @php
                                            $a=0;
                                            $sum_origin_20=0;
                                            $sum_origin_40=0;
                                            $sum_origin_40hc=0;
                                            $sum_origin_40nor=0;
                                            $sum_origin_45=0;
                                            $sum_origin_m20=0;
                                            $sum_origin_m40=0;
                                            $sum_origin_m40hc=0;
                                            $sum_origin_m40nor=0;
                                            $sum_origin_m45=0;
                                            @endphp
                                            @foreach($rate->charge as $item)
                                            @if($item->type_id==1  && $item->saleterm==0)
                                            <?php
                                            $rate_id=$item->automatic_rate_id;
                                            $origin_amounts = json_decode($item->amount,true);
                                            $origin_markups = json_decode($item->markups,true);

                                            if(isset($origin_amounts['c20'])){
                                                $sum_origin_20+=$item->total_20;
                                            }
                                            if(isset($origin_amounts['c40'])){
                                                $sum_origin_40+=@$item->total_40;
                                            }
                                            if(isset($origin_amounts['c40hc'])){
                                                $sum_origin_40hc+=@$item->total_40hc;
                                            }
                                            if(isset($origin_amounts['c40nor'])){
                                                $sum_origin_40nor+=@$item->total_40nor;
                                            }
                                            if(isset($origin_amounts['c45'])){
                                                $sum_origin_45+=@$item->total_45;
                                            }

                                            if(isset($origin_markups['m20'])){
                                                $sum_origin_m20+=$item->total_markup20;
                                            }
                                            if(isset($origin_markups['m40'])){
                                                $sum_origin_m40+=@$item->total_markup40;
                                            }
                                            if(isset($origin_markups['m40hc'])){
                                                $sum_origin_m40hc+=@$item->total_markup40hc;
                                            }
                                            if(isset($origin_markups['m40nor'])){
                                                $sum_origin_m40nor+=@$item->total_markup40nor;
                                            }
                                            if(isset($origin_markups['m45'])){
                                                $sum_origin_m45+=@$item->total_markup45;
                                            }
                                            ?>
                                            <tr style="height:40px;">
                                                <td class="tds" style="padding-left: 30px">
                                                    <input name="type" value="1" class="form-control type" type="hidden" />
                                                    <input name="charge_id td-a" value="{{$item->id}}" class="form-control charge_id" type="hidden" />

                                                    <a href="#" class="editable surcharge_id td-a" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                </td>
                                                <td class="tds">
                                                    <a href="#" class="editable calculation_type_id td-a" data-source="{{$calculation_types}}" data-name="calculation_type_id" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                </td>
                                                <td {{ @$equipmentHides['20'] }} class="tds">
                                                    <a href="#" class="editable-amount-20 amount_20  td-a" data-type="text" data-name="amount->c20" data-value="{{@$origin_amounts['c20']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-20 markup_20  td-a" data-type="text" data-name="markups->m20" data-value="{{@$origin_markups['m20']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>
                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_20 td-a">{{@$origin_amounts['c20']+@$origin_markups['m20']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40'] }} class="tds">
                                                    <a href="#" class="editable-amount-40 amount_40  td-a" data-type="text" data-name="amount->c40" data-value="{{@$origin_amounts['c40']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40 markup_40  td-a" data-type="text" data-name="markups->m40" data-value="{{@$origin_markups['m40']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40 td-a">{{@$origin_amounts['c40']+@$origin_markups['m40']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds">
                                                    <a href="#" class="editable-amount-40hc amount_40hc  td-a" data-type="text" data-name="amount->c40hc" data-value="{{@$origin_amounts['c40hc']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40hc markup_40hc  td-a" data-type="text" data-name="markups->m40hc" data-value="{{@$origin_markups['m40hc']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>
                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40hc td-a">{{@$origin_amounts['c40hc']+@$origin_markups['m40hc']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds">
                                                    <a href="#" class="editable-amount-40nor amount_40nor  td-a" data-type="text" data-name="amount->c40nor" data-value="{{@$origin_amounts['c40nor']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40nor markup_40nor  td-a" data-type="text" data-name="markups->m40nor" data-value="{{@$origin_markups['m40nor']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40nor td-a">{{@$origin_amounts['c40nor']+@$origin_markups['m40nor']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['45'] }} class="tds">
                                                    <a href="#" class="editable-amount-45 amount_45  td-a" data-type="text" data-name="amount->c45" data-value="{{@$origin_amounts['c45']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-45 markup_45  td-a" data-type="text" data-name="markups->m45" data-value="{{@$origin_markups['m45']}}" data-pk="{{$item->id}}" data-cargo-type="origin" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_45 td-a">{{@$origin_amounts['c45']+@$origin_markups['m45']}}</span>
                                                </td>
                                                <td class="tds">
                                                    <a href="#" class="editable td-a local_currency" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                    &nbsp;
                                                    <a class="delete-charge" style="cursor: pointer;" title="Delete">
                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            @php
                                            $a++;
                                            @endphp
                                            @endif
                                            @endforeach

                                            <!-- Hide origin charges-->

                                            <tr class="hide" id="origin_charges_{{$v}}">
                                                <input name="number" value="{{$v}}" class="form-control number" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <input name="type_id" value="1" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <td>
                                                    {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                </td>
                                                <td>
                                                    {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                </td>
                                                <td {{ @$equipmentHides['20'] }} style="width: 20%">
                                                    <div class="row ">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_20" value="{{ @$equipmentHides['20'] }}" class="form-control hide_20" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c20" class="amount_c20 form-control" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m20" class="form-control markup_m20" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40" value="{{ @$equipmentHides['40'] }}" class="form-control hide_40" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40" class="form-control markup_m40" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40hc'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40hc" value="{{ @$equipmentHides['40hc'] }}" class="form-control hide_40hc" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c40hc" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40hc" class="form-control markup_m40hc" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40nor'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40nor" value="{{ @$equipmentHides['40nor'] }}" class="form-control hide_40nor" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c40nor" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40nor" class="form-control markup_m40nor" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['45'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_45" value="{{ @$equipmentHides['45'] }}" class="form-control hide_45" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c45" class="form-control amount_c45" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m45" class="form-control markup_m45" type="number" min="0" step="0.0000001"  placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-btn">
                                                            <div class="btn-group">
                                                                {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control currency_id local_currency select-2-width']) }}
                                                            </div>
                                                            <a class="btn btn-xs btn-primary-plus store_charge">
                                                                <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                            <a class="btn btn-xs btn-primary-plus removeOriginCharge">
                                                                <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($rate->id == @$rate_id )
                                            <tr class="total_origin_{{$v}}">
                                                <td></td>
                                                <td class="title-quote size-12px tds" colspan=""><span class="td-a">Total</span></td>
                                                <td {{ @$equipmentHides['20'] }} class="tds"><span class="td-a total_origin_20">{{number_format(@$sum_origin_20+@$sum_origin_m20, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40'] }} class="tds"><span class="td-a total_origin_40">{{number_format(@$sum_origin_40+@$sum_origin_m40, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds"><span class="td-a total_origin_40hc">{{number_format(@$sum_origin_40hc+@$sum_origin_m40hc, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds"><span class="td-a total_origin_40nor">{{number_format(@$sum_origin_40nor+@$sum_origin_m40nor, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['45'] }} class="tds"><span class="td-a total_origin_45">{{number_format(@$sum_origin_45+@$sum_origin_m45, 2, '.', '')}}</span></td>
                                                <td class="tds"><span class="td-a">{{$currency_cfg->alphacode}}</span></td>
                                                <input type="hidden" name="subtotal_c20_origin" value="{{$sum_origin_20}}" class="subtotal_c20_origin"/>
                                                <input type="hidden" name="subtotal_c40_origin" value="{{$sum_origin_40}}" class="subtotal_c40_origin"/>
                                                <input type="hidden" name="subtotal_c40hc_origin" value="{{$sum_origin_40hc}}" class="subtotal_c40hc_origin"/>
                                                <input type="hidden" name="subtotal_c40nor_origin" value="{{$sum_origin_40nor}}" class="subtotal_c40nor_origin"/>
                                                <input type="hidden" name="subtotal_c45_origin" value="{{$sum_origin_45}}" class="subtotal_c45_origin"/>

                                                <input type="hidden" name="subtotal_m20_origin" value="{{$sum_origin_m20}}" class="subtotal_m20_origin"/>
                                                <input type="hidden" name="subtotal_m40_origin" value="{{$sum_origin_m40}}" class="subtotal_m40_origin"/>
                                                <input type="hidden" name="subtotal_m40hc_origin" value="{{$sum_origin_m40hc}}" class="subtotal_m40hc_origin"/>
                                                <input type="hidden" name="subtotal_m40nor_origin" value="{{$sum_origin_m40nor}}" class="subtotal_m40nor_origin"/>
                                                <input type="hidden" name="subtotal_m45_origin" value="{{$sum_origin_m45}}" class="subtotal_m45_origin"/>                                                
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <h5 class="title-quote pull-right">
                                    <b>Add origin charge</b>
                                    <a class="btn" onclick="addOriginCharge({{$v}})" style="vertical-align: middle">
                                        <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                    </a>
                                </h5>
                            </div>
                        </div>

                        <!-- Destination charges -->

                        <div class="row no-mg-row">
                            <div class="col-md-12 header-charges">
                                <h5 class="title-quote size-12px">Destination charges</h5>
                            </div>
                            <div class="col-md-12 thead">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered color-blue text-center">
                                        <thead class="title-quote text-center header-table">
                                            <tr style="height: 40px;">
                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                <td class="td-table">Detail</td>
                                                <td class="td-table" {{ @$equipmentHides['20'] }}>20'</td>
                                                <td class="td-table" {{ @$equipmentHides['40'] }}>40'</td>
                                                <td class="td-table" {{ @$equipmentHides['40hc'] }}>40HC'</td>
                                                <td class="td-table" {{ @$equipmentHides['40nor'] }}>40NOR'</td>
                                                <td class="td-table" {{ @$equipmentHides['45'] }}>45'</td>
                                                <td class="td-table" >Currency</td>
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: white;">
                                            @php
                                            $a=0;
                                            $sum_destination_20=0;
                                            $sum_destination_40=0;
                                            $sum_destination_40hc=0;
                                            $sum_destination_40nor=0;
                                            $sum_destination_45=0;
                                            $sum_destination_m20=0;
                                            $sum_destination_m40=0;
                                            $sum_destination_m40hc=0;
                                            $sum_destination_m40nor=0;
                                            $sum_destination_m45=0;
                                            @endphp

                                            @foreach($rate->charge as $item)
                                            @if($item->type_id==2 && $item->saleterm==0)
                                            <?php
                                            $rate_id=$item->automatic_rate_id;
                                            $destination_amounts = json_decode($item->amount,true);
                                            $destination_markups = json_decode($item->markups,true);


                                            if(isset($destination_amounts['c20'])){
                                                $sum_destination_20+=$item->total_20;
                                            }
                                            if(isset($destination_amounts['c40'])){
                                                $sum_destination_40+=@$item->total_40;
                                            }
                                            if(isset($destination_amounts['c40hc'])){
                                                $sum_destination_40hc+=@$item->total_40hc;
                                            }
                                            if(isset($destination_amounts['c40nor'])){
                                                $sum_destination_40nor+=@$item->total_40nor;
                                            }
                                            if(isset($destination_amounts['c45'])){
                                                $sum_destination_45+=@$item->total_45;
                                            }

                                            if(isset($destination_markups['m20'])){
                                                $sum_destination_m20+=$item->total_markup20;
                                            }
                                            if(isset($destination_markups['m40'])){
                                                $sum_destination_m40+=@$item->total_markup40;
                                            }
                                            if(isset($destination_markups['m40hc'])){
                                                $sum_destination_m40hc+=@$item->total_markup40hc;
                                            }
                                            if(isset($destination_markups['m40nor'])){
                                                $sum_destination_m40nor+=@$item->total_markup40nor;
                                            }
                                            if(isset($destination_markups['m45'])){
                                                $sum_destination_m45+=@$item->total_markup45;
                                            }

                                            ?>

                                            <tr style="height:40px;">
                                                <td class="tds" style="padding-left: 30px">
                                                    <input name="type" value="1" class="form-control type" type="hidden" />
                                                    <input name="charge_id" value="{{$item->id}}" class="form-control charge_id td-a" type="hidden" />

                                                    <a href="#" class="editable surcharge_id td-a" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                </td>
                                                <td class="tds">
                                                    <a href="#" class="editable calculation_type_id td-a" data-source="{{$calculation_types}}" data-name="calculation_type_id" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                </td>
                                                <td {{ @$equipmentHides['20'] }} class="tds">
                                                    <a href="#" class="editable-amount-20 amount_20  td-a" data-type="text" data-name="amount->c20" data-value="{{@$destination_amounts['c20']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-20 markup_20  td-a" data-type="text" data-name="markups->m20" data-value="{{@$destination_markups['m20']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>
                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_20 td-a">{{@$destination_amounts['c20']+@$destination_markups['m20']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40'] }} class="tds">
                                                    <a href="#" class="editable-amount-40 amount_40  td-a" data-type="text" data-name="amount->c40" data-value="{{@$destination_amounts['c40']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40 markup_40  td-a" data-type="text" data-name="markups->m40" data-value="{{@$destination_markups['m40']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40 td-a">{{@$destination_amounts['c40']+@$destination_markups['m40']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds">
                                                    <a href="#" class="editable-amount-40hc amount_40hc  td-a" data-type="text" data-name="amount->c40hc" data-value="{{@$destination_amounts['c40hc']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40hc markup_40hc  td-a" data-type="text" data-name="markups->m40hc" data-value="{{@$destination_markups['m40hc']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>
                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40hc td-a">{{@$destination_amounts['c40hc']+@$destination_markups['m40hc']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds">
                                                    <a href="#" class="editable-amount-40nor amount_40nor  td-a" data-type="text" data-name="amount->c40nor" data-value="{{@$destination_amounts['c40nor']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-40nor markup_40nor  td-a" data-type="text" data-name="markups->m40nor" data-value="{{@$destination_markups['m40nor']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_40nor td-a">{{@$destination_amounts['c40nor']+@$destination_markups['m40nor']}}</span>
                                                </td>
                                                <td {{ @$equipmentHides['45'] }} class="tds">
                                                    <a href="#" class="editable-amount-45 amount_45  td-a" data-type="text" data-name="amount->c45" data-value="{{@$destination_amounts['c45']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>
                                                    +
                                                    <a href="#" class="editable-markup-45 markup_45  td-a" data-type="text" data-name="markups->m45" data-value="{{@$destination_markups['m45']}}" data-pk="{{$item->id}}" data-cargo-type="destination" data-title="Total"></a>

                                                    <i class="la la-caret-right arrow-down"></i>
                                                    <span class="total_45 td-a">{{@$destination_amounts['c45']+@$destination_markups['m45']}}</span>
                                                </td>
                                                <td class="tds">
                                                    <a href="#" class="editable td-a local_currency" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                    &nbsp;
                                                    <a class="delete-charge" style="cursor: pointer;" title="Delete">
                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            @php
                                            $a++;
                                            @endphp
                                            @endif
                                            @endforeach

                                            <!-- Hide destination charges -->

                                            <tr class="hide" id="destination_charges_{{$v}}">
                                                <input name="number" value="{{$v}}" class="form-control number" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <input name="type_id" value="2" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                <td>
                                                    {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                </td>
                                                <td>
                                                    {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                </td>
                                                <td {{ @$equipmentHides['20'] }} style="width: 20%">
                                                    <div class="row ">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_20" value="{{ @$equipmentHides['20'] }}" class="form-control hide_20" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c20" class="amount_c20 form-control" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m20" class="form-control markup_m20" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40" value="{{ @$equipmentHides['40'] }}" class="form-control hide_40" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40" class="form-control markup_m40" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40hc'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40hc" value="{{ @$equipmentHides['40hc'] }}" class="form-control hide_40hc" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c40hc" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40hc" class="form-control markup_m40hc" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['40nor'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="input-group">
                                                                <input name="hide_40nor" value="{{ @$equipmentHides['40nor'] }}" class="form-control hide_40nor" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                <input name="amount_c40nor" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                                <input name="markup_m40nor" class="form-control markup_m40nor" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td {{ @$equipmentHides['45'] }} style="width: 20%">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <input name="hide_45" value="{{ @$equipmentHides['45'] }}" class="form-control hide_45" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <input name="amount_c45" class="form-control amount_c45" type="number" min="0" step="0.0000001" placeholder="Rate" />
                                                            <input name="markup_m45" class="form-control markup_m45" type="number" min="0" step="0.0000001" placeholder="Markup" />
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-btn">
                                                            <div class="btn-group">
                                                                {{ Form::select('destination_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control local_currency currency_id select-2-width']) }}
                                                            </div>
                                                            <a class="btn btn-xs btn-primary-plus store_charge">
                                                                <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                            <a class="btn btn-xs btn-primary-plus removeOriginCharge">
                                                                <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($rate->id == @$rate_id )
                                            <tr class="total_destination_{{$v}}">
                                                <td></td>
                                                <td class="title-quote size-12px tds" colspan=""><span class="td-a">Total</span></td>
                                                <td {{ @$equipmentHides['20'] }} class="tds"><span class="td-a total_destination_20">{{number_format(@$sum_destination_20+@$sum_destination_m20, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40'] }} class="tds"><span class="td-a total_destination_40">{{number_format(@$sum_destination_40+@$sum_destination_m40, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds"><span class="td-a total_destination_40hc">{{number_format(@$sum_destination_40hc+@$sum_destination_m40hc, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds"><span class="td-a total_destination_40nor">{{number_format(@$sum_destination_40nor+@$sum_destination_m40nor, 2, '.', '')}}</span></td>
                                                <td {{ @$equipmentHides['45'] }} class="tds"><span class="td-a total_destination_45">{{number_format(@$sum_destination_45+@$sum_destination_m45, 2, '.', '')}}</span></td>
                                                <td class="tds"><span class="td-a">{{$currency_cfg->alphacode}}</span></td>
                                                
                                                <input type="hidden" name="subtotal_c20_destination" value="{{$sum_destination_20}}" class="subtotal_c20_destination"/>
                                                <input type="hidden" name="subtotal_c40_destination" value="{{$sum_destination_40}}" class="subtotal_c40_destination"/>
                                                <input type="hidden" name="subtotal_c40hc_destination" value="{{$sum_destination_40hc}}" class="subtotal_c40hc_destination"/>
                                                <input type="hidden" name="subtotal_c40nor_destination" value="{{$sum_destination_40nor}}" class="subtotal_c40nor_destination"/>
                                                <input type="hidden" name="subtotal_c45_destination" value="{{$sum_destination_45}}" class="subtotal_c45_destination"/>

                                                <input type="hidden" name="subtotal_m20_destination" value="{{$sum_destination_m20}}" class="subtotal_m20_destination"/>
                                                <input type="hidden" name="subtotal_m40_destination" value="{{$sum_destination_m40}}" class="subtotal_m40_destination"/>
                                                <input type="hidden" name="subtotal_m40hc_destination" value="{{$sum_destination_m40hc}}" class="subtotal_m40hc_destination"/>
                                                <input type="hidden" name="subtotal_m40nor_destination" value="{{$sum_destination_m40nor}}" class="subtotal_m40nor_destination"/>
                                                <input type="hidden" name="subtotal_m45_destination" value="{{$sum_destination_m45}}" class="subtotal_m45_destination"/>                                                 
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <h5 class="title-quote pull-right">
                                    <b>Add destination charge</b>
                                    <a class="btn" onclick="addDestinationCharge({{$v}})" style="vertical-align: middle">
                                        <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                    </a>
                                </h5>
                            </div>
                        </div>
                        <br>
                        <!-- Totals -->
                        @if($rate->id == @$rate_id )
                        <div class="row no-mg-row">
                            <div class="col-md-12 header-charges">
                                <h5 class="title-quote size-12px">Totals</h5>
                            </div>
                            <div class="col-md-12 thead">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover table color-blue text-center">
                                        <thead class="title-quote text-center header-table">
                                            <tr style="height:40px;">
                                                <td class="tds" style="padding-left: 30px"></td>
                                                <td {{ @$equipmentHides['20'] }} class="tds">20'</td>
                                                <td {{ @$equipmentHides['40'] }} class="tds">40'</td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds">40HC'</td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds">40NOR'</td>
                                                <td {{ @$equipmentHides['45'] }} class="tds">45'</td>
                                                <td class="tds">Currency</td>
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: white;">
                                            <?php
                                            $amount_20=number_format(@$sum20+@$sum_origin_20+@$sum_destination_20, 2, '.', '');
                                            $amount_40=number_format(@$sum40+@$sum_origin_40+@$sum_destination_40, 2, '.', '');
                                            $amount_40hc=number_format(@$sum40hc+@$sum_origin_40hc+@$sum_destination_40hc, 2, '.', '');
                                            $amount_40nor=number_format(@$sum40nor+@$sum_origin_40nor+@$sum_destination_40nor, 2, '.', '');
                                            $amount_45=number_format(@$sum45+@$sum_origin_45+@$sum_destination_45, 2, '.', '');

                                            $markup_20=number_format(@$sum_m20+@$sum_origin_m20+@$sum_destination_m20, 2, '.', '');
                                            $markup_40=number_format(@$sum_m40+@$sum_origin_m40+@$sum_destination_m40, 2, '.', '');
                                            $markup_40hc=number_format(@$sum_m40hc+@$sum_origin_m40hc+@$sum_destination_m40hc, 2, '.', '');
                                            $markup_40nor=number_format(@$sum_m40nor+@$sum_origin_m40nor+@$sum_destination_m40nor, 2, '.', '');
                                            $markup_45=number_format(@$sum_m45+@$sum_origin_m45+@$sum_destination_m45, 2, '.', '');

                                            $amount_markup_20=number_format($amount_20+$markup_20, 2, '.', '');
                                            $amount_markup_40=number_format($amount_40+$markup_40, 2, '.', '');
                                            $amount_markup_40hc=number_format($amount_40hc+$markup_40hc, 2, '.', '');
                                            $amount_markup_40nor=number_format($amount_40nor+$markup_40nor, 2, '.', '');
                                            $amount_markup_45=number_format($amount_45+$markup_45, 2, '.', '');
                                            ?>
                                            <tr style="height:40px;">
                                                <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>
                                                <td {{ @$equipmentHides['20'] }} class="tds"><span class=" td-a sum_total_amount_20">{{$amount_20}}</span> + <span class=" td-a sum_total_markup_20">{{$markup_20}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_20 td-a">{{$amount_markup_20}}</span></td>
                                                <td {{ @$equipmentHides['40'] }} class="tds"><span class=" td-a sum_total_amount_40">{{$amount_40}}</span> + <span class=" td-a sum_total_markup_40">{{$markup_40}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_40 td-a">{{$amount_markup_40}}</span></td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds"><span class=" td-a sum_total_amount_40hc">{{$amount_40hc}}</span> + <span class=" td-a sum_total_markup_40hc">{{$markup_40hc}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_40hc td-a">{{$amount_markup_40hc}}</span></td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds"><span class=" td-a sum_total_amount_40nor">{{$amount_40nor}}</span> + <span class=" td-a sum_total_markup_40nor">{{$markup_40nor}}</span> <i class="la la-caret-right arrow-down"></i><span class="sum_total_40nor td-a">{{$amount_markup_40nor}}</span></td>
                                                <td {{ @$equipmentHides['45'] }} class="tds"><span class=" td-a sum_total_amount_45">{{$amount_45}}</span> + <span class=" td-a sum_total_markup_45 td-a">{{$markup_45}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_45">{{$amount_markup_45}}</span></td>
                                                <td class="tds"><span class="td-a global-currency">{{$currency_cfg->alphacode}}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    <br>
                                    <br>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="row no-mg-row">
                            <div class="col-md-12 header-charges">
                                <h5 class="title-quote size-12px">Totals</h5>
                            </div>
                            <div class="col-md-12 thead">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover table color-blue text-center">
                                        <thead class="title-quote text-center header-table">
                                            <tr style="height:40px;">
                                                <td class="tds" style="padding-left: 30px"></td>
                                                <td {{ @$equipmentHides['20'] }} class="tds">20'</td>
                                                <td {{ @$equipmentHides['40'] }} class="tds">40'</td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds">40HC'</td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds">40NOR'</td>
                                                <td {{ @$equipmentHides['45'] }} class="tds">45'</td>
                                                <td class="tds">Currency</td>
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: white;">
                                            <?php
                                            $amount_20=number_format(@$sum20+@$sum_origin_20+@$sum_destination_20, 2, '.', '');
                                            $amount_40=number_format(@$sum40+@$sum_origin_40+@$sum_destination_40, 2, '.', '');
                                            $amount_40hc=number_format(@$sum40hc+@$sum_origin_40hc+@$sum_destination_40hc, 2, '.', '');
                                            $amount_40nor=number_format(@$sum40nor+@$sum_origin_40nor+@$sum_destination_40nor, 2, '.', '');
                                            $amount_45=number_format(@$sum45+@$sum_origin_45+@$sum_destination_45, 2, '.', '');

                                            $markup_20=number_format(@$sum_m20+@$sum_origin_m20+@$sum_destination_m20, 2, '.', '');
                                            $markup_40=number_format(@$sum_m40+@$sum_origin_m40+@$sum_destination_m40, 2, '.', '');
                                            $markup_40hc=number_format(@$sum_m40hc+@$sum_origin_m40hc+@$sum_destination_m40hc, 2, '.', '');
                                            $markup_40nor=number_format(@$sum_m40nor+@$sum_origin_m40nor+@$sum_destination_m40nor, 2, '.', '');
                                            $markup_45=number_format(@$sum_m45+@$sum_origin_m45+@$sum_destination_m45, 2, '.', '');

                                            $amount_markup_20=number_format($amount_20+$markup_20, 2, '.', '');
                                            $amount_markup_40=number_format($amount_40+$markup_40, 2, '.', '');
                                            $amount_markup_40hc=number_format($amount_40hc+$markup_40hc, 2, '.', '');
                                            $amount_markup_40nor=number_format($amount_40nor+$markup_40nor, 2, '.', '');
                                            $amount_markup_45=number_format($amount_45+$markup_45, 2, '.', '');
                                            ?>
                                            <tr style="height:40px;">
                                                <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>
                                                <td {{ @$equipmentHides['20'] }} class="tds"><span class=" td-a sum_total_amount_20">{{$amount_20}}</span> + <span class=" td-a sum_total_markup_20">{{$markup_20}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_20 td-a">{{$amount_markup_20}}</span></td>
                                                <td {{ @$equipmentHides['40'] }} class="tds"><span class=" td-a sum_total_amount_40">{{$amount_40}}</span> + <span class=" td-a sum_total_markup_40">{{$markup_40}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_40 td-a">{{$amount_markup_40}}</span></td>
                                                <td {{ @$equipmentHides['40hc'] }} class="tds"><span class=" td-a sum_total_amount_40hc">{{$amount_40hc}}</span> + <span class=" td-a sum_total_markup_40hc">{{$markup_40hc}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_40hc td-a">{{$amount_markup_40hc}}</span></td>
                                                <td {{ @$equipmentHides['40nor'] }} class="tds"><span class=" td-a sum_total_amount_40nor">{{$amount_40nor}}</span> + <span class=" td-a sum_total_markup_40nor">{{$markup_40nor}}</span> <i class="la la-caret-right arrow-down"></i><span class="sum_total_40nor td-a">{{$amount_markup_40nor}}</span></td>
                                                <td {{ @$equipmentHides['45'] }} class="tds"><span class=" td-a sum_total_amount_45">{{$amount_45}}</span> + <span class=" td-a sum_total_markup_45 td-a">{{$markup_45}}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_45">{{$amount_markup_45}}</span></td>
                                                <td class="tds"><span class="td-a global-currency">{{$currency_cfg->alphacode}}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    <br>
                                    <br>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Inlands -->
                        <div class="row no-mg-row">
                            <div class="col-md-12 header-charges">
                                <h5 class="title-quote size-12px">Inlands</h5>
                            </div>
                            <div class="col-md-12 thead">
                                <div class="">
                                    <div class="">
                                        @php
                                        $x=0;
                                        @endphp
                                        @if(!$rate->inland->isEmpty())
                                        @foreach($rate->inland as $inland)
                                        <?php
                                        $inland_rates = json_decode($inland->rate,true);
                                        $inland_markups = json_decode($inland->markup,true);
                                        ?>
                                        <span >
                                            <div class="tab-content">
                                                <div class="flex-list">
                                                    <ul >
                                                        <input name="inland_id" value="{{$inland->id}}" class="form-control inland_id" type="hidden" />
                                                        <li ><i class="fa fa-truck" style="font-size: 2rem"></i></li>
                                                        <li class="size-12px">{{$inland->port->name}}, {{$inland->port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($inland->port->code, 0, 2))}}.svg"></li>
                                                        <li class="size-12px">Type: {{$inland->type}}</li>
                                                        <li class="size-12px">Contract: {{$rate->contract}}</li>
                                                        <li class="size-12px no-border-left d-flex justify-content-end">
                                                            <div onclick="show_hide_element('details_inland_{{$x}}')"><i class="fa fa-angle-down"></i></div>
                                                        </li>
                                                        <li>
                                                            <button onclick="AbrirModal('editInland',{{$inland->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit">
                                                                <i class="la la-edit"></i>
                                                            </button>

                                                            <button class="delete-inland m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete">
                                                                <i class="la la-trash"></i>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="details_inland_{{$x}}">
                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                            <tr style="height: 40px;">
                                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                                <td class="td-table">Distance</td>
                                                                <td class="td-table" {{ @$equipmentHides['20'] }}>20'</td>
                                                                <td class="td-table" {{ @$equipmentHides['40'] }}>40'</td>
                                                                <td class="td-table" {{ @$equipmentHides['40hc'] }}>40HC'</td>
                                                                <td class="td-table" {{ @$equipmentHides['40nor'] }}>40NOR'</td>
                                                                <td class="td-table" {{ @$equipmentHides['45'] }}>45'</td>
                                                                <td class="td-table" >Currency</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                            <tr style="height:40px;">
                                                                <td class="tds" style="padding-left: 30px">
                                                                    <input name="inland_id" value="{{$inland->id}}" class="form-control inland_id" type="hidden" />
                                                                    <a href="#" class="editable-inland provider td-a" data-type="text" data-value="{{$inland->provider}}" data-name="provider" data-pk="{{@$inland->id}}" data-title="Provider"></a>
                                                                </td>
                                                                <td class="tds">
                                                                    <a href="#" class="editable-inland distance td-a" data-type="text" data-name="distance" data-value="{{@$inland->distance}}" data-pk="{{@$inland->id}}" data-title="Distance"></a> &nbsp;km
                                                                </td>
                                                                <td {{ @$equipmentHides['20'] }} class="tds">
                                                                    <a href="#" class="editable-inland-20 amount_20 td-a" data-type="text" data-name="rate->c20" data-value="{{@$inland_rates['c20']}}" data-pk="{{@$inland->id}}" data-title="Amount"></a>
                                                                    +
                                                                    <a href="#" class="editable-inland-m20 markup_20 td-a" data-type="text" data-name="markup->m20" data-value="{{@$inland_markups['m20']}}" data-pk="{{@$inland->id}}" data-title="Markup"></a>
                                                                    <i class="la la-caret-right arrow-down"></i>
                                                                    <span class="total_20 td-a">{{@$inland_rates['c20']+@$inland_markups['m20']}}</span>
                                                                </td>
                                                                <td {{ @$equipmentHides['40'] }} class="tds">
                                                                    <a href="#" class="editable-inland-40 amount_40 td-a" data-type="text" data-name="rate->c40" data-value="{{@$inland_rates['c40']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    +
                                                                    <a href="#" class="editable-inland-m40 markup_40 td-a"data-type="text" data-name="markup->m40" data-value="{{@$inland_markups['m40']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    <i class="la la-caret-right arrow-down"></i>
                                                                    <span class="total_40 td-a">{{@$inland_rates['c40']+@$inland_markups['m40']}}</span>
                                                                </td>
                                                                <td {{ @$equipmentHides['40hc'] }} class="tds">
                                                                    <a href="#" class="editable-inland-40hc amount_40hc td-a" data-type="text" data-name="rate->c40hc" data-value="{{@$inland_rates['c40hc']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    +
                                                                    <a href="#" class="editable-inland-m40hc markup_40hc td-a" data-type="text" data-name="markup->m40hc" data-value="{{@$inland_markups['m40hc']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    <i class="la la-caret-right arrow-down"></i>
                                                                    <span class="total_40hc td-a">{{@$inland_rates['c40hc']+@$inland_markups['m40hc']}}</span>
                                                                </td>
                                                                <td {{ @$equipmentHides['40nor'] }} class="tds">
                                                                    <a href="#" class="editable-inland-40nor amount_40nor td-a" data-type="text" data-name="rate->c40nor" data-value="{{@$inland_rates['c40nor']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    +
                                                                    <a href="#" class="editable-inland-m40nor markup_40nor td-a" data-type="text" data-name="markup->m40nor" data-value="{{@$inland_markups['m40nor']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    <i class="la la-caret-right arrow-down"></i>
                                                                    <span class="total_40nor td-a">{{@$inland_rates['c40nor']+@$inland_markups['m40nor']}}</span>
                                                                </td>
                                                                <td {{ @$equipmentHides['45'] }} class="tds">
                                                                    <a href="#" class="editable-inland-45 amount_45 td-a" data-type="text" data-name="rate->45" data-value="{{@$inland_rates['c45']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    +
                                                                    <a href="#" class="editable-inland-m45 markup_45 td-a" data-type="text" data-name="markup->m45" data-value="{{@$inland_markups['m45']}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                    <i class="la la-caret-right arrow-down"></i>
                                                                    <span class="total_45 td-a">{{@$inland_rates['c45']+@$inland_markups['m45']}}</span>
                                                                </td>
                                                                <td class="tds">
                                                                    <a href="#" class="editable-inland td-a" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$inland->currency_id}}" data-pk="{{@$inland->id}}" data-title="Select currency"></a>
                                                                    &nbsp;
                                                                </td>
                                                            </tr>
                                                            <tr style="height:40px;" class="hide" id="inland_charges_{{$x}}">
                                                                <td class="tds" style="padding-left: 30px">
                                                                    <input class="provider form-control" type="text" value="" name="provider" title="Provider" placeholder="Charge">
                                                                </td>
                                                                <td class="tds">
                                                                    <input class="distance form-control" type="text" name="distance" value="" title="Distance" placeholder="Distance">
                                                                </td>
                                                                <td {{ @$equipmentHides['20'] }} class="tds">
                                                                    <div class="input-group">
                                                                        <input class="amount_20 form-control" type="text" name="amount_20" value="" title="Distance" placeholder="Rate">
                                                                        <input class="markup_20 form-control" type="text" name="markup_20" value="" title="Distance" placeholder="Markup">
                                                                    </div>
                                                                </td>
                                                                <td {{ @$equipmentHides['40'] }} class="tds">
                                                                    <div class="input-group">
                                                                        <input class="amount_40 form-control" type="text" name="amount_40" value="" title="Distance" placeholder="Rate">
                                                                        <input class="markup_40 form-control" type="text" name="markup_40" value="" title="Distance" placeholder="Markup">
                                                                    </div>
                                                                </td>
                                                                <td {{ @$equipmentHides['40hc'] }} class="tds">
                                                                    <div class="input-group">
                                                                        <input class="amount_40hc form-control" type="text" name="amount_40hc" value="" title="Distance" placeholder="Rate">
                                                                        <input class="markup_40hc form-control" type="text" name="markup_40hc" value="" title="Distance" placeholder="Markup">
                                                                    </div>
                                                                </td>
                                                                <td {{ @$equipmentHides['40nor'] }} class="tds">
                                                                    <div class="input-group">
                                                                        <input class="amount_40nor form-control" type="text" name="amount_40nor" value="" title="Distance" placeholder="Rate">
                                                                        <input class="markup_40nor form-control" type="text" name="markup_40nor" value="" title="Distance" placeholder="Markup">
                                                                    </div>
                                                                </td>
                                                                <td {{ @$equipmentHides['45'] }} class="tds">
                                                                    <div class="input-group">
                                                                        <input class="amount_45 form-control" type="text" name="amount_45" value="" title="Distance" placeholder="Rate">
                                                                        <input class="markup_45 form-control" type="text" name="markup_45" value="" title="Distance" placeholder="Markup">
                                                                    </div>
                                                                </td>
                                                                <td class="tds">
                                                                    <div class="btn-group">
                                                                        {{ Form::select('destination_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
                                                                    </div>
                                                                    <br>
                                                                    <a class="btn btn-xs btn-primary-plus store_charge">
                                                                        <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                                                    </a>
                                                                    <a class="btn btn-xs btn-primary-plus removeOriginCharge">
                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </span>
                                        <br>
                                        @php
                                        $x++;
                                        @endphp
                                        @endforeach
                                        <div class='row'>
                                            <div class="col-md-12 ">
                                                <div class="m-portlet__body">
                                                    <button class="btn btn-primary-v2 btn-edit pull-right open-inland-modal" data-rate-id="{{$rate->id}}" data-toggle="modal" data-target="#createInlandModal">
                                                        Add inland &nbsp;&nbsp;<i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class='row'>
                                            <div class="col-md-12 ">
                                                <div class="m-portlet__body">
                                                    <button class="btn btn-primary-v2 btn-edit open-inland-modal" data-rate-id="{{$rate->id}}" data-toggle="modal" data-target="#createInlandModal">
                                                        Add inland &nbsp;&nbsp;<i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <!-- Remarks -->
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="">
                                    <div class="header-charges" >
                                        <div class="row " style="padding-left: 20px;">
                                            <h5 class="title-quote size-12px">Remarks</h5>
                                        </div>
                                    </div>
                                    <div class="m-portlet__body">
                                        <div class="remarks_span_{{$v}}">
                                            <button class="btn btn-primary-v2 edit-remarks btn-edit" onclick="edit_remark('remarks_span_{{$v}}','remarks_textarea_{{$v}}','update_remarks_{{$v}}')" style="margin-bottom: 12px;">
                                                Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                            </button>
                                            <div class="card card-body bg-light remarks_box_{{$v}}">
                                                <span>{!! $rate->remarks !!}</span>
                                                <br>
                                            </div>
                                        </div>
                                        <div class="remarks_textarea_{{$v}}" hidden>
                                            <textarea name="remarks_{{$v}}" class="form-control remarks_{{$v}} editor">{!!$rate->remarks!!}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-center update_remarks_{{$v}}"  hidden>
                                                <br>
                                                <button class="btn btn-danger cancel-remarks_{{$v}}" onclick="cancel_update('remarks_span_{{$v}}','remarks_textarea_{{$v}}','update_remarks_{{$v}}')">
                                                    Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                                </button>
                                                <button class="btn btn-primary update-remarks_{{$v}}" onclick="update_remark({{$rate->id}},'remarks_{{$v}}',{{$v}})">
                                                    Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                                </button>
                                                <br>
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
@php
$v++;
@endphp
@endforeach