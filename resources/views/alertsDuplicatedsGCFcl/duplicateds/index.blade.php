@extends('layouts.app')
@section('title', 'Globalchargers Duplicateds - Ref')
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
                        Globalchargers Duplicateds - <strong style="color: #1638ef">{{$data['company_name']}}</strong> - Especific
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

                <div class="col-md-12">
                    <div class="form-group m-form__group row">
                        <div class="col-lg-1">
                            <label>
                                {!! Form::label('Surcharge', 'Surcharge',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('Surcharge', $data['surcharge']) !!}
                        </div>
                        <div class="col-lg-1">
                            <label>
                                {!! Form::label('currency', 'Currency',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('currency', $data['currency']) !!}
                        </div>
                        <div class="col-lg-5">
                            <label class="">
                                {!! Form::label('Origin', 'Origin',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('Origin',$data['origin']) !!}
                        </div>
                        <div class="col-lg-4">
                            <label>
                                {!! Form::label('Destiny', 'Destiny',['style' =>'color:#031B4E']) !!}
                            </label> <br>
                            {{ Form::label('destiny',$data['destiny']) }}
                        </div>
                        <div class="col-lg-1">
                            <a href="#" onclick="showModal({{$data['id']}})"  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Edit G.C. Duplicated">
                                <i style="color:#036aa0" class="la la-edit"></i>
                            </a>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <div class="col-lg-2">
                            <label>
                                {!! Form::label('Typedestiny', 'Type Destiny',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('typedestiny', $data['typedestiny']) !!}
                        </div>
                        <div class="col-lg-2">
                            <label>
                                {!! Form::label('calculationtype', 'Calculation Type',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('calculationtype', $data['calculationtype']) !!}
                        </div>
                        <div class="col-lg-1">
                            <label>
                                {!! Form::label('amount', 'Amount',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('amount', $data['amount']) !!}
                        </div>
                        <div class="col-lg-2">
                            <label>
                                {!! Form::label('validity', 'Validity',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('validity', $data['validity']) !!}
                        </div>

                        <div class="col-lg-2">
                            <label>
                                {!! Form::label('expire', 'Expire',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('expire', $data['expire']) !!}
                        </div>

                        <div class="col-lg-3">
                            <label>
                                {!! Form::label('carrier', 'Carrier',['style' =>'color:#031B4E']) !!}
                            </label><br>
                            {!! Form::label('carrier', $data['carrier']) !!}
                        </div>
                        
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table m-table m-table--head-separator-primary"  id="myatest" >
                        <thead >
                            <tr>
                                <th>Type</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Charge type</th>
                                <th>Calculation type</th>
                                <th>Currency</th>
                                <th>Carrier</th>
                                <th>Amount</th>
                                <th>Validity</th>
                                <th>Options</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="global-modal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Global Charges
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>
                    </div>
                    <div class="modal-body" id="global-body">
                        <div class="m-scrollable"  data-scrollbar-shown="true" data-scrollable="true" data-max-height="200">
                        </div>
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


    $('.m-select2-general').select2({

    });

    function showModal(id){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        var url = '{{ route("gc.duplicated.especific.show", [":global_id",$id]) }}';
        url = url.replace(':global_id', id);
        $('#global-body').load(url,function(){
            $('#global-modal').modal({show:true});
        });

    }


    $(function() {
        $('#myatest').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route("GlobalsDuplicatedEspecific.edit",$id) !!}',
            columns: [
                { data: 'surcharges', name: 'surcharges' },
                { data: 'origin', name: 'origin' },
                { data: 'destiny', name: 'destiny' },
                { data: 'typedestiny', name: 'typedestiny' },
                { data: 'calculationtype', name: 'calculationtype' },
                { data: 'currency', name: 'currency' },
                { data: 'carrier', name: 'carrier' },
                { data: 'ammount', name: 'ammount' },
                { data: 'validitylb', name: 'validitylb' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true
        });

    });

    
    function DestroyGroup(id){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

        url='{!! route("GlobalsDuplicatedEspecific.destroy",":id") !!}';
        url = url.replace(':id',id);

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
                $.ajax({
            url:url,
            method:'Delete',
            success: function(data){
                //alert(data.data + data.status);
                if(data.data == 1){
                    $('#myatest').DataTable().ajax.reload();
                    swal(
                        'Deleted!',
                        'Your GloblaCharger has been Deleted.',
                        'success'
                    );
                }else if(data.data == 2){
                    swal("Error!", "An internal error occurred!", "error");
                }
            }
        });                
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your GloblaCharger is safe :)',
                    'error'
                )
            }
        });

    }


</script>

@stop
