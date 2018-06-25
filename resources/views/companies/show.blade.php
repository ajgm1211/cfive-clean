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
                                            @if(isset($company->price_name))
                                                <ul>
                                                    @foreach($company->price_name as $price)
                                                        <li style="margin-left: -25px;">{{$price->name}}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>There are not prices associated</p>
                                            @endif
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
                                    Status
                                </th>
                                <th title="Field #2">
                                    Client
                                </th>
                                <th title="Field #3">
                                    Created
                                </th>
                                <th title="Field #4">
                                    Owner
                                </th>
                                <th title="Field #5">
                                    Origin
                                </th>
                                <th title="Field #6">
                                    Destination
                                </th>
                                <th title="Field #7">
                                    Ammount
                                </th>
                                <th title="Field #8">
                                    Options
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($quotes as $quote)
                                <tr>
                                    <td>{{$quote->status_id }}</td>
                                    <td>{{$quote->company->business_name }}</td>
                                    <td>{{$quote->created_at }}</td>
                                    <td>{!!$quote->user->name.' '.$quote->user->lastname!!}</td>
                                    @if($quote->origin_country)
                                        <td>{{$quote->origin_country->name }}</td>
                                    @else
                                        <td>---</td>
                                    @endif
                                    @if($quote->destination_country)
                                        <td>{{$quote->destination_country->name }}</td>
                                    @else
                                        <td>---</td>
                                    @endif
                                    <td>{{$quote->ammount }}</td>
                                    <td>
                                        <a href="#" data-toggle="modal" data-target="#editQuoteModal" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                            <i class="la la-edit"></i>
                                        </a>
                                        <button id="delete-quote" data-quote-id="{{$quote->id}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
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
        </div>
    </div>
    @include('companies.partials.companiesModal');
    @include('companies.partials.deleteCompaniesModal');
    @include('companies.partials.addContactModal');
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

