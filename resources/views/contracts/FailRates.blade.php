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
                    </h3>
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
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">

                    <table class="m-datatable__table"  id="html_table2" width="100%" >
                        <thead>
                            <tr>
                                <th title="Field #1">
                                    Origin
                                </th>
                                <th title="Field #2">
                                    Destiny
                                </th>

                                <th title="Field #6">
                                    Carrier
                                </th>
                                <th title="Field #7">
                                    20'
                                </th>
                                <th title="Field #8">
                                    40'
                                </th>
                                <th title="Field #9">
                                    40'Hc
                                </th>
                                <th title="Field #10">
                                    Currency
                                </th>
                                <th title="Field #11">
                                    Options
                                </th>

                            </tr>

                        </thead>
                        <tbody>
                            @foreach ($failrates as $rate)
                            <tr>
                                <td>{{$rate->origin_port}}</td>
                                <td>{{$rate->destiny_port}}</td>
                                <td>{{$rate->carrier_id}}</td>
                                <td>{{$rate->twuenty}}</td>
                                <td>{{$rate->forty}}</td>
                                <td>{{$rate->fortyhc}}</td>
                                <td>{{$rate->currency_id}}</td>
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
                </div>
            </div>

        </div>
    </div>

    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Rates Good
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <table class="m-datatable"  id="html_table" >
                        <thead >
                            <tr>
                                <th title="Field #1">
                                    Origin
                                </th>
                                <th title="Field #2">
                                    Destiny
                                </th>

                                <th title="Field #3">
                                    Carrier
                                </th>
                                <th title="Field #4">
                                    20'
                                </th>
                                <th title="Field #5">
                                    40'
                                </th>
                                <th title="Field #6">
                                    40'Hc
                                </th>
                                <th title="Field #7">
                                    Currency
                                </th>
                                <th title="Field #8">
                                    Options
                                </th>
                                <th  title="Field #9">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rates as $rate)
                            <tr>
                                <td>{{$rate->origin_port}}</td>
                                <td>{{$rate->destiny_port}}</td>
                                <td>{{$rate->Carrier->name}}</td>
                                <td>{{$rate->twuenty}}</td>
                                <td>{{$rate->forty}}</td>
                                <td>{{$rate->fortyhc}}</td>
                                <td>{{$rate->Currency->name}}</td>
                                <td>
                                    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"   title="Edit ">
                                        <i class="la la-edit"></i>
                                    </a>

                                    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                                        <i class="la la-eraser"></i>
                                    </a>

                                </td>
                                <td>2</td>
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


    @stop
