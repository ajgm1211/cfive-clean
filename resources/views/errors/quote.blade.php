@extends('layouts.app')
@section('title', 'Add Quote')
@section('content')

<div class="m-content">
    <div class="row">
        <div class="col-md-2">
            <button class="btn btn-primary btn-block">Schedules</button>
        </div>
        <div class="col-md-10" >
            @if(isset($company_user->companyUser->currency_id))
            <div class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
                <input type="text" id="currency_id" class="form-control " value="{{$currency_cfg->alphacode}}" disabled="true"/>
            </div>
            @endif
        </div>
    </div>
    <br>
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
    <div class="row">
        <div class="col-md-12">    
            <div class="row">
                <div class="col-md-12">
                    <div class="m-portlet m-portlet--tabs">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-tools">
                                <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--right m-tabs-line-danger" role="tablist">
                                    <li class="nav-item m-tabs__item" style="padding-top: 20px;padding-bottom: 20px;">
                                        <a href="{{url('settings')}}" class="btn btn-primary" role="tab">
                                            Go to settings
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-portlet__body">
                                <div class="tab-content">
                                    <div class="tab-pane active text-center" id="m_portlet_tab_1_1">
                                        <h5>If you are account admin, you must configure company's preferences in the settings section. If not, please request that to account admin</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@parent
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/js/quote.js"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>
@stop
