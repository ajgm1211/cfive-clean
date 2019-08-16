@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            List of users
                        </h3>
                    </div>
                </div>
            </div>

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

                                <div class="col-md-4">
                                    <div class="m-form__group m-form__group--inline">
                                        <div class="m-form__label">
                                            <label class="m-label m-label--single">
                                                Rol:
                                            </label>
                                        </div>
                                        <div class="m-form__control">
                                            <select class="form-control m-bootstrap-select" id="m_form_type">
                                                <option value="">
                                                    All
                                                </option>
                                                <option value="1">
                                                    Admin
                                                </option>
                                                <option value="2">
                                                    Company
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

                            <button id="add" type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                            <span>
                                <i class="la la-user"></i>
                                <span>
                                    Add User
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
                            Name
                        </th>
                        <th title="Field #2">
                            Last Name
                        </th>
                        <th title="Field #3">
                            Email
                        </th>
                        <th title="Field #4">
                            Type
                        </th>
                        <th title="Field #5">
                            Company Name
                        </th>
                        @hasrole('administrator')
                        <th title="Field #6">
                            Last Login
                        </th>
                        @endhasrole
                        <th title="Field #7">
                            Status
                        </th>
                        <th title="Field #8">
                            Options
                        </th>

                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($arreglo as $arr)

                        <tr>
                            <td>{{ $arr->name }}</td>
                            <td>{{ $arr->lastname }}</td>
                            <td>{{ $arr->email }}</td>
                            <td>@if($arr->type == "admin")
                                    1
                                @elseif($arr->type == "company")
                                    2
                                @elseif($arr->type == "subuser")
                                    3
                                @else
                                    4
                                @endif
                            </td>
                            <td>@if($arr->companyUser){{ $arr->companyUser->name }}@endif</td>
                            @hasrole('administrator')
                            <td>
                                {{ $arr->last_login }}
                            </td>
                            @endhasrole
                            <td>@if($arr->state==1) Active @else Inactive @endif</td>
                            @if(Auth::user()->type != 'subuser')
                                <td>
                                    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  onclick="AbrirModal('edit',{{  $arr->id }})" title="Edit ">
                                        <i class="la la-edit"></i>
                                    </a>
                                    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="AbrirModal('delete',{{  $arr->id }})" >
                                        <i class="la la-eraser"></i>
                                    </a>
                                    @if( (Auth::user()->type == 'company') ||  (Auth::user()->type == 'admin')  )
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Reset " onclick="AbrirModal('reset',{{  $arr->id }})"  >
                                            <i class="la la-key"></i>
                                        </a>
                                    @endif
                                    <a href="{{route('users.activate',$arr->id)}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Enable/Disable User" >
                                        <i class="la la-user"></i>
                                    </a>
                                    @role('administrator')
                                    <a href="{{route('users.verify',$arr->id)}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Verify User" >
                                        <i class="la la-toggle-on"></i>
                                    </a>
                                    <a href="{{route('impersonate.impersonate',$arr->id)}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Impersonate User" >
                                        <i class="la la-eye"></i>
                                    </a>
                                    @endrole
                                </td>
                            @else
                                <td>-</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="modal fade" id="m_modal_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">
                                    User
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
@endsection

@section('js')
    @parent
    <script src="/assets/demo/default/custom/components/datatables/base/html-table.js" type="text/javascript"></script>
    <script>

        function AbrirModal(action,id){

            if(action == "edit"){
                var url = '{{ route("users.edit", ":id") }}';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });
            }if(action == "add"){
                var url = '{{ route("users.add") }}';
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });
            }
            if(action == "delete"){
                var url = '{{ route("users.msg", ":id") }}';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });

            }
            if(action == "reset"){
                var url = '{{ route("users.msgreset", ":id") }}';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });

            }

        }
    </script>

@stop
