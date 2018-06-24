@extends('layouts.app')
@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Upload File
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
        <!--
<div ng-app="">


<button ng-click="showme=true">Mostrar</button>
<button ng-click="showme=false">Ocultar</button> 

<div class="wrapper">
<p ng-hide="showme">Esto debe aparecer</p>
<h2 ng-show="showme">Contenido oculto</h2>
</div>

</div>    
-->

        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        {!! Form::open(['route' => 'UploadFileRates.store','method' => 'post','class' => 'form-group m-form__group', 'files'=>true]) !!}
                        <div class="form-group m-form__group row">
                            <label class="col-form-label col-lg-3 col-sm-12">
                                Single File Upload
                            </label>

                            <div class="col-lg-4 col-md-9 col-sm-12">
                                <input type="file" name="file" required>
                            </div>
                            <!--      <div class="form-group m-form__group row">
<label class="col-form-label col-lg-3 col-sm-12">
Single File Upload
</label>
<div class="col-lg-4 col-md-9 col-sm-12">

<div class="m-dropzone dropzone" action="inc/api/dropzone/upload.php" id="m-dropzone-one">
<div class="m-dropzone__msg dz-message needsclick">
<h3 class="m-dropzone__msg-title">
Drop files here or click to upload.
</h3>
<span class="m-dropzone__msg-desc">
This is just a demo dropzone. Selected files are
<strong>
not
</strong>
actually uploaded.
</span>
</div>
</div>
</div> -->

                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">

                                <button type="submit" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                    <span>
                                        <span>
                                            Upload
                                        </span>
                                        <i class="la la-plus"></i>
                                    </span>
                                </button>


                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>

                        </div>
                    </div>
                    {!! Form::close() !!}

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
