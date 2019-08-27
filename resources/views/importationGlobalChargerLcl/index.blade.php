@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('title', 'Importation GlobalCharger LCL')
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
        </div>
        {!! Form::open(['route'=>'Upload.File.Globalcharges.Lcl','method'=>'PUT','files'=>true, 'id' => 'formupload'])!!}
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b>CONTRACT:</b></label>
                                </div>
                                <div class="col-lg-3">
                                    <label for="numberid" class=" ">Company User</label>
                                    {!!  Form::select('CompanyUserId',$companysUser,null,['id'=>'CompanyUserId',
                                    'required',
                                    'class'=>'form-control m-input','onchange' => 'selectvalidate()'])!!}
                                </div>
                                <div class="col-lg-3">
                                    <label for="nameid" class="">Importation Name</label>
                                    {!!  Form::text('name',null,['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <div class="col-lg-3">
                                    <label for="numberid" class=" ">Date Importation</label>
                                    {!!  Form::date('date',\Carbon\Carbon::now(),['id'=>'dateid',
                                    'placeholder'=>'Number Contract',
                                    'required',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b></b></label>
                                </div>
                                <div class="col-lg-3">
                                    <label for="request_id" class=" ">Request Gc Id</label>
                                    {!!  Form::text('request_id',null,['id'=>'request_id',
                                    'placeholder'=>'Request Gc Id',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                            </div>
                            <hr>
                            <div class="form-group m-form__group row"  id="divvaluescurren">
                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>TYPE:</b></label>
                                </div>
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
                                                    Ports and Countries or Regions
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
                                                    Origin Port, Country or Regions Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" id="origininp" hidden="hidden" >
                                        <label for="destiny" class=" ">Ports</label>
                                        {!! Form::select('origin[]',$harbor,null,['class'=>'m-select2-general form-control  ','id'=>'origin','multiple'=>'multiple'])!!}
                                    </div>
                                    <div class="col-form-label" id="origininpCount" hidden="hidden" >
                                        <label for="destiny" class=" ">Countries</label>
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
                                                <input name="DatCar" id="carrierchk" type="checkbox">
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
                                        {!! Form::select('carrier',$carrier,null,['class'=>'m-select2-general form-control','id'=>'carrier'])!!}
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
                                <div class="col-2"></div>
                                <div class="col-3">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="DatDtValid" id="datevaiditychk" type="checkbox">
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Date Validity Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>

                                    <div class="col-form-label" hidden="hidden" id="datevaiditydiv">
                                        {!! Form::text('validitydate','Please enter validation date',['class'=>'form-control m-input datevalidityinp','id'=>'m_daterangepicker_1','placeholder' => 'Date Validity' ])!!}
                                    </div>
                                </div>
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
<script src="{{asset('js/Globalchargers/ImporttationGlobalchargersFcl.js')}}"></script>

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