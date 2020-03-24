@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Importation GC LCL '.$account['id'].' - '.$account['name'])
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

    <!--Begin::Main Portlet-->
    <div class="m-portlet m-portlet--full-height">
        <!--begin: Portlet Head-->
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Importation Globalchargers LCL
                    </h3>
                </div>
            </div>


            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="#" data-toggle="m-tooltip" class="m-portlet__nav-link m-portlet__nav-link--icon" data-direction="left" data-width="auto" title="Get help with filling up this form">
                            <i class="flaticon-info m--icon-font-size-lg3"></i> 
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {!! Form::open(['route'=>'ImportationGlobalChargerLcl.create','method'=>'get'])!!} 
        <input type="hidden" name="statustypecurren" value="{{$statustypecurren}}">

        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <div class="row">
                        @foreach($data as $value)
                        <div class="col-lg-12">
                            <div class="form-group m-form__group row">

                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b>Account:</b></label>
                                </div>
                                {!! Form::hidden('account_id',$value['account_id'])!!}
                                {!! Form::hidden('FileName',$value['fileName'])!!}
                                <div class="col-lg-2">
                                    <label for="nameid" class="">Importation Name</label>
                                    {!!  Form::text('name',$value['name'],['id'=>'nameid',
                                    'class'=>'form-control m-input',
                                    'disabled'
                                    ])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="numberid" class=" ">Importation date</label>
                                    {!!  Form::text('date',$value['date'],['id'=>'numberid',
                                    'disabled',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                            </div>

                            <hr>

                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>DATA:</b></label>
                                </div>
                                @if($value['existorigin'] == true)
                                <div class="col-2 col-form-label">
                                    <label for="origin" class=" ">Origin Ports.</label>
                                    {!! Form::select('origin[]',$harbor,$value['origin'],['class'=>'m-select2-general form-control  ','id'=>'origin','multiple'=>'multiple'])!!}                            
                                </div>
                                @if($statusPortCountry == true)
                                <div class="col-2 col-form-label">
                                    <label for="origin" class=" ">Origin Contries.</label>
                                    {!! Form::select('originCount[]',$country,$value['originCount'],['class'=>'m-select2-general form-control  ','id'=>'originCountry','multiple'=>'multiple'])!!}                           
                                </div>
                                <div class="col-2 col-form-label">
                                    <label for="originRegion" class=" ">Origin Region.</label>
                                    {!! Form::select('originRegion[]',$region,$value['originRegion'],['class'=>'m-select2-general form-control  ','id'=>'originRegion','multiple'=>'multiple'])!!}                           
                                </div>
                                @endif
                                @endif

                                <input type="hidden" name="existorigin" id="existorigin" value="{{$value['existorigin']}}" />

                                @if($value['existdestiny'] == true)
                                <div class="col-2 col-form-label">
                                    <label for="destiny" class=" ">Destination Ports.</label>
                                    {!! Form::select('destiny[]',$harbor,$value['destiny'],['class'=>'m-select2-general form-control  ','id'=>'destiny','multiple'=>'multiple'])!!}
                                </div>
                                @if($statusPortCountry == true)
                                <div class="col-2 col-form-label">
                                    <label for="destiny" class=" ">Destination Countries.</label>
                                    {!! Form::select('destinyCount[]',$country,$value['destinyCount'],['class'=>'m-select2-general form-control  ','id'=>'destinyCountry','multiple'=>'multiple'])!!}  
                                </div>
                                <div class="col-form-label" id="destinyinpRegion">
                                        <label for="destinyRegion" class=" ">Destination Regions.</label>
                                        {!! Form::select('destinyRegion[]',$region,$value['destinyRegion'],['class'=>'m-select2-general form-control','id'=>'destinyRegion','multiple'=>'multiple'])!!}
                                    </div>
                                @endif
                                @endif

                                <input type="hidden" name="existdestiny" id="existdestiny" value="{{$value['existdestiny']}}" />
                                <input type="hidden" name="statusPortCountry" id="statusPortCountry" value="{{$statusPortCountry}}" />
                                
                                @if($value['existcarrier'] == true)
                                <div class="col-2 col-form-label">
                                    <label for="carrier" class=" ">Carrier</label>
                                    {!! Form::select('carrier',$carrier,$value['carrier'],['class'=>'m-select2-general form-control','id'=>'carrier'])!!}
                                </div>
                                @endif

                                @if($value['existtypedestiny'] == true)
                                <div class="col-2 col-form-label">
                                    <label for="carrier" class=" ">Type Destiny</label>
                                    {!! Form::select('typedestiny',$typedestiny,$value['typedestiny'],['class'=>'m-select2-general form-control','id'=>'typedestiny'])!!}
                                </div>
                                @endif

                                @if($value['existdatevalidity'] == true)
                                <div class="col-2 col-form-label">
                                    <label for="m_daterangepicker_1" class=" ">Date Validity</label>
                                    {!! Form::text('validitydate',$value['validitydate'],['class'=>'form-control m-input datevalidityinp','id'=>'m_daterangepicker_1','placeholder' => 'Date Validity' ])!!}
                                </div>
                                @endif

                                <input type="hidden" name="existcarrier" id="existcarrier" value="{{$value['existcarrier']}}" />
                                <input type="hidden" name="existtypedestiny" id="existtypedestiny" value="{{$value['existtypedestiny']}}" />
                                <input type="hidden" name="existdatevalidity" id="existdatevalidity" value="{{$value['existdatevalidity']}}" />
                                <input type="hidden" name="statustypecurren" id="existcarrier" value="{{$statustypecurren}}" />
                                <input type="hidden" name="statusPortCountry" id="statusPortCountry" value="{{$statusPortCountry}}" />

                            </div>
                        </div>
                        @endforeach
                        <div class="form-group m-form__group row"></div>
                        <div class="form-group m-form__group row">
                            <input type="hidden" name="slopp" value="{{ count($targetsArr)}}" id="loop_id">
                            @foreach($targetsArr as $targets)
                            <div class="col-md-3">
                                <div class="m-portlet m-portlet--metal m-portlet--head-solid-bg m-portlet--bordered">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <h3 class="m-portlet__head-text">
                                                    {{$targets}}
                                                    <!--<small>portlet sub title</small>-->
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-portlet__body">
                                        <div class="col-md-12">
                                            <label for="" class="">Column  in the file excel</label>
                                        </div>
                                        <div class="col-md-12">
                                            {!! Form::select($targets,$coordenates,null,['class' => 'm-select2-general form-control', 'id' => 'select'.$loop->iteration, 'onchange'=>'equals('.$loop->iteration.')'])!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="countTarges" id="countTarges" value="{{$countTarges}}" />
                        <input type="hidden" name="CompanyUserId" id="CompanyUserId" value="{{$CompanyUserId}}" />
                    </div>
                    <div class="form-group m-form__group row">

                        <div class="col-lg-4 col-lg-offset-4"> </div>
                        <div class="col-lg-2 col-lg-offset-2">
                            <button type="submit" id="processid" class="btn btn-primary form-control">
                                Process
                            </button>
                        </div>
                        <div class="col-lg-2 col-lg-offset-2">
                            <a href="{{route('delete.Accounts.Globalcharges.Fcl',[$account_id,1])}}">

                                <button type="button" class="btn btn-danger form-control" >
                                    <span>
                                        <span>
                                            Cancel&nbsp;
                                        </span>
                                        <i class="la la-trash"></i>
                                    </span>
                                </button>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {!! Form::close()!!}
    <!--end: Form Wizard-->
</div>
<!--End::Main Portlet-->

@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="{{asset('js/Globalchargers/proccessFlcTargets.js')}}" type="application/javascript"></script>

@stop