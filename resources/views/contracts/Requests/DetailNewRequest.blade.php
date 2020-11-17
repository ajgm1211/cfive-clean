@extends('layouts.app')
@section('title', 'Contracts')
@section('content')
<link rel="stylesheet" href="{{asset('css/loadviewipmort.css')}}">
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Details Importation Request
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
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <div class="row">
                        <div class="col-lg-12">
                            @foreach($colectionFinal as $colection)

                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class=""><b>CONTRACT:</b></label>
                                </div>
                                <div class="col-lg-2">
                                    <label for="nameid" class="">Contract Name</label>
                                    {!!  Form::text('name',$colection['namecontract'],['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'class'=>'form-control m-input',
                                    'disabled',
                                    'style'=>'color:black'])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="numberid" class=" ">Contract Number</label>
                                    {!!  Form::text('number',$colection['numbercontract'],['id'=>'numberid',
                                    'placeholder'=>'Number Contract',
                                    'class'=>'form-control m-input',
                                    'disabled',
                                    'style'=>'color:black'])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    {!!  Form::text('validation',$colection['validation'],['id'=>'validationid',
                                    'placeholder'=>'2018-08-22 / 2018-08-22',
                                    'class'=>'form-control m-input',
                                    'disabled',
                                    'style'=>'color:black'])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="companyid" class=" ">Company</label>
                                    {!!  Form::text('company',$colection['company'],['id'=>'companyid',
                                    'placeholder'=>'company',
                                    'class'=>'form-control m-input',
                                    'disabled',
                                    'style'=>'color:black'])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="statusid" class=" ">Status</label>
                                    {!!  Form::text('status',$colection['status'],['id'=>'statusid',
                                    'placeholder'=>'status',
                                    'class'=>'form-control m-input',
                                    'disabled',
                                    'style'=>'color:black'])!!}
                                </div>
                                <!------------------------------------->
                                <div class="col-lg-2"></div>
                                <div class="col-lg-2">
                                    <label for="Userid" class="col-form-label">User</label>
                                    {!!  Form::text('user',$colection['User'],['id'=>'Userid',
                                    'placeholder'=>'User',
                                    'class'=>'form-control m-input',
                                    'disabled',
                                    'style'=>'color:black'])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="createdid" class="col-form-label">Created</label>
                                    {!!  Form::text('User',$colection['created'],['id'=>'createdid',
                                    'placeholder'=>'created',
                                    'class'=>'form-control m-input',
                                    'disabled',
                                    'style'=>'color:black'])!!}
                                </div>
                            </div>
                            <hr>

                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class=""><b>DETAILS:</b></label>
                                </div>

                                <div class="col-lg-4">

                                    <div class="m-portlet m-portlet--creative m-portlet--bordered-semi">
                                        <div class="m-portlet__head">
                                            <div class="m-portlet__head-caption">
                                                <div class="m-portlet__head-title">
                                                    <span class="m-portlet__head-icon">
                                                        <i class="la la-check-square"></i>
                                                    </span>
                                                    <h3 class="m-portlet__head-text">
                                                        Type Of Selection
                                                    </h3>
                                                    <h2 class="m-portlet__head-label m-portlet__head-label--accent">
                                                        <span>
                                                            Type
                                                        </span>
                                                    </h2>
                                                </div>
                                            </div>
                                            <div class="m-portlet__head-tools">
                                            </div>
                                        </div>
                                        <div class="m-portlet__body" style="font-size:17px">
                                            <ul>
                                                @if($colection['surchargeBol'])
                                                <li> {{$colection['contenSurchar']}}</li>
                                                <br>
                                                @if($colection['ValuesSomeBol'])

                                                <li> {{$colection['contenValuesSome']}}</li>

                                                @elseif($colection['ValuesWithCurreBol'])

                                                <li> {{$colection['contenValuesWithCurre']}}</li>

                                                @endif

                                                @elseif($colection['rateBol'])
                                                <li> {{$colection['contenRate']}}</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    @if($colection['tarjetBol'])
                                    <div class="m-portlet m-portlet--creative m-portlet--bordered-semi">
                                        <div class="m-portlet__head">
                                            <div class="m-portlet__head-caption">
                                                <div class="m-portlet__head-title">
                                                    <span class="m-portlet__head-icon">
                                                        <i class="flaticon-statistics"></i>
                                                    </span>
                                                    <h3 class="m-portlet__head-text">
                                                        Type Of Selection
                                                    </h3>
                                                    <h2 class="m-portlet__head-label m-portlet__head-label--primary">
                                                        <span>
                                                            Data
                                                        </span>
                                                    </h2>
                                                </div>
                                            </div>
                                            <div class="m-portlet__head-tools">
                                            </div>
                                        </div>
                                        <div class="m-portlet__body" style="font-size:17px">
                                            <ul>
                                                @if($colection['ValCarrierBol'])
                                                <li>{{$colection['contenValuesCarrier']}}</li>
                                                @endif
                                                
                                                @if($colection['ValuesOriginBol'])
                                                <br>
                                                <li>{{$colection['contenValuesOrigin']}}</li>
                                                @endif
                                                
                                                @if($colection['ValuesDestinyBol'])
                                                <br>
                                                <li>{{$colection['contenValuesDestiny']}}</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    @endsection

    @section('js')
    @parent

    @stop
