@extends('layouts.app')
@section('css')
@parent

@endsection
@section('title', 'Api Settings')
@section('content')

<div class="m-content">
    @if(Session::has('message.nivel'))
        <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show"
            role="alert">
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

    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        <b>External API Settings</b>
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
                                    Enable External API Integration
                                    <input type="hidden" name="company_user_id"
                                        value="{{\Auth::user()->company_user_id}}" id="company_user_id">
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-2">
                                <span class="m-switch m-switch--icon">
                                    <label>
                                        <input type="checkbox" name="enable" id="enable_api"
                                            {{@$api->enable==1 ? 'checked':''}}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <br>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary pull-right" data-toggle="modal"
                            data-target="#AddIntegrationModal">Add Integration &nbsp;<i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12">
                        <table class="m-datatable" width="100%">
                            <thead>
                                <tr>
                                    <th title="Field #1">
                                        <b>Name</b>
                                    </th>
                                    <th title="Field #2">
                                        <b>URL</b>
                                    </th>
                                    <th title="Field #3">
                                        <b>API Key</b>
                                    </th>
                                    <th title="Field #4">
                                        <b>Associated to</b>
                                    </th>
                                    <th title="Field #5">
                                        <b>Module</b>
                                    </th>
                                    <th title="Field #6">
                                        <b>Options</b>
                                    </th>

                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($api->api_integration))
                                    @foreach($api->api_integration as $item)
                                        <tr>
                                            <td>{{$item->name}}</td>
                                            <td>{{$item->url}}</td>
                                            <td>{{$item->api_key}}</td>
                                            <td>{{$item->partner->name}}</td>
                                            <td>{{$item->module}}</td>
                                            <td>
                                                <input name="api_id" type="hidden" value="{{$item->id}}" class="api_id"/>
                                                <a href="#" class="open_edit_modal" data-toggle="modal"
                                                data-target="#EditIntegrationModal"><i class="fa fa-edit"></i></a> 
                                                &nbsp; 
                                                <a href="#" class="delete-api-integration"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
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
<script src="{{asset('js/api-settings.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-oauth.js" type="text/javascript">
</script>
@include('api.add', ['api' => @$api])
@include('api.edit', ['api' => @$api])
@stop