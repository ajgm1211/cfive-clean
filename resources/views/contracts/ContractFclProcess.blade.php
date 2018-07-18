@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Contracts')
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
                        Importation New Contract 
                        <!--<small>
new registration
</small>-->
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
        {!! Form::open(['route'=>'process.contract.fcl','method'=>'get'])!!}
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <div class="row">
                        @foreach($data as $value)
                        <div class="col-lg-12">
                            <div class="form-group m-form__group row">

                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b>CONTRACT:</b></label>
                                </div>
                                {!! Form::hidden('Contract_id',$value['Contract_id'])!!}
                                {!! Form::hidden('FileName',$value['fileName'])!!}
                                <div class="col-lg-2">
                                    <label for="nameid" class="">Contract Name</label>
                                    {!!  Form::text('name',$value['name'],['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required',
                                    'class'=>'form-control m-input',
                                    'disabled'
                                    ])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="numberid" class=" ">Contract Number</label>
                                    {!!  Form::text('number',$value['number'],['id'=>'numberid',
                                    'placeholder'=>'Number Contract',
                                    'required',
                                    'disabled',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="{{$value['validatiion']}}" disabled>
                                </div>
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Name of File</label>
                                    {!!  Form::text('filename',$value['fileName'],['id'=>'fileName',
                                    'placeholder'=>'File Name Contract',
                                    'required',
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
                                    <label for="origin" class=" ">Origin</label>
                                    {!! Form::select('origin',$harbor,$value['origin'],['class'=>'m-select2-general form-control  ','id'=>'origin'])!!}                            
                                </div>
                                @endif
                                @if($value['existdestiny'] == true)
                                <div class="col-2 col-form-label">
                                    <label for="destiny" class=" ">Destiny</label>
                                    {!! Form::select('destiny',$harbor,$value['destiny'],['class'=>'m-select2-general form-control  ','id'=>'destiny'])!!}
                                </div>
                                @endif
                                @if($value['existcarrier'] == true)
                                <div class="col-2 col-form-label">
                                    <label for="carrier" class=" ">Carrier</label>
                                    {!! Form::select('carrier',$carrier,$value['carrier'],['class'=>'m-select2-general form-control','id'=>'carrier'])!!}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <div class="form-group m-form__group row"></div>
                        <div class="form-group m-form__group row">
                            @foreach($targetsArr as $targets)
                            <div class="col-md-3">
                                <div class="m-portlet m-portlet--metal m-portlet--head-solid-bg m-portlet--bordered">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <h3 class="m-portlet__head-text">
                                                    {{$targets}}
                                                    <!--<small>
portlet sub title
</small>-->
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-portlet__body">
                                        <div class="col-md-12">
                                            <label for="carrier" class=" ">Column  in the file excel</label>
                                        </div>
                                        <div class="col-md-12">
                                            {!! Form::select($targets,$coordenates,null,['class' => 'm-select2-general form-control'])!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-group m-form__group row">
                            <div class="col-lg-12 col-lg-offset-4"></div>
                            <div class="col-lg-12 col-lg-offset-12">
                                <button type="submit" class="btn btn-primary form-control">
                                    Process
                                </button>
                            </div>
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
<script>

    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

    $('#originchk').on('click',function(){
        if($('#originchk').prop('checked')){
            $('#origininp').removeAttr('hidden');
            $('#origin').attr('required','required');
        } else{
            $('#origininp').attr('hidden','hidden');
            $('#origin').removeAttr('required');
        }
    }); 

    $('#destinychk').on('click',function(){
        if($('#destinychk').prop('checked')){
            $('#destinyinp').removeAttr('hidden');
            $('#destiny').attr('required','required');
        } else{
            $('#destinyinp').attr('hidden','hidden');
            $('#destiny').removeAttr('required');
        }
    });

    $('#carrierchk').on('click',function(){
        if($('#carrierchk').prop('checked')){
            $('#carrierinp').removeAttr('hidden');
            $('#carrier').attr('required','required');
        } else{
            $('#carrierinp').attr('hidden','hidden');
            $('#carrier').removeAttr('required');
        }
    });


    jQuery(document).ready(function($){
        Dropzone.options.mss = {
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 1,
            maxFilesize: 5, // MB
            addRemoveLinks: true,
            accept: function(file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else { 
                    done(); 
                }
            }   
        };
    });

</script>

@stop