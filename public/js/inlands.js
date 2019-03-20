function display_twuenty(id){

  $("#tr_twuenty"+id+" .val").attr('hidden','true');
  $("#tr_twuenty"+id+" .in").removeAttr('hidden');
  $("#tr_twuenty"+id+" .in input , #tr_twuenty"+id+" .in select ").prop('disabled', false);


  $("#save_twuenty"+id).removeAttr('hidden');
  $("#cancel_twuenty"+id).removeAttr('hidden');
  $("#remove_twuenty"+id).removeAttr('hidden');
  $("#edit_twuenty"+id).attr('hidden','true');

}

function cancel_twuenty(id){

  $("#tr_twuenty"+id+" .val").removeAttr('hidden');
  $("#tr_twuenty"+id+" .in").attr('hidden','true');
  $("#tr_twuenty"+id+" .in input , #tr_twuenty"+id+" .in select ").prop('disabled', true);

  $("#save_twuenty"+id).attr('hidden','true');
  $("#cancel_twuenty"+id).attr('hidden','true');
  $("#remove_twuenty"+id).attr('hidden','true');
  $("#edit_twuenty"+id).removeAttr('hidden');

}

function save_twuenty(id,idval){

  $.ajax({
    type: 'GET',
    url: '../updateDetails/' + idval,
    data: {
      'lower' : $("#lo20"+id).val(),
      'upper' : $("#up20"+id).val(),
      'ammount' : $("#ammounttwuenty"+id).val(),
      'currency_id' : $("#currencytwuenty"+id).val()
    },
    success: function(data) {
      swal(
        'Updated!',
        'Your Inland has been updated.',
        'success'
      )
      $("#save_twuenty"+id).attr('hidden','true');
      $("#cancel_twuenty"+id).attr('hidden','true');
      $("#remove_twuenty"+id).attr('hidden','true');
      $("#edit_twuenty"+id).removeAttr('hidden');

      $("#tr_twuenty"+id+" .val").removeAttr('hidden');
      $("#tr_twuenty"+id+" .in").attr('hidden','true');
      $("#tr_twuenty"+id+" .in input , #tr_twuenty"+id+" .in select ").prop('disabled', true);

      $("#divlowertwuenty"+id).html($("#lo20"+id).val());
      $("#divuppertwuenty"+id).html($("#up20"+id).val());

      var ammount = $("#ammounttwuenty"+id).val()+"/"+$("#currencytwuenty"+id+" option:selected").text();
      $("#divammounttwuenty"+id).html(ammount);
    },
    error: function (request, status, error) {
      alert(request.responseText);
    }

  });

}

// Funciones container 40 

function display_forty(id){

  $("#tr_forty"+id+" .val").attr('hidden','true');
  $("#tr_forty"+id+" .in").removeAttr('hidden');
  $("#tr_forty"+id+" .in input , #tr_forty"+id+" .in select ").prop('disabled', false);


  $("#save_forty"+id).removeAttr('hidden');
  $("#cancel_forty"+id).removeAttr('hidden');
  $("#remove_forty"+id).removeAttr('hidden');
  $("#edit_forty"+id).attr('hidden','true');

}

function cancel_forty(id){

  $("#tr_forty"+id+" .val").removeAttr('hidden');
  $("#tr_forty"+id+" .in").attr('hidden','true');
  $("#tr_forty"+id+" .in input , #tr_forty"+id+" .in select ").prop('disabled', true);

  $("#save_forty"+id).attr('hidden','true');
  $("#cancel_forty"+id).attr('hidden','true');
  $("#remove_forty"+id).attr('hidden','true');
  $("#edit_forty"+id).removeAttr('hidden');

}

function save_forty(id,idval){

  $.ajax({
    type: 'GET',
    url: '../updateDetails/' + idval,
    data: {
      'lower' : $("#lowerforty"+id).val(),
      'upper' : $("#upperforty"+id).val(),
      'ammount' : $("#ammountforty"+id).val(),
      'currency_id' : $("#currencyforty"+id).val()
    },
    success: function(data) {
      swal(
        'Updated!',
        'Your Inland has been updated.',
        'success'
      )
      $("#save_forty"+id).attr('hidden','true');
      $("#cancel_forty"+id).attr('hidden','true');
      $("#remove_forty"+id).attr('hidden','true');
      $("#edit_forty"+id).removeAttr('hidden');

      $("#tr_forty"+id+" .val").removeAttr('hidden');
      $("#tr_forty"+id+" .in").attr('hidden','true');
      $("#tr_forty"+id+" .in input , #tr_forty"+id+" .in select ").prop('disabled', true);

      $("#divlowerforty"+id).html($("#lowerforty"+id).val());
      $("#divupperforty"+id).html($("#upperforty"+id).val());

      var ammount = $("#ammountforty"+id).val()+"/"+$("#currencyforty"+id+" option:selected").text();
      $("#divammountforty"+id).html(ammount);
    },
    error: function (request, status, error) {
      alert(request.responseText);
    }

  });

}

// Funciones Container 40HC

function display_fortyhc(id){

  $("#tr_fortyhc"+id+" .val").attr('hidden','true');
  $("#tr_fortyhc"+id+" .in").removeAttr('hidden');
  $("#tr_fortyhc"+id+" .in input , #tr_fortyhc"+id+" .in select ").prop('disabled', false);


  $("#save_fortyhc"+id).removeAttr('hidden');
  $("#cancel_fortyhc"+id).removeAttr('hidden');
  $("#remove_fortyhc"+id).removeAttr('hidden');
  $("#edit_fortyhc"+id).attr('hidden','true');

}

function cancel_fortyhc(id){

  $("#tr_fortyhc"+id+" .val").removeAttr('hidden');
  $("#tr_fortyhc"+id+" .in").attr('hidden','true');
  $("#tr_fortyhc"+id+" .in input , #tr_fortyhc"+id+" .in select ").prop('disabled', true);

  $("#save_fortyhc"+id).attr('hidden','true');
  $("#cancel_fortyhc"+id).attr('hidden','true');
  $("#remove_fortyhc"+id).attr('hidden','true');
  $("#edit_fortyhc"+id).removeAttr('hidden');

}

function save_fortyhc(id,idval){

  $.ajax({
    type: 'GET',
    url: '../updateDetails/' + idval,
    data: {
      'lower' : $("#lowerfortyhc"+id).val(),
      'upper' : $("#upperfortyhc"+id).val(),
      'ammount' : $("#ammountfortyhc"+id).val(),
      'currency_id' : $("#currencyfortyhc"+id).val()
    },
    success: function(data) {
      swal(
        'Updated!',
        'Your Inland has been updated.',
        'success'
      )
      $("#save_fortyhc"+id).attr('hidden','true');
      $("#cancel_fortyhc"+id).attr('hidden','true');
      $("#remove_fortyhc"+id).attr('hidden','true');
      $("#edit_fortyhc"+id).removeAttr('hidden');

      $("#tr_fortyhc"+id+" .val").removeAttr('hidden');
      $("#tr_fortyhc"+id+" .in").attr('hidden','true');
      $("#tr_fortyhc"+id+" .in input , #tr_fortyhc"+id+" .in select ").prop('disabled', true);

      $("#divlowerfortyhc"+id).html($("#lowerfortyhc"+id).val());
      $("#divupperfortyhc"+id).html($("#upperfortyhc"+id).val());

      var ammount = $("#ammountfortyhc"+id).val()+"/"+$("#currencyfortyhc"+id+" option:selected").text();
      $("#divammountfortyhc"+id).html(ammount);
    },
    error: function (request, status, error) {
      alert(request.responseText);
    }

  });

}


$("#newtwuenty").on("click", function() {

  var $template = $('#twuentyclone');
  $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
  $ids = $( ".low20" ).length + 1;

  $myClone.find(".cloLow20").removeAttr('id').attr('id', 'lo20'+$ids).attr('onblur',"validateRange(this.id,'t20clone')").attr('required', 'true').addClass('low20');

  $myClone.find(".cloUp20").removeAttr('id').attr('id', 'up20'+$ids).attr('onblur',"validateRange(this.id,'t20')").attr('required', 'true').addClass('up20');

  $myClone.find(".sel").addClass('col-lg-4'); 
  $("#twuenty").append($myClone);
});

$("#newforty").on("click", function() {

  var $template = $('#fortyclone');
  $myClone = $template.clone().removeAttr('hidden').removeAttr('id');

  $ids = $( ".low40" ).length + 1;
  $myClone.find(".cloLow40").removeAttr('id').attr('id', 'lo40'+$ids).attr('onblur',"validateRange40(this.id,'t40clone')").attr('required', 'true').addClass('low40');

  $myClone.find(".cloUp40").removeAttr('id').attr('id', 'up40'+$ids).attr('onblur',"validateRange40(this.id,'t40')").attr('required', 'true').addClass('up40');

  $myClone.find(".sel").addClass('col-lg-4'); 
  $("#forty").append($myClone);
});

$("#newfortyhc").on("click", function() {

  var $template = $('#fortyhcclone');
  $myClone = $template.clone().removeAttr('hidden').removeAttr('id');


  $ids = $( ".low40H" ).length + 1;

  $myClone.find(".cloLow40H").removeAttr('id').attr('id', 'lo40H'+$ids).attr('onblur',"validateRange40hc(this.id,'t40cloneH')").attr('required', 'true').addClass('low40H');

  $myClone.find(".cloUp40H").removeAttr('id').attr('id', 'up40H'+$ids).attr('onblur',"validateRange40hc(this.id,'t40H')").attr('required', 'true').addClass('up40H');
  $myClone.find(".sel").addClass('col-lg-4'); 
  $("#fortyhc").append($myClone);
});

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
        url: '../deleteDetails/' + idval,
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

$(document).on('click', '.remove', function () {
  $(this).closest('tr').remove();
  $('#msg20').hide();
});

function deleteInland(idval){

  swal({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it!'
  }).then(function(result) {
    if (result.value) {

      $.ajax({
        type: 'GET',
        url: 'inlands/deleteInland/' + idval,
        async: false, 
        success: function(data) {

          swal({
            title: 'Deleted',
            text: "Your file has been deleted.",
            type: 'success',
            showCancelButton: false,
            confirmButtonText: 'Ok!'
          }).then(function(result) {
            if (result.value) {
              window.location = "";
            }

          });   





        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
      });

    }

  });


}

function validateRange(id,tipo){

  if(tipo == 't20'){
    var idval = id.substr(4);
    $('.low20').each(function(index){

      var id = index + 1;

      if(id <= idval ){
        var low = parseInt($('#lo20'+id).val());
        var up =  parseInt($('#up20'+idval).val());
        if(low >= up ){
          $('#msg20').show();
          $('#up20'+idval).val("");

        }else{
          $('#msg20').hide();
        }
      }else{
        $('#up20'+id).val("");
        $('#lo20'+id).val("");
      }
    });
  } 

  if(tipo == 't20ELOW'){
    $('#msg20').hide();
    var idval = id.substr(4);
    var bool = 'false';
    var low = parseInt($('#lo20'+idval).val());
    var up =  parseInt($('#up20'+idval).val());
    if(low >= up){
      bool  ='true';
    }
    if(bool == 'true'){
      $('#msg20').show();
      $('#lo20'+idval).val(0);
    }
  } 

  if(tipo == 't20EUP'){

    $('#msg20').hide();
    var idval = id.substr(4);
    var idprox = parseInt(idval) + 1;

    var bool = 'false';

    var up =  parseInt($('#up20'+idval).val());
    var low =  parseInt($('#lo20'+idval).val());
    var lowprox = parseInt($('#lo20'+idprox).val());

    if(up >= lowprox || up <= low){
      bool  ='true';
    }
     if(bool == 'true'){
      $('#msg20').show();
      $('#up20'+idval).val(0);
    }
  } 

  if(tipo == 't20clone'){
    var idval = id.substr(4);
    var idanterior = idval - 1;
    var low = parseInt($('#lo20'+idval).val());
    var up =  parseInt($('#up20'+idval).val());
    var upAnterior =  parseInt($('#up20'+idanterior).val());
    if((low <= upAnterior) || (low >= up)){
      $('#msg20').show();
      $('#up20'+idval).val("");
      $('#lo20'+idval).val("");

    }else{
      $('#msg20').hide();
    }
  } 


}
function validateRange40(id,tipo){

  if(tipo == 't40'){
    var idval = id.substr(4);
    $('.low40').each(function(index){
      var id = index + 1;
      if(id <= idval ){
        var low = parseInt($('#lo40'+id).val());
        var up =  parseInt($('#up40'+idval).val());
        if(low >= up ){
          $('#msg40').show();
          $('#up40'+idval).val("");

        }else{
          $('#msg40').hide();
        }
      }else{
        $('#up40'+id).val("");
        $('#lo40'+id).val("");
      }
    });
  } 

  if(tipo == 't40clone'){
    var idval = id.substr(4);
    var idanterior = idval - 1;
    var low = parseInt($('#lo40'+idval).val());
    var up =  parseInt($('#up40'+idval).val());
    var upAnterior =  parseInt($('#up40'+idanterior).val());
    if((low <= upAnterior) || (low >= up)){
      $('#msg40').show();
      $('#up40'+idval).val("");
      $('#lo40'+idval).val("");

    }else{
      $('#msg40').hide();
    }
  } 


}
function validateRange40hc(id,tipo){


  if(tipo == 't40H'){
    var idval = id.substr(5);
    $('.low40H').each(function(index){
      var id = index + 1;
      if(id <= idval ){
        var low = parseInt($('#lo40H'+id).val());
        var up =  parseInt($('#up40H'+idval).val());
        if(low >= up ){
          $('#msg40H').show();
          $('#up40H'+idval).val("");

        }else{
          $('#msg40H').hide();
        }
      }else{
        $('#up40H'+id).val("");
        $('#lo40H'+id).val("");
      }
    });
  } 

  if(tipo == 't40cloneH'){
    var idval = id.substr(5);
    var idanterior = idval - 1;
    var low = parseInt($('#lo40H'+idval).val());
    var up =  parseInt($('#up40H'+idval).val());
    var upAnterior =  parseInt($('#up40H'+idanterior).val());
    if((low <= upAnterior) || (low >= up)){
      $('#msg40H').show();
      $('#up40H'+idval).val("");
      $('#lo40H'+idval).val("");

    }else{
      $('#msg40H').hide();
    }
  } 


}

$('.m-select2-general').select2({
  placeholder: "Select an option"
});



