@extends('layouts.app')
@section('title','Import Information GCLCL Acount / '.$id)
@section('content')
<link rel="stylesheet" href="{{asset('css/loadviewipmort.css')}}">
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Information of the import
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
                        <center>
                            <div class="form-group">
                                <div class="col-sm-6"></div>
                                <div class="col-sm-6">
                                    <img src="{{asset('images/ship.gif')}}" style="height:170px">
                                </div>
                                <div class="col-md-12">
                                    We will notify you when the file has been processed
                                </div>
                                <br>
                                <div class="col-md-12">
                                    <a href="{{route('showview.globalcharge.lcl',[$id,1])}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                        Failed Globalcharge LCL
                                        <i class="fa flaticon-tool-1"></i>
                                    </a>
                                </div>
                            </div>
                        </center>
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
