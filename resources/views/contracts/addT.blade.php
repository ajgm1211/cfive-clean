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

@section('title', 'Contracts')
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
            Create contract
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
      {!! Form::open(['route' => 'contracts.store','class' => 'form-group m-form__group', 'files' => true, 'enctype' => 'multipart/form-data','id'=>'formu']) !!}
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
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_4" role="tab">
                  <i class="la la-edit"></i>
                  Remarks
                </a>
              </li>
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_5" role="tab">
                  <i class="la la-file"></i>
                  Files
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="container-fluid mt-4">
          <div class="tab-content">
            <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
              <a  id="new" class="">
                <button type="button" class="btn btn-brand">
                  Add New
                  <i class="fa fa-plus"></i>
                </button>
              </a>
              <hr>
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
                      <th title="Field #7" style="padding: 0.75rem 0.2rem;">
                        Schedule Type
                      </th>
                      <th title="Field #7" style="width:7%; padding: 0.75rem 0.2rem;">
                        TT
                      </th>
                      <th title="Field #7" style="width:10%; padding: 0.75rem 0.2rem;">
                        Via
                      </th>
                      <th title="Field #7">

                      </th>
                    </tr>
                  </thead>
                  <tbody>

                    <tr id='tr_clone'  >
                      <td style="padding: 0.75rem 0.2rem;">{{ Form::select('origin_id1[]', $harbor,null,['class'=>'m-select2-general  col-sm-6 form-control ','style' => 'width:100%;' ,'multiple' => 'multiple','required'=>'true']) }}</td>
                      <td style="padding: 0.75rem 0.2rem;">{{ Form::select('destiny_id1[]', $harbor,null,['class'=>'m-select2-general col-sm-6 form-control ','style' => 'width:100%;' ,'multiple' => 'multiple','required'=>'true']) }}</td>
                      <td style="padding: 0.75rem 0.2rem;">{{ Form::select('carrier_id[]', $carrier,null,['class'=>'m-select2-general col-sm-6 form-control','style' => 'width:100%;','required'=>'true']) }}</td>
                      <td style="padding: 0.75rem 0.2rem;">{!! Form::number('twuenty[]', 0, ['placeholder' => 'Enter 20','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;']) !!} </td>
                      <td style="padding: 0.75rem 0.2rem;">{!! Form::number('forty[]', 0, ['placeholder' => 'Enter 40','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ]) !!} </td>
                      <td style="padding: 0.75rem 0.2rem;"> {!! Form::number('fortyhc[]', 0, ['placeholder' => 'Enter 40HC','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ]) !!}</td>
                      <td style="padding: 0.75rem 0.2rem;"> {!! Form::number('fortynor[]', 0, ['placeholder' => 'Enter 40NOR','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ]) !!}</td>
                      <td style="padding: 0.75rem 0.2rem;"> {!! Form::number('fortyfive[]', 0, ['placeholder' => 'Enter 45','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;']) !!}</td>
                      <td style="padding: 0.75rem 0.2rem;">{{ Form::select('currency_id[]', $currency,@$currency_cfg->id,['class'=>'m-select2-general col-sm-6 form-control','style' => 'width:100%;']) }}</td>
                      <td style="padding: 0.75rem 0.2rem;">{{ Form::select('scheduleT[]',$scheduleT,null,['class'=>'m-select2-general col-sm-6 form-control','style' => 'width:100%;']) }}</td>
                      <td style="padding: 0.75rem 0.2rem;"> {!! Form::number('transitTi[]', 0, ['placeholder' => 'Enter Days','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ,'min' => '0' ]) !!}</td>
                      <td style="padding: 0.75rem 0.2rem;"> {!! Form::text('via[]', null, ['placeholder' => 'Enter via','class' => 'form-control m-input','style' => 'width:100%; font-size: 11px;' ]) !!}</td>
                      <td style="padding: 0.75rem 0.2rem;">-</td>
                    </tr>
                </table>
              </div>
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
              <br><br>
              <div class="table-responsive"> 
                <table cellpadding='4' cellspacing='4' class="col-md-12  m-table m-table--head-separator-primary" id="sample_editable_2" width="100%">
                  <thead>
                    <tr>
                      <th title="Field #0">
                        Type Route
                      </th>
                      <th title="Field #1">
                        Type
                      </th>
                      <th title="Field #2">
                        Origin
                      </th>
                      <th title="Field #2">
                        Destination
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
                      <td width='10%'>
                        <div class="m-radio-inline">
                          <label class="m-radio">
                            <input type="radio" onclick="activarCountry('divport','1')" checked='true' name="typeroute1"  value="port"> Port
                            <span></span>
                          </label>
                          <label class="m-radio">
                            <input type="radio"  onclick="activarCountry('divcountry','1')"  name="typeroute1" value="country"> Country
                            <span></span>
                          </label>

                        </div>
                      </td>
                      <td width='10%'>{{ Form::select('type[]', $surcharge,null,['class'=>'m-select2-general form-control type','style' => 'width:100%;']) }}</td>
                      <td width='15%'>
                        <div class="divport1" >
                          {{ Form::select('port_origlocal1[]', $harbor,null,['class'=>'m-select2-general form-control col-lg-7','multiple' => 'multiple','style' => 'width:70%;']) }}
                        </div>
                        <div class="divcountry1" hidden="true">
                          {{ Form::select('country_orig1[]', $country,
                          null,['class'=>'m-select2-general form-control col-lg-7' ,'multiple' => 'multiple','style' => 'width:70%;']) }}
                        </div>
                      </td>
                      <td width='12%'>
                        <div class="divport1" >
                          {{ Form::select('port_destlocal1[]', $harbor,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','style' => 'width:70%;']) }}
                        </div>
                        <div class="divcountry1" hidden="true">
                          {{ Form::select('country_dest1[]', $country,
                          null,['class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple','style' => 'width:70%;']) }}
                        </div>
                      </td>
                      <td width='10%'>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                      <td width='10%'>{{ Form::select('localcarrier_id1[]', $carrier,null,['class'=>'m-select2-general form-control','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                      <td width='11%'>{{ Form::select('calculationtype1[]', $calculationT,null,['class'=>'m-select2-general form-control','style' => 'width:100%;','multiple' => 'multiple']) }}</td>
                      <td width='10%'> {!! Form::number('ammount[]', null, ['placeholder' => 'Please enter the amount','class' => 'form-control m-input','step'=>'0.01','style' => 'width:100%;']) !!}</td>
                      <td width='14%'>{{ Form::select('localcurrency_id[]', $currency,@$currency_cfg->id,['class'=>'m-select2-general form-control','style' => 'width:100%;']) }}</td>
                      <td  width='8%'>-</td>

                    </tr>

                  </tbody>
                </table>
              </div>
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
            <div class="tab-pane" id="m_tabs_6_4" role="tabpanel">
              <div class="form-group m-form__group row">
                <div class="col-lg-12">
                  {!! Form::label('remarks', 'Remarks') !!}
                  {!! Form::textarea('remarks', null, ['placeholder' => '','class' => 'form-control m-input','rows'=>4]) !!}
                </div>
              </div>
            </div>
            <div class="tab-pane" id="m_tabs_6_5" role="tabpanel">

              <div class="row">
                <div class="col-lg-12">


                  <div class="m-portlet__body">
                    <!--begin::Section-->
                    <div class="m-section m-section--last">
                      <div class="m-section__content">
                        <!--begin::Preview-->
                        <div class="m-demo">
                          <div class="m-demo__preview">



                            <div class="col-lg-8 ">
                              <div class="m-dropzone dropzone m-dropzone--success"  id="document-dropzone">
                                <div class="m-dropzone__msg dz-message needsclick">
                                  <h3 class="m-dropzone__msg-title">
                                    Drop files here or click to upload.
                                  </h3>
                                  <span class="m-dropzone__msg-desc">
                                    Only image, pdf and psd files are allowed for upload
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
          <br>
          <div class="m-portlet__foot m-portlet__foot--fit">
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
    <!-- Tabla que contiene el tr que se clona de los localcharges -->
    <table>
      <tr id='tclone2' hidden="true" >
        <td>
          <div class="m-radio-inline">
            <label class="m-radio">
              <input type="radio" checked='true'  class="rdrouteP"  value="port"> Port
              <span></span>
            </label>
            <label class="m-radio">
              <input type="radio" class="rdrouteC"  value="country"> Country
              <span></span>
            </label>
          </div>
        </td>
        <td>{{ Form::select('type[]', $surcharge,null,['class'=>'form-control' ,'style' => 'width:100%;']) }}</td>
        <td>
          <div class="divport" >
            {{ Form::select(null, $harbor,null,['class'=>'form-control portOrig' ,'multiple' => 'multiple','style' => 'width:100%;']) }}
          </div>
          <div class="divcountry" hidden="true">
            {{ Form::select(null, $country,null,['class'=>'form-control countryOrig' ,'style' => 'width:100%;','multiple' => 'multiple']) }}
          </div>


        </td>
        <td>
          <div class="divport" >
            {{ Form::select(null, $harbor,null,['class'=>'form-control portDest' ,'multiple' => 'multiple','style' => 'width:100%;']) }}
          </div>
          <div class="divcountry" hidden="true">
            {{ Form::select(null, $country,null,['class'=>'form-control countryDest' ,'style' => 'width:100%;','multiple' => 'multiple']) }}
          </div>

        </td>
        <td>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
        <td>{{ Form::select(null, $carrier,null,['class'=>'form-control carrier','multiple' => 'multiple','style' => 'width:100%;']) }}</td>

        <td>{{ Form::select(null, $calculationT,null,['class'=>'form-control calculationT','style' => 'width:100%;','multiple' => 'multiple']) }}</td>
        <td> {!! Form::number('ammount[]', null, ['placeholder' => 'Please enter the ammount','class' => 'form-control m-input','style' => 'width:100%;','step'=>'0.01']) !!}</td>
        <td>{{ Form::select('localcurrency_id[]', $currency,@$currency_cfg->id,['class'=>'form-control','style' => 'width:100%;']) }}</td>
        <td>  <a  class="removeL m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
          <i class="la la-eraser"></i>
          </a>
        </td>
      </tr>
    </table>
    <!-- Tabla que contiene el tr que se clona de los rates -->
    <table  hidden="true">
      <tr   id='tclone' class="trRate" hidden="true" >

        <td style="padding: 0.75rem 0.2rem;">{{ Form::select(null, $harbor,null,['class'=>'col-sm-10 form-control rateOrig','style' => 'width:100%;','multiple' => 'multiple']) }}</td>
        <td style="padding: 0.75rem 0.2rem;">{{ Form::select(null, $harbor,null,['class'=>'col-sm-10 form-control rateDest','style' => 'width:100%;','multiple' => 'multiple']) }}</td>
        <td style="padding: 0.75rem 0.2rem;">{{ Form::select('carrier_id[]', $carrier,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>

        <td style="padding: 0.75rem 0.2rem;">{!! Form::number('twuenty[]', 0, ['placeholder' => 'Enter 20','class' => 'form-control m-input','style' => 'width:100%; font-size: 11px;' ]) !!} </td>
        <td style="padding: 0.75rem 0.2rem;">{!! Form::number('forty[]', 0, ['placeholder' => 'Enter 40','class' => 'form-control m-input','style' => 'width:100%; font-size: 11px;' ]) !!} </td>
        <td style="padding: 0.75rem 0.2rem;"> {!! Form::number('fortyhc[]', 0, ['placeholder' => 'Enter 40HC','class' => 'form-control m-input','style' => 'width:100%; font-size: 11px;' ]) !!}</td>
        <td style="padding: 0.75rem 0.2rem;"> {!! Form::number('fortynor[]', 0, ['placeholder' => 'Enter 40NOR','class' => 'form-control m-input','style' => 'width:100%; font-size: 11px;' ]) !!}</td>
        <td style="padding: 0.75rem 0.2rem;"> {!! Form::number('fortyfive[]', 0, ['placeholder' => 'Enter 45','class' => 'form-control m-input','style' => 'width:100%; font-size: 11px;' ]) !!}</td>
        <td style="padding: 0.75rem 0.2rem;">{{ Form::select('currency_id[]', $currency,@$currency_cfg->id,['class'=>'custom-select form-control','style' => 'width:100%; font-size: 11px;']) }}</td>
        <td  style="padding: 0.75rem 0.2rem;" width = '7%'>{{ Form::select('scheduleT[]',$scheduleT,null,['class'=>'col-sm-10 rateScheduleT form-control','style' => 'width:100%;']) }}</td>
        <td   style="padding: 0.75rem 0.2rem;" width = '5%'> {!! Form::number('transitTi[]', 0, ['placeholder' => 'Enter Days','class' => 'form-control m-input','required' => 'required','style' => 'width:100%; font-size: 11px;' ,'min' => '0' ]) !!}</td>
        <td  style="padding: 0.75rem 0.2rem;" width = '5%'> {!! Form::text('via[]', null, ['placeholder' => 'Enter via','class' => 'form-control m-input','style' => 'width:100%; font-size: 11px;' ]) !!}</td>
        <td style="padding: 0.75rem 0.2rem;">   <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " >
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
<script>


  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });



  var uploadedDocumentMap = {}
  Dropzone.options.documentDropzone = {
    url: '{{ route('contracts.storeMedia') }}',
    maxFilesize: 2, // MB
    timeout: 18000,
    addRemoveLinks: true,
    headers: {
    'X-CSRF-TOKEN': "{{ csrf_token() }}"
  },
    success: function (file, response) {
      $('#formu').append('<input type="hidden" name="document[]" value="' + response.name + '">')
      uploadedDocumentMap[file.name] = response.name
    },
      removedfile: function (file) {
        file.previewElement.remove()
        var name = ''
        if (typeof file.file_name !== 'undefined') {
          name = file.file_name
        } else {
          name = uploadedDocumentMap[file.name]
        }
        $('#formu').find('input[name="document[]"][value="' + name + '"]').remove()
      },
        init: function () {
          @if(isset($project) && $project->document)
          var files =
              {!! json_encode($project->document) !!}
          for (var i in files) {
            var file = files[i]
            this.options.addedfile.call(this, file)
            file.previewElement.classList.add('dz-complete')
            $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
          }
          @endif
        }
        }






</script>
@stop
