@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection

@section('title', 'New Request')
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
                        New Import Request
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
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <br>
                    <div class="row">
                        <div class="col-lg-12">

                            <form method="post" id="form" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group m-form__group row">

                                    <div class="col-lg-2">
                                        <label for="nameid" class="">References</label>
                                        {!!  Form::text('name',null,['id'=>'nameid',
                                        'placeholder'=>'References  ',
                                        'required',
                                        'class'=>'form-control m-input'])!!}
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="validation_expire" class=" ">Validation</label>
                                        <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="Please enter validation date">
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="">Carrier</label>
                                        <div class="" id="carrierMul">
                                            {!! Form::select('carrierM[]',$carrier,null,['class'=>'m-select2-general form-control','id'=>'carrierM','required','multiple'=>'multiple'])!!}
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="">Direction</label>
                                        <div class="" id="direction">
                                            {!! Form::select('direction',$direction,null,['class'=>'m-select2-general form-control','required','id'=>'direction'])!!}
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group row ">
                                            <div class="col-lg-4">
                                                <label><br></label>
                                                <!--<button type="text" id="btnFiterSubmitSearch"  class="btn btn-primary form-control">Search</button>-->
                                                <a href="#" id="btnFiterSubmitSearch"  class="btn btn-primary form-control">Search</a>
                                            </div>
                                            <div class="col-lg-4">
                                                <label><br></label>
                                                <br>
                                                <label for="file" class="btn btn-primary form-control-label form-control" >
                                                    Choose File
                                                </label>
                                                <input type="file" class="form-control" name="file" onchange='cambiar()' id="file" required style='display: none;'>
                                                <div id="info" style="color:red"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label><br></label>
                                                <button type="submit" class="btn btn-primary form-control" onclick="fileempty()" >
                                                    <i class=""></i>Import File
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="CompanyUserId" value="{{$user->company_user_id}}" />
                                <input type="hidden" name="user" value="{{$user->id}}" />
                                <!--  No aplica por el momento Inicio ----------------------------------------- -->
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

                                <!--  No aplica por el momento Fin -------------------------------------------- -->
                            </form>

                        </div>
                        <div class="col-lg-12">
                            <!--<form method="POST" id="search-form" class="form-inline" role="form">
@csrf
<div class="form-group">
<label for="name">Name</label>
<input type="text" class="form-control" name="namead" id="namead" placeholder="search name">
</div>

<button type="submit" class="btn btn-primary">Search</button>
</form>-->

                            <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="100%" style="width:100%">
                                <thead >
                                    <tr>
                                        <th style="width:30%">Reference</th>
                                        <th >Direction</th>
                                        <th >Carriers</th>
                                        <th >Validation</th>
                                        <th >Expire</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                    <div id="uploadStatus"></div>
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
                        <h7>Do not close this window. You will be redirected in a few moments.</h7>
                    </center>
                </div>
            </div>

            <!--  end modal editar rate -->


        </div>
    </div>


</div>

@endsection
@section('js')
@parent

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="{{asset('js/Contracts/ImporContractFcl.js')}}"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>

<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var oTable = $('#requesttable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route("Similar.Contracts.Request",$user->company_user_id) !!}',
            data: function (d) {
                d.carrierM = $('select#carrierM').val();
                var date = ($('#m_daterangepicker_1').val()).split(" / ");
                d.dateO = date[0];
                d.dateT = date[1];
                d.direction = $('#direction select').val();
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'direction', name: 'direction' },
            { data: 'carrier', name: 'carrier' },
            { data: 'validity', name: 'validity' },
            { data: 'expire', name: 'expire' }
        ],
        "stateSave": true
    });

    $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    });    

    $('#btnFiterSubmitSearch').click(function(){
        $('#nameid').removeAttr('required');
        $('#carrierM').removeAttr('required');
        $('#direction').removeAttr('required');
        $('#requesttable').DataTable().draw(true);
        $('#nameid').attr('required','required');
        $('#carrierM').attr('required','required');
        $('#direction').attr('required','required');
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
    function validate(formData, jqForm, options) {
        var form = jqForm[0];
        if (!form.file.value) {
            //alert('File not found');
            return false;
        }
    }

    $("#form").on('submit', function(e){
        var date = $('#m_daterangepicker_1').val().split(' / ');
        var date_star = $.trim(date[0]);
        var date_end  = $.trim(date[1]);
        e.preventDefault();
        if(date_star == date_end){
            swal(
                "Error",
                "Error, Please select the date!", "error",
                true,
            );
        }else {

            var count =0;
            var bar = $('.progress-bar');
            var percent = $('.percent');
            var status = $('#status');
            var data = new FormData(this)
            $.ajax({
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = ((evt.loaded / evt.total) * 100);
                            $(".progress-bar").width(percentComplete + '%');
                            $(".progress-bar").html(percentComplete+'%');
                            var percentVal = percentComplete + '%';
                            bar.width(percentVal);
                            percent.html(percentVal);
                        }
                    }, false);
                    return xhr;
                },
                url: '{{route("RequestImportation.store2")}}',
                method: 'post',
                data:new FormData(this),
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSubmit: validate,
                beforeSend: function(){
                    $('#modaledit').modal('show');
                    $(".progress-bar").width('0%');
                    status.empty();
                    var percentVal = '0%';
                    bar.width(percentVal)
                    percent.html(percentVal);
                },
                error:function(){
                    $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
                },
                success: function(resp){
                    if(resp.data == 'ok'){
                        $('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
                        $('#modaledit').modal('hide');
                        window.location.href = "{{route('contracts.index')}}";
                    }else if(resp.data == 'err'){
                        $('#uploadStatus').html('<p style="color:#EA4335;">Error, try again.</p>');
                        $('#modaledit').modal('hide');
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
                                window.location.href = "{{route('contracts.index')}}";
                            } else {
                                count = 0;
                                $('#modaledit').modal('hide');
                                window.location.href = "{{route('contracts.index')}}";
                            }
                        });
                    }
                }
            });
        }

    });



</script>

@stop