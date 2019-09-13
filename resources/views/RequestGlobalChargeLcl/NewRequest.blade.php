@extends('layouts.app')
@section('css')
@parent
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'New Request GlobalChargers LCL')
@section('content')

<div class="m-content">
    @if (count($errors) > 0)
    <div id="notificationError" class="alert alert-danger">
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

    <!--Begin::Main Portlet-->
    <div class="m-portlet m-portlet--full-height">
        <!--begin: Portlet Head-->
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        New import request for Global Chargers LCL
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
        {!! Form::open(['route'=>'RequestsGlobalchargersLcl.store','method'=>'POST','files'=>true])!!}
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <br>
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="form-group m-form__group row">

                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b>DATA:</b></label>
                                </div>

                                <div class="col-lg-3">
                                    <label for="nameid" class="">Name</label>
                                    {!!  Form::text('name',null,['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required',
                                    'class'=>'form-control m-input'])!!}
                                </div>
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="Please enter validation date">
                                </div>
                                <div class="col-lg-2">
                                    <label><br></label>
                                    <br>
                                    <label for="file" class="btn btn-primary form-control-label form-control" >
                                        Choose File
                                    </label>
                                    <input type="file" class="form-control" name="file" onchange='cambiar()' id="file" required style='display: none;'>
                                    <div id="info" style="color:red"></div>
                                </div>

                            </div>
                            <input type="hidden" name="CompanyUserId" value="{{$user->company_user_id}}" />
                            <input type="hidden" name="user" value="{{$user->id}}" />
                            <!-- <hr> -->
                            <div class="form-group m-form__group row" style='display:none;'>

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
                            <div class="form-group m-form__group row" hidden="hidden" id="divvaluescurren" style='display:none;'>
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
                            <!--    <hr> -->
                            <div class="form-group m-form__group row"style='display:none;'>

                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>DATA:</b></label>
                                </div>


                                <div class="col-3">
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
                                                    Origin Port Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" id="origininp" hidden="hidden" >
                                        {!! Form::select('origin[]',$harbor,null,['class'=>'m-select2-general form-control  ','id'=>'origin','multiple'=>'multiple'])!!}
                                    </div>
                                </div>

                                <div class="col-3">
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
                                                    Destiny Port Not Included
                                                </span>
                                            </span>
                                        </span>
                                    </label>
                                    <div class="col-form-label" id="destinyinp" hidden="hidden" >
                                        {!! Form::select('destiny[]',$harbor,null,['class'=>'m-select2-general form-control  ','id'=>'destiny','multiple'=>'multiple'])!!}
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

                            </div>


                            <div class="form-group m-form__group ">
                                <div class="col-lg-12 col-lg-offset-12 ">
                                    <center>
                                        <div class="col-lg-2 col-lg-offset-2 ">
                                            <br />
                                            <button type="submit" onclick="fileempty()" class="btn btn-primary form-control">
                                                Load Request
                                            </button>
                                        </div>
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
    <!--  begin modal editar rate -->

    <div class="modal fade bd-example-modal-lg" id="modaledit"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Load Request
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div id="edit-modal-body" class="modal-body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-12 order-2 order-xl-1 conten_load">
                                <center>
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <h2 id="mjsH"> Please Wait...</h2>
                                        </div>
                                        <div class="col-sm-6">
                                            <img src="{{asset('images/ship.gif')}}" style="height:170px">
                                        </div>
                                    </div>

                                    <div class="col-sm-8">
                                        <div class="percent">0%</div> Complete
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                            </div>
                                        </div>
                                    </div>
                                </center>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        <h7>Do not leave this window, we will redirect you Thank you.</h7>
                    </center>
                </div>
            </div>

            <!--  end modal editar rate -->


        </div>
    </div>

    @endsection
    @section('js')
    @parent
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
    <script src="{{asset('js/Contracts/ImporContractFcl.js')}}"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>

    <script>
        function fileempty(){
            if( document.getElementById("file").files.length == 0 ){
                swal("Error!", "Choose File", "error");
            }
        }
        function cambiar(){
            var pdrs = document.getElementById('file').files[0].name;
            document.getElementById('info').innerHTML = pdrs;
        } 

        function validate(formData, jqForm, options) {
            var form = jqForm[0];
            if (!form.file.value) {
                alert('File not found');
                return false;
            }
        }

        $(function() {
            var count = 0;
            var bar = $('.progress-bar');
            var percent = $('.percent');
            var status = $('#status');

            $('form').ajaxForm({
                beforeSubmit: validate,
                beforeSend: function() {
                    $('#modaledit').modal('show');
                    status.empty();
                    var percentVal = '0%';
                    var posterValue = $('input[name=file]').fieldValue();
                    bar.width(percentVal)
                    percent.html(percentVal);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                error: function(req, textStatus, errorThrown) {
                    count = 1;
                },
                success: function() {
                    var percentVal = 'Wait, Saving';
                    bar.width(percentVal)
                    $('#mjsH').text('OK');
                    percent.html(percentVal);
                },
                complete: function(xhr) {
                    status.html(xhr.responseText);
                    $('#mjsH').text('Contract Uploaded');
                    if(count == 1){
                        swal({
                            title: "Error",
                            text: "Error, Please try again !",
                            icon: "error",
                            buttons: true,
                        })
                            .then((willDelete) => {
                            if (willDelete) {
                                count = 0;
                                $('#modaledit').modal('hide');
                                window.location.href = "{{route('globalchargeslcl.index')}}";
                            } else {
                                count = 0;
                                $('#modaledit').modal('hide');
                                window.location.href = "{{route('globalchargeslcl.index')}}";
                            }
                        });

                    } else{
                        window.location.href = "{{route('globalchargeslcl.index')}}";
                    }

                    //window.location.href = "{{route('RequestsGlobalchargersFcl.indexListClient')}}";
                }
            });

        });

    </script>

    @stop