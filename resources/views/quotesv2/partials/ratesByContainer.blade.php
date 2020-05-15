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
                                                    <!-- Seteando cabeceras -->
                                                    @foreach ($equipmentHides as $key=>$item)
                                                        @foreach ($containers as $c)
                                                            @if($c->code == $key)
                                                                <td class="td-table" {{$item}}>{{$key}}</td>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    <td class="td-table" >Currency</td>
                                                </tr>
                                            </thead>
                                            <tbody style="background-color: white;">
                                                @php
                                                    $i=0;
                                                    $pre = 'c';
                                                    $post = 'markup';
                                                    //Creando variables para sumatorias en freight
                                                    foreach ($containers as $c) {
                                                        ${'sum'.$c->code} = 0;
                                                        ${'sum_m'.$c->code} = 0;
                                                    }
                                                @endphp 
                                                @foreach($rate->charge as $item)
                                                    @if($item->type_id==3)
                                                        <?php
                                                            $rate_id=$item->automatic_rate_id;
                                                            $freight_amounts = json_decode($item->amount,true);
                                                            $freight_markups = json_decode($item->markups,true);

                                                            if(!Empty($freight_amounts)){
                                                                foreach ($freight_amounts as $k => $amount_value) {
                                                                    if ($k == 'c20') {
                                                                        $freight_amounts['c20DV'] = $amount_value;
                                                                        unset($freight_amounts['c20']);
                                                                    } elseif ($k == 'c40') {
                                                                        $freight_amounts['c40DV'] = $amount_value;
                                                                        unset($freight_amounts['c40']);
                                                                    } elseif ($k == 'c40hc') {
                                                                        $freight_amounts['c40HC'] = $amount_value;
                                                                        unset($freight_amounts['c40hc']);
                                                                    } elseif ($k == 'c40nor') {
                                                                        $freight_amounts['c40NOR'] = $amount_value;
                                                                        unset($freight_amounts['c40nor']);
                                                                    } elseif ($k == 'c45hc') {
                                                                        $freight_amounts['c45hc'] = $amount_value;
                                                                        unset($freight_amounts['c45hc']);
                                                                    }
                                                                }
                                                            }

                                                            if(!Empty($freight_markups)){
                                                                foreach ($freight_markups as $k => $markup_value) {
                                                                    if ($k == 'm20') {
                                                                        $freight_markups['m20DV'] = $markup_value;
                                                                        unset($freight_markups['m20']);
                                                                    } elseif ($k == 'm40') {
                                                                        $freight_markups['m40DV'] = $markup_value;
                                                                        unset($freight_markups['m40']);
                                                                    } elseif ($k == 'm40hc') {
                                                                        $freight_markups['m40HC'] = $markup_value;
                                                                        unset($freight_markups['m40hc']);
                                                                    } elseif ($k == 'm40nor') {
                                                                        $freight_markups['m40NOR'] = $markup_value;
                                                                        unset($freight_markups['m40nor']);
                                                                    } elseif ($k == 'm45hc') {
                                                                        $freight_markups['m45HC'] = $markup_value;
                                                                        unset($freight_markups['m45hc']);
                                                                    }
                                                                }
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
                                                            <!-- Seteando tarifas de contenedores -->                                 
                                                            @foreach ($equipmentHides as $key=>$hide)
                                                                @foreach ($containers as $c)
                                                                    @if($c->code == $key)
                                                                        @php
                                                                            ${$pre.$c->code} = 'c'.$c->code;
                                                                            ${$pre.$c->code.$post} = 'c'.$c->code.'_markup';
                                                                        @endphp
                                                                        <td {{$hide}} class="tds">
                                                                            <a href="#" class="editable-amount-rate amount_{{$key}} td-a" data-type="text" data-name="amount->c{{$key}}" data-value="{{@$freight_amounts['c'.$key]}}" data-pk="{{$item->id}}" data-container="{{$key}}" data-cargo-type="freight" data-title="Total"></a>
                                                                            +
                                                                            <a href="#" class="editable-amount-markup markup_{{$key}} td-a" data-type="text" data-name="markups->m{{$key}}" data-value="{{@$freight_markups['m'.$key]}}" data-pk="{{$item->id}}" data-container="{{$key}}" data-cargo-type="freight" data-title="Total"></a>
                                                                            <i class="la la-caret-right arrow-down"></i>
                                                                            <span class="total_{{$key}} td-a">{{@$freight_amounts['c'.$c->code]+@$freight_markups['m'.$c->code]}}</span>
                                                                        </td>
                                                                        @php
                                                                            ${'sum'.$c->code} += @$item->${$pre.$c->code};
                                                                            ${'sum_m'.$c->code} += @$item->${$pre.$c->code.$post};
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                            @endforeach
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
                                                    @foreach ($equipmentHides as $key=>$hide)
                                                        @foreach ($containers as $c)
                                                            @if($c->code == $key)
                                                                <td {{$hide}} class="tds">
                                                                    <div class="row ">
                                                                        <div class="col-12">
                                                                            <div class="input-group">
                                                                                <input name="hide_{{$key}}" value="{{ $hide }}" class="form-control hide_{{$key}}" type="hidden" min="0" step="0.0000001" />
                                                                                <input name="amount_c{{$key}}" class="amount_c{{$key}} form-control" type="number" min="0" step="0.0000001" placeholder="Rate"/>

                                                                                <input name="markup_m{{$key}}" class="form-control markup_m{{$key}}" type="number" min="0" step="0.0000001" placeholder="Markup"/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('currency_id',$currencies,$company_user->currency->id,['class'=>'form-control currency_id local_currency select-2-width']) }}
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
                                                        @foreach ($equipmentHides as $key=>$hide)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td {{$hide}} class="tds">
                                                                        <span class="td-a total_freight_{{$c->code}}">{{number_format( ${'sum'.$c->code} + ${'sum_m'.$c->code} , 2, '.', '')}}</span>
                                                                    </td>
                                                                    <input type="hidden" name="subtotal_c{{$c->code}}_freight" value="{{ ${'sum'.$c->code} }}" class="subtotal_c{{$c->code}}_freight"/>
                                                                    <input type="hidden" name="subtotal_m{{$c->code}}_freight" value="{{ ${'sum_m'.$c->code} }}" class="subtotal_m{{$c->code}}_freight"/>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td class="tds"><span class="td-a">{{$company_user->currency->alphacode}}</span></td>
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
                                                    <!-- Seteando cabeceras -->
                                                    @foreach ($equipmentHides as $key=>$item)
                                                        @foreach ($containers as $c)
                                                            @if($c->code == $key)
                                                                <td class="td-table" {{$item}}>{{$key}}</td>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    <td class="td-table" >Currency</td>
                                                </tr>
                                            </thead>
                                            <tbody style="background-color: white;">
                                                @php
                                                    $a=0;
                                                    //Creando variables para sumatorias en origin
                                                    foreach ($containers as $c) {
                                                        ${'sum_origin'.$c->code} = 0;
                                                        ${'sum_origin_m'.$c->code} = 0;
                                                    }
                                                @endphp
                                                @foreach($rate->charge as $item)
                                                    @if($item->type_id==1  && $item->saleterm==0)
                                                        <?php
                                                            $rate_id=$item->automatic_rate_id;
                                                            $origin_amounts = json_decode($item->amount,true);
                                                            $origin_markups = json_decode($item->markups,true);

                                                            if(!Empty($origin_amounts)){
                                                                foreach ($origin_amounts as $k => $amount_value) {
                                                                    if ($k == 'c20') {
                                                                        $origin_amounts['c20DV'] = $amount_value;
                                                                        unset($origin_amounts['c20']);
                                                                    } elseif ($k == 'c40') {
                                                                        $origin_amounts['c40DV'] = $amount_value;
                                                                        unset($origin_amounts['c40']);
                                                                    } elseif ($k == 'c40hc') {
                                                                        $origin_amounts['c40HC'] = $amount_value;
                                                                        unset($origin_amounts['c40hc']);
                                                                    } elseif ($k == 'c40nor') {
                                                                        $origin_amounts['c40NOR'] = $amount_value;
                                                                        unset($origin_amounts['c40nor']);
                                                                    } elseif ($k == 'c45hc') {
                                                                        $origin_amounts['c45HC'] = $amount_value;
                                                                        unset($origin_amounts['c45hc']);
                                                                    }
                                                                }
                                                            }

                                                            if(!Empty($origin_markups)){
                                                                foreach ($origin_markups as $k => $markup_value) {
                                                                    if ($k == 'm20') {
                                                                        $origin_markups['m20DV'] = $markup_value;
                                                                        unset($origin_markups['m20']);
                                                                    } elseif ($k == 'm40') {
                                                                        $origin_markups['m40DV'] = $markup_value;
                                                                        unset($origin_markups['m40']);
                                                                    } elseif ($k == 'm40hc') {
                                                                        $origin_markups['m40HC'] = $markup_value;
                                                                        unset($origin_markups['m40hc']);
                                                                    } elseif ($k == 'm40nor') {
                                                                        $origin_markups['m40NOR'] = $markup_value;
                                                                        unset($origin_markups['m40nor']);
                                                                    } elseif ($k == 'm45hc') {
                                                                        $origin_markups['m45HC'] = $markup_value;
                                                                        unset($origin_markups['m45hc']);
                                                                    }
                                                                }
                                                            }
                                                        ?>
                                                        <tr style="height:40px;">
                                                            <td class="tds" style="padding-left: 30px">
                                                                <input name="type" value="1" class="form-control type" type="hidden" />
                                                                <input name="charge_id td-a" value="{{$item->id}}" class="form-control charge_id" type="hidden" />

                                                                <a href="#" class="editable surcharge_id td-a" data-source="{{$surcharges}}" data-type="select"  data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                            </td>
                                                            <td class="tds">
                                                                <a href="#" class="editable calculation_type_id td-a" data-source="{{$calculation_types}}" data-name="calculation_type_id" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                            </td>
                                                            <!-- Seteando tarifas de contenedores -->                                 
                                                            @foreach ($equipmentHides as $key=>$hide)
                                                                @foreach ($containers as $c)
                                                                    @if($c->code == $key)
                                                                        @php
                                                                            ${$pre.$c->code} = 'c'.$c->code;
                                                                            ${$pre.$c->code.$post} = 'c'.$c->code.'_markup';
                                                                        @endphp
                                                                        <td {{$hide}} class="tds">
                                                                            <a href="#" class="editable-amount-rate amount_{{$key}} td-a" data-type="text" data-name="amount->c{{$key}}" data-value="{{@$origin_amounts['c'.$key]}}" data-pk="{{$item->id}}" data-container="{{$key}}" data-cargo-type="origin" data-title="Total"></a>
                                                                            +
                                                                            <a href="#" class="editable-amount-markup markup_{{$key}} td-a" data-type="text" data-name="markups->m{{$key}}" data-value="{{@$origin_markups['m'.$key]}}" data-pk="{{$item->id}}" data-container="{{$key}}" data-cargo-type="origin" data-title="Total"></a>
                                                                            <i class="la la-caret-right arrow-down"></i>
                                                                            <span class="total_{{$key}} td-a">{{@$origin_amounts['c'.$c->code]+@$origin_markups['m'.$c->code]}}</span>
                                                                        </td>
                                                                        @php
                                                                            ${'sum_origin'.$c->code} += @$item->${$pre.$c->code};
                                                                            ${'sum_origin_m'.$c->code} += @$item->${$pre.$c->code.$post};
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                            @endforeach
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
                                                    @foreach ($equipmentHides as $key=>$hide)
                                                        @foreach ($containers as $c)
                                                            @if($c->code == $key)
                                                                <td {{$hide}} class="tds">
                                                                    <div class="row ">
                                                                        <div class="col-12">
                                                                            <div class="input-group">
                                                                                <input name="hide_{{$key}}" value="{{ $hide }}" class="form-control hide_{{$key}}" type="hidden" min="0" step="0.0000001" />
                                                                                <input name="amount_c{{$key}}" class="amount_c{{$key}} form-control" type="number" min="0" step="0.0000001" placeholder="Rate"/>

                                                                                <input name="markup_m{{$key}}" class="form-control markup_m{{$key}}" type="number" min="0" step="0.0000001" placeholder="Markup"/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('origin_ammount_currency[]',$currencies,$company_user->currency->id,['class'=>'form-control currency_id local_currency select-2-width']) }}
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
                                                        @foreach ($equipmentHides as $key=>$hide)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td {{$hide}} class="tds">
                                                                        <span class="td-a total_origin_{{$c->code}}">{{number_format( ${'sum_origin'.$c->code} + ${'sum_origin_m'.$c->code} , 2, '.', '')}}</span>
                                                                    </td>
                                                                    <input type="hidden" name="subtotal_c{{$c->code}}_origin" value="{{ ${'sum_origin'.$c->code} }}" class="subtotal_c{{$c->code}}_origin"/>
                                                                    <input type="hidden" name="subtotal_m{{$c->code}}_origin" value="{{ ${'sum_origin_m'.$c->code} }}" class="subtotal_m{{$c->code}}_origin"/>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td class="tds"><span class="td-a">{{$company_user->currency->alphacode}}</span></td>
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
                                                    <!-- Seteando cabeceras -->
                                                    @foreach ($equipmentHides as $key=>$item)
                                                        @foreach ($containers as $c)
                                                            @if($c->code == $key)
                                                                <td class="td-table" {{$item}}>{{$key}}</td>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    <td class="td-table" >Currency</td>
                                                </tr>
                                            </thead>
                                            <tbody style="background-color: white;">
                                                @php
                                                    $a=0;
                                                    //Creando variables para sumatorias en destination
                                                    foreach ($containers as $c) {
                                                        ${'sum_destination'.$c->code} = 0;
                                                        ${'sum_destination_m'.$c->code} = 0;
                                                    }
                                                @endphp

                                                @foreach($rate->charge as $item)
                                                    @if($item->type_id==2 && $item->saleterm==0)
                                                    
                                                        @php
                                                            $rate_id=$item->automatic_rate_id;
                                                            $destination_amounts = json_decode($item->amount,true);
                                                            $destination_markups = json_decode($item->markups,true);

                                                            if(!Empty($destination_amounts)){
                                                                foreach ($destination_amounts as $k => $amount_value) {
                                                                    if ($k == 'c20') {
                                                                        $destination_amounts['c20DV'] = $amount_value;
                                                                        unset($destination_amounts['c20']);
                                                                    } elseif ($k == 'c40') {
                                                                        $destination_amounts['c40DV'] = $amount_value;
                                                                        unset($destination_amounts['c40']);
                                                                    } elseif ($k == 'c40hc') {
                                                                        $destination_amounts['c40HC'] = $amount_value;
                                                                        unset($destination_amounts['c40hc']);
                                                                    } elseif ($k == 'c40nor') {
                                                                        $destination_amounts['c40NOR'] = $amount_value;
                                                                        unset($destination_amounts['c40nor']);
                                                                    } elseif ($k == 'c45hc') {
                                                                        $destination_amounts['c45HC'] = $amount_value;
                                                                        unset($destination_amounts['c45hc']);
                                                                    }
                                                                }
                                                            }
                                                            if(!Empty($destination_markups)){
                                                                foreach ($destination_markups as $k => $markup_value) {
                                                                    if ($k == 'm20') {
                                                                        $destination_markups['m20DV'] = $markup_value;
                                                                        unset($destination_markups['m20']);
                                                                    } elseif ($k == 'm40') {
                                                                        $destination_markups['m40DV'] = $markup_value;
                                                                        unset($destination_markups['m40']);
                                                                    } elseif ($k == 'm40hc') {
                                                                        $destination_markups['m40HC'] = $markup_value;
                                                                        unset($destination_markups['m40hc']);
                                                                    } elseif ($k == 'm40nor') {
                                                                        $destination_markups['m40NOR'] = $markup_value;
                                                                        unset($destination_markups['m40nor']);
                                                                    } elseif ($k == 'm45hc') {
                                                                        $destination_markups['m45HC'] = $markup_value;
                                                                        unset($destination_markups['m45hc']);
                                                                    }
                                                                }
                                                            }
                                                        @endphp

                                                        <tr style="height:40px;">
                                                            <td class="tds" style="padding-left: 30px">
                                                                <input name="type" value="1" class="form-control type" type="hidden" />
                                                                <input name="charge_id" value="{{$item->id}}" class="form-control charge_id td-a" type="hidden" />

                                                                <a href="#" class="editable surcharge_id td-a" data-source="{{$surcharges}}" data-type="select"  data-name="surcharge_id" data-value="{{$item->surcharge_id}}" data-pk="{{$item->id}}" data-title="Select surcharge"></a>
                                                            </td>
                                                            <td class="tds">
                                                                <a href="#" class="editable calculation_type_id td-a" data-source="{{$calculation_types}}" data-name="calculation_type_id" data-type="select" data-value="{{$item->calculation_type_id}}" data-pk="{{$item->id}}" data-title="Select calculation type"></a>
                                                            </td>
                                                            <!-- Seteando tarifas de contenedores -->                                 
                                                            @foreach ($equipmentHides as $key=>$hide)
                                                                @foreach ($containers as $c)
                                                                    @if($c->code == $key)
                                                                        @php
                                                                            ${$pre.$c->code} = 'c'.$c->code;
                                                                            ${$pre.$c->code.$post} = 'c'.$c->code.'_markup';
                                                                        @endphp
                                                                        <td {{$hide}} class="tds">
                                                                            <a href="#" class="editable-amount-rate amount_{{$key}} td-a" data-type="text" data-name="amount->c{{$key}}" data-value="{{@$destination_amounts['c'.$key]}}" data-pk="{{$item->id}}" data-container="{{$key}}" data-cargo-type="destination" data-title="Total"></a>
                                                                            +
                                                                            <a href="#" class="editable-amount-markup markup_{{$key}} td-a" data-type="text" data-name="markups->m{{$key}}" data-value="{{@$destination_markups['m'.$key]}}" data-pk="{{$item->id}}" data-container="{{$key}}" data-cargo-type="destination" data-title="Total"></a>
                                                                            <i class="la la-caret-right arrow-down"></i>
                                                                            <span class="total_{{$key}} td-a">{{@$destination_amounts['c'.$c->code]+@$destination_markups['m'.$c->code]}}</span>
                                                                        </td>
                                                                        @php
                                                                            ${'sum_destination'.$c->code} += @$item->${$pre.$c->code};
                                                                            ${'sum_destination_m'.$c->code} += @$item->${$pre.$c->code.$post};
                                                                        @endphp
                                                                    @endif
                                                                @endforeach
                                                            @endforeach
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
                                                    @foreach ($equipmentHides as $key=>$hide)
                                                        @foreach ($containers as $c)
                                                            @if($c->code == $key)
                                                                <td {{$hide}} class="tds">
                                                                    <div class="row ">
                                                                        <div class="col-12">
                                                                            <div class="input-group">
                                                                                <input name="hide_{{$key}}" value="{{ $hide }}" class="form-control hide_{{$key}}" type="hidden" min="0" step="0.0000001" />
                                                                                <input name="amount_c{{$key}}" class="amount_c{{$key}} form-control" type="number" min="0" step="0.0000001" placeholder="Rate"/>

                                                                                <input name="markup_m{{$key}}" class="form-control markup_m{{$key}}" type="number" min="0" step="0.0000001" placeholder="Markup"/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                    <td>
                                                        <div class="input-group">
                                                            <div class="input-group-btn">
                                                                <div class="btn-group">
                                                                    {{ Form::select('destination_ammount_currency[]',$currencies,$company_user->currency->id,['class'=>'form-control local_currency currency_id select-2-width']) }}
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
                                                        @foreach ($equipmentHides as $key=>$hide)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td {{$hide}} class="tds">
                                                                        <span class="td-a total_destination_{{$c->code}}">{{number_format( ${'sum_destination'.$c->code} + ${'sum_destination_m'.$c->code} , 2, '.', '')}}</span>
                                                                    </td>
                                                                    <input type="hidden" name="subtotal_c{{$c->code}}_destination" value="{{ ${'sum_destination'.$c->code} }}" class="subtotal_c{{$c->code}}_destination"/>
                                                                    <input type="hidden" name="subtotal_m{{$c->code}}_destination" value="{{ ${'sum_destination_m'.$c->code} }}" class="subtotal_m{{$c->code}}_destination"/>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td class="tds"><span class="td-a">{{$company_user->currency->alphacode}}</span></td>
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
                                                        @foreach ($equipmentHides as $key=>$item)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td class="td-table" {{$item}}>{{$key}}</td>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td class="tds">Currency</td>
                                                    </tr>
                                                </thead>
                                                <tbody style="background-color: white;">
                                                    <?php
                                                        foreach ($equipmentHides as $key=>$hide){
                                                            foreach ($containers as $c){
                                                                if($c->code == $key){
                                                                    ${'amount_'.$key}=number_format(${'sum'.$c->code}+${'sum_origin'.$c->code}+${'sum_destination'.$c->code}, 2, '.', '');
                                                                    ${'markup_'.$key}=number_format(${'sum_m'.$c->code}+${'sum_origin_m'.$c->code}+${'sum_destination_m'.$c->code}, 2, '.', '');
                                                                    ${'amount_markup_'.$key}=number_format(${'amount_'.$key}+${'markup_'.$key}, 2, '.', '');
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    <tr style="height:40px;">
                                                        <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>
                                                        @foreach ($equipmentHides as $key=>$hide)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td {{$hide}} class="tds">
                                                                        <span class="td-a sum_total_amount_{{$c->code}}">{{ ${'amount_'.$key} }}</span> + <span class=" td-a sum_total_markup_{{$c->code}}">{{ ${'markup_'.$key} }}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_{{$c->code}} td-a">{{ ${'amount_markup_'.$key} }}</span>
                                                                    </td>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td class="tds"><span class="td-a global-currency">{{$company_user->currency->alphacode}}</span></td>
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
                                                        @foreach ($equipmentHides as $key=>$item)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td class="td-table" {{$item}}>{{$key}}</td>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td class="tds">Currency</td>
                                                    </tr>
                                                </thead>
                                                <tbody style="background-color: white;">
                                                    <?php
                                                        foreach ($equipmentHides as $key=>$hide){
                                                            foreach ($containers as $c){
                                                                if($c->code == $key){
                                                                    ${'amount_'.$key}=number_format(${'sum'.$c->code}+${'sum_origin'.$c->code}+${'sum_destination'.$c->code}, 2, '.', '');
                                                                    ${'markup_'.$key}=number_format(${'sum_m'.$c->code}+${'sum_origin_m'.$c->code}+${'sum_destination_m'.$c->code}, 2, '.', '');
                                                                    ${'amount_markup_'.$key}=number_format(${'amount_'.$key}+${'markup_'.$key}, 2, '.', '');
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    <tr style="height:40px;">
                                                        <td class="title-quote size-12px tds" style="padding-left: 30px"><span class="td-a">Total</span></td>
                                                        @foreach ($equipmentHides as $key=>$hide)
                                                            @foreach ($containers as $c)
                                                                @if($c->code == $key)
                                                                    <td {{$hide}} class="tds">
                                                                        <span class="td-a sum_total_amount_{{$c->code}}">{{ ${'amount_'.$key} }}</span> + <span class=" td-a sum_total_markup_{{$c->code}}">{{ ${'markup_'.$key} }}</span> <i class="la la-caret-right arrow-down"></i> <span class="sum_total_{{$c->code}} td-a">{{ ${'amount_markup_'.$key} }}</span>
                                                                    </td>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <td class="tds"><span class="td-a global-currency">{{$company_user->currency->alphacode}}</span></td>
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

                                                        if(!Empty($inland_rates)){
                                                                foreach ($inland_rates as $k => $amount_value) {
                                                                    if ($k == 'c20') {
                                                                        $inland_rates['c20DV'] = $amount_value;
                                                                        unset($inland_rates['c20']);
                                                                    } elseif ($k == 'c40') {
                                                                        $inland_rates['c40DV'] = $amount_value;
                                                                        unset($inland_rates['c40']);
                                                                    } elseif ($k == 'c40hc') {
                                                                        $inland_rates['c40HC'] = $amount_value;
                                                                        unset($inland_rates['c40hc']);
                                                                    } elseif ($k == 'c40nor') {
                                                                        $inland_rates['c40NOR'] = $amount_value;
                                                                        unset($inland_rates['c40nor']);
                                                                    } elseif ($k == 'c45hc') {
                                                                        $inland_rates['c45hc'] = $amount_value;
                                                                        unset($inland_rates['c45hc']);
                                                                    }
                                                                }
                                                            }

                                                            if(!Empty($inland_markups)){
                                                                foreach ($inland_markups as $k => $markup_value) {
                                                                    if ($k == 'm20') {
                                                                        $inland_markups['m20DV'] = $markup_value;
                                                                        unset($inland_markups['m20']);
                                                                    } elseif ($k == 'm40') {
                                                                        $inland_markups['m40DV'] = $markup_value;
                                                                        unset($inland_markups['m40']);
                                                                    } elseif ($k == 'm40hc') {
                                                                        $inland_markups['m40HC'] = $markup_value;
                                                                        unset($inland_markups['m40hc']);
                                                                    } elseif ($k == 'm40nor') {
                                                                        $inland_markups['m40NOR'] = $markup_value;
                                                                        unset($inland_markups['m40nor']);
                                                                    } elseif ($k == 'm45hc') {
                                                                        $inland_markups['m45HC'] = $markup_value;
                                                                        unset($inland_markups['m45hc']);
                                                                    }
                                                                }
                                                            }
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
                                                                            @foreach ($equipmentHides as $key=>$item)
                                                                                @foreach ($containers as $c)
                                                                                    @if($c->code == $key)
                                                                                        <td class="td-table" {{$item}}>{{$key}}</td>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endforeach
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
                                                                            <!-- Seteando tarifas de contenedores -->                                 
                                                                            @foreach ($equipmentHides as $key=>$hide)
                                                                                @foreach ($containers as $c)
                                                                                    @if($c->code == $key)
                                                                                        <td {{$hide}} class="tds">
                                                                                            <a href="#" class="editable-inland-rate inland_amount_{{$key}} td-a" data-type="text" data-name="rate->c{{$key}}" data-value="{{@$inland_rates['c'.$c->code]}}" data-pk="{{$inland->id}}" data-container="{{$key}}" data-cargo-type="inland" data-title="Rate"></a>
                                                                                            +
                                                                                            <a href="#" class="editable-inland-markup inland_markup_{{$key}} td-a" data-type="text" data-name="markup->m{{$key}}" data-value="{{@$inland_markups['m'.$c->code]}}" data-pk="{{$inland->id}}" data-container="{{$key}}" data-cargo-type="inland" data-title="Markup"></a>
                                                                                            <i class="la la-caret-right arrow-down"></i>
                                                                                            <span class="total_inland_{{$key}} td-a">{{@$inland_rates['c'.$c->code]+@$inland_markups['m'.$c->code]}}</span>
                                                                                        </td>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endforeach
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
                                                                            @foreach ($equipmentHides as $key=>$hide)
                                                                                @foreach ($containers as $c)
                                                                                    @if($c->code == $key)
                                                                                        <td {{$hide}} class="tds">
                                                                                            <input class="amount_{{$key}} form-control" type="text" name="rate_{{$key}}" value="" title="Distance" placeholder="Rate">
                                                                                            <input class="markup_{{$key}} form-control" type="text" name="markup_{{$key}}" value="" title="Distance" placeholder="Markup">
                                                                                        </td>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endforeach
                                                                            <td class="tds">
                                                                                <div class="btn-group">
                                                                                    {{ Form::select('destination_ammount_currency[]',$currencies,$company_user->currency->id,['class'=>'form-control currency_id select-2-width']) }}
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
                            @include('quotesv2.partials.remarks')
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