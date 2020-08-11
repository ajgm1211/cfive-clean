@extends('layouts.app')
@section('title', ' Validator Detail FCL')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('content')
<link rel="stylesheet" href="{{asset('css/loadviewipmort.css')}}">
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <!--
<div class="m-portlet__head">
<div class="m-portlet__head-caption">
<div class="m-portlet__head-title">
<h3 class="m-portlet__head-text">
Importation Request FCL
</h3>
</div>
</div>
</div>
-->
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



        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#RequestCFCL" role="tab">
                                <i class="la la-cog"></i>
                                Validator Detail FCL
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="RequestCFCL" role="tabpanel">

                    <div class="m-portlet__body">
                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-12 order-2 order-xl-1 conten_load">
                                    <div class="form-group m-form__group row">
                                        <div class="col-lg-3">
                                            <label for="nameid" class="">Contract Name</label>
                                            {!!  Form::text('nameD',$contract['name'],['id'=>'nameid',
                                            'placeholder'=>'Contract Name',
                                            'required','disabled',
                                            'class'=>'form-control m-input'])!!}
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="numberid" class=" ">Company User</label>
                                            {!!  Form::text('CompanyUserIdd',$contract->companyUser->name,['id'=>'CompanyUserId',
                                            'required','disabled',
                                            'class'=>'form-control m-input m-select2-general','onchange' => 'selectvalidate()'])!!}
                                        </div>
                                        <div class="col-lg-1">
                                            <label class="">Direction</label>
                                            <div class="" id="direction">
                                                {!! Form::text('direction',$contract->direction->name,['class'=>'m-select2-general form-control','disabled','required','id'=>'direction'])!!}
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="">Carriers</label>
                                            <div class="" id="carrierMul">
                                                {!! Form::text('carrierM',$contract->carriers->pluck('carrier')->pluck('name')->implode(', '),['class'=>'m-select2-general form-control','disabled','id'=>'carrierM','required','multiple'=>'multiple'])!!}
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="">Equiment</label>
                                            <div class="" id="Equiment">
                                                {!! Form::text('equiment',$contract->gpContainer->name,['class'=>'m-select2-general form-control','disabled','id'=>'Equiment','required','multiple'=>'multiple'])!!}
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <label><br></label>
                                            <a href="{{route('check.surchargers',$id)}}" class="btn btn-primary form-control"> <strong>Try again</strong>&nbsp; <i class="la la-refresh"></i></a>
                                        </div>
                                    </div>
                                    <hr>
                                    <!--begin::Portlet-->
                                    <div class="m-portlet  m-portlet--accent m-portlet--head-solid-bg m-portlet--head-sm" data-portlet="true" id="m_portlet_tools_1">
                                        <div class="m-portlet__head">
                                            <div class="m-portlet__head-caption">
                                                <div class="m-portlet__head-title">
                                                    <span class="m-portlet__head-icon">
                                                        <i class="flaticon-placeholder-2"></i>
                                                    </span>
                                                    <h3 class="m-portlet__head-text">
                                                        Validator Detail
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="m-portlet__body">
                                            <div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-max-height="300" style="overflow:hidden; height: 300px">
                                                <div class="form-group row"> 
                                                    <div class="col-md-4"> 
                                                        <h5> Surcharges Details Found</h5>
                                                        <ul>
                                                            @foreach($data['surcharMas_locals_found'] as $surcharMas_locals_found )
                                                            <li> 
                                                                <span style="color:green"> <strong>{{$surcharMas_locals_found}}</strong></span>
                                                            </li>                                                
                                                            @endforeach
                                                        </ul>
                                                    </div>  
                                                    <div class="col-md-4"> 
                                                        <h5> Surcharges Details Not Found</h5>
                                                        <ul>
                                                            @foreach($data['surcharMas_locals_not_found'] as $surcharMas_locals_not_found )
                                                            <li> 
                                                                <span style="color:#bf7a1a"><strong>  {{$surcharMas_locals_not_found}}</strong></span>
                                                            </li>                                                
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-4"> 
                                                        <h5> Surcharges not found in  Surcharge Details</h5>
                                                        <ul>
                                                            @foreach($data['local_not_found_in_sur_mast'] as $local_not_found_in_sur_mast )
                                                            <li> 
                                                                <span style="color:red"> <strong>{{$local_not_found_in_sur_mast}}</strong></span>
                                                            </li>                                                
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="m-separator m-separator--space m-separator--dashed"></div>
                                                <div class="form-group row"> 
                                                    <div class="col-md-4"> 
                                                        <h5> Surcharge not Registered</h5>
                                                        <ul>
                                                            @foreach($data['surcharge_not_registred'] as $surcharge_not_registred )
                                                            <li> 
                                                                <span style="color:red"> <strong>{{$surcharge_not_registred}}</strong></span>
                                                            </li>                                                
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-4"> 
                                                        <h5> Surcharges Duplicated "Variations"</h5>
                                                        <ul>
                                                            @foreach($data['surcharge_duplicated'] as $surcharge_duplicated )
                                                            <li> 
                                                                <span style="color:red"> <strong>{{$surcharge_duplicated}}</strong></span>
                                                            </li>                                                
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="m-separator m-separator--space m-separator--dashed"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Portlet-->
                                    <br />
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
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="application/x-javascript" src="/js/toarts-config.js"></script>

<script>
    $('.m-select2-general').select2({

    });

</script>

@stop
