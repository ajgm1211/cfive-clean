//== Class definition

var DatatableHtmlTableDemo = function() {
    //== Private functions

    // demo initializer
    var demo = function() {


        var datatable = $('.m-datatable').mDatatable({
            data: {
                saveState: {cookie: false},
                pageSize: 5,

            },

            layout: {
                theme: 'default',
                class: '',
                scroll: true,
                footer: false
            },

            sortable: true,

            filterable: true,

            pagination: true,

            search: {
                input: $('#generalSearch'),
            },
            toolbar: {
                // toolbar items
                items: {
                    // pagination
                    pagination: {
                        pageSizeSelect: [5, 10, 20, 30, 50, 100/*, -1*/] // display dropdown to select pagination size. -1 is used for "ALl" option
                    }
                }
            },
            columns: [
                {
                    field: "Name",
                    title: "Name",
                    width: 60,
                    overflow: 'visible',
                },
                {
                    field: "Number",
                    title: "Number",
                    width: 60,
                    overflow: 'visible',
                },


                {
                    field: "Currency",
                    title: "Currency",
                    width: 60,
                    overflow: 'visible',
                },
                {
                    field: "Carrier",
                    title: "Carrier",
                    width: 60,
                    overflow: 'visible',
                },
                {
                    field: "20'",
                    title: "20'",
                    width: 35,
                    overflow: 'visible',
                },
                {
                    field: "40'",
                    title: "40'",
                    width: 35,
                    overflow: 'visible',
                },
                {
                    field: "40'HC",
                    title: "40'HC",
                    width: 35,
                    overflow: 'visible',
                },


                {
                    field: 'Status',
                    title: 'Status',
                    width: 40,
                    // callback function support for column rendering
                    template: function(row) {
                        var status = {
                            1: {'title': 'Draft', 'state': 'warning'},
                            2: {'title': 'Sent', 'state': 'info'},
                            3: {'title': 'Negociated', 'state': 'info'},
                            4: {'title': 'Lost', 'state': 'danger'},
                            5: {'title': 'Win', 'state': 'success'},
                        };
                        return '<span class="m-badge m-badge--' + status[row.Status].state + ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' +
                            status[row.Status].state + '">' +
                            status[row.Status].title + '</span>';
                    },
                },
                {
                    field: 'status',
                    title: 'status',
                    width: 60,
                    // callback function support for column rendering
                    template: function(row) {
                        var status = {
                            1: {'title': 'publish', 'state': 'success'},
                            2: {'title': 'draft', 'state': 'warning'},
                            3: {'title': 'expired', 'state': 'danger'},

                        };
                        return '<span class="m-badge m-badge--' + status[row.status].state + ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' +
                            status[row.status].state + '">' +
                            status[row.status].title + '</span>';
                    },
                    template: function (row, index, datatable) {
                        var dropup = (datatable.getPageSize() - index) <= 4 ? 'dropup' : '';
                        return '\
<div class="dropdown ' + dropup + '">\
<a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">\
<i class="la la-ellipsis-h"></i>\
</a>\
<div class="dropdown-menu dropdown-menu-right">\
<a class="dropdown-item" href="#"><i class="la la-edit"></i> Edit Details</a>\
<a class="dropdown-item" href="#"><i class="la la-leaf"></i> Update Status</a>\
<a class="dropdown-item" href="#"><i class="la la-print"></i> Generate Report</a>\
</div>\
</div>\
<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
<i class="la la-edit"></i>\
</a>\
<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete">\
<i class="la la-trash"></i>\
</a>\
';
                    },
                },
            ],
        });


        var datatable2 =  $('.m-datatable2').mDatatable({
            data: {
                saveState: {cookie: false},
                pageSize: 5,

            },
            layout: {
                theme: 'default',
                class: '',
                scroll: true,
                footer: false
            },

            sortable: true,

            filterable: true,

            pagination: true,

            search: {
                input: $('#generalSearch2'),
            },
            toolbar: {
                // toolbar items
                items: {
                    // pagination
                    pagination: {
                        pageSizeSelect: [5, 10, 20, 30, 50, 100/*, -1*/] // display dropdown to select pagination size. -1 is used for "ALl" option
                    }
                }
            },

            columns: [
                {
                    field: 'status',
                    title: 'status',
                    width: 60,
                    // callback function support for column rendering
                    template: function(row) {
                        var status = {
                            1: {'title': 'publish', 'state': 'success'},
                            2: {'title': 'draft', 'state': 'warning'},

                        };
                        return '<span class="m-badge m-badge--' + status[row.status].state + ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' +
                            status[row.status].state + '">' +
                            status[row.status].title + '</span>';
                    },
                },
            ],


        });

        $('#m_form_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('.m_form_type2').on('change', function() {
            datatable2.search($(this).val().toLowerCase(), 'status');
        });
        $('#m_form_status, #m_form_type').selectpicker();

    };

    return {
        //== Public functions
        init: function() {
            // init dmeo
            demo();
        },
    };
}();

jQuery(document).ready(function() {
    DatatableHtmlTableDemo.init();
});
