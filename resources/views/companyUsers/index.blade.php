@extends('layouts.app')
@section('title', 'Companies | List')
@section('content')
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <!--<div class="m-portlet__head">
<div class="m-portlet__head-caption">
<div class="m-portlet__head-title">
<h3 class="m-portlet__head-text">
Companies List
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

                        <button type="button" dusk="addCompany" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                            <span>
                                <i class="la la-user"></i>
                                <span>
                                    Add Company
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                        </button>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
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
                            <a href="{{route('companies.show',$company->id)}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill">
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
</div>
@include('companies.partials.companiesModal');
@include('companies.partials.deleteCompaniesModal');
@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
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
