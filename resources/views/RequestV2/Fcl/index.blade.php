@extends('layouts.app')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.b-select').multiselect();
        $('.multiselect-selected-text').text('Select an option', "title", "my new title");
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
    <link rel="stylesheet" href="/css/equipment.css">


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
        @if (Session::has('message.nivel'))

            <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show"
                role="alert">
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
                            <strong style="color:#0062ff;">Import Contract - Sea Freight FCL</strong>
                        </h5>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                {!! Form::open(['route' => 'RequestFcl.store', 'method' => 'POST', 'id' => 'form', 'files' => true]) !!}
                                @csrf
                                <div class="form-group m-form__group row">
                                    <div class="col-lg-2">
                                        <label class="">Carrier</label>
                                        <div class="" id="carrierMul">
                                            {!! Form::select('carrierM[]', $carrier, null, ['class' => 'm-select2-general form-control', 'id' => 'carrierM', 'required', 'inpName' => 'Carrier', 'multiple' => 'multiple']) !!}
                                        </div>
                                    </div>
                                    <div class="col-lg-2" id="equipment_id">
                                        <label>Equipments</label>
                                        {{ Form::select('equipment[]', $contain, $equipment, ['class' => 'c5-select-multiple select-group', 'id' => 'equipment', 'multiple' => 'multiple', 'required' => 'true', 'select-type' => 'groupLabel']) }}

                                    </div>

                                    <div class="col-lg-3">
                                        <label for="validation_expire" class=" ">Validity</label>
                                        <input placeholder="Contract Validity" inpName="Validation"
                                            class="form-control m-input" readonly="" id="m_daterangepicker_1"
                                            required="required" name="validation_expire" type="text"
                                            value="Please enter validation date">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="nameid" class="">Reference</label>
                                        {!! Form::text('name', null, ['id' => 'nameid', 'placeholder' => 'References  ', 'required', 'inpName' => 'References', 'class' => 'form-control m-input']) !!}
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="">Direction</label>
                                        <div class="" id="direction">
                                            {!! Form::select('direction', $direction, null, ['class' => 'm-select2-general form-control', 'inpName' => 'Direction', 'required', 'id' => 'direction']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                </div>

                                <input type="hidden" name="CompanyUserId" value="{{ $user->company_user_id }}" />
                                <input type="hidden" name="user" value="{{ $user->id }}" />

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
                                                                <span
                                                                    class="m-list-search__result-category m-list-search__result-category--first"
                                                                    style="text-transform: initial;">
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
                                                                        border-radius: 10px;
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
                                                                    <div class="m-dropzone dropzone m-dropzone--success"
                                                                        id="document-dropzone">
                                                                        <div class="m-dropzone__msg dz-message needsclick">
                                                                            <img class="img-dropzone"
                                                                                style="margin-bottom:15px;"
                                                                                src="/images/upload-files.png"
                                                                                alt="Smiley face" height="100" width="100">
                                                                            <h3 style="margin-bottom:10px;"
                                                                                class="m-dropzone__msg-title">
                                                                                Drag and drop a file here
                                                                                <br>
                                                                                or
                                                                            </h3>
                                                                            <a href="#" class="btn btn-primary col-2">
                                                                                Choose file </a>
                                                                            <br>
                                                                            <br>
                                                                            <small>Only formats ('XLSX, XLS, CSV,
                                                                                PDF')</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                                <style>
                                                                    .col-centered {
                                                                        float: none;
                                                                        margin: 0 auto;
                                                                    }

                                                                </style>
                                                                <input type="hidden" id="existsFile" name="existsFile"
                                                                    value="">
                                                                <div class="form-group m-form__group row ">
                                                                    <div class="col-lg-2 col-centered">
                                                                        <input type="button" id="submitRequest" value="Save"
                                                                            class="btn btn-primary form-control">
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
        <div class="modal fade bd-example-modal-lg" id="modaledit" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
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
                                                <img src="{{ asset('images/ship.gif') }}" style="height:170px">
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

        <!--End::Main Portlet-->
        <!--  begin modal editar rate -->
    </div>

@endsection
@section('js')
    @parent

    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript">
    </script>
    <script src="{{ asset('js/Contracts/ImporContractFcl.js') }}"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>
    <script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>
    <script src="/js/bootstrap-multiselect.js"></script>
    <script type="application/x-javascript" src="/js/toarts-config.js"></script>

    <script>
        $(document).ready(function() {
            $('#equipment').selectC5();

        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        //C5 Select
        (function($) {
            $.fn.selectC5 = function() {
                var clickOnID = '' + $(this).attr('id') + '';
                var optionSelect = '#' + $(this).attr('id') + ' option';
                var selectType = '' + $(this).attr('select-type') + '';
                var selectContainer = $('select#' + clickOnID + ' option').val();
                var multiSelect = '<span class="c5-select-multiple-dropdown ' + clickOnID + '">' +
                    '<ul class="c5-select-dropdown-list select-list">' +
                    '</ul>' +
                    '</span>' +
                    '<span class="c5-select-multiple-container ' + clickOnID + '">' +
                    /*  '<span class="c5-select-header">Types</span>' +
                     '<ul class="c5-select-list list-types-carriers">' +
                     '<li class="c5-case"><label class="c5-label">CMA CGM Spot' +
                     '<input id="mode4" type="checkbox" class="c5-check" value="CMA" title="CMA">' +
                     '<span class="checkmark"></span></label></li>' +
                     '<li class="c5-case"><label class="c5-label">MAERSK Spot' +
                     '<input id="mode5" type="checkbox" class="c5-check" value="MAERSK" title="MAERSK">' +
                     '<span class="checkmark"></span></label></li>' +
                     '<li class="c5-case"><label class="c5-label">SAFMARINE Spot' +
                     '<input id="mode6" type="checkbox" class="c5-check" value="SAFMARINE" title="SAFMARINE">' +
                     '<span class="checkmark"></span></label></li>' +
                     '</ul>' + */
                    '<span class="c5-select-header">Carriers</span>' +
                    '<span class="c5-select-container-close">' +
                    '<i class="fa fa-times" aria-hidden="true"></i>' +
                    '</span>' +
                    '<span class="c5-select-multiple-switch">' +
                    'Select All ' +
                    '<label class="switch">' +
                    '<input type="checkbox" class="c5-switch">' +
                    '<span class="slider round"></span>' +
                    '</label>' +
                    '</span>' +
                    '<ul class="c5-select-list select-normal"></ul>' +
                    '</span>';
                var multiSelectGroup = '<span class="c5-select-multiple-dropdown ' + clickOnID + '">' +
                    '<ul class="c5-select-dropdown-list select-list">' +
                    '</ul>' +
                    '</span>' +
                    '<span class="c5-select-multiple-container ' + clickOnID + '">' +
                    '<span class="c5-select-container-close">' +
                    '<i class="fa fa-times" aria-hidden="true"></i>' +
                    '</span>' +
                    '<span class="c5-select-header">Types</span>' +
                    '<ul class="c5-select-list list-group1"></ul>' +
                    '<span class="c5-select-header h-hidden">Equipment List</span>' +
                    '<ul class="c5-select-list list-group2"></ul>' +
                    '</span>';
                // Select Multiple con swicth

                // Select Multiple con Lables
                if (selectType == 'groupLabel') {
                    $(this).after(multiSelectGroup);
                    var showEquip = $('.select-list li.hida');
                    var data = '{{ $group_contain }}';
                    var newData = JSON.parse(data.replace(/&quot;/g, '"'));
                    var defaultValues = $('#' + clickOnID + '').val();
                    var containerType = '{{ $containerType }}';
                    getContainerByGroup('' + containerType + '');
                    for (var i in newData) {
                        var code = `${newData[i]}`;
                        //console.log(i, code);
                        var list2 = '<li class="c5-case"><label class="c5-label">' + code +
                            '<input type="radio" name="container_type" onclick="getContainerByGroup(' + i +
                            ')" class="c5-check" title="' + code + '" value="' + i +
                            '"><span class="checkmark"></span></label></li>';
                        $('.list-group1').append(list2);
                    }
                    $('.list-group1 .c5-case:nth-child(' + containerType + ') input').attr('checked', true);
                }
                $('.c5-select-multiple-dropdown.' + clickOnID + '').on('click', function() {
                    $('.c5-select-multiple-container.' + clickOnID + '').toggle();
                    $('.' + clickOnID + ' .c5-select-dropdown-list').css({
                        'border-color': '#716aca'
                    });
                });
                $('.select2').on('click', function() {
                    $('.c5-select-multiple-container.' + clickOnID + '').css({
                        'display': 'none'
                    });
                    $('.' + clickOnID + ' .c5-select-dropdown-list').css({
                        'border-color': '#eee'
                    });
                });
                $('.' + clickOnID + ' .c5-select-container-close').on('click', function() {
                    $('.c5-select-multiple-container.' + clickOnID + '').toggle();
                    $('.' + clickOnID + ' .c5-select-dropdown-list').css({
                        'border-color': '#716aca'
                    });
                });

            }
        })(jQuery);


        $(document).on("click", function(e) {

            var container = $(".equipment");

            if (!container.is(e.target) && container.has(e.target).length === 0) {
                var a = $('.c5-select-multiple-container.equipment').attr('style');

                if (a == 'display: block;') {
                    $('.c5-select-multiple-container.equipment').toggle();
                    $('.equipment .c5-select-dropdown-list').css({
                        'border-color': '#716aca'
                    });
                }
            }
        });

        function getContainerByGroup(id_group) {
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '/Container/getContainer/',
                data: {
                    'id_group': id_group
                },
                success: function(data) {
                    //console.log(data);
                    var selectValues = $('select#equipment').val();
                    var containerType = '{{ $containerType }}';
                    const defaultValuesController = <?php echo json_encode(
                    $equipment); ?>;                $('.h-hidden').css({
                        'display': 'block'
                    });
                    $('.list-group2 li').remove();
                    //Opciones de equipment list
                    for (const equip in data) {
                        var code = `${data[equip].code}`;
                        var idEquip = `${data[equip].id}`;
                        var list2 = '<li class="c5-case"><label class="c5-label">' + code +
                            '<input type="checkbox"  class="c5-check" title="' + code + '" value="' +
                            idEquip +
                            '"><span class="checkmark"></span></label></li>';
                        $('.equipment .select-list').append('<li title="' + code + '">' + code +
                            ', </li>');
                        $('.list-group2').append(list2);
                    }
                    $('.equipment .select-list').html('');
                    //Cargamos valores de DRY predeterminados final
                    if (id_group == containerType) {
                        for (var i in defaultValuesController) {
                            var ident = defaultValuesController[i];
                            var name = $('.list-group2 .c5-case input[value="' + ident + '"]').attr(
                                'title');
                            $('.equipment .select-list').append('<li title="' + name + '">' + name +
                                ', </li>');
                            $('.list-group2 .c5-case input[value="' + ident + '"]').attr('checked',
                                true);
                            defaultValuesController.push(ident);
                            //console.log(ident);
                        }
                        $('.equipment .select-list li[title="undefined"]').remove();
                        $('#equipment.select-group').val(defaultValuesController);
                        //console.log($('#equipment.select-group').val());
                        //console.log($('#equipment.select-group').val());
                    } else {
                        var valueArray = [];
                        for (const equip in data) {
                            var code = `${data[equip].code}`;
                            var idEquip = `${data[equip].id}`;
                            $('.equipment .select-list').append('<li title="' + code + '">' + code +
                                ', </li>');
                            $('.list-group2 .c5-case input[value="' + idEquip + '"]').attr('checked',
                                true);
                            valueArray.push(idEquip);
                            //console.log(idEquip);
                        }
                        $('#equipment.select-group').val(valueArray);
                        //console.log($('#equipment.select-group').val());
                    }
                    //Cargamos valores al click de equipment list
                    $('.equipment .list-group2 .c5-check').on("click", function() {
                        var valueEquipment = [];
                        var title = $(this).attr('title');
                        $('.equipment .list-group2 .c5-check').each(function() {
                            if (this.checked) {
                                valueEquipment.push($(this).val());
                                //console.log(valueEquipment);
                            }
                        });
                        $('#equipment.select-group').val(valueEquipment);
                        var countEquip = valueEquipment.length;
                        if ($(this).is(':checked')) {
                            var html = '<li title="' + title + '">' + title + ', </li>';
                            $('.equipment .select-list').append(html);
                            $(".select-list .hida").hide();
                        } else {
                            var listLength = $('.equipment .list-group2 .c5-check:checked')
                                .length;
                            $('li[title="' + title + '"]').remove();
                            if (countEquip == 0) {
                                $(".select-list .hida").show();
                            }
                        }
                    });
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                }
            });
        }

        function AbrirModal(action, id, request_id) {
            action = $.trim(action);
            if (action == "DuplicatedContractOtherCompany") {
                var url = '{{ route('contract.duplicated.other.company', [':id', '*id']) }}';
                url = url.replace(':id', id);
                url = url.replace('*id', request_id);
                $('#global-body').load(url, function() {
                    $('#global-modal').modal({
                        show: true
                    });
                });
            } else if (action == "showRequestDp") {
                var url = '{{ route('show.request.dp.cfcl', ':id') }}';
                url = url.replace(':id', id);
                $('#global-body').load(url, function() {
                    $('#global-modal').modal({
                        show: true
                    });
                });
            }
        }

        function editcontract(id) {
            var url = '{{ route('show.contract.edit', ':id') }}';
            url = url.replace(':id', id);
            $('#modal-bodys').load(url, function() {
                $('#contrac').modal();
            });
        }

        function loadContainers() {
            var groupContainers = $("#groupContainers").select2('val');
            var url = '{!! route('request.fcl.get.containers') !!}';
            //        $('.b-select').remove();
            //        $('#containers_div').load(url,{groupContainers:groupContainers},function(){
            //			
            //		});
            $.ajax({
                cache: false,
                type: 'get',
                data: {
                    groupContainers
                },
                url: url,
                success: function(response, textStatus, request) {
                    //console.log(response);
                    if (request.status === 200) {
                        var arr = $('#containerID').val();
                        $('#containerID').multiselect('deselect', arr);
                        $('#containerID').multiselect('select', response.data.values)
                        //$('#containerID').val(response.data.values).trigger('change');
                    }
                },
                error: function(ajaxContext) {
                    toastr.error('Export error: ' + ajaxContext.responseText);
                }
            });
        }

        function message() {

            $('#form').find('input[name="existsFile"]').val('');
            $('#form').find('input[name="document"]').remove();
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-bottom-center",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "0",
                "hideDuration": "0",
                "timeOut": "0",
                "extendedTimeOut": "0",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.error('The file was not uploaded correctly. Please try again');

        }

        $('.m-select2-general').select2({
            placeholder: "Select an option"
        });
        $("#submitRequest").on('click', function(e) {
            var $fileVal = $('#existsFile').val();
            var carrierM = $('#carrierM').val();

            var fail = false;
            var name;
            if (!carrierM.length >= 1) {
                fail = true;
                toastr.error('Carrier is required');
            }
            $('#form').find('select, textarea, input').each(function() {
                if (!$(this).prop('required')) {} else {
                    if (!$(this).val()) {
                        var fail_log = '';
                        fail = true;
                        name = $(this).attr('inpName');
                        fail_log = name + " is required \n";
                        toastr.error(fail_log);
                    }
                }
            });
            if (!fail) {
                if ($fileVal >= 1) {
                    var date = $('#m_daterangepicker_1').val().split(' / ');
                    var date_star = $.trim(date[0]);
                    var date_end = $.trim(date[1]);
                    //e.preventDefault();
                    if (date_star == date_end) {
                        swal(
                            "Error",
                            "Error, Please select the date", "error",
                            true,
                        );
                    } else {
                        $("#modaledit").modal('show');
                        $("#form").submit();
                    }

                } else {
                    toastr.error('Select a file');
                }
            }

        });

        var uploadedDocumentMap = {}

        function existsFiles() {
            var files = null;
            var files = $('#files').val();
            console.log(files);
            if (files != null) {
                $('#submitRequest').removeAttr('disabled');
                $('#submitRequest').removeAttr('hidden');
            } else {
                $('#submitRequest').attr('disabled', 'disabled');
                $('#submitRequest').attr('hidden', 'hidden');
            }
        }

        Dropzone.options.documentDropzone = {
            url: '{{ route('request.fcl.storeMedia') }}',
            maxFilesize: 100, // MB
            maxFiles: 1,
            timeout: 60000,
            acceptedFiles: 'text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,.pdf,.xlsx,.xls,.csv',
            addRemoveLinks: true,
            /*accept: function(file, done) {
                console.log(file);
                var extensions = ['text/csv','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','.pdf','.xlsx','.xls','.csv','.odt'];
                if(!extensions.includes(file.type)) {
                    toastr.error("Error! Files of this type are not accepted");
                }
                else { toastr.info("ok! ");}
            },*/
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(file, response) {
                if (response.error) {
                    message();
                } else {
                    $('#form').append('<input type="hidden" id="files" name="document" value="' + response.name +
                        '">')
                    uploadedDocumentMap[file.name] = response.name;
                    $('#form').find('input[name="existsFile"]').val(1);

                }

            },
            sending: function(file, xhr, formData) {
                //Execute on case of timeout only
                xhr.ontimeout = function(e) {
                    //Output timeout error message here

                    message();


                };
            },

            removedfile: function(file) {
                file.previewElement.remove()
                $('#form').find('input[name="existsFile"]').val('');
                $('#form').find('input[name="document"]').remove();
            },
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    file.previewElement.remove();
                    toastr.error('You can’t upload more than 1 file!');
                });

            },

        }



        $('.dropdown-toggle').dropdown();

    </script>

@stop
