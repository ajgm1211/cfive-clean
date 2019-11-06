@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<style>
    #tableRates_wrapper {
        overflow: auto;
    }
</style>
@endsection

@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        List of contracts
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
                                FCL Contracts
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link tabrates" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                                <i class="la la-cog"></i>
                                FCL Rates
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
                                    <a href="{{ route('contracts.add') }}">
                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    Add Contract
                                                </span>
                                                <i class="la la-plus"></i>
                                            </span>
                                        </button>
                                    </a>

                                    <a href="{{route('Request.importaion.fcl')}}">

                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    New Import Contract &nbsp;
                                                </span>
                                                <i class="la la-clipboard"></i>
                                            </span>
                                        </button>
                                    </a>
                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                        </div>
                        <table class="table tableData" id="tableContracts" width="100%">
                            <thead width="100%">
                                <tr >
                                    <th  width="27%" title="Field #1">
                                        Reference
                                    </th>
                                    <th width="12%" title="Field #2">
                                        Carriers
                                    </th>
                                    <th width="12%" title="Field #3">
                                        Direction
                                    </th>
                                    <th width="13%" title="Field #9">
                                        Validity
                                    </th>
                                    <th width="13%" title="Field #9">
                                        Expire
                                    </th>
                                    <th width="10%" title="Field #11">
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

                                <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                    <a href="{{ route('contracts.add') }}">
                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    Add Contract
                                                </span>
                                                <i class="la la-plus"></i>
                                            </span>
                                        </button>
                                    </a>

                                    <a href="{{route('Request.importaion.fcl')}}">

                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    New Import Contract &nbsp;
                                                </span>
                                                <i class="la la-clipboard"></i>
                                            </span>
                                        </button>
                                    </a>
                                    <button type="button" onclick="resetSelects()" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                        <span>
                                            <span>
                                                Reset Selects &nbsp;
                                            </span>
                                            <i class="la la-clipboard"></i>
                                        </span>
                                    </button>
                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                            <div class="row align-items-center" >
                                <div class="col-xl-12 order-2 order-xl-1" style="margin-top:30px;">
                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                    <div class="form-group m-form__group row align-items-center">

                                        <div class="col-lg-3">
                                            <label class=""><b>Origin</b></label>
                                            <div class="" id="carrierMul">
                                                {!! Form::select('origin',$values['origin'],null,['class'=>'m-select2-general form-control','id'=>'originS','required'])!!}
                                            </div>                                            
                                        </div>
                                        <div class="col-lg-3">
                                            <label class=""><b>Destination</b></label>
                                            <div class="" id="carrierMul">
                                                {!! Form::select('destination',$values['destination'],null,['class'=>'m-select2-general form-control','id'=>'destinationS','required'])!!}
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class=""><b>Carrier</b></label>
                                            <div class="" id="carrierMul">
                                                {!! Form::select('carrierM[]',$values['carrier'],null,['class'=>'m-select2-general form-control','id'=>'carrierM','required'])!!}
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class=""><b>Status</b></label>
                                            <div class="" id="carrierMul">
                                                {!! Form::select('destination',$values['status'],null,['class'=>'m-select2-general form-control','id'=>'statusS','required'])!!}
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <br>
                                            <button type="text" id="btnFiterSubmitSearch" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btn-block">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table tableData" id="tableRates" class="tableRates" width="100%">
                                <thead width="100%" class="tableRatesTH">
                                    <tr>
                                        <th style="width:25%;" title="Field #1">
                                            Reference
                                        </th>
                                        <th style="width:20%" title="Field #3">
                                            Carrier
                                        </th>
                                        <th style="width:20%" title="Field #4">
                                            Origin Port
                                        </th>
                                        <th style="width:20%" title="Field #5">
                                            Destination Port
                                        </th>
                                        <th title="Field #6" >
                                            20'
                                        </th>
                                        <th title="Field #7" >
                                            40'
                                        </th>
                                        <th title="Field #8" >
                                            40'HC
                                        </th>
                                        <th title="Field #9" >
                                            40'NOR
                                        </th>
                                        <th title="Field #10" >
                                            45'HC
                                        </th>
                                        <th title="Field #10">
                                            Currency
                                        </th>
                                        <th title="Field #9">
                                            Validity
                                        </th>
                                        <th style="width:10%">
                                            Status
                                        </th>
                                        <th style="width:15%">
                                            Schedule
                                        </th>
                                        <th style="width:5%">
                                            TT
                                        </th>
                                        <th style="width:5%">
                                            Via
                                        </th>
                                        <th  title="Field #12">
                                            Options
                                        </th>

                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>
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
    <div class="modal fade bd-example-modal-lg" id="global-modal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
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
                <div class="modal-body" id="global-body">
                    <div class="m-scrollable"  data-scrollbar-shown="true" data-scrollable="true" data-max-height="200">
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
<script src="/js/contracts.js"></script>
<script>


    $('.m-select2-general').select2({

    });


</script>
<script>

    function resetSelects(){
        $('#originS').val(null).trigger('change');
        $('#select2-originS-container').text('Select option');
        $('#destinationS').val(null).trigger('change');
        $('#select2-destinationS-container').text('Select option');
        $('#carrierM').val(null).trigger('change');
        $('#select2-carrierM-container').text('Select option');
        $('#statusS').val(null).trigger('change');
        $('#select2-statusS-container').text('Select option');
    }

    function AbrirModal(action,id){

        if(action == "DuplicatedContract"){
            var url = '{{ route("contract.duplicated", ":id") }}';
            url = url.replace(':id', id);
            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });
        }
    }

    $(document).on('click','.tabrates',function(e){
        //console.log($(window).width());
        //$('.tableRatesTH').css('width',$(window).width() );
    });

    $('#btnFiterSubmitSearch').click(function(){
        var carrier=$('#carrierM').val();
        var origin = $('#originS').val();
        var destination = $('#destinationS').val();
        var status = $('#statusS').val();

        if(carrier!='null' || origin!='null' || destination!='null' || status!='null'){
            var oTable = $('#tableRates').DataTable({
                searching: true,
                ordering: true,
                processing: false,
                serverSide: false,
                autoWidth: true,
                destroy: true,
                "columnDefs": [
                    { className: "truncate", "targets": [ 0,1] },
                    { "type": "num", targets: [4,5,6,7,8] }
                ],
                order: [[ 3, "asc" ],[ 4, "asc" ]],
                ajax: {
                    url: "{{ route('contract.table') }}",
                    data: {
                        "carrier":carrier,
                        "origin":origin,
                        "destination":destination,
                        "status":status,
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'carrier', name: 'carrier'},
                    {data: 'port_orig', name: 'port_orig'},
                    {data: 'port_dest', name: 'port_dest'},
                    {data: 'twuenty', name: 'twuenty'},
                    {data: 'forty', name: 'forty'},
                    {data: 'fortyhc', name: 'fortyhc'},
                    {data: 'fortynor', name: 'fortynor'},
                    {data: 'fortyfive', name: 'fortyfive'},
                    {data: 'currency', name: 'currency'},
                    {data: 'validity', name: 'validity'},
                    {data: 'status', name: 'status'},
                    {data: 'schedule_type', name: 'schedule_type'},
                    {data: 'transit_time', name: 'transit_time'},
                    {data: 'via', name: 'via'},
                    {data: 'options', name: 'options'}
                ],

            });
        }else{
            swal(
                'Warning!',
                'Select an option at least',
                'warning'
            )
        }
    });

    $(function() {
        $('#tableContracts').DataTable({
            ajax:  "{{ route('contract.tableG') }}",
            "columnDefs": [
                { className: "truncate", "targets": [ 0,1] }
            ],
            columns: [
                {data: 'name', name: 'name'},
                {data: 'carrier', name: 'carrier'},
                {data: 'direction', name: 'direction'},
                {data: 'validity', name: 'validity'},
                {data: 'expire', name: 'expire'},
                {data: 'status', name: 'status'},
                {data: 'options', name: 'options'}
            ],
            initComplete: function () {
                this.api().columns([0,1,2,3,4,5]).every(function () {
                    var column = this;
                    $('#tableContracts .head .head_hide').html('');

                    var select = $('<select id="formfilter" class="filterdropdown form-control"><option value="">' + $(column.header()).text() + '</option></select>')
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
            },
            "lengthChange": false,
            "stateSave": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": true,
            "deferLoading": 57,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true,
            "scrollX": true,

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