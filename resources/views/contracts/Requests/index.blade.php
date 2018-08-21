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
                        <table class="table m-table m-table--head-separator-primary "  id="html_table" >
                            <thead >
                                <tr>
                                    <th width="1%" >
                                        Id
                                    </th>
                                    <th width="4%" >
                                        Contract Name
                                    </th>
                                    <th width="4%" >
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
                                        Options
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($Ncontracts as $Ncontract)
                                <tr>
                                    <td>
                                        {{$loop->iteration}}
                                    </td>
                                    <td>
                                        {{$Ncontract->namecontract}}
                                    </td>
                                    <td>
                                        {{$Ncontract->numbercontract}}
                                    </td>
                                    <td>
                                        {{$Ncontract->validation}}
                                    </td>
                                    <td>
                                        {{$Ncontract->created}}
                                    </td>
                                    <td>
                                        {{$Ncontract->user->name.' '.$Ncontract->user->lastname}}
                                    </td>
                                    <td>
                                        ------
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

@endsection

@section('js')
@parent


@stop
