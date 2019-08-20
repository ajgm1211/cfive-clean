@php
$v=0;
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
                            <li class="size-12px long-text">&nbsp;{{$item->port['name'].', '.$item->port['code']}} &nbsp;<img class="rounded" style="width: 15px !important; padding-top: 0 0 0 0!important; margin-top: -5px !important;" src="/images/flags/1x1/{{@$item->country_code}}.svg"/></li>
                            <li class="size-12px no-border-left d-flex justify-content-end m-width-100">
                                <div onclick="show_hide_element('saleterms_{{$v}}')"><i class="fa fa-angle-down"></i></div>
                            </li>
                            <li class="size-12px m-width-100">
                                <button onclick="AbrirModal('edit',{{$item->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit">
                                    <i class="la la-edit"></i>
                                </button>

                                <button class="delete-rate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" data-rate-id="{{$item->id}}">
                                    <i class="la la-trash"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <br>
                    <div class="saleterms_{{$v}} rates hide" style="background-color: white; border-radius: 5px; margin-top: 20px;">
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
                                                <td class="td-table" {{ @$equipmentHides['20'] }}>20'</td>
                                                <td class="td-table" {{ @$equipmentHides['40'] }}>40'</td>
                                                <td class="td-table" {{ @$equipmentHides['40hc'] }}>40HC'</td>
                                                <td class="td-table" {{ @$equipmentHides['40nor'] }}>40NOR'</td>
                                                <td class="td-table" {{ @$equipmentHides['45'] }}>45'</td>
                                                <td class="td-table" >Currency</td>
                                            </tr>
                                        </thead>
                                        <tbody style="background-color: white;">
                                            
                                            @foreach($item->charge as $v)
                                                <tr class="tr-freight" style="height:40px;">
                                                <td class="tds" style="padding-left: 30px">
                                                    <a href="#" class="editable td-a" data-type="text" data-name="charge" data-value="{{$v->charge}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="Charge" data-emptyText="-"></a>
                                                </td>
                                                <td class="tds">
                                                    <a href="#" class="editable td-a" data-type="text" data-name="charge" data-value="{{$v->detaild}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="Detail" data-emptyText="-"></a>
                                                </td>
                                                <td class="tds" {{ @$equipmentHides['20'] }}><a href="#" class="editable td-a" data-type="text" data-name="c20" data-value="{{$v->c20}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="20"></a></td>
                                                <td class="tds" {{ @$equipmentHides['40'] }}><a href="#" class="editable td-a" data-type="text" data-name="c40" data-value="{{$v->c40}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="40"></a></td>
                                                <td class="tds" {{ @$equipmentHides['40hc'] }}><a href="#" class="editable td-a" data-type="text" data-name="c40hc" data-value="{{$v->c40hc}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="40hc"></a></td>
                                                <td class="tds" {{ @$equipmentHides['40nor'] }}><a href="#" class="editable td-a" data-type="text" data-name="c40nor" data-value="{{$v->c40nor}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="20"></a></td>
                                                <td class="tds" {{ @$equipmentHides['45'] }}><a href="#" class="editable td-a" data-type="text" data-name="c45" data-value="{{$v->c45}}" data-pk="{{$v->id}}" data-cargo-type="freight" data-title="20"></a></td>
                                                <td class="tds"><a href="#" class="editable td-a" data-source="{{$currencies}}" data-type="select" data-name="currency_id" data-value="{{$v->currency_id}}" data-pk="{{$v->id}}" data-title="Select currency" data-emptyText="-"></a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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