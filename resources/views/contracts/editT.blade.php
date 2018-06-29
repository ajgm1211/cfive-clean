@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
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


            {!! Form::model($contracts, ['route' => ['contracts.update', $contracts], 'method' => 'PUT','class' => 'form-group m-form__group']) !!}
            @include('contracts.partials.form_contractsT')
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                    <i class="la la-cog"></i>
                                    Routes
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_2" role="tab">
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
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                            <a  id="new" class="">

                                <button type="button" class="btn btn-brand">
                                    Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </a>


                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadfile">
                                Upload Rates
                                <i class="fa flaticon-tool-1"></i>
                            </button>
                            <a href="{{route('Failed.Rates.For.Contracts',$id)}}" class="btn btn-info">
                                 Failed Rates
                                <i class="fa flaticon-tool-1"></i>
                            </a>
                            <table class="table m-table m-table--head-separator-primary" id="sample_editable_2" width="100%">
                                <thead>
                                    <tr>
                                        <th title="Field #1">
                                            Origin Port
                                        </th>
                                        <th title="Field #2">
                                            Destiny Port    
                                        </th>
                                        <th title="Field #3">
                                            Carrier
                                        </th>
                                        <th title="Field #4">
                                            20'
                                        </th>
                                        <th title="Field #5">
                                            40'
                                        </th>
                                        <th title="Field #6">
                                            40'HC
                                        </th>
                                        <th title="Field #7">
                                            Currency
                                        </th>
                                        <th title="Field #7">
                                            Options
                                        </th>


                                    </tr>
                                </thead>
                                <tbody>


                                    @foreach ($contracts->rates as $rates)

                                    <tr id='tr{{++$loop->index}}' disabled='true'>
                                        <td width='15%' > 
                                            <div id='divoriginport{{$loop->index}}' class='val'>{{$rates->port_origin->name}}</div>
                                            <div class='in' hidden="true">
                                                {{ Form::select('origin_id[]', $harbor,$rates->port_origin->id,['id' => 'origin'.$loop->index,'class'=>'m-select2-general form-control', 'disabled' => 'true' ,'style' => 'width:100%;']) }} 
                                            </div>
                                        </td>
                                        <td  width='15%' >  
                                            <div  id='divdestinyport{{$loop->index}}' class='val'>   {{$rates->port_destiny->name}} </div>
                                            <div class='in' hidden="true">  
                                                {{ Form::select('destiny_id[]', $harbor,$rates->port_destiny->id,['id' => 'destiny'.$loop->index,'class'=>'m-select2-general form-control' ,'disabled' => 'true' ,'style' => 'width:100%;']) }}
                                            </div>
                                        </td>
                                        <td  width='10%' >
                                            <div id='divcarrier{{$loop->index}}' class='val'>  {{$rates->carrier->name }} </div>
                                            <div class='in' hidden="true"> 
                                                {{ Form::select('carrier_id[]', $carrier,$rates->carrier->id,['id' => 'carrier'.$loop->index,'class'=>'m-select2-general form-control','disabled' => 'true']) }}
                                            </div>
                                        </td>
                                        <td  width='10%' >
                                            <div  id='divtwuenty{{$loop->index}}' class='val'>  {{$rates->twuenty  }}  </div>
                                            <div class='in' hidden="true">  
                                                {!! Form::text('twuenty[]', $rates->twuenty, ['id' => 'twuenty'.$loop->index,'placeholder' => 'Please enter the 20','class' => 'form-control m-input', 'disabled' =>  'true' ]) !!} 
                                            </div>
                                        </td>
                                        <td  width='10%'>
                                            <div id='divforty{{$loop->index}}' class='val'>  {{$rates->forty  }}  </div>
                                            <div class='in' hidden="true"> 
                                                {!! Form::text('forty[]', $rates->forty, ['id' => 'forty'.$loop->index,'placeholder' => 'Please enter the 40','class' => 'form-control m-input' ,'disabled' =>  'true']) !!} 
                                            </div>
                                        </td>
                                        <td  width='10%'  >
                                            <div id='divfortyhc{{$loop->index}}' class='val'>{{ $rates->fortyhc}}  </div>
                                            <div class='in' hidden="true">
                                                {!! Form::text('fortyhc[]', $rates->fortyhc, ['id' => 'fortyhc'.$loop->index,'placeholder' => '40HC','class' => 'form-control','disabled' =>  'true' ]) !!}  
                                            </div>
                                        </td>
                                        <td  width='15%' >
                                            <div id='divalphacode{{$loop->index}}' class='val'> {{$rates->currency->alphacode}}   </div>
                                            <div class='in' hidden="true">
                                                {{ Form::select('currency_id[]', $currency,$rates->currency->id,['id' => 'currency'.$loop->index,'class'=>'m-select2-general form-control','disabled' => 'true']) }}
                                            </div>
                                        </td>
                                        <td   width='10%'>      
                                            <a  id='edit{{$loop->index}}' onclick="display({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                                <i class="la la-edit"></i>
                                            </a>

                                            <a  id='save{{$loop->index}}' onclick="save({{$loop->index}},{{$rates->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                <i class="la la-save"></i>
                                            </a>

                                            <a  id='cancel{{$loop->index}}' onclick="cancel({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                <i class="la la-reply"></i>
                                            </a>

                                        </td>
                                    </tr>

                                    @endforeach

                                    <tr   id='tclone' hidden="true" >
                                        <td>{{ Form::select('origin_id[]', $harbor,null,['class'=>'custom-select form-control']) }}</td>
                                        <td>{{ Form::select('destiny_id[]', $harbor,null,['class'=>'custom-select form-control']) }}</td>
                                        <td>{{ Form::select('carrier_id[]', $carrier,null,['class'=>'custom-select form-control']) }}</td>

                                        <td>{!! Form::text('twuenty[]', null, ['placeholder' => 'Please enter the 20','class' => 'form-control m-input' ]) !!} </td>
                                        <td>{!! Form::text('forty[]', null, ['placeholder' => 'Please enter the 40','class' => 'form-control m-input']) !!} </td>
                                        <td> {!! Form::text('fortyhc[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}</td>
                                        <td>{{ Form::select('currency_id[]', $currency,null,['class'=>'custom-select form-control']) }}</td>
                                        <td>  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete"  >
                                            <i class="la la-eraser" ></i>
                                            </a>
                                        </td>
                                    </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="m_tabs_6_2" role="tabpanel">
                            <a  id="newL" class="">

                                <button type="button" class="btn btn-brand">
                                    Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </a>
                            <a>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadfileSubcharge">
                                    Upload Surcharge
                                    <i class="fa flaticon-tool-1"></i>
                                </button>
                            </a>
                            <a href="{{route('Failed.Subcharge.For.Contracts',$id)}}" class="btn btn-info">
                                 Failed Surcharge
                                <i class="fa flaticon-tool-1"></i>
                            </a>
                            <table class="table m-table m-table--head-separator-primary" id="sample_editable_1" width="100%">
                                <thead>
                                    <tr>
                                        <th title="Field #1">
                                            Type
                                        </th>
                                        <th title="Field #2">
                                            Origin Port
                                        </th>
                                        <th title="Field #2">
                                            Destiny Port
                                        </th>
                                        <th title="Field #3">
                                            Change type
                                        </th>
                                        <th title="Field #4">
                                            Carrier
                                        </th>
                                        <th title="Field #7">
                                            Calculation <br>type
                                        </th>
                                        <th title="Field #8">
                                            Ammount
                                        </th>
                                        <th title="Field #9">
                                            Currency
                                        </th>
                                        <th title="Field #10">
                                            Options
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts->localcharges as $localcharges)
                                    <tr id='tr_l{{++$loop->index}}'>
                                        <td>
                                            <div id="divtype{{$loop->index}}"  class="val">{!! $localcharges->surcharge->name !!}</div>
                                            <div class="in" hidden="true">
                                                {{ Form::select('type[]', $surcharge,$localcharges->surcharge_id,['id' => 'type'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true']) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div id="divport{{$loop->index}}"  class="val">   
                                                {!! str_replace(["[","]","\""], ' ', $localcharges->localcharports->pluck('portOrig')->unique()->pluck('name') ) !!}
                                            </div>
                                            <div class="in" hidden="true">
                                                {{ Form::select('port_origlocal[]', $harbor,$localcharges->localcharports->pluck('port_orig'),['id' => 'portOrig'.$loop->index ,'class'=>'m-select2-general  form-control ','disabled' => 'true','multiple' => 'multiple']) }}
                                            </div>
                                        </td>
                                        <td>


                                            <div id="divportDest{{$loop->index}}"  class="val">   
                                                {!! str_replace(["[","]","\""], ' ', $localcharges->localcharports->pluck('portDest')->unique()->pluck('name') ) !!}
                                            </div>
                                            <div class="in" hidden="true">
                                                {{ Form::select('port_destlocal[]', $harbor,$localcharges->localcharports->pluck('port_dest'),['id' => 'portDest'.$loop->index ,'class'=>'m-select2-general  form-control ','disabled' => 'true','multiple' => 'multiple']) }}
                                            </div>
                                        </td>
                                        <td>

                                            <div id="divchangetype{{$loop->index}}"  class="val">{!! $localcharges->typedestiny->description !!} </div>
                                            <div class="in" hidden="true">

                                                {{ Form::select('changetype[]',$typedestiny, $localcharges->typedestiny_id,['id' => 'changetype'.$loop->index ,'class'=>'m-select2-general form-control','disabled' => 'true']) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div id="divcarr{{$loop->index}}"  class="val">
                                                {!! str_replace(["[","]","\""], ' ', $localcharges->localcharcarriers->pluck('carrier')->pluck('name') ) !!}
                                            </div>
                                            <div class="in" hidden="true">
                                                {{ Form::select('localcarrier_id[]', $carrier,$localcharges->localcharcarriers->pluck('carrier_id'),['id' => 'localcarrier'.$loop->index ,'class'=>'m-select2-general form-control','disabled' => 'true','multiple' => 'multiple']) }}
                                            </div>
                                        </td>

                                        <td>   
                                            <div id="divcalculation{{$loop->index}}"  class="val">{!! $localcharges->calculationtype->name !!}</div>
                                            <div class="in" hidden="true">
                                                {{ Form::select('calculationtype[]', $calculationT,$localcharges->calculationtype_id,['id' => 'calculationtype'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true']) }}
                                            </div> 
                                        <td> 
                                            <div id="divammount{{$loop->index}}" class="val"> {!! $localcharges->ammount !!} </div>
                                            <div class="in" hidden="    true"> {!! Form::text('ammount[]', $localcharges->ammount, ['id' => 'ammount'.$loop->index ,'placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','disabled' => 'true']) !!}</div> 
                                        </td>
                                        <td>
                                            <div id="divcurrency{{$loop->index}}"  class="val"> {!! $localcharges->currency->alphacode !!} </div>
                                            <div class="in" hidden="true">
                                                {{ Form::select('localcurrency_id[]', $currency,$localcharges->currency_id,['id' => 'localcurrency'.$loop->index ,'class'=>'m-select2-general form-control' ,'disabled' => 'true']) }}
                                            </div> 
                                        </td>
                                        <td>
                                            <a  id='edit_l{{$loop->index}}' onclick="display_l({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                                <i class="la la-edit"></i>
                                            </a>

                                            <a  id='save_l{{$loop->index}}' onclick="save_l({{$loop->index}},{{$localcharges->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                <i class="la la-save"></i>
                                            </a>
                                            <a  id='remove_l{{$loop->index}}'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" hidden="true">
                                                <i id='rm_l{{$localcharges->id}}' class="la la-times-circle"></i>
                                            </a>

                                            <a  id='cancel_l{{$loop->index}}' onclick="cancel_l({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                <i  class="la la-reply"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            <table hidden="true">
                                <tr   id='tclone2' hidden="true"  >
                                    <td>
                                        {{ Form::select('type[]', $surcharge,null,['class'=>'form-control']) }}
                                    </td>
                                    <td>{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control portOrig','multiple' => 'multiple']) }}</td>
                                    <td>{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control portDest','multiple' => 'multiple']) }}</td>
                                    <td>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control']) }}</td>
                                    <td>{{ Form::select(null, $carrier,null,['class'=>'custom-select form-control carrier','multiple' => 'multiple']) }}</td>

                                    <td>  {{ Form::select('calculationtype[]', $calculationT,null,['class'=>'custom-select form-control ']) }}</td>
                                    <td> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}</td>
                                    <td>{{ Form::select('localcurrency_id[]', $currency,null,['class'=>'custom-select form-control']) }}</td>
                                    <td>  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                                        <i class="la la-eraser"></i>
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
                                            {{ Form::select('companies[]',$companies,@$company->id,['multiple','class'=>'m-select2-general','id' => 'm-select2-company']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="origin_harbor_label">
                                        <label>Users</label>
                                        <div class="form-group m-form__group align-items-center">
                                            {{ Form::select('users[]',$users,@$user->id,['multiple','class'=>'m-select2-general','id' => 'm-select2-client']) }}
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m-portlet__foot--fit">
                        <div class="m-form__actions m-form__actions">
                            {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                            <a class="btn btn-success" href="{{url()->previous()}}">
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
    
        <div class="modal fade" id="uploadfileSubcharge" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                {!! Form::open(['route' => 'Upload.File.Subcharge.For.Contracts','method' => 'PUT', 'files'=>true]) !!}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Upload File Of Subcharge
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="form-control-label">
                            Single File Upload:
                        </label>
                        {!!Form::file('file',['id'=>'','required'])!!}
                    </div>
                    {!!Form::hidden('contract_id',$id,['id'=>''])!!}


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <!--  <button type="submit" class="btn btn-success">
Load
</button>-->
                    <input type="submit" class="btn btn-success">
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </div>
    
    
    
    <div class="modal fade" id="uploadfile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                {!! Form::open(['route' => 'Upload.File.Rates.For.Contracts','method' => 'PUT', 'files'=>true]) !!}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Upload File Of Rates
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="form-control-label">
                            Single File Upload:
                        </label>
                        {!!Form::file('file',['id'=>'recipient-name','required'])!!}
                    </div>
                    {!!Form::hidden('contract_id',$id,['id'=>'contract_id'])!!}


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <!--  <button type="submit" class="btn btn-success">
Load
</button>-->
                    <input type="submit" class="btn btn-success">
                    {!! Form::close()!!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@parent

<script type="application/x-javascript">
    $(document).ready(function(){

    });
</script>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>


<script src="/js/editcontracts.js"></script>
<script src="/assets/plugins/table-datatables-editable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.bootstrap.js" type="text/javascript"></script>

@stop
