<div class="row">
    <div class="col-md-12">
        <div class="">
            <div class=" ">
                <!-- Rates -->
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
                                                @if($quote->type!='AIR')
                                                    @if(isset($rate->carrier->image) && $rate->carrier->image!='')
                                                        <img src="{{ url('imgcarrier/'.$rate->carrier->image) }}"  class="img img-responsive" width="50" height="auto" style="margin-top: 10px;" />
                                                    @endif
                                                @else
                                                    @if(isset($rate->airline->image) && $rate->airline->image!='')
                                                        <img src="{{ url('images/airlines/'.$rate->airline->image) }}"  class="img img-responsive" width="90" height="auto" style="margin-top: 10px;" />
                                                    @endif
                                                @endif
                                            </li>
                                            <li class="size-12px long-text m-width-200"><b>POL:</b> &nbsp;@if($quote->type=='LCL') {{$rate->origin_port->name.', '.$rate->origin_port->code}}  @else {{$rate->origin_airport->display_name}} @endif &nbsp;
                                                @if($quote->type!='AIR')
                                                    <img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->origin_country_code}}.svg"/>
                                                @endif
                                            </li>
                                            <li class="size-12px long-text m-width-200"><b>POD:</b> &nbsp;@if($quote->type=='LCL') {{$rate->destination_port->name.', '.$rate->destination_port->code}} @else {{$rate->destination_airport->display_name}} @endif &nbsp;
                                                @if($quote->type!='AIR')
                                                    <img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->destination_country_code}}.svg"/>
                                                @endif
                                            </li>
                                            <li class="size-12px long-text m-width-200 desktop"><b>Contract:</b> &nbsp;{{$rate->contract}}</li>
                                            <li class="size-12px long-text m-width-100 desktop"><b>Type:</b> &nbsp;{{$rate->schedule_type}}</li>
                                            <li class="size-12px long-text m-width-100 desktop"><b>TT:</b> &nbsp;{{$rate->transit_time}}</li>
                                            <li class="size-12px long-text m-width-100 desktop"><b>Via:</b> &nbsp;{{$rate->via}}</li>
                                            <li class="size-12px no-border-left d-flex justify-content-end m-width-50">
                                                <div onclick="show_hide_element('details_{{$v}}')"><i class="fa fa-angle-down"></i></div>
                                            </li>
                                            <li class="size-12px m-width-100">
                                                <button onclick="AbrirModal('edit',{{$rate->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit">
                                                    <i class="la la-edit"></i>
                                                </button>

                                                <button class="delete-rate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" data-rate-id="{{$rate->id}}">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </li>                      
                                        </ul>
                                    </div>
                                    <br>
                                    <div class="details_{{$v}} hide amount_charges" style="background-color: white; border-radius: 5px; margin-top: 20px;">
                                        <!-- Freight charges -->
                                        <div class="row no-mg-row">
                                            <div class="col-md-12 header-charges">
                                                <h5 class="title-quote size-12px">Freight charges</h5>
                                            </div>
                                            <div class="col-md-12 thead freight_box">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover charges color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                            <tr style="height: 40px;">
                                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                                <td class="td-table">Detail</td>
                                                                <td class="td-table">Units</td>
                                                                <td class="td-table">Rate</td>
                                                                <td class="td-table">Markup</td>
                                                                <td class="td-table">Total</td>
                                                                <td class="td-table">Currency</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                            <?php
                                                                $total_freight=0;
                                                                $total_origin=0;
                                                                $total_destination=0;
                                                                $total_freight_units=0;
                                                                $total_freight_rates=0;
                                                                $total_freight_markups=0;
                                                            ?>
                                                            @foreach($rate->charge_lcl_air as $item)
                                                                @if($item->type_id==3)
                                                                    <?php
                                                                        $rate_id=$item->automatic_rate_id;
                                                                        $total_freight+=$item->total_freight;
                                                                        $total_freight_units+=$item->units;
                                                                        $total_freight_rates+=$item->price_per_unit*$item->units;
                                                                        $total_freight_markups+=$item->markup;
                                                                    ?>
                                                                    <tr style="height:40px;">
                                                                        <td class="tds" style="padding-left: 30px">
                                                                            <input name="charge_id" value="{{@$item->id}}" class="form-control charge_id" type="hidden" style="max-width: 50px;"/>
                                                                            @if($item->surcharge_id!='')
                                                                                <input name="type" value="1" class="form-control type" type="hidden" />
                                                                                <a href="#" class="editable-lcl-air td-a" data-source="{{$surcharges}}" data-type="select" data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{@$item->id}}" data-title="Select surcharge"></a>
                                                                            @else
                                                                                <input name="type" value="2" class="form-control type" type="hidden" />
                                                                                {{$quote->type=='LCL' ? 'Ocean Freight':'Freight'}}
                                                                            @endif
                                                                        </td>
                                                                        <td class="tds">
                                                                            @if($item->surcharge_id!='')
                                                                                <a href="#" class="editable-lcl-air td-a" data-source="{{$calculation_types_lcl_air}}" data-type="select" data-name="calculation_type_id" data-value="{{$item->calculation_type_id}}" data-pk="{{@$item->id}}" data-title="Select calculation type"></a>
                                                                                @else
                                                                                {{$quote->type=='LCL' ? 'TON/M3':'CW'}}
                                                                            @endif
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air units td-a" data-type="text" data-name="units" data-value="{{$item->units}}" data-pk="{{@$item->id}}" data-title="Units"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air price_per_unit td-a" data-type="text" data-name="price_per_unit" data-value="{{$item->price_per_unit}}" data-pk="{{@$item->id}}" data-title="Price per unit"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air markup td-a" data-type="text" data-name="markup" data-value="{{$item->markup}}" data-pk="{{@$item->id}}" data-title="Markup"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <span class="td-a total-amount">{{($item->units*$item->price_per_unit)+$item->markup}}</span>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air td-a local_currency" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{@$item->id}}" data-title="Select currency"></a>
                                                                            &nbsp;
                                                                            <a class="delete-charge-lcl" style="cursor: pointer;" title="Delete">
                                                                                <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach

                                                            <!-- Hide Freight -->
                                                            <div class="add_charge">
                                                                <tr class="hide" id="freight_charges_{{$v}}">
                                                                    <input name="number" value="{{$v}}" class="form-control number" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                    <input name="type_id" value="3" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                    <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                    <td>
                                                                        {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                                    </td>
                                                                    <td>
                                                                        {{ Form::select('calculation_type_id[]',$calculation_types_lcl_air,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                                    </td>
                                                                    <td >
                                                                        <input name="units" class="units form-control" type="number" min="0" step="0.0000001" />
                                                                    </td>
                                                                    <td >
                                                                        <input name="price_per_unit" class="form-control price_per_unit" type="number" min="0" step="0.0000001" />
                                                                    </td>
                                                                    <td >
                                                                        <input name="markup" class="form-control markup" type="number" min="0" step="0.0000001" />
                                                                    </td>
                                                                    <td >
                                                                        <input name="total" class="form-control total_2" type="number" min="0" step="0.0000001" />
                                                                    </td>
                                                                    <td >
                                                                        <div class="input-group">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    {{ Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
                                                                                </div>
                                                                                <a class="btn btn-xs btn-primary-plus store_charge_lcl">
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
                                                                    <td colspan="4" class="tds"></td>
                                                                    <td class="title-quote size-12px tds" ><span class="td-a">Total</span></td>
                                                                    <td class="tds"><input type="hidden" value="{{$total_freight}}" name="sum_total" class="sum_total"><b><span class="td-a td_sum_total sub_total">{{$total_freight}}</span></b></td>
                                                                    <td class="tds"><b><span class="td-a"> {{$currency_cfg->alphacode}}</span></b></td>
                                                                </tr>
                                                                @endif
                                                            </div>
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
                                        <div class="no-mg-row">
                                            <div class="col-md-12 header-charges">
                                                <h5 class="title-quote size-12px">Origin charges</h5>
                                            </div>
                                            <div class="col-md-12 thead origin_box">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                            <tr>
                                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                                <td class="td-table">Detail</td>
                                                                <td class="td-table">Units</td>
                                                                <td class="td-table">Rate</td>
                                                                <td class="td-table">Markup</td>
                                                                <td class="td-table">Total</td>
                                                                <td class="td-table">Currency</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                            @php
                                                                $a=0;
                                                                $total_origin_units=0;
                                                                $total_origin_rates=0;
                                                                $total_origin_markups=0;
                                                            @endphp
                                                            @foreach($rate->charge_lcl_air as $item)
                                                                @if($item->type_id==1)
                                                                    <?php

                                                                        $rate_id=$item->automatic_rate_id;
                                                                        $total_origin+=$item->total_origin;
                                                                        $total_origin_units+=$item->units;
                                                                        $total_origin_rates+=$item->price_per_unit*$item->units;
                                                                        $total_origin_markups+=$item->markup;

                                                                    ?>
                                                                    <tr style="height:40px;">
                                                                        <td class="tds" style="padding-left: 30px">
                                                                            <input name="type" value="1" class="form-control type" type="hidden" />
                                                                            <input name="charge_id" value="{{@$item->id}}" class="form-control charge_id" type="hidden" style="max-width: 50px;"/>


                                                                            <a href="#" class="editable-lcl-air td-a" data-source="{{$surcharges}}" data-type="select" data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{@$item->id}}" data-title="Select surcharge"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air td-a" data-source="{{$calculation_types_lcl_air}}" data-type="select" data-name="calculation_type_id" data-value="{{$item->calculation_type_id}}" data-pk="{{@$item->id}}" data-title="Select calculation type"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air units td-a" data-type="text" data-name="units" data-value="{{$item->units}}" data-pk="{{@$item->id}}" data-title="Units"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air price_per_unit td-a" data-type="text" data-name="price_per_unit" data-value="{{$item->price_per_unit}}" data-pk="{{@$item->id}}" data-title="Price per unit"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air markup td-a" data-type="text" data-name="markup" data-value="{{$item->markup}}" data-pk="{{@$item->id}}" data-title="Markup"></a>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <span class="td-a total-amount">{{($item->units*$item->price_per_unit)+$item->markup}}</span>
                                                                        </td>
                                                                        <td class="tds">
                                                                            <a href="#" class="editable-lcl-air td-a local_currency" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{@$item->id}}" data-title="Select currency"></a>
                                                                            &nbsp;
                                                                            <a class="delete-charge-lcl" style="cursor: pointer;" title="Delete">
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
                                                                    {{ Form::select('calculation_type_id[]',$calculation_types_lcl_air,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                                </td>
                                                                <td >
                                                                    <input name="units" class="units form-control" type="number" min="0" step="0.0000001" />
                                                                </td>
                                                                <td >
                                                                    <input name="price_per_unit" class="form-control price_per_unit" type="number" min="0" step="0.0000001" />
                                                                </td>
                                                                <td >
                                                                    <input name="markup" class="form-control markup" type="number" min="0" step="0.0000001" />
                                                                </td>
                                                                <td >
                                                                    <input name="total" class="form-control total_2" type="number" min="0" step="0.0000001" />
                                                                </td>                                                
                                                                <td >
                                                                    <div class="input-group">
                                                                        <div class="input-group-btn">
                                                                            <div class="btn-group">
                                                                                {{ Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
                                                                            </div>
                                                                            <a class="btn btn-xs btn-primary-plus store_charge_lcl">
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
                                                                <td colspan="4" class="tds"></td>
                                                                <td class="title-quote size-12px tds" ><span class="td-a">Total</span></td>
                                                                <td class="tds"><input type="hidden" value="{{$total_origin}}" name="sum_total" class="sum_total"><b><span class="td-a td_sum_total sub_total">{{$total_origin}}</span></b></td>
                                                                <td class="tds"><b><span class="td-a"> {{$currency_cfg->alphacode}}</span></b></td>
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

                                        <div class="no-mg-row">
                                            <div class="col-md-12 header-charges">
                                                <h5 class="title-quote size-12px">Destination charges</h5>
                                            </div>
                                            <div class="col-md-12 thead destination_box">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                            <tr>
                                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                                <td class="td-table">Detail</td>
                                                                <td class="td-table">Units</td>
                                                                <td class="td-table">Rate</td>
                                                                <td class="td-table">Markup</td>
                                                                <td class="td-table">Total</td>
                                                                <td class="td-table">Currency</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                            @php
                                                            $a=0;
                                                            $total_destination_units=0;
                                                            $total_destination_rates=0;
                                                            $total_destination_markups=0;
                                                            @endphp

                                                            @foreach($rate->charge_lcl_air as $item)
                                                            @if($item->type_id==2)
                                                            <?php
                                                            $rate_id=$item->automatic_rate_id;
                                                            $total_destination+=$item->total_destination;
                                                            $total_destination_units+=$item->units;
                                                            $total_destination_rates+=$item->price_per_unit*$item->units;
                                                            $total_destination_markups+=$item->markup;
                                                            ?>                                                   

                                                            <tr style="height:40px;">
                                                                <td class="tds" style="padding-left: 30px">
                                                                    <input name="type" value="1" class="form-control type" type="hidden" />
                                                                    <input name="charge_id" value="{{@$item->id}}" class="form-control charge_id" type="hidden" style="max-width: 50px;"/>


                                                                    <a href="#" class="editable-lcl-air td-a" data-source="{{$surcharges}}" data-type="select" data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{@$item->id}}" data-title="Select surcharge"></a>
                                                                </td>
                                                                <td class="tds">
                                                                    <a href="#" class="editable-lcl-air td-a" data-source="{{$calculation_types_lcl_air}}" data-type="select" data-name="calculation_type_id" data-value="{{$item->calculation_type_id}}" data-pk="{{@$item->id}}" data-title="Select calculation type"></a>
                                                                </td>
                                                                <td class="tds">
                                                                    <a href="#" class="editable-lcl-air units td-a" data-type="text" data-name="units" data-value="{{$item->units}}" data-pk="{{@$item->id}}" data-title="Units"></a>
                                                                </td>
                                                                <td class="tds">
                                                                    <a href="#" class="editable-lcl-air price_per_unit td-a" data-type="text" data-name="price_per_unit" data-value="{{$item->price_per_unit}}" data-pk="{{@$item->id}}" data-title="Price per unit"></a>
                                                                </td>
                                                                <td class="tds">
                                                                    <a href="#" class="editable-lcl-air markup td-a" data-type="text" data-name="markup" data-value="{{$item->markup}}" data-pk="{{@$item->id}}" data-title="Markup"></a>
                                                                </td>
                                                                <td class="tds">
                                                                    <span class="td-a total-amount">{{($item->units*$item->price_per_unit)+$item->markup}}</span>
                                                                </td>
                                                                <td class="tds">
                                                                    <a href="#" class="editable-lcl-air td-a local_currency" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{@$item->id}}" data-title="Select currency"></a>
                                                                    &nbsp;
                                                                    <a class="delete-charge-lcl" style="cursor: pointer;" title="Delete">
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
                                                            <div class="add_charge">
                                                                <tr class="hide" id="destination_charges_{{$v}}">
                                                                    <input name="number" value="{{$v}}" class="form-control number" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                    <input name="type_id" value="2" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                    <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                                    <td>
                                                                        {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                                    </td>
                                                                    <td>
                                                                        {{ Form::select('calculation_type_id[]',$calculation_types_lcl_air,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                                    </td>
                                                                    <td >
                                                                        <input name="units" class="units form-control" type="number" min="0" step="0.0000001" />
                                                                    </td>
                                                                    <td >
                                                                        <input name="price_per_unit" class="form-control price_per_unit" type="number" min="0" step="0.0000001" />
                                                                    </td>
                                                                    <td >
                                                                        <input name="markup" class="form-control markup" type="number" min="0" step="0.0000001" />
                                                                    </td>
                                                                    <td >
                                                                        <input name="total" class="form-control total_2" type="number" min="0" step="0.0000001"  />
                                                                    </td>                                                
                                                                    <td >
                                                                        <div class="input-group">
                                                                            <div class="input-group-btn">
                                                                                <div class="btn-group">
                                                                                    {{ Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
                                                                                </div>
                                                                                <a class="btn btn-xs btn-primary-plus store_charge_lcl">
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
                                                                    <td colspan="4" class="tds"></td>
                                                                    <td class="title-quote size-12px tds" ><span class="td-a">Total</span></td>
                                                                    <td class="tds"><input type="hidden" value="{{$total_destination}}" name="sum_total" class="sum_total"><b><span class="td-a td_sum_total sub_total">{{$total_destination}}</span></b></td>
                                                                    <td class="tds"><b><span class="td-a"> {{$currency_cfg->alphacode}}</span></b></td>
                                                                </tr>
                                                                @endif
                                                            </div>
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
                                        <!-- Total -->
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
                                                                <td class="tds">Units * Rate</td>
                                                                <td class="tds">Markups</td>
                                                                <td class="tds">Total</td>
                                                                <td class="tds">Currency</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="background-color: white;">
                                                            <?php
                                                            $total_units=number_format($total_freight_units+$total_origin_units+$total_destination_units, 2, '.', '');
                                                            $total_rates=number_format($total_freight_rates+$total_origin_rates+$total_destination_rates, 2, '.', '');
                                                            $total_markups=number_format($total_freight_markups+$total_origin_markups+$total_destination_markups, 2, '.', '');
                                                            ?>
                                                            <tr style="height:40px;">
                                                                <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>

                                                                <td class="tds"><span class="bg-rates td-a sum_total_rates">{{$total_rates}}</td>
                                                                <td class="tds"><span class="bg-rates td-a sum_total_markup">{{$total_markups}}</td>
                                                                <td class="tds"><span class="bg-rates td-a sum_total_amount">{{$total_freight+$total_origin+$total_destination}}</td>
                                                                <td class="tds"><span class="td-a">{{$currency_cfg->alphacode}}</span></td>
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
                                        @if($quote->type!='AIR')
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
                                                        @if(!$rate->automaticInlandLclAir->isEmpty())
                                                        @foreach($rate->automaticInlandLclAir as $inland)
                                                        <?php
                                                        $inland_rates = json_decode($inland->rate,true);
                                                        $inland_markups = json_decode($inland->markup,true);
                                                        ?>
                                                        <div class="tab-content">
                                                            <div class="flex-list">
                                                                <ul >
                                                                    <li ><i class="fa fa-truck" style="font-size: 2rem"></i></li>
                                                                    <li class="size-12px">{{$inland->port->name}}, {{$inland->port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($inland->port->code, 0, 2))}}.svg"></li>
                                                                    <li class="size-12px">Type: {{$inland->type}}</li>
                                                                    <li class="size-12px">Contract: {{$inland->contract}}</li>
                                                                    <li class="size-12px no-border-left d-flex justify-content-end">
                                                                        <div onclick="show_hide_element('details_inland_{{$x}}')"><i class="fa fa-angle-down"></i></div>
                                                                    </li>
                                                                    <li>
                                                                        <button onclick="AbrirModal('editInlandLcl',{{$inland->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit">
                                                                            <i class="la la-edit"></i>
                                                                        </button>

                                                                        <button class="delete-inland m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" data-rate-id="{{$rate->id}}">
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
                                                                            <td class="td-table" >Units</td>
                                                                            <td class="td-table" >Rate</td>
                                                                            <td class="td-table" >Markup</td>
                                                                            <td class="td-table" >Total</td>
                                                                            <td class="td-table" >Currency</td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody style="background-color: white;">
                                                                        <tr style="height:40px;">
                                                                            <td class="tds" style="padding-left: 30px">
                                                                                <a href="#" class="editable-lcl-air-inland provider td-a" data-type="text" data-value="{{$inland->provider}}" data-name="provider" data-pk="{{@$inland->id}}" data-title="Provider"></a>
                                                                            </td>
                                                                            <td class="tds">
                                                                                <a href="#" class="editable-lcl-air-inland distance td-a" data-type="text" data-name="distance" data-value="{{@$inland->distance}}" data-pk="{{@$inland->id}}" data-title="Distance"></a> &nbsp;km
                                                                            </td>
                                                                            <td  class="tds">
                                                                                <a href="#" class="editable-lcl-air-inland td-a" data-type="text" data-name="units" data-value="{{@$inland->units}}" data-pk="{{@$inland->id}}" data-title="Amount"></a>
                                                                            </td>
                                                                            <td class="tds">
                                                                                <a href="#" class="editable-lcl-air-inland td-a" data-type="text" data-name="price_per_unit" data-value="{{@$inland->price_per_unit}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td class="tds">
                                                                                <a href="#" class="editable-lcl-air-inland td-a" data-type="text" data-name="markup" data-value="{{@$inland->markup}}" data-pk="{{@$inland->id}}" data-title="Total"></a>
                                                                            </td>
                                                                            <td class="tds">
                                                                                <span class="td-a">{{($inland->units*$inland->price_per_unit)+$inland->markup}}</span>
                                                                            </td>
                                                                            <td class="tds">
                                                                                <a href="#" class="editable-lcl-air-inland td-a" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$inland->currency_id}}" data-pk="{{@$inland->id}}" data-title="Select currency"></a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
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
                                                                    <button class="btn btn-primary-v2 btn-edit open-inland-modal" data-toggle="modal" data-rate-id="{{$rate->id}}" data-target="#createInlandModal">
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
                                        @endif
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
            </div>
        </div>
    </div>
</div>
