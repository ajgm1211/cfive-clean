@extends('layouts.app')
@section('title', 'Usuarios')
@section('content')
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Lista de Usuarios 
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-8 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-4">
                                <div class="m-form__group m-form__group--inline">
                                    <div class="m-form__label">
                                        <label class="m-label m-label--single">
                                            Rol:
                                        </label>
                                    </div>
                                    <div class="m-form__control">
                                        <select class="form-control m-bootstrap-select" id="m_form_type">
                                            <option value="">
                                                All
                                            </option>
                                            <option value="1">
                                                Company
                                            </option>
                                            <option value="2">
                                                Admin
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="m-input-icon m-input-icon--left">
                                    <input type="text" class="form-control m-input" placeholder="Search..." id="generalSearch">
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
                        
                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" data-toggle="modal" data-target="#m_modal_6">
												<span>
                                <i class="la la-user"></i>
                                <span>
                                    Add User
                                </span>
                            </span>
											</button>
                        
              
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    </div>
                </div>
            </div>
            <div class="m_datatable" id="local_data"></div>
            
             @include('users.add')
					
        </div>
    </div>
</div>
</div>
</div>
@endsection

@section('js')
@parent
<script>
    //== Class definition

    var DatatableDataLocalDemo = function () {
        var demo = function () {
            var dataJSONArray = JSON.parse('{!! $url !!}');
            var datatable = $('.m_datatable').mDatatable({
                // datasource definition
                data: {
                    type: 'local',
                    source:dataJSONArray,
                    pageSize: 10
                },

                // layout definition
                layout: {
                    theme: 'default', // datatable theme
                    class: '', // custom wrapper class
                    scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                    // height: 450, // datatable's body's fixed height
                    footer: false // display/hide footer
                },

                // column sorting
                sortable: true,

                pagination: true,

                search: {
                    input: $('#generalSearch')
                },

                // inline and bactch editing(cooming soon)
                // editable: false,

                // columns definition
                columns: [{
                    field: "name",
                    title: "Name"
                }, {
                    field: "lastname",
                    title: "Last Name",
                    responsive: {visible: 'lg'}
                }, {
                    field: "email",
                    title: "Email",
                    width: 100
                }, {
                    field: 'rol',
                    title: 'rol',
                    // callback function support for column rendering

                }, {
                    field: "options",
                    width: 110,
                    title: "Options",
                    sortable: false,
                    overflow: 'visible',
                    template: function (row, index, datatable) {
                        var dropup = (datatable.getPageSize() - index) <= 4 ? 'dropup' : '';

                        return '\
<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit ">\
<i class="la la-edit"></i>\
    </a>\
<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete ">\
<i class="la la-eraser"></i>\
    </a>\
';
                    }
                }]
            });

            var query = datatable.getDataSourceQuery();


            $('#m_form_type').on('change', function () {
                datatable.search($(this).val(), 'rol');
            }).val(typeof query.rol !== 'undefined' ? query.rol : '');

            $('#m_form_status, #m_form_type').selectpicker();

        };

        return {
            //== Public functions
            init: function () {
                // init dmeo
                demo();
            }
        };
    }()

    jQuery(document).ready(function () {
        DatatableDataLocalDemo.init();
    });

</script>
@stop


