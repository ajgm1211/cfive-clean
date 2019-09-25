@extends('layouts.app')
@section('title', 'Test')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Test
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
                {!! Form::open(['route'=> 'TestApp.create','method' => 'get'])!!}
                <div class="form-group row ">

                    <h5>Request Auto-Import</h5>
                    <div class="col-md-6">
                        <label class="form-control-label">Campo ID Request </label>
                        <input type="text" class="form-control" name="text1" value="" >
                    </div>
                    <div class="col-md-2">
                        <label class="form-control-label"><br></label>
                        <input type="submit" class=" form-control btn btn-success" value="test">
                    </div>
                </div>
                {!! Form::close()!!}
                {!! Form::open(['route'=> ['TestApp.edit',1],'method' => 'get'])!!}
                 <div class="form-group row ">

                    <h5>Edit 1</h5>
                    <div class="col-md-6">
                        <label class="form-control-label">Campo ID Request </label>
                        <input type="text" class="form-control" name="text1" value="" >
                    </div>
                    <div class="col-md-2">
                        <label class="form-control-label"><br></label>
                        <input type="submit" class=" form-control btn btn-success" value="test">
                    </div>
                </div>
                {!! Form::close()!!}

            </div>
        </div>



        <div class="modal fade bd-example-modal-lg" id="addHarborModal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Harbors
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>
                    </div>
                    <div id="modal-body" class="modal-body">

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('js')
@parent

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script>




</script>

@stop
