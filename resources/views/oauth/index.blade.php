@extends('layouts.app')
@section('title', 'Companies | Contacts')
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
                        <div class="col-xl-8 order-2 order-xl-1">
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
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                            <a href="{{route('create.passport.client')}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                            <span>
                                <span>
                                    Add Password Grant Client
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                            </a>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead style="background-color:#f4f5f8;">
                    <tr>
                        <th title="Client id">
                            <b>Client id</b>
                        </th>
                        <th title="Name">
                            <b>Name</b>
                        </th>
                        <th title="Secret">
                            <b>Secret</b>
                        </th>
                        <th title="Created at">
                            <b>Created at</b>
                        </th>
                        <th title="Options">
                            <b>Options</b>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tokens as $token)
                        <tr>
                            <td>{{$token->id }}</td>
                            <td>{{$token->name }}</td>
                            <td>{{$token->secret }}</td>
                            <td>{{$token->created_at }}</td>
                            <td>
                                <button id="delete-token" data-token-id="{{$token->id}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
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
@endsection

@section('js')
    @parent
    <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/oauth.js')}}" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-oauth.js" type="text/javascript"></script>
@stop
