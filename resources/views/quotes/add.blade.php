@extends('layouts.app')
@section('title', 'Quotes')
@section('content')

    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
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
                <div class="row">
                    <div class="col-xl-12">
                        {!! Form::open(['route' => 'quotes.store','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']) !!}
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
                                                    <input name="type" value="1" type="radio">
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
                                                    <input name="type" value="2" type="radio">
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
                                                        <input name="type" value="3" type="radio">
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
                                                {!! Form::text('qty_20', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                40' :
                                            </label>
                                            <div class="m-bootstrap-touchspin-brand">
                                                {!! Form::text('qty_40', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                40' HC :
                                            </label>
                                            <div class="m-bootstrap-touchspin-brand">
                                                {!! Form::text('qty_40_hc', null, ['id' => 'm_touchspin_2_1' ,'placeholder' => ' ','class' => 'col-lg-12 form-control']) !!}

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
                                            <label>Modality</label>
                                            {{ Form::select('modality',['1' => 'Export','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label>Incoterm</label>
                                            {{ Form::select('incoterm',['1' => 'FOB','2' => 'ECX'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-5">
                                            <label>Delivery type</label>
                                            {{ Form::select('delivery_type',['1' => 'PORT(Origin) To DOOR(Destination)','2' => 'PORT(Origin) To PORT(Destination)'],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-3">
                                            <label>Pick up date</label>
                                            <div class="input-group date">
                                                {!! Form::text('pick_up_date', null, ['id' => 'm_datepicker_2' ,'placeholder' => 'Select date','class' => 'form-control m-input']) !!}
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
                                            <label>Origin port</label>
                                            {{ Form::select('origin_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Destination port</label>
                                            {{ Form::select('destination_harbor_id',$harbors,null,['class'=>'m-select2-general form-control','multiple' => 'multiple']) }}
                                        </div>
                                        <div class="col-md-8">
                                            <label>Destination address</label>
                                            {!! Form::text('destination_address', null, ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','required' => 'required']) !!}
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
                                            <label>Company</label>
                                            {{ Form::select('company_id',$companies,null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal']) }}
                                        </div>
                                        <div class="col-md-4">
                                            <label>Client</label>
                                            {{ Form::select('client',[],null,['class'=>'m-select2-general form-control']) }}
                                        </div>
                                        <div class="col-md-4">
                                            <label>Price level</label>
                                            {{ Form::select('price_id',[],null,['class'=>'m-select2-general form-control']) }}
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                </div>
                                <div class="col-lg-10">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Status</label>
                                            {{ Form::select('status_id',[1=>'Sent',2=>'Draft',3=>'Accepted'],null,['placeholder' => 'Please choose a option','class'=>'m-select2-general form-control','id' => 'm_select2_2_modal']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row">
                                <div class="row">
                                    <div class="col-lg-4 col-lg-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Create Quote
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
