@php
$validation_expire = $inland->validity ." / ". $inland->expire ;
@endphp
@extends('layouts.app')
@section('title', 'Edit Inland')
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
      {!! Form::model($inland, ['route' => ['inlands.update', setearRouteKey($inland->id)], 'method' => 'PUT','class' => 'form-group m-form__group']) !!}


      <div class="form-group m-form__group row">
        <div class="col-lg-3">
          {!! Form::label('provider', 'Provider') !!}
          {!! Form::text('provider', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']) !!}
        </div>
        <div class="col-lg-3">
          {!! Form::label('ports', 'Port') !!}
          {{ Form::select('inlandport[]', $harbor,$inland->inlandports->pluck('port'),['class'=>'m-select2-general form-control port','multiple' => 'multiple']) }}
        </div>
        <div class="col-lg-3">
          {!! Form::label('validation_expire', 'Validation') !!}
          {!! Form::text('validation_expire', $validation_expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
        </div>
        <div class="col-lg-3">
          {!! Form::label('change', 'Change Type') !!}<br>
          {{ Form::select('type',['1' => 'Export','2' => 'Import','3'=>'All'],null,['class'=>'m-select2-general form-control']) }}
        </div>
      </div>
      <div class="form-group m-form__group row">
        <div class="col-lg-3">
          {!! Form::label('KM 20', 'Charge for 20') !!}
          {!! Form::number('km_20',$inland->inlandadditionalkms->km_20, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0']) !!}
        </div>
        <div class="col-lg-3">
          {!! Form::label('KM 40', 'Charge for 40') !!}
          {!! Form::number('km_40',$inland->inlandadditionalkms->km_40, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0']) !!}
        </div>
        <div class="col-lg-3">
          {!! Form::label('KM 40 HC', 'Charge for 40HC') !!}
          {!! Form::number('km_40hc',$inland->inlandadditionalkms->km_40hc, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0' ]) !!}
        </div>
        <div class="col-lg-3">
          <label>Company Restriction</label>
          <div class="form-group m-form__group align-items-center">
            {{ Form::select('companies[]',$companies,$inland->inland_company_restriction->pluck('company_id'),['multiple','class'=>'m-select2-general','id' => 'm-select2-company']) }}
          </div>
        </div>
      </div>
      <div class="form-group m-form__group row">
        <div class="col-lg-3">
          {!! Form::label('Charge Currency', 'Charge Currency') !!}
          {{ Form::select('chargecurrencykm',$currency,$inland->inlandadditionalkms->currency_id,['class'=>'custom-select form-control','id' => '']) }}
        </div>
      </div>


      <hr>
      <!--begin: Form Wizard-->
      <div class="m-portlet m-portlet--tabs">
        <div class="m-portlet__head">
          <div class="m-portlet__head-tools">
            <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">

              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link addS active" data-toggle="tab" href="#m_tabs_1" role="tab">

                  Inland charge for 20
                </a>
              </li>
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link tabrates" data-toggle="tab" href="#m_tabs_2" role="tab">

                  Inland charge for 40
                </a>
              </li>
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link tabrates" data-toggle="tab" href="#m_tabs_3" role="tab">

                  Inland charge for 40 HC
                </a>
              </li>

            </ul>
          </div>
        </div>
      </div>
      <div class="tab-content">
        <div class="tab-pane active " id="m_tabs_1" role="tabpanel">
          <div class="m-portlet__body">
            <div class="">

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
                        <a  id='newtwuenty' class="m-portlet__nav-link btn btn-btn btn-primary m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill">
                          <i class="la la-plus"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="text-center" style="font-size: 11px !important;">

                  <table id='twuenty' class=" table table-condensed col-lg-12">
                    <thead>
                      <tr>
                        <th id="lower-limit-fcl"> <span><b>Lower limit (KM)</b></span></th>
                        <th id="upper-limit-fcl">  <span><b>Upper limit (KM)</b></span></th>
                        <th id="rate-limit-fcl"><span><b>Rate Per<br> Container</b></span></th>
                        <th id="options-limit-fcl"><span><b>Options</b></span></th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $contTwuenty = 0; 
                      @endphp
                      @foreach($inland->inlanddetails as $inlanddetails)

                      @if($inlanddetails->type == "twuenty")
                      <tr id='tr_twuenty{{++$contTwuenty}}'>
                        <td  width="20%"  >
                          <div id="divlowertwuenty{{$contTwuenty}}" class="val">
                            {{ $inlanddetails->lower }}
                          </div>
                          <div class="in" hidden="    true">
                            {!! Form::text('lowertwuenty[]', $inlanddetails->lower, ['id' => 'lo20'.$contTwuenty ,'placeholder' => '0','class' => 'col-lg-12 form-control m-input low20 cloLow20','disabled' => 'true','style'=>'width:100%' ,'onblur' => 'validateRange(this.id,\'t20ELOW\')']) !!}
                          </div>
                        </td>
                        <td width="20%" >
                          <div id="divuppertwuenty{{$contTwuenty}}" class="val">
                            {{ $inlanddetails->upper }}
                          </div>
                          <div class="in" hidden="    true">
                            {!! Form::text('uppertwuenty[]', $inlanddetails->upper, ['id' => 'up20'.$contTwuenty ,'placeholder' => '50','class' => ' col-lg-12 form-control m-input up20 cloUp20','disabled' => 'true','style'=>'width:100%','onblur' => 'validateRange(this.id,\'t20EUP\')']) !!}
                          </div>


                        </td>
                        <td width="30%"  >
                          <div id="divammounttwuenty{{$contTwuenty}}" class="val">
                            {{ $inlanddetails->ammount }} /
                            {{ $inlanddetails->currency->alphacode }}
                          </div>
                          <div class="in" hidden="    true">
                            <div class="input-group">
                              {!! Form::number('ammounttwuenty[]', $inlanddetails->ammount, ['id' => 'ammounttwuenty'.$contTwuenty ,'placeholder' => '50','class' => 'form-control m-input','disabled' => 'true','style'=>'width:50%']) !!}
                              <div class="input-group-btn">
                                <div class="btn-group">
                                  {{ Form::select('currencytwuenty[]',$currency,$inlanddetails->currency_id,['id' =>    'currencytwuenty'.$contTwuenty ,'class'=>'custom-select form-control col-lg-12','disabled' => 'true']) }}
                                </div>
                              </div>
                            </div>
                          </div>

                        </td>

                        <td  width="20%" >
                          <a  id='edit_twuenty{{$contTwuenty}}' onclick="display_twuenty({{$contTwuenty}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                            <i class="la la-edit"></i>
                          </a>

                          <a  id='save_twuenty{{$contTwuenty}}' onclick="save_twuenty({{$contTwuenty}},{{$inlanddetails->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                            <i class="la la-save"></i>
                          </a>
                          <a  id='remove_twuenty{{$contTwuenty}}'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" hidden="true">
                            <i id='rm_l{{$inlanddetails->id}}' class="la la-times-circle"></i>
                          </a>

                          <a  id='cancel_twuenty{{$contTwuenty}}' onclick="cancel_twuenty({{$contTwuenty}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                            <i  class="la la-reply"></i>
                          </a>
                        </td>
                      </tr>
                     
                      @endif
                      @endforeach
                    </tbody>
                  </table>

                  <table hidden="true">
                    <tr id="twuentyclone">
                      <td width="20%"> {!! Form::text('lowertwuenty[]', null, ['placeholder' => '0','class' => 'form-control m-input low cloLow20','style'=>'width:100%']) !!}</td>
                      <td width="20%">         {!! Form::text('uppertwuenty[]', null, ['placeholder' => '50','class' => 'form-control m-input up cloUp20','style'=>'width:100%']) !!}</td>
                      <td  width="30%">
                        <div class="input-group">
                          {!! Form::number('ammounttwuenty[]', null, ['placeholder' => '50','class' => 'form-control m-input','style'=>'width:50%']) !!}
                          <div class="input-group-btn">
                            <div class="btn-group">
                              {{ Form::select('currencytwuenty[]',$currency,null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td width="20%">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                        <i class="la la-eraser"></i>
                        </a>
                      </td>

                    </tr>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="tab-pane " id="m_tabs_2" role="tabpanel">
          <div class="m-portlet__body">
            <div class="">
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
                        <a  id='newforty' class="m-portlet__nav-link btn btn-btn btn-primary m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill">
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
                        <th ><span><b>Options</b></span></th>


                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $contforty = 0; 
                      @endphp
                      @foreach($inland->inlanddetails as $inlanddetails)
                      @if($inlanddetails->type == "forty")
                      <tr id='tr_forty{{++$contforty}}'>
                        <td  width="20%">

                          <div id="divlowerforty{{$contforty}}" class="val">
                            {{ $inlanddetails->lower }}
                          </div>
                          <div class="in" hidden="    true">
                            {!! Form::text('lowerforty[]', $inlanddetails->lower, ['id' => 'lo40'.$contforty ,'placeholder' => '0','class' => 'col-lg-12 form-control m-input low40','disabled' => 'true', 'onblur' => 'validateRange40(this.id,\'t40ELOW\')']) !!}
                          </div>
                        </td>
                        <td width="20%">
                          <div id="divupperforty{{$contforty}}" class="val">
                            {{ $inlanddetails->upper }}
                          </div>
                          <div class="in" hidden="    true">
                            {!! Form::text('upperforty[]', $inlanddetails->upper, ['id' => 'up40'.$contforty ,'placeholder' => '50','class' => ' col-lg-12 form-control m-input up up40','disabled' => 'true','onblur' => 'validateRange40(this.id,\'t40EUP\')','style' => 'width:100%']) !!}
                          </div>


                        </td>
                        <td  width="30%" >
                          <div id="divammountforty{{$contforty}}" class="val">
                            {{ $inlanddetails->ammount }} /
                            {{ $inlanddetails->currency->alphacode }}
                          </div>


                          <div class="in" hidden="    true">
                            <div class="input-group">
                              {!! Form::number('ammountforty[]', $inlanddetails->ammount, ['id' => 'ammountforty'.$contforty ,'placeholder' => '50','class' => 'form-control m-input','disabled' => 'true','style'=>'width:50%']) !!}
                              <div class="input-group-btn">
                                <div class="btn-group">
                                  {{ Form::select('currencyforty[]',$currency,$inlanddetails->currency_id,['id' =>    'currencyforty'.$contforty ,'class'=>'custom-select form-control col-lg-12','disabled' => 'true']) }}
                                </div>
                              </div>
                            </div>
                          </div>

                        </td>

                        <td width="20%" >
                          <a  id='edit_forty{{$contforty}}' onclick="display_forty({{$contforty}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                            <i class="la la-edit"></i>
                          </a>

                          <a  id='save_forty{{$contforty}}' onclick="save_forty({{$contforty}},{{$inlanddetails->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                            <i class="la la-save"></i>
                          </a>
                          <a  id='remove_forty{{$contforty}}'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" hidden="true">
                            <i id='rm_l{{$inlanddetails->id}}' class="la la-times-circle"></i>
                          </a>

                          <a  id='cancel_forty{{$contforty}}' onclick="cancel_forty({{$contforty}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                            <i  class="la la-reply"></i>
                          </a>
                        </td>
                      </tr>
                      @endif
                      @endforeach
                    </tbody>
                  </table>


                  <table hidden="true">
                    <tr id="fortyclone">
                      <td  width="20%"> {!! Form::text('lowerforty[]', null, ['placeholder' => '0','class' => 'form-control m-input  low
                        cloLow40','style'=>'width:100%']) !!}</td>
                      <td  width="20%">         {!! Form::text('upperforty[]', null, ['placeholder' => '50','class' => ' form-control m-input  up cloUp40','style' => 'width:100%']) !!}</td>
                      <td   width="30%">
                        <div class="input-group">
                          {!! Form::number('ammountforty[]', null, ['placeholder' => '50','class' => ' form-control m-input','style'=>'width:50%']) !!}
                          <div class="input-group-btn">
                            <div class="btn-group">
                              {{ Form::select('currencyforty[]',$currency,null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td width="20%">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                        <i class="la la-eraser"></i>
                        </a>
                      </td>

                    </tr>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="tab-pane " id="m_tabs_3" role="tabpanel">
          <div class="m-portlet__body">
            <div class="">
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
                        <a  id='newfortyhc' class="m-portlet__nav-link btn btn-primary m-btn m-btn--hover-brand m-btn--icon m-btn--icon-only m-btn--pill">
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
                        <th ><span><b>Options</b></span></th>

                      </tr>
                    </thead>
                    <tbody>
                      @php
                      $contfortyH = 0; 
                      @endphp
                      @foreach($inland->inlanddetails as $inlanddetails)
                      @if($inlanddetails->type == "fortyhc")
                      <tr id='tr_fortyhc{{++$contfortyH}}'>
                        <td width="20%" >

                          <div id="divlowerfortyhc{{$contfortyH}}" class="val">
                            {{ $inlanddetails->lower }}
                          </div>
                          <div class="in" hidden="    true">
                            {!! Form::text('lowerfortyhc[]', $inlanddetails->lower, ['id' => 'lo40H'.$contfortyH ,'placeholder' => '0','class' => 'col-lg-12 form-control m-input low40H','disabled' => 'true','style'=>'width:100%','onblur' => 'validateRange40hc(this.id,\'t40ELOWH\')']) !!}
                          </div>
                        </td>

                        <td width="20%" >
                          <div id="divupperfortyhc{{$contfortyH}}" class="val">
                            {{ $inlanddetails->upper }}
                          </div>
                          <div class="in" hidden="    true">
                            {!! Form::text('upperfortyhc[]', $inlanddetails->upper, ['id' => 'up40H'.$contfortyH ,'placeholder' => '50','class' => ' col-lg-12 form-control m-input','disabled' => 'true','style'=>'width:100%','onblur' => 'validateRange40hc(this.id,\'t40EUPH\')']) !!}
                          </div>


                        </td>
                        <td  width="30%" >
                          <div id="divammountfortyhc{{$contfortyH}}" class="val">
                            {{ $inlanddetails->ammount }} /
                            {{ $inlanddetails->currency->alphacode }}
                          </div>
                          <div class="in" hidden="    true">
                            <div class="input-group">
                              {!! Form::number('ammountfortyhc[]', $inlanddetails->ammount, ['id' => 'ammountfortyhc'.$contfortyH ,'placeholder' => '50','class' => 'form-control m-input' ,'disabled' => 'true','style'=>'width:50%']) !!}
                              <div class="input-group-btn">
                                <div class="btn-group">
                                  {{ Form::select('currencyfortyhc[]',$currency,$inlanddetails->currency_id,['id' =>    'currencyfortyhc'.$contfortyH ,'class'=>'custom-select form-control col-lg-12' ,'disabled' => 'true']) }}
                                </div>
                              </div>
                            </div>
                          </div>

                        </td>

                        <td width="20%">
                          <a  id='edit_fortyhc{{$contfortyH}}' onclick="display_fortyhc({{$contfortyH}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                            <i class="la la-edit"></i>
                          </a>

                          <a  id='save_fortyhc{{$contfortyH}}' onclick="save_fortyhc({{$contfortyH}},{{$inlanddetails->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                            <i class="la la-save"></i>
                          </a>
                          <a  id='remove_fortyhc{{$contfortyH}}'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" hidden="true">
                            <i id='rm_l{{$inlanddetails->id}}' class="la la-times-circle"></i>
                          </a>

                          <a  id='cancel_fortyhc{{$contfortyH}}' onclick="cancel_fortyhc({{$contfortyH}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                            <i  class="la la-reply"></i>
                          </a>
                        </td>
                      </tr>
                      @endif
                      @endforeach
                    </tbody>
                  </table>



                  <table hidden="true">
                    <tr id="fortyhcclone">
                      <td width="20%"> {!! Form::text('lowerfortyhc[]', null, ['placeholder' => '0','class' => 'col-lg-12 form-control m-input low cloLow40H ','style'=>'width:100%'  ]) !!}</td>
                      <td width="20%">         {!! Form::text('upperfortyhc[]', null, ['placeholder' => '50','class' => ' col-lg-12 form-control m-input up cloUp40H','style'=>'width:100%']) !!}</td>
                      <td   width="30%">
                        <div class="input-group">
                          {!! Form::number('ammountfortyhc[]', null, ['placeholder' => '50','class' => 'form-control m-input','style'=>'width:50%']) !!}
                          <div class="input-group-btn">
                            <div class="btn-group">
                              {{ Form::select('currencyfortyhc[]',$currency,null,['class'=>'custom-select form-control col-lg-12','id' => '']) }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td width="20%">  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                        <i class="la la-eraser"></i>
                        </a>
                      </td>

                    </tr>
                  </table>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="m-portlet__foot m-portlet__foot--fit">
        <br>
        <div class="m-form__actions">
          <button type="submit" class="btn btn-primary">
            Submit
          </button>
          <button type="reset" class="btn btn-danger">
            Cancel
          </button>
        </div>
      </div>


      <!--end: Form Wizard-->
      {!! Form::close() !!}
    </div>

  </div>
</div>
@endsection

@section('js')
@parent
<script src="/js/inlands.js"></script>
<script src="/assets/demo/default/custom/components/forms/wizard/wizard_edit.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
@stop