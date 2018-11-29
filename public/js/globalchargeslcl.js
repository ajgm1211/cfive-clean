$(document).on('click', '.m_sweetalert_demo_8', function (e) {
  var res = $("i",this).attr('id'); 

  var theElement = $(this);
  var idval = res.substr(4);

  swal({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!'
  }).then(function(result) {
    if (result.value) {

      $.ajax({
        type: 'get',
        url: 'globalchargeslcl/deleteGlobalChargeLcl/' + idval,
        success: function(data) {
          swal(
            'Deleted!',
            'Your file has been deleted.',
            'success'
          )
          $(theElement).closest('tr').remove();

        },
        
        error: function (request, status, error) {
          alert(request.responseText);
        }
      });

    }

  });

});
function activarCountry(act){
  var divCountry = $( ".divcountry");
  var divport = $( ".divport");
  if(act == 'divcountry'){
    divport.attr('hidden','true');
    divCountry.removeAttr('hidden');
  }else if(act == 'divport'){
    divCountry.attr('hidden','true');
    divport.removeAttr('hidden');
  }
}


$('.m-select2-general').select2({
  placeholder: "Select an option"
});

$(document).on('click', '.addS', function () {

  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "0",
    "hideDuration": "0",
    "timeOut": "0",
    "extendedTimeOut": "0",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };
  var surcharge = $('.type');
  if(surcharge.val() == null){
    toastr.error('You have to first add surcharges terms in order to add surcharges to this global. <a href="surcharges" > <b> Add Surcharge</b> </a>!','IMPORTANT MESSAGE!');
  }
} );

