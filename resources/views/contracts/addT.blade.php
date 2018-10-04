@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Contracts')
@section('content')
@php
$validation_expire = 'Please enter validity date';
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
      {!! Form::open(['route' => 'contracts.store','class' => 'form-group m-form__group']) !!}
      @include('contracts.partials.form_contractsT')

      <div class="m-portlet m-portlet--tabs">
        <div class="m-portlet__head">
          <div class="m-portlet__head-tools">
            <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                  <i class="la la-cog"></i>
                  Ocean Freight
                </a>
              </li>
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link addCT" data-toggle="tab" href="#m_tabs_6_2" role="tab">
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
                      Destination Port
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
                    <th title="Field #6">
                      40'NOR
                    </th>
                    <th title="Field #6">
                      45'
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

                  <tr   id='tr_clone'  >
                    <td width = '15%'>{{ Form::select('origin_id[]', $harbor,null,['class'=>'m-select2-general  col-sm-6 form-control','style' => 'width:100%;']) }}</td>
                    <td  width = '15%'>{{ Form::select('destiny_id[]', $harbor,null,['class'=>'m-select2-general col-sm-6 form-control','style' => 'width:100%;']) }}</td>
                    <td  width = '10%'>{{ Form::select('carrier_id[]', $carrier,null,['class'=>'m-select2-general col-sm-6 form-control','style' => 'width:100%;']) }}</td>

                    <td  width = '10%'>{!! Form::number('twuenty[]', null, ['placeholder' => 'Enter 20','class' => 'form-control m-input','required' => 'required','style' => 'width:100%;' ,'min' => '0' ]) !!} </td>
                    <td  width = '10%'>{!! Form::number('forty[]', null, ['placeholder' => 'Enter 40','class' => 'form-control m-input','required' => 'required','style' => 'width:100%;' ,'min' => '0' ]) !!} </td>
                    <td  width = '10%'> {!! Form::number('fortyhc[]', null, ['placeholder' => 'Enter 40HC','class' => 'form-control m-input','required' => 'required','style' => 'width:100%;' ,'min' => '0' ]) !!}</td>
                    <td  width = '10%'> {!! Form::number('fortynor[]', null, ['placeholder' => 'Enter 40NOR','class' => 'form-control m-input','required' => 'required','style' => 'width:100%;' ,'min' => '0' ]) !!}</td>
                    <td  width = '10%'> {!! Form::number('fortyfive[]', null, ['placeholder' => 'Enter 45','class' => 'form-control m-input','required' => 'required','style' => 'width:100%;' ,'min' => '0' ]) !!}</td>
                    <td width = '10%'>{{ Form::select('currency_id[]', $currency,null,['class'=>'m-select2-general col-sm-6 form-control','style' => 'width:100%;']) }}</td>
                    <td>-</td>

                  </tr>



              </table>
            </div>
            <div class="tab-pane" id="m_tabs_6_2" role="tabpanel">

              <div class="row">
                <div class="col-md-2">
                  <a  id="new2" class="">
                    <button type="button" class="btn btn-brand">
                      Add New
                      <i class="fa fa-plus"></i>
                    </button>
                  </a>
                </div>

              </div>
              <table class="table m-table m-table--head-separator-primary" id="sample_editable_2" width="100%">
                <thead>
                  <tr>
                    <th title="Field #1">
                      Type
                    </th>
                    <th title="Field #2">
                      Origin Port
                    </th>
                    <th title="Field #2">
                      Destination Port
                    </th>
                    <th title="Field #3">
                      Charge Type
                    </th>
                    <th title="Field #4">
                      Carrier
                    </th>
                    <th title="Field #7">
                      Calculation type
                    </th>
                    <th title="Field #8">
                      Amount
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
                  <tr>
                    <td width='10%'>{{ Form::select('type[]', $surcharge,null,['class'=>'m-select2-general form-control type','style' => 'width:100%;']) }}</td>
                    <td width='15%'>{{ Form::select('port_origlocal1[]', $harbor,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                    <td width='12%'>{{ Form::select('port_destlocal1[]', $harbor,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                    <td width='10%'>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                    <td width='10%'>{{ Form::select('localcarrier_id1[]', $carrier,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                    <td width='11%'>{{ Form::select('calculationtype[]', $calculationT,null,['class'=>'m-select2-general form-control','style' => 'width:100%;']) }}</td>
                    <td width='10%'> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','style' => 'width:100%;']) !!}</td>
                    <td width='14%'>{{ Form::select('localcurrency_id[]', $currency,null,['class'=>'m-select2-general form-control','style' => 'width:100%;']) }}</td>
                    <td  width='8%'>-</td>

                  </tr>
                  <tr   id='tclone2' hidden="true" >
                    <td>{{ Form::select('type[]', $surcharge,null,['class'=>'form-control' ,'style' => 'width:100%;']) }}</td>
                    <td>{{ Form::select(null, $harbor,null,['class'=>'form-control portOrig' ,'multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                    <td>{{ Form::select(null, $harbor,null,['class'=>'form-control portDest' ,'multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                    <td>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                    <td>{{ Form::select(null, $carrier,null,['class'=>'form-control carrier','multiple' => 'multiple','style' => 'width:100%;']) }}</td>

                    <td>{{ Form::select('calculationtype[]', $calculationT,null,['class'=>'form-control','style' => 'width:100%;']) }}</td>
                    <td> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the ammount','class' => 'form-control m-input','style' => 'width:100%;']) !!}</td>
                    <td>{{ Form::select('localcurrency_id[]', $currency,null,['class'=>'form-control','style' => 'width:100%;']) }}</td>
                    <td>  <a  class="removeL m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                      <i class="la la-eraser"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="tab-pane" id="m_tabs_6_3" role="tabpanel">
              <div class="row">
                <div class="col-md-12" id="origin_harbor_label">
                  <label>Company</label>
                  <div class="form-group m-form__group align-items-center">
                    {{ Form::select('companies[]',$companies,null,['multiple','class'=>'m-select2-general','id' => 'm-select2-company']) }}
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12" id="origin_harbor_label">
                  <label>Users</label>
                  <div class="form-group m-form__group align-items-center">
                    {{ Form::select('users[]',$users,null,['multiple','class'=>'m-select2-general','id' => 'm-select2-client']) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
              {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
              <a class="btn btn-danger" href="{{url()->previous()}}">
                Cancel
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    {!! Form::close() !!}
    <table  hidden="true">
      <tr   id='tclone' hidden="true" >
        <td>{{ Form::select('origin_id[]', $harbor,null,['class'=>'col-sm-10 form-control','style' => 'width:100%;']) }}</td>
        <td>{{ Form::select('destiny_id[]', $harbor,null,['class'=>'col-sm-10 form-control','style' => 'width:100%;']) }}</td>
        <td>{{ Form::select('carrier_id[]', $carrier,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>

        <td>{!! Form::number('twuenty[]', null, ['placeholder' => 'Enter 20','class' => 'form-control m-input','style' => 'width:100%;' ,'min' => '0' ]) !!} </td>
        <td>{!! Form::number('forty[]', null, ['placeholder' => 'Enter 40','class' => 'form-control m-input','style' => 'width:100%;' ,'min' => '0' ]) !!} </td>
        <td> {!! Form::number('fortyhc[]', null, ['placeholder' => 'Enter 40HC','class' => 'form-control m-input','style' => 'width:100%;' ,'min' => '0' ]) !!}</td>
        <td> {!! Form::number('fortynor[]', null, ['placeholder' => 'Enter 40NOR','class' => 'form-control m-input','style' => 'width:100%;' ,'min' => '0' ]) !!}</td>
        <td> {!! Form::number('fortyfive[]', null, ['placeholder' => 'Enter 45','class' => 'form-control m-input','style' => 'width:100%;' ,'min' => '0' ]) !!}</td>
        <td>{{ Form::select('currency_id[]', $currency,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
        <td>   <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " >
          <i class="la la-eraser"></i>
          </a>
        </td>

      </tr>
    </table>
    <!--end: Form Wizard-->
  </div>
  <!--End::Main Portlet-->
</div>

@endsection

@section('js')
@parent

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/addcontracts.js"></script>

@stop
