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
                                    <input type="hidden" name="company_user_id" value="{{\Auth::user()->company_user_id}}" id="company_user_id">
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-2">
                                <span class="m-switch m-switch--icon">
                                    <label>
                                        <input type="checkbox" name="enable" id="enable_api" {{@$api->enable==1 ? 'checked':''}}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--begin: Search Form -->
                <div class="{{@$api->enable==1 ? '':'hide'}}" id="api-table">
                    <hr>
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-3">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="api_key" name="api_key" value="{{@$api->api_key}}" placeholder="Api key" aria-label="Api key" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" id="store_api_key" type="button">Save <i class="fa fa-save"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
