@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('title', 'Global Charges')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        List  Global Charges 
                    </h3>
                </div>
            </div>
        </div>

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

            {!! Form::open(['route' => 'globalcharges.store','class' => 'form-group m-form__group']) !!}
           
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">

                    <div class="new col-xl-12 order-1 order-xl-2 m--align-right">
                        <a >
                            <button id="new" type="button" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                <span>
                                    <i class="la la-user"></i>
                                    <span>
                                        Add Charge
                                    </span>
                                </span>
                            </button>
                        </a>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    </div>
                </div>
            </div>

            <table class="table m-table m-table--head-separator-primary" id="sample_editable_1" width="100%">
                <thead>
                    <tr>
                        <th title="Field #1">
                            Type
                        </th>
                        <th title="Field #2">
                            Ports
                        </th>
                        <th title="Field #3">
                            Changetype
                        </th>
                        <th title="Field #4">
                            Carrier
                        </th>
                        <th title="Field #7">
                            Calculation type
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

                    @foreach ($global as $globalcharges)
                    <tr id='tr_l{{++$loop->index}}'>
                        <td>
                            <div id="divtype{{$loop->index}}"  class="val">{!! $globalcharges->surcharge->name !!}</div>
                            <div class="in" hidden="true">
                                {{ Form::select('type[]', $surcharge,$globalcharges->surcharge_id,['id' => 'type'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true']) }}
                            </div>
                        </td>
                        <td>
                            <div id="divport{{$loop->index}}"  class="val">{!! $globalcharges->ports->name !!}</div>
                            <div class="in" hidden="true">
                                {{ Form::select('port_id[]', $harbor,$globalcharges->port,['id' => 'port'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true']) }}
                            </div>
                        </td>
                        <td>

                            <div id="divchangetype{{$loop->index}}"  class="val">{!! $globalcharges->changetype !!}</div>
                            <div class="in" hidden="true">

                                {{ Form::select('changetype[]',['origin' => 'Origin','destination' => 'Destination'],$globalcharges->changetype,['id' => 'changetype'.$loop->index ,'class'=>'m-select2-general form-control','disabled' => 'true']) }}
                            </div>
                        </td>
                        <td>
                            <div id="divcarrier{{$loop->index}}"  class="val">{!! $globalcharges->carrier->name !!}</div>
                            <div class="in" hidden="true">
                                {{ Form::select('localcarrier_id[]', $carrier,$globalcharges->carrier_id,['id' => 'localcarrier'.$loop->index ,'class'=>'m-select2-general form-control','disabled' => 'true']) }}
                            </div>
                        </td>

                        <td>   
                            <div id="divcalculation{{$loop->index}}"  class="val">{!! $globalcharges->calculationtype->name !!}</div>
                            <div class="in" hidden="true">
                                {{ Form::select('calculationtype[]', $calculationT,$globalcharges->calculationtype_id,['id' => 'calculationtype'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true']) }}
                            </div> 
                        <td> 
                            <div id="divammount{{$loop->index}}" class="val"> {!! $globalcharges->ammount !!} </div>
                            <div class="in" hidden="    true"> {!! Form::text('ammount[]', $globalcharges->ammount, ['id' => 'ammount'.$loop->index ,'placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','disabled' => 'true']) !!}</div> 
                        </td>
                        <td>
                            <div id="divcurrency{{$loop->index}}"  class="val"> {!! $globalcharges->currency->alphacode !!} </div>
                            <div class="in" hidden="true">
                                {{ Form::select('localcurrency_id[]', $currency,$globalcharges->currency_id,['id' => 'localcurrency'.$loop->index ,'class'=>'m-select2-general form-control' ,'disabled' => 'true']) }}
                            </div> 
                        </td>
                        <td>
                            <a  id='edit_l{{$loop->index}}' onclick="display_l({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                <i class="la la-edit"></i>
                            </a>

                            <a  id='save_l{{$loop->index}}' onclick="save_l({{$loop->index}},{{$globalcharges->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                <i class="la la-save"></i>
                            </a>
                            <a  id='remove_l{{$loop->index}}'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" hidden="true">
                                <i id='rm_l{{$globalcharges->id}}' class="la la-times-circle"></i>
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
                <tr  id='globalclone' hidden="true" >
                    <td>{{ Form::select('type[]', $surcharge,null,['class'=>'custom-select form-control']) }}</td>
                    <td>{{ Form::select('port_id[]', $harbor,null,['class'=>'custom-select form-control']) }}</td>
                    <td>{{ Form::select('changetype[]',['origin' => 'Origin','destination' => 'Destination'],null,['class'=>'custom-select form-control']) }}</td>
                    <td>{{ Form::select('localcarrier_id[]', $carrier,null,['class'=>'custom-select form-control']) }}</td>
                    <td>{{ Form::select('calculationtype[]', $calculationT,null,['class'=>'custom-select form-control']) }}</td>
                    <td> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}</td>
                    <td>{{ Form::select('localcurrency_id[]', $currency,null,['class'=>'custom-select form-control']) }}</td>
                    <td>  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                        <i class="la la-eraser"></i>
                        </a>
                    </td>
                </tr>
            </table>
            <div class="m-portlet__foot m-portlet__foot--fit">
                <div id="buttons" hidden="true" class="m-form__actions m-form__actions">
                    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                    <a class="cancel btn btn-success">
                        Cancel
                    </a>
                </div>
            </div>

            {!! Form::close() !!}
        </div>


    </div>
</div>
</div>
@endsection

@section('js')
@parent

<script src="/js/globalcharges.js"></script>
<script src="/assets/plugins/table-datatables-editable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.bootstrap.js" type="text/javascript"></script>


@stop
