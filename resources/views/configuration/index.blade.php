@extends('layouts.app')
@section('css')
@parent

@endsection
@section('title', 'Notifications Settings')
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
    {!! Form::open(['route' => ['UserConfiguration.update',$user],'method' => 'put'])!!}
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Notifications Settings 
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                <i class="la la-cog"></i>
                                Email Notifications
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="m-portlet__body">
                <div class="tab-content">
                    <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="new col-xl-12 order-1 order-xl-2 m--align-right">
                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                            <div class="row">

                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="col-2">
                                <label class=" col-form-label">
                                   FCL Contract Imported
                                </label>
                            </div>
                            <div class="col-2">
                                <span class="m-switch m-switch--icon">
                                    <label>
                                        @if($json['notifications']['request-importation-fcl'])
                                        <input type="checkbox" checked="checked" name="notifications-request-importation-fcl">
                                        @else
                                        <input type="checkbox" name="notifications-request-importation-fcl">
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                            <div class="col-2">
                                <label class=" col-form-label">
                                    LCL Contract Imported
                                </label>
                            </div>
                            <div class="col-2">
                                <span class="m-switch m-switch--icon">
                                    <label>
                                        @if($json['notifications']['request-importation-lcl'])
                                        <input type="checkbox" checked="checked" name="notifications-request-importation-lcl">
                                        @else
                                        <input type="checkbox" name="notifications-request-importation-lcl">
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                            <div class="col-2">
                                <label class=" col-form-label">
                                    Global Charges Imported
                                </label>
                            </div>
                            <div class="col-2">
                                <span class="m-switch m-switch--icon">
                                    <label>
                                        @if($json['notifications']['request-importation-gcfcl'])
                                        <input type="checkbox" checked="checked" name="notifications-request-importation-gcfcl">
                                        @else
                                        <input type="checkbox" name="notifications-request-importation-gcfcl">
                                        @endif
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="m-portlet m-portlet--mobile">


        <div class="m-portlet__body">

            <div class="form-group row">
                <div class="col-xl-12 order-1 order-xl-2 m--align-center">
                    <a  id="newmodal" class="">
                        <button id="new" type="submit"   class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                            Save &nbsp;
                            <i class="fa fa-save"></i>
                        </button>

                    </a>
                </div>
            </div>


        </div>
    </div>
    {!! Form::close()!!}
</div>

@endsection

@section('js')
@parent


@stop
