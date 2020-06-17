@extends('layouts.app')
@section('title', 'Importation Transit Time')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('content')
<link rel="stylesheet" href="{{asset('css/loadviewipmort.css')}}">
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Importation Transit Time
                    </h3>
                </div>
            </div>
        </div>
        @if (count($errors) > 0)
        <div id="notificationError" class="alert alert-danger">
            <strong>Ocurrió un problema con tus datos de entrada</strong><br>
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
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#fails" role="tab">
                                <i class="la la-cog"></i>
                                Fails &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS " data-toggle="tab" href="#good" role="tab">
                                <i class="la la-briefcase"></i>
                                Good &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="fails" role="tabpanel">
                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">
                                    <div class="col-xl-12 order-1 order-xl-2 m--align-right row">
                                        <div class="col-md-2">
                                            <a href="{{route('ImpTransitTime.index')}}" class="btn btn-primary form-control">Importation</a>
                                        </div>
                                    </div>
                                    <br />
                                    <table class="table tableData "  id="tableFail" width="100%" style="width:100%">
                                        <thead >
                                            <tr>
                                                <th >ID</th>
                                                <th >Origin</th>
                                                <th >Destination</th>
                                                <th >Carrier</th>
                                                <th >Transit Time</th>
                                                <th >Destination Type</th>
                                                <th >Via</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="tab-pane " id="good" role="tabpanel">
                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">
                                    <div class="col-xl-12 order-1 order-xl-2 m--align-right row">
                                        <div class="col-md-2">
                                            <a href="{{route('ImpTransitTime.index')}}" class="btn btn-primary form-control">Importation</a>
                                        </div>
                                    </div>
                                    <table class="table tableData"  id="tableGood" width="100%">
                                        <thead width="100%">
                                            <tr>
                                                <th >ID</th>
                                                <th >Origin</th>
                                                <th >Destination</th>
                                                <th >Carrier</th>
                                                <th >Transit Time</th>
                                                <th >Destination Type</th>
                                                <th >Via</th>
                                            </tr>
                                        </thead>
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

@endsection

@section('js')
@parent
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="application/x-javascript" src="/js/toarts-config.js"></script>

<script>
    $('.m-select2-general').select2({

    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var requesttableV = '';
    $(function() {    
        $('#tableFail').DataTable({
            processing: true,
            ajax: {
                url:'{!! route("ImpTransitTime.show","1") !!}',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'origin', name: 'origin' },
                { data: 'destiny', name: 'destiny' },
                { data: 'carrier', name: 'carrier' },
                { data: 'transit_time', name: 'transit_time' },
                { data: 'destination_type', name: "destination_type" },
                { data: 'via', name: "via" },
                //{ data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "order": [[0, 'desc']],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "width": true,
            "autoWidth": false,
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "paging": true
            //"scrollX": true,
        });

        requesttableV = $('#tableGood').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:'{!! route("ImpTransitTime.show","0") !!}',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'origin', name: 'origin' },
                { data: 'destiny', name: 'destiny' },
                { data: 'carrier', name: 'carrier' },
                { data: 'transit_time', name: 'transit_time' },
                { data: 'destination_type', name: "destination_type" },
                { data: 'via', name: "via" },
                //{ data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            orderCellsTop: true,
            fixedHeader: true,
            "order": [[0, 'des']],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "width": true,
            "autoWidth": false,
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "paging": true
        });

        var state = requesttableV.state.loaded();
        if(state) {
            var colSearch = state.columns[5].search;
            if (colSearch.search) {
                $('input', requesttableV.column(5).footer()).val(colSearch.search);
            }
            requesttableV.draw();
        }
    });

</script>

@stop
