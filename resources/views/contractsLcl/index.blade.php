@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection

@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        List Contracts
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
        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">

                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                <i class="la la-briefcase"></i>
                                LCL Contracts
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link tabrates" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                                <i class="la la-cog"></i>
                                LCL Rates
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane active " id="m_tabs_6_1" role="tabpanel">
                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-6 order-2 order-xl-1">
                                    <div class="form-group m-form__group row align-items-center">
                                        <div class="col-md-4">
                                        </div>
                                        <div class="col-md-4">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 order-1 order-xl-2 m--align-right">

                                    <a href="{{route('Request.importaion.lcl')}}">

                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    Import Contract&nbsp;
                                                </span>
                                                <i class="la la-clipboard"></i>
                                            </span>
                                        </button>
                                    </a>
                                    <!-- <a href="{{route('RequestImportationLcl.indexListClient')}}">

<button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
<span>
<span>
New \ Status Import  &nbsp;
</span>
<i class="la la-clipboard"></i>
</span>
</button>
</a>-->
                                    <div class="m-separator m-separator--dashed d-xl-none"></div> 
                                    <a href="{{ route('contractslcl.add') }}">
                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    Add Contract LCL
                                                </span>
                                                <i class="la la-plus"></i>
                                            </span>
                                        </button>
                                    </a>
                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                        </div>
                        <table class="table tableData text-center" id="tableContracts" width="100%">
                            <thead width="100%">
                                <tr >
                                    <th title="Field #1">
                                        Name
                                    </th>
                                    <th title="Field #2">
                                        Number
                                    </th>
                                    <th title="Field #9">
                                        Validity
                                    </th>
                                    <th title="Field #9">
                                        Expire
                                    </th>
                                    <th title="Field #11">
                                        Status
                                    </th>
                                    <th title="Field #12">
                                        Options
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="modal fade" id="m_select2_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            Contracts
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane " id="m_tabs_6_2" role="tabpanel">
                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-6 order-2 order-xl-1">
                                    <div class="form-group m-form__group row align-items-center">

                                        <div class="col-md-4">

                                            <div class="d-md-none m--margin-bottom-10"></div>
                                        </div>
                                        <div class="col-md-4">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                    <a href="{{ route('contractslcl.add') }}">
                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    Add Contract LCL
                                                </span>
                                                <i class="la la-plus"></i>
                                            </span>
                                        </button>
                                    </a>
                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                        </div><br><br>
                        <table class="table tableData text-center" id="tableRates" class="tableRates" width="100%">
                            <thead class="tableRatesTH">
                                <tr>
                                    <th title="Field #1">
                                        Name
                                    </th>
                                    <th title="Field #2">
                                        Number
                                    </th>
                                    <th title="Field #3">
                                        Carrier
                                    </th>
                                    <th title="Field #4">
                                        Origin Port
                                    </th>
                                    <th title="Field #5">
                                        Destination Port
                                    </th>
                                    <th title="Field #6" >
                                        W/M
                                    </th>
                                    <th title="Field #7" >
                                        Minimum
                                    </th>
                                    <th title="Field #10">
                                        Currency
                                    </th>
                                    <th title="Field #9">
                                        Validity
                                    </th>
                                    <th title="Field #11">
                                        Status
                                    </th>
                                    <th title="Field #12">
                                        Options
                                    </th>
                                </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>



                        <div class="modal fade" id="m_select2_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            Contracts
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">
                                                &times;
                                            </span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
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
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>
<script src="/js/contractsLcl.js"></script>
<script>         
    $(function() {
        $('#tableRates').DataTable({
            ordering: true,
            searching: true,
            processing: true,
            serverSide: true,
            order: [[ 3, "asc" ],[ 4, "asc" ]],
            ajax:  "{{ route('contractlcl.table') }}",
            "columnDefs": [
                { className: "truncate", "targets": [ 0,1] }
            ],
            columns: [

                {data: 'name', name: 'name'},
                {data: 'number', name: 'number'},
                {data: 'carrier', name: 'carrier'},
                {data: 'port_orig', name: 'port_orig'},
                {data: 'port_dest', name: 'port_dest'},
                {data: 'uom', name: 'uom'},
                {data: 'minimum', name: 'minimum'},
                {data: 'currency', name: 'currency'},
                {data: 'validity', name: 'validity'},
                {data: 'status', name: 'status'},
                {data: 'options', name: 'options'}
            ] ,
            "autoWidth": true,
            "overflow":false,
            //"scrollY": true,         
            "bPaginate": false,
            "bJQueryUI": true,
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ],
            initComplete: function () {
                this.api().columns(12).every(function () {
                    var column = this;
                    $('#tableContracts .head .head_hide').html('');

                    var select = $('<select id="formfilter" class="filterdropdown search2"><option value="">' + $(column.header()).text() + '</option></select>')
                    .prependTo($(column.header()).empty())
                    .on('change', function () {
                        var val = new Array();
                        //set val to current element in the dropdown.
                        val = $(this).val();

                        if (val.length > 1){

                            valString = val.toString();
                            valPiped =  valString.replace(/,/g,"|")

                            column
                                .search( valPiped ? '^'+valPiped+'$' : '', true, false ) //find this value in this column, if it matches, draw it into the table.
                                .draw();
                        } else if (val.length == 1) {
                            column
                                .search( val ? '^'+val+'$' : '', true, false ) //find this value in this column, if it matches, draw it into the table.
                                .draw();
                        } else {
                            column
                                .search('',true,false)
                                .draw();
                        }
                    });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
                $('.search2').select2();
            }
        });
        $('#tableContracts').DataTable({
            ajax:  "{{ route('contractlcl.tableG') }}",
            "columnDefs": [
                { className: "truncate", "targets": [ 0,1] }
            ],
            columns: [        
                {data: 'name', name: 'name'},
                {data: 'number', name: 'number'},
                {data: 'validity', name: 'validity'},
                {data: 'expire', name: 'expire'},
                {data: 'status', name: 'status'},
                {data: 'options', name: 'options'}
            ],
            initComplete: function () {
                this.api().columns([0,1,2,3,4]).every(function () {
                    var column = this;
                    $('#tableContracts .head .head_hide').html('');

                    var select = $('<select id="formfilter" class="filterdropdown form-control"><option value="">' + $(column.header()).text() + '</option></select><br>')
                    .prependTo($(column.header()))
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                    column.data().unique().sort().each(function (d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            },
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "deferLoading": 57,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true,
            "scrollX": true,
            "stateSave": true,

            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ]
        });
    });  
</script>
@stop




