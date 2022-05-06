@extends('layouts.app')
@section('title', 'Requests FCL')
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
                        Importation Request FCL
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
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#RequestCFCL" role="tab">
                                <i class="la la-cog"></i>
                                Request Contract FCL 
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS " data-toggle="tab" href="#AccountCFCL" role="tab">
                                <i class="la la-briefcase"></i>
                                Account Contract FCL
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="RequestCFCL" role="tabpanel">

                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">

                                    <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                        <a href="#">
                                            <button type="button" data-toggle="modal" data-target="#exportdata" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                                <span>
                                                    <span>
                                                        Export Request&nbsp;
                                                    </span>
                                                    <i class="la la-cloud-download"></i>
                                                </span>
                                            </button>
                                        </a>
                                    </div>
                                    <div class="col-xl-12 order-1 order-xl-2 m--align-right row">
                                        <div class="col-md-3">
                                            <input placeholder="Contract Validity" class="form-control m-input requestDate" readonly="" id="m_daterangepicker_1" required="required" name="between" type="text" value="{{$date}}">
                                        </div>
                                        <div class="col-lg-2">
                                            {!! Form::text('carrierM',null,['class'=>'form-control m-input','id'=>'carrierM','placeholder'=> 'Carrier','required'])!!}
                                        </div>
                                        <div class="col-md-2">

                                            <!--<button type="text" id="btnFiterSubmitSearch"  class="btn btn-primary form-control">Search</button>-->
                                            <a href="#" id="btnFiterSubmitSearch"  class="btn btn-primary form-control">Search</a>
                                        </div>
                                    </div>
                                    <br />
                                    <table class="table tableData" id="requesttable" width="100%" style="width:100%">
                                        <thead >
                                            <tr>
                                                <th >ID</th>
                                                <th >Company</th>
                                                <th >Equiment</th>
                                                <th >Reference</th>
                                                <th >Code</th>
                                                <th >Direction</th>
                                                <th >Carrier</th>
                                                <th >C. Validation</th>
                                                <th >Date</th>
                                                <th >User</th>
                                                <th width="8%">Time elapsed</th>
                                                <th >Username Load</th>
                                                <th >Status</th>
                                                <th >Options</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane " id="AccountCFCL" role="tabpanel">
                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">
                                    <div class="col-xl-12 order-1 order-xl-2 m--align-right row">
                                        <div class="col-md-3">
                                            <input placeholder="Account Date" class="form-control m-input accountDate" readonly="" id="m_daterangepicker_1" required="required" name="betweenAccount" type="text" value="{{$date}}">
                                        </div>
                                        <div class="col-md-2">
                                            <!--<button type="text" id="btnFiterSubmitSearch"  class="btn btn-primary form-control">Search</button>-->
                                            <a href="#" id="btnFiterSubmitSearchAcc"  class="btn btn-primary form-control">Search</a>
                                        </div>
                                    </div>
                                    <table class="table tableData"  id="myatest" width="100%">
                                        <thead width="100%">
                                            <tr>
                                                <th width="1%" >
                                                    Id
                                                </th>
                                                <th width="3%" >
                                                    Request Id
                                                </th>
                                                <th width="3%" >
                                                    Reference
                                                </th>
                                                <th width="5%" >
                                                    Date
                                                </th>
                                                <th width="5%" >
                                                    Status
                                                </th>
                                                <th width="4%" >
                                                    Company
                                                </th>
                                                <th width="5%" >
                                                    Options
                                                </th>
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

    <div class="modal fade bd-example-modal-lg" id="exportdata" role="dialog" aria-labelledby="exampleModalCenterTitle2" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle2">
                        Export - Request FCL
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div id="modal-bodyl" class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <h4 class="">Between:</h4>
                        </div>
                        <div class="col-md-5">
                            <input placeholder="Contract Validity" class="form-control m-input exportRqDate" readonly="" id="m_daterangepicker_1" required="required" name="between" type="text" value="Please enter validation date">
                        </div>
                        <div class="col-md-5">
                            <a href="#" onclick="exportjs()" class="btn btn-info">
                                Export Contract
                                <i class="fa flaticon-tool-1"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="changeStatus" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Status Of The Request
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div id="modal-body" class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="SaveStatusModal()">
                        Load
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade bd-example-modal-lg" id="contrac" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="modal-bodys">

            </div>
        </div>
    </div>

</div>
<div class="modal fade bd-example-modal-lg" id="global-modal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Contracts - Request
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


@endsection

@section('js')
@parent
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="application/x-javascript" src="/js/toarts-config.js"></script>

<script>
    $('.m-select2-general').select2({

    });

    function AbrirModal(action,id,request_id){
        action = $.trim(action);
        if(action == "DuplicatedContractOtherCompany"){
            var url = '{{ route("contract.duplicated.other.company",[":id","*id"]) }}';
            url = url.replace(':id', id);
            url = url.replace('*id', request_id);
            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });
        } else if(action == "showRequestDp"){
            var url = '{{ route("show.request.dp.cfcl",":id") }}';
            url = url.replace(':id', id);
            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });
        }
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#btnFiterSubmitSearch').click(function(){
        $('#requesttable').DataTable().draw(true);
    });

    $('#btnFiterSubmitSearchAcc').click(function(){
        $('#myatest').DataTable().draw(true);
    });


    //    $(document).ready(function(){
    //        $('#requesttable thead tr').clone(true).appendTo( '#requesttable thead' );
    //        $('#requesttable thead tr:eq(1) th').each( function (i) {
    //            // alert(i)
    //            var title = $(this).text();
    //            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    //
    //            $( 'input', this ).on( 'keyup change', function () {
    //                if ( requesttableV.column(i).search() !== this.value ) {
    //                    requesttableV
    //                        .column(i)
    //                        .search( this.value )
    //                        .draw();
    //                }
    //            } );
    //        } );
    //    });

    $('#carrierM' ).on( 'keyup change', function () {
        carriername = $(this).val();
        i = 6;
        if ( requesttableV.column(i).search() !== carriername ) {
            requesttableV
                .column(i)
                .search( this.value )
                .draw();
        }
    } );

    var requesttableV = '';
    var accounttableV = '';
    $(function() {    
        accounttableV = $('#myatest').DataTable({
            processing: true,
            ajax: {
                url:'{!! route("index.Account.import.fcl") !!}',
                data: function (d) {
                    var date = ($('.accountDate').val()).split(" / ");
                    d.dateS = $.trim(date[0]);
                    d.dateE = $.trim(date[1]);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'request_id', name: 'request_id' },
                { data: 'name', name: 'name' },
                { data: 'date', name: 'date' },
                { data: 'status', name: 'status' },
                { data: 'company_name', name: "company_name" },
                { data: 'action', name: 'action', orderable: false, searchable: false },
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

        requesttableV = $('#requesttable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:'{!! route("RequestFcl.create") !!}',
                data: function (d) {
                    //d.carrierM = $('select#carrierM').val();
                    var date = ($('.requestDate').val()).split(" / ");
                    d.dateS = $.trim(date[0]);
                    d.dateE = $.trim(date[1]);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'Company', name: 'Company' },
                { data: 'equiment', name: 'equiment' },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                { data: 'direction', name: 'direction' },
                { data: 'carrier', name: 'carrier' },
                { data: 'validation', name: 'validation' },
                { data: 'date', name: 'date' },
                { data: 'user', name: 'user' },
                { data: 'time_elapsed', name: 'time_elapsed' },
                { data: 'username_load', name: 'username_load' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
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
            var colSearch = state.columns[6].search;
            if (colSearch.search) {
                $('input', requesttableV.column(6).footer()).val(colSearch.search);
            }
            requesttableV.draw();
        }
    });

    //Para resetear otros campos o mostrar save state
    //    var state = table.state.loaded();
    //        if (state) {
    //            table.columns().eq(0).each(function (colIdx) {
    //                var colSearch = state.columns[colIdx].search;
    // 
    //                if (colSearch.search) {
    //                    $('input', table.column(colIdx).footer()).val(colSearch.search);
    //                }
    //            });
    // 
    //            table.draw();
    //        }

    $(document).ready(function(){
        setInterval(refreshDataTable, 300000);
    });
    
    function refreshDataTable(){
        requesttableV.draw();
        accounttableV.draw();
    }
    
    function showModal(id){
        /*var url = '{{ route("RequestFcl.show",":id") }}';
        url = url.replace(':id',id);
        $('#modal-body').load(url,function(){
            $('#changeStatus').modal();
        });*/
        alert('This functionality is disabled, Available in Barracuda.')
    }

    function editcontract(id){
        var url = '{{ route("show.contract.edit",":id") }}';
        url = url.replace(':id',id);
        $('#modal-bodys').load(url,function(){
            $('#contrac').modal();
        });
    }

    $(document).on('click','.eliminarrequest',function(e){
        var id = $(this).attr('data-id-request');
        var info = $(this).attr('data-info');
        var elemento = $(this);
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this! "+info,
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(function(result){
            if (result.value) {

                url='{!! route("RequestFcl.destroy",":id") !!}';
                url = url.replace(':id', id);
                // $(this).closest('tr').remove();
                $.ajax({
                    url:url,
                    method:'DELETE',
                    success: function(data){
                        if(data == 1){
                            swal(
                                'Deleted!',
                                'The Request has been deleted.',
                                'success'
                            )
                            $(elemento).closest('tr').remove();
                        }else if(data == 2){
                            swal("Error!", "an internal error occurred!", "error");
                        }
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your rate is safe :)',
                    'error'
                )
            }
        });

    });

    $(document).on('click','#delete-account-cfcl',function(){
        var id = $(this).attr('data-id-account-cfcl');
        var elemento = $(this);
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(function(result){
            if (result.value) {

                url='{!! route("Destroy.account.cfcl",":id") !!}';
                url = url.replace(':id', id);
                // $(this).closest('tr').remove();
                $.ajax({
                    url:url,
                    method:'get',
                    success: function(data){
                        if(data.jobAssociate == false){
                            if(data.success == 1){
                                swal(
                                    'Deleted!',
                                    'Your Account FCL has been deleted.',
                                    'success'
                                )
                                $(elemento).closest('tr').remove();
                                var a = $('#strfailinput').val();
                                a--;
                                $('#strfail').text(a);
                                $('#strfailinput').attr('value',a);
                                $('#requesttable').DataTable().ajax.reload();
                            }else if(data.success == 2){
                                swal("Error!", "An internal error occurred!", "error");
                            }
                        } else {                            
                            swal('Error!',
                                 'Your Acount cannot be deleted. It is being managed by Importation',
                                 'warning');
                        }
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your Account FCL is safe :)',
                    'error'
                )
            }
        });
    });

    function exportjs(){
        var url = '{!! route("export.request.fcl.v2") !!}';
        var between = $('.exportRqDate').val();
        //alert(date);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            cache: false,
            type:'POST',
            data:{between:between},
            url: url,
            /*beforeSend: function(){
                    $('#modalwait').modal('show');
                },*/
            success: function (response, textStatus, request) {
                $('#exportdata').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                if (request.status === 200) {
                    if(response.actt == 1){
                        console.log('Load: '+request.status);
                        var a = document.createElement("a");
                        a.href = response.file;
                        a.download = response.name;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        location.reload();
                    } else if(response.actt == 2){
                        swal(
                            'Done!',
                            'The export is being processed. We will send it to your email.',
                            'success'
                        )
                    }
                }
            },
            error: function (ajaxContext) {
                toastr.error('Export error: '+ajaxContext.responseText);
            }
        });
    }
</script>

@stop
