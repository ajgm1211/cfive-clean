@extends('layouts.app')
@section('title', ' Manager - Surcharge Detail')
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
                        Surcharge Detail
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
                                Detail &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                                            <a href="#" onclick="AbrirModal('addsurcharge',0)" class="btn btn-primary form-control"> Add <i class="fa fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <br />
                                    <table class="table tableData "  id="masterSurcharge" width="100%" style="width:100%">
                                        <thead >
                                            <tr>
                                                <th >ID</th>
                                                <th >Name</th>
                                                <th >Carrier</th>
                                                <th >Type Destiny</th>
                                                <th >Calculation Type</th>
                                                <th >Destination</th>
                                                <th >Options</th>
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
<div class="modal fade bd-example-modal-lg" id="MasterS" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal-bodys">

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
    $('.m-select2-general').select2({});

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    function AbrirModal(action,id){

        if(action == "addsurcharge"){
            var url = '{{ route("MasterSurcharge.create")}}';
            $('#modal-bodys').load(url,function(){
                $('#MasterS').modal({show:true});
            });
        }if(action == "editMasterSurcharge"){
            var url = '{{ route("MasterSurcharge.edit",":id")}}';
            url = url.replace(':id',id);
            $('#modal-bodys').load(url,function(){
                $('#MasterS').modal({show:true});
            });
        }

        //$('#frmSurcharges').text('<input type="hidden" name="company_user_id" value="">');
    }

    $(function() {    
        $('#masterSurcharge').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url:'{!! route("MasterSurcharge.show","0") !!}',
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'carrier', name: 'carrier' },
                { data: 'typedestiny', name: 'typedestiny' },
                { data: 'calculationtype', name: 'calculationtype' },
                { data: 'direction', name: "direction" },
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

    });

    $(document).on('click','.eliminarMS',function(e){
        var id = $(this).attr('data-id-MS');
        var info = $(this).attr('data-info');
       
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

                url='{!! route("MasterSurcharge.destroy",":id") !!}';
                url = url.replace(':id', id);
                // $(this).closest('tr').remove();
                $.ajax({
                    url:url,
                    method:'DELETE',
                    success: function(data){
                        if(data.success == 1){
                            swal(
                                'Deleted!',
                                'The Surcharge Detail has been deleted.',
                                'success'
                            )
                            $('#masterSurcharge').DataTable().ajax.reload();
                        }else if(data.success == 2){
                            swal("Error!", "an internal error occurred!", "error");
                        }
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your Surcharge is safe :)',
                    'error'
                )
            }
        });

    });
</script>

@stop
