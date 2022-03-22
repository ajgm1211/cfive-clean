@extends('layouts.app')
@section('title', 'Surcharges')
@section('content')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
<div class="m-content">
    <div class="dropdown show" align="right" style="margin:20px;">
        <a class="dropdown-toggle" style="font-size:16px" href="#" role="button" id="helpOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            See how it works
        </a>

        <div 
            class="dropdown-menu" 
            aria-labelledby="helpOptions"
        >
            <a 
                class="dropdown-item" 
                target="_blank" 
                href="https://support.cargofive.com/how-to-manage-surcharges-on-cargofive/"
                style="overflow-x:hidden;"
            > How to manage surcharges 
            </a>
        </div>
    </div>
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Surcharges list
                    </h3>
                </div>
            </div>
        </div>
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
        <div class="m-portlet__body">
            <div class="m-portlet__head-tools">
                <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                            <i class="la la-cog"></i>
                            Surcharges list
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item">
                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_tabs_6_2" role="tab">
                            <i class="la la-briefcase"></i>
                            List Sale Terms
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">
                    <!--begin: Search Form -->
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <div class="m-input-icon m-input-icon--left">
                                            <div class="m-checkbox-list">
                                                <label class="m-checkbox" for="company_user">
                                                    <input type="checkbox" id="company_user" value="General">
                                                    <strong>Company User: </strong> General
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>
                                            Add Surcharge
                                        </span>
                                    </span>
                                </button>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                    <table class="table tableData " style="width:100%" id="surchargestable" width="100%">
                        <thead>
                            <tr>
                                <th title="">ID </th>
                                <th title="">Name </th>
                                <th title="">Description </th>
                                <th title="">Company User</th>
                                <th title="">Sale term </th>
                                <th title="">Variations </th>
                                <th title="">Options </th>
                            </tr>
                        </thead>
                    </table>
                    <div class="modal fade" id="m_modal_6" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                        Surcharges
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">
                                            &times;
                                        </span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                        Close
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="m_tabs_6_2" role="tabpanel">
                    <!--begin: Search Form -->
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <div class="m-input-icon m-input-icon--left">
                                            <input type="text" class="form-control m-input" placeholder="Search..." id="generalSearch2">
                                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                                <span>
                                                    <i class="la la-search"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModalSaleTerm('add',0)">
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>
                                            Add Sale Term
                                        </span>
                                    </span>
                                </button>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                    <table class="table tableData " style="width:100%" id="salesTermTable" width="100%">
                        <thead>
                            <tr>
                                <th title="ID">ID</th>
                                <th title="name">Name</th>
                                <th title="description">Description</th>
                                <th title="options">Options</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="modal fade" id="m_modal_6" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                        Sale terms
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">
                                            &times;
                                        </span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="m_modal_sale_terms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">
                                        Sale terms
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">
                                            &times;
                                        </span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                        Close
                                    </button>
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
<script src="/assets/demo/default/custom/components/datatables/base/html-table-surcharge.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-saleterms.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="application/x-javascript" src="/js/toarts-config.js"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script>

    function agregarcampo(){
        var newtr = '<div class="col-lg-4 ">';
        newtr = newtr + '<label class="form-control-label">Variation:</label>';
        newtr = newtr + '<input type="text" name="variation[]" class="form-control" required="required">';
        newtr = newtr + '<a href="#" class="borrado"><span class="la la-remove"></span></a>';
        newtr = newtr + '</div>';
        $('#variatiogroup').append(newtr);
    }

    $(document).on('click','.borrado', function(e){
        var elemento = $(this);
        $(elemento).closest('div').remove();
    });

    function AbrirModal(action,id){

        if(action == "edit"){
            var url = '{{ route("surcharges.edit", ":id") }}';
            url = url.replace(':id', id);


            $('.modal-body').load(url,function(){
                $('#m_modal_6').modal({show:true});
            });
        }if(action == "add"){
            var url = 'surcharges/add';


            $('.modal-body').load(url,function(){
                $('#m_modal_6').modal({show:true});
            });

        }
        if(action == "delete"){
            var url = '{{ route("surcharges.msg", ":id") }}';
            url = url.replace(':id', id);

            $('.modal-body').load(url,function(){
                $('#m_modal_6').modal({show:true});
            });

        }
    }

    function AbrirModalSaleTerm(action,id){
        if(action == "edit"){
            var url = '{{ route("saleterms.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#m_modal_sale_terms').modal({show:true});
            });
        }if(action == "add"){
            var url = '{{ route("saleterms.create") }}';
            $('.modal-body').load(url,function(){
                $('#m_modal_sale_terms').modal({show:true});
            });

        }
        if(action == "delete"){
            var url = '{{ route("saleterms.msg", ":id") }}';
            url = url.replace(':id', id);

            $('.modal-body').load(url,function(){
                $('#m_modal_sale_terms').modal({show:true});
            });
        }
    }

    $('#company_user' ).on( 'change', function () {
        if($('#company_user').prop('checked')){
            company_user = $(this).val();
        } else {
            company_user = '';                           
        }
        i = 3;
        if ( surchargestableV.column(i).search() !== company_user ) {
            surchargestableV
                .column(i)
                .search( company_user )
                .draw();
        }
    } );
    function activeCheck(){
        $("#company_user").prop("checked",true);

        company_user = $("#company_user").val();

        i = 3;
        if ( surchargestableV.column(i).search() !== company_user ) {
            surchargestableV
                .column(i)
                .search( company_user )
                .draw();
        }
    }
    var surchargestableV = '';
    $(function() {    
        surchargestableV = $('#surchargestable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route("surcharges.load.datatables","1") !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'company_user', name: 'company_user' },
                { data: 'sale_term', name: 'sale_term' },
                { data: 'variations', name: 'variations' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            orderCellsTop: true,
            fixedHeader: true,
            "order": [[0, 'des']],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "width": true,
            "autoWidth": false,
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "paging": true
        });

        var state = surchargestableV.state.loaded();
        if(state) {
            var colSearch = state.columns[3].search;
            if (colSearch.search == 'General') {
                $("#company_user").prop("checked",true);
            }
            surchargestableV.draw();
        }
        activeCheck();
        $('#salesTermTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route("surcharges.load.datatables","2") !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            orderCellsTop: true,
            fixedHeader: true,
            "order": [[0, 'des']],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "width": true,
            "autoWidth": false,
            "stateSave": true,
            "processing": true,
            "serverSide": true,
            "paging": true
        });
    });

    function addExtraField() {
        var $template = $('#hide_extra_field'),
            $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id')
        .addClass('clone')
        .insertAfter($template);
    }

    $(document).on('click', '#add_extra_field', function(e) {
        e.preventDefault();
        $("#hide_extra_field").append('a');

    });
    $(document).on('click', '.deleter', function() {
        $(this).closest('div.clone').find('.row').remove();
    });
</script>

@stop
