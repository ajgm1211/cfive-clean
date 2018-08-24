@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection
@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Rates And Surchargers
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

        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#FailRates" role="tab">
                                <i class="la la-cog"></i>
                                Fail Rates 
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS" data-toggle="tab" href="#GoodRates" role="tab">
                                <i class="la la-briefcase"></i>
                                Good Rates
                            </a>
                        </li>

                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="FailRates" role="tabpanel">
                    <br>
                    <div class="m-portlet__head">
                        <label >
                            <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                            <strong >
                                Rates Failed: 
                            </strong>
                            <strong id="strfail">{{$countfailrates}}</strong>
                            <input type="hidden" value="{{$countfailrates}}" id="strfailinput" />
                        </label>
                        <br>

                    </div>

                    <div class="m-portlet__body">
                        <!--begin: tab body -->

                        <table class="table m-table m-table--head-separator-primary"  id="myatest" >
                            <thead >
                                <tr>
                                    <th>origin</th>
                                    <th>destiny</th>
                                    <th>carrier</th>
                                    <th>20</th>
                                    <th>40</th>
                                    <th>40'hc</th>
                                    <th>currency</th>
                                    <th>option</th>
                                </tr>
                            </thead>

                        </table>

                        <!--end: tab body -->

                    </div>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center"></div>
                    </div>

                </div>

                <!-- /////////////////////////////////////////////////////////////////////////////////////////// -->

                <div class="tab-pane " id="GoodRates" role="tabpanel">
                    <br>
                    <div class="m-portlet__head">

                        <label>
                            <i class="fa fa-dot-circle-o" style="color:green;"> </i>
                            <strong id="">
                                Good Rates: 
                            </strong>
                            <strong id="strgood">
                                {{$countrates}}
                            </strong>
                            <input type="hidden" value="{{$countrates}}" id="strgoodinput" />
                        </label>
                    </div>

                    <div class="m-portlet__body">
                        <!--begin: tab body -->

                        <table class="table m-table m-table--head-separator-primary"  id="myatest2" >
                            <thead >
                                <tr>
                                    <th>origin</th>
                                    <th>destiny</th>
                                    <th>carrier</th>
                                    <th>20</th>
                                    <th>40</th>
                                    <th>40'hc</th>
                                    <th>currency</th>
                                    <th>option</th>
                                </tr>
                            </thead>

                        </table>

                        <!--end: tab body -->
                    </div>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <input type="hidden" value="{{$id}}" id="idcontract" />
</div>


@endsection
@section('js')
@parent


<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script>

    $(function() {
        $('#myatest').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '{!! route("Failed.Rates.Developer.view.For.Contracts",[$id,1]) !!}',
            columns: [
                { data: 'origin_portLb', name: 'origin_portLb' },
                { data: 'destiny_portLb', name: 'destiny_portLb' },
                { data: 'carrierLb', name: 'carrierLb' },
                { data: 'twuenty', name: 'twuenty' },
                { data: 'forty', name: "forty" },
                { data: 'fortyhc', name: "fortyhc" },
                { data: 'currency_id', name: 'currency_id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "deferLoading": 57,
            "autoWidth": false,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true
        });

        $('#myatest2').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '{!! route("Failed.Rates.Developer.view.For.Contracts",[$id,2]) !!}',
            columns: [
                { data: 'origin_portLb', name: 'origin_portLb' },
                { data: 'destiny_portLb', name: 'destiny_portLb' },
                { data: 'carrierLb', name: 'carrierLb' },
                { data: 'twuenty', name: 'twuenty' },
                { data: 'forty', name: "forty" },
                { data: 'fortyhc', name: "fortyhc" },
                { data: 'currency_id', name: 'currency_id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "deferLoading": 57,
            "autoWidth": false,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true
        });
    });



</script>

@stop
