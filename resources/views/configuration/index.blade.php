@extends('layouts.app')
@section('css')
@parent

@endsection
@section('title', 'Notifications Settings')
@section('content')

<div class="m-content">
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
                                Notifications
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
                                <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                    <a  id="newmodal" class="">
                                        <button id="new" type="button"   class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            Save &nbsp;
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-3 col-form-label">
                                Import Contract FCL
                            </label>
                            <div class="col-3">
                                <span class="m-switch m-switch--icon">
                                    <label>
                                        <input type="checkbox" checked="checked" name="">
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

</div>

@endsection

@section('js')
@parent


@stop
