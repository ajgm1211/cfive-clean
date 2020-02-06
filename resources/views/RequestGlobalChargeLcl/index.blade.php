@extends('layouts.app')
@section('title', 'Requests GlobalCharge LCL')
@section('css')
@parent
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
                        Importation Request Global Charger LCL
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
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#RequestGC" role="tab">
                                <i class="la la-cog"></i>
                                Request G.C. LCL
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS " data-toggle="tab" href="#AccountGC" role="tab">
                                <i class="la la-briefcase"></i>
                                Account G.C. LCL
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="RequestGC" role="tabpanel">

                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">
                                    <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                        <a href="{{route('ImportationGlobalChargerLcl.index')}}">
                                            <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                                <span>
                                                    <span>
                                                        Import Globalchargers Lcl &nbsp;
                                                    </span>
                                                    <i class="la la-cloud-upload"></i>
                                                </span>
                                            </button>
                                        </a>
                                    </div>
                                    <br>
                                    <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="100%" style="width:100%">
                                        <thead >
                                            <tr>
                                                <th >ID</th>
                                                <th >Company</th>
                                                <th >Contract Name</th>
                                                <th >Contract Validation</th>
                                                <th >Date</th>
                                                <th >User</th>
                                                <th width="14%">Time elapsed</th>
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
                <div class="tab-pane " id="AccountGC" role="tabpanel">
                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">
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
                                                    Name
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


    @endsection

    @section('js')
    @parent

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<!--    <script type="application/x-javascript" src="/js/Globalchargers/Request.GlobalCLCL.Status.js"></script>-->
	<script type="application/x-javascript" src="/js/toarts-config.js"></script>
    <script>

		function downlodRequest(id){
			url='{!! route("RequestsGlobalchargersLcl.show",":id") !!}';
			url = url.replace(':id', id);
			$.ajax({
				url:url,
				method:'get',
				success: function(response){
					if(response.success == true){
						window.location = response.url;
					}else {
						toastr.error('File not found');
					}
					///console.log(response);
				}
			});
		}
		
        $(function() {
            $('#myatest').DataTable({
                processing: true,
                ajax: '{!! route("index.Account.import.gc.lcl") !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'requestgc_id', name: 'requestgc_id' },
                    { data: 'name', name: 'name' },
                    { data: 'date', name: 'date' },
                    { data: 'status', name: 'status' },
                    { data: 'company_user_id', name: "company_user_id" },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                "order": [[0, 'desc']],
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "stateSave": true,
                "deferLoading": 57,
                "stateSave": true,
                "autoWidth": true,
                "processing": true,
                "dom": 'Bfrtip',
                "paging": true,
                //"scrollX": true,
            });
        });

        $(function() {
            $('#requesttable').DataTable({
                processing: true,
                //serverSide: true,
                ajax: '{!! route("RequestsGlobalchargersLcl.create2") !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'Company', name: 'Company' },
                    { data: 'name', name: 'name' },
                    { data: 'validation', name: 'validation' },
                    { data: 'date', name: 'date' },
                    { data: 'user', name: 'user' },
                    { data: 'time_elapsed', name: 'time_elapsed' },
                    { data: 'username_load', name: 'username_load' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                "order": [[0, 'desc']],
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "width": true,
                "info": true,
                "deferLoading": 57,
                "stateSave": true,
                "autoWidth": false,
                "processing": true,
                "dom": 'Bfrtip',
                "paging": true
            });

        });

        function showModal(id){

            var url = '{{ route("show.status.Request.gc.lcl",":id") }}';
            url = url.replace(':id',id);
            $('#modal-body').load(url,function(){
                $('#changeStatus').modal();
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

                    url='{!! route("destroy.GlobalC.lcl",":id") !!}';
                    url = url.replace(':id', id);
                    // $(this).closest('tr').remove();
                    $.ajax({
                        url:url,
                        method:'get',
                        success: function(data){
                            if(data == 1){
                                swal(
                                    'Deleted!',
                                    'The Request has been deleted.',
                                    'success'
                                )
                                //$(elemento).closest('tr').remove();
                                $('#requesttable').DataTable().ajax.reload();
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
        $(document).on('click','.eliminaracount',function(e){
            var id = $(this).attr('data-id-acount');
            var elemento = $(this);
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this! ",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then(function(result){
                if (result.value) {
                    url='{!! route("delete.Accounts.Globalcharges.Lcl",[":id",2]) !!}';
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
                                        'The Account has been deleted.',
                                        'success'
                                    )
                                    //$(elemento).closest('tr').remove();
                                    $('#myatest').DataTable().ajax.reload();
                                }else if(data.success == 2){
                                    swal("Error!", "an internal error occurred!", "error");
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
                        'Your rate is safe :)',
                        'error'
                    )
                }
            });

        });
        
    </script>

    @stop
