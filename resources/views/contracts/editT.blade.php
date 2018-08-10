@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection

@section('title', 'Contracts')
@section('content')
@php
$validation_expire = $contracts->validity ." / ". $contracts->expire ;
@endphp
<div class="m-content">

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


      {!! Form::model($contracts, ['route' => ['contracts.update', $contracts], 'method' => 'PUT','class' => 'form-group m-form__group']) !!}
      @include('contracts.partials.form_contractsT')
      <div class="m-portlet m-portlet--tabs">
        <div class="m-portlet__head">
          <div class="m-portlet__head-tools">
            <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link active " data-toggle="tab" href="#m_tabs_6_1" role="tab">
                  <i class="la la-cog"></i>
                  Routes
                </a>
              </li>
              <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link " data-toggle="tab" href="#m_tabs_6_2" role="tab">
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


              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadfile">
                Upload Rates
                <i class="fa flaticon-tool-1"></i>
              </button>
              <a href="{{route('Failed.Rates.For.Contracts',$id)}}" class="btn btn-info">
                Failed Rates
                <i class="fa flaticon-tool-1"></i>
              </a>
              <table class="table m-table m-table--head-separator-primary" id="rateTable" width="100%">
                <thead>
                  <tr>
                    <th title="Field #1">
                      Origin Port
                    </th>
                    <th title="Field #2">
                      Destiny Port    
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
                    <th title="Field #7">
                      Currency
                    </th>
                    <th title="Field #7">
                      Options
                    </th>


                  </tr>
                </thead>
                <tbody>


                </tbody>
              </table>
            </div>
            <div class="tab-pane " id="m_tabs_6_2" role="tabpanel">
              <a  id="newL" class="">

                <button type="button" class="btn btn-brand">
                  Add New
                  <i class="fa fa-plus"></i>
                </button>
              </a>
              <a>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadfileSubcharge">
                  Upload Surcharge
                  <i class="fa flaticon-tool-1"></i>
                </button>
              </a>
              <a href="{{route('Failed.Subcharge.For.Contracts',$id)}}" class="btn btn-info">
                Failed Surcharge
                <i class="fa flaticon-tool-1"></i>
              </a>
              <br><br><br>
              <table class="table m-table m-table--head-separator-primary" id="users-table" width="100%" >

                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Origin Port</th>
                    <th>Destination Port</th>
                    <th>Change Type</th>
                    <th>Carrier</th>
                    <th>Calculation Type</th>
                    <th>Ammount</th>
                    <th>Currency</th>
                    <th>Options</th>

                  </tr>
                </thead>
              </table>



              <table hidden="true">
                <tr   id='tclone2' hidden="true"  >
                  <td>
                    {{ Form::select('type[]', $surcharge,null,['class'=>'form-control','style' => 'width:100%;']) }}
                  </td>
                  <td>{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control portOrig','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                  <td>{{ Form::select(null, $harbor,null,['class'=>'custom-select form-control portDest','multiple' => 'multiple','style' => 'width:100%;']) }}</td>
                  <td>{{ Form::select('changetype[]', $typedestiny,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                  <td>{{ Form::select(null, $carrier,null,['class'=>'custom-select form-control carrier','multiple' => 'multiple','style' => 'width:100%;']) }}</td>

                  <td>  {{ Form::select('calculationtype[]', $calculationT,null,['class'=>'custom-select form-control ','style' => 'width:100%;']) }}</td>
                  <td> {!! Form::text('ammount[]', null, ['placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']) !!}</td>
                  <td>{{ Form::select('localcurrency_id[]', $currency,null,['class'=>'custom-select form-control','style' => 'width:100%;']) }}</td>
                  <td>  <a  class="remove m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete "  >
                    <i class="la la-eraser"></i>
                    </a>
                  </td>
                </tr>

              </table>
            </div>
            <div class="tab-pane" id="m_tabs_6_3" role="tabpanel">
              <div class="row">
                <div class="col-md-12" id="origin_harbor_label">
                  <label>Company</label>
                  <div class="form-group m-form__group align-items-center">
                    {{ Form::select('companies[]',$companies,@$company->id,['multiple','class'=>'m-select2-general','id' => 'm-select2-company']) }}
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12" id="origin_harbor_label">
                  <label>Users</label>
                  <div class="form-group m-form__group align-items-center">
                    {{ Form::select('users[]',$users,@$user->id,['multiple','class'=>'m-select2-general','id' => 'm-select2-client']) }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions">
              {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
              <a class="btn btn-success" href="{{url()->previous()}}">
                Cancel
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    {!! Form::close() !!}
    <!--end: Form Wizard-->
  </div>
  <!--End::Main Portlet-->

  <div class="modal fade" id="uploadfileSubcharge" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

        {!! Form::open(['route' => 'Upload.File.Subcharge.For.Contracts','method' => 'PUT', 'files'=>true]) !!}
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            Upload File Of Subcharge
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              ×
            </span>
          </button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label for="recipient-name" class="form-control-label">
              Single File Upload:
            </label>
            {!!Form::file('file',['id'=>'','required'])!!}
          </div>
          {!!Form::hidden('contract_id',$id,['id'=>''])!!}


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <!--  <button type="submit" class="btn btn-success">
Load
</button>-->
          <input type="submit" class="btn btn-success">
          {!! Form::close()!!}
        </div>
      </div>
    </div>
  </div>



  <div class="modal fade" id="uploadfile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

        {!! Form::open(['route' => 'Upload.File.Rates.For.Contracts','method' => 'PUT', 'files'=>true]) !!}
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            Upload File Of Rates
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              ×
            </span>
          </button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label for="recipient-name" class="form-control-label">
              Single File Upload:
            </label>
            {!!Form::file('file',['id'=>'recipient-name','required'])!!}
          </div>
          {!!Form::hidden('contract_id',$id,['id'=>'contract_id'])!!}


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Close
          </button>
          <!--  <button type="submit" class="btn btn-success">
Load
</button>-->
          <input type="submit" class="btn btn-success">
          {!! Form::close()!!}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
@parent



<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/editcontracts.js"></script>
<script>                 
  $(function() {

    $('#users-table').DataTable({
      ajax:  "{{ route('localchar.table',['id' => $id]) }}",
      columns: [
        {data: 'type', name: 'type'},
        {data: 'port_orig', name: 'port_orig'},
        {data: 'port_dest', name: 'port_dest'},
        {data: 'changetype', name: 'changetype'},
        {data: 'carrier', name: 'carrier'},
        {data: 'calculation_type', name: 'calculation_type'},
        {data: 'ammount', name: 'ammount'},
        {data: 'currency', name: 'currency'},
        {data: 'options', name: 'options'}
      ]
      ,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "deferLoading": 57,
      "processing": true,
      "dom": 'Bfrtip',
      "paging": true,

      buttons: [
        {
          extend: 'copyHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3]
          }
        },
        {
          extend: 'excelHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3]
          }
        },
        {
          extend: 'pdfHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3]
          }
        }
      ]


    });


    $('#rateTable').DataTable({
      ajax:  "{{ route('rate.table',['id' => $id]) }}",
      columns: [


        {data: 'port_orig', name: 'port_orig'},
        {data: 'port_dest', name: 'port_dest'},
        {data: 'carrier', name: 'carrier'},
        {data: 'twuenty', name: 'twuenty'},
        {data: 'forty', name: 'forty'},
        {data: 'fortyhc', name: 'fortyhc'},
        {data: 'currency', name: 'currency'},
        {data: 'options', name: 'options'}
      ]
      ,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "deferLoading": 57,
      "processing": true,
      "dom": 'Bfrtip',
      "paging": true,

      buttons: [
        {
          extend: 'copyHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3]
          }
        },
        {
          extend: 'excelHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3]
          }
        },
        {
          extend: 'pdfHtml5',
          exportOptions: {
            columns: [0, 1, 2, 3]
          }
        }
      ]


    });
  

  });  
</script>

@stop
