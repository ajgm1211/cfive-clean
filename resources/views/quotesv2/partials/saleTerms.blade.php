@php
$x=0;
@endphp
@foreach($sale_terms as $item)
<div class="row">
    <div class="col-md-12">
        <div class="m-portlet custom-portlet no-border">
            <div class="m-portlet__body padding-portlet">
                <div class="tab-content">
                    <div class="flex-list" style=" margin-bottom:-30px; margin-top: 0;">
                        <ul >
                            <li class="m-width-150" style="border-left:none;">

                            </li>
                            <li class="size-12px long-text m-width-150"><b>Type:</b> &nbsp;{{$item->type}}</li>
                            @if($quote->type!='AIR')
                            <li class="size-12px long-text ">&nbsp;{{$item->port['name'].', '.$item->port['code']}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{@$item->country_code}}.svg"/></li>
                            @else
                            <li class="size-12px long-text ">&nbsp;{{$item->airport['name'].', '.$item->airport['code']}} &nbsp;</li>
                            @endif
                            <li class="size-12px no-border-left d-flex justify-content-end m-width-100">
                                <div onclick="show_hide_element('saleterms_{{$x}}')"><i class="fa fa-angle-down"></i></div>
                            </li>
                            <li class="size-12px m-width-100">
                                <!--<button onclick="AbrirModal('editSaleTerm',{{$item->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit">
                                    <i class="la la-edit"></i>
                                </button>-->

                                <button class="delete-sale-term m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" data-saleterm-id="{{$item->id}}">
                                    <i class="la la-trash"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <br>
                    <div class="saleterms_{{$x}} rates hide" style="background-color: white; border-radius: 5px; margin-top: 20px;">
                        <!-- Freight charges -->
                        <div class="row no-mg-row">
                            <div class="col-md-12 header-charges">
                                <h5 class="title-quote size-12px">Local charges</h5>
                            </div>
                            <div class="col-md-12 thead">
                                <div class="table-responsive">
                                    <table class="table fc table-sm table-bordered table-hover table color-blue text-center freight-table">
                                        <thead class="title-quote text-center header-table">
                                            <tr style="height: 40px;">
                                                <td class="td-table" style="padding-left: 30px">Charge</td>
                                                <td class="td-table">Detail</td>
                                                <!-- Seteando cabeceras -->
                                                @foreach ($equipmentHides as $key=>$hide)
                                                    @foreach ($containers as $c)
                                                        @if($c->code == $key)
                                                            <td class="td-table" {{$hide}}>{{$key}}</td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                <td class="td-table {{$quote->type=='FCL' ? 'hide':''}}">Units</td>
                                                <td class="td-table {{$quote->type=='FCL' ? 'hide':''}}">Rate</td>
                                                <td class="td-table {{$quote->type=='FCL' ? 'hide':''}}">Total</td>
                                                <td class="td-table" >Currency</td>
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: white;">
                                            @php
                                                $pre = 'c';
                                            @endphp
                                            @foreach($item->charge as $v)
                                            @php
                                                $amounts = json_decode($v->rate, true);
                                            @endphp
                                            <tr class="tr-freight" style="height:40px;">
                                                <td class="tds" style="padding-left: 30px">
                                                    <a href="#" class="editable-saleterms td-a" data-type="text" data-name="charge" data-value="{{$v->charge}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="Charge" data-emptyText="-"></a>
                                                </td>
                                                <td class="tds">
                                                    <a href="#" class="editable-saleterms td-a" data-type="text" data-name="detail" data-value="{{$v->detail}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="Detail" data-emptyText="-"></a>
                                                </td>
                                                @foreach ($equipmentHides as $key=>$hide)
                                                    @foreach ($containers as $c)
                                                        @if($c->code == $key)
                                                            @php
                                                                ${$pre.$c->code} = 'c'.$c->code;
                                                                
                                                            @endphp
                                                            <td class="tds" {{ $hide }}><a href="#" class="editable-saleterms td-a" data-type="text" data-name="rate->c{{$key}}" data-value="{{@$amounts['c'.$key]}}" data-pk="{{$v->id}}" data-title="{{$key}}"></a></td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                <td class="tds {{$quote->type=='FCL' ? 'hide':''}}"><a href="#" class="editable-saleterms td-a" data-type="text" data-name="units" data-value="{{$v->units}}" data-pk="{{$v->id}}" data-title="units"></a></td>
                                                <td class="tds {{$quote->type=='FCL' ? 'hide':''}}"><a href="#" class="editable-saleterms td-a" data-type="text" data-name="amount" data-value="{{$v->amount}}" data-pk="{{$v->id}}" data-title="amount"></a></td>
                                                <td class="tds {{$quote->type=='FCL' ? 'hide':''}}"><a href="#" class="editable-saleterms td-a" data-type="text" data-name="total" data-value="{{$v->total}}" data-pk="{{$v->id}}" data-title="total"></a></td>
                                                <td class="tds"><a href="#" class="editable-saleterms td-a" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$v->currency_id}}" data-pk="{{$v->id}}" data-title="Select currency" data-emptyText="-"></a>
                                                &nbsp;
                                                <input type="hidden" name="saleterm_charge_id" class="form-control saleterm_charge_id" value="{{$v->id}}"/>
                                                <a class="delete-saleterm-charge" style="cursor: pointer;" title="Delete">
                                                    <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr class="hide" id="sale_charges_{{$x}}">
                                                <td class="tds" style="padding-left: 30px">
                                                    <input type="text" class="form-control charge" name="charge" placeholder="Charge"/>
                                                    <input type="hidden" class="form-control sale_term_id" name="sale_term_id" value="{{$item->id}}"/>
                                                </td>
                                                <td class="tds">
                                                    <input type="text" class="form-control detail" name="detail" placeholder="Detail"/>
                                                </td>
                                                @foreach ($equipmentHides as $key=>$hide)
                                                    @foreach ($containers as $c)
                                                        @if($c->code == $key)
                                                            <td class="tds" {{ $hide }}><input type="number" class="form-control c{{$key}}" name="c{{$key}}" placeholder="{{$key}}"/></td>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                                <td class="tds {{$quote->type=='FCL' ? 'hide':''}}"><input type="number" class="form-control amount" name="amount" placeholder="Amount"/></td>
                                                <td class="tds {{$quote->type=='FCL' ? 'hide':''}}"><input type="number" class="form-control units" name="units" placeholder="Units"/></td>
                                                <td class="tds {{$quote->type=='FCL' ? 'hide':''}}"><input type="number" class="form-control total" name="total" placeholder="Total"/></td>
                                                <td class="tds">
                                                    <div class="input-group">
                                                        <div class="input-group-btn">
                                                            <div class="btn-group">
                                                                {{ Form::select('currency_id',$currencies,$company_user->currency->id,['class'=>'form-control currency_id select-2-width']) }}
                                                            </div>
                                                            <a class="btn btn-xs btn-primary-plus store_sale_charge">
                                                                <span class="fa fa-save" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                            <a class="btn btn-xs btn-primary-plus removeFreightCharge">
                                                                <span class="fa fa-trash" role="presentation" aria-hidden="true"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-12">
                                <h5 class="title-quote pull-right">
                                    <b>Add charge</b><a class="btn" onclick="addSaleCharge({{$x}})" style="vertical-align: middle">
                                    <button class="btn-xs btn-primary-plus"><span class="fa fa-plus"></span></button>
                                    </a>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php
$x++;
@endphp
@endforeach