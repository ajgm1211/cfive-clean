<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 15/05/2018
 * Time: 03:59 PM
 */
?>
@extends('layouts.app')
@section('title', 'Companies | Details')
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
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body text-center">
                                        <h2><b>{{$company->business_name}}</b> <a onclick="AbrirModal('edit',{{$company->id}})" href="#" class="pull-right"><i class="fa fa-edit"></i></a></h2>
                                        <br>
                                        <button class="btn btn-default">
                                            Actions
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4><b>About {{$company->business_name}}</b></h4>
                                        <br>
                                        <label><b>Name</b></label>
                                        <p>{{$company->business_name}}</p>
                                        <hr>
                                        <label><b>Phone</b></label>
                                        <p>{{$company->phone}}</p>
                                        <hr>
                                        <label><b>Address</b></label>
                                        <p>{{$company->address}}</p>
                                        <hr>
                                        <label><b>Price level</b></label>
                                        {{ Form::select('price_id',$prices,$company->company_price->price_id,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4><b>Contacts</b></h4>
                                        <hr>
                                        @if(!$company->contact->isEmpty())
                                            @foreach($company->contact as $contact)
                                                <ul>
                                                    <li>{{$contact->first_name}} {{$contact->last_name}} <a href="#" data-contact-id="{{$contact->id}}" id="delete-contact"><span class="pull-right"><i class="fa fa-close"></i></span></a></li>
                                                </ul>
                                            @endforeach
                                        @else
                                            <p>No contacts</p>
                                        @endif
                                        <br>
                                        <div class="text-center">
                                            <button class="btn btn-default" data-toggle="modal" data-target="#addContactModal">
                                                Add contact
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="m-datatable" id="html_table" >
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
                                    Address
                                </th>
                                <th title="Field #5">
                                    Associated Contacts
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($companies as $company)
                                <tr>
                                    <td>{{$company->business_name }}</td>
                                    <td>{{$company->created_at }}</td>
                                    <td>{{$company->email }}</td>
                                    <td>{{$company->address  }}</td>
                                    <td>
                                        @foreach($company->contact as $contact)
                                            <ul>
                                                <li>{{$contact->first_name}} {{$contact->last_name}}</li>
                                            </ul>
                                        @endforeach
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
    @include('companies.partials.companiesModal');
    @include('companies.partials.deleteCompaniesModal');
    @include('companies.partials.addContactModal');
@endsection

@section('js')
    @parent
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

