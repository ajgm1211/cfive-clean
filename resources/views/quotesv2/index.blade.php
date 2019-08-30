
@extends('layouts.app')
@section('title', 'Quotes | List')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/assets/plugins/button-dropdown/css/bootstrap.css">
<script src="/assets/plugins/button-dropdown/js/jquery3.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/assets/plugins/button-dropdown/js/bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
@endsection
@section('content')
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        @if(Session::has('message.nivel'))
        <div class="col-md-12">
            <br>
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
        </div>
        @endif
        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-6 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-4">
                                <div class="m-input-icon m-input-icon--left">
                                    <a href="{{route('quotes-v2.download')}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                        <span>
                                            <span>
                                                Export to excel
                                            </span>
                                            <i class="la la-download"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                        <a href="{{route('quotes-v2.search')}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                            <span>
                                <span>
                                    Search Rates
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                        </a>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    </div>
                </div>
            </div>
            <style>

                button.quote-options {
                    background: #fafafa !important;
                    color: #041A4E !important;
                    padding: 7px 15px;
                    font-size: 12px;
                    box-shadow: none !important;
                    border:1px solid #041A4E !important;
                }

                .quote-options:hover {
                    color: #041A4E;

                }
            </style>

            <table class="table tableData" id="tablequote" width="100%">
                <thead >
                    <tr class="title-quote">
                        <th title="id">
                            Id
                        </th>
                        <th title="Client">
                            Client Company
                        </th>
                        <!--<th title="Contact">
                            Client Contact
                        </th>-->
                        <th title="User">
                            User
                        </th>
                        <th title="Created">
                            Created
                        </th>
                        <th title="Origin">
                            Origin
                        </th>
                        <th title="Destination">
                            Destination
                        </th>
                        <th title="Type">
                            Type
                        </th>
                        <th title="Options">
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="modal fade" id="change_status_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">
                                Change status
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
@include('quotes.partials.quotesModal')
@endsection

@section('js')
@parent

<script type="text/javascript" charset="utf8" src="{{ asset('/assets/datatable/jquery.dataTables.js')}}"></script>
<script src="{{ asset('/assets/demo/default/custom/components/datatables/base/html-table-quotes.js')}}" type="text/javascript"></script>
<script src="{{ asset('/assets/demo/default/custom/components/forms/widgets/select2.js')}}" type="text/javascript"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>

<script>
    function AbrirModal(action,id){
        if(action == "edit"){
            var url = '{{ route("quotes.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#priceModal').modal({show:true});
            });
        }
        if(action == "delete"){
            var url = '{{ route("quotes.destroy", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#deletePriceModal').modal({show:true});
            });
        }
        if(action == "change_status"){
            var url = '{{ route("quotes.change_status", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#change_status_modal').modal({show:true});
            });
        }
    }

    $(document).ready(function() {
        $('#select-origin--2').select2();
        $('#select-destination--2').select2();
    });

    // $('#tablequote').css('height', '180px');

    /*  $(window).resize(function() {
           console.log($(window).height());
           $('.dataTables_scrollBody').css('height', ($(window).height() - 500));
        });*/

    $(function() {
        $('#tablequote').DataTable({
            ordering: true,
            searching: true,
            processing: false,
            serverSide: false,
            order: [[ 0, "desc" ]],
            ajax:  "{{ route('quotes-v2.index.datatable') }}",
            "columnDefs": [
                { "width": "5%", "targets": 0 },
                { "width": "25%", "targets": 1 },
                { "width": "12%", "targets": [2,3] },
                { "width": "15%", "targets": [4,5] },
                { "width": "10%", "targets": 6 },
                { "type": "date", "targets": 2 },
            ],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'client', name: 'client'},
                //{data: 'contact', name: 'contact'},
                {data: 'user', name: 'user'},
                {data: 'created', name: 'created'},
                {data: 'origin', name: 'origin', className: 'details-control'},
                {data: 'destination', name: 'destination'},
                {data: 'type', name: 'type'},
                {data: 'action', name: 'action', orderable: false, searchable: false },
            ] ,
            "autoWidth": true,
            'overflow':false,
            "paging":true,
            "sScrollY": "490px",
            "bPaginate": false,
            "bJQueryUI": true,
            buttons: [
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                }
            ]


        });

        // Add event listener for opening and closing details
        $('#tablequote tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        } );
    });


    /* Formatting function for row details - modify as you need */
    function format ( d ) {
        // `d` is the original data object for the row
        return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
            '<tr>'+
            '<td>Full name:</td>'+
            '<td>'+d.name+'</td>'+
            '</tr>'+
            '<tr>'+
            '<td>Extension number:</td>'+
            '<td>'+d.extn+'</td>'+
            '</tr>'+
            '<tr>'+
            '<td>Extra info:</td>'+
            '<td>And any further details here (images etc)...</td>'+
            '</tr>'+
            '</table>';
    }

</script>
@stop
