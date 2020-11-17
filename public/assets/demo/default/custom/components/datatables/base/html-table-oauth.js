//== Class definition

var DatatableHtmlTableDemo = function() {
    //== Private functions

    // demo initializer
    var demo = function() {


        var datatable = $('.m-datatable').mDatatable({
            data: {
                saveState: { cookie: false },
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
                        pageSizeSelect: [5, 10, 20, 30, 50, 100 /*, -1*/ ] // display dropdown to select pagination size. -1 is used for "ALl" option
                    }
                }
            },
            columns: [{
                    field: "Name",
                    title: "Name",
                    width: 150,
                    overflow: 'visible',
                },
                {
                    field: "Company user",
                    title: "Company user",
                    width: 300,
                    overflow: 'visible',
                },
                {
                    field: "Secret",
                    title: "Secret",
                    width: 300,
                    overflow: 'visible',
                },
                {
                    field: "Created at",
                    title: "Created at",
                    width: 300,
                    overflow: 'visible',
                },
                {
                    field: "Options",
                    title: "Options",
                    width: 60,
                    overflow: 'visible',
                },
            ],
        });


        var datatable2 = $('.m-datatable2').mDatatable({
            data: {
                saveState: { cookie: false },
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
                        pageSizeSelect: [5, 10, 20, 30, 50, 100 /*, -1*/ ] // display dropdown to select pagination size. -1 is used for "ALl" option
                    }
                }
            },

            columns: [],
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