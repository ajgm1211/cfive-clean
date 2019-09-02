@extends('layouts.app')
@section('title', 'Companies | List')
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
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Companies from API
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
                        <th title="Field #5">
                            Address
                        </th>
                        <th title="Field #9">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $company)
                    <tr>
                        <td>{{$company->business_name }}</td>
                        <td>{{$company->phone }}</td>
                        <td>{{$company->email }}</td>
                        <td>{{$company->address  }}</td>
                        <td>{{$company->api_status}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
<script src="/assets/demo/default/custom/components/datatables/base/html-table-companies.js" type="text/javascript"></script>
@stop