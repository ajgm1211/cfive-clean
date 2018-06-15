@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Quotes')
@section('content')


<div class="row">

    <div class="col-xl-3">
        <!--begin:: Widgets/Authors Profit-->
        <div class="m-portlet m-portlet--bordered-semi m-portlet--full-height ">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Filters
                        </h3>
                    </div>
                </div>
                <div class="m-portlet__head-tools">

                </div>
            </div>
            <div class="m-portlet__body">
                <div class="m-accordion m-accordion--default m-accordion--solid m-accordion--section  m-accordion--toggle-arrow" id="m_accordion_7" role="tablist">   

                    <!--begin::Item-->              
                    <div class="m-accordion__item">
                        <div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_7_item_1_head" data-toggle="collapse" href="#m_accordion_7_item_1_body" aria-expanded="false">
                            <span class="m-accordion__item-icon"><i class="fa flaticon-user-ok"></i></span>
                            <span class="m-accordion__item-title">Services</span>

                            <span class="m-accordion__item-mode"></span>     
                        </div>

                        <div class="m-accordion__item-body collapse" id="m_accordion_7_item_1_body" role="tabpanel" aria-labelledby="m_accordion_7_item_1_head" data-parent="#m_accordion_7" style=""> 
                            <div class="m-accordion__item-content">
                                <p>
                                <div class="m-checkbox-list">
                                    <label class="m-checkbox m-checkbox--state-brand">
                                        <input type="checkbox"> Direct
                                        <span></span>
                                    </label>
                                    <label class="m-checkbox m-checkbox--state-brand">
                                        <input type="checkbox">Transhipment
                                        <span></span>
                                    </label>
                                    <label class="m-checkbox m-checkbox--state-brand">
                                        <input type="checkbox">2 + Transhipment
                                        <span></span>
                                    </label>
                                </div>
                                </p>

                        </div>
                    </div>
                </div>
                <!--end::Item--> 

                <!--begin::Item--> 
                <div class="m-accordion__item">
                    <div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_7_item_2_head" data-toggle="collapse" href="#m_accordion_7_item_2_body" aria-expanded="    false">
                        <span class="m-accordion__item-icon"><i class="fa  flaticon-placeholder"></i></span>
                        <span class="m-accordion__item-title">Ammount</span>

                        <span class="m-accordion__item-mode"></span>     
                    </div>

                    <div class="m-accordion__item-body collapse" id="m_accordion_7_item_2_body" role="tabpanel" aria-labelledby="m_accordion_7_item_2_head" data-parent="#m_accordion_7"> 
                        <div class="m-accordion__item-content">
                            <p>

                            <div class="m-ion-range-slider">
                                <input type="hidden" id="m_slider_1"/>
                            </div>

                            </p>

                    </div>
                </div>
            </div>   
            <!--end::Item--> 

            <!--begin::Item--> 
            <div class="m-accordion__item">
                <div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_7_item_3_head" data-toggle="collapse" href="#m_accordion_7_item_3_body" aria-expanded="    false">
                    <span class="m-accordion__item-icon"><i class="fa  flaticon-alert-2"></i></span>
                    <span class="m-accordion__item-title">Carriers</span>
                    <span class="m-accordion__item-mode"></span>     
                </div>

                <div class="m-accordion__item-body collapse" id="m_accordion_7_item_3_body" role="tabpanel" aria-labelledby="m_accordion_7_item_3_head" data-parent="#m_accordion_7"> 
                    <div class="m-accordion__item-content">
                        <p>
                        <div class="m-checkbox-list">
                            <label class="m-checkbox m-checkbox--state-brand">
                                <input type="checkbox"> CMA CGM
                                <span></span>
                            </label>
                            <label class="m-checkbox m-checkbox--state-brand">
                                <input type="checkbox"> Maersk Line
                                <span></span>
                            </label>
                            <label class="m-checkbox m-checkbox--state-brand">
                                <input type="checkbox">2 + CSC
                                <span></span>
                            </label>
                        </div>
                        </p>

                </div>
            </div>                       
        </div>
        <!--end::Item-->                       
    </div>
</div>
</div>
<!--end:: Widgets/Authors Profit-->  </div>
<div class="col-xl-9">
    <!--begin:: Widgets/Best Sellers-->
    <div class="m-portlet m-portlet--full-height ">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Rates 

                    </h3>
                </div>
            </div>

        </div>
        <div class="m-portlet__body">
            <table  class="table m-table " id="sample_editable_1">
                <thead>
                    <tr>
                        <th title="Field #1">
                            Detail
                        </th>
                        <th title="Field #2">
                            Sort By :
                        </th>
                        <th title="Field #3">
                            Origin
                        </th>
                        <th title="Field #4">
                            Destination 
                        </th>
                        <th title="Field #5">
                            Validity
                        </th>
                        <th title="Field #6">
                            Price
                        </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($arreglo as $key => $arr)
                    @php

                    $inl = 'false';
                    $subtotalRate = 0;
                    @endphp
                    <tr id="principal{{$loop->iteration}}">
                        <td>
                            Details <br>
                            <a  id='display_l{{$loop->iteration}}' onclick="display({{$loop->iteration}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" >
                                <i  class="la la-plus"></i>
                            </a>
                        </td>
                        <td>
                            <div class="m-widget5">
                                <div class="m-widget5__item">
                                    <div class="m-widget5__pic"> 
                                        <img class="m-widget7__img" src="/assets/app/media/img//products/ccma.png" alt="">  
                                    </div>
                                </div>
                            </div>

                        </td>
                        <td>
                            {{$arr->port_origin->name  }}
                        </td>
                        <td>
                            {{$arr->port_destiny->name  }}
                        </td>
                        <td>     
                            <span class="m-widget5__info-date m--font-info">
                                {{ $formulario->date }}
                            </span>
                        </td>
                        <td>           
                            <div class="m-widget5">
                                <div class="m-widget5__item">
                                    <div class="m-widget5__stats1">
                                        <span class="m-widget5__number">{{ $arr->totalQuote }}

                                        </span><br>
                                        <button type="button" class="btn m-btn--square  btn-primary m-btn--wide">Select</button><br>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr id="origin{{$loop->iteration}}" hidden="true"  >
                        <td colspan="6">
                            <b>Origin Charges</b>
                            <hr>
                            <table  class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Charge</th>
                                    <th>Detail  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>
                                <!--  Local charge  containter 20 , TEU , Per Container in Origin -->

                                @foreach($arr->localOrig as $origin)
                                <tr>
                                    <td>{{ $origin['origin']['surcharge_name'] }}</td>
                                    <td>{{ $origin['origin']['calculation_name'] }} </td>
                                    <td>{{  $origin['origin']['cantidad']  }}</td>
                                    <td>{{ $origin['origin']['monto']  }} {{ $origin['origin']['currency']  }}</td>
                                    <td>{{  $origin['origin']['subtotal_local']  }} {{ $origin['origin']['currency']  }}</td>
                                    <td>{{  $origin['origin']['totalAmmount']  }} </td>
                                </tr>
                                @endforeach
                                @foreach($arr->globalOrig as $originGlo)
                                <tr>
                                    <td>{{ $originGlo['origin']['surcharge_name'] }}</td>
                                    <td>{{ $originGlo['origin']['calculation_name'] }} </td>
                                    <td>{{  $originGlo['origin']['cantidad']  }}</td>
                                    <td>{{ $originGlo['origin']['monto']  }} {{ $originGlo['origin']['currency']  }}</td>
                                    <td>{{  $originGlo['origin']['subtotal_global']  }} {{ $originGlo['origin']['currency']  }}</td>
                                    <td>{{  $originGlo['origin']['totalAmmount']  }} </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4"></td>
                                    <td ><b>SUBTOTAL:</b></td>
                                    <td>{{$arr->totalOrigin  }}  </td>
                                </tr>
                            </table>
                        </td>
                    </tr> 
                    <tr id="detail{{$loop->iteration}}"  hidden="true">
                        <td colspan="6">
                            <b>Freight Charges</b>
                            <hr>
                            <table class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Charge</th>
                                    <th>Details  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>
                                @if($formulario->twuenty !="0")
                                <tr>
                                    <td>Ocean Freight 20</td>
                                    <td>Container 20'</td>
                                    <td>{{ $formulario->twuenty  }}</td>
                                    <td>{{ $arr->twuenty  }} {{ $arr->currency->alphacode  }}</td>
                                    <td>{{ $arr->montT['subtotal']  }} {{ $arr->currency->alphacode  }}</td>
                                    <td>
                                        {{ $arr->montT['total']  }} 

                                    </td>
                                </tr>
                                @endif
                                @if($formulario->forty !="0")
                                <tr>
                                    <td> Ocean Freight 40</td>
                                    <td>Container 40' </td>
                                    <td>{{ $formulario->forty  }}</td>
                                    <td>{{ $arr->forty  }} {{ $arr->currency->alphacode  }}</td>
                                    <td>{{ $arr->montF['subtotal'] }}  {{ $arr->currency->alphacode  }}</td>
                                    <td>
                                        {{ $arr->montF['total']  }} 
                                    </td>
                                </tr>
                                @endif
                                @if($formulario->fortyhc !="0")
                                <tr>
                                    <td>Ocean Freight 40´HC</td>
                                    <td>Container 40HC'</td>
                                    <td>{{ $formulario->fortyhc  }}</td>
                                    <td>{{ $arr->fortyhc  }} {{ $arr->currency->alphacode  }}</td>
                                    <td>{{ $arr->montFHC['subtotal'] }} {{ $arr->currency->alphacode  }}</td>
                                    <td>
                                        {{ $arr->montFHC['total']  }} 
                                    </td>
                                </tr>
                                @endif

                                @foreach($arr->localFreight as $freight)
                                <tr>
                                    <td>{{ $freight['freight']['surcharge_name'] }}</td>
                                    <td>{{ $freight['freight']['calculation_name'] }} </td>
                                    <td>{{  $freight['freight']['cantidad']  }}</td>
                                    <td>{{ $freight['freight']['monto']  }} {{ $freight['freight']['currency']  }}</td>
                                    <td>{{  $freight['freight']['subtotal_local']  }} {{ $freight['freight']['currency']  }}</td>
                                    <td>{{  $freight['freight']['totalAmmount']  }} </td>
                                </tr>
                                @endforeach

                                @foreach($arr->globalFreight as $freightGlo)
                                <tr>
                                    <td>{{ $freightGlo['freight']['surcharge_name'] }}</td>
                                    <td>{{ $freightGlo['freight']['calculation_name'] }} </td>
                                    <td>{{  $freightGlo['freight']['cantidad']  }}</td>
                                    <td>{{ $freightGlo['freight']['monto']  }} {{ $freightGlo['freight']['currency']  }}</td>
                                    <td>{{  $freightGlo['freight']['subtotal_global']  }} {{ $freightGlo['freight']['currency']  }}</td>
                                    <td>{{  $freightGlo['freight']['totalAmmount']  }} </td>
                                </tr>
                                @endforeach




                                @if( ($formulario->twuenty !="0") || ($formulario->forty !="0") || ($formulario->fortyhc!="0") )
                                <tr>
                                    <td colspan="4"></td>
                                    <td ><b>SUBTOTAL:</b></td>
                                    <td>{{$arr->totalFreight  }}</td>
                                </tr>

                                @else
                                <tr>
                                    <td colspan='6'>No data available</td>
                                </tr>

                                @endif

                            </table>
                        </td>
                    </tr>
                    <tr id="destination{{$loop->iteration}}" hidden="true" >
                        <td colspan="6">
                            <b>Destination Charges</b>
                            <hr>
                            <table class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Charge</th>
                                    <th>Detail  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>

                                @foreach($arr->localDest as $destiny)
                                <tr>
                                    <td>{{ $destiny['destiny']['surcharge_name'] }}</td>
                                    <td>{{ $destiny['destiny']['calculation_name'] }} </td>
                                    <td>{{  $destiny['destiny']['cantidad']  }}</td>
                                    <td>{{ $destiny['destiny']['monto']  }} {{ @$destiny['destiny']['currency']  }}</td>
                                    <td>{{  $destiny['destiny']['subtotal_local']  }} {{ $destiny['destiny']['currency']  }}</td>
                                    <td>{{  $destiny['destiny']['totalAmmount']  }} </td>
                                </tr>
                                @endforeach

                                @foreach($arr->globalDest as $destinyGlo)
                                <tr>
                                    <td>{{ $destinyGlo['destiny']['surcharge_name'] }}</td>
                                    <td>{{ $destinyGlo['destiny']['calculation_name'] }} </td>
                                    <td>{{  $destinyGlo['destiny']['cantidad']  }}</td>
                                    <td>{{ $destinyGlo['destiny']['monto']  }} {{ @$destinyGlo['destiny']['currency']  }}</td>
                                    <td>{{  $destinyGlo['destiny']['subtotal_global']  }} {{ $destinyGlo['destiny']['currency']  }}</td>
                                    <td>{{  $destinyGlo['destiny']['totalAmmount']  }} </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4"></td>
                                    <td ><b>SUBTOTAL:</b></td>
                                    <td>{{  $arr->totalDestiny  }}  </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @if((!empty($inlandDestiny)) || (!empty($inlandOrigin)))
                    <tr id="inlands{{$loop->iteration}}" hidden="true" >
                        <td colspan="6">
                            <b>Inlands Charges</b>
                            <hr>
                            <table class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Provider</th>
                                    <th>Type</th>
                                    <th>Distance  </th>
                                    <th>Port Name</th>
                                    <th>Total Ammount</th>
                                </tr>
                                @if(!empty($inlandDestiny))
                                @foreach($inlandDestiny as $inlandDest)
                                @if($inlandDest['port_id'] == $arr->port_destiny->id )
                                <tr>
                                    <th>{{ $inlandDest['provider'] }}</th>
                                    <th>{{ $inlandDest['type'] }}</th>
                                    <th>{{ $inlandDest['km'] }} KM</th>
                                    <th>{{ $inlandDest['port_name'] }}</th>
                                    <th>{{ $inlandDest['monto'] }} {{  $inlandDest['type_currency'] }}</th>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                                @if(!empty($inlandOrigin))
                                @foreach($inlandOrigin as $inlandOrig)
                                @if($inlandOrig['port_id'] == $arr->port_origin->id )
                                <tr>
                                    <th>{{ $inlandOrig['provider'] }}</th>
                                    <th>{{ $inlandOrig['type'] }}</th>
                                    <th>{{ $inlandOrig['km'] }} KM</th>
                                    <th>{{ $inlandOrig['port_name'] }}</th>
                                    <th>{{ $inlandOrig['monto'] }} {{  $inlandOrig['type_currency'] }}</th>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                            </table>
                        </td>
                    </tr>
                    @endif
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <!--end:: Widgets/Best Sellers-->  
</div>
</div>
@endsection

@section('js')
@parent
<script src="/assets/plugins/table-datatables-editable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
<script src="/js/quote.js"></script>


@stop


