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
                    field: 'Status',
                    title: 'Status',
                    // callback function support for column rendering
                    template: function(row) {
                        var status = {
                            1: {'title': 'publish', 'state': 'success'},
                            2: {'title': 'draft', 'state': 'warning'},
                        };
                        return '<span class="m-badge m-badge--' + status[row.Status].state + ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' +
                            status[row.Status].state + '">' +
                            status[row.Status].title + '</span>';
                    },
                    width: 50,
                },{
                    field: "20'",
                    title: "Field #6",
                    sortable: false,
                    width: 30,
                },{
                    field: "40'",
                    title: "Field #7",
                    sortable: false,
                    width: 30,
                },{
                    field: "40'HC",
                    title: "Field #8",
                    sortable: false,
                    width: 45,
                },{
                    field: "Currency",
                    title: "Field #10",
                    sortable: false,
                    width: 70,
                },{
                    field: "Number",
                    title: "Field #2",
                    sortable: false,
                    width: 55,
                },{
                    field: "Name",
                    title: "Field #1",
                    sortable: false,
                    width: 80,
                },
            ],
        });


        $('#m_form_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
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
