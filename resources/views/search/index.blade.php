@extends('layouts.app')
@section('title', 'History Search')
@section('content')

<div class="m-content">
  <div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
          <h3 class="m-portlet__head-text">
            History Search
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
      <!--begin: Search Form -->
      <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
        <div class="row align-items-center">
          <div class="col-xl-8 order-2 order-xl-1">
            <div class="form-group m-form__group row align-items-center">


              <div class="col-md-4">
                <div class="m-input-icon m-input-icon--left">
                  <input type="text" class="form-control m-input" placeholder="Search..." id="generalSearch">
                  <span class="m-input-icon__icon m-input-icon__icon--left">
                    <span>
                      <i class="la la-search"></i>
                    </span>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-4 order-1 order-xl-2 m--align-right">
            <div class="m-separator m-separator--dashed d-xl-none"></div>
          </div>
        </div>
      </div>
      <table class="m-datatable" id="html_table" width="100%">
        <thead>
          <tr>
            <th title="name">
              Usuario
            </th>
            <th title="description">
              Pick Up Date
            </th>
            <th title="description">
              Search Date
            </th>
            <th>
              Origin Port
            </th>

            <th title="options">
              Destination Port
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach ($searchRates as $search)
          <tr>
            <td>{{ $search->user->name }}</td>
            <td>{{ $search->pick_up_date }}</td>
            <td>{{ $search->created_at }}</td>
            <td>
              {!! str_replace(["[","]","\""], ' ', $search->search_ports->pluck('portOrig')->unique()->pluck('name') ) !!}
            </td>
            <td>
              {!! str_replace(["[","]","\""], ' ', $search->search_ports->pluck('portDest')->unique()->pluck('name') ) !!}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </div>
</div>
@endsection

@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-surcharge.js" type="text/javascript"></script>

@stop
