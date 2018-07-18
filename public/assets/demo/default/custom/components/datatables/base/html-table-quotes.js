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
        field: "Status",
        title: "Status",
        width: 80,        
        overflow: 'visible',
      },


      {
        field: "Created",
        title: "Created",
        width: 60,
        overflow: 'visible',
      },
      {
        field: "Origin",
        title: "Origin",
        width: 60,
        overflow: 'visible',
      },
      {
        field: "Destination",
        title: "Destination",
        width: 60,
        overflow: 'visible',
      },
      {
        field: "Ammount",
        title: "Ammount",
        width: 60,
        overflow: 'visible',
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
