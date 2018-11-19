@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection

@section('title', 'Contracts')
@section('content')

<div class="m-content">
   <div class="m-portlet m-portlet--mobile">
      <div class="m-portlet__head">
         <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
               <h3 class="m-portlet__head-text">
                  List Contracts
               </h3>
            </div>
         </div>
      </div>
      @if (count($errors) > 0)
      <div id="notificationError" class="alert alert-danger">
         <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
         </ul>
      </div>
      @endif
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
                     <a class="nav-link m-tabs__link addS active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                        <i class="la la-briefcase"></i>
                        LCL Contracts
                     </a>
                  </li>
                  <li class="nav-item m-tabs__item">
                     <a class="nav-link m-tabs__link tabrates" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                        <i class="la la-cog"></i>
                        LCL Rates
                     </a>
                  </li>
               </ul>
            </div>
         </div>
         <div class="tab-content">
            <div class="tab-pane active " id="m_tabs_6_1" role="tabpanel">
               <div class="m-portlet__body">
                  <!--begin: Search Form -->
                  <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                     <div class="row align-items-center">
                        <div class="col-xl-6 order-2 order-xl-1">
                           <div class="form-group m-form__group row align-items-center">
                              <div class="col-md-4">
                              </div>
                              <div class="col-md-4">

                              </div>
                           </div>
                        </div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                           <a href="{{ route('contractslcl.add') }}">
                              <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                 <span>
                                    <span>
                                       Add Contract LCL
                                    </span>
                                    <i class="la la-plus"></i>
                                 </span>
                              </button>
                           </a>
                           @role('administrator')
                           <a href="{{route('importaion.fcl')}}">

                              <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                 <span>
                                    <span>
                                       Import Contract&nbsp;
                                    </span>
                                    <i class="la la-cloud-upload"></i>
                                 </span>
                              </button>
                           </a>
                           @endrole
                           <a href="{{route('Request.importaion.fcl')}}">

                              <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                 <span>
                                    <span>
                                       Import Contract &nbsp;
                                    </span>
                                    <i class="la la-clipboard"></i>
                                 </span>
                              </button>
                           </a>
                           <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                     </div>
                  </div>
                  <table class="table tableData "   id="tableContracts" width="100%">
                     <thead width="100%">
                        <tr >
                           <th title="Field #1">
                              Name
                           </th>
                           <th title="Field #2">
                              Number
                           </th>
                           <th title="Field #9">
                              Validity
                           </th>
                           <th title="Field #9">
                              Expire
                           </th>
                           <th title="Field #11">
                              Status
                           </th>
                           <th title="Field #12">
                              Options
                           </th>

                        </tr>
                     </thead>
                     <tbody>


                     </tbody>
                  </table>
                  <div class="modal fade" id="m_select2_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                     <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLongTitle">
                                 Contracts
                              </h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                 <span aria-hidden="true">
                                    &times;
                                 </span>
                              </button>
                           </div>
                           <div class="modal-body">

                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                 Close
                              </button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tab-pane " id="m_tabs_6_2" role="tabpanel">
               <div class="m-portlet__body">
                  <!--begin: Search Form -->
                  <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                     <div class="row align-items-center">
                        <div class="col-xl-6 order-2 order-xl-1">
                           <div class="form-group m-form__group row align-items-center">

                              <div class="col-md-4">

                                 <div class="d-md-none m--margin-bottom-10"></div>
                              </div>
                              <div class="col-md-4">

                              </div>
                           </div>
                        </div>
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                           <a href="{{ route('contracts.add') }}">
                              <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                 <span>
                                    <span>
                                       Add Contract
                                    </span>
                                    <i class="la la-plus"></i>
                                 </span>
                              </button>
                           </a>
                           @role('administrator')
                           <a href="{{route('importaion.fcl')}}">

                              <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                 <span>
                                    <span>
                                       Import Contract&nbsp;
                                    </span>
                                    <i class="la la-cloud-upload"></i>
                                 </span>
                              </button>
                           </a>
                           @endrole
                           <a href="{{route('Request.importaion.fcl')}}">

                              <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                 <span>
                                    <span>
                                       Request Importation &nbsp;
                                    </span>
                                    <i class="la la-clipboard"></i>
                                 </span>
                              </button>
                           </a>

                           <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                     </div>
                  </div><br><br>
                  <table class="table tableData" id="tableRates" class="tableRates" width="100%">
                     <thead class="tableRatesTH">
                        <tr>
                           <th title="Field #1">
                              Name
                           </th>
                           <th title="Field #2">
                              Number
                           </th>

                           <th title="Field #3">
                              Carrier
                           </th>
                           <th title="Field #4">
                              Origin Port
                           </th>
                           <th title="Field #5">
                              Destination Port
                           </th>
                           <th title="Field #6" >
                              20'
                           </th>
                           <th title="Field #7" >
                              40'
                           </th>
                           <th title="Field #8" >
                              40'HC
                           </th>
                           <th title="Field #9" >
                              40'NOR
                           </th>
                           <th title="Field #10" >
                              45'HC
                           </th>
                           <th title="Field #10">
                              Currency
                           </th>
                           <th title="Field #9">
                              Validity
                           </th>
                           <th title="Field #11">
                              Status
                           </th>
                           <th title="Field #12">
                              Options
                           </th>

                        </tr>
                     </thead>
                     <tbody>


                     </tbody>
                  </table>



                  <div class="modal fade" id="m_select2_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                     <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLongTitle">
                                 Contracts
                              </h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                 <span aria-hidden="true">
                                    &times;
                                 </span>
                              </button>
                           </div>
                           <div class="modal-body">

                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                 Close
                              </button>
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

@endsection

@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>

<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>
<script src="/js/contracts.js"></script>


@stop




