@extends('layouts.app')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.b-select').multiselect();
        $('.multiselect-selected-text').text('Select an option',"title", "my new title" );
    });

</script>
@section('css')
@parent
<style>
    .multiselect-selected-text {
        font-size: small;
        /*color: #9699a2;*/
    }

</style>
<link rel="stylesheet" href="/css/bootstrap-multiselect.css">

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
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <h5 class="m-portlet__head-text">
                        <strong  style="color:#0062ff;">Import Transit Time</strong>
                    </h5>
                    <br>
                    <div class="row">
                        <div class="col-lg-12">
                            {!! Form::open(['route'=>'ImpTransitTime.store','method'=>'POST','id'=>'form','files'=>true])!!}
                            @csrf
                            <div class="form-group m-form__group row">
                            </div>
                            <div class="form-group m-form__group row">
                            </div>

                            <div class="col-md-4 col-md-offset-4">&nbsp;</div>
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
                                                            <input type="hidden" id="existsFile" name="existsFile" value="">
                                                            <div class="form-group m-form__group row ">
                                                                <div class="col-lg-2 col-centered">
                                                                    <input type="button" id="submitRequest"   value="Save"  class="btn btn-primary form-control">
                                                                </div>
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
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--end: Form Wizard-->
    </div>
    <div class="modal fade bd-example-modal-lg" id="modaledit"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Load File
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

    <!--End::Main Portlet-->
    <!--  begin modal editar rate -->
</div>

@endsection
@section('js')
@parent

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="{{asset('js/Contracts/ImporContractFcl.js')}}"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script type="application/x-javascript" src="/js/toarts-config.js"></script>

<script>
    $(document).ready(function(){

    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#submitRequest").on('click', function(e){
        var $fileVal = $('#existsFile').val();
        if($fileVal >= 1){
            $("#modaledit").modal('show');
            $("#form").submit();
        }else {
            toastr.error('Select a file! ');
        }
    });

    var uploadedDocumentMap = {}

    function existsFiles(){
        var  files = null;
        var  files = $('#files').val();
        console.log(files);
        if(files != null){
            $('#submitRequest').removeAttr('disabled');
            $('#submitRequest').removeAttr('hidden');
        } else {
            $('#submitRequest').attr('disabled','disabled');
            $('#submitRequest').attr('hidden','hidden');
        }
    }

    Dropzone.options.documentDropzone = {
        url: '{{ route("ImpTransitTime.storeMedia") }}',
        maxFilesize: 15, // MB
        maxFiles: 1,
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
            $('#form').append('<input type="hidden" id="files" name="document" value="' + response.name + '">')
            uploadedDocumentMap[file.name] = response.name;
            $('#form').find('input[name="existsFile"]').val(1);
        },
        removedfile: function (file) {
            file.previewElement.remove()
            $('#form').find('input[name="existsFile"]').val('');
            $('#form').find('input[name="document"]').remove();
        },
        init: function() {
            this.on("maxfilesexceeded", function(file){
                file.previewElement.remove();
                toastr.error('You can’t upload more than 1 file!');
            });
        }
    }


</script>

@stop