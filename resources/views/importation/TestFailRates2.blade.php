@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css">
@endsection
@section('title', 'Failed Rates FCL '.$contract['id'].' - '.$contract['number'].' / '.$contract['name'])
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Rates
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
                        @if($tab)
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#FailRates" role="tab">
                                <i class="la la-cog"></i>
                                Fail Rates 
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS " data-toggle="tab" href="#GoodRates" role="tab">
                                <i class="la la-briefcase"></i>
                                Good Rates
                            </a>
                        </li>
                        <!--
<li class="nav-item m-tabs__item">
<a class="nav-link m-tabs__link addS " data-toggle="tab" href="#editMultRates" role="tab">
<i class="la la-briefcase"></i>
Edit Fail Rates
</a>
</li>
-->
                        @else
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link " data-toggle="tab" href="#FailRates" role="tab">
                                <i class="la la-cog"></i>
                                Fail Rates 
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS active" data-toggle="tab" href="#GoodRates" role="tab">
                                <i class="la la-briefcase"></i>
                                Good Rates
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                @if($tab)
                <div class="tab-pane active" id="FailRates" role="tabpanel">
                    @else
                    <div class="tab-pane " id="FailRates" role="tabpanel">
                        @endif
                        <br>
                        <div class="m-portlet__head">
                            <div class="form-group row ">
                                <div class="col-lg-12">
                                    <label >
                                        <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                                        <strong >
                                            Rates Failed: 
                                        </strong>
                                        <strong id="strfail">{{$countfailrates}}</strong>
                                        <input type="hidden" value="{{$countfailrates}}" id="strfailinput" />
                                    </label>
                                    &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                                    <a href="{{route('Reprocesar.Rates',$id)}}" class="btn btn-primary">Reprocess &nbsp;<span class="la la-refresh"></span></a>
                                    &nbsp; &nbsp;
                                    <a href="#" onclick="showModalsavetorate({{$id}},'editMultRates')" class="btn btn-primary">Edit Multiple &nbsp;<span class="la la-edit"></span></a>
                                    &nbsp; &nbsp;
                                    <a href="#" onclick="showModalsavetorate({{$id}},'editRatesByDetalls')"  class="btn btn-primary">Edit Multiple By Detalls &nbsp;<span class="la la-edit"></span></a>
                                </div>
                            </div>
                            <br>

                        </div>
                        <a href="#" class="toggle-vis" data-column="1" >Origin</a>
                        <a href="#" class="toggle-vis" data-column="2">Destiny</a>
                        <div class="m-portlet__body">
                            <!--begin: tab body -->

                            <table class="table tableData" class="display" id="myatest" width="100%">
                                <thead width="100%">
                                    <tr>
                                        <th></th>
                                        <th>Origin</th>
                                        <th>Destiny</th>
                                        <th>Carrier</th>
                                        <th>20</th>
                                        <th>40</th>
                                        <th>40'HC</th>
                                        <th>40'NOR</th>
                                        <th>45'</th>
                                        <th>Currency</th>
                                        <th>Schedule Type</th>
                                        <th>Transit time</th>
                                        <th>Via</th>
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

                    <!-- /////////////////////////////////////////////////////////////////////////////////////////// -->
                    @if($tab)
                    <div class="tab-pane " id="GoodRates" role="tabpanel">
                        @else
                        <div class="tab-pane active" id="GoodRates" role="tabpanel">
                            @endif
                            <br>
                            <div class="m-portlet__head">
                                <label>
                                    <i class="fa fa-dot-circle-o" style="color:green;"> </i>
                                    <strong id="">
                                        Good Rates: 
                                    </strong>
                                    <strong id="strgood">
                                        {{$countrates}}
                                    </strong>
                                    <input type="hidden" value="{{$countrates}}" id="strgoodinput" />
                                </label>
                            </div>

                            <div class="m-portlet__body">
                                <!--begin: tab body -->

                                <table class="table tableData"  id="myatest2" width="100%">
                                    <thead width="100%">
                                        <tr>
                                            <th>Origin</th>
                                            <th>Destiny</th>
                                            <th>Carrier</th>
                                            <th>20</th>
                                            <th>40</th>
                                            <th>40'HC</th>
                                            <th>40'NOR</th>
                                            <th>45'</th>
                                            <th>Currency</th>
                                            <th>Schedule Type</th>
                                            <th>Transit time</th>
                                            <th>Via</th>
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
                    <div class="tab-pane ShEditMultRates" id="editMultRates" hidden role="tabpanel"></div>

                </div>
            </div>
            <input type="hidden" value="{{$id}}" id="idcontract" />
        </div>

        <!--  begin modal editar rate -->

        <div class="modal fade bd-example-modal-lg" id="modaleditRate"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Edit Rates
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
    </div>
</div>

<div id="body-div-submit" ></div>
<!--  end modal editar rate -->

@endsection
@section('js')
@parent


<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8"  src="js/Contracts/RatesAndFailForContract.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
<script>


    $('.m-select2-general').select2({

    });
    $(document).ready(function() {
        table = '';
//        var table = $('#myatest').DataTable( {
//            "paging": false
//        } );

        $('a.toggle-vis').on( 'click', function (e) {
            e.preventDefault();

            // Get the column API object
            var column = table.column( $(this).attr('data-column') );
            //console.log(column);
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );
    } );    


</script>
<script>
    $(function() {
        columnas = [{ data: null, render:function(){return "";}},
                    { data: 'origin_portLb', name: 'origin_portLb' },
                    { data: 'destiny_portLb', name: 'destiny_portLb' },
                    { data: 'carrierLb', name: 'carrierLb' },
                    { data: 'twuenty', name: 'twuenty' },
                    { data: 'forty', name: "forty" },
                    { data: 'fortyhc', name: "fortyhc" },
                    { data: 'fortynor', name: "fortynor" },
                    { data: 'fortyfive', name: "fortyfive" },
                    { data: 'currency_id', name: 'currency_id' },
                    { data: 'schedule_type', name: 'schedule_type' },
                    { data: 'transit_time', name: 'transit_time' },
                    { data: 'via', name: 'via' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }];
        table =  $('#myatest').DataTable({
            processing: true,
            //serverSide: true,
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
            ajax: '{!! route("Failed.Rates.Developer.view.For.Contracts",[$id,1]) !!}',
            columns: columnas,
            //"scrollX": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "stateSave": true,
            "deferLoading": 57,
            "autoWidth": true,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true
        });

        $('#myatest2').DataTable({
            processing: true,
            //serverSide: true,

            buttons: [],
            ajax: '{!! route("Failed.Rates.Developer.view.For.Contracts",[$id,2]) !!}',
            columns: [
                // { data: null, render:function(){return "";}},
                { data: 'origin_portLb', name: 'origin_portLb' },
                { data: 'destiny_portLb', name: 'destiny_portLb' },
                { data: 'carrierLb', name: 'carrierLb' },
                { data: 'twuenty', name: 'twuenty' },
                { data: 'forty', name: "forty" },
                { data: 'fortyhc', name: "fortyhc" },
                { data: 'fortynor', name: "fortynor" },
                { data: 'fortyfive', name: "fortyfive" },
                { data: 'currency_id', name: 'currency_id' },
                { data: 'schedule_type_id', name: 'schedule_type_id' },
                { data: 'transit_time', name: 'transit_time' },
                { data: 'via', name: 'via' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "deferLoading": 57,
            "autoWidth": true,
            "stateSave": true,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true,
            //"scrollX": true
        });
    });

    function showModalsavetorate(id,operation){
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        if(operation == 1){
            var url = '{{ route("Edit.Rates.Fail.For.Contracts", ":id") }}';
            url = url.replace(':id', id);
            $('#edit-modal-body').load(url,function(){
                $('#modaleditRate').modal();
            });
        }else if(operation == 2){
            var url = '{{ route("Edit.Rates.Good.For.Contracts", ":id") }}';
            url = url.replace(':id', id);
            $('#edit-modal-body').load(url,function(){
                $('#modaleditRate').modal();
            });
        } else  if(operation == 'editMultRates'){

            var idAr = [];
            var oTable = $("#myatest").dataTable();
            var length=table.rows('.selected').data().length;

            if(length > 0)
            {
                for (var i = 0; i < length; i++) { 
                    idAr.push(table.rows('.selected').data()[i].id);
                }
                //console.log();
                var url = "{{route('Edicion.Multiples.Rates.Fcl')}}";
                //url = url.replace(':id', idAr);
                data2 = {idAr:idAr,contract_id:id}
                $('#edit-modal-body').load(url,data2,function(){
                    $('#modaleditRate').modal({show:true});
                });

            } else {
                swal("Error!", "Please select at least one record", "error");
            }
        } else  if(operation == 'editRatesByDetalls'){
            var idAr = [];
            var oTable = $("#myatest").dataTable();
            var length=table.rows('.selected').data().length;

            if(length >= 2)
            {
                for (var i = 0; i < length; i++) { 
                    idAr.push(table.rows('.selected').data()[i].id);
                }
                //console.log();
                var url = "{{route('load.arr.Rates.por.detalles.Fcl')}}";
                //url = url.replace(':id', idAr);
                data2 = {idAr:idAr,contract_id:id}

                $('#body-div-submit').load(url,data2,function(response,status,jqxhr){
                    $('#frrRates').submit();
                });

            } else {
                swal("Error!", "Please select at least two record", "error");
            }
        }
    }

    $(document).on('click','#delete-FailRate',function(){
        var id = $(this).attr('data-id-failrate');
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

                url='{!! route("Destroy.RatesF.For.Contracts",":id") !!}';
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

    $(document).on('click','#delete-Rate',function(){
        var id = $(this).attr('data-id-rate');
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

                url='{!! route("Destroy.RatesG.For.Contracts",":id") !!}';
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
                            var b = $('#strgoodinput').val();
                            b--;
                            $('#strgood').text(b);
                            $('#strgoodinput').attr('value',b);
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
