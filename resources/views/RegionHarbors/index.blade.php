@extends('layouts.app')
@section('title', 'Regions/Harbor')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Manage Regions / Harbors
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
                    <a href="#" class="btn btn-primary " onclick="showModal(1,0)" > Add <span class="la la-plus"></span></a>
                    <div class="col-md-12">
                        <table class="table m-table m-table--head-separator-primary"  id="myatest" width="100%" style="width:100%">
                            <thead >
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Harbors</th>
                                    <th>Options</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>



        <div class="modal fade bd-example-modal-lg" id="addRegionModal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Regions
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

    /*    function agregarcampo(){
        var newtr = '<div class="col-lg-4 ">';
        newtr = newtr + '<label class="form-control-label">Variation:</label>';
        newtr = newtr + '<input type="text" name="variation[]" class="form-control" required="required">';
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
*/
    function showModal(selector,id){
        if(selector == 1){
            var url = '{{ route("add-regionP") }}';
            $('#modal-body').load(url,function(){
                $('#addRegionModal').modal();
            });
        } else if(selector == 2){
            var url = '{{ route("RegionP.show",":id") }}';
            url = url.replace(':id',id);
            $('#modal-body').load(url,function(){
                $('#addRegionModal').modal();
            });
        }
    }
    
    $(document).on('click','.BorrarRegion', function(e){
        var elemento = $(this);
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
                var token = $("meta[name='csrf-token']").attr("content");
                url='{!! route("RegionP.destroy",":id") !!}';
                url = url.replace(':id', id);
                // $(this).closest('tr').remove();
                $.ajax({
                    url:url,
                    method:'DELETE',
                    data:{"id":id,
                         "_token":token},
                    success: function(data){
                        if(data.success == 1){
                            swal(
                                'Deleted!',
                                'Your Region has been deleted.',
                                'success'
                            )
                            $(elemento).closest('tr').remove();

                        }else if(data == 2){
                            swal("Error!", "an internal error occurred!", "error");
                        }
                        //alert(data.success);
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your Harbor is safe :)',
                    'error'
                )
            }
        });
    });

    $(function() {
        $('#myatest').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '{!! route("RegionP.create") !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'harbors', name: 'harbors' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
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
