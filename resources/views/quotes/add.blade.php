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
                            <div class="row">
                                <div class="m-portlet m-portlet--tabs">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-tools">
                                            <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
                                                <li class="nav-item m-tabs__item" style="padding-top: 20px;padding-bottom: 20px;">
                                                    <a class="btn btn-primary" id="create-quote" data-toggle="tab" href="#m_portlet_tab_1_2" role="tab">
                                                        Create Manual Quote
                                                    </a>
                                                    <a class="btn btn-primary" id="create-quote-back" style="display: none;" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                                        Back
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="m-portlet__body">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="m_portlet_tab_1_1">
                                                <div class="row">
                                                    <div class="col-lg-12">
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
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="m_portlet_tab_1_2">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group m-form__group row">
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Description:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input type="text" class="form-control origin_exp_description" id="origin_exp_description" name="origin_exp_description[]"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Detail:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input id="origin_exp_quantity" name="origin_exp_quantity[]" class="origin_exp_quantity form-control" type="text"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="input-group">
                                                                                <input type="number" id="origin_exp_amount" name="origin_exp_amount[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
                                                                                <div class="input-group-btn">
                                                                                    <div class="btn-group">
                                                                                        <select class="btn btn-default origin_exp_amount_currency" name="origin_currency[]">
                                                                                            <option value="usd">USD</option>
                                                                                            <option value="clp">CLP</option>
                                                                                            <option value="ars">ARS</option>
                                                                                            <option value="eur">EUR</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 conversion" >
                                                                        <label>
                                                                            Total Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input type="text" name="origin_exp_amount_usd[]"  class="origin_exp_amount_clp form-control" aria-label="...">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class='row hide' id="freight_ammounts">
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Description:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input type="text" class="form-control origin_exp_description" id="origin_exp_description" name="origin_exp_description[]"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Detail:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input id="origin_exp_quantity" name="origin_exp_quantity[]" class="origin_exp_quantity form-control" type="text"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="input-group">
                                                                                <input type="number" id="origin_exp_amount" name="origin_exp_amount[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
                                                                                <div class="input-group-btn">
                                                                                    <div class="btn-group">
                                                                                        <select class="btn btn-default origin_exp_amount_currency" name="origin_currency[]">
                                                                                            <option value="usd">USD</option>
                                                                                            <option value="clp">CLP</option>
                                                                                            <option value="ars">ARS</option>
                                                                                            <option value="eur">EUR</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 conversion" >
                                                                        <label>
                                                                            Total Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input type="text" name="origin_exp_amount_usd[]"  class="origin_exp_amount_clp form-control" aria-label="...">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label></label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="form-group">
                                                                                <div class="">
                                                                                    <a href="#" class="btn removeButton">
                                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class='row'>
                                                                    <div class="col-md-9"></div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <span>
                                                                                <h5>
                                                                                    Sub-Total:<span id="sub_total_origin">0.00</span>&nbsp;
                                                                                </h5>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <div class="form-group">
                                                                            <span>
                                                                                <a href="#" class="btn addButton" style="vertical-align: middle">
                                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="form-group m-form__group row">
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Description:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input type="text" class="form-control origin_exp_description" id="origin_exp_description" name="origin_exp_description[]"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Detail:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input id="origin_exp_quantity" name="origin_exp_quantity[]" class="origin_exp_quantity form-control" type="text"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="input-group">
                                                                                <input type="number" id="origin_exp_amount" name="origin_exp_amount[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
                                                                                <div class="input-group-btn">
                                                                                    <div class="btn-group">
                                                                                        <select class="btn btn-default origin_exp_amount_currency" name="origin_currency[]">
                                                                                            <option value="usd">USD</option>
                                                                                            <option value="clp">CLP</option>
                                                                                            <option value="ars">ARS</option>
                                                                                            <option value="eur">EUR</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 conversion" >
                                                                        <label>
                                                                            Total Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input type="text" name="origin_exp_amount_usd[]"  class="origin_exp_amount_clp form-control" aria-label="...">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class='row hide' id="destination_ammounts">
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Description:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input type="text" class="form-control origin_exp_description" id="origin_exp_description" name="origin_exp_description[]"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Detail:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <input id="origin_exp_quantity" name="origin_exp_quantity[]" class="origin_exp_quantity form-control" type="text"/>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <label>
                                                                            Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="input-group">
                                                                                <input type="number" id="origin_exp_amount" name="origin_exp_amount[]" min="1" step="0.01" class="origin_exp_amount form-control" aria-label="...">
                                                                                <div class="input-group-btn">
                                                                                    <div class="btn-group">
                                                                                        <select class="btn btn-default origin_exp_amount_currency" name="origin_currency[]">
                                                                                            <option value="usd">USD</option>
                                                                                            <option value="clp">CLP</option>
                                                                                            <option value="ars">ARS</option>
                                                                                            <option value="eur">EUR</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 conversion" >
                                                                        <label>
                                                                            Total Ammount:
                                                                        </label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input type="text" name="origin_exp_amount_usd[]"  class="origin_exp_amount_clp form-control" aria-label="...">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <label></label>
                                                                        <div class="m-bootstrap-touchspin-brand">
                                                                            <div class="form-group">
                                                                                <div class="">
                                                                                    <a href="#" class="btn removeButtonDestination">
                                                                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class='row'>
                                                                    <div class="col-md-9"></div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <span>
                                                                                <h5>
                                                                                    Sub-Total:<span id="sub_total_origin">0.00</span>&nbsp;
                                                                                </h5>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <div class="form-group">
                                                                            <span>
                                                                                <a href="#" class="btn addButtonDestination" style="vertical-align: middle">
                                                                                    <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                                                                                </a>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
