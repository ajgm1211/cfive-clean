        @php
            $v=0;
        @endphp
        @foreach($rates as $rate)
                <div class="row">
                    <div class="col-md-12">
                        <div class="m-portlet custom-portlet">
                            <div class="m-portlet__body">
                                <div class="tab-content">
                                    <div class="flex-list" style=" margin-bottom:-30px; margin-top: 0;">
                                        <ul >
                                            <li style="max-height: 20px;">                                            
                                                @if(isset($rate->carrier->image) && $rate->carrier->image!='')
                                                    <img src="{{ url('imgcarrier/'.$rate->carrier->image) }}"  class="img img-responsive" width="80" height="auto" style="margin-top: -15px;" />
                                                @endif
                                            </li>
                                            <li class="size-12px">POL: {{$rate->origin_address != '' ? $rate->origin_address:$rate->origin_port->name.', '.$rate->origin_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->origin_country_code}}.svg"/></li>
                                            <li class="size-12px">POD: {{$rate->destination_address != '' ? $rate->destination_address:$rate->destination_port->name.', '.$rate->destination_port->code}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{$rate->destination_country_code}}.svg"/></li>
                                            <li class="size-12px">Contract: {{$rate->contract}}</li>
                                            <li class="size-12px">
                                                <div onclick="show_hide_element('details_{{$v}}')"><i class="down"></i></div>
                                            </li>
                                            <li class="size-12px">
                                                <div class="delete-rate" data-rate-id="{{$rate->id}}" style="cursor:pointer;"><i class="fa fa-trash fa-4x"></i></div>
                                            </li>
                                        </ul>
                                    </div>
                                    <br>
                                    <div class="details_{{$v}} hide" style="background-color: white; padding: 20px; border-radius: 5px; margin-top: 20px;">
                                        <!-- Freight charges -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h5 class="title-quote size-12px">Freight charges</h5>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover table color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                        <tr>
                                                            <td >Charge</td>
                                                            <td >Detail</td>
                                                            <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                            <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                            <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                            <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                            <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                            <td >Currency</td>
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
                                                                    
                                                                    $sum20+=$item->total_20;
                                                                    $sum40+=@$item->total_40;
                                                                    $sum40hc+=@$item->total_40hc;
                                                                    $sum40nor+=@$item->total_40nor;
                                                                    $sum45+=@$item->total_45;

                                                                    $sum_m20+=$item->total_markup20;
                                                                    $sum_m40+=@$item->total_markup40;
                                                                    $sum_m40hc+=@$item->total_markup40hc;
                                                                    $sum_m40nor+=@$item->total_markup40nor;
                                                                    $sum_m45+=@$item->total_markup45;                                                                    
                                                                ?>
                                                                <tr >
                                                                    <td>
                                                                        <a href="#" class="editable" data-source="{{$surcharges}}" data-type="select" data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" class="editable" data-source="{{$calculation_types}}" data-type="select" data-name="calculation_type_id" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <a href="#" class="editable-amount-20 amount_20"data-type="text" data-name="amount->c20" data-value="{{@$freight_amounts['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <a href="#" class="editable-markup-20 markup_20"data-type="text" data-name="markups->c20" data-value="{{@$freight_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <span class="total_20">{{@$freight_amounts['c20']+@$freight_markups['c20']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <a href="#" class="editable-amount-40 amount_40" data-type="text" data-name="amount->c40" data-value="{{@$freight_amounts['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->c40" data-value="{{@$freight_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <span class="total_40">{{@$freight_amounts['c40']+@$freight_markups['c40']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$freight_amounts['c40hc']}}" data-pk="{{$item->id}}" data-emptytext="0" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <a href="#" class="editable-markup-40hc markup_40hc"data-type="text" data-name="markups->c40hc" data-value="{{@$freight_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <span class="total_40hc">{{@$freight_amounts['c40hc']+@$freight_markups['c40hc']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <a href="#" class="editable-amount-40nor amount_40nor "data-type="text" data-name="amount->c40nor" data-value="{{@$freight_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->c40nor" data-value="{{@$freight_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <span class="total_40nor">{{@$freight_amount['c40nor']+@$freight_markups['c40nor']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->c45" data-value="{{@$freight_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->c45" data-value="{{@$freight_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <span class="total_45">{{@$freight_amount['45']+@$freight_markups['45']}}</span>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                    $i++;
                                                                @endphp
                                                            @endif
                                                        @endforeach

                                                        <!-- Hide Freight -->

                                                        <tr class="hide" id="freight_charges_{{$v}}">
                                                            <input name="type_id" value="3" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <td>
                                                                {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                            </td>
                                                            <td>
                                                                {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="amount_c20" class="amount_c20 form-control" id="amount_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="markup_c20" class="form-control markup_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="total_c20" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="markup_c40" value="" class="form-control markup_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="total_c40" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="amount_c40hc" value="" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="markup_c40hc" value="" class="form-control markup_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />

                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="amount_c40nor" value="" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="markup_c40nor" value="" class="form-control markup_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="amount_c45" value="" class="form-control amount_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="markup_c45" value="" class="form-control markup_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="freight_total[]" value="" class="form-control freight_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled/>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <div class="input-group-btn">
                                                                        <div class="btn-group">
                                                                            {{ Form::select('currency_id',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
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
                                                            <tr>
                                                                <td class="title-quote size-12px" colspan="2">Total</td>
                                                                <td {{ $equipmentHides['20'] }} colspan="3">{{number_format(@$sum20, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40'] }} colspan="3">{{number_format(@$sum40, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40hc'] }} colspan="3">{{number_format(@$sum40hc, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40nor'] }} colspan="3">{{number_format(@$sum40nor, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['45'] }} colspan="3">{{number_format(@$sum45, 2, '.', '')}}</td>
                                                                <td >{{$currency_cfg->alphacode}}</td>
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

                                        <div class="row">
                                            <div class="col-md-3">
                                                <h5 class="title-quote size-12px">Origin charges</h5>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                        <tr>
                                                            <td >Charge</td>
                                                            <td >Detail</td>
                                                            <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                            <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                            <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                            <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                            <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                            <td >Currency</td>
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
                                                            @if($item->type_id==1)
                                                                <?php
                                                                    $rate_id=$item->automatic_rate_id;
                                                                    $origin_amounts = json_decode($item->amount,true);
                                                                    $origin_markups = json_decode($item->markups,true);
                                                            
                                                                    $sum_origin_20+=@$item->total_20;
                                                                    $sum_origin_40+=@$item->total_40;
                                                                    $sum_origin_40hc+=@$item->total_40hc;
                                                                    $sum_origin_40nor+=@$item->total_40nor;
                                                                    $sum_origin_45+=@$item->total_45;

                                                                    $sum_origin_m20+=@$item->total_markup20;
                                                                    $sum_origin_m40+=@$item->total_markup40;
                                                                    $sum_origin_m40hc+=@$item->total_markup40hc;
                                                                    $sum_origin_m40nor+=@$item->total_markup40nor;
                                                                    $sum_origin_m45+=@$item->total_markup45;                                                                    
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <a href="#" class="editable surcharge_id" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" class="editable calculation_type_id" data-source="{{$calculation_types}}" data-name="calculation_type_id" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <a href="#" class="editable-amount-20 amount_20"data-type="text" data-name="amount->c20" data-value="{{@$origin_amounts['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <a href="#" class="editable-markup-20 markup_20"data-type="text" data-name="markups->c20" data-value="{{@$origin_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <span class="total_20">{{@$origin_amounts['c20']+@$origin_markups['c20']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <a href="#" class="editable-amount-40 amount_40"data-type="text" data-name="amount->c40" data-value="{{@$origin_amounts['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->c40" data-value="{{@$origin_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <span class="total_40">{{@$origin_amounts['c40']+@$origin_markups['c40']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$origin_amounts['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <a href="#" class="editable-amount-40hc markup_40hc"data-type="text" data-name="markups->c40hc" data-value="{{@$origin_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <span class="total_40hc">{{@$origin_amounts['c40hc']+@$origin_markups['c40hc']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <a href="#" class="editable-amount-40nor amount_40nor"data-type="text" data-name="amount->c40nor" data-value="{{@$origin_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->40nor" data-value="{{@$origin_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <span class="total_40nor">{{@$origin_amounts['c40nor']+@$origin_markups['c40nor']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->c45" data-value="{{@$origin_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->c45" data-value="{{@$origin_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <span class="total_45">{{@$origin_amounts['c45']+@$origin_markups['c45']}}</span>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                    $a++;
                                                                @endphp
                                                            @endif
                                                        @endforeach

                                                        <!-- Hide origin charges-->

                                                        <tr class="hide" id="origin_charges_{{$v}}">
                                                            <input name="type_id" value="1" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <td>
                                                                {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                            </td>
                                                            <td>
                                                                {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="amount_c20" class="amount_c20 form-control" id="amount_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="markup_c20" class="form-control markup_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="total_c20" class="form-control origin_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="markup_c40" value="" class="form-control markup_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="total_c40" value="" class="form-control origin_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="amount_c40hc" value="" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="markup_c40hc" value="" class="form-control markup_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="origin_total[]" value="" class="form-control origin_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />

                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="amount_c40nor" value="" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="markup_c40nor" value="" class="form-control markup_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="origin_total[]" value="" class="form-control origin_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="amount_c45" value="" class="form-control amount_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="markup_c45" value="" class="form-control markup_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="origin_total[]" value="" class="form-control origin_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled/>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <div class="input-group-btn">
                                                                        <div class="btn-group">
                                                                            {{ Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
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
                                                            <tr>
                                                                <td class="title-quote size-12px" colspan="2">Total</td>
                                                                <td {{ $equipmentHides['20'] }} colspan="3">{{number_format(@$sum_origin_20, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40'] }} colspan="3">{{number_format(@$sum_origin_40, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40hc'] }} colspan="3">{{number_format(@$sum_origin_40hc, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40nor'] }} colspan="3">{{number_format(@$sum_origin_40nor, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['45'] }} colspan="3">{{number_format(@$sum_origin_45, 2, '.', '')}}</td>
                                                                <td >{{$currency_cfg->alphacode}}</td>
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

                                        <div class="row">
                                            <div class="col-md-3">
                                                <h5 class="title-quote size-12px">Destination charges</h5>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered color-blue text-center">
                                                        <thead class="title-quote text-center header-table">
                                                        <tr>
                                                            <td >Charge</td>
                                                            <td >Detail</td>
                                                            <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                            <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                            <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                            <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                            <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                            <td >Currency</td>
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
                                                            @if($item->type_id==2)
                                                                <?php
                                                                    $rate_id=$item->automatic_rate_id;
                                                                    $destination_amounts = json_decode($item->amount,true);
                                                                    $destination_markups = json_decode($item->markups,true);

                                                                    $sum_destination_20+=@$item->total_20;
                                                                    $sum_destination_40+=@$item->total_40;
                                                                    $sum_destination_40hc+=@$item->total_40hc;
                                                                    $sum_destination_40nor+=@$item->total_40nor;
                                                                    $sum_destination_45+=@$item->total_45;

                                                                    $sum_destination_m20+=@$item->total_markup20;
                                                                    $sum_destination_m40+=@$item->total_markup40;
                                                                    $sum_destination_m40hc+=@$item->total_markup40hc;
                                                                    $sum_destination_m40nor+=@$item->total_markup40nor;
                                                                    $sum_destination_m45+=@$item->total_markup45;
                                                                ?>                                                     

                                                                <tr>
                                                                    <td>
                                                                        <a href="#" class="editable surcharge_id" data-source="{{$surcharges}}" data-type="select" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" class="editable calculation_type_id" data-source="{{$calculation_types}}" data-name="calculation_type_id" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <a href="#" class="editable destination_amount_20"data-type="text" data-name="amount->c20" data-value="{{@$destination_amounts['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <a href="#" class="editable destination_markup_20"data-type="text" data-name="markups->c20" data-value="{{@$destination_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['20'] }}>
                                                                        <span class="total_20">{{@$destination_amounts['c20']+@$destination_markups['c20']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <a href="#" class="editable destination_amount_40"data-type="text" data-name="amount->c40" data-value="{{@$destination_amounts['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <a href="#" class="editable destination_markup_40"data-type="text" data-name="markups->c40" data-value="{{@$destination_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40'] }}>
                                                                        <span class="total_40">{{@$destination_amounts['c40']+@$destination_markups['c40']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <a href="#" class="editable destination_amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$destination_amounts['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <a href="#" class="editable destination_markup_40hc"data-type="text" data-name="markups->c40hc" data-value="{{@$destination_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40hc'] }}>
                                                                        <span class="total_40hc">{{@$destination_amounts['c40hc']+@$destination_markups['c40hc']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <a href="#" class="editable destination_amount_40nor"data-type="text" data-name="amount->c40nor" data-value="{{@$destination_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <a href="#" class="editable destination_markup_40nor"data-type="text" data-name="markups->c40hc" data-value="{{@$destination_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['40nor'] }}>
                                                                        <span class="total_40nor">{{@$destination_amounts['c40nor']+@$destination_markups['c40nor']}}</span>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <a href="#" class="editable destination_amount_45" data-type="text" data-name="amount->c45" data-value="{{@$destination_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <a href="#" class="editable destination_markup_45" data-type="text" data-name="markups->c45" data-value="{{@$destination_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                    </td>
                                                                    <td {{ $equipmentHides['45'] }}>
                                                                        <span class="total_45">{{@$destination_amounts['c45']+@$destination_markups['c45']}}</span>
                                                                    </td>
                                                                    <td>
                                                                        <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$item->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                    </td>
                                                                </tr>
                                                                @php
                                                                    $a++;
                                                                @endphp
                                                            @endif
                                                        @endforeach

                                                        <!-- Hide destination charges -->

                                                        <tr class="hide" id="destination_charges_{{$v}}">
                                                            <input name="type_id" value="2" class="form-control type_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <input name="automatic_rate_id" value="{{$rate->id}}" class="form-control automatic_rate_id" type="hidden" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            <td>
                                                                {{ Form::select('surcharge_id[]',$surcharges,null,['class'=>'form-control surcharge_id','required'=>true]) }}
                                                            </td>
                                                            <td>
                                                                {{ Form::select('calculation_type_id[]',$calculation_types,null,['class'=>'form-control calculation_type_id','required'=>true]) }}
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="amount_c20" class="amount_c20 form-control" id="amount_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="markup_c20" class="form-control markup_c20" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['20'] }}>
                                                                <input name="total_c20" class="form-control destination_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="amount_c40" class="form-control amount_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="markup_c40" value="" class="form-control markup_c40" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40'] }}>
                                                                <input name="total_c40" value="" class="form-control destination_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="amount_c40hc" value="" class="form-control amount_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="markup_c40hc" value="" class="form-control markup_c40hc" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40hc'] }}>
                                                                <input name="destination_total[]" value="" class="form-control destination_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />

                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="amount_c40nor" value="" class="form-control amount_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="markup_c40nor" value="" class="form-control markup_c40nor" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['40nor'] }}>
                                                                <input name="origin_total[]" value="" class="form-control destination_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled />
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="amount_c45" value="" class="form-control amount_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="markup_c45" value="" class="form-control markup_c45" type="number" min="0" step="0.0000001" style="max-width: 50px;"/>
                                                            </td>
                                                            <td {{ $equipmentHides['45'] }}>
                                                                <input name="destination_total[]" value="" class="form-control destination_total" type="number" min="0" step="0.0000001" style="max-width: 50px;" disabled/>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <div class="input-group-btn">
                                                                        <div class="btn-group">
                                                                            {{ Form::select('destination_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control currency_id select-2-width']) }}
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
                                                            <tr>
                                                                <td class="title-quote size-12px" colspan="2">Total</td>
                                                                <td {{ $equipmentHides['20'] }} colspan="3">{{number_format(@$sum_destination_20, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40'] }} colspan="3">{{number_format(@$sum_destination_40, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40hc'] }} colspan="3">{{number_format(@$sum_destination_40hc, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['40nor'] }} colspan="3">{{number_format(@$sum_destination_40nor, 2, '.', '')}}</td>
                                                                <td {{ $equipmentHides['45'] }} colspan="3">{{number_format(@$sum_destination_45, 2, '.', '')}}</td>
                                                                <td >{{$currency_cfg->alphacode}}</td>
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
                                        @if($rate->id == @$rate_id )
                                        <br>
                                         <table class="table table-sm table-bordered table-hover table color-blue text-center">
                                            <thead class="title-quote text-center header-table">
                                                <tr>
                                                    <td >Total</td>
                                                    <td {{ $equipmentHides['20'] }} >20'</td>
                                                    <td {{ $equipmentHides['20'] }} >Markup</td>
                                                    <td {{ $equipmentHides['40'] }} >40'</td>
                                                    <td {{ $equipmentHides['40'] }} >Markup</td>
                                                    <td {{ $equipmentHides['40hc'] }} >40HC'</td>
                                                    <td {{ $equipmentHides['40hc'] }} >Markup</td>
                                                    <td {{ $equipmentHides['40nor'] }} >40NOR'</td>
                                                    <td {{ $equipmentHides['40nor'] }} >Markup</td>
                                                    <td {{ $equipmentHides['45'] }} >45'</td>
                                                    <td {{ $equipmentHides['45'] }} >Markup</td>                 
                                                    <td >Currency</td>
                                                </tr>
                                            </thead>
                                            <tbody style="background-color: white;">
                                                <td class="title-quote size-12px"></td>
                                                <td {{ $equipmentHides['20'] }} >{{number_format(@$sum20+@$sum_origin_20+@$sum_destination_20, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['20'] }} >{{number_format(@$sum_m20+@$sum_origin_m20+@$sum_destination_m20, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['40'] }} >{{number_format(@$sum40+@$sum_origin_40+@$sum_destination_40, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['40'] }} >{{number_format(@$sum_m40+@$sum_origin_m40+@$sum_destination_m40, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['40hc'] }} >{{number_format(@$sum40hc+@$sum_origin_40hc+@$sum_destination_40hc, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['40hc'] }} >{{number_format(@$sum_m40hc+@$sum_origin_m40hc+@$sum_destination_m40hc, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['40nor'] }} >{{number_format(@$sum40nor+@$sum_origin_40nor+@$sum_destination_40nor, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['40nor'] }} >{{number_format(@$sum_m40nor+@$sum_origin_m40nor+@$sum_destination_m40nor, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['45'] }} >{{number_format(@$sum45+@$sum_origin_45+@$sum_destination_45, 2, '.', '')}}</td>
                                                <td {{ $equipmentHides['45'] }} >{{number_format(@$sum_m45+@$sum_origin_m45+@$sum_destination_m45, 2, '.', '')}}</td>
                                                <td >{{$currency_cfg->alphacode}}</td>
                                            </tbody>
                                        </table>
                                        @endif
                                        <!-- Inlands -->
                                        @if(!$rate->inland->isEmpty())
                                            <div class="row" >
                                                <div class="col-md-12">
                                                    <br>
                                                    <h5 class="title-quote size-12px">Inland charges</h5>
                                                    <hr>
                                                    <div class="">
                                                        <div class="">
                                                            @php
                                                                $x=0;
                                                            @endphp
                                                            @foreach($rate->inland as $inland)
                                                                <?php 
                                                                    $inland_rates = json_decode($inland->rate,true);
                                                                    $inland_markups = json_decode($inland->markup,true);
                                                                ?>
                                                                <div class="tab-content">
                                                                    <div class="flex-list">
                                                                        <ul >
                                                                            <li ><i class="fa fa-truck" style="font-size: 2rem"></i></li>
                                                                            <li class="size-12px">From: {{$rate->origin_address != '' ? $rate->origin_address:$rate->origin_port->name}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($rate->origin_port->code, 0, 2))}}.svg"></li>
                                                                            <li class="size-12px">To: {{$rate->destination_address != '' ? $rate->destination_address:$rate->destination_port->name}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{strtolower(substr($rate->destination_port->code, 0, 2))}}.svg"></li>
                                                                            <li class="size-12px">Contract: {{$inland->contract}}</li>
                                                                            <li class="size-12px">
                                                                                <div onclick="show_hide_element('details_inland_{{$x}}')"><i class="down"></i></div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="details_inland_{{$x}} hide">
                                                                        <table class="table table-sm table-bordered color-blue text-center">
                                                                            <thead class="title-quote text-center header-table">
                                                                            <tr>
                                                                                <td >Charge</td>
                                                                                <td >Distance</td>
                                                                                <td {{ $equipmentHides['20'] }} colspan="3">20'</td>
                                                                                <td {{ $equipmentHides['40'] }} colspan="3">40'</td>
                                                                                <td {{ $equipmentHides['40hc'] }} colspan="3">40HC'</td>
                                                                                <td {{ $equipmentHides['40nor'] }} colspan="3">40NOR'</td>
                                                                                <td {{ $equipmentHides['45'] }} colspan="3">45'</td>
                                                                                <td >Currency</td>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody style="background-color: white;">
                                                                            <tr >
                                                                                <td>
                                                                                    <a href="#" class="editable" data-source="{{$surcharges}}" data-type="text" data-value="{{$inland->provider}}" data-pk="{{$item->id}}" data-title="Charge"></a>
                                                                                </td>
                                                                                <td>
                                                                                    <a href="#" class="editable-amount-20 amount_20" data-type="text" data-name="amount->c20" data-value="{{@$inland->distance}}" data-pk="{{$item->id}}" data-title="Amount"></a> &nbsp;km
                                                                                </td>
                                                                                <td {{ $equipmentHides['20'] }}>
                                                                                    <a href="#" class="editable-amount-20 amount_20" data-type="text" data-name="amount->c20" data-value="{{@$inland_rates['c20']}}" data-pk="{{$item->id}}" data-title="Amount"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['20'] }}>
                                                                                    <a href="#" class="editable-markup-20 markup_20" data-type="text" data-name="markups->20" data-value="{{@$inland_markups['c20']}}" data-pk="{{$item->id}}" data-title="Markup"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['20'] }}>
                                                                                    <span class="total_20">{{@$inland_rates['c20']+@$inland_markups['c20']}}</span>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40'] }}>
                                                                                    <a href="#" class="editable-amount-40 amount_40"data-type="text" data-name="amount->c40" data-value="{{@$inland_rates['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40'] }}>
                                                                                    <a href="#" class="editable-markup-40 markup_40"data-type="text" data-name="markups->40" data-value="{{@$inland_markups['c40']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40'] }}>
                                                                                    <span class="total_40">{{@$inland_rates['c40']+@$inland_markups['c40']}}</span>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                                    <a href="#" class="editable-amount-40hc amount_40hc"data-type="text" data-name="amount->c40hc" data-value="{{@$inland_amounts['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                                    <a href="#" class="editable-markup-40hc markup_40hc"data-type="text" data-name="markups->40hc" data-value="{{@$inland_markups['c40hc']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40hc'] }}>
                                                                                    <span class="total_40hc">{{@$inland_amounts['c40hc']+@$inland_markups['c40hc']}}</span>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                                    <a href="#" class="editable-amount-40nor amount_40nor "data-type="text" data-name="amount->c40nor" data-value="{{@$inland_amounts['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                                    <a href="#" class="editable-markup-40nor markup_40nor"data-type="text" data-name="markups->40nor" data-value="{{@$inland_markups['c40nor']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['40nor'] }}>
                                                                                    <span class="total_40nor">{{@$inland_amounts['c40nor']+@$inland_markups['c40nor']}}</span>
                                                                                </td>
                                                                                <td {{ $equipmentHides['45'] }}>
                                                                                    <a href="#" class="editable-amount-45 amount_45" data-type="text" data-name="amount->45" data-value="{{@$inland_amounts['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['45'] }}>
                                                                                    <a href="#" class="editable-markup-45 markup_45" data-type="text" data-name="markups->45" data-value="{{@$inland_markups['c45']}}" data-pk="{{$item->id}}" data-title="Total"></a>
                                                                                </td>
                                                                                <td {{ $equipmentHides['45'] }}>
                                                                                    <span class="total_45">{{@$inland_amounts['c45']+@$inland_markups['c45']}}</span>
                                                                                </td>
                                                                                <td>
                                                                                    <a href="#" class="editable" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$inland->currency_id}}" data-pk="{{$item->id}}" data-title="Select currency"></a>
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>

                                                                        <div class='row'>
                                                                            <div class="col-md-12">
                                                                                <h5 class="title-quote pull-right">
                                                                                    <b>Add inland charge</b>
                                                                                    <a class="btn" onclick="addInlandCharge({{$i}})" style="vertical-align: middle">
                                                                                        <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                                                                    </a>
                                                                                </h5>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    @endif

                                    <!-- Remarks -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="m-portlet" style="box-shadow: none;">
                                                    <div class="m-portlet__head">
                                                        <div class="row" style="padding-top: 20px;">
                                                            <h5 class="title-quote size-12px">Remarks</h5>
                                                        </div>
                                                        <div class="m-portlet__head-tools">
                                                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                                                <li class="nav-item m-tabs__item" id="edit_li">
                                                                    <button class="btn btn-primary-v2 edit-remarks" onclick="edit_remark('remarks_span_{{$v}}','remarks_textarea_{{$v}}','update_remarks_{{$v}}')">
                                                                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="m-portlet__body">
                                                        <div class="card card-body bg-light remarks_span_{{$v}}">
                                                            <span>{!! $rate->remarks !!}</span>
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