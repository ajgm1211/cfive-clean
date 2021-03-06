//== Class definition

var DatatableHtmlTableDemo = function() {
    //== Private functions

    // demo initializer
    var demo = function() {

        var datatable = $('.m-datatable').mDatatable({
            data: {
                saveState: {cookie: false},
            },
            search: {
                input: $('#generalSearch'),
            },
            columns: [
                {
                    field: 'Type',
                    title: 'Type',
                    // callback function support for column rendering
                    template: function(row) {
                        var status = {
                            1: {'title': 'Admin', 'state': 'primary'},
                            2: {'title': 'Company', 'state': 'accent'},
                            3: {'title': 'Subuser', 'state': 'warning'},
                            4: {'title': 'Data entry', 'state': 'success'},
                        };
                        return '<span class="m-badge m-badge--' + status[row.Type].state + ' m-badge--dot"></span>&nbsp;<span class="m--font-bold m--font-' +
                            status[row.Type].state + '">' +
                            status[row.Type].title + '</span>';
                    },
                },
            ],
        });

        $('#m_form_status').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });

        $('#m_form_type').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'Type');
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
