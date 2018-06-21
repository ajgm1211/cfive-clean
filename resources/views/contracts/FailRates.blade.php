@extends('layouts.app')
@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Rates Failed
                    </h3><br>

                </div>
            </div>
        </div>
        @if (count($errors) > 0)
        <div id="notificationError" class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(Session::has('message.nivel'))

        <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
            <div class="m-alert__icon">
                <i class="la la-warning"></i>
            </div>
            <div class="m-alert__text">
                <strong>
                    {{ session('message.title') }}
                </strong>
                {{ session('message.content') }}
            </div>
            <div class="m-alert__close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif

        <div class="m-portlet__body">
            <div class="m-portlet__head">
                <label>
                    <strong>
                        <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                        Failed fees: {{$countfailrates}}
                    </strong>
                </label>
                <br>
                <label>
                    <strong>
                        <i class="fa fa-dot-circle-o" style="color:green;"> </i>
                        Good Rates: {{$countrates}}
                    </strong>
                </label>
            </div>
            <table class="m-datatable "  id="html_table" >
                <thead >
                    <tr>
                        <th >
                            Status
                        </th>
                        <th >
                            Origin
                        </th>
                        <th >
                            Destiny
                        </th>

                        <th >
                            Carrier
                        </th>
                        <th >
                            20'
                        </th>
                        <th >
                            40'
                        </th>
                        <th >
                            40'Hc
                        </th>
                        <th >
                            Currency
                        </th>
                        <th >
                            Options
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @php
                    $i=1 
                    @endphp
                    @foreach($failrates as $ratef)
                    <tr class="" >
                        <td>
                            <i class="fa fa-dot-circle-o " style="color:red;" id=""></i>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <abbr style="{{$ratef['classorigin']}}">{{$ratef['origin_port']}}</abbr>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$ratef['classorigin']}}" name="origin_port" id="{{'origin'.$i}}" value="{{$ratef['origin_port']}}" class="form-control m-input">
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <abbr style="{{$ratef['classdestiny']}}">{{$ratef['destiny_port']}}</abbr>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$ratef['classdestiny']}}" name="destiny_port" id="{{'destination'.$i}}" value="{{$ratef['destiny_port']}}" class="form-control m-input">
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <abbr style="{{$ratef['classcarrier']}}">{{$ratef['carrier_id']}}</abbr>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$ratef['classcarrier']}}" name="carrier_id" id="{{'carrier'.$i}}" value="{{$ratef['carrier_id']}}" class="form-control m-input"> 
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <abbr style="{{$ratef['classtwuenty']}}">{{$ratef['twuenty']}}</abbr>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$ratef['classtwuenty']}}" name="twuenty" id="{{'twuenty'.$i}}" value="{{$ratef['twuenty']}}" class="form-control m-input"> 
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <abbr style="{{$ratef['classforty']}}">{{$ratef['forty']}}</abbr>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$ratef['classforty']}}" name="forty" id="{{'forty'.$i}}" value="{{$ratef['forty']}}" class="form-control m-input"> 
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <abbr style="{{$ratef['classfortyhc']}}">{{$ratef['fortyhc']}}</abbr>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$ratef['classfortyhc']}}" name="fortyhc" id="{{'fortyhc'.$i}}" value="{{$ratef['fortyhc']}}" class="form-control m-input"> 
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <abbr style="{{$ratef['classcurrency']}}">{{$ratef['currency_id']}}</abbr>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden="hidden">
                                <input type="text" style="{{$ratef['classcurrency']}}" name="currency" id="{{'currency'.$i}}" value="{{$ratef['currency_id']}}" class="form-control m-input"> 
                            </div>
                        </td>
                        <td>
                            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill {{'tdAB'.$i}}" onclick="showbox({{$i}})" title="Edit ">
                                <i class="la la-edit"></i>
                            </a>

                            <a href="#" hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="hidebox({{$i}})" >
                                <i class="la la-eraser"></i>
                            </a>

                        </td>

                    </tr>
                        
                    @php
                    $i++
                    @endphp
                    @endforeach

                    @foreach ($rates as $rate)
                    <tr class="m-table__row--active">
                        <td><i class="fa fa-dot-circle-o " style="color:green; "></i></td>
                        <td>{{$rate->origin_port}}</td>
                        <td>{{$rate->destiny_port}}</td>
                        <td>{{$rate->Carrier->name}}</td>
                        <td>{{$rate->twuenty}}</td>
                        <td>{{$rate->forty}}</td>
                        <td>{{$rate->fortyhc}}</td>
                        <td>{{$rate->Currency->alphacode}}</td>
                        <td>
                            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"   title="Edit ">
                                <i class="la la-edit"></i>
                            </a>

                            <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                                <i class="la la-eraser"></i>
                            </a>

                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">


                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-surcharge.js" type="text/javascript"></script>
<script>
    function showbox(id){
        $(".tdAB"+id).attr('hidden','hidden');
        $(".tdIn"+id).removeAttr('hidden');
    }
    
    function hidebox(id){
        $(".tdIn"+id).attr('hidden','hidden');
        $(".tdAB"+id).removeAttr('hidden');
    }
</script>

@stop
