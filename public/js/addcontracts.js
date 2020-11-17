$("#new").on("click", function() {



  $ids = $( ".rateOrig" ).length;
  $ids = $ids + 1;

  var $template = $('#tclone');


  $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
  $myClone.find(".rateOrig").attr('name', 'origin_id'+$ids+'[]').attr('required',true);
  $myClone.find(".rateDest").attr('name', 'destiny_id'+$ids+'[]').attr('required',true);
  //$myClone.find(".rateScheduleT").attr('name', 'scheduleT_id'+$ids+'[]');
  $myClone.find("select").select2();
  $("#sample_editable_1").append($myClone);
  // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
  // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});
function activarCountry(act,id){
  var divCountry = $( ".divcountry"+id);
  var divport = $( ".divport"+id);
  if(act == 'divcountry'){
    divport.attr('hidden','true');
    divCountry.removeAttr('hidden');
  }else if(act == 'divport'){
    divCountry.attr('hidden','true');
    divport.removeAttr('hidden');
  }
}
$("#new2").on("click", function() {


  var $template = $('#tclone2');
  $myClone = $template.clone().removeAttr('hidden').removeAttr('id').addClass('trclone2');
  $myClone.find("select").select2();
  $ids = $( ".portOrig" ).length;
  $ids = $ids + 1;

  $id_carrier = $( ".carrier" ).length;
  $id_carrier = $id_carrier + 1;


  $myClone.find(".portOrig").attr('name', 'port_origlocal'+$ids+'[]');
  $myClone.find(".portDest").attr('name', 'port_destlocal'+$ids+'[]');
  $myClone.find(".carrier").attr('name', 'localcarrier_id'+$id_carrier+'[]');
  // se agrega el nombre a los nuevos combos pais 
  $myClone.find(".countryOrig").attr('name', 'country_orig'+$ids+'[]');
  $myClone.find(".countryDest").attr('name', 'country_dest'+$ids+'[]');
  // se agrega el nombre al calculation type
  $myClone.find(".calculationT").attr('name', 'calculationtype'+$ids+'[]');

  $id_radio = $( ".rdrouteP" ).length;
  $id_radio = $id_radio + 1;

  // botones del radio se le agrega el nombre y la funcion que habilita o no los paises 
  $myClone.find(".rdrouteP").attr('name', 'typeroute'+$id_radio).attr('onClick', 'activarCountry(\'divport\','+$id_radio+')');
  $myClone.find(".rdrouteC").attr('name', 'typeroute'+$id_radio).attr('onClick', 'activarCountry(\'divcountry\',\''+$id_radio+'\')');

  // se agrega la clase con los numeros para identificar a cada linea de la tabla 
  $myClone.find(".divport").addClass('divport'+$id_radio);
  $myClone.find(".divcountry").addClass('divcountry'+$id_radio);

  $("#sample_editable_2").append($myClone);
  // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
  // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$('#m-select2-company').select2({
  placeholder: "Select an option"
});
$('#m-select2-client').select2({
  placeholder: "Select an option"
});

$(document).on('click', '.remove', function () {
  $(this).closest('tr').remove();
  $i = 2;
  $('.trRate').each(function () {
    var res = $(".rateOrig",this).removeAttr('name').attr('name', 'origin_id'+$i+'[]');
    var res = $(".rateDest",this).removeAttr('name').attr('name', 'destiny_id'+$i+'[]');


    $i++;
  });
});

$(document).on('click', '.removeL', function () {
  $(this).closest('tr').remove();
  $i = 2;
  $('.trclone2').each(function () {
    var res = $(".portOrig",this).removeAttr('name').attr('name', 'port_origlocal'+$i+'[]');
    var res = $(".portDest",this).removeAttr('name').attr('name', 'port_destlocal'+$i+'[]');
    var car = $(".carrier",this).removeAttr('name').attr('name', 'localcarrier_id'+$i+'[]');
    var countryO = $(".countryOrig").removeAttr('name').attr('name', 'country_orig'+$i+'[]');
    var countryD = $(".countryDest").removeAttr('name').attr('name', 'country_dest'+$i+'[]');
    var ctype = $(".calculationT").removeAttr('name').attr('name','calculationtype'+$i+'[]');

    $i++;
  });
});


$('.m-select2-general').select2({
  placeholder: "Select an option"
});
$(document).on('click', '.addCT', function () {

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
    toastr.error(' You have to first add surcharges terms in order to add surcharges to this contract. <a href="surcharges" > <b> Add Surcharge</b> </a>!','IMPORTANT MESSAGE!');
  }
} );


