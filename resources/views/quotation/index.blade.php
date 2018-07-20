@extends('layouts.app')
@section('css')
@parent
<link href="/css/quote.css" rel="stylesheet" type="text/css" />
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
        <div class="m-demo">
          <div class="m-demo__preview">
            <div class="m-list-search">
              <div class="m-list-search__results">
                <span class="m-list-search__result-message m--hide">
                  No record found
                </span>
                <span class="m-list-search__result-category m-list-search__result-category--first">
                  Services
                </span>
                <a href="#" class="m-list-search__result-item">
                  <span class="m-list-search__result-item-icon"><i class="flaticon-interface-3 m--font-warning"></i></span>
                  <span class="m-list-search__result-item-text">
                    Direct                    
                  </span>
                  <label class="m-checkbox m-checkbox--state-brand">
                    <input type="checkbox"> 
                    <span></span>
                  </label>
                </a>
                <a href="#" class="m-list-search__result-item">
                  <span class="m-list-search__result-item-icon"><i class="flaticon-share m--font-success"></i></span>
                  <span class="m-list-search__result-item-text">Transhipment</span>
                  <label class="m-checkbox m-checkbox--state-brand">
                    <input type="checkbox"> 
                    <span></span>
                  </label>
                </a>
                <a href="#" class="m-list-search__result-item">
                  <span class="m-list-search__result-item-icon"><i class="flaticon-paper-plane m--font-info"></i></span>
                  <span class="m-list-search__result-item-text">2 + Transhipment</span>
                  <label class="m-checkbox m-checkbox--state-brand">
                    <input type="checkbox"> 
                    <span></span>
                  </label>
                </a>
                <span class="m-list-search__result-category">
                  Ammount
                </span>
                <a href="#" class="m-list-search__result-item">
                  <span class="m-list-search__result-item-icon"><i class="flaticon-lifebuoy m--font-warning"></i></span>
                  <span class="m-list-search__result-item-text">Min/Max Price</span>

                </a>
                <div class="col-sm-12">
                  <input type="hidden" id="m_slider_1"/>
                  <div class="m-ion-range-slider">
                    <input type="hidden" id="m_slider_1"/>
                  </div>
                </div>
                <a href="#" class="m-list-search__result-item">
                  <span class="m-list-search__result-item-icon"><i class="flaticon-coins m--font-primary"></i></span>
                  <span class="m-list-search__result-item-text">Min Charge</span>
                </a>
                <div class="col-sm-12">
                  <input type="hidden" id="m_slider_1_1"/>
                  <div class="m-ion-range-slider">
                    <input type="hidden" id="m_slider_1_1"/>
                  </div>
                </div>

                <span class="m-list-search__result-category">
                  Carrier 
                </span>
                <a href="#" class="m-list-search__result-item">
                  <span class="m-list-search__result-item-pic"><img class="m--img"  src="{{ url('imgcarrier/cosco.jpeg') }} " title="">
                  </span>
                  <span class="m-list-search__result-item-text">Cosco</span>
                  <label class="m-checkbox m-checkbox--state-brand">
                    <input type="checkbox"> 
                    <span></span>
                  </label>
                </a>
                <a href="#" class="m-list-search__result-item">
                  <span class="m-list-search__result-item-pic"><img class="m--img"  src="{{ url('imgcarrier/apl_logo.png') }} " title="">
                  </span>
                  <span class="m-list-search__result-item-text">APL</span>
                  <label class="m-checkbox m-checkbox--state-brand">
                    <input type="checkbox"> 
                    <span></span>
                  </label>
                </a>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--end:: Widgets/Authors Profit--> 
  </div>

  <div class="col-xl-9">
    @if(!$arreglo->isEmpty())
    <div  class="row">
      <div class="col-xl-11">
        <div class="m-portlet m-portlet--full-height ">

          <div class="m-portlet__body">
            <table  class="table m-table m-table--head-separator-primary"  border="0" id="">
              <thead>
                <tr>
                  <th  width = '20%' title="Field #2">
                    <span class="darkblue cabezeras">Sort By: </span>
                  </th>
                  <th  width = '20%' title="Field #3">
                    <span class="gray cabezeras">Origin </span>
                  </th>
                  <th  width = '20%' title="Field #4" >
                    <span  class="gray cabezeras"  style=" float: right;">Destination </span> 
                  </th>
                  <th  width = '20%' title="Field #5">
                    <span class="gray cabezeras"> Expire</span> 
                  </th>
                  <th  width = '20%' title="Field #6" >
                    <span class="gray cabezeras"  style=" float: right;">Price</span>  
                  </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    @foreach ($arreglo as $key => $arr)
    @php
    $inl = 'false';
    @endphp
    <div  class="row">
      <div class="col-xl-11">
        <div class="m-portlet m-portlet--full-height ">
          <div class="m-portlet__body">
            {!! Form::open(['route' => ['quotes.test'] ,'name' => 'info','method' => 'post','class' => 'form-group m-form__group']) !!}
            <table  class="table m-table m-table--head-separator-primary" border="0" id="sample_editable">
              <tbody>

                <input type="hidden" name="info" value="{{ json_encode($arr) }}">
                <input type="hidden" name="form" value="{{ json_encode($form) }}">
                <tr id="principal{{$loop->iteration}}">

                  <td width = '20%'>
                    <div class="m-widget5">
                      <div class="m-widget5__item">
                        <div class="m-widget5__pic"> 
                          <img src="{{ url('imgcarrier/'.$arr->carrier->image) }}" alt="" title="" />
                        </div>
                      </div>
                    </div>

                  </td>
                  <td width = '40%' colspan="2">

                    <div class="row">
                      <div class="col-md-4">
                        <span class="portcss"> {{$arr->port_origin->name  }}</span><br>
                        <span class="portalphacode"> {{$arr->port_origin->code  }}</span>
                      </div>
                      <div class="col-md-4">
                        <div class="progress m-progress--sm">
                          <div class="progress-bar " role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <span class="portcss"> {{$arr->port_destiny->name  }}</span><br>
                        <span class="portalphacode"> {{$arr->port_destiny->code  }}</span>
                      </div>
                    </div>

                    <br>
                    <span class="workblue">Detail Cost</span>  <a  id='display_l{{$loop->iteration}}' onclick="display({{$loop->iteration}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" >
                    <i  class="la la-angle-down blue"></i>
                    </a>
                  </td>

                  <td width = '20%'>

                    <span class="darkblue validate"> {{   \Carbon\Carbon::parse($arr->contract->expire)->format('d M Y') }}</span>
                  </td>
                  <td width = '20%'>     
                    <div class="m-widget5" style="float:right;">

                      <span class="m-widget5__number"> <span class="portalphacode"> {{ $arr->quoteCurrency }} </span> <span class="darkblue totalq">  {{ $arr->totalQuoteSin }} </span> 

                      </span><br>
                      <button type="submit" class="btn boton btn-md">Select</button><br>

                      @if(!$arr->schedulesFin->isEmpty())
                      <span class="workblue">Salling Schedules</span>  <a  id='schedule_l{{$loop->iteration}}'  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" onclick="schedules({{$loop->iteration}})"  title="Cancel" >
                      <i  class="la la-angle-down blue"></i>
                      </a>
                      @endif

                    </div>
                  </td>

                </tr>
                @if((!$arr->globalOrig->isEmpty()) || (!$arr->localOrig->isEmpty()))
                <tr id="origin{{$loop->iteration}}" hidden="true"  >
                  <td colspan="6">
                    <span class="darkblue cabezeras">Origin Charges</span>
                    <hr>
                    <table  class="table  table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Charge</span></th>
                        <th><span class="portalphacode">Detail</span>  </th>
                        <th><span class="portalphacode">Units</span></th>
                        <th><span class="portalphacode">Price per Unit</span></th>
                        <th><span class="portalphacode">Ammount</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>
                      <!--  Local charge  containter 20 , TEU , Per Container in Origin -->

                      @foreach($arr->localOrig as $origin)
                      <tr>
                        <td>{{ $origin['origin']['surcharge_name'] }}</td>
                        <td>{{ $origin['origin']['calculation_name'] }} </td>
                        <td>{{  $origin['origin']['cantidad']  }}</td>
                        <td>{{ $origin['origin']['monto']  }} {{ $origin['origin']['currency']  }}</td>
                        <td>{{  $origin['origin']['subtotal_local']  }} {{ $origin['origin']['currency']  }}</td>
                        <td>{{  $origin['origin']['markup']  }} {{ $origin['origin']['typemarkup']  }}</td>
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
                        <td>{{  $originGlo['origin']['markup']  }} {{ $originGlo['origin']['typemarkup']  }}</td>
                        <td>{{  $originGlo['origin']['totalAmmount']  }} </td>
                      </tr>
                      @endforeach
                      <tr>
                        <td colspan="5"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td><span  class="darkblue px12" >{{$arr->totalOrigin  }} </span> </td>
                      </tr>
                    </table>
                  </td>
                </tr> 
                @endif
                <tr id="detail{{$loop->iteration}}"  hidden="true">
                  <td colspan="6">
                    <span class="darkblue cabezeras">Freight Charges</span>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Charge</span></th>
                        <th><span class="portalphacode">Details  </span></th>
                        <th><span class="portalphacode">Units</span></th>
                        <th><span class="portalphacode">Price per Unit</span></th>
                        <th><span class="portalphacode">Ammount</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>

                      @foreach($arr->rates  as $var)
                      <tr>
                        <td>{{ $var['type'] }}</td>
                        <td>{{ $var['detail'] }}</td>
                        <td>{{ $var['cantidad'] }}</td>
                        <td>{{ $var['price'] }} {{ $var['currency']   }}</td>
                        <td>{{ $var['subtotal'] }} {{ $var['currency']   }}</td>
                        <td>{{ $var['markup'] }} {{ $var['typemarkup']   }}</td>
                        <td>{{ $var['total'] }}</td>
                      </tr>
                      @endforeach
                      @foreach($arr->localFreight as $freight)
                      <tr>
                        <td>{{ $freight['freight']['surcharge_name'] }}</td>
                        <td>{{ $freight['freight']['calculation_name'] }} </td>
                        <td>{{  $freight['freight']['cantidad']  }}</td>
                        <td>{{ $freight['freight']['monto']  }} {{ $freight['freight']['currency']  }}</td>
                        <td>{{  $freight['freight']['subtotal_local']  }} {{ $freight['freight']['currency']  }}</td>
                        <td>{{  $freight['freight']['markup']  }} {{ $freight['freight']['typemarkup']  }}</td>
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
                        <td>{{  $freightGlo['freight']['markup']  }} {{ $freightGlo['freight']['typemarkup']  }}</td>
                        <td>{{  $freightGlo['freight']['totalAmmount']  }} </td>
                      </tr>
                      @endforeach
                      @if( ($formulario->twuenty !="0") || ($formulario->forty !="0") || ($formulario->fortyhc!="0") )
                      <tr>
                        <td colspan="5"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td> <span  class="darkblue px12" > {{$arr->totalFreight  }} </span></td>
                      </tr>

                      @else
                      <tr>
                        <td colspan='6'>No data available</td>
                      </tr>

                      @endif

                    </table>
                  </td>
                </tr>
                @if((!$arr->globalDest->isEmpty() ) || (!$arr->localDest->isEmpty() ))
                <tr id="destination{{$loop->iteration}}" hidden="true" >
                  <td colspan="6">
                    <span class="darkblue cabezeras"> Destination Charges</span>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Charge</span></th>
                        <th><span class="portalphacode">Detail</span>  </th>
                        <th><span class="portalphacode">Units</span></th>
                        <th><span class="portalphacode">Price per Unit</span></th>
                        <th><span class="portalphacode">Ammount</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>

                      @foreach($arr->localDest as $destiny)
                      <tr>
                        <td>{{ $destiny['destiny']['surcharge_name'] }}</td>
                        <td>{{ $destiny['destiny']['calculation_name'] }} </td>
                        <td>{{  $destiny['destiny']['cantidad']  }}</td>
                        <td>{{ $destiny['destiny']['monto']  }} {{ @$destiny['destiny']['currency']  }}</td>
                        <td>{{  $destiny['destiny']['subtotal_local']  }} {{ $destiny['destiny']['currency']  }}</td>
                        <td>{{  $destiny['destiny']['markup']  }} {{ $destiny['destiny']['typemarkup']  }}</td>
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
                        <td>{{  $destinyGlo['destiny']['markup']  }} {{ $destinyGlo['destiny']['typemarkup']  }}</td>
                        <td>{{  $destinyGlo['destiny']['totalAmmount']  }} </td>
                      </tr>
                      @endforeach
                      <tr>
                        <td colspan="5"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td> <span  class="darkblue px12" > {{  $arr->totalDestiny  }}</span>  </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                @endif
                @if((!empty($inlandDestiny)) || (!empty($inlandOrigin)))
                <tr id="inlands{{$loop->iteration}}" hidden="true" >
                  <td colspan="6">
                    <b>Inlands Charges</b>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Provider</span></th>
                        <th><span class="portalphacode">Type</span></th>
                        <th><span class="portalphacode">Distance</span>  </th>
                        <th><span class="portalphacode">Port Name</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>
                      @if(!empty($inlandDestiny))
                      @foreach($inlandDestiny as $inlandDest)
                      @if($inlandDest['port_id'] == $arr->port_destiny->id )
                      <tr>
                        <th>{{ $inlandDest['provider'] }}</th>
                        <th>{{ $inlandDest['type'] }}</th>
                        <th>{{ $inlandDest['km'] }} KM</th>
                        <th>{{ $inlandDest['port_name'] }}</th>
                        <th>{{ $inlandDest['markup'] }} {{ $inlandDest['typemarkup'] }}</th>
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
                        <th>{{ $inlandOrig['markup'] }} {{ $inlandOrig['typemarkup'] }}</th>
                        <th>{{ $inlandOrig['monto'] }} {{  $inlandOrig['type_currency'] }}</th>
                      </tr>
                      @endif
                      @endforeach
                      @endif
                      <tr>
                        <td colspan="4"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td> <span  class="darkblue px12" > {{ $arr->totalInland }} {{ $arr->quoteCurrency }}</span>  </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                @endif
                @if(!$arr->schedulesFin->isEmpty())
                <tr id="schedules{{$loop->iteration}}" hidden="true"   >
                  <td colspan="6">
                    <span class="darkblue cabezeras"><b>Schedules</b></span>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Vessel</span></th>
                        <th><span class="portalphacode">ETD</span></th>
                        <th><span class="portalphacode"><center>Transit Time</center></span>  </th>
                        <th><span class="portalphacode">ETA</span></th>
                        <th><span class="portalphacode">-</span></th>         
                      </tr>                    


                      @foreach($arr->schedulesFin as $schedule)

                      <tr>
                        <td width='15%'>{{ $schedule['VesselName'] }}</td>
                        <td width='15%'>{{ $schedule['Etd'] }}</td>
                        <td width='45%'>
                          <div class="row">
                            <div class="col-md-4">
                              <span class="portcss"> {{$arr->port_origin->name  }}</span><br>            
                            </div>
                            <div class="col-md-4">
                              <center> {{ $schedule['days'] }} Days</center>
                              <div class="progress m-progress--sm">    
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                              <center> {{ $schedule['type'] }} </center>

                            </div>
                            <div class="col-md-4">
                              <span class="portcss"> {{$arr->port_destiny->name  }}</span><br>

                            </div>
                          </div>                        
                        </td>
                        <td width='15%'>{{ $schedule['Eta'] }}</td>
                        <td width='10%'>      
                          <label class="m-checkbox m-checkbox--state-brand">
                            <input name="schedules[]" type="checkbox" value="{{ json_encode($schedule) }}"> 
                            <span></span>
                          </label>
                        </td>

                      </tr>
                      @endforeach
                    </table>
                  </td>
                </tr>
                @endif
              </tbody>
            </table>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
    @endforeach
    @else

    <!--end::Portlet-->


    <div  class="row" style="margin-top:10%">
      <div class="col-xl-11" >
        <div class="m-portlet m-portlet m-portlet--head-solid-bg m-portlet--rounded">
          <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
              <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon">
                  <i class="flaticon-cogwheel-2"></i>
                </span>
                <h3 class="m-portlet__head-text m--font-brand">
                  NO RATES FOUND !
                </h3>
              </div>			
            </div>
            <div class="m-portlet__head-tools">
              <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover">
                  <a href="#" class="m-portlet__nav-link btn btn-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill   m-dropdown__toggle">
                    <i class="la la-ellipsis-v"></i>
                  </a>
                  <div class="m-dropdown__wrapper">
                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                    <div class="m-dropdown__inner">
                      <div class="m-dropdown__body">
                        <div class="m-dropdown__content">
                          <ul class="m-nav">
                            <li class="m-nav__section m-nav__section--first">
                              <span class="m-nav__section-text">
                                Quick Actions
                              </span>
                            </li>
                            <li class="m-nav__item">
                              <a href="{{route('contracts.index')}}"  class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-share"></i>
                                <span class="m-nav__link-text">
                                  View Rates
                                </span>
                              </a>
                            </li>
                            <li class="m-nav__item">
                              <a href="{{route('quotes.index')}}" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-share"></i>
                                <span class="m-nav__link-text">
                                 View Quotes
                                </span>
                              </a>
                            </li>
                            <li class="m-nav__item">
                              <a href="" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-share"></i>
                                <span class="m-nav__link-text">
                                  Generate Quote Automatic
                                </span>
                              </a>
                            </li>
                            </li>
                          <li class="m-nav__separator m-nav__separator--fit"></li>
                          <li class="m-nav__item">
                            <a href="#" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm">
                              Cancel
                            </a>
                          </li>
                          </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
        </div>
      </div>
      <div class="m-portlet__body">
        <div class="m-alert m-alert--icon m-alert--outline alert alert-danger" role="alert">
          <div class="m-alert__icon">
            <i class="la la-warning"></i>
          </div>
          <div class="m-alert__text">
            <strong>Well done!</strong> You successfully read this message.	
          </div>	
        </div>
      </div>
    </div>
  </div>
</div>

@endif
</div>
</div>
@endsection
@section('js')
@parent

<script src="/js/quote.js"></script>
<script src="/assets/plugins/datatable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/portlets/draggable.js" type="text/javascript"></script>
<script>
  $(document).ready( function () {
    $('#sample_editable').DataTable();
  } );
</script>
@stop


