
function display(id){

  $("#tr"+id+" .val").attr('hidden','true');
  $("#tr"+id+" .in").removeAttr('hidden');
  $("#tr"+id+" .in input , #tr"+id+" .in select ").prop('disabled', false);

  $("#save"+id).removeAttr('hidden');
  $("#cancel"+id).removeAttr('hidden');
  $("#edit"+id).attr('hidden','true');
}

function cancel(id){

  $("#tr"+id+" .val").removeAttr('hidden');
  $("#tr"+id+" .in").attr('hidden','true');
  $("#tr"+id+" .in input , #tr"+id+" .in select ").prop('disabled', true);

  $("#save"+id).attr('hidden','true');
  $("#cancel"+id).attr('hidden','true');
  $("#edit"+id).removeAttr('hidden');

}

function save(id,idval){


  $.ajax({
    type: 'GET',
    url: '../updateRate/' + idval,
    data: {
      'origin_port': $("#origin"+id).val(),
      'destiny_port': $("#destiny"+id).val(),
      'carrier_id': $("#carrier"+id).val(),
      'twuenty': $("#twuenty"+id).val(),
      'forty': $("#forty"+id).val(),
      'fortyhc': $("#fortyhc"+id).val(),
      'currency_id': $("#currency"+id).val(),
    },
    success: function(data) {
      swal(
        'Updated!',
        'Your rate has been updated.',
        'success'
      )
      $("#save"+id).attr('hidden','true');
      $("#cancel"+id).attr('hidden','true');
      $("#edit"+id).removeAttr('hidden');

      $("#tr"+id+" .val").removeAttr('hidden');
      $("#tr"+id+" .in").attr('hidden','true');
      $("#tr"+id+" .in input , #tr"+id+" .in select ").prop('disabled', true);

      $("#divoriginport"+id).html($("#origin"+id+" option:selected").text());
      $("#divdestinyport"+id).html($("#destiny"+id+" option:selected").text());
      $("#divcarrier"+id).html($("#carrier"+id+" option:selected").text());
      $("#divtwuenty"+id).html($("#twuenty"+id).val());
      $("#divforty"+id).html($("#forty"+id).val());
      $("#divfortyhc"+id).html($("#fortyhc"+id).val());
      $("#divalphacode"+id).html($("#currency"+id+" option:selected").text());

    }
  });

}

function display_l(id){

  $("#tr_l"+id+" .val").attr('hidden','true');
  $("#tr_l"+id+" .in").removeAttr('hidden');
  $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', false);

  $("#save_l"+id).removeAttr('hidden');
  $("#cancel_l"+id).removeAttr('hidden');
  $("#remove_l"+id).removeAttr('hidden');
  $("#edit_l"+id).attr('hidden','true');

}

function cancel_l(id){

  $("#tr_l"+id+" .val").removeAttr('hidden');
  $("#tr_l"+id+" .in").attr('hidden','true');
  $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', true);

  $("#save_l"+id).attr('hidden','true');
  $("#cancel_l"+id).attr('hidden','true');
  $("#remove_l"+id).attr('hidden','true');
  $("#edit_l"+id).removeAttr('hidden');

}

function save_l(id,idval){

  $.ajax({


    type: 'GET',
    url: '../updateLocalCharge/' + idval,
    data: {
      'surcharge_id' : $("#type"+id).val(),
      'port_origlocal' : $("#portOrig"+id).val(),
      'port_destlocal' : $("#portDest"+id).val(),
      'changetype' : $("#changetype"+id).val(),
      'carrier_id' : $("#localcarrier"+id).val(),
      'calculationtype_id' : $("#calculationtype"+id).val(),
      'ammount' : $("#ammount"+id).val(),
      'currency_id' : $("#localcurrency"+id).val()

    },

    success: function(data) {

      swal(
        'Updated!',
        'Your local charge has been updated.',
        'success'
      )
      $("#save_l"+id).attr('hidden','true');
      $("#cancel_l"+id).attr('hidden','true');
      $("#remove_l"+id).attr('hidden','true');
      $("#edit_l"+id).removeAttr('hidden');

      $("#tr_l"+id+" .val").removeAttr('hidden');
      $("#tr_l"+id+" .in").attr('hidden','true');
      $("#tr_l"+id+" .in input , #tr_l"+id+" .in select ").prop('disabled', true);
      var selText ="";
      var porText = "";
      var porTextDest = "";
      $("#localcarrier"+id+" option:selected").each(function () {
        var $this = $(this);
        if ($this.length) {
          selText += $this.text()+ ", ";

        }
      });

      $("#portOrig"+id+" option:selected").each(function () {
        var $this = $(this);
        if ($this.length) {
          porText += $this.text()+ ", ";

        }
      });

      $("#portDest"+id+" option:selected").each(function () {
        var $this = $(this);
        if ($this.length) {
          porTextDest += $this.text()+ ", ";

        }
      });
      $("#divtype"+id).html($("#type"+id+" option:selected").text());
      $("#divport"+id).html(porText);
      $("#divportDest"+id).html(porTextDest);
      $("#divcarr"+id).html(selText);
      $("#divchangetype"+id).html($("#changetype"+id+" option:selected").text());
      $("#divcalculation"+id).html($("#calculationtype"+id+" option:selected").text());
      $("#divammount"+id).html($("#ammount"+id).val());
      $("#divcurrency"+id).html($("#localcurrency"+id+" option:selected").text());

    },
    error: function (request, status, error) {
      alert(request.responseText);
    }

  });

}

$("#new").on("click", function() {

  var $template = $('#tclone');
  $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
  $myClone.find("select").select2();
  $("#rateTable").prepend($myClone);
  // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
  // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$("#newL").on("click", function() {

  var $template = $('#tclone2');

  $myClone = $template.clone().removeAttr('hidden').removeAttr('id').addClass('tr_edit');;
  $myClone.find("select").select2();
  $ids = $( ".portOrig" ).length;
  $myClone.find(".portOrig").attr('name', 'port_origlocal'+$ids+'[]');
  $myClone.find(".portDest").attr('name', 'port_destlocal'+$ids+'[]');
  $myClone.find(".carrier").attr('name', 'localcarrier_id'+$ids+'[]');
  $("#users-table").prepend($myClone);


});

$(document).on('click', '.remove', function () {
  $(this).closest('tr').remove();
  $i = 1;
  $('.tr_edit').each(function () {

    var res = $(".portOrig",this).removeAttr('name').attr('name', 'port_origlocal'+$i+'[]');
    var resDest = $(".portDest",this).removeAttr('name').attr('name', 'port_destlocal'+$i+'[]');
    var car = $(".carrier",this).removeAttr('name').attr('name', 'localcarrier_id'+$i+'[]');
    $i++;
  });
});

$(document).on('click', '.m_sweetalert_demo_8', function (e) {

  var theElement = $(this);
  var idval = $(this).attr('data-local-id');

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
        url: '../deleteLocalCharge/' + idval,
        success: function(data) {
          swal(
            'Deleted!',
            'Your file has been deleted.',
            'success'
          )
          $(theElement).closest('tr').remove();

        }
      });

    }

  });

});

$('.m-select2-general').select2({
  placeholder: "Select an option"
});


$(document).on('click', '#delete-rate', function () {
  var id = $(this).attr('data-rate-id');


  var theElement = $(this);
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
        url: '/contracts/delete-rates/' + id,
        success: function(data) {
          swal(
            'Deleted!',
            'Your rate has been deleted.',
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


$(document).on('click', '.file-contract', function () {
  var id = $(this).attr('media-id');
  var id_contract = $(this).attr('contract-id');

  var theElement = $(this);
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
        url: '/contracts/excel-delete/' + id+'/'+id_contract,
        success: function(data) {
          swal(
            'Deleted!',
            'Your rate has been deleted.',
            'success'
          )

          $('.col-dad'+id).remove();
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

  var idPortOrig = $( "#port_orig"); 
  var idCountryOrig = $( "#country_orig");  


  var idPortDest = $( "#port_dest"); 
  var idCountryDest = $( "#country_dest"); 
  if(act == 'divcountry'){
    divport.attr('hidden','true');
    divCountry.removeAttr('hidden');

    idCountryOrig.attr('required','true');
    idCountryDest.attr('required','true');

    idPortOrig.removeAttr('required');
    idPortDest.removeAttr('required');

  }else if(act == 'divport'){
    divCountry.attr('hidden','true');
    divport.removeAttr('hidden');


    idPortOrig.attr('required','true');
    idPortDest.attr('required','true');

    idCountryOrig.removeAttr('required');
    idCountryDest.removeAttr('required');
  }
}


