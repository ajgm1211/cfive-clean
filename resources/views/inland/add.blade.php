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
                        <div id="msg20" style="display:none" class="m-alert m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>I'm Sorry!</strong> the upper limit can not be less than the initial limit			  	
                        </div>
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
                        <div class="text-center" style="font-size: 11px !important;">

                            <table id='twuenty' class=" table table-condensed">
                                <thead>
                                    <tr>
                                        <th> <span><b>Lower <br> limit (KM)</b></span></th>
                                        <th>  <span><b>Upper <br> limit (KM)</b></span></th>
                                        <th><span><b>Rate Per <br> Container</b></span></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class=""> {!! Form::text('lowertwuenty[]', null, ['placeholder' => '0','class' => 'form-control m-input low ','required' => 'required' , 'id' => 'l20']) !!}</td>
                                        <td class="">  {!! Form::text('uppertwuenty[]', null, ['placeholder' => '50','class' => 'form-control m-input prove up up20','required' => 'required','id' => 'up201' ,'onblur' => 'validateRange(this.id,\'t20\')']) !!}</td>
                                        <td class="">
                                            <div class="input-group">
                                                {!! Form::number('ammounttwuenty[]', null, ['placeholder' => '50','class' => 'form-control m-input','required' => 'required']) !!}
                                            
                                            <div class="input-group-btn">
                                                <div class="btn-group">
                                                    {{ Form::select('currencytwuenty[]',$currency,null,['class'=>'custom-select form-control','id' => '']) }}
                                                </div>
                                            </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table hidden="true">
                                <tr id="twuentyclone">

                                    <td class=""> {!! Form::text('lowertwuenty[]', null, ['placeholder' => '0','class' => 'form-control m-input low cloLow20']) !!}</td>
                                    <td class="">         {!! Form::text('uppertwuenty[]', null, ['placeholder' => '50','class' => 'form-control m-input  up cloUp20']) !!}</td>
                                    <td  class="">
                                        <div class="input-group">
                                            {!! Form::number('ammounttwuenty[]', null, ['placeholder' => '50','class' => 'form-control m-input']) !!}
                                        
                                        <div class="input-group-btn">
                                            <div class="btn-group">
                                                {{ Form::select('currencytwuenty[]',$currency,null,['class'=>'custom-select form-control','id' => '']) }}
                                            </div>
                                        </div>
                                        </div>
                                        
                                    </td>
                                    <td class="">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
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
                        <div id="msg40" style="display:none" class="m-alert m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>I'm Sorry!</strong> the upper limit can not be less than the initial limit			  	
                        </div>
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
                                        <th> <span><b>Lower <br> limit (KM)</b></span></th>
                                        <th>  <span><b>Upper <br> limit (KM)</b></span></th>
                                        <th><span><b>Rate Per <br> Container</b></span></th>
                                        

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class=""> {!! Form::text('lowerforty[]', null, ['placeholder' => '0','class' => 'form-control m-input low low40','required' => 'required' , 'id' => 'lo401']) !!}</td>
                                        <td class="">  {!! Form::text('upperforty[]', null, ['placeholder' => '50','class' => 'form-control m-input up up40','required' => 'required','id' => 'up401' ,'onblur' => 'validateRange40(this.id,\'t40\')']) !!}</td>
                                        <td  class="">
                                            <div class="input-group">
                                                {!! Form::number('ammountforty[]', null, ['placeholder' => '50','class' => 'form-control m-input','required' => 'required']) !!}
                                                <div class="input-group-btn">
                                                    <div class="btn-group">
                                                        {{ Form::select('currencyforty[]',$currency,null,['class'=>'custom-select form-control','id' => '']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table hidden="true">
                                <tr id="fortyclone">
                                    <td class=""> {!! Form::text('lowerforty[]', null, ['placeholder' => '0','class' => 'form-control m-input low cloLow40']) !!}</td>
                                    <td class="">         {!! Form::text('upperforty[]', null, ['placeholder' => '50','class' => 'form-control m-input up cloUp40']) !!}</td>
                                    <td  class="">
                                        <div class="input-group">
                                            {!! Form::number('ammountforty[]', null, ['placeholder' => '50','class' => 'form-control m-input']) !!}
                                            <div class="input-group-btn">
                                                <div class="btn-group">
                                                    {{ Form::select('currencyforty[]',$currency,null,['class'=>'custom-select form-control','id' => '']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
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
                        <div id="msg40H" style="display:none" class="m-alert m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>I'm Sorry!</strong> the upper limit can not be less than the initial limit			  	
                        </div>
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
                                        <th> <span><b>Lower <br> limit (KM)</b></span></th>
                                        <th>  <span><b>Upper <br> limit (KM)</b></span></th>
                                        <th><span><b>Rate Per <br> Container</b></span></th>
                                    

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class=""> {!! Form::text('lowerfortyhc[]', null, ['placeholder' => '0','class' => 'form-control m-input low low40H','required' => 'required' ,'id' => 'lo40H1']) !!}</td>
                                        <td class="">  {!! Form::text('upperfortyhc[]', null, ['placeholder' => '50','class' => 'form-control m-input up up40H','required' => 'required', 'id' => 'up40H1' ,'onblur' => 'validateRange40hc(this.id,\'t40H\')' ]) !!}</td>
                                        <td  class="">
                                            <div class="input-group">
                                                {!! Form::number('ammountfortyhc[]', null, ['placeholder' => '50','class' => 'form-control m-input','required' => 'required']) !!}
                                                <div class="input-group-btn">
                                                    <div class="btn-group">
                                                        {{ Form::select('currencyfortyhc[]',$currency,null,['class'=>'custom-select form-control','id' => '']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table hidden="true">
                                <tr id="fortyhcclone">
                                    <td class=""> {!! Form::text('lowerfortyhc[]', null, ['placeholder' => '0','class' => 'form-control m-input low cloLow40H']) !!}</td>
                                    <td class="">  {!! Form::text('upperfortyhc[]', null, ['placeholder' => '50','class' => 'form-control m-input up cloUp40H']) !!}</td>
                                    <td  class="">
                                        <div class="input-group">
                                            {!! Form::number('ammountfortyhc[]', null, ['placeholder' => '50','class' => 'form-control m-input']) !!}
                                            <div class="input-group-btn">
                                                <div class="btn-group">
                                                    {{ Form::select('currencyfortyhc[]',$currency,null,['class'=>'custom-select form-control','id' => '']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
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
@endsection

@section('js')
@parent
<script src="/js/inlands.js"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
@stop
