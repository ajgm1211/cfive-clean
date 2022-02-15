@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<style>
  .btn-save__modal {
    padding: 15px 35px !important;
    border-radius: 50px;
    background-color: #36a3f7 !important;
    border-color: #36a3f7 !important;
    font-size: 18px;
  }
  .icon__modal {
    margin-right: 10px;
  }
</style>
@endsection

@section('title', 'Contracts')
@section('content')
@php
$validation_expire = $contracts->validity ." / ". $contracts->expire ;
@endphp
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

    <!--Begin::Main Portlet-->
    <div class="m-portlet m-portlet--full-height">
        <!--begin: Portlet Head-->
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Contract 
                        <small>
                            new registration
                        </small>
                    </h3>
                </div>
            </div>


            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="#" data-toggle="m-tooltip" class="m-portlet__nav-link m-portlet__nav-link--icon" data-direction="left" data-width="auto" title="Get help with filling up this form">
                            <i class="flaticon-info m--icon-font-size-lg3"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">


            {!! Form::model($contracts, ['route' => ['contractslcl.update', $contracts], 'method' => 'PUT','class' => 'form-group m-form__group']) !!}
            @include('contracts.partials.form_contractsT')
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link  {{ session('activeRLcl')}}" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                    <i class="la la-cog"></i>
                                    Ocean Freight
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link {{ session('activeSLcl')}} " data-toggle="tab" href="#m_tabs_6_2" role="tab">
                                    <i class="la la-briefcase"></i>
                                    Surcharges
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_3" role="tab">
                                    <i class="la la-bell-o"></i>
                                    Restrictions
                                </a>
                            </li>

                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_4" role="tab">
                                    <i class="la la-comments"></i>
                                    Remarks
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane {{ session('activeRLcl')}} " id="m_tabs_6_1" role="tabpanel">

                            <a  id="newRate" class="">

                                <button type="button" onclick="AbrirModal('addRate',{{ $contracts->id }})" class="btn btn-brand">
                                    Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </a>
                            <!--
@role('administrator')
<button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadfile">
Upload Rates
<i class="fa flaticon-tool-1"></i>
</button>-->
                            <a href="{{route('Failed.Rates.lcl.view',[$id,1])}}" class="btn btn-info">
                                Failed Rates Lcl
                                <i class="fa flaticon-tool-1"></i>
                            </a>
                            <!--<a href="{{route('Exportation.show',$id)}}" class="btn btn-info">
Export Contract
<i class="fa flaticon-tool-1"></i>
</a>
@endrole
-->
                            <br><br>
                            <table  class="table tableData" id="rateTable" width="100%">
                                <thead>
                                    <tr>
                                        <th title="Field #1">
                                            Origin Port
                                        </th>
                                        <th title="Field #2">
                                            Destination Port    
                                        </th>
                                        <th title="Field #3">
                                            Carrier
                                        </th>
                                        <th title="Field #4">
                                            W/M
                                        </th>
                                        <th title="Field #5">
                                            Minimum
                                        </th>

                                        <th title="Field #7">
                                            Currency
                                        </th>
                                        <th style="width:13%">
                                            Schedule
                                        </th>
                                        <th style="width:7%">
                                            Transit Time
                                        </th>
                                        <th style="width:10%">
                                            Via
                                        </th>
                                        <th title="Field #7">
                                            Options
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane {{ session('activeSLcl')}} " id="m_tabs_6_2" role="tabpanel">
                            <a  id="newChar" class="">

                                <button type="button"  onclick="AbrirModal('addLocalCharge',{{ $contracts->id }})"  class="btn btn-brand">
                                    Add New
                                    <i class="fa fa-plus"></i>
                                </button><br><br>
                            </a>
                            <!--
@role('administrator')
<a>
<button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadfileSubcharge">
Upload Surcharge
<i class="fa flaticon-tool-1"></i>
</button>
</a>

<a href="{{route('Failed.Rates.lcl.view',[$id,1])}}" class="btn btn-info">
Failed Rates Lcl
<i class="fa flaticon-tool-1"></i>
</a>
<br><br><br>
@endrole
-->
                            <table class="table tableData" id="users-table" width="100%" >

                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Origin Port</th>
                                        <th>Destination Port</th>
                                        <th>Change Type</th>
                                        <th>Carrier</th>
                                        <th>Calculation Type</th>
                                        <th>Amount</th>
                                        <th>Minimum</th>
                                        <th>Currency</th>
                                        <th>Options</th>

                                    </tr>
                                </thead>
                            </table>
                            <table hidden="true">
                                <tr   id='tclone2' hidden="true"  >
                                    <td>
                                        {{ Form::select('type[]', $surcharge,null,['class'=>'form-control','style' => 'width:100%;']) }}
                                    </td>
                                    <td>{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control portOrig','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                                    <td>{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control portDest','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                                    <td>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                                    <td>{{ Form::select(null, $carrier,null,['class'=>'custom-select form-control carrier','multiple' => 'multiple','style' => 'width:100%;']) }}</td>

                                    <td>  {{ Form::select('calculationtype[]', $calculationT,null,['class'=>'custom-select form-control ','style' => 'width:100%;']) }}</td>
                                    <td> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}</td>
                                    <td> {!! Form::text('minimumL[]', null, ['placeholder' => 'Please enter minimum','class' => 'form-control m-input']) !!}</td>
                                    <td>{{ Form::select('localcurrency_id[]', $currency,$currency_cfg->id,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                                    <td>  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                                        <i class="la la-eraser"></i>
                                        </a>
                                    </td>
                                </tr>

                            </table>

                            <table>
                                <tr   id='tclone' hidden="true" >
                                    <td>{{ Form::select('origin_id[]', $harbor,null,['class'=>'custom-select form-control']) }}</td>
                                    <td>{{ Form::select('destiny_id[]', $harbor,null,['class'=>'custom-select form-control']) }}</td>
                                    <td>{{ Form::select('carrier_id[]', $carrier,null,['class'=>'custom-select form-control']) }}</td>

                                    <td>{!! Form::text('twuenty[]', null, ['placeholder' => 'Please enter the 20','class' => 'form-control m-input' ]) !!} </td>
                                    <td>{!! Form::text('forty[]', null, ['placeholder' => 'Please enter the 40','class' => 'form-control m-input']) !!} </td>
                                    <td> {!! Form::text('fortyhc[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}</td>
                                    <td>{{ Form::select('currency_id[]', $currency,$currency_cfg->id,['class'=>'custom-select form-control']) }}</td>
                                    <td>  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete"  >
                                        <i class="la la-eraser" ></i>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="m_tabs_6_3" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12" id="origin_harbor_label">
                                    <label>Company</label>
                                    <div class="form-group m-form__group align-items-center">
                                        {{ Form::select('companies[]',$companies,@$company,['multiple','class'=>'m-select2-general','id' => 'm-select2-company']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="origin_harbor_label">
                                    <label>Users</label>
                                    <div class="form-group m-form__group align-items-center">
                                        {{ Form::select('users[]',$users,@$user,['multiple','class'=>'m-select2-general','id' => 'm-select2-client']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="m_tabs_6_4" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12" id="comments">

                                    <div class="form-group m-form__group align-items-center">
                                        {{ Form::textarea('comments',null,['class'=>'form-control','rows'=>'5']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m-portlet__foot--fit">
                        <div class="m-form__actions m-form__actions">
                            {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                            <a class="btn btn-danger" href="{{url()->previous()}}">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <!--end: Form Wizard-->
    </div>
    <!--End::Main Portlet-->

    <div class="modal fade bd-example-modal-lg" id="modalLocalchargeAdd"   role="dialog" aria-labelledby="exampleModalCenterTitleAdd" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitleAdd">
                        Add Local Charges
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


    <div class="modal fade bd-example-modal-lg" id="modalLocalcharge"   role="dialog" aria-labelledby="exampleModalLongTitle2" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle2">
                        Update Local Charges
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div class="modal-body-edit">
                    <center>
                        <div id="spinner" style="display:none">
                            <img src="/images/ship.gif" alt="Loading" />
                        </div>
                    </center>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg"  id="modalRates"  role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Ocean Freight LCL
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div id = 'rate-body'>

                </div>

            </div>
        </div>
    </div>



</div>

</div>
@endsection
@section('js')
@parent


<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/contractsLcl.js"></script>
<script src="{{asset('js/Contracts/ImporContractFcl.js')}}"></script>
<script>                 
    $(function() {

        $('#users-table').DataTable({
            ordering: true,
            searching: true,
            processing: true,
            serverSide: true,
            order: [[ 1, "asc" ],[ 2, "asc" ]],
            ajax:  "{{ route('localcharlcl.table',['id' => $id]) }}",
            columns: [
                {data: 'surcharge', name: 'surcharge'},
                {data: 'origin', name: 'origin'},
                {data: 'destiny', name: 'destiny'},
                {data: 'changetype', name: 'changetype'},
                {data: 'carrier', name: 'carrier'},
                {data: 'calculation_type', name: 'calculation_type'},
                {data: 'ammount', name: 'ammount'},
                {data: 'minimum', name: 'minimum'},
                {data: 'currency', name: 'currency'},
                {data: 'options', name: 'options'}
            ],

        });

        $('#rateTable').DataTable({
            ordering: true,
            searching: true,
            processing: true,
            serverSide: true,
            order: [[ 0, "asc" ],[ 1, "asc" ]],

            ajax:  "{{ route('ratelcl.table',['id' => $id]) }}",
            columns: [
                {data: 'port_orig', name: 'port_orig'},
                {data: 'port_dest', name: 'port_dest'},
                {data: 'carrier', name: 'carrier'},
                {data: 'uom', name: 'uom'},
                {data: 'minimum', name: 'minimum'},
                {data: 'currency', name: 'currency'},
                {data: 'schedule_type', name: 'schedule_type'},
                {data: 'transit_time', name: 'transit_time'},
                {data: 'via', name: 'via'},
                {data: 'options', name: 'options'}
            ],

            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ]


        });


    });  
</script>

<script>
    function AbrirModal(action,id){

        if(action == "editRate"){
            var url = '{{ route("edit-rates-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('#rate-body').load(url,function(){
                $('#modalRates').modal({show:true});
            });

        }
        if(action == "addRate"){
            var url = '{{ route("add-rates-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('#rate-body').load(url,function(){
                $('#modalRates').modal({show:true});
            });

        }
        if(action == "duplicateRate"){
            var url = '{{ route("duplicate-rates-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('#rate-body').load(url,function(){
                $('#modalRates').modal({show:true});
            });

        }

        if(action == "editLocalCharge"){
            $('#spinner').show();
            $('#modalLocalcharge').modal({show:true});
            var url = '{{ route("edit-local-charge-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-edit').load(url,function(){
                $('#modalLocalcharge').modal({show:true});
                $('#spinner').hide();
            });

        }
        if(action == "addLocalCharge"){
            var url = '{{ route("add-LocalCharge-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-add').load(url,function(){
                $('#modalLocalchargeAdd').modal({show:true});

            });

        }
        if(action == "duplicateLocalCharge"){
            $('#spinner').show();
            $('#modalLocalcharge').modal({show:true});
            var url = '{{ route("duplicate-local-charge-lcl", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body-edit').load(url,function(){
                $('#modalLocalcharge').modal({show:true});
                $('#spinner').hide();
            });

        }
    }

</script>

@if(session('editRateLcl'))
<script>

    swal(
        'Done!',
        'Rate updated.',
        'success'
    )
</script>
@endif
@if(session('localcharLcl'))
<script>
    swal(
        'Done!',
        'Local Charge updated.',
        'success'
    )
</script>
@endif


@if(session('localcharSaveLcl'))
<script>
    swal(
        'Done!',
        'Local Charge  saved.',
        'success'
    )
</script>
@endif
@if(session('ratesSaveLcl'))
<script>
    swal(
        'Done!',
        'rate saved.',
        'success'
    )
</script>
@endif



@stop
