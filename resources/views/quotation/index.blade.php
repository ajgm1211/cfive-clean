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
            <table class="table m-table m-table--head-separator-primary" id="sample_editable_1">
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
                    $subtotalRate = 0;

                    @endphp
                    <tr>
                        <td>
                            Details <br>
                            <a  id='display_l{{++$loop->index}}' onclick="display({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" >
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
                                        <span class="m-widget5__number">{{$arr->currency->alphacode  }} {{ $sub[$key] }}    

                                        </span><br>
                                        <button type="button" class="btn m-btn--square  btn-primary m-btn--wide">Select</button><br>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr id="detail{{$loop->index}}" hidden="true">
                        <td colspan="6">
                            <b>Freight Charges</b>
                            <hr>
                            <table class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Carrier</th>
                                    <th>Detail  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>
                                @if($formulario->twuenty !=null)
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Per Container 20'</td>
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
                                @if($formulario->forty !=null)
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Per Container 40' </td>
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
                                @if($formulario->fortyhc !=null)
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Per Container 40HC'</td>
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
                                @if( ($formulario->twuenty !=null) || ($formulario->forty !=null) || ($formulario->fortyhc !=null) )
                                <td colspan="4"></td>
                                <td ><b>SUBTOTAL:</b></td>
                                <td>{{$subtotalRate  }} {{ $arr->currency->alphacode  }}</td>

                                @else
                                <td colspan='6'>No data available</td>

                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr id="origin{{$loop->index}}" hidden="true">
                        <td colspan="6">
                            <b>Destination Charges</b>
                            <hr>
                            <table class="table m-table m-table--head-separator-primary">
                                <tr>
                                    <th>Carrier</th>
                                    <th>Detail  </th>
                                    <th>Units</th>
                                    <th>Price per Unit</th>
                                    <th>Ammount</th>
                                    <th>Total Ammount</th>
                                </tr>
                                @if($formulario->twuenty !=null)
                                @foreach($localTwuenty as $originTwuenty)
                                @if($originTwuenty->changetype == "destination")
                                @foreach($originTwuenty->localcharcarriers as $carrierTwuenty)
                                @if($carrierTwuenty->carrier_id == $arr->carrier->id )
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Per Container 20'</td>
                                    <td>{{ $formulario->twuenty  }}</td>
                                    <td>{{ $originTwuenty->ammount  }} {{ $originTwuenty->currency->alphacode  }}</td>
                                    <td>{{ $formulario->twuenty *  $originTwuenty->ammount   }} {{ $originTwuenty->currency->alphacode  }}</td>
                                    <td>
                                        {{ $formulario->twuenty *  $originTwuenty->ammount   }} {{ $originTwuenty->currency->alphacode  }} 
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                                @endforeach
                                @endif

                                @if($formulario->forty !=null)
                                @foreach($localForty as $originForty)
                                @if($originForty->changetype == "destination")
                                @foreach($originForty->localcharcarriers as $carrierForty)
                                @if($carrierForty->carrier_id == $arr->carrier->id )
                                <tr>
                                    <td>{{$arr->carrier->name  }}</td>
                                    <td>Per Container 40'</td>
                                    <td>{{ $formulario->forty  }}</td>
                                    <td>{{ $originForty->ammount  }} {{ $originForty->currency->alphacode  }}</td>
                                    <td>{{ $formulario->forty *  $originForty->ammount   }} {{ $originTwuenty->currency->alphacode  }}</td>
                                    <td>
                                        {{ $formulario->forty *  $originForty->ammount   }} {{ $originForty->currency->alphacode  }} 
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                                @endforeach
                                @endif

                            </table>
                        </td>
                    </tr>

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


