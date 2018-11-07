@extends('layouts.app')
@section('title', 'Inland for FCL')
@section('content')
    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Inland for FCL
                        </h3>
                    </div>
                </div>
            </div>
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

                            <a href="{{ route('inlands.add') }}">


                                <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                <span>
                                    <i class="la la-user"></i>
                                    <span>
                                        Add Inland
                                    </span>
                                </span>
                                </button>
                            </a>


                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
                </div>
                <table class="m-datatable" id="html_table" width="100%">

                    <thead>
                    <tr>
                        <th title="Field #1">
                            Provider
                        </th>
                        <th title="Field #2">
                            Ports
                        </th>
                        <th title="Field #3">
                            Type
                        </th>
                        <th title="Field #4">
                            Valid From
                        </th>
                        <th title="Field #5">
                            Valid To
                        </th>

                        <th title="Field #6">
                            Options
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($arreglo as $arr)

                        <tr>
                            <td>{{ $arr->provider }}</td>
                            <td>
                                @foreach($arr->inlandports as $inlandports)
                                    {{ $inlandports->ports->display_name }}
                                @endforeach
                            </td>
                            <td>
                                @if($arr->type == 1)
                                    Export
                                @else
                                    Import
                                @endif

                            </td>
                            <td>{{ $arr->validity }}</td>
                            <td>{{ $arr->expire }}</td>
                            <td>
                                <a href="{{ route("inlands.edit", setearRouteKey($arr->id)) }}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                    <i class="la la-edit"></i>
                                </a>

                                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="deleteInland({{  $arr->id }})" >
                                    <i class="la la-eraser"></i>
                                </a>

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
    <script src="/js/inlands.js"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-ireland.js" type="text/javascript"></script>

@stop
