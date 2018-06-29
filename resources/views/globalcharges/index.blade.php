@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('title', 'Global Charges')
@section('content')



<div class="m-content">
  <div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
          <h3 class="m-portlet__head-text">
            List  Global Charges 
          </h3>
        </div>
      </div>
    </div>



    <div class="m-portlet m-portlet--tabs">
      <div class="m-portlet__head">
        <div class="m-portlet__head-tools">
          <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
            <li class="nav-item m-tabs__item">
              <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                <i class="la la-cog"></i>
                List Global Charge
              </a>
            </li>
            <li class="nav-item m-tabs__item">
              <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                <i class="la la-briefcase"></i>
                Add Global Charge
              </a>
            </li>

          </ul>
        </div>
      </div>

      <div class="m-portlet__body">
        <div class="tab-content">
          <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">


            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
              <div class="row align-items-center">

                <div class="new col-xl-12 order-1 order-xl-2 m--align-right">

                  <div class="m-separator m-separator--dashed d-xl-none"></div>
                </div>
              </div>
            </div>
            <table class="table m-table m-table--head-separator-primary" id="sample_editable_1" width="100%">
              <thead>
                <tr>
                  <th title="Field #1">
                    Type
                  </th>
                  <th title="Field #2">
                    Origin Port
                  </th>
                  <th title="Field #2">
                    Destiny Port
                  </th>
                  <th title="Field #3">
                    Changetype
                  </th>
                  <th title="Field #4">
                    Carrier
                  </th>
                  <th title="Field #7">
                    Calculation type
                  </th>
                  <th title="Field #8">
                    Ammount
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

                @foreach ($global as $globalcharges)
                <tr id='tr_l{{++$loop->index}}'>
                  <td>
                    <div id="divtype{{$loop->index}}"  class="val">{!! $globalcharges->surcharge->name !!}</div>
                    <div class="in" hidden="true">
                      {{ Form::select('type[]', $surcharge,$globalcharges->surcharge_id,['id' => 'type'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true']) }}
                    </div>
                  </td>
                  <td>
                    <div id="divport{{$loop->index}}"  class="val">

                      {!! str_replace(["[","]","\""], ' ', $globalcharges->globalcharport->pluck('portOrig')->unique()->pluck('name') ) !!} 
                    </div>

                    <div class="in" hidden="true">
                      {{ Form::select('port_orig[]', $harbor,
                      $globalcharges->globalcharport->pluck('portOrig')->unique()->pluck('id'),['id' => 'port_orig'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true','multiple' => 'multiple']) }}
                    </div>
                  </td>
                  <td>
                    <div id="divportDest{{$loop->index}}"  class="val">

                      {!! str_replace(["[","]","\""], ' ', $globalcharges->globalcharport->pluck('portDest')->unique()->pluck('name') ) !!} 
                    </div>

                    <div class="in" hidden="true">
                      {{ Form::select('port_dest[]', $harbor,
                      $globalcharges->globalcharport->pluck('portDest')->unique()->pluck('id'),['id' => 'port_dest'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true','multiple' => 'multiple']) }}
                    </div>
                  </td>
                  <td>

                    <div id="divchangetype{{$loop->index}}"  class="val">{!! $globalcharges->typedestiny->description !!}</div>
                    <div class="in" hidden="true">
                      {{ Form::select('changetype[]',$typedestiny, $globalcharges->typedestiny_id,['id' => 'changetype'.$loop->index ,'class'=>'m-select2-general form-control','disabled' => 'true']) }}

                    </div>
                  </td>
                  <td>
                    <div id="divcarrier{{$loop->index}}"  class="val">

                      {!! str_replace(["[","]","\""], ' ', $globalcharges->GlobalCharCarrier->pluck('carrier')->pluck('name') ) !!}

                    </div>
                    <div class="in" hidden="true">
                      {{ Form::select('localcarrier_id[]', $carrier,$globalcharges->globalcharcarrier->pluck('carrier_id'),['id' => 'localcarrier'.$loop->index ,'class'=>'m-select2-general form-control','disabled' => 'true','multiple' => 'multiple']) }}
                    </div>
                  </td>
                  <td>   
                    <div id="divcalculation{{$loop->index}}"  class="val">{!! $globalcharges->calculationtype->name !!}</div>
                    <div class="in" hidden="true">
                      {{ Form::select('calculationtype[]', $calculationT,$globalcharges->calculationtype_id,['id' => 'calculationtype'.$loop->index ,'class'=>'m-select2-general form-control ','disabled' => 'true']) }}
                    </div> 
                  <td> 
                    <div id="divammount{{$loop->index}}" class="val"> {!! $globalcharges->ammount !!} </div>
                    <div class="in" hidden="    true"> {!! Form::text('ammount[]', $globalcharges->ammount, ['id' => 'ammount'.$loop->index ,'placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','disabled' => 'true']) !!}</div> 
                  </td>
                  <td>
                    <div id="divcurrency{{$loop->index}}"  class="val"> {!! $globalcharges->currency->alphacode !!} </div>
                    <div class="in" hidden="true">
                      {{ Form::select('localcurrency_id[]', $currency,$globalcharges->currency_id,['id' => 'localcurrency'.$loop->index ,'class'=>'m-select2-general form-control' ,'disabled' => 'true']) }}
                    </div> 
                  </td>
                  <td>
                    <a  id='edit_l{{$loop->index}}' onclick="display_l({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                      <i class="la la-edit"></i>
                    </a>

                    <a  id='save_l{{$loop->index}}' onclick="save_l({{$loop->index}},{{$globalcharges->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                      <i class="la la-save"></i>
                    </a>
                    <a  id='remove_l{{$loop->index}}'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" hidden="true">
                      <i id='rm_l{{$globalcharges->id}}' class="la la-times-circle"></i>
                    </a>

                    <a  id='cancel_l{{$loop->index}}' onclick="cancel_l({{$loop->index}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                      <i  class="la la-reply"></i>
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="tab-pane " id="m_tabs_6_2" role="tabpanel">
            {!! Form::open(['route' => 'globalcharges.store','class' => 'form-group m-form__group']) !!}
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
              <div class="row align-items-center">

                <div class="new col-xl-12 order-1 order-xl-2 m--align-right">
                  <a >
                    <button id="new" type="button" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                      <span>
                        <i class="la la-user"></i>
                        <span>
                          Add Charge
                        </span>
                      </span>
                    </button>
                  </a>
                  <div class="m-separator m-separator--dashed d-xl-none"></div>
                </div>
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
                    Destiny Port
                  </th>
                  <th title="Field #3">
                    Changetype
                  </th>
                  <th title="Field #4">
                    Carrier
                  </th>
                  <th title="Field #7">
                    Calculation type
                  </th>
                  <th title="Field #8">
                    Ammount
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
                  <td width='10%'>{{ Form::select('type[]', $surcharge,null,['class'=>'m-select2-general form-control','style' => 'width:100%;']) }}</td>
                  <td width='15%'>{{ Form::select('port_orig1[]', $harbor,null,['class'=>'m-select2-general form-control port_orig','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                  <td width='15%'>{{ Form::select('port_dest1[]', $harbor,null,['class'=>'m-select2-general form-control port_dest','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                  <td width='15%'>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'m-select2-general form-control','style' => 'width:100%;']) }}</td>
                  <td width='15%'> {{ Form::select('localcarrier1[]', $carrier,null,['class'=>'m-select2-general form-control carrier','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                  <td width='15%'>{{ Form::select('calculationtype[]', $calculationT,null,['class'=>'m-select2-general form-control','style' => 'width:100%;']) }}</td>
                  <td width='15%'> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','style' => 'width:100%;']) !!}</td>
                  <td width='15%'>{{ Form::select('localcurrency_id[]', $currency,null,['class'=>'m-select2-general form-control','style' => 'width:100%;']) }}</td>
                  <td width='15%'>  -
                  </td>
                </tr>
              </tbody>
            </table>

            <table hidden="true">
              <tr  id='globalclone' hidden="true" >
                <td width='10%'>{{ Form::select('type[]', $surcharge,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                <td width='15%'>{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control port_orig','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                <td width='15%' >{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control port_dest','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                <td width='15%'>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                <td width='15%'>{{ Form::select(null, $carrier,null,['class'=>'custom-select form-control carrier','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                <td width='15%'>{{ Form::select('calculationtype[]', $calculationT,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                <td width='15%'> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input','style' => 'width:100%;']) !!}</td>
                <td width='15%'>{{ Form::select('localcurrency_id[]', $currency,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                <td width='15%'>  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                  <i class="la la-eraser"></i>
                  </a>
                </td>
              </tr>
            </table>
            
            <div class="m-portlet__foot m-portlet__foot--fit">
              <div id="button"  class="m-form__actions m-form__actions">
                {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                <a class="cancel btn btn-success">
                  Cancel
                </a>
              </div>
            </div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection

@section('js')
@parent

<script src="/js/globalcharges.js"></script>
<script src="/assets/plugins/table-datatables-editable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.bootstrap.js" type="text/javascript"></script>


@stop
