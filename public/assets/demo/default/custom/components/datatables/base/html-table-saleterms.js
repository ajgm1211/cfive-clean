//== Class definition

var DatatableHtmlTableSaleTerms = function() {
    //== Private functions

    // demo initializer
    var table = function() {

        var datatable = $('.m-datatable-2').mDatatable({
            data: {
                saveState: {cookie: false},
            },
            search: {
                input: $('#generalSearch2'),
            },
            columns: [
             
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
            table();
        },
    };
}();

jQuery(document).ready(function() {
    DatatableHtmlTableSaleTerms.init();
});
