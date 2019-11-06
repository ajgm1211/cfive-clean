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
                    field: "Business Name",
                    title: "Business Name",
                    width: 100,
                    overflow: 'visible',
                },
                {
                    field: "Phone",
                    title: "Phone",
                    width: 80,
                    overflow: 'visible',
                },

                {
                    field: "Email",
                    title: "Email",
                    width: 100,
                    overflow: 'visible',
                },
                {
                    field: "Address",
                    title: "Address",
                    width: 160,
                    overflow: 'visible',
                },
                {
                    field: 'Status',
                    title: 'Status',
                    width: 100,
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
