@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('title', 'Global Charges')
@section('content')



<div class="m-content">
    <div class="dropdown show" align="right" style="margin-bottom:20px;margin-right:20px;">
        <a class="dropdown-toggle" style="font-size:16px" href="#" role="button" id="helpOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            See how it works
        </a>

        <div 
            class="dropdown-menu" 
            aria-labelledby="helpOptions"
        >
            <a class="dropdown-item" target="_blank" href="https://support.cargofive.com/how-to-manage-local-charges/"> 
                How to manage Local Charges
            </a>
        </div>
    </div>
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Global Charges LCL
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
                                List Global Charge LCL
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
                                    <!-- <a href="{{route('RequestsGlobalchargersLcl.create')}}">

                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    Import Global Charges &nbsp;
                                                </span>
                                                <i class="la la-clipboard"></i>
                                            </span>
                                        </button>
                                    </a> -->
                                    <button type="button" name="bulk_delete" id="bulk_delete" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                        <span>
                                            <span>
                                                Delete Selected &nbsp;
                                            </span>
                                            <i class="la la-trash"></i>
                                        </span>
                                    </button>
                                </div>

                            </div>
                        </div>
                        <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="120%" style="width:120%">
                            <thead >
                                <tr>
                                    <th width="1%">
                                        Select
                                    </th>
                                    <th  width="8%">
                                        Type
                                    </th>
                                    <th width="9%">
                                        Origin Port
                                    </th>
                                    <th width="9%">
                                        Destination Port
                                    </th>
                                    <th width="9%">
                                        Charge Type
                                    </th>
                                    <th width="9%">
                                        Calculation type
                                    </th>
                                    <th width="9%">
                                        Currency
                                    </th>
                                    <th width="11%">
                                        Carrier
                                    </th>
                                    <th width="9%">
                                        Amount
                                    </th>
                                    <th  width="9%">
                                        Minimum
                                    </th>
                                    <th width="9%">
                                        Validity
                                    </th>
                                    <th width="8%">
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
<script>
    $(document).ready( function () {
        $('#global-table').DataTable();
    } );
</script>
<script>
    function AbrirModal(action,id){


        if(action == "editGlobalCharge"){
            var url = '{{ route("edit-global-charge-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#modalGlobalcharge').modal({show:true});
            });

        }
        if(action == "addGlobalCharge"){
            var url = '{{ route("add-global-charge-lcl")}}';

            $('.modal-body-add').load(url,function(){
                $('#modalGlobalchargeAdd').modal({show:true});
            });

        }
        if(action == "duplicateGlobalCharge"){

            var url = '{{ route("duplicate-global-charge-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-add').load(url,function(){
                $('#modalGlobalchargeAdd').modal({show:true});
            });
        }
    }

    $(function() {
        $('#requesttable').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '{!! route("globalchargeslcl.show",$company_userid) !!}',
            columns: [
                { data: 'checkbox', orderable:false, searchable:false},
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

        initComplete: function () {
                this.api().columns([1,2,3,4,5,6,7,8,9,10]).every(function () {
                    var column = this;
                    $('#requesttable .head .head_hide').html('');

                    var select = $('<select id="formfilter" class="filterdropdown form-control"><option value="">' + $(column.header()).text() + '</option></select>')
                    .prependTo($(column.header()).empty())
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
            "ordering": false,
            "info": true,
            "autoWidth": true, 
            "deferLoading": 57,   
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true,
            "scrollX": true,
            "stateSave": false, 
        });      
    });  
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
                $('.checkbox_global:checked').each(function(){
                    id.push($(this).val());
                });

                if(id.length > 0)
                {
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
                                    'Your GloblaChargers Lclc has been deleted.',
                                    'success'
                                );
                                $('#requesttable').DataTable().ajax.reload();
                            }else if(data == 2){
                                swal("Error!", "an internal error occurred!", "error");
                            }
                        }
                    });
                }
                else
                {
                    swal("Error!", "Please select atleast one checkbox", "error");
                }
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your GloblaChargers is safe :)',
                    'error'
                )
            }
        });
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
