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
                    $destination = 'false';
                    $origin = 'false';
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
                                        <span class="m-widget5__number">{{$arr->currency->alphacode  }} 

                                        </span><br>
                                        <button type="button" class="btn m-btn--square  btn-primary m-btn--wide">Select</button><br>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr id="detail{{$loop->iteration}}"  hidden="true">
                        <td colspan="6">
                            <b>Freight Charges</b>
                            <hr>
                            <table class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Carrier</th>
                                    <th>Type  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>
                                @if($formulario->twuenty !="0")
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Container 20'</td>
                                    <td>{{ $formulario->twuenty  }}</td>
                                    <td>{{ $arr->twuenty  }} {{ $arr->currency->alphacode  }}</td>
                                    <td>{{ $formulario->twuenty *  $arr->twuenty   }} {{ $arr->currency->alphacode  }}</td>
                                    <td>
                                        @php
                                        $subtotalRate = $formulario->twuenty *  $arr->twuenty + $subtotalRate 
                                        @endphp
                                        {{ $formulario->twuenty *  $arr->twuenty   }} {{ $arr->currency->alphacode  }} 

                                    </td>
                                </tr>
                                @endif
                                @if($formulario->forty !="0")
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Container 40' </td>
                                    <td>{{ $formulario->forty  }}</td>
                                    <td>{{ $arr->forty  }} {{ $arr->currency->alphacode  }}</td>
                                    <td>{{ $formulario->forty *  $arr->forty   }} {{ $arr->currency->alphacode  }}</td>
                                    <td>
                                        @php
                                        $subtotalRate = $formulario->forty *  $arr->forty  + $subtotalRate 
                                        @endphp
                                        {{ $formulario->forty *  $arr->forty   }} {{ $arr->currency->alphacode  }} 
                                    </td>
                                </tr>
                                @endif
                                @if($formulario->fortyhc !="0")
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Container 40HC'</td>
                                    <td>{{ $formulario->fortyhc  }}</td>
                                    <td>{{ $arr->fortyhc  }} {{ $arr->currency->alphacode  }}</td>
                                    <td>{{ $formulario->fortyhc *  $arr->fortyhc   }} {{ $arr->currency->alphacode  }}</td>
                                    <td>
                                        @php
                                        $subtotalRate = $formulario->fortyhc *  $arr->fortyhc  + $subtotalRate 
                                        @endphp
                                        {{ $formulario->fortyhc *  $arr->fortyhc   }} {{ $arr->currency->alphacode  }} 
                                    </td>
                                </tr>
                                @endif

                                @foreach($freighTwuenty as $freigh20)
                                <tr>
                                    <td>{{$freigh20['carrier_name'] }}</td>
                                    <td>Container 20 ' Local</td>
                                    <td>{{ $freigh20['cantidad']  }}</td>
                                    <td>{{ $freigh20['monto']  }} {{ $freigh20['currency']  }}</td>
                                    <td>{{  $freigh20['totalAmmount']  }} {{ $freigh20['currency']  }}</td>

                                    <td>{{  $freigh20['totalAmmount']  }} {{ $freigh20['currency']  }}</td>

                                </tr>
                                @endforeach
                                @foreach($freighForty as $freigh40)
                                <tr>
                                    <td>{{$freigh40['carrier_name'] }}</td>
                                    <td>Container 40 ' Local</td>
                                    <td>{{ $freigh40['cantidad']  }}</td>
                                    <td>{{ $freigh40['monto']  }} {{ $freigh40['currency']  }}</td>
                                    <td>{{  $freigh40['totalAmmount']  }} {{ $freigh40['currency']  }}</td>

                                    <td>{{  $freigh40['totalAmmount']  }} {{ $freigh40['currency']  }}</td>

                                </tr>
                                @endforeach
                                @foreach($freighFortyHc as $freigh40hc)
                                <tr>
                                    <td>{{$freigh40hc['carrier_name'] }}</td>
                                    <td>Container 40 HC ' Local</td>
                                    <td>{{ $freigh40hc['cantidad']  }}</td>
                                    <td>{{ $freigh40hc['monto']  }} {{ $freigh40hc['currency']  }}</td>
                                    <td>{{  $freigh40hc['totalAmmount']  }} {{ $freigh40hc['currency']  }}</td>

                                    <td>{{  $freigh40hc['totalAmmount']  }} {{ $freigh40hc['currency']  }}</td>

                                </tr>
                                @endforeach
                                
                                @foreach($freightPer as $freightPeer)
                                <tr>
                                    <td>{{$freightPeer['carrier_name'] }}</td>
                                    <td>Per Shipment ' Local</td>
                                    <td>{{$freightPeer['calculation_name'] }} </td>
                                    <td>{{ $freightPeer['cantidad']  }}</td>
                                    <td>{{ $freightPeer['monto']  }} {{ $freightPeer['currency']  }}</td>
                                    <td>{{  $freightPeer['totalAmmount']  }} {{ $freightPeer['currency']  }}</td>

                                    <td>{{  $freightPeer['totalAmmount']  }} {{ $freightPeer['currency']  }}</td>

                                </tr>
                                @endforeach


                                @if( ($formulario->twuenty !="0") || ($formulario->forty !="0") || ($formulario->fortyhc!="0") )
                                <tr>
                                    <td colspan="4"></td>
                                    <td ><b>SUBTOTAL:</b></td>
                                    <td>{{$subtotalRate  }} {{ $arr->currency->alphacode  }}</td>
                                </tr>

                                @else
                                <tr>
                                    <td colspan='6'>No data available</td>
                                </tr>

                                @endif

                            </table>
                        </td>
                    </tr>
                    <tr id="origin{{$loop->iteration}}" hidden="true"  >
                        <td colspan="6">
                            <b>Origin Charges</b>
                            <hr>
                            <table  class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Carrier</th>
                                    <th>Type</th>
                                    <th>Detail  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>
                                <!--  Local charge  containter 20 , TEU , Per Container in Origin -->
                                @foreach($origTwuenty as $originTwuenty)
                                <tr>
                                    <td>{{$originTwuenty['carrier_name'] }}</td>
                                    <td>Container 20 ' Local</td>
                                    <td>{{$originTwuenty['calculation_name'] }} </td>
                                    <td>{{ $originTwuenty['cantidad']  }}</td>
                                    <td>{{ $originTwuenty['monto']  }} {{ $originTwuenty['currency']  }}</td>
                                    <td>{{  $originTwuenty['totalAmmount']  }} {{ $originTwuenty['currency']  }}</td>

                                    <td>{{  $originTwuenty['totalAmmount']  }} {{ $originTwuenty['currency']  }}</td>

                                </tr>
                                @endforeach
                                @foreach($origForty as $originForty)
                                <tr>
                                    <td>{{$originForty['carrier_name'] }}</td>
                                    <td>Container 40 ' Local</td>
                                    <td>{{$originForty['calculation_name'] }} </td>
                                    <td>{{ $originForty['cantidad']  }}</td>
                                    <td>{{ $originForty['monto']  }} {{ $originForty['currency']  }}</td>
                                    <td>{{  $originForty['totalAmmount']  }} {{ $originForty['currency']  }}</td>

                                    <td>{{  $originForty['totalAmmount']  }} {{ $originForty['currency']  }}</td>

                                </tr>
                                @endforeach
                                @foreach($origFortyHc as $originFortyHc)
                                <tr>
                                    <td>{{$originFortyHc['carrier_name'] }}</td>
                                    <td>Container 40 HC ' Local</td>
                                    <td>{{$originFortyHc['calculation_name'] }} </td>
                                    <td>{{ $originFortyHc['cantidad']  }}</td>
                                    <td>{{ $originFortyHc['monto']  }} {{ $originFortyHc['currency']  }}</td>
                                    <td>{{  $originFortyHc['totalAmmount']  }} {{ $originFortyHc['currency']  }}</td>

                                    <td>{{  $originFortyHc['totalAmmount']  }} {{ $originFortyHc['currency']  }}</td>

                                </tr>
                                @endforeach
                                @foreach($origPer as $originPer)
                                <tr>
                                    <td>{{$originPer['carrier_name'] }}</td>
                                    <td>Per Shipment ' Local</td>
                                    <td>{{$originPer['calculation_name'] }} </td>
                                    <td>{{ $originPer['cantidad']  }}</td>
                                    <td>{{ $originFororiginPertyHc['monto']  }} {{ $originPer['currency']  }}</td>
                                    <td>{{  $originPer['totalAmmount']  }} {{ $originPer['currency']  }}</td>

                                    <td>{{  $originPer['totalAmmount']  }} {{ $originPer['currency']  }}</td>

                                </tr>
                                @endforeach





                            </table>
                        </td>
                    </tr> 
                    <tr id="destination{{$loop->iteration}}" hidden="true" >
                        <td colspan="6">
                            <b>Destination Charges</b>
                            <hr>
                            <table class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Carrier</th>
                                    <th>Type</th>
                                    <th>Detail  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>
                                @foreach($destTwuenty as $destinyTwuenty)
                                <tr>
                                    <td>{{$destinyTwuenty['carrier_name'] }}</td>
                                    <td>Container 20 ' Local</td>
                                    <td>{{$destinyTwuenty['calculation_name'] }} </td>
                                    <td>{{ $destinyTwuenty['cantidad']  }}</td>
                                    <td>{{ $destinyTwuenty['monto']  }} {{ $destinyTwuenty['currency']  }}</td>
                                    <td>{{  $destinyTwuenty['totalAmmount']  }} {{ $destinyTwuenty['currency']  }}</td>

                                    <td>{{  $destinyTwuenty['totalAmmount']  }} {{ $destinyTwuenty['currency']  }}</td>

                                </tr>
                                @endforeach
                                @foreach($destForty as $destinyForty)
                                <tr>
                                    <td>{{$destinyForty['carrier_name'] }}</td>
                                    <td>Container 40 ' Local</td>
                                    <td>{{$destinyForty['calculation_name'] }} </td>
                                    <td>{{ $destinyForty['cantidad']  }}</td>
                                    <td>{{ $destinyForty['monto']  }} {{ $destinyForty['currency']  }}</td>
                                    <td>{{  $destinyForty['totalAmmount']  }} {{ $destinyForty['currency']  }}</td>

                                    <td>{{  $destinyForty['totalAmmount']  }} {{ $destinyForty['currency']  }}</td>

                                </tr>
                                @endforeach
                                @foreach($destFortyHc as $destinyFortyHc)
                                <tr>
                                    <td>{{$destinyFortyHc['carrier_name'] }}</td>
                                    <td>Container 40 HC ' Local</td>
                                    <td>{{$destinyFortyHc['calculation_name'] }} </td>
                                    <td>{{ $destinyFortyHc['cantidad']  }}</td>
                                    <td>{{ $destinyFortyHc['monto']  }} {{ $destinyFortyHc['currency']  }}</td>
                                    <td>{{  $destinyFortyHc['totalAmmount']  }} {{ $destinyFortyHc['currency']  }}</td>

                                    <td>{{  $destinyFortyHc['totalAmmount']  }} {{ $destinyFortyHc['currency']  }}</td>

                                </tr>
                                @endforeach

                                @foreach($destPer as $destinyPer)
                                <tr>
                                    <td>{{$destinyPer['carrier_name'] }}</td>
                                    <td>Per Shipment ' Local</td>
                                    <td>{{$destinyPer['calculation_name'] }} </td>
                                    <td>{{ $destinyPer['cantidad']  }}</td>
                                    <td>{{ $destinyPer['monto']  }} {{ $destinyPer['currency']  }}</td>
                                    <td>{{  $destinyPer['totalAmmount']  }} {{ $destinyPer['currency']  }}</td>

                                    <td>{{  $destinyPer['totalAmmount']  }} {{ $destinyPer['currency']  }}</td>

                                </tr>
                                @endforeach

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
                                    <th>{{ $inlandDest['monto'] }}</th>
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
                                    <th>{{ $inlandOrig['monto'] }}</th>
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


