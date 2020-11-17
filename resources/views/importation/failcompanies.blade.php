@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection
@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Companies
                    </h3><br>

                </div>
            </div>
        </div>

        @if (count($errors) > 0)
        <div id="notificationError" class="alert alert-danger">
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
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#FailRates" role="tab">
                                <i class="la la-cog"></i>
                                Fail Companies 
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="FailRates" role="tabpanel">

                        <br>
                        <div class="m-portlet__head">
                            <div class="form-group row ">
                                <div class="col-lg-12">
                                    <label >
                                        <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                                        <strong >
                                            Failed Companies: 
                                        </strong>
                                        <strong id="strfail">{{$countfailcompanies}}</strong>
                                        <input type="hidden" value="{{$countfailcompanies}}" id="strfailinput" />
                                    </label>
                                </div>
                            </div>
                            <br>

                        </div>

                        <div class="m-portlet__body">
                            <!--begin: tab body -->

                            <table class="table m-table m-table--head-separator-primary"  id="myatest" >
                                <thead >
                                    <tr>
                                        <th>Business Name</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Email</th>
                                        <th>Tax Number</th>
                                        <th>Company User</th>
                                        <th>Owner</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>

                            </table>

                            <!--end: tab body -->

                        </div>
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  begin modal editar rate -->

        <div class="modal fade bd-example-modal-lg" id="modaleditcompanies"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Edit Failed Companies
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>
                    </div>
                    <div id="edit-modal-body" class="modal-body">

                    </div>

                </div>
            </div>
        </div>

        <!--  end modal editar rate -->

        @endsection
        @section('js')
        @parent


        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
        <script>
            $(function() {
                $('#myatest').DataTable({
                    processing: true,
                    //serverSide: true,
                    ajax: '{!! route("list.fail.company",$companyuser) !!}',
                    columns: [
                        { data: 'businessname', name: 'businessname' },
                        { data: 'phone', name: 'phone' },
                        { data: 'address', name: 'address' },
                        { data: 'email', name: 'email' },
                        { data: 'taxnumber', name: "taxnumber" },
                        { data: 'compnyuser', name: "compnyuser" },
                        { data: 'owner', name: "owner" },
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
                    "paging": true,
                   "scrollX": true,
                });
            });

            function showModalcompany(id){
                    var url = '{{ route("show.fail.company", ":id") }}';
                    url = url.replace(':id', id);
                    $('#edit-modal-body').load(url,function(){
                        $('#modaleditcompanies').modal();
                    });
            }

            $(document).on('click','#delete-failcompany',function(){
                var id = $(this).attr('data-id-failcompany');
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

                        url='{!! route("delete.fail.company",":id") !!}';
                        url = url.replace(':id', id);
                        // $(this).closest('tr').remove();
                        $.ajax({
                            url:url,
                            method:'get',
                            success: function(data){
                                if(data == 1){
                                    swal(
                                        'Deleted!',
                                        'Your rate has been deleted.',
                                        'success'
                                    )
                                    $(elemento).closest('tr').remove();
                                    var a = $('#strfailinput').val();
                                    a--;
                                    $('#strfail').text(a);
                                    $('#strfailinput').attr('value',a);
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

        </script>

        @stop
