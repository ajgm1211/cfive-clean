@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
<style>

    .select2-selection__choice {
        width: 40px !important;
        display: inline-block;
        white-space: pre-line;
    }

</style>
@endsection

@section('title', 'Edit Fails Rates FCL By Detalls')
@section('content')
@php
$validation_expire = 'Please enter validity date';
@endphp
<div class="container-fluid mt-4">

    <!--Begin::Main Portlet-->
    <div class="m-portlet m-portlet--full-height">
        <!--begin: Portlet Head-->
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Edit Fails Rates FCL
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
        <div class="container-fluid mt-4">
            {!! Form::open(['route' => 'store.multi.rates.fails', 'method' => 'post','class' => 'form-group m-form__group']) !!}

            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                    <i class="la la-cog"></i>
                                    Fail Rates
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="container-fluid mt-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped m-table m-table--head-separator-primary table-contracts" id="sample_editable_1" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th title="Field #1" style="padding: 0.75rem 0.2rem;">
                                                Origin Port
                                            </th>
                                            <th title="Field #2" style="padding: 0.75rem 0.2rem;">
                                                Destination Port
                                            </th>
                                            <th title="Field #3" style="padding: 0.75rem 0.2rem;">
                                                Carrier
                                            </th>
                                            <th title="Field #4" style="width:12%; padding: 0.75rem 0.2rem;">
                                                20'
                                            </th>
                                            <th title="Field #5" style="width:12%; padding: 0.75rem 0.2rem;">
                                                40'
                                            </th>
                                            <th title="Field #6" style="width:12%; padding: 0.75rem 0.2rem;">
                                                40'HC
                                            </th>
                                            <th title="Field #6" style="width:12%; padding: 0.75rem 0.2rem;">
                                                40'NOR
                                            </th>
                                            <th title="Field #6" style="width:12%; padding: 0.75rem 0.2rem;">
                                                45'
                                            </th>
                                            <th title="Field #7" style="padding: 0.75rem 0.2rem;">
                                                Currency
                                            </th>
                                            <th title="Field #7">
                                                Option
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <input type="hidden" name="contract_id" value="{{$contract_id}}">
                                        @foreach($fail_rates_total as $ratef)
                                        <tr id="{{'tr_clone'.$loop->index}}"  >
                                            <input type="hidden" name="rate_fail_id[]" value="{{$ratef['rate_id']}}">
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('origin_id['.$loop->index.'][]', $harbor,$ratef['origin_port'],['class'=>'m-select2-general  col-sm-6 form-control ','style' => 'width:100%;'.$ratef["classorigin"] ,'required' => 'required','multiple' => 'multiple']) }}

                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('destiny_id['.$loop->index.'][]', $harbor,$ratef['destiny_port'],['class'=>'m-select2-general col-sm-6 form-control ','required' => 'required','style' => 'width:100%;' ,'multiple' => 'multiple']) }}
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('carrier_id['.$loop->index.']', $carrier,$ratef['carrierAIn'],['class'=>'m-select2-general col-sm-6 form-control','required' => 'required','style' => 'width:100%;']) }}
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {!! Form::text('twuenty['.$loop->index.']', $ratef['twuenty'], ['placeholder' => 'Enter 20','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;']) !!} 
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {!! Form::text('forty['.$loop->index.']', $ratef['forty'], ['placeholder' => 'Enter 40','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ]) !!} 
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {!! Form::text('fortyhc['.$loop->index.']', $ratef['fortyhc'], ['placeholder' => 'Enter 40HC','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ]) !!}
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {!! Form::text('fortynor['.$loop->index.']', $ratef['fortynor'], ['placeholder' => 'Enter 40NOR','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ]) !!}
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {!! Form::text('fortyfive['.$loop->index.']', $ratef['fortyfive'], ['placeholder' => 'Enter 45','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;']) !!}
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('currency_id['.$loop->index.']', $currency,$ratef['currencyAIn'],['class'=>'m-select2-general col-sm-6 form-control','required' => 'required','style' => 'width:100%;']) }}
                                            </td>
                                            <td style="padding: 0.75rem 0.2rem;">
                                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" onclick="removeTr('{{'tr_clone'.$loop->index}}')">
                                                    <i class="la la-remove"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="m-portlet__foot m-portlet__foot--fit ">
                        <br>
                        <div class="m-form__actions m-form__actions">
                            {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                            <a class="btn btn-danger" href="{{url()->previous()}}">
                                Cancel
                            </a>
                        </div>
                        <br>
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
<script>
    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

    function removeTr(tr){
        $('#'+tr).remove(); 
    }
</script>
@stop
