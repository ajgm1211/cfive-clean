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

                            <table class="table m-table m-table--head-separator-primary" id="sample_editable_1" width="100%">
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
                                        <td class="col-sm-2">{{ Form::select('origin_id[]', $harbor,$rates->port_origin->id,['id' => 'origin'.$loop->index,'class'=>'custom-select form-control', 'disabled' => 'true']) }} </td>
                                        <td class="col-sm-2"> {{ Form::select('destiny_id[]', $harbor,$rates->port_destiny->id,['id' => 'destiny'.$loop->index,'class'=>'custom-select form-control' ,'disabled' => 'true' ]) }}</td>
                                        <td class="col-sm-2">{{ Form::select('carrier_id[]', $carrier,$rates->carrier->id,['id' => 'carrier'.$loop->index,'class'=>'custom-select form-control','disabled' => 'true']) }}</td>
                                        <td class="col-sm-2"><span id='spantwuenty{{$loop->index}}'> {{$rates->twuenty  }} </span></td>
                                        <td class="col-sm-2"> <span id='spanforty{{$loop->index}}'> {{$rates->forty  }} </span></td>
                                        <td class="col-sm-2"><span id='spanfortyhc{{$loop->index}}'>{{$rates->fortyhc  }} </span> </td>
                                        <td class="col-sm-2">{{ Form::select('currency_id[]', $currency,$rates->currency->id,['id' => 'currency'.$loop->index,'class'=>'custom-select form-control','disabled' => 'true']) }}</td>
                                        <td class="col-sm-1">      
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
                            It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        </div>
                        <div class="tab-pane" id="m_tabs_6_3" role="tabpanel">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged
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
</div>

@endsection

@section('js')
@parent

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>

<script src="/js/rates.js"></script>
<script src="/assets/plugins/table-datatables-editable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.bootstrap.js" type="text/javascript"></script>

@stop
