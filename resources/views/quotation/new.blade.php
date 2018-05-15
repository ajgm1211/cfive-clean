@extends('layouts.app')
@section('title', 'Quotes')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        List  Surcharges 
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
            <div class="row">
                <div class="col-xl-12">
                    {!! Form::open(['route' => 'quotes.listRate','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
                    <div class="m-portlet__body">
                        <div class="form-group m-form__group row">
                            <div class="col-lg-2">
                                <label>
                                    <b>MODE:</b>
                                </label>
                            </div>
                            <div class="col-lg-6">
                                <div class='row'>
                                    <div class="col-md-4">
                                        <label class="m-option">
                                            <span class="m-option__control">
                                                <span class="m-radio m-radio--brand m-radio--check-bold">
                                                    <input name="m_option_1" value="1" type="radio">
                                                    <span></span>
                                                </span>
                                            </span>
                                            <span class="m-option__label">
                                                <span class="m-option__head">		
                                                    <span class="m-option__title">
                                                        FCL		
                                                    </span>
                                                </span>
                                            </span>		
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="m-option">
                                            <span class="m-option__control">
                                                <span class="m-radio m-radio--brand m-radio--check-bold">
                                                    <input name="m_option_1" value="1" type="radio">
                                                    <span></span>
                                                </span>
                                            </span>
                                            <span class="m-option__label">
                                                <span class="m-option__head">				 
                                                    <span class="m-option__title">
                                                        LCL				
                                                    </span>
                                                </span>		
                                                </label>
                                            </div>
                                        <div class="col-md-4">
                                            <label class="m-option">
                                                <span class="m-option__control">
                                                    <span class="m-radio m-radio--brand m-radio--check-bold">
                                                        <input name="m_option_1" value="1" type="radio">
                                                        <span></span>
                                                    </span>
                                                </span>
                                                <span class="m-option__label">
                                                    <span class="m-option__head">								 
                                                        <span class="m-option__title">
                                                            AIR	
                                                        </span>
                                                    </span>	
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <br>
                                    <div class='row'>
                                        <div class="col-md-4">
                                            <label>
                                                20' :
                                            </label>
                                            <div class="m-bootstrap-touchspin-brand">
                                                {!! Form::text('twuenty', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => '20 ','class' => 'col-lg-12 form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                40' :
                                            </label>
                                            <div class="m-bootstrap-touchspin-brand">
                                                {!! Form::text('forty', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => '40 ','class' => 'col-lg-12 form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                40' HC :
                                            </label>
                                            <div class="m-bootstrap-touchspin-brand">
                                                {!! Form::text('fortyhc', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => '40hc ','class' => 'col-lg-12 form-control']) !!}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label>
                                        <b>SERVICES:</b>
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <div class="row"> 
                                        <div class="col-md-2">            
                                            {!! Form::label('type2', 'Type') !!}<br>
                                            {{ Form::select('type',['1' => 'Export','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::label('icorterm', 'Icorterm') !!}<br>
                                            {{ Form::select('icoterm',['1' => 'Icoterm','2' => 'Icoterm2'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-5">
                                            {!! Form::label('poor', 'Poor') !!}<br>
                                            {{ Form::select('typpe2',['1' => 'Poor Origin TO DOOR DESTINATION','2' => 'Poor'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-3">
                                            {!! Form::label('pick', 'Pick up date') !!}<br>
                                            <div class="input-group date">
                                                {!! Form::text('date', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select date','class' => 'form-control m-input']) !!}
                                              
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar-check-o"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <br>
                                    <div class="row"> 
                                        <div class="col-md-4">            
                                            {!! Form::label('origin', 'Origin Port') !!}<br>
                                            {{ Form::select('originport',$harbor,null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::label('destiny', 'Destiny Port') !!}<br>
                                            {{ Form::select('destinyport',$harbor,null,['class'=>'m-select2-general form-control']) }}
                                        </div>

                                    </div>  <br>
                                    <div class="row">
                                        <div class="col-md-8">
                                            {!! Form::label('adrees', 'Adreess') !!}<br>
                                            {!! Form::text('name', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label>
                                        <b>CLIENT:</b>
                                    </label>
                                </div>
                                <div class="col-lg-10">
                                    <br>
                                    <div class="row"> 
                                        <div class="col-md-4">            
                                            {!! Form::label('client', 'Client') !!}<br>
                                            {{ Form::select('type',['1' => 'CLient','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::label('company', 'Company') !!}<br>
                                            {{ Form::select('icoterm',['1' => 'Company','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::label('price', 'Price Levels') !!}<br>
                                            {{ Form::select('icoterm',['1' => 'Price Levels','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>

                                    </div> 
                                </div>

                            </div>
                            <hr>
                            <div class="form-group m-form__group row">

                                <div class="row">
                                    <div class="col-lg-4">
                                        <button type="submit" class="btn btn-primary">
                                            Save
                                        </button>

                                    </div>
                                    <div class="col-lg-4">
                                        <button type="reset" class="btn btn-danger">
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
        </div>
    </div>
    @endsection

    @section('js')
    @parent
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/js/quote.js"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
    <script src="s/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>

    @stop
