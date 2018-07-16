@extends('layouts.app')
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
    <!--
<div ng-app="">


<button ng-click="showme=true">Mostrar</button>
<button ng-click="showme=false">Ocultar</button> 

<div class="wrapper">
<p ng-hide="showme">Esto debe aparecer</p>
<h2 ng-show="showme">Contenido oculto</h2>
</div>

</div>    
-->

    <div class="m-portlet m-portlet--tabs">
      <div class="m-portlet__head">
        <div class="m-portlet__head-tools">
          <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
            <li class="nav-item m-tabs__item">
              <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                <i class="la la-cog"></i>
                List Rates 
              </a>
            </li>
            <li class="nav-item m-tabs__item">
              <a class="nav-link m-tabs__link addS" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                <i class="la la-briefcase"></i>
                List Contracts
              </a>
            </li>

          </ul>
        </div>
      </div>
      <div class="tab-content">
        <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
          <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
              <div class="row align-items-center">
                <div class="col-xl-8 order-2 order-xl-1">
                  <div class="form-group m-form__group row align-items-center">

                    <div class="col-md-4">
                      <div class="m-form__group m-form__group--inline">
                        <div class="m-form__label">
                          <label class="m-label m-label--single">
                            Status:
                          </label>
                        </div>
                        <div class="m-form__control">
                          <select class="form-control m-bootstrap-select" id="m_form_type">
                            <option value="">
                              All
                            </option>
                            <option value="1">
                              Publish
                            </option>
                            <option value="2">
                              Drafts
                            </option>
                          </select>
                        </div>
                      </div>
                      <div class="d-md-none m--margin-bottom-10"></div>
                    </div>
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

                  <div class="m-separator m-separator--dashed d-xl-none"></div>
                </div>
              </div>
            </div>
            <table class="m-datatable"  id="html_table" >
              <thead>
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
                    Destiny Port
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
                  <th title="Field #10">
                    Currency
                  </th>
                  <th title="Field #9">
                    Validity
                  </th>
                  <th title="Field #11">
                    status
                  </th>
                  <th title="Field #12">
                    Options
                  </th>

                </tr>
              </thead>
              <tbody>

                @foreach ($arreglo as $arr)

                @foreach ($arr->rates as $rates)
                <tr>
                  <td>{{ $arr->name }}</td>
                  <td>{{ $arr->number }}</td>
                  <td>{{ $rates->carrier->name }}</td>
                  <td>{{$rates->port_origin->name  }}</td>
                  <td>{{$rates->port_destiny->name  }}</td>
                  <td>{{$rates->twuenty  }}</td>
                  <td>{{$rates->forty  }}</td>
                  <td>{{$rates->fortyhc  }}</td>
                  <td>{{$rates->currency->alphacode  }}</td>
                  <td>{{ $arr->validity}} -  {{$arr->expire}}</td>
                  <td>@if($arr->status == "publish")
                    1
                    @else
                    2
                    @endif
                  </td>
                  <td>
                    <a href="{{ route("contracts.edit", $arr->id) }}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                      <i class="la la-edit"></i>
                    </a>

                    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="AbrirModal('delete',{{  $rates->id }})" >
                      <i class="la la-eraser"></i>
                    </a>

                  </td>
                </tr>
                @endforeach
                @endforeach
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
                <div class="col-xl-8 order-2 order-xl-1">
                  <div class="form-group m-form__group row align-items-center">

                    <div class="col-md-4">
                      <div class="m-form__group m-form__group--inline">
                        <div class="m-form__label">
                          <label class="m-label m-label--single">
                            Status:
                          </label>
                        </div>
                        <div class="m-form__control">
                          <select class="form-control m-bootstrap-select m_form_type2" id="m_form_type">
                            <option value="">
                              All
                            </option>
                            <option value="1">
                              Publish
                            </option>
                            <option value="2">
                              Drafts
                            </option>
                          </select>
                        </div>
                      </div>
                      <div class="d-md-none m--margin-bottom-10"></div>
                    </div>
                    <div class="col-md-4">
                      <div class="m-input-icon m-input-icon--left">
                        <input type="text" class="form-control m-input" placeholder="Search..." id="generalSearch2">
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

                  <div class="m-separator m-separator--dashed d-xl-none"></div>
                </div>
              </div>
            </div>
            <table class="m-datatable2"  id="" >
              <thead>
                <tr>
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
                    status
                  </th>
                  <th title="Field #12">
                    Options
                  </th>

                </tr>
              </thead>
              <tbody>
                @foreach($contractG as $contractG)
                <tr>
                  <td>{{ $contractG->name }}</td>
                  <td>{{ $contractG->number }}</td>
                  <td>{{ $contractG->validity}}</td>
                  <td>{{$contractG->expire}}</td>
                  <td>@if($arr->status == "publish")
                    1
                    @else
                    2
                    @endif
                  </td>
                  <td>
                    <a href="{{ route("contracts.edit", $arr->id) }}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                      <i class="la la-edit"></i>
                    </a>


                    <a  id="delete-contract" data-contract-id="{{$arr->id}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete">
                      <i class="la la-eraser"></i>
                    </a>


                  </td>
                </tr>
                @endforeach

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
<script src="/js/contracts.js"></script>

<!--
<script>

function AbrirModal(action,id){

if(action == "edit"){
var url = '{{ route("contracts.edit", ":id") }}';
url = url.replace(':id', id);


$('.modal-body').load(url,function(){
$('#m_select2_modal').modal({show:true});
});
}if(action == "add"){
var url = '{{ route("contracts.add") }}';


$('.modal-body').load(url,function(){
$('#m_select2_modal').modal({show:true});
});

}
if(action == "delete"){
var url = '{{ route("contracts.msg", ":id") }}';
url = url.replace(':id', id);

$('.modal-body').load(url,function(){
$('#m_select2_modal').modal({show:true});
});

}

}
</script>
-->
@stop
