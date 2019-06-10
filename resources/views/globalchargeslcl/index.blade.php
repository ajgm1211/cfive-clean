@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('title', 'Global Charges')
@section('content')



<div class="m-content">
  <div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
          <h3 class="m-portlet__head-text">
            Global Charges LCL
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
                List Global Charge LCL
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
              <div class="row">
                <div class="col-md-2">
                  <a  id="newmodal" class="">
                    <button id="new" type="button"  onclick="AbrirModal('addGlobalCharge',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                      Add New
                      <i class="fa fa-plus"></i>
                    </button>
                  </a>
                </div>

              </div>
            </div>
            <table class="table tableData" id="global-table" width="100%" >
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
                  <th title="Field #8">
                    Minimum
                  </th>
                  <th title="Field #9">
                    Currency
                  </th>
                  <th title="Field #10">
                    Validity
                  </th>
                  <th title="Field #11">
                    Options
                  </th>
                </tr>
              </thead>
              <tbody>

                @foreach ($global as $globalcharges)
                <tr id='tr_l{{++$loop->index}}'>
                  <td>
                    <div id="divtype{{$loop->index}}"  class="val">{!! $globalcharges->surcharge->name !!}</div>
                  </td>
                  <td>
                    @if(!$globalcharges->globalcharportlcl->isEmpty())
                    <div id="divport{{$loop->index}}"  class="val">
                      {!! str_replace(["[","]","\""], ' ', $globalcharges->globalcharportlcl->pluck('portOrig')->unique()->pluck('display_name') ) !!} 
                    </div>
                    @endif
                    @if(!$globalcharges->globalcharcountrylcl->isEmpty())
                    <div id="divcountry{{$loop->index}}"  class="val">
                      {!! str_replace(["[","]","\""], ' ', $globalcharges->globalcharcountrylcl->pluck('countryOrig')->unique()->pluck('name') ) !!} 
                    </div>
                    @endif
                  </td>
                  <td>

                    @if(!$globalcharges->globalcharportlcl->isEmpty())
                    <div id="divportDest{{$loop->index}}"  class="val">
                      {!! str_replace(["[","]","\""], ' ', $globalcharges->globalcharportlcl->pluck('portDest')->unique()->pluck('display_name') ) !!} 
                    </div>
                    @endif
                    @if(!$globalcharges->globalcharcountrylcl->isEmpty())
                    <div id="divcountryDest{{$loop->index}}"  class="val">
                      {!! str_replace(["[","]","\""], ' ', $globalcharges->globalcharcountrylcl->pluck('countryDest')->unique()->pluck('name') ) !!} 
                    </div>
                    @endif
                  </td>
                  <td>
                    <div id="divchangetype{{$loop->index}}"  class="val">{!! $globalcharges->typedestiny->description !!}</div>
                  </td>
                  <td>
                    <div id="divcarrier{{$loop->index}}"  class="val">
                      {!! str_replace(["[","]","\""], ' ', $globalcharges->globalcharcarrierslcl->pluck('carrier')->pluck('name') ) !!}
                    </div>
                  </td>
                  <td>   
                    <div id="divcalculation{{$loop->index}}"  class="val">{!! $globalcharges->calculationtypelcl->name !!}</div>

                  <td> 
                    <div id="divammount{{$loop->index}}" class="val"> {!! $globalcharges->ammount !!} </div>
                  </td>
                  <td> 
                    <div id="divminimum{{$loop->index}}" class="val"> {!! $globalcharges->minimum !!} </div>
                  </td>
                  <td>
                    <div id="divcurrency{{$loop->index}}"  class="val"> {!! $globalcharges->currency->alphacode !!} </div>
                  </td>
                  <td>
                    <div id="divvalidity{{$loop->index}}"  class="val"> {!! $globalcharges->validity !!} / {!! $globalcharges->expire !!}</div>
                  </td>
                  <td>
                    <a  id='edit_l{{$loop->index}}' onclick="AbrirModal('editGlobalCharge',{{$globalcharges->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                      <i class="la la-edit"></i>
                    </a>    
                    <a  id='remove_l{{$loop->index}}'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
                      <i id='rm_l{{$globalcharges->id}}' class="la la-times-circle"></i>
                    </a>
                    <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='Duplicate '  onclick='AbrirModal("duplicateGlobalCharge",{{$globalcharges->id}})'>
                      <i class='la la-plus'></i>
                    </a>

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
  <div class="modal fade bd-example-modal-lg" id="modalGlobalchargeAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">
            Add Global Charges
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
        </div>
        <div class="modal-body-add">

        </div>

      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="modalGlobalcharge"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">
            Edit Global Charges
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
        </div>
        <div class="modal-body">

        </div>

      </div>
    </div>
  </div>

</div>

@endsection

@section('js')
@parent



<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>
<script>
  $(document).ready( function () {
    $('#global-table').DataTable();
  } );
</script>
<script>
  function AbrirModal(action,id){


    if(action == "editGlobalCharge"){
      var url = '{{ route("edit-global-charge-lcl", ":id") }}';
      url = url.replace(':id', id);
      $('.modal-body').load(url,function(){
        $('#modalGlobalcharge').modal({show:true});
      });

    }
    if(action == "addGlobalCharge"){
      var url = '{{ route("add-global-charge-lcl")}}';

      $('.modal-body-add').load(url,function(){
        $('#modalGlobalchargeAdd').modal({show:true});
      });

    }
    if(action == "duplicateGlobalCharge"){

      var url = '{{ route("duplicate-global-charge-lcl", ":id") }}';
      url = url.replace(':id', id);
      $('.modal-body-add').load(url,function(){
        $('#modalGlobalchargeAdd').modal({show:true});
      });
    }
  }

</script>
<script src="/js/globalchargeslcl.js"></script>
@if(session('globalchar'))
<script>
  swal(
    'Done!',
    'GlobalCharge updated.',
    'success'
  )
</script>
@endif

@stop
