@extends('layouts.app')
@section('css')
@parent

@endsection
@section('title', 'Api Settings')
@section('content')

<div class="m-content">
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
    {!! Form::open(['route' => ['UserConfiguration.update',@$user],'method' => 'put'])!!}
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            <b>Api Settings</b> 
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet m-portlet--tabs">
                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                            <!--begin: Search Form -->
                            <div class="form-group row">
                                <div class="col-2">
                                    <label class=" col-form-label">
                                        Enable API Integration
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-2">
                                    <span class="m-switch m-switch--icon">
                                        <label>
                                            <input type="checkbox" name="enable-api" id="enable-api">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--begin: Search Form -->
                    <div class="hide" id="api-table">
                        <hr>
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
                                    <a href="" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                    <span>
                                        <span>
                                            Add API Key
                                        </span>
                                        <i class="la la-plus"></i>
                                    </span>
                                    </a>
                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                        </div>
                        <table class="m-datatable">
                            <thead>
                                <tr>
                                    <th title="Secret">
                                        Secret
                                    </th>
                                    <th title="Created at">
                                        Created at
                                    </th>
                                    <th title="Options">
                                        Options
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <button id="delete-token" data-token-id="" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
                                            <i class="la la-eraser"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close()!!}
</div>

@endsection

@section('js')
@parent
    <script src="{{asset('js/api-settings.js')}}" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-oauth.js" type="text/javascript"></script>
@stop
