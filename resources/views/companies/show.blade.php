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
    <div class="m-portlet--mobile">
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
                                    @if($company->logo!='')
                                    <div class="" style="line-height: .5;">
                                        <img src="/{{$company->logo}}" class="img img-fluid" style="width: 200px; height: auto; margin-bottom:25px">
                                    </div>
                                    <br>
                                    @endif
                                    <h2 class="size-18px color-blue" style="text-transform: uppercase;"><b>{{$company->business_name}}</b> </h2>
                                    <hr>
                                    <button class="btn btn-primary" data-toggle="collapse" data-target="#actions">
                                        Actions
                                    </button>
                                    <br>
                                    <div id="actions" class="collapse">
                                        <hr>
                                        <button class="btn btn-success btn-sm" onclick="AbrirModal('edit',{{$company->id}})">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm" data-toggle="collapse" data-target="#actions">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                <div class="m-portlet__body">
                                    <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#about_company" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>About {{$company->business_name}}</b></h4>
                                    <hr>
                                    <div class="collapse show" id="about_company">
                                        <label><b>Name</b></label>
                                        <p class="color-black">{{$company->business_name}}</p>
                                        <hr>
                                        <label><b>Phone</b></label>
                                        <p class="color-black">{{$company->phone}}</p>
                                        <hr>
                                        <label><b>Address</b></label>
                                        <p class="color-black">{{$company->address}}</p>
                                        <hr>
                                        <label><b>Price level</b></label>
                                        @if(isset($company->price_name) && count($company->price_name)>0)
                                        <ul>
                                            @foreach($company->price_name as $price)
                                            <li style="margin-left: -25px;" class="color-black">{{$price->name}}</li>
                                            @endforeach
                                        </ul>
                                        @else
                                        <p class="color-black">There are not associated prices</p>
                                        @endif
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                <div class="m-portlet__body">
                                    <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#company_contacts" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Owners</b></h4>
                                    <hr>
                                    <div class="collapse show" id="company_contacts"> 
                                        @if(!$company->groupUserCompanies->isEmpty())
                                        @foreach($company->groupUserCompanies as $groupUser)
                                        <ul>
                                            <li>{{$groupUser->user->name}} {{$groupUser->user->lastname}} <a href="#" data-contact-id="{{$groupUser->id}}" id="delete-owner"><span class="pull-right"><i class="fa fa-close"></i></span></a></li>
                                        </ul>
                                        @endforeach
                                        @else
                                        <p>No Owners</p>
                                        @endif
                                        <!--<br>
<div class="text-center">
<button class="btn btn-default" data-toggle="modal" data-target="#addContactModal">
Add contact
</button>
</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                <div class="m-portlet__body">
                                    <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#company_contacts" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Contacts</b></h4>
                                    <hr>
                                    <div class="collapse show" id="company_contacts"> 
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
                                            <button class="btn btn-default" onclick="AbrirModal('addContact',0)">
                                                Add contact
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="m-datatable text-center" id="html_table" >
                        <thead>
                            <tr>
                                <th title="Status">
                                    Status
                                </th>
                                <th title="Created">
                                    Created
                                </th>
                                <th title="Origin">
                                    Origin
                                </th>
                                <th title="Destination">
                                    Destination
                                </th>
                                <th title="Ammount">
                                    Ammount
                                </th>
                                <th title="Options">
                                    Options
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quotes as $quote)
                            <tr>
                                <td ><span class="{{$quote->status->name}}">{{$quote->status->name }}</span></td>
                                <td>{{ date_format($quote->created_at, 'M d, Y H:i')}}</td>
                                @if($quote->origin_harbor)
                                <td>{{$quote->origin_harbor->name }}</td>
                                @else
                                <td>{{$quote->origin_address }}</td>
                                @endif
                                @if($quote->destination_harbor)
                                <td>{{$quote->destination_harbor->name }}</td>
                                @else
                                <td>{{$quote->destination_address }}</td>
                                @endif
                                <td>{{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}} {{$quote->currencies->alphacode}}</td>
                                <td>
                                    <a href="{{route('quotes.show',setearRouteKey($quote->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Show ">
                                        <i class="la la-eye"></i>
                                    </a>
                                    <a href="{{route('quotes.edit',setearRouteKey($quote->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                        <i class="la la-edit"></i>
                                    </a>
                                    <a href="{{route('quotes.duplicate',setearRouteKey($quote->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Duplicate ">
                                        <i class="la la-plus"></i>
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
@include('companies.partials.companiesModal')
@include('companies.partials.deleteCompaniesModal')
@include('companies.partials.addContactModal')
@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-quotes.js" type="text/javascript"></script>
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
        if(action == "addContact"){
            var url = '{{ route("contacts.add") }}';
            $('.modal-body').load(url,function(){
                $('#addContactModal').modal({show:true});
            });
        }
    }
</script>
@stop

