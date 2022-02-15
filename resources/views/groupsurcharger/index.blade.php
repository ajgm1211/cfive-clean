@extends('layouts.app')
@section('title', 'Group Surcharger')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endsection
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Manage Groups Surcharger
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
                    <div class="new col-xl-12 order-1 order-xl-2 m--align-right">
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 order-1 order-xl-2 m--align-right">
                        <a  id="newmodal" class="">
                            <a href="#" class="btn btn-primary col-md-1" onclick="showModal(1,0)" > Add <span class="la la-plus"></span></a>
                        </a>
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table m-table m-table--head-separator-primary"  id="myatest" >
                        <thead >
                            <tr>
                                <th style="width:3%">ID</th>
                                <th style="width:7%">Name</th>
                                <th style="width:7%">Varation</th>
                                <th style="width:5%">Options</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>



        <div class="modal fade bd-example-modal-lg" id="addModal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Groups Surchargers
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
        </div>

    </div>
</div>

@endsection

@section('js')
@parent

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script>

    function agregarcampo(){
        var newtr = '<div class="col-lg-4 ">';
        newtr = newtr + '<label class="form-control-label">Variation:</label>';
        newtr = newtr + '<input type="text" name="varation[]" class="form-control" required="required">';
        newtr = newtr + '<a href="#" class="borrado"><span class="la la-remove"></span></a>';
        newtr = newtr + '</div>';
        $('#variatiogroup').append(newtr);
    }

    $(document).on('click','.borrado', function(e){
        var elemento = $(this);
        $(elemento).closest('div').remove();
    });

    $('.m-select2-general').select2({

    });

    function showModal(selector,id){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        if(selector == 1){
            var url = '{{ route("group.surcharger.showAdd") }}';
            $('#modal-body').load(url,{},function(){
                $('#addModal').modal();
            });
        } else if(selector == 2){
            var url = '{{ route("gruopSurcharger.show",":id") }}';
            url = url.replace(':id',id);
            $('#modal-body').load(url,function(){
                $('#addModal').modal();
            });
        }
    }

    $(document).on('click','.BorrarHarbor', function(e){
        var elemento = $(this);
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        var id = $(elemento).attr('data-id-remove');
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

                url='{!! route("gruopSurcharger.destroy",":id") !!}';
                url = url.replace(':id', id);
                // $(this).closest('tr').remove();
                $.ajax({
                    url:url,
                    method:'DELETE',
                    success: function(data){
                        if(data == 1){
                            swal(
                                'Deleted!',
                                'Your rate has been deleted.',
                                'success'
                            );
                            $('#myatest').DataTable().ajax.reload();
                            //$(elemento).closest('tr').remove();

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

    $(function() {
        $('#myatest').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '{!! route("gruopSurcharger.create") !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'varation', name: "varation" },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
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
