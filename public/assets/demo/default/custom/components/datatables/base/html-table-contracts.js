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
          width: 60,
          overflow: 'visible',
        },
        {
          field: "40'",
          title: "40'",
          width: 60,
          overflow: 'visible',
        },
        {
          field: "40'HC",
          title: "40'HC",
          width: 60,
          overflow: 'visible',
        },

        {
          field: 'Status',
          title: 'Status',
          width: 60,
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
