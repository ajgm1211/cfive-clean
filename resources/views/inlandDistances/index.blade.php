@extends('layouts.app')
@section('title', 'Inland Distance')
@section('content')

<div class="m-content">
  <div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
          <h3 class="m-portlet__head-text">
            Inland Distance 
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
      <div class="m-portlet__head-tools">
        <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
          <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
              <i class="la la-cog"></i>
              Inland Distance 
            </a>
          </li>

        </ul>
      </div>
      <div class="tab-content">
        <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
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

                <div class="form-group m-form__group row align-items-center">
                  <div class="col-md-4">
                    <div class="m-input-icon m-input-icon--left">
                        <h5 >
															Harbor 
														</h5>
                      <h5 class="m--font-primary">
															 {{ $harbor->name }}
														</h5>
                    </div>
                  </div>
                </div>

              </div>
              <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',{{  $harbor->id }})">
                  <span>
                    <i class="la la-plus"></i>
                    <span>
                      Add Distance
                    </span>
                  </span>
                </button>
                <div class="m-separator m-separator--dashed d-xl-none"></div>
              </div>
            </div>
          </div>
          <table class="m-datatable" id="html_table" width="100%">
            <thead>
              <tr>
                <th title="Field #1">
                  ID
                </th>
                <th title="Field #1">
                  Zip Code
                </th>
                <th title="Field #2">
                  Address
                </th>
                <th title="Field #2">
                  Province/State
                </th>
                <th title="Field #2">
                  Display Name
                </th>
                <th title="Field #6">
                  Distance
                </th>
                <th title="Field #6">
                  Options
                </th>

              </tr>
            </thead>
            <tbody>
              @foreach ($data as $arr)
              <tr>
                <td>{{ $arr->id }}</td>
                <td>{{ $arr->zip }}</td>
                <td>{{ $arr->address }}</td>
                <td>{{ @$arr->inlandLocation->name }}</td>
                <td>{{ $arr->zip }}, {{ $arr->address }}, {{ @$arr->inlandLocation->name }} </td>
                <td>{{ $arr->distance }}</td>
                <td>
                  <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  onclick="AbrirModal('edit',{{  $arr->id }})" title="Edit ">
                    <i class="la la-edit"></i>
                  </a>

                  <a href="#"  data-inlandd-id="{{$arr->id}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill delete-inlandd" title="Delete" >
                    <i class="la la-eraser"></i>
                  </a>

                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div class="modal fade" id="m_modal_6"  role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">
                    Inland Distance
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
                  <button type="button" class="btn btn-danger" data-dismiss="modal">
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

@endsection

@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-surcharge.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-saleterms.js" type="text/javascript"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script>

  function AbrirModal(action,id){

    if(action == "edit"){
      var url = '{{ route("inlandD.edit", ":id") }}';
      url = url.replace(':id', id);


      $('.modal-body').load(url,function(){
        $('#m_modal_6').modal({show:true});
      });
    }
    if(action == "add"){
      var url = '{{ route("inlandD.add", ":id") }}';
      url = url.replace(':id', id);
      $('.modal-body').load(url,function(){
        $('#m_modal_6').modal({show:true});
      });

    }

  }


</script>

@stop
