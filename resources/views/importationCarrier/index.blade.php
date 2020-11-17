@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">

@endsection
@section('title', 'Importation Carrier.')
@section('content')

<div class="m-content">
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
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Importation Carrier
                    </h3>
                </div>
            </div>
        </div>


        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="new col-xl-12 order-1 order-xl-2 m--align-right">

                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 order-1 order-xl-2 m--align-right">
                        <a  id="newmodal" class="">
                            <button id="new" type="button"  onclick="AbrirModal('addCarriers',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                Add New &nbsp;
                                <i class="fa fa-plus"></i>
                            </button>
                            <a href="{{route('surcherger.filtro.index')}}" id="new"  onclick="AbrirModal('addCarriers',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                Surchargers Filters &nbsp;
                                <i class="fa fa-plus"></i>
                            </a>
                            <a href="#" id="new"  onclick="AbrirModal('forwardRequest',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                Forward Request &nbsp;
                                <i class="fa fa-refresh"></i>
                            </a>
                        </a>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table m-table m-table--head-separator-primary" id="importationcompany" width="100%" style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Carriers</th>
                                <th>Status</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="company-imp-modal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Importation Carrier 
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div class="modal-body" id="company-imp-body">
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

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>

<script src=""></script>
<script>

    function AbrirModal(action,id){
        if(action == "carrier-import-edit"){
            var url = '{{ route("CarrierImportation.edit", ":id") }}';
            url = url.replace(':id', id);
            $('#company-imp-body').load(url,function(){
                $('#company-imp-modal').modal({show:true});
            });
        }
        if(action == "addCarriers"){
            var url = '{{ route("CarrierImportation.add")}}';
            $('#company-imp-body').load(url,function(){
                $('#company-imp-modal').modal({show:true});
            });
        }
        if(action == "forwardRequest"){
            var url = '{{ route("forward.modal.show")}}';
            $('#company-imp-body').load(url,function(){
                $('#company-imp-modal').modal({show:true});
            });
        }
    }

    $(function() {
        $('#importationcompany').DataTable({
            //serverSide: true,
            ajax: '{!! route("CarrierImportation.create") !!}',
            columns: [
                { data: 'id', name: 'id'},
                { data: 'name', name: 'name' },
                { data: 'carriers', name: 'carriers' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            "order": [[0, 'ASC']],
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
    });


    $(document).on('click', '#delete-company', function(){
        var id = $(this).attr('data-delete');
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
                url='{!! route("CarrierImportation.destroy",":id") !!}';
                url = url.replace(':id', id);
                var token = $("meta[name='csrf-token']").attr("content");
                $.ajax({
                    url:url,
                    method:"DELETE",
                    data:{id:id,_token:token},
                    success:function(data)
                    {
                        if(data.success == 1){
                            swal(
                                'Deleted!',
                                'Your Company has been deleted.',
                                'success'
                            );
                            $('#importationcompany').DataTable().ajax.reload();
                        }else if(data == 2){
                            swal("Error!", "an internal error occurred!", "error");
                        }
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your Company is safe :)',
                    'error'
                )
            }
        });
    });
    


</script>

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
