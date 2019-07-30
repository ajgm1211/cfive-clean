@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endsection
@section('title', 'Global Charges LCL')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Global Charges LCL Administrator
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                <i class="la la-cog"></i>
                                Global Charge LCL List
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="m-portlet__body">
                <div class="tab-content">
                    <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">

                                <div class="new col-xl-12 order-1 order-xl-2 m--align-right">

                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                    <a  id="newmodal" class="">
                                        <button id="new" type="button"  onclick="AbrirModal('addGlobalCharge',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                    <button type="button" name="bulk_delete" id="bulk_delete" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                        <span>
                                            <span>
                                                Delete Selected &nbsp;
                                            </span>
                                            <i class="la la-trash"></i>
                                        </span>
                                    </button>
                                    <button type="button" name="bulk_delete" id="bulk_duplicate" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                        <span>
                                            <span>
                                                Duplicate Selected &nbsp;
                                            </span>
                                            <i class="la la-copy"></i>
                                        </span>
                                    </button>
                                </div>

                            </div>
                            <br>
                            <br>
                            <div class="row" style="margin-bottom:30px;">
                                <div class="col-md-3 order-1 order-xl-2 m--align-left">
                                    {!! Form::select('company_user',@$companies,null,['class'=>'m-select2-general form-control','id'=>'company_user','placeholder'=>'Select company'])!!}
                                </div>
                                <div class="col-md-3 order-1 order-xl-2 m--align-left">
                                    {!! Form::select('carrier',@$carriers,null,['class'=>'m-select2-general form-control','id'=>'carrier','placeholder'=>'Select company'])!!}
                                </div>
                                <div class="col-md-3 order-1 order-xl-2 m--align-left">
                                    <button id="search" class="btn btn-primary">Search</button>
                                    <a href="/globalchargeslcl/indexLclAdm" class="btn btn-primary">Reset</a>
                                </div>
                            </div>
                        </div>
                        <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Company</th>
                                    <th>Type</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th>Charge T.</th>
                                    <th>Calculation T.</th>
                                    <th>Currency</th>
                                    <th>Carrier</th>
                                    <th>Amount</th>
                                    <th>Minimum</th>
                                    <th>Validity</th>
                                    <th>Options</th>
                                </tr>
                            </thead>

                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="modalGlobalcharge"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Edit Global Charges
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div class="modal-body">

                </div>

            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="modalGlobalchargeAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Add Global Charges
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div class="modal-body-add">

                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
@parent



<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>

<script>
    $(document).ready( function () {
        $('#global-table').DataTable();
    } );
</script>
<script>
    function AbrirModal(action,id){
        if(action == "editGlobalCharge"){
            var url = '{{ route("gclcladm.show", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#modalGlobalcharge').modal({show:true});
            });

        }
        if(action == "addGlobalCharge"){
            var url = '{{ route("gclcladm.add")}}';

            $('.modal-body-add').load(url,function(){
                $('#modalGlobalchargeAdd').modal({show:true});
            });

        }
        if(action == "duplicateGlobalCharge"){
            var url = '{{ route("gclcladm.duplicate", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-add').load(url,function(){
                $('#modalGlobalchargeAdd').modal({show:true});
            });
        }
    }

    $(document).on('click', '#search', function(){
        var company_id=$('#company_user').val();
        var carrier=$('#carrier').val();

        table = $('#requesttable').DataTable({
            dom: 'Bfrtip',
            processing: true,
            destroy: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            } ],
            select: {
                style:    'multi',
                selector: 'td:first-child'
            },
            buttons: [
                {
                    text: 'Select all',
                    action : function(e) {
                        e.preventDefault();
                        table.rows({ page: 'all'}).nodes().each(function() {
                            $(this).removeClass('selected')
                        })
                        table.rows({ search: 'applied'}).nodes().each(function() {
                            $(this).addClass('selected');        
                        })
                    }
                },
                {
                    text: 'Select none',
                    action: function () {
                        table.rows().deselect();
                    }
                }
            ],
            //serverSide: true,
            //ajax: '/globalchargeslcl/createLclAdm/'+company_id,
            ajax: {
                processData: true,
                url: "{{ route('gclcladm.create') }}",
                data: {
                    "company_id":company_id,
                    "carrier":carrier,
                }
            },
            columns: [
                { data: null, render:function(){return "";}},
                { data: 'company_user', name: 'company_user' },
                { data: 'surchargelb', name: 'surchargelb' },
                { data: 'origin_portLb', name: 'origin_portLb' },
                { data: 'destiny_portLb', name: 'destiny_portLb' },
                { data: 'typedestinylb', name: 'typedestinylb' },
                { data: 'calculationtypelb', name: 'calculationtypelb' },
                { data: 'currencylb', name: 'currencylb' },
                { data: 'carrierlb', name: 'carrierlb' },
                { data: 'ammount', name: 'ammount' },
                { data: 'minimum', name: 'minimum' },
                { data: 'validitylb', name: 'validitylb' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "order": [[0, 'des']],
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
        table.clear();
    });

    $('#requesttable tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );

    $(document).on('click', '#bulk_delete', function(){
        var id = [];
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
                var oTableT = $("#requesttable").dataTable();
                var length=table.rows('.selected').data().length;

                if(length > 0)
                {
y
                    url='{!! route("globalchargeslcl.destroyArr",":id") !!}';
                    url = url.replace(':id', id);
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax({
                        url:url,
                        method:"post",
                        data:{id:id,_token:token},
                        success:function(data)
                        {
                            if(data.success == 1){
                                swal(
                                    'Deleted!',
                                    'Your GloblaCharger LCL has been deleted.',
                                    'success'
                                );
                                $('#requesttable').DataTable().ajax.reload();
                            }else if(data == 2){
                                swal("Error!", "An internal error occurred!", "error");
                            }
                        }
                    });
                }
                else
                {
                    swal("Error!", "Please select at least one record", "error");
                }
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your GloblaCharger is safe :)',
                    'error'
                )
            }
        });
    });

    $(document).on('click', '#bulk_duplicate', function(){
        //alert();
        var id = [];
        var oTable = $("#requesttable").dataTable(); 
        var length=table.rows('.selected').data().length;


        if(length > 0){
            for (var i = 0; i < length; i++) { 
                id.push(table.rows('.selected').data()[i].id);
            }
            url='{!! route("gclcladm.duplicate.Array",":id") !!}';
            url = url.replace(':id', id);
            var token = $("meta[name='csrf-token']").attr("content");
            url = url.replace(':id', id);
            $('.modal-body').load(url,{id:id,_token:token},function(){
                $('#modalGlobalcharge').modal({show:true});
            });
        } else {
            swal("Error!", "Please select at least one record", "error");
        }
    });
</script>
<script src="/js/globalchargeslcl.js"></script>
@if(session('globalchar'))
<script>
    swal(
        'Done!',
        'GlobalCharge updated.',
        'success'
    )
</script>
@endif

@stop
