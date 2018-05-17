@php
$validation_expire = 'Please enter validation date';
@endphp
@extends('layouts.app')
@section('title', 'New Inland')
@section('content')
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <!--<div class="m-portlet__head">
<div class="m-portlet__head-caption">
<div class="m-portlet__head-title">
<h3 class="m-portlet__head-text">
Price levels
</h3>
</div>
</div>
</div>-->
        @if(Session::has('message.nivel'))
        <div class="col-md-12">
            <br>
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
        </div>
        @endif
        <div class="m-portlet__body">
            {!! Form::open(['route' => 'inlands.store','class' => 'form-group m-form__group']) !!}
            <div class="row">
                @include('inland.partials.form_inlands')
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="m-portlet m-portlet--responsive-mobile">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-technology m--font-brand"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text m--font-brand">
                                        Inland Charge for 20' Container
                                    </h3>
                                </div>			
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <a  id='newtwuenty' class="m-portlet__nav-link btn btn-secondary m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </li>		
                                </ul>
                            </div>
                        </div>
                        <div    class="text-center" style="font-size: 11px !important;">
                            <table id='twuenty' class=" table table-condensed col-lg-12">
                                <thead>
                                    <tr>
                                        <th> <span><b>Lower limit (KM)</b></span></th>
                                        <th>  <span><b>Upper limit (KM)</b></span></th>
                                        <th><span><b>Rate Per<br> Container</b></span></th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="col-md-2"> {!! Form::text('lowertwuenty[]', null, ['placeholder' => '0','class' => 'col-lg-12 form-control m-input','required' => 'required']) !!}</td>
                                        <td class="col-md-2">  {!! Form::text('uppertwuenty[]', null, ['placeholder' => '50','class' => 'col-lg-12 form-control m-input','required' => 'required']) !!}</td>
                                        <td  class="col-md-5">
                                            <div class="input-group">
                                                {!! Form::number('ammounttwuenty[]', null, ['placeholder' => '50','class' => ' col-lg-5 form-control m-input','required' => 'required']) !!}
                                                <div class="input-group-btn">
                                                    <div class="btn-group">
                                                        {{ Form::select('currencytwuenty[]',['1' => 'USD','2' => 'VEF'],null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-md-2">  -
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table hidden="true">
                                <tr id="twuentyclone">
                                    <td class="col-md-2"> {!! Form::text('lowertwuenty[]', null, ['placeholder' => '0','class' => 'col-lg-12 form-control m-input']) !!}</td> 
                                    <td class="col-md-2">         {!! Form::text('uppertwuenty[]', null, ['placeholder' => '50','class' => ' col-lg-12 form-control m-input']) !!}</td>
                                    <td  class="col-md-5">
                                        <div class="input-group">
                                            {!! Form::number('ammounttwuenty[]', null, ['placeholder' => '50','class' => ' col-lg-5 form-control m-input']) !!}
                                            <div class="input-group-btn">
                                                <div class="btn-group">
                                                    {{ Form::select('currencytwuenty[]',['1' => 'USD','2' => 'VEF'],null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-md-2">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                                        <i class="la la-eraser"></i>
                                        </a>
                                    </td>

                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="m-portlet m-portlet--responsive-mobile">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-technology m--font-brand"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text m--font-brand">
                                        Inland Charge for 40' Container
                                    </h3>
                                </div>			
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <a  id='newforty' class="m-portlet__nav-link btn btn-secondary m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </li>		
                                </ul>
                            </div>

                        </div>
                      <div    class="text-center" style="font-size: 11px !important;">
                            <table id='forty' class=" table table-condensed col-lg-12">
                                <thead>
                                    <tr>
                                        <th> <span><b>Lower limit (KM)</b></span></th>
                                        <th>  <span><b>Upper limit (KM)</b></span></th>
                                        <th><span><b>Rate Per<br> Container</b></span></th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="col-md-2"> {!! Form::text('lowerforty[]', null, ['placeholder' => '0','class' => 'col-lg-12 form-control m-input','required' => 'required']) !!}</td>
                                        <td class="col-md-2">  {!! Form::text('upperforty[]', null, ['placeholder' => '50','class' => 'col-lg-12 form-control m-input','required' => 'required']) !!}</td>
                                        <td  class="col-md-5">
                                            <div class="input-group">
                                                {!! Form::number('ammountforty[]', null, ['placeholder' => '50','class' => ' col-lg-5 form-control m-input','required' => 'required']) !!}
                                                <div class="input-group-btn">
                                                    <div class="btn-group">
                                                        {{ Form::select('currencyforty[]',['1' => 'USD','2' => 'VEF'],null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-md-2">  -
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table hidden="true">
                                <tr id="fortyclone">
                                    <td class="col-md-2"> {!! Form::text('lowerforty[]', null, ['placeholder' => '0','class' => 'col-lg-12 form-control m-input']) !!}</td> 
                                    <td class="col-md-2">         {!! Form::text('upperforty[]', null, ['placeholder' => '50','class' => ' col-lg-12 form-control m-input']) !!}</td>
                                    <td  class="col-md-5">
                                        <div class="input-group">
                                            {!! Form::number('ammountforty[]', null, ['placeholder' => '50','class' => ' col-lg-5 form-control m-input']) !!}
                                            <div class="input-group-btn">
                                                <div class="btn-group">
                                                    {{ Form::select('currencyforty[]',['1' => 'USD','2' => 'VEF'],null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-md-2">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                                        <i class="la la-eraser"></i>
                                        </a>
                                    </td>

                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="m-portlet m-portlet--responsive-mobile">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon">
                                        <i class="flaticon-technology m--font-brand"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text m--font-brand">
                                        Inland Charge for 40'HC  Container
                                    </h3>
                                </div>			
                            </div>
                            <div class="m-portlet__head-tools">
                                <ul class="m-portlet__nav">
                                    <li class="m-portlet__nav-item">
                                        <a  id='newfortyhc' class="m-portlet__nav-link btn btn-secondary m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </li>		
                                </ul>
                            </div>

                        </div>
                        <div    class="text-center" style="font-size: 11px !important;">
                            <table id='fortyhc' class=" table table-condensed col-lg-12">
                                <thead>
                                    <tr>
                                        <th> <span><b>Lower limit (KM)</b></span></th>
                                        <th>  <span><b>Upper limit (KM)</b></span></th>
                                        <th><span><b>Rate Per<br> Container</b></span></th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="col-md-2"> {!! Form::text('lowerfortyhc[]', null, ['placeholder' => '0','class' => 'col-lg-12 form-control m-input','required' => 'required']) !!}</td>
                                        <td class="col-md-2">  {!! Form::text('upperfortyhc[]', null, ['placeholder' => '50','class' => 'col-lg-12 form-control m-input','required' => 'required']) !!}</td>
                                        <td  class="col-md-5">
                                            <div class="input-group">
                                                {!! Form::number('ammountfortyhc[]', null, ['placeholder' => '50','class' => ' col-lg-5 form-control m-input','required' => 'required']) !!}
                                                <div class="input-group-btn">
                                                    <div class="btn-group">
                                                        {{ Form::select('currencyfortyhc[]',['1' => 'USD','2' => 'VEF'],null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-md-2">  -
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table hidden="true">
                                <tr id="fortyhcclone">
                                    <td class="col-md-2"> {!! Form::text('lowerfortyhc[]', null, ['placeholder' => '0','class' => 'col-lg-12 form-control m-input']) !!}</td> 
                                    <td class="col-md-2">         {!! Form::text('upperfortyhc[]', null, ['placeholder' => '50','class' => ' col-lg-12 form-control m-input']) !!}</td>
                                    <td  class="col-md-5">
                                        <div class="input-group">
                                            {!! Form::number('ammountfortyhc[]', null, ['placeholder' => '50','class' => ' col-lg-5 form-control m-input']) !!}
                                            <div class="input-group-btn">
                                                <div class="btn-group">
                                                    {{ Form::select('currencyfortyhc[]',['1' => 'USD','2' => 'VEF'],null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="col-md-2">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                                        <i class="la la-eraser"></i>
                                        </a>
                                    </td>

                                </tr>
                            </table>
                        </div>
                    </div>

                </div>  

            </div>
            <hr>
            <div class="m-portlet__foot m-portlet__foot--fit">
                <br>
                <div class="m-form__actions">
                    <button type="submit" class="btn btn-primary">
                        Submit
                    </button>
                    <button type="reset" class="btn btn-success">
                        Cancel
                    </button>
                </div>
            </div>

        </div>

    </div>

    {!! Form::close() !!}
</div>
</div>
</div>
@endsection

@section('js')
@parent
<script src="/js/inlands.js"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
@stop
