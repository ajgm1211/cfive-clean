@extends('layouts.app')
@section('css')
@parent

<style>

    .select2-selection__choice {
        width: 40px !important;
        display: inline-block;
        white-space: pre-line;
    }


    th {
        font-weight: bold;
        background-color: #f4f3f8;
        color: #031B4E !important;
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
                    <h3 class="m-portlet__head-text" title="{{$contract['id'] .' - '. $contract['name'] }}">
                        Failed Surcharge - Contract/<strong style="color:{{$equiment['color']}};"><i> {{$equiment['name']}} </i></strong>
                        <div class="progress m-progress--sm">
                            <div class="progress-bar " role="progressbar" style=" background-color:{{$equiment['color']}};width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </h3><br>
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
            {!! Form::open(['route' => 'store.multi.Surcharge.fails', 'method' => 'post','class' => 'form-group m-form__group']) !!}
            <input type="hidden" name="equiment_id" value="{{$equiment_id}}">
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                    <i class="la la-cog"></i>
                                    Fail Surcharge
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
                                            <th title="Field #1" style="width:20%;padding: 0.75rem 0.3rem;">
                                                Surcharge
                                            </th>
                                            <th title="Field #2" style="width:20%;padding: 0.75rem 0.3rem;">
                                                Origin Port
                                            </th>
                                            <th title="Field #3" style="width:20%;padding: 0.75rem 0.3rem;">
                                                Destination Port
                                            </th>
                                            <th title="Field #4" style="width:20%;padding: 0.75rem 0.3rem;">
                                                Type Destiny
                                            </th>

                                            <th title="Field #5" style="width:20%;padding: 0.75rem 0.3rem;">
                                                Type Calculation
                                            </th>
                                            <th title="Field #6" style="width:20%;padding: 0.75rem 0.3rem;">
                                                Amount
                                            </th>
                                            <th title="Field #7" style="padding: 0.75rem 0.3rem;">
                                                Currency
                                            </th>   
                                            <th title="Field #8" style="padding: 0.75rem 0.3rem;">
                                                Carrier
                                            </th>
                                            <th title="Field #9">
                                                Option
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $rowTable = 0;
                                        @endphp
                                        <input type="hidden" name="contract_id" value="{{$contract_id}}">
                                        @foreach($fail_surcharge_total as $surchargef)
                                        @php
                                        $rowTable = $loop->index;
                                        @endphp
                                        <tr id="{{'tr_clone'.$loop->index}}"  >
                                            <input type="hidden" name="surcharge_fail_id[{{$loop->index}}]" value="{{$surchargef['surcharge_id']}}">
                                            <input type="hidden" name="typerate" value="{{$surchargef['type_rate']}}">
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('id_surcharge['.$rowTable.']', $surcharges,$surchargef['surcharge'],['class'=>'m-select2-general  col-sm-6 form-control ','style' => 'width:00%;'.$surchargef["classorigin"] ,'required' => 'required']) }}
                                                <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                    <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classsurcharger']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <!-- input port -->
                                            @if($surchargef['type_rate']=='port')
                                                <td style="padding: 0.75rem 0.2rem;">
                                                    {{ Form::select('origin_id['.$rowTable.'][]', $harbor,$surchargef['origin_port'],['class'=>'m-select2-general  col-sm-6 form-control ','style' => 'width:00%;'.$surchargef["classorigin"] ,'required' => 'required','multiple' => 'multiple']) }}
                                                    <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                        <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classorigin']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td style="padding: 0.75rem 0.2rem;">
                                                    {{ Form::select('destiny_id['.$rowTable.'][]', $harbor,$surchargef['destiny_port'],['class'=>'m-select2-general col-sm-6 form-control ','required' => 'required','style' => 'width:100%;' ,'multiple' => 'multiple']) }}
                                                    <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                        <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classdestiny']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                            @endif
                                            <!-- input country -->
                                            @if($surchargef['type_rate']=='country')
                                                <td style="padding: 0.75rem 0.2rem;">
                                                    {{ Form::select('origin_id['.$rowTable.'][]', $countries,$surchargef['origin_port'],['class'=>'m-select2-general  col-sm-6 form-control ','style' => 'width:00%;'.$surchargef["classorigin"] ,'required' => 'required','multiple' => 'multiple']) }}
                                                    <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                        <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classorigin']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                                <td style="padding: 0.75rem 0.2rem;">
                                                    {{ Form::select('destiny_id['.$rowTable.'][]', $countries,$surchargef['destiny_port'],['class'=>'m-select2-general col-sm-6 form-control ','required' => 'required','style' => 'width:100%;' ,'multiple' => 'multiple']) }}
                                                    <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                        <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classdestiny']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </td>
                                            @endif
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('type_destiny_id['.$rowTable.']', $type_destiny,$surchargef['type_destiny'],['class'=>'m-select2-general col-sm-6 form-control ','required' => 'required','style' => 'width:100%;' ]) }}
                                                <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                    <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classtypedestiny']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>

                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('type_calculation__id['.$rowTable.']', $calculation_type,$surchargef['calculation_type'],['class'=>'m-select2-general col-sm-6 form-control ','required' => 'required','style' => 'width:100%;' ]) }}
                                                <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                    <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classcalculationtype']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>

                                            <td style="padding: 0.80rem 0.2rem;">
                                                {!! Form::text('amountS['.$rowTable.']', $surchargef['amount'], ['placeholder' => 'Enter Amount','class' => 'form-control m-input','required' => 'required','style' => 'width:110%; font-size: 12px;']) !!}
                                                <div class="progress m-progress--sm" style="padding: 0.1rem 0.0rem;">
                                                    <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classamount']}} ;width: 110%;" aria-valuenow="110" aria-valuemin="0" aria-valuemax="110"></div>
                                                </div> 
                                            </td>
                                            
                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('currency_id['.$rowTable.']', $currency,$surchargef['currencyAIn'],['class'=>'m-select2-general col-sm-6 form-control','required' => 'required','style' => 'width:100%;']) }}
                                                <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                    <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classcurrency']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>

                                            <td style="padding: 0.75rem 0.2rem;">
                                                {{ Form::select('carrier_id['.$rowTable.'][]', $carrier,$surchargef['carrierAIn'],['class'=>'m-select2-general col-sm-6 form-control','required' => 'required','style' => 'width:100%;','multiple' => 'multiple']) }}
                                                <div class="progress m-progress--sm" style="padding: 0.1rem 0.2rem;">
                                                    <div class="progress-bar " role="progressbar" style=" background-color:{{$surchargef['classcarrier']}} ;width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>

                                            <td style="padding: 0.75rem 0.2rem;" style="padding: 0.1rem 0.2rem;">
                                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" onclick="removeTr('{{'tr_clone'.$rowTable}}')">
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
