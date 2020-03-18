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
                    </h3>
                </div>
            </div>
            <!--
<div class="m-portlet__head-tools">
<ul class="m-portlet__nav">
<li class="m-portlet__nav-item">
<a href="#" data-toggle="m-tooltip" class="m-portlet__nav-link m-portlet__nav-link--icon" data-direction="left" data-width="auto" title="Get help with filling up this form">
<i class="flaticon-info m--icon-font-size-lg3"></i> 
</a>
</li>
</ul>
</div>
-->
        </div>
        {!! Form::open(['route'=>'Upload.File.New.Contracts','method'=>'PUT','files'=>true, 'id' => 'formupload'])!!}
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <div class="row">
                        <div class="col-lg-12">
                            @if($selector == 1)
                            <div class="form-group m-form__group row">
                                <div class="col-lg-3">
                                    <label for="nameid" class="">Contract Name</label>
                                    {!!  Form::text('name',$requestfcl['namecontract'],['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <div class="col-lg-2">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="{{$requestfcl['validation']}}">
                                </div>
                                <div class="col-lg-2">
                                    <label for="numberid" class=" ">Company User</label>
                                    {!!  Form::select('CompanyUserId',$companysUser,$requestfcl['company_user_id'],['id'=>'CompanyUserId',
                                    'required',
                                    'class'=>'form-control m-input','onchange' => 'selectvalidate()'])!!}
                                </div>
                                <div class="col-lg-1">
                                    <label for="request_id" class=" ">Request id</label>
                                    {!!  Form::text('request_id',$requestfcl['id'],['id'=>'request_id',
                                    'placeholder'=>'Request Id',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <div class="col-lg-1">
                                    <label class="">Direction</label>
                                    <div class="" id="direction">
                                        {!! Form::select('direction',$direction,$requestfcl['direction_id'],['class'=>'m-select2-general form-control','required','id'=>'direction'])!!}
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="">Carriers</label>
                                    <div class="" id="carrierMul">
                                        {!! Form::select('carrierM[]',$carrier,$requestfcl->Requestcarriers->pluck('carrier_id'),['class'=>'m-select2-general form-control','id'=>'carrierM','required','multiple'=>'multiple'])!!}
                                    </div>
                                </div>
                            </div>
                            @elseif($selector == 2)
                            <div class="form-group m-form__group row">
                                <div class="col-lg-3">
                                    <label for="nameid" class="">Contract Name</label>
                                    {!!  Form::text('nameD',$contract['name'],['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required','disabled',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <input type="hidden" name="name" value="{{$contract['name']}}">
                                <div class="col-lg-2">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" disabled required="required" name="validation_expire" type="text" value="{{$contract['validity'].' / '.$contract['expire']}}">
                                </div>
                                <div class="col-lg-2">
                                    <label for="numberid" class=" ">Company User</label>
                                    {!!  Form::select('CompanyUserIdd',$companysUser,$contract['company_user_id'],['id'=>'CompanyUserId',
                                    'required','disabled',
                                    'class'=>'form-control m-input','onchange' => 'selectvalidate()'])!!}
                                </div>
                                <input type="hidden" name="CompanyUserId" value="{{$contract['company_user_id']}}">

                                <div class="col-lg-1">
                                    <label for="request_id" class=" ">Request id</label>
                                    {!!  Form::text('request_idd',$request_id,['id'=>'request_id',
                                    'placeholder'=>'Request Id','disabled',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <input type="hidden" name="request_id" value="{{$request_id}}">
                                <div class="col-lg-1">
                                    <label class="">Direction</label>
                                    <div class="" id="direction">
                                        {!! Form::select('direction',$direction,$contract['direction_id'],['class'=>'m-select2-general form-control','disabled','required','id'=>'direction'])!!}
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="">Carriers</label>
                                    <div class="" id="carrierMul">
                                        {!! Form::select('carrierM[]',$carrier,$contract->carriers->pluck('carrier_id'),['class'=>'m-select2-general form-control','disabled','id'=>'carrierM','required','multiple'=>'multiple'])!!}
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="contract_id" value="{{$contract['id']}}">
                            @endif
                            <input type="hidden" name="selector" value="{{$selector}}">
                            <hr>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>DATA:</b></label>
                                </div>
                                <div class="col-2">
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
                                <div class="col-2" id="divtyped">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                                                <input name="DatTypeDes" id="typedestinychk" checked type="checkbox">
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
                                    <div class="col-form-label"  id="typedestinyinp">
                                        {!! Form::select('typedestiny',$typedestiny,3,['class'=>'m-select2-general form-control','id'=>'typedestiny'])!!}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="valuesCurrency" value="3" class="currencychk" checked type="radio" >
                                                <span></span>
                                            </span>
                                        </span>
                                        <span class="m-option__label">
                                            <span class="m-option__head">
                                                <span class="m-option__title">
                                                    Specific currency
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" hidden="hidden" id="currencyinp">
                                        {!! Form::select('currency',$coins,$currency,['class'=>'m-select2-general form-control','id'=>'currency'])!!}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="valuesCurrency" class="currencychk" value="1"  type="radio" >
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
                                <div class="col-2">
                                    <label class="m-option">
                                        <span class="m-option__control">
                                            <span class="m-radio m-radio--brand m-radio--check-bold">
                                                <input name="valuesCurrency" value="2" class="currencychk" type="radio">
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

                                <div class="col-2">
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
                                <div class="col-2">
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
                                                    Ports - Countries - Regions
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                </div>

                            </div>
                            <div class="form-group m-form__group row">

                            </div>
                            <!--
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
--><input type="hidden" id="existsFile" name="existsFile" value="">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <!--begin::Section-->
                                    <div class="m-section m-section--last">
                                        <div class="m-section__content">
                                            <!--begin::Preview-->
                                            <div class="m-demo">
                                                <div class="m-demo__preview">
                                                    <div class="m-list-search">
                                                        <div class="m-list-search__results">
                                                            <span class="m-list-search__result-message m--hide">
                                                                No record found
                                                            </span>
                                                            <span class="m-list-search__result-category m-list-search__result-category--first" style="text-transform: initial;" >
                                                                Upload
                                                            </span>
                                                            <br>
                                                            <style>
                                                                .m-list-search .m-list-search__results .m-list-search__result-category {
                                                                    color: #45426c;
                                                                }
                                                                .m-demo {
                                                                    background: #f7f7fa;
                                                                    margin-bottom: 20px;
                                                                    border-radius: 10px ;
                                                                }
                                                                .m-dropzone.m-dropzone--success {
                                                                    border-color: rgba(46, 35, 175, 0.28);
                                                                }
                                                                .m-dropzone {
                                                                    border: 1px solid;

                                                                }
                                                                .dropzone {
                                                                    background: #f7f7fa;
                                                                    border-radius: 10px;
                                                                }
                                                                .m-demo .m-demo__preview {
                                                                    /*border-radius: 100px;*/
                                                                    background: #f7f7fa;
                                                                    border: 4px solid #f7f7fa;
                                                                    border-radius: 10px;
                                                                    padding: 30px;
                                                                }
                                                            </style>
                                                            <div class="tabDrag ">
                                                                <div class="m-dropzone dropzone m-dropzone--success"  id="document-dropzone">
                                                                    <div class="m-dropzone__msg dz-message needsclick">
                                                                        <img class="img-dropzone" style="margin-bottom:15px;" src="/images/upload-files.png" alt="Smiley face" height="100" width="100">
                                                                        <h3 style="margin-bottom:10px;" class="m-dropzone__msg-title">
                                                                            Drag and drop a file here 
                                                                            <br>
                                                                            or
                                                                        </h3>
                                                                        <a href="#" class="btn btn-primary col-2" > Choose file </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <br>
                                                            <style>
                                                                .col-centered{
                                                                    float: none;
                                                                    margin: 0 auto;
                                                                }
                                                            </style>
                                                            <div class="col-lg-2 col-centered">
                                                                <center>
                                                                    <a href="#" id="validatebutton"  onclick="validar()" class="btn btn-primary col-lg-9 form-control"> Validate</a>
                                                                </center>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <!--end::Section-->
                                </div>
                            </div>
                            <br>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12 col-lg-offset-12" id="scrollToHere">
                                    <center>
                                        <!--
<button type="submit" id="loadbutton" class="btn btn-success col-2 form-control">
Load
</button>
-->
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
<script type="application/x-javascript" src="/js/toarts-config.js"></script>
<script src="{{asset('js/importation/import-new-fcl.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#loadbutton').hide();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
        var $fileVal = $('#existsFile').val();
        if($fileVal >= 1){
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
                            $('html,body').animate({
                                scrollTop: $("#scrollToHere").offset().top
                            }, 2000);
                            $('#formupload').submit();

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
        }else {
            toastr.error('Select a file! ');
        }
    }

    Dropzone.options.documentDropzone = {
        url: '{{ route("importation.storeMedia.fcl") }}',
        maxFilesize: 15, // MB
        maxFiles: 1,
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
            $('#formupload').append('<input type="hidden" id="files" name="document" value="' + response.name + '">')
            $('#formupload').find('input[name="existsFile"]').val(1);
            uploadedDocumentMap[file.name] = response.name;
        },
        removedfile: function (file) {
            file.previewElement.remove()
            $('#formupload').find('input[name="existsFile"]').val('');
            $('#formupload').find('input[name="document"]').remove();
        },
        init: function() {
            this.on("maxfilesexceeded", function(file){
                file.previewElement.remove();
                toastr.error('You can’t upload more than 1 file!');
            });
        },
        acceptedFiles: ".xlsx, .xls"
    }

</script>

@stop