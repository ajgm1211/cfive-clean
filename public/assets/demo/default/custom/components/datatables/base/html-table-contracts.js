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
