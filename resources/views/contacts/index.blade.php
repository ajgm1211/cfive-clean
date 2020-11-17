@extends('layouts.app')
@section('title', 'Contacts')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/plugins/button-dropdown/css/bootstrap.css">
<script src="/assets/plugins/button-dropdown/js/jquery3.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/assets/plugins/button-dropdown/js/bootstrap.js"></script>
@endsection
@section('content')
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <!--<div class="m-portlet__head">
<div class="m-portlet__head-caption">
<div class="m-portlet__head-title">
<h3 class="m-portlet__head-text">
Contacts
</h3>
</div>
</div>
</div>-->
        @if(Session::has('message.nivel'))
        <div class="col-md-12">
            <br>
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
        </div>
        @endif
        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <!--<div class="col-md-4">
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
</select>
</div>
</div>
<div class="d-md-none m--margin-bottom-10"></div>
</div>-->
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

                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                            <span>
                                <span>
                                    Add Contact
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                        </button>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>


                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Importation
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -136px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalupload">
                                <span>
                                    <i class="la la-upload"></i>
                                    &nbsp;
                                    Upload Contacts
                                </span>
                            </a>      
                            <a href="{{route('DownLoad.Files',2)}}" class="dropdown-item" >
                                <span>
                                    <i class="la la-download"></i>
                                    &nbsp;
                                    Download File
                                </span>
                            </a>
                            <a href="{{route('view.fail.contact')}}" class="dropdown-item" >
                                <span>
                                    <i class="la la-search"></i>
                                    &nbsp;
                                    Failed Contacts
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <table class="m-datatable"  id="html_table" >
                <thead>
                    <tr>
                        <th title="Field #1">
                            First Name
                        </th>
                        <th title="Field #2">
                            Last Name
                        </th>
                        <th title="Field #3">
                            Company
                        </th>
                        <th title="Field #4">
                            Email
                        </th>
                        <th title="Field #5">
                            Phone
                        </th>
                        <th title="Field #6">
                            Position
                        </th>                        
                        <th title="Field #7">
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $contact)
                    <tr>
                        <td>{{$contact->first_name }}</td>
                        <td>{{$contact->last_name }}</td>
                        <td>{{$contact->company->business_name}}</td>
                        <td>{{$contact->email }}</td>
                        <td>{{$contact->phone}}</td>
                        <td>{{$contact->position}}</td>
                        <td>
                            <button onclick="AbrirModal('edit',{{$contact->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                <i class="la la-edit"></i>
                            </button>
                            <button id="delete-contact" data-contact-id="{{$contact->id}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
                                <i class="la la-eraser"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal fade bd-example-modal-lg" id="modalupload"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">
            Upload Contacts
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
        </div>
        <div id="edit-modal-body-E" class="modal-body-E">
        <br>
          {!! Form::open(['route' => 'Upload.Contacts', 'method' => 'POST', 'files' => 'true'])!!}

          <div class="form-group row pull-right">
            <div class="col-md-3 ">

            </div>
          </div>
          <div class="form-group row ">
            <div class="col-md-1 "></div>
            <div class="col-md-6 ">
              <input type="file" name="file" value="Load File" required />
            </div>
          </div>
        </div>
        <div id="edit-modal-body" class="modal-footer">
          {!! Form::submit('Load', ['class'=> 'btn btn-primary']) !!}
          <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">Cancel</span>
          </button>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
    
</div>
@include('contacts.partials.contactsModal')
@include('contacts.partials.deleteContactsModal')
@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
<script>
    function AbrirModal(action,id){
        if(action == "edit"){
            var url = '{{ route("contacts.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#contactModal').modal({show:true});
            });
        }if(action == "add"){
            var url = '{{ route("contacts.add") }}';
            $('.modal-body').load(url,function(){
                $('#contactModal').modal({show:true});
            });
        }
        if(action == "delete"){
            var url = '{{ route("contacts.delete", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#deleteContactModal').modal({show:true});
            });
        }
    }
</script>
@stop
