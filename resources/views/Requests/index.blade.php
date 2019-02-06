@extends('layouts.app')
@section('title', 'Contracts')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('content')
<link rel="stylesheet" href="{{asset('css/loadviewipmort.css')}}">
<div class="m-content">
   <div class="m-portlet m-portlet--mobile">
      <div class="m-portlet__head">
         <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
               <h3 class="m-portlet__head-text">
                  Importation Request
               </h3>
            </div>
         </div>
      </div>
      @if (count($errors) > 0)
      <div id="notificationError" class="alert alert-danger">
         <strong>Ocurrió un problema con tus datos de entrada</strong><br>
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

      <div class="m-portlet__body">
         <!--begin: Search Form -->
         <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
            <div class="row align-items-center">
               <div class="col-xl-12 order-2 order-xl-1 conten_load">
                  <table class="table tableData"  id="html_table" >
                     <thead >
                        <tr>
                           <th width="1%" >
                              Id
                           </th>
                           <th width="3%" >
                              Company
                           </th>
                           <th width="4%" >
                              Contract Name
                           </th>
                           <th width="3%" >
                              Contract Number
                           </th>
                           <th width="4%" >
                              Contract Validation
                           </th>
                           <th width="3%" >
                              Date
                           </th>
                           <th width="5%" >
                              User
                           </th>
                           <th width="5%" >
                              Status
                           </th>
                           <th width="5%" >
                              Options
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($Ncontracts as $Ncontract)
                        <tr>
                           <td>
                              {{$Ncontract->id}}
                           </td>
                           <td>
                              {{$Ncontract->companyuser->name}}
                           </td>
                           <td id="{{'thnamec'.$loop->iteration}}">
                              {{$Ncontract->namecontract}}
                           </td>
                           <td id="{{'thnumc'.$loop->iteration}}">
                              {{$Ncontract->numbercontract}}
                           </td>
                           <td>
                              {{$Ncontract->validation}}
                           </td>
                           <td>
                              {{\Carbon\Carbon::parse($Ncontract->created_at)->format('d-m-Y h:i:s')}}
                           </td>
                           <td>
                              {{$Ncontract->user->name.' '.$Ncontract->user->lastname}}
                           </td>
                           <td>
                              <a href="#" style="color:#031B4E" id="{{'thstatus'.$loop->iteration}}" onclick="LoadModalStatus({{$Ncontract->id}},{{$loop->iteration}},{{$Ncontract->status}})">{{$Ncontract->status}}</a>
                              &nbsp;
                              <samp class="la la-pencil-square-o" for="{{'thstatus'.$loop->iteration}}" style="font-size:15px"></samp>
                           </td>
                           <td>
                              <a href="{{route('RequestImportation.show',$Ncontract->id)}}" title="Download File">
                                 <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                              </a>
                              &nbsp; &nbsp;  <!--
<a href="{{route('RequestImportation.edit',$Ncontract->id)}}" title="See Details" >
<samp class="la	la-file-text" style="font-size:20px; color:#031B4E"></samp>
</a> -->

                              <a href="#" class="eliminarrequest" data-id-request="{{$Ncontract->id}}" data-info="{{'id: '.$Ncontract->id.' Number Contract: '.$Ncontract->numbercontract}}"  title="Delete" >
                                 <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
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
</div>

<div class="modal fade" id="Loadstatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
               Status of the request
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">
                  &times;
               </span>
            </button>
         </div>
         <div class="modal-body">
            <form>
               <div class="form-group">
                  <label id="NameCon" class="form-control-label">
                     Name Contract:
                  </label>

               </div>
               <div class="form-group">
                  <label id="NumCon" class="form-control-label">
                     Number Contract:
                  </label>

               </div>
               <input type="hidden" id="idContract" value="0"/>
               <input type="hidden" id="posicionval" value="0"/>
               <div class="form-group">
                  <label class="form-control-label" for="statusSelectMD">Status: </label>
                  <select class="form-control" id="statusSelectMD">
                     <option value="Pending" id="Pending">Pending</option>
                     <option value="Processing" id="Processing">Processing</option>
                     <option value="Done" id="Done">Done</option>
                  </select>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="SaveStatusModal()">
               Load
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
               Close
            </button>
         </div>
      </div>
   </div>
</div>

@endsection

@section('js')
@parent

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="application/x-javascript" src="/js/RequestContracts/Request.Index.Status.js"></script>
<script>

   $(document).on('click','.eliminarrequest',function(e){
      var id = $(this).attr('data-id-request');
      var info = $(this).attr('data-info');
      var elemento = $(this);
      swal({
         title: 'Are you sure?',
         text: "You won't be able to revert this! "+info,
         type: 'warning',
         showCancelButton: true,
         confirmButtonText: 'Yes, delete it!',
         cancelButtonText: 'No, cancel!',
         reverseButtons: true
      }).then(function(result){
         if (result.value) {

            url='{!! route("destroy.Request",":id") !!}';
            url = url.replace(':id', id);
            // $(this).closest('tr').remove();
            $.ajax({
               url:url,
               method:'get',
               success: function(data){
                  if(data == 1){
                     swal(
                        'Deleted!',
                        'The Request has been deleted.',
                        'success'
                     )
                     $(elemento).closest('tr').remove();
                  }else if(data == 2){
                     swal("Error!", "an internal error occurred!", "error");
                  }
               }
            });
         } else if (result.dismiss === 'cancel') {
            swal(
               'Cancelled',
               'Your rate is safe :)',
               'error'
            )
         }
      });

   });
</script>

@stop
