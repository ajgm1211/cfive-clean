@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection
@if($selector == 1)
@section('title', 'Importation FCL R. '.$requestfcl['id'].' / '.$requestfcl['namecontract'])
@elseif($selector == 2)
@section('title', 'Importation FCL C. '.$contract['id'].' / '.$contract['name'])
@endif
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
        {!! Form::open(['route'=>'Upload.File.New.Contracts','method'=>'PUT','files'=>true, 'id' => 'formupload'])!!}
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <div class="row">
                        <div class="col-lg-12">
                            @if($selector == 1)
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b>CONTRACT:</b></label>
                                </div>
                                <div class="col-lg-3">
                                    <label for="nameid" class="">Contract Name</label>
                                    {!!  Form::text('name',$requestfcl['namecontract'],['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="{{$requestfcl['validation']}}">
                                </div>
                                <div class="col-lg-3">
                                    <label for="numberid" class=" ">Company User</label>
                                    {!!  Form::select('CompanyUserId',$companysUser,$requestfcl['company_user_id'],['id'=>'CompanyUserId',
                                    'required',
                                    'class'=>'form-control m-input','onchange' => 'selectvalidate()'])!!}
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2"> </div>

                                <div class="col-lg-3">
                                    <label for="request_id" class=" ">Request id</label>
                                    {!!  Form::text('request_id',$requestfcl['id'],['id'=>'request_id',
                                    'placeholder'=>'Request Id',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <div class="col-lg-3">
                                    <label class="">Carriers</label>
                                    <div class="" id="carrierMul">
                                        {!! Form::select('carrierM[]',$carrier,$requestfcl->Requestcarriers->pluck('carrier_id'),['class'=>'m-select2-general form-control','id'=>'carrierM','required','multiple'=>'multiple'])!!}
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="">Direction</label>
                                    <div class="" id="direction">
                                        {!! Form::select('direction',$direction,$requestfcl['direction_id'],['class'=>'m-select2-general form-control','required','id'=>'direction'])!!}
                                    </div>
                                </div>
                            </div>
                            @elseif($selector == 2)
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b>CONTRACT:</b></label>
                                </div>
                                <div class="col-lg-3">
                                    <label for="nameid" class="">Contract Name</label>
                                    {!!  Form::text('nameD',$contract['name'],['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required','disabled',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <input type="hidden" name="name" value="{{$contract['name']}}">
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" disabled required="required" name="validation_expire" type="text" value="{{$contract['validity'].' / '.$contract['expire']}}">
                                </div>
                                <div class="col-lg-3">
                                    <label for="numberid" class=" ">Company User</label>
                                    {!!  Form::select('CompanyUserIdd',$companysUser,$contract['company_user_id'],['id'=>'CompanyUserId',
                                    'required','disabled',
                                    'class'=>'form-control m-input','onchange' => 'selectvalidate()'])!!}
                                </div>
                                <input type="hidden" name="CompanyUserId" value="{{$contract['company_user_id']}}">                                
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2"> </div>

                                <div class="col-lg-3">
                                    <label for="request_id" class=" ">Request id</label>
                                    {!!  Form::text('request_idd',$request_id,['id'=>'request_id',
                                    'placeholder'=>'Request Id','disabled',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <input type="hidden" name="request_id" value="{{$request_id}}">
                                <div class="col-lg-3">
                                    <label class="">Carriers</label>
                                    <div class="" id="carrierMul">
                                        {!! Form::select('carrierM[]',$carrier,$contract->carriers->pluck('carrier_id'),['class'=>'m-select2-general form-control','disabled','id'=>'carrierM','required','multiple'=>'multiple'])!!}
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="">Direction</label>
                                    <div class="" id="direction">
                                        {!! Form::select('direction',$direction,$contract['direction_id'],['class'=>'m-select2-general form-control','disabled','required','id'=>'direction'])!!}
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="contract_id" value="{{$contract['id']}}">
                            @endif
                            <input type="hidden" name="selector" value="{{$selector}}">
                            <hr>
                            <div class="form-group m-form__group row">

                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>TYPE:</b></label>
                                </div>


                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="type" value="1" id="rdRate" type="radio" checked>
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Rates
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="type" value="2" id="rdRateSurcharge" type="radio" >
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Rates &nbsp; + &nbsp; Surcharges
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>

                            </div>

                            <div class="form-group m-form__group row"  id="divvaluescurren">
                                <div class="col-2"></div>
                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="valuesCurrency" value="1"  type="radio" >
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Values Only
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="valuesCurrency" value="2"  type="radio" checked>
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Values With Currency
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group m-form__group row"  id="divvaluesschedules">
                                <div class="col-2"></div>
                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="DatShe" id="schedulechk" checked type="checkbox">
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Info Schedules Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row"  id="divvaluesportscountries">
                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>PLACES:</b></label>
                                </div>

                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="valuesportcountry" value="1" id="portchk" type="radio" checked>
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Ports Only
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="valuesportcountry" value="2" id="portcountrychk" type="radio" >
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Ports and Countries or Region
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row">

                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>DATA:</b></label>
                                </div>


                                <div class="col-3" id="divorigin">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="DatOri" id="originchk" type="checkbox">
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Origin Port or Country Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" id="origininp" hidden="hidden" >
                                        <label for="origin" class=" ">Ports</label>
                                        {!! Form::select('origin[]',$harbor,null,['class'=>'m-select2-general form-control  ','id'=>'origin','multiple'=>'multiple'])!!}
                                    </div>
                                    <div class="col-form-label" id="origininpCount" hidden="hidden" >
                                        <label for="originCountry" class=" ">Countries</label>
                                        {!! Form::select('originCount[]',$country,null,['class'=>'m-select2-general form-control  ','id'=>'originCountry','multiple'=>'multiple'])!!}
                                    </div>
                                    <div class="col-form-label" id="origininpRegion" hidden="hidden" >
                                        <label for="originRegion" class=" ">Regions</label>
                                        {!! Form::select('originRegion[]',$region,null,['class'=>'m-select2-general form-control  ','id'=>'originRegion','multiple'=>'multiple'])!!}
                                    </div>
                                </div>

                                <div class="col-3" id="divdestiny">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="DatDes" id="destinychk" type="checkbox">
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Destiny Port or Country Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" id="destinyinp" hidden="hidden" >
                                        <label for="destiny" class=" ">Ports</label>
                                        {!! Form::select('destiny[]',$harbor,null,['class'=>'m-select2-general form-control  ','id'=>'destiny','multiple'=>'multiple'])!!}
                                    </div>
                                    <div class="col-form-label" id="destinyinpCount" hidden="hidden" >
                                        <label for="destiny" class=" ">Countries</label>
                                        {!! Form::select('destinyCount[]',$country,null,['class'=>'m-select2-general form-control  ','id'=>'destinyCountry','multiple'=>'multiple'])!!}
                                    </div>
                                    <div class="col-form-label" id="destinyinpRegion" hidden="hidden" >
                                        <label for="destinyRegion" class=" ">Regions</label>
                                        {!! Form::select('destinyRegion[]',$region,null,['class'=>'m-select2-general form-control  ','id'=>'destinyRegion','multiple'=>'multiple'])!!}
                                    </div>
                                </div>
                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                @if($load_carrier)
                                                <input name="DatCar" id="carrierchk" type="checkbox" checked>
                                                @else
                                                <input name="DatCar" id="carrierchk" type="checkbox">                       
                                                @endif
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Carrier Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" hidden="hidden" id="carrierinp">
                                        @if($load_carrier)
                                            @if($selector == 1)
                                                {!! Form::select('carrier',$carrier,$requestfcl->Requestcarriers->pluck('carrier_id'),['class'=>'m-select2-general form-control','id'=>'carrier'])!!}
                                            @elseif($selector == 2)
                                                {!! Form::select('carrier',$carrier,$contract->carriers->pluck('carrier_id'),['class'=>'m-select2-general form-control','id'=>'carrier'])!!}
                                            @endif
                                        @else
                                        {!! Form::select('carrier',$carrier,null,['class'=>'m-select2-general form-control','id'=>'carrier'])!!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2"></div>

                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="Datftynor" id="fortynorchk" type="checkbox">
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Includes 40'NOR Column
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="Datftyfive" id="fortyfivechk" type="checkbox">
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Includes 45 Column
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-3" id="divtyped">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="DatTypeDes" id="typedestinychk" type="checkbox">
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Type Destiny Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" hidden="hidden" id="typedestinyinp">
                                        {!! Form::select('typedestiny',$typedestiny,null,['class'=>'m-select2-general form-control','id'=>'typedestiny'])!!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">

                            </div>
                            <br>
                            <div class="form-group m-form__group row">

                                <div class="col-lg-12">
                                    <br>
                                    <center>
                                        <label for="file" class="btn btn-primary" style="padding: 11px 18px;">
                                            <i class="la la-cloud-upload"></i>&nbsp; Choose File
                                        </label>
                                        <input type="file" class="" name="file" onchange='cambiar()' id="file" required style='display: none;'>
                                        <div id="info" style="color:red"></div>
                                    </center>
                                </div>

                            </div>
                            <br>
                            <br>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12 col-lg-offset-12" id="scrollToHere">
                                    <center>
                                        <button type="submit" id="loadbutton" class="btn btn-success col-2 form-control">
                                            Load
                                        </button>

                                        <a href="#" id="validatebutton" onclick="validar()" class="btn btn-primary col-2 form-control"> Validate</a>
                                    </center>
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



</div>

@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="{{asset('js/Contracts/ImporContractFcl.js')}}"></script>

<script>
    $(document).ready(function(){
        $('#loadbutton').hide();
    });

    function fileempty(){
        if( document.getElementById("file").files.length == 0 ){
            swal("Error!", "Choose File", "error");
        }
    }
    function cambiar(){
        var pdrs = document.getElementById('file').files[0].name;
        document.getElementById('info').innerHTML = pdrs;
    } 

    function selectvalidate(){
        var id = $('#CompanyUserId').val();
        //alert(id);
        $('#validatebutton').show();
        $('#loadbutton').hide();
    }

    function validar(){
        var id = $('#CompanyUserId').val();

        url='{!! route("validate.import",":id") !!}';
        url = url.replace(':id', id);
        // $(this).closest('tr').remove();
        $.ajax({
            url:url,
            method:'get',
            success: function(data){
                swal({
                    title: 'Are you sure?',
                    text: "Selected company: "+data.name,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, select it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then(function(result){
                    if (result.value) {

                        $('#validatebutton').hide();
                        $('#loadbutton').show();

                        $('html,body').animate({
                            scrollTop: $("#scrollToHere").offset().top
                        }, 2000);
                        fileempty();

                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'You can validate again :)',
                            'error'
                        )
                    }
                });
            }
        });






    }

</script>

@stop