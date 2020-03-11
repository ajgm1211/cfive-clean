@extends('layouts.app')
@section('title', 'Containers Calculation Type')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('content')
<link rel="stylesheet" href="{{asset('css/loadviewipmort.css')}}">
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
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
                            <a class="nav-link m-tabs__link addS " data-toggle="tab" href="#conatinersCT" role="tab">
                                <i class="la la-briefcase"></i>
                                Containers Calculation Types
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="conatinersCT" role="tabpanel">
                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">

                                    <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                        <a href="#" onclick="showModal('add',0)">
                                            <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                                <span>
                                                    <span>
                                                        Add&nbsp;
                                                    </span>
                                                    <i class="la la-plus"></i>
                                                </span>
                                            </button>
                                        </a>

                                    </div>
                                </div>
                            </div>
                            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                                <div class="row align-items-center">
                                    <div class="col-xl-12 order-2 order-xl-1 conten_load">
                                        <table class="table tableData"  id="containersTable" width="100%">
                                            <thead width="100%">
                                                <tr>
                                                    <th width="5%" >
                                                        Id
                                                    </th>
                                                    <th width="15%" >
                                                        Container
                                                    </th>
                                                    <th width="15%" >
                                                        Calculation Type
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
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="changeStatus" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="modal-content">

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

    function showModal(action,id){
        action = $.trim(action);
        if(action == "add"){
            var url = '{{ route("add.conatiner.calculation") }}';
            $('#modal-content').load(url,function(){
                $('#changeStatus').modal({show:true});
            });
        } else if(action == "update"){
            var url = '{{ route("ContainerCalculation.edit",":id") }}';
            url = url.replace(':id', id);
            $('#modal-content').load(url,function(){
                $('#changeStatus').modal({show:true});
            });
        }
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    var databableCC = $('#containersTable').DataTable({
        processing: true,
        ajax: '{!! route("ContainerCalculation.create") !!}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'container', name: 'container' },
            { data: 'calculationtype', name: 'calculationtype' },
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


    $(document).on('click','.eliminarconatinerCalculation',function(){
        var id = $(this).attr('data-id-conatiner-calculation');
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
                url='{!! route("ContainerCalculation.destroy",":id") !!}';
                url = url.replace(':id', id);
                // $(this).closest('tr').remove();
                $.ajax({
                    url:url,
                    method:'DELETE',
                    success: function(data){
                        //console.log(data);
                        if(data.success == true){
                            swal(
                                'Deleted!',
                                'Your ralation has been deleted.',
                                'success'
                            );
                            databableCC.ajax.reload();
                        }else if(data.success == false){
                            swal("Error!", "An internal error occurred!", "error");
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

</script>

@stop
