@extends('layouts.app')
@section('title', 'Manager Carriers')
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
                        Manager Carriers
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

        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1 conten_load">
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <a href="#" onclick="showModal(1,'2')">

                                <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                    <span>
                                        <span>
                                            Add &nbsp; &nbsp;
                                        </span>
                                        <i class="la la-ship"></i>
                                    </span>
                                </button>
                            </a>
                        </div>

                        <br />
                        <table class="table m-table m-table--head-separator-primary"  id="carriertable" width="100%" style="width:100%">
                            <thead >
                                <tr>
                                    <th >ID</th>
                                    <th >Name</th>
                                    <th >Picture</th>
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

<div class="modal fade bd-example-modal-lg" id="modaleditCarrier"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Carriers
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div id="modal-body" class="modal-body">

            

        </div>
    </div>
</div>

@endsection

@section('js')
@parent

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script>

    function showModal(id,operation){

        if(operation == 1){
            var url = '{{ route("managercarriers.edit", ":id") }}';
            url = url.replace(':id', id);
            $('#modal-body').load(url,function(){
                $('#modaleditCarrier').modal();
            });
        } else if(operation == 2){
            var url = '{{route("managercarriers.show",":id")}}';
            url = url.replace(':id', id);
            $('#modal-body').load(url,function(){
                $('#modaleditCarrier').modal();
            });
        }
    }

    $(function() {
        $('#carriertable').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '{!! route("managercarriers.create")!!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'image', name: 'image' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "order": [[0, 'asc']],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "width": true,
            "info": true,
            "deferLoading": 57,
            "autoWidth": false,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true
        });

    });

</script>

@stop
