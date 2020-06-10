@extends('layouts.app')
@section('title', 'Companies')
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
                    <div class="col-xl-6 order-2 order-xl-1">
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
                    <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                        <button type="button" dusk="addCompany" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                            <span>
                                <span>
                                    Add Company
                                </span>
                                &nbsp;
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
                                    Upload Companies
                                </span>
                            </a>      
                            <a href="{{route('DownLoad.Files',1)}}" class="dropdown-item" >
                                <span>
                                    <i class="la la-download"></i>
                                    &nbsp;
                                    Download File
                                </span>
                            </a>
                            <a href="{{route('view.fail.company')}}" class="dropdown-item" >
                                <span>
                                    <i class="la la-search"></i>
                                    &nbsp;
                                    Failed Companies
                                </span>
                            </a>
                        </div>
                        @if(!empty($api) && $api->enable==1)
                            @if(@$api->status==0)
                                <a href="javascript:void(0)" id="syncCompanies" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                    <span>
                                        <span>
                                            Fetch from API
                                        </span>
                                        &nbsp;
                                        <i class="la la-refresh"></i>
                                    </span>
                                </a>
                                <a href="#" id="syncCompaniesLoading" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill hide disabled">
                                    <span>
                                        <span>
                                            Fetching data
                                        </span>
                                        &nbsp;
                                        <i class="la la-refresh la-spin"></i>
                                    </span>
                                </a>
                            @else
                                <a href="#" id="syncCompaniesLoading" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill disabled">
                                    <span>
                                        <span>
                                            Fetching data
                                        </span>
                                        &nbsp;
                                        <i class="la la-refresh la-spin"></i>
                                    </span>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <table class="m-datatable text-center" id="html_table" >
                <thead>
                    <tr>
                        <th title="Field #1">
                            Business Name
                        </th>
                        <th title="Field #2">
                            Phone
                        </th>
                        <th title="Field #3">
                            Email
                        </th>
                        <th title="Field #4">
                            Tax number
                        </th>                        
                        <th title="Field #5">
                            Address
                        </th>
                        <th title="Field #6">
                            Owners
                        </th>
                        <th title="Field #7">
                            Contacts
                        </th>
                        <th title="Field #8">
                            Price Levels
                        </th>
                        <th title="Field #9">
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $company)
                    <tr>
                        <td>{{$company->business_name }}</td>
                        <td>{{$company->phone }}</td>
                        <td>{{$company->email }}</td>
                        <td>{{$company->tax_number }}</td>
                        <td>{{$company->address  }}</td>
                        <td>
                            @foreach($company->groupUserCompanies as $groupUser)
                            <ul>
                                <li>{{$groupUser->user->name}} </li>
                            </ul>
                            @endforeach
                        </td>
                        <td>{{$company->contact->count()}}</td>
                        <td>

                            @foreach($company->price_name as $price)
                            <ul>
                                <li>{{$price->name}}</li>
                            </ul>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{route('companies.show',setearRouteKey($company->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill">
                                <i class="la la-eye"></i>
                            </a>
                            <button onclick="AbrirModal('edit',{{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit">
                                <i class="la la-edit"></i>
                            </button>
                            <button id="delete-company" data-company-id="{{$company->id}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete">
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
                        Upload Companies
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div id="edit-modal-body-E" class="modal-body-E">
                    <br>
                    {!! Form::open(['route' => 'Upload.Company', 'method' => 'POST', 'files' => 'true'])!!}

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
@include('companies.partials.companiesModal')
@include('companies.partials.deleteCompaniesModal')
@endsection

@section('js')
@parent


<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="{{asset('js/companies.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
<script>
    function AbrirModal(action,id){
        if(action == "edit"){
            var url = '{{ route("companies.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#companyModal').modal({show:true});
            });
        }if(action == "add"){
            var url = '{{ route("companies.add") }}';
            $('.modal-body').load(url,function(){
                $('#companyModal').modal({show:true});
            });
        }
        if(action == "delete"){
            var url = '{{ route("companies.delete", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#deleteCompanyModal').modal({show:true});
            });
        }
    }
</script>
@stop