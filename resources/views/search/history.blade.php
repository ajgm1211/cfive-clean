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



    <div class="m-portlet m-portlet--tabs">
      <div class="m-portlet__head">
        <div class="m-portlet__head-tools">
          <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">

            <li class="nav-item m-tabs__item">
              <a class="nav-link m-tabs__link  active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                <i class="la la-briefcase"></i>
                FCL Contracts
              </a>
            </li>
            <li class="nav-item m-tabs__item">
              <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                <i class="la la-cog"></i>
                LCL Contracts
              </a>
            </li>

          </ul>
        </div>
      </div>
      <div class="tab-content">
        <div class="tab-pane active " id="m_tabs_6_1" role="tabpanel">
          <div class="m-portlet__body">
         <table class="table" id="otro" width="100%">
              <thead>
                <tr>
                  <th title="name">
                    Usuario
                  </th>
                  <th title="description">
                    Pick Up Date
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
      
                <tr>
                  <td>aaaaa/td>
                  <td>aaaaa</td>

                  <td>
                   aaaa
                  </td>
                  <td>
                    aaaa
                  </td>
                </tr>

              </tbody>
            </table>
          </div>
        </div>
        <div class="tab-pane  " id="m_tabs_6_2" role="tabpanel">
          <div class="m-portlet__body">
            <table class="table" id="html_table" width="100%">
              <thead>
                <tr>
                  <th title="name">
                    Usuario
                  </th>
                  <th title="description">
                    Pick Up Date
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
    </div>
  </div>
</div>
@endsection

@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-surcharge.js" type="text/javascript"></script>

@stop
