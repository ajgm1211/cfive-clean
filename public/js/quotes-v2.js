$.fn.editable.defaults.mode = 'inline';

$(document).ready(function() {

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  //Hide grouped options in pdf layout
  if($('#show_hide_select').val()=='total in'){
    $(".group_origin_charges").addClass('hide');
    $(".group_freight_charges").addClass('hide');
    $(".group_destination_charges").addClass('hide');
  }    

  //Show total amounts for freight
  var sum_freight=0;
  $(".total_freight_20").each(function(){
    sum_freight = sum_freight + parseFloat($(this).html());
  });
  $(".total_freight_40").each(function(){
    sum_freight = sum_freight + parseFloat($(this).html());
  });
  $(".total_freight_40hc").each(function(){
    sum_freight = sum_freight + parseFloat($(this).html());
  });
  $(".total_freight_40nor").each(function(){
    sum_freight = sum_freight + parseFloat($(this).html());
  });
  $(".total_freight_45").each(function(){
    sum_freight = sum_freight + parseFloat($(this).html());
  });
  $("#sub_total_freight").html(sum_freight + " USD");

  //Show total amounts for origin
  var sum_origin=0;
  $(".total_origin_20").each(function(){
    sum_origin = sum_origin + parseFloat($(this).html());
  });
  $(".total_origin_40").each(function(){
    sum_origin = sum_origin + parseFloat($(this).html());
  });
  $(".total_origin_40hc").each(function(){
    sum_origin = sum_origin + parseFloat($(this).html());
  });
  $(".total_origin_40nor").each(function(){
    sum_origin = sum_origin + parseFloat($(this).html());
  });
  $(".total_origin_45").each(function(){
    sum_origin = sum_origin + parseFloat($(this).html());
  });
  $("#sub_total_origin").html(sum_origin + " USD");

  //Show total amounts for origin
  var sum_destination=0;
  $(".total_destination_20").each(function(){
    sum_destination = sum_destination + parseFloat($(this).html());
  });
  $(".total_destination_40").each(function(){
    sum_destination = sum_destination + parseFloat($(this).html());
  });
  $(".total_destination_40hc").each(function(){
    sum_destination = sum_destination + parseFloat($(this).html());
  });
  $(".total_destination_40nor").each(function(){
    sum_destination = sum_destination + parseFloat($(this).html());
  });
  $(".total_destination_45").each(function(){
    sum_destination = sum_destination + parseFloat($(this).html());
  });
  $("#sub_total_destination").html(sum_destination + " USD");

  $("#total").html(sum_origin+sum_destination+sum_freight+" USD");   


  $('.editable').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,
    success: function(response, newValue) {

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,
    success: function(response, newValue) {

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-lcl-air').editable({
    url:'/v2/quotes/lcl/charges/update',
    emptytext:0,
    success: function(response, newValue) {

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-20').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_20').attr('data-value'));
      $(this).closest('tr').find('.total_20').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-m20').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_20').attr('data-value'));
      $(this).closest('tr').find('.total_20').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-40').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40').attr('data-value'));
      $(this).closest('tr').find('.total_40').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-m40').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40').attr('data-value'));
      $(this).closest('tr').find('.total_40').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-40hc').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40hc').attr('data-value'));
      $(this).closest('tr').find('.total_40hc').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-m40hc').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40hc').attr('data-value'));
      $(this).closest('tr').find('.total_40hc').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-40nor').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40nor').attr('data-value'));
      $(this).closest('tr').find('.total_40nor').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-m40nor').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40nor').attr('data-value'));
      $(this).closest('tr').find('.total_40nor').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-45').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_45').attr('data-value'));
      $(this).closest('tr').find('.total_45').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-inland-m45').editable({
    url:'/v2/quotes/inland/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_45').attr('data-value'));
      $(this).closest('tr').find('.total_45').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  //Inline edit amounts
  $('.editable-amount-20').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_20').attr('data-value'));
      $(this).closest('tr').find('.total_20').html(total);
      //$(this).closest('tr').find('.total_freight_20').html('here');
      $('.editable-amount-20').children().css( "background-color", "red" );

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-markup-20').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_20').attr('data-value'));
      $(this).closest('tr').find('.total_20').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-amount-40').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40').attr('data-value'));
      $(this).closest('tr').find('.total_40').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-markup-40').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40').attr('data-value'));
      $(this).closest('tr').find('.total_40').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-amount-40hc').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40hc').attr('data-value'));
      $(this).closest('tr').find('.total_40hc').html(total);


      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-markup-40hc').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40hc').attr('data-value'));
      $(this).closest('tr').find('.total_40hc').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-amount-40nor').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40nor').attr('data-value'));
      $(this).closest('tr').find('.total_40nor').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-markup-40nor').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40nor').attr('data-value'));
      $(this).closest('tr').find('.total_40nor').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-amount-45').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_45').attr('data-value'));
      $(this).closest('tr').find('.total_45').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-markup-45').editable({
    url:'/v2/quotes/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_45').attr('data-value'));
      $(this).closest('tr').find('.total_45').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  //Inline rates charges
  $('.editable-rate-amount-20').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_20').attr('data-value'));
      $(this).closest('tr').find('.total_20').html(total);
      //$(this).closest('tr').find('.total_freight_20').html('here');
      $('.editable-amount-20').children().css( "background-color", "red" );

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-markup-20').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_20').attr('data-value'));
      $(this).closest('tr').find('.total_20').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-amount-40').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40').attr('data-value'));
      $(this).closest('tr').find('.total_40').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-markup-40').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40').attr('data-value'));
      $(this).closest('tr').find('.total_40').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-amount-40hc').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40hc').attr('data-value'));
      $(this).closest('tr').find('.total_40hc').html(total);


      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-markup-40hc').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40hc').attr('data-value'));
      $(this).closest('tr').find('.total_40hc').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-amount-40nor').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40nor').attr('data-value'));
      $(this).closest('tr').find('.total_40nor').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-markup-40nor').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40nor').attr('data-value'));
      $(this).closest('tr').find('.total_40nor').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-amount-45').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_45').attr('data-value'));
      $(this).closest('tr').find('.total_45').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });

  $('.editable-rate-markup-45').editable({
    url:'/v2/quotes/rate/charges/update',
    emptytext:0,    
    success: function(response, newValue) {

      total =  parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_45').attr('data-value'));
      $(this).closest('tr').find('.total_45').html(total);

      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });



  $('#created_at').editable({
    format: 'yyyy-mm-dd',
    viewformat: 'dd/mm/yyyy',
    datepicker: {
      weekStart: 1
    },
    url:'/v2/quotes/update/details',
    success: function(response, newValue) {
      if(!response) {
        return "Unknown error!";
      }

      if(response.success === false) {
        return response.msg;
      }
    }
  });
});

//Add rates lcl
$(document).on('click', '.store_charge_lcl', function () {
  var id = $(this).closest("tr").find(".automatic_rate_id").val();
  var surcharge_id = $(this).closest("tr").find(".surcharge_id").val();
  var calculation_type_id = $(this).closest("tr").find(".calculation_type_id").val();
  var units = $(this).closest("tr").find(".units").val();
  var price_per_unit = $(this).closest("tr").find(".price_per_unit").val();
  var total = $(this).closest("tr").find(".total_2").val();
  var markup = $(this).closest("tr").find(".markup").val();
  var type_id = $(this).closest("tr").find(".type_id").val();
  var currency_id = $(this).closest("tr").find(".currency_id").val();

  $.ajax({
    type: 'POST',
    url: '/v2/quotes/lcl/store/charge',
    data:{
      "automatic_rate_id":id,
      "surcharge_id":surcharge_id,
      "calculation_type_id":calculation_type_id,
      "units":units,
      "price_per_unit":price_per_unit,
      "total":total,
      "markup":markup,
      "type_id":type_id,
      "currency_id":currency_id
    },
    success: function(data) {
      if(data.message=='Ok'){
        swal(
          'Done!',
          'Charge saved successfully',
          'success'
        )
      }
      setTimeout(location.reload.bind(location), 3000);
    }
  });
});

//Add rates
$(document).on('click', '.store_charge', function () {
  var id = $(this).closest("tr").find(".automatic_rate_id").val();
  var surcharge_id = $(this).closest("tr").find(".surcharge_id").val();
  var calculation_type_id = $(this).closest("tr").find(".calculation_type_id").val();
  var amount_c20 = $(this).closest("tr").find(".amount_c20").val();
  var markup_c20 = $(this).closest("tr").find(".markup_c20").val();
  var amount_c40 = $(this).closest("tr").find(".amount_c40").val();
  var markup_c40 = $(this).closest("tr").find(".markup_c40").val();
  var amount_c40hc = $(this).closest("tr").find(".amount_c40hc").val();
  var markup_c40hc = $(this).closest("tr").find(".markup_c40hc").val();
  var amount_c40nor = $(this).closest("tr").find(".amount_c40nor").val();
  var markup_c40nor = $(this).closest("tr").find(".markup_c40nor").val();
  var amount_c45 = $(this).closest("tr").find(".amount_c45").val();
  var markup_c45 = $(this).closest("tr").find(".markup_c45").val();
  var type_id = $(this).closest("tr").find(".type_id").val();
  var currency_id = $(this).closest("tr").find(".currency_id").val();

  $.ajax({
    type: 'POST',
    url: '/v2/quotes/store/charge',
    data:{
      "automatic_rate_id":id,
      "surcharge_id":surcharge_id,
      "calculation_type_id":calculation_type_id,
      "amount_c20":amount_c20,
      "markup_c20":markup_c20,
      "amount_c40":amount_c40,
      "markup_c40":markup_c40,
      "amount_c40hc":amount_c40hc,
      "markup_c40hc":markup_c40hc,
      "amount_c40nor":amount_c40nor,
      "markup_c40nor":markup_c40nor,
      "amount_c45":amount_c45,
      "markup_c45":markup_c45,
      "type_id":type_id,
      "currency_id":currency_id
    },
    success: function(data) {
      if(data.message=='Ok'){
        swal(
          'Done!',
          'Charge saved successfully',
          'success'
        )
      }
      setTimeout(location.reload.bind(location), 3000);
    }
  });
});

//Delete rates
$(document).on('click', '.delete-rate', function () {
  var id=$(this).attr('data-rate-id');
  var theElement = $(this);
  swal({
    title: 'Are you sure?',
    text: "Please confirm!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, I am sure!'
  }).then(function (result) {
    if (result.value) {
      $.ajax({
        type: 'GET',
        url: '/v2/quotes/delete/rate/'+id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Updated!',
              'The rete has been deleted.',
              'success'
            )
            $(theElement).closest('.row').find('.tab-content').remove();
            //setTimeout(location.reload.bind(location), 3000);
          }
        }
      });
    }
  });
});

//Delete charges
$(document).on('click', '.delete-charge', function () {
  var id=$(this).closest('tr').find('.charge_id').val();
  var theElement = $(this);
  swal({
    title: 'Are you sure?',
    text: "Please confirm!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, I am sure!'
  }).then(function (result) {
    if (result.value) {
      $.ajax({
        type: 'GET',
        url: '/v2/quotes/delete/charge/'+id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Updated!',
              'The charge has been deleted.',
              'success'
            )
          }
          $(theElement).closest('tr').remove();
          //setTimeout(location.reload.bind(location), 3000);
        }
      });
    }
  });
});

$(document).on('click', '.delete-charge-lcl', function () {
  var id=$(this).closest('tr').find('.charge_id').val();
  var theElement = $(this);
  swal({
    title: 'Are you sure?',
    text: "Please confirm!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, I am sure!'
  }).then(function (result) {
    if (result.value) {
      $.ajax({
        type: 'GET',
        url: '/v2/quotes/lcl/delete/charge/'+id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Updated!',
              'The charge has been deleted.',
              'success'
            )
          }
          $(theElement).closest('tr').remove();
          //setTimeout(location.reload.bind(location), 3000);
        }
      });
    }
  });
});

//Edit payments
$(document).on('click', '#edit-payments', function () {
  $(".payment_conditions_span").attr('hidden','true');
  $(".payment_conditions_textarea").removeAttr('hidden');
  $("#update_payments").removeAttr('hidden');
});

$(document).on('click', '#cancel-payments', function () {
  $(".payment_conditions_span").removeAttr('hidden');
  $(".payment_conditions_textarea").attr('hidden','true');
  $("#update_payments").attr('hidden','true');
});

$(document).on('click', '#update-payments', function () {
  var id=$(".id").val();
  var payments = tinymce.get("payment_conditions").getContent();
  $.ajax({
    type: 'POST',
    url: '/v2/quotes/update/payments/'+id,
    data: {
      'payments': payments,
    },
    success: function(data) {
      if(data.message=='Ok'){
        swal(
          'Updated!',
          'The payment conditions has been updated.',
          'success'
        )

        $(".payment_conditions_span").html(data.quote['payment_conditions']);
        $(".payment_conditions_span").removeAttr('hidden');
        $(".payment_conditions_textarea").attr('hidden','true');
        $("#update_payments").attr('hidden','true');
      }
    }
  });
});

//Edit terms
$(document).on('click', '#edit-terms', function () {
  $(".terms_and_conditions_span").attr('hidden','true');
  $(".terms_and_conditions_textarea").removeAttr('hidden');
  $("#update_terms").removeAttr('hidden');
});

$(document).on('click', '#cancel-terms', function () {
  $(".terms_and_conditions_span").removeAttr('hidden');
  $(".terms_and_conditions_textarea").attr('hidden','true');
  $("#update_terms").attr('hidden','true');
});

$(document).on('click', '#update-terms', function () {
  var id=$(".id").val();
  var terms = tinymce.get("terms_and_conditions").getContent();
  $.ajax({
    type: 'POST',
    url: '/v2/quotes/update/terms/'+id,
    data: {
      'terms': terms,
    },
    success: function(data) {
      if(data.message=='Ok'){
        swal(
          'Updated!',
          'The terms and conditions has been updated.',
          'success'
        )

        $(".terms_and_conditions_span").html(data.quote['terms_and_conditions']);
        $(".terms_and_conditions_span").removeAttr('hidden');
        $(".terms_and_conditions_textarea").attr('hidden','true');
        $("#update_terms").attr('hidden','true');
      }
    }
  });
});

//Edit remarks
function edit_remark($span,$textarea,$update_box){
  $('.'+$span).attr('hidden','true');
  $('.'+$textarea).removeAttr('hidden');
  $('.'+$update_box).removeAttr('hidden');
}

function cancel_update($span,$textarea,$update_box){
  $('.'+$span).removeAttr('hidden');
  $('.'+$textarea).attr('hidden','true');
  $('.'+$update_box).attr('hidden','true');
}

function update_remark($id,$content,$v){
  var id=$(".id").val();
  var remarks = tinymce.get($content).getContent();
  $.ajax({
    type: 'POST',
    url: '/v2/quotes/update/remarks/'+$id,
    data: {
      'remarks': remarks,
    },
    success: function(data) {
      if(data.message=='Ok'){
        swal(
          'Updated!',
          'The remarks has been updated.',
          'success'
        )

        $(".remarks_span_"+$v).html(data.rate['remarks']);
        $(".remarks_span_"+$v).removeAttr('hidden');
        $(".remarks_textarea_"+$v).attr('hidden','true');
        $(".update_remarks_"+$v).attr('hidden','true');
      }
    }
  });
};

//Edit main quotes details
$(document).on('click', '#edit-quote', function () {
  $(".quote_id_span").attr('hidden','true');
  $(".company_span").attr('hidden','true');
  $(".status_span").attr('hidden','true');
  $(".delivery_type_span").attr('hidden','true');
  $(".price_level_span").attr('hidden','true');
  $(".type_span").attr('hidden','true');
  $(".incoterm_id_span").attr('hidden','true');
  $(".commodity_span").attr('hidden','true');
  $(".kind_of_cargo_span").attr('hidden','true');
  $(".contact_id_span").attr('hidden','true');
  $(".validity_span").attr('hidden','true');
  $(".user_id_span").attr('hidden','true');
  $(".date_issued_span").attr('hidden','true');
  $(".equipment_span").attr('hidden','true');
  $(".quote_id").removeAttr('hidden');
  $(".company_id").removeAttr('hidden');
  $(".type").removeAttr('hidden');
  $(".status").removeAttr('hidden');
  $(".delivery_type").removeAttr('hidden');
  $(".incoterm_id").removeAttr('hidden');
  $(".commodity").removeAttr('hidden');
  $(".kind_of_cargo").removeAttr('hidden');
  $(".contact_id").removeAttr('hidden');
  $(".contact_id").prop('disabled',false);
  $(".validity").removeAttr('hidden');
  $(".user_id").removeAttr('hidden');
  $(".equipment").removeAttr('hidden');
  $(".date_issued").removeAttr('hidden');
  $(".price_id").removeAttr('hidden');
  $("#update_buttons").removeAttr('hidden');
  $("#edit_li").attr('hidden','true');
  $(".type").select2();
  $(".status").select2();
  $(".kind_of_cargo").select2();
  $(".company_id").select2();
  $(".delivery_type").select2();
  $(".incoterm_id").select2();
  $(".contact_id").select2();
  $(".user_id").select2();
  $(".price_id").select2();
  $(".equipment").select2();
});

$(document).on('click', '#cancel', function () {
  $(".quote_id_span").removeAttr('hidden');
  $(".company_span").removeAttr('hidden');
  $(".status_span").removeAttr('hidden');
  $(".delivery_type_span").removeAttr('hidden');
  $(".price_level_span").removeAttr('hidden');
  $(".type_span").removeAttr('hidden');
  $(".incoterm_id_span").removeAttr('hidden');
  $(".commodity_span").removeAttr('hidden');
  $(".kind_of_cargo_span").removeAttr('hidden');
  $(".contact_id_span").removeAttr('hidden');
  $(".validity_span").removeAttr('hidden');
  $(".user_id_span").removeAttr('hidden');
  $(".date_issued_span").removeAttr('hidden');
  $(".equipment_span").removeAttr('hidden');
  $(".quote_id").attr('hidden','true');
  $(".company_id").attr('hidden','true');
  $(".type").attr('hidden','true');
  $(".status").attr('hidden','true');
  $(".delivery_type").attr('hidden','true');
  $(".incoterm_id").attr('hidden','true');
  $(".commodity").attr('hidden','true');
  $(".kind_of_cargo").attr('hidden','true');
  $(".contact_id").attr('hidden','true');
  $(".validity").attr('hidden','true');
  $(".user_id").attr('hidden','true');
  $(".date_issued").attr('hidden','true');
  $(".equipment").attr('hidden','true');
  $(".price_id").attr('hidden','true');
  $("#update_buttons").attr('hidden','true');
  $("#edit_li").removeAttr('hidden');
  $(".type").select2('destroy');
  $(".kind_of_cargo").select2('destroy');
  $(".status").select2('destroy');
  $(".company_id").select2('destroy');
  $(".delivery_type").select2('destroy');
  $(".incoterm_id").select2('destroy');
  $(".contact_id").select2('destroy');
  $(".user_id").select2('destroy');
  $(".price_id").select2('destroy');
  $(".equipment").select2('destroy');
});

$(document).on('click', '#update', function () {
  var id=$(".id").val();
  var quote_id=$(".quote_id").val();
  var company_id=$(".company_id").val();
  var type=$(".type").val();
  var status=$(".status").val();
  var delivery_type=$(".delivery_type").val();
  var incoterm_id=$(".incoterm_id").val();
  var contact_id=$(".contact_id").val();
  var validity=$(".validity").val();
  var equipment=$(".equipment").val();
  var user_id=$(".user_id").val();
  var date_issued=$(".date_issued").val();
  var price_id=$(".price_id").val();
  var commodity=$(".commodity").val();
  var kind_of_cargo=$(".kind_of_cargo").val();

  $.ajax({
    type: 'POST',
    url: '/v2/quotes/update/'+id,
    data: {
      'quote_id': quote_id,
      'company_id': company_id,
      'type': type,
      'status': status,
      'delivery_type': delivery_type,
      'incoterm_id': incoterm_id,
      'contact_id': contact_id,
      'validity': validity,
      'equipment': equipment,
      'user_id': user_id,
      'date_issued': date_issued,
      'price_id': price_id,
      'commodity': commodity,
      'kind_of_cargo': kind_of_cargo,
    },
    success: function(data) {
      if(data.message=='Ok'){
        swal(
          'Updated!',
          'Your quote has been updated.',
          'success'
        )
        var incoterm = data.quote['incoterm_id'];
        var delivery_type = data.quote['delivery_type'];

        if(incoterm==1){
          incoterm='EWX';
        }else if(incoterm==2){
          incoterm='FAS';
        }else if(incoterm==3){
          incoterm='FCA';
        }else if(incoterm==4){
          incoterm='FOB';
        }else if(incoterm==5){
          incoterm='CFR';
        }else if(incoterm==6){
          incoterm='CIF';
        }else if(incoterm==7){
          incoterm='CIP';
        }else if(incoterm==8){
          incoterm='DAT';
        }else if(incoterm==9){
          incoterm='DAP';
        }else{
          incoterm='DDP';
        }

        if(delivery_type==1){
          delivery_type='Port to Port';
        }else if(delivery_type==2){
          delivery_type='Port to Door';
        }else if(delivery_type==3){
          delivery_type='Door to Port';
        }else{
          delivery_type='Door to Door'
        }
        $(".type").val(data.quote['type']);
        $(".type_span").html(data.quote['type']);
        if(data.quote['custom_quote_id']!=''){
          $(".quote_id").val(data.quote['custom_quote_id']);
          $(".quote_id_span").html(data.quote['custom_quote_id']);    
        }else{
          $(".quote_id").val(data.quote['quote_id']);
          $(".quote_id_span").html(data.quote['quote_id']);
        }
        $(".company_id").val(data.quote['company_id']);
        $(".company_id_span").html(data.quote['company_id']);
        $(".status").val(data.quote['status']);
        $(".status_span").html(data.quote['status']+' <i class="fa fa-check"></i>');
        $(".status_span").addClass('Status_'+data.quote['status']);
        $(".delivery_type").val(data.quote['delivery_type']);
        $(".delivery_type_span").html(delivery_type);
        $(".incoterm_id").val(data.quote['incoterm_id']);
        $(".incoterm_id_span").html(incoterm);
        $(".commodity").val(data.quote['commodity']);
        $(".commodity_span").html(data.quote['commodity']);
        $(".kind_of_cargo").val(data.quote['kind_of_cargo']);
        $(".kind_of_cargo_span").html(data.quote['kind_of_cargo']);        
        $(".equipment").val(data.quote['equipment']);
        $(".equipment_span").empty();
        var length = $.parseJSON(data.quote['equipment']).length;
        $.each($.parseJSON(data.quote['equipment']), function( index, value ){
          if (index === (length-1)) {
            $(".equipment_span").append(value);
          }else{
            $(".equipment_span").append(value + ', ');
          }
        });

        $(".contact_id").val(data.quote['contact_id']);
        $(".contact_id_span").html(data.contact_name);
        $(".user_id").val(data.quote['user_id']);
        $(".user_id_span").html(data['owner']);
        $(".date_issued").val(data.quote['date_issued']);
        $(".date_issued_span").html(data.quote['date_issued']);
        $(".price_id").val(data.quote['price_id']);
        $(".price_level_span").html(data['price_name']);
        $(".validity").val(data.quote['validity_start']+'/'+data.quote['validity_end']);
        $(".validity_span").html(data.quote['validity_start']+'/'+data.quote['validity_end']);

        $(".quote_id_span").removeAttr('hidden');
        $(".company_span").removeAttr('hidden');
        $(".status_span").removeAttr('hidden');
        $(".delivery_type_span").removeAttr('hidden');
        $(".price_level_span").removeAttr('hidden');
        $(".type_span").removeAttr('hidden');
        $(".incoterm_id_span").removeAttr('hidden');
        $(".commodity_span").removeAttr('hidden');
        $(".kind_of_cargo_span").removeAttr('hidden');
        $(".contact_id_span").removeAttr('hidden');
        $(".validity_span").removeAttr('hidden');
        $(".user_id_span").removeAttr('hidden');
        $(".date_issued_span").removeAttr('hidden');
        $(".equipment_span").removeAttr('hidden');
        $(".quote_id").attr('hidden','true');
        $(".company_id").attr('hidden','true');
        $(".type").attr('hidden','true');
        $(".status").attr('hidden','true');
        $(".delivery_type").attr('hidden','true');
        $(".incoterm_id").attr('hidden','true');
        $(".commodity").attr('hidden','true');
        $(".kind_of_cargo").attr('hidden','true');
        $(".contact_id").attr('hidden','true');
        $(".validity").attr('hidden','true');
        $(".user_id").attr('hidden','true');
        $(".date_issued").attr('hidden','true');
        $(".price_id").attr('hidden','true');
        $(".equipment").attr('hidden','true');
        $("#update_buttons").attr('hidden','true');
        $("#edit_li").removeAttr('hidden');
        $(".type").select2('destroy');
        $(".status").select2('destroy');
        $(".company_id").select2('destroy');
        $(".delivery_type").select2('destroy');
        $(".incoterm_id").select2('destroy');
        $(".contact_id").select2('destroy');
        $(".user_id").select2('destroy');
        $(".price_id").select2('destroy');
        $(".equipment").select2('destroy');
        $(".kind_of_cargo").select2('destroy');

        //Refresh page after 5 seconds
        //setTimeout(location.reload.bind(location), 5000);
      }
    }
  });
});

//Charges 
$('.date_issued').datetimepicker();

$('.select2-freight').select2();

$('.select2-origin').select2();

$('.select2-destination').select2();

function addFreightCharge($value){
  var $template = $('#freight_charges_'+$value),
      $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertAfter($template)
  $clone.find("select").select2({
    placeholder: "Currency"
  });
}

function addOriginCharge($value){
  var $template = $('#origin_charges_'+$value),
      $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertAfter($template)
  $clone.find("select").select2({
    placeholder: "Currency"
  });
}

function addDestinationCharge($value){
  var $template = $('#destination_charges_'+$value),
      $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertAfter($template)
  $clone.find("select").select2({
    placeholder: "Currency"
  });
}

$(document).on('click', '.removeFreightCharge', function (e) {
  $(this).closest('tr').remove();
});

$(document).on('click', '.removeOriginCharge', function (e) {
  $(this).closest('tr').remove();
});

$(document).on('click', '.removeDestinationCharge', function (e) {
  $(this).closest('tr').remove();
});

//Sending quotes
$(document).on('click', '#send-pdf-quotev2', function () {
  var id = $('#quote-id').val();
  var email = $('#quote_email').val();
  var to = $('#addresse').val();
  var email_template_id = $('#email_template').val();
  var email_subject = $('#email-subject').val();
  var email_body = $('#email-body').val();

  if(email_template_id!=''&&to!=''){
    $.ajax({
      type: 'POST',
      url: '/v2/quotes/send',
      data:{"email_template_id":email_template_id,"id":id,"subject":email_subject,"body":email_body,"to":to},
      beforeSend: function () {
        $('#send-pdf-quotev2').hide();
        $('#send-pdf-quote-sending').show();
      },
      success: function(data) {
        $('#spin').hide();
        $('#send-pdf-quotev2').show();
        $('#send-pdf-quote-sending').hide();
        if(data.message=='Ok'){
          $('#SendQuoteModal').modal('toggle');
          $('body').removeClass('modal-open');
          $('.modal-backdrop').remove();
          $('#subject-box').html('');
          $('.editor').html('');
          $('#textarea-box').hide();
          swal(
            'Done!',
            'Your message has been sent.',
            'success'
          )
        }else{
          swal(
            'Error!',
            'Your message has not been sent.',
            'error'
          )
        }
      }
    });
  }else{
    swal(
      '',
      'Please complete all fields',
      'error'
    )
  }
});

/*** PDF ***/

//Show and hide pdf layouts options
$(document).on('change', '#show_hide_select', function () {
  if($('#show_hide_select').val()=='total in'){
    $(".group_origin_charges").addClass('hide');
    $(".group_destination_charges").addClass('hide');
    $(".group_freight_charges").addClass('hide');
  }else{
    $(".group_origin_charges").removeClass('hide');
    $(".group_destination_charges").removeClass('hide');
    $(".group_freight_charges").removeClass('hide');      
  }

});

//Updating pdf features
$(document).on('change', '.pdf-feature', function () {
  var id=$(this).attr('data-quote-id');
  var name=$(this).attr('data-name');
  var value=0;
  if($(this).attr('data-type')=='checkbox'){
    if($(this). prop("checked") == true){
      value=1;
    }
  }else{
    value=$(this).val();
  }
  $.ajax({
    type: 'POST',
    url: '/v2/quotes/feature/pdf/update',
    data:{"value":value,"name":name,"id":id},
    success: function(data) {
      if(data.message=='Ok'){
        //$(this).attr('checked', true).val(0);
      }
    }
  });
});

//Calculating total in charges air lcl

$(document).on("change keyup keydown", ".units, .price_per_unit, .markup", function() {
  var sum = 0;
  var total_amount = 0;
  var markup = 0;
  var total=0;
  var self = this;
  var currency_cfg = $("#currency_id").val();
  $(".price_per_unit").each(function(){
    $( this).each(function() {
      var quantity = $(this).closest('tr').find('.units').val();
      var currency_id = $(self).closest('tr').find('.currency_id').val();

      if(quantity > 0) {
        if ($(self).closest('tr').find('.currency_id').val() != "") {
          $.ajax({
            url: '/api/currency/'+currency_id,
            dataType: 'json',
            success: function (json) {
              var amount = $(self).closest('tr').find('.price_per_unit').val();
              var quantity = $(self).closest('tr').find('.units').val();
              markup = $(self).closest('tr').find('.markup').val();
              var sub_total = amount * quantity;

              /*if(currency_cfg+json.alphacode == json.api_code){
                total = sub_total / json.rates;
              }else{
                total = sub_total / json.rates_eur;
              }*/
              total = sub_total.toFixed(2);

              if(markup > 0){
                var total_amount_m = Number(total)+ Number(markup);
                $(self).closest('tr').find('.total_2').val(total_amount_m.toFixed(2));
                $(self).closest('tr').find('.total_2').change();
              }else{
                $(self).closest('tr').find('.total_2').val(total);
                $(self).closest('tr').find('.total_2').change();
              }
            }
          });
        }
        total_amount = quantity * $(this).val();
        $(this).closest('tr').find('.total').val(total_amount);
        $(this).closest('tr').find('.total').change();
      }else{
        total_amount = 0;
        $(this).closest('tr').find('.total').val(total_amount);
        $(this).closest('tr').find('.total').change();
      }
    });
  });
});

$( document ).ready(function() {
  $( "select[name='company_id']" ).on('change', function() {
    var company_id = $(this).val();
    if(company_id) {
      $('select[name="contact_id"]').empty();
      $.ajax({
        url: "/quotes/company/contact/id/"+company_id,
        dataType: 'json',
        success: function(data) {
          $('select[name="client"]').empty();
          $('select[name="contact_id"]').append('<option value="">Select an option</option>');
          $.each(data, function(key, value) {
            $('select[name="contact_id"]').append('<option value="'+ key +'">'+ value +'</option>');
          });
        }
      });
      $.ajax({
        url: "/quotes/company/price/id/"+company_id,
        dataType: 'json',
        success: function(data) {
          $('select[name="price_id"]').empty();
          $('select[name="price_id"]').append('<option value="">Select an option</option>');
          $.each(data, function(key, value) {
            $('select[name="price_id"]').append('<option value="'+ key +'">'+ value +'</option>');
          });
        }
      });
    }else{
      $('select[name="contact_id"]').empty();
      $('select[name="price_id"]').empty();
    }
  });
});

//Custom functions

function show_hide_element($element,$button){
  if($('.'+$element).hasClass('hide')){
    $('.'+$element).removeClass('hide');
  }else{
    $('.'+$element).addClass('hide');
  }
}

function precargar(){
  var company_id = $("#m_select2_2_modal").val();
  var contact_id =  $("#contact_id_num").val();
  var price_id =  $("#price_id_num").val();


  var selected = '';
  var selected_price = '';
  if(company_id) {
    $('select[name="contact_id"]').empty();
    $('select[name="contact_id"]').prop("disabled",false);

    $.ajax({
      url: "/quotes/company/contact/id/"+company_id,
      dataType: 'json',
      success: function(data) {
        $('select[name="client"]').empty();
        $.each(data, function(key, value) {
          if(key == contact_id){
            selected = 'selected';
          }else{
            selected = '';
          }

          $('select[name="contact_id"]').append('<option '+selected+' value="'+ key +'">'+ value +'</option>');
        });
      }
    });

    $.ajax({
      url: "/quotes/company/price/id/"+company_id,
      dataType: 'json',
      success: function(data) {


        $('select[name="price_id"]').empty();
        $.each(data, function(key, value) {
          if(key == price_id){
            selected_price = 'selected';
          }else{
            selected_price = '';
          }
          $('select[name="price_id"]').append('<option '+selected_price+' value="0">Select an option</option>');
          $('select[name="price_id"]').append('<option '+selected_price+' value="'+ key +'">'+ value +'</option>');
        });
      }
    });
  }
}
// SEARCH 

$(document).on('change', '#quoteType', function (e) {


  if($(this).val()==1){
    $(".quote_search").show();
    $(".formu").val('');
    $(".search").hide();

    $("#origin_harbor").prop( "disabled", false );
    $("#destination_harbor").prop( "disabled", false );
    $("#equipment_id").show();
    $("#equipment").prop( "disabled", false );

    $("#delivery_type").prop( "disabled", false );
    $("#delivery_type_air").prop( "disabled", true );
    $("#delivery_type_label").show();
    $("#delivery_type_air_label").hide();
    $("#fcl_load").show();
    $("#origin_harbor_label").show();
    $("#destination_harbor_label").show();
    $("#airline_label").hide();
    $("#carrier_label").show();
    $("#airline_id").prop( "disabled", true );
    $("#carrier_id").prop( "disabled", false );
    $("#lcl_air_load").hide();
    $("#origin_airport_label").hide();
    $("#destination_airport_label").hide();
    $("input[name=total_quantity]").val('');
    $("input[name=total_weight]").val('');
    $("input[name=total_volume]").val('');
    $('#lcl_air_load').find('.quantity').val('');
    $('#lcl_air_load').find('.height').val('');
    $('#lcl_air_load').find('.width').val('');
    $('#lcl_air_load').find('.large').val('');
    $('#lcl_air_load').find('.weight').val('');
    $('#lcl_air_load').find('.volume').val('');
  }

  if($(this).val()==2){
    $(".quote_search").hide();
    $(".formu").val('');
    $(".search").hide();

    $("#origin_harbor").prop( "disabled", false );
    $("#destination_harbor").prop( "disabled", false );
    $("#equipment_id").hide();
    $("#equipment").prop( "disabled", true );
    $("#delivery_type").prop( "disabled", false );
    $("#delivery_type_air").prop( "disabled", true );
    $("#delivery_type_label").show();
    $("#delivery_type_air_label").hide();
    $("#lcl_air_load").show();
    $("#origin_harbor_label").show();
    $("#destination_harbor_label").show();
    $("#airline_label").hide();
    $("#carrier_label").show();
    $("#airline_id").prop( "disabled", true );
    $("#carrier_id").prop( "disabled", false );
    $("#fcl_load").hide();
    $("#origin_airport_label").hide();
    $("#destination_airport_label").hide();
    $("input[name=qty_20]").val('');
    $("input[name=qty_40]").val('');
    $("input[name=qty_40_hc]").val('');
    $("input[name=qty_45_hc]").val('');
    var chargeable_weight=0;
    var volume=0;
    var total_volume=0;
    var total_weight=0;
    var weight=sum;
    var sum = 0;
    var sum_vol = 0;

    if(($('#total_volume').val()!='' && $('#total_volume').val()>0) && ($('#total_weight').val()!='' && $('#total_weight').val()>0)){
      total_volume=$('#total_volume').val();
      weight=$('#total_weight').val();

      if($('#quoteType').val()==2){


        total_weight=weight/1000;
        if(total_volume>total_weight){
          chargeable_weight=total_volume;
        }else{
          chargeable_weight=total_weight;
        }
        $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
      }else if($('#quoteType').val()==3){
        total_volume=total_volume*166;
        if(total_volume>weight){
          chargeable_weight=total_volume;
        }else{
          chargeable_weight=weight;
        }
        $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" kg");
      }

      $("#chargeable_weight_pkg_input").val(chargeable_weight);
    }else{
      if(($('#total_volume_pkg_input').val()!='' && $('#total_volume_pkg_input').val()>0) && ($('#total_weight_pkg_input').val()!='' && $('#total_weight_pkg_input').val()>0)) {

        sum_vol = $('#total_volume_pkg_input').val();
        weight = $('#total_weight_pkg_input').val()/1000;

        total_vol_chargeable = sum_vol;
        if (total_vol_chargeable > weight) {
          chargeable_weight = total_vol_chargeable;
        } else {
          chargeable_weight = weight;
        }

      }

      $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
      $("#chargeable_weight_pkg_input").val(chargeable_weight);
    }

  }

  if($(this).val()==3){
    $(".quote_search").hide();
    $(".formu").val('');
    $(".search").hide();


    $("#origin_harbor").prop( "disabled", true );
    $("#destination_harbor").prop( "disabled", true );
    $("#equipment_id").hide();
    $("#equipment").prop( "disabled", true );
    $("#delivery_type").prop( "disabled", true );
    $("#delivery_type_air").prop( "disabled", false );
    $("#delivery_type_label").hide();
    $("#delivery_type_air_label").show();
    $("#lcl_air_load").show();
    $("#origin_airport_label").show();
    $("#destination_airport_label").show();
    $("#airline_label").show();
    $("#carrier_label").hide();
    $("#airline_id").prop( "disabled", false );
    $("#carrier_id").prop( "disabled", true );
    $("#fcl_load").hide();
    $("#origin_harbor_label").hide();
    $("#destination_harbor_label").hide();
    $("input[name=qty_20]").val('');
    $("input[name=qty_40]").val('');
    $("input[name=qty_40_hc]").val('');
    $("input[name=qty_45_hc]").val('');
    var chargeable_weight=0;
    var volume=0;
    var total_volume=0;
    var total_weight=0;
    var weight=sum;
    var sum = 0;
    var sum_vol = 0;

    if(($('#total_volume').val()!='' && $('#total_volume').val()>0) && ($('#total_weight').val()!='' && $('#total_weight').val()>0)){
      total_volume=$('#total_volume').val();
      total_weight=$('#total_weight').val();
      if($('#quoteType').val()==2){
        total_weight=total_weight/1000;
        if(total_volume>total_weight){
          chargeable_weight=total_volume;
        }else{
          chargeable_weight=total_weight;
        }
        $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
      }else if($('#quoteType').val()==3){
        total_volume=total_volume*166;
        if(total_volume>total_weight){
          chargeable_weight=total_volume;
        }else{
          chargeable_weight=total_weight;
        }
        $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" kg");
      }

      $("#chargeable_weight_pkg_input").val(chargeable_weight);
    }else{
      if(($('#total_volume_pkg_input').val()!='' && $('#total_volume_pkg_input').val()>0) && ($('#total_weight_pkg_input').val()!='' && $('#total_weight_pkg_input').val()>0)) {

        sum_vol = $('#total_volume_pkg_input').val();
        weight = $('#total_weight_pkg_input').val();

        total_vol_chargeable = sum_vol * 166;
        if (total_vol_chargeable > weight) {
          chargeable_weight = total_vol_chargeable;
        } else {
          chargeable_weight = weight;
        }
      }
      $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2)+" kg");
      $("#chargeable_weight_pkg_input").val(chargeable_weight);
    }
  }
});



$(document).on('change', '#delivery_type', function (e) {

  if($(this).val()==1){
    $("#origin_address_label").addClass('hide');
    $("#destination_address_label").addClass('hide');
    $("#origin_address").val('');
    $("#destination_address").val('');
  }
  if($(this).val()==2){

    $("#origin_address_label").addClass('hide');
    $("#destination_address_label").removeClass('hide');
    $("#origin_address").val('');
  }
  if($(this).val()==3){
    $("#origin_address_label").removeClass('hide');
    $("#destination_address_label").addClass('hide');
    $("#destination_address").val('');
  }
  if($(this).val()==4){
    $("#origin_address_label").removeClass('hide');
    $("#destination_address_label").removeClass('hide');
  }
});

$( document ).ready(function() {
  $('.select2-selection__rendered').removeAttr('title');
  $('#select2-price_id-container').text('Please an option');


  // CLEARING COMPANIES SELECT

  $( "select[name='company_id_quote']" ).on('change', function() {
    var company_id = $(this).val();
    $("#contact_id").val('');

    $('#select2-contact_id-container').text('Please an option');
    if(company_id) {
      $('select[name="contact_id"]').empty();
      $('select[name="contact_id"]').prop("disabled",false);

      $.ajax({
        url: "/quotes/company/contact/id/"+company_id,
        dataType: 'json',
        success: function(data) {
          $('select[name="contact_id"]').empty();
          $.each(data, function(key, value) {
            $('select[name="contact_id"]').append('<option value="'+ key +'">'+ value +'</option>');
          });
        }
      });

      $.ajax({
        url: "/quotes/company/price/id/"+company_id,
        dataType: 'json',
        success: function(data) {
          $('select[name="price_id"]').empty();
          $('select[name="price_id"]').append('<option value="0">Select an option</option>');
          $.each(data, function(key, value) {
            $('select[name="price_id"]').append('<option value="'+ key +'">'+ value +'</option>');
          });

          // CLEARING PRICE SELECT
          $("select[name='contact_id']").val('');
          $('#select2-contact_id-container').text('Please an option');

          $("select[name='price_id']").val('');

        }
      });
    }else{
      $('#select2-contact_id-container').text('Please an option');
      $('select[name="contact_id"]').empty();
      $('select[name="price_id"]').empty();
    }
  });
});

$('.m-select2-general').select2({
  placeholder: "Select an option"
});

function display(id){

  var freight = $("#freight"+id);
  var origin = $("#origin"+id);
  var destiny = $("#destiny"+id);
  var inland =  $("#inland"+id);
  var remark = $("#remark"+id);

  if(freight.attr('hidden')){
    $("#freight"+id).removeAttr('hidden');
    $("#remark"+id).attr('hidden','true');
  }else{
    $("#freight"+id).attr('hidden','true');
  }

  if(origin.attr('hidden')){
    $("#origin"+id).removeAttr('hidden');
  }else{
    $("#origin"+id).attr('hidden','true');
  }

  if(destiny.attr('hidden')){
    $("#destiny"+id).removeAttr('hidden');
  }else{
    $("#destiny"+id).attr('hidden','true');
  }
  if(inland.attr('hidden')){
    $("#inland"+id).removeAttr('hidden');
  }else{
    $("#inland"+id).attr('hidden','true');
  }
}

function display_r(id){

  var freight = $("#freight"+id);
  var origin = $("#origin"+id);
  var destiny = $("#destiny"+id);
  var inland =  $("#inland"+id);
  var remark = $("#remark"+id);
  if(remark.attr('hidden')){
    $("#remark"+id).removeAttr('hidden');
    $("#freight"+id).attr('hidden','true');
    $("#origin"+id).attr('hidden','true');
    $("#destiny"+id).attr('hidden','true');
    $("#inland"+id).attr('hidden','true');
  }else{
    $("#remark"+id).attr('hidden','true');
  }

}


$(".quote_search").on("click", function() {
  $("#airline_id").attr( "required", false );
  $("#carrieManual").prop( "required", false );
  $('#FormQuote').attr('action', '/v2/quotes/processSearch');
  $(".quote_search").attr("type","submit");

});



$(".quote_man").on("click", function() {



  $('#FormQuote').attr('action', '/v2/quotes/store');
  if($('#quoteType').val()==1){
    $("#carrieManual").attr( "required", true );
  }
  if($('#quoteType').val()==2){
    $("#carrieManual").attr( "required", true );
    if($("#total_quantity_pkg_input").val()>0){
      $("#total_quantity").val($("#total_quantity_pkg_input").val());
    }
    if($("#total_weight_pkg_input").val()>0){
      $("#total_weight").val($("#total_weight_pkg_input").val());
    }
    if($("#total_volume_pkg_input").val()>0){
      $("#total_volume").val($("#total_volume_pkg_input").val());
    }

  }

  if($('#quoteType').val()==3){
    $("#airline_id").attr( "required", true );
    $("#carrieManual").prop( "required", false );

  }


  $(".quote_man").attr("type","submit");
});

function calcularInlands(tipo,idRate){


  if(tipo == 'destino'){
    var  i20= $("#valor-d201-"+idRate).html();
    var  i40= $("#valor-d401-"+idRate).html();
    var  i40h= $("#valor-d40h1-"+idRate).html();


  }else{

    var  i20= $("#valor-o201-"+idRate).html();
    var  i40= $("#valor-o401-"+idRate).html();
    var  i40h= $("#valor-o40h1-"+idRate).html();
  }

  var  sub20d= $("#sub_inland_20_d"+idRate);
  var  sub40d= $("#sub_inland_40_d"+idRate);
  var  sub40hd= $("#sub_inland_40h_d"+idRate);

  var  sub20o= $("#sub_inland_20_o"+idRate);
  var  sub40o= $("#sub_inland_40_o"+idRate);
  var  sub40ho= $("#sub_inland_40h_o"+idRate);

  var  sub20= $("#sub_inland_20"+idRate).html();
  var  sub40= $("#sub_inland_40"+idRate).html();
  var  sub40h= $("#sub_inland_40h"+idRate).html();

  if(tipo == 'destino'){

    sub20d.val(parseFloat(i20));
    sub40d.val(parseFloat(i40));
    sub40hd.val(parseFloat(i40h));

  }else{

    sub20o.val(parseFloat(i20));
    sub40o.val(parseFloat(i40));
    sub40ho.val(parseFloat(i40h));

  }

  sub20 = parseFloat(sub20o.val()) +  parseFloat(sub20d.val());
  sub40 = parseFloat(sub40o.val()) + parseFloat(sub40d.val());
  sub40h = parseFloat(sub40ho.val()) + parseFloat(sub40hd.val());



  $("#sub_inland_20"+idRate).html(sub20);
  $("#sub_inland_40"+idRate).html(sub40);
  $("#sub_inland_40h"+idRate).html(sub40h);


}

$('.btn-input__select').on('click', function(){

  var idRate = $(this).attr('rate-id');  
  $cantidadDestino = $('.labelDest'+idRate).length;
  $cantidadOrigen = $('.labelOrig'+idRate).length;


  $('.labelSelectDest'+idRate).toggleClass('hidden-general');
  $('.labelOrig'+idRate).toggleClass('visible__select-add');
  $('.labelDest'+idRate).toggleClass('visible__select-add');
  if($cantidadDestino == 1){
    $('.labelDest'+idRate).addClass('style__select-add');
    $('#inputID-select1-'+idRate).attr('checked',true);

    calcularInlands('destino',idRate);

  }

  if($cantidadOrigen == 1){

    $('.labelOrig'+idRate).addClass('style__select-add');
    $('#inputIO-select1-'+idRate).attr('checked',true);
    calcularInlands('origen',idRate);
  }

});
$('.btn-input__select-add').on('click', function(){
  $(this).toggleClass('style__select-add');
});

$('.input-select').on('click', function(){
  var ident = $(this).attr('id');
  $('.'+ident+'').toggleClass('border-card');
});


$('.inlands').on('click', function(){
  $('.card-p__quotes').toggleClass('border-card-p');
  var id = $(this).attr('data-inland');
  var idRate = $(this).attr('data-rate');



  var theElement = $(this);
  $('.labelDest'+idRate).removeClass('style__select-add');
  if(theElement.prop('checked')){

    $('.labelI'+idRate+'-'+id).addClass('style__select-add');
    var group = "input:checkbox[name='" + theElement.attr("name") + "']";
    $(group).prop("checked", false);
    theElement.prop("checked", true);
  } else {

    theElement.prop("checked", false);
  }





  var  i20= $("#valor-d20"+id+"-"+idRate).html();
  var  i40= $("#valor-d40"+id+"-"+idRate).html();
  var  i40h= $("#valor-d40h"+id+"-"+idRate).html();


  var  sub20o= $("#sub_inland_20_o"+idRate);
  var  sub40o= $("#sub_inland_40_o"+idRate);
  var  sub40ho= $("#sub_inland_40h_o"+idRate);

  var  sub20d= $("#sub_inland_20_d"+idRate);
  var  sub40d= $("#sub_inland_40_d"+idRate);
  var  sub40hd= $("#sub_inland_40h_d"+idRate);


  var  sub20= $("#sub_inland_20"+idRate).html();
  var  sub40= $("#sub_inland_40"+idRate).html();
  var  sub40h= $("#sub_inland_40h"+idRate).html();
  if(theElement.prop('checked')){

    sub20d.val(parseFloat(i20));
    sub40d.val(parseFloat(i40));
    sub40hd.val(parseFloat(i40h));

    sub20 = parseFloat(sub20o.val()) +  parseFloat(sub20d.val());
    sub40 = parseFloat(sub40o.val()) + parseFloat(sub40d.val());
    sub40h = parseFloat(sub40ho.val()) + parseFloat(sub40hd.val());


  }else{

    sub20d.val(0.00);
    sub40d.val(0.00);
    sub40hd.val(0.00);

    if(parseFloat(sub20o.val())  > parseFloat(sub20d.val()) )
      sub20 = parseFloat(sub20o.val())  - parseFloat(sub20d.val()) ;
    else
      sub20 = parseFloat(sub20d.val()) -  parseFloat(sub20o.val()) ;

    if( parseFloat(sub40o.val())  >  parseFloat(sub40d.val()))
      sub40 =parseFloat(sub40o.val())   - parseFloat(sub40d.val())  ;
    else
      sub40 =  parseFloat(sub40d.val())  - parseFloat(sub40o.val()) ;

    if(parseFloat(sub40ho.val()) > parseFloat(sub40hd.val()) )
      sub40h = parseFloat(sub40ho.val())   - parseFloat(sub40hd.val())  ;
    else
      sub40h =  parseFloat(sub40hd.val() -  parseFloat(sub40ho.val()) )  ;


  }

  $("#sub_inland_20"+idRate).html(sub20);
  $("#sub_inland_40"+idRate).html(sub40);
  $("#sub_inland_40h"+idRate).html(sub40h);

});

$('.inlandsO').on('click', function(){
  $('.card-p__quotes').toggleClass('border-card-p');
  var id = $(this).attr('data-inland');
  var idRate = $(this).attr('data-rate');

  var theElement = $(this);

  $('.labelOrig'+idRate).removeClass('style__select-add');

  if(theElement.prop('checked')){
    $('.labelO'+idRate+'-'+id).addClass('style__select-add');
    var group = "input:checkbox[name='" + theElement.attr("name") + "']";
    $(group).prop("checked", false);
    theElement.prop("checked", true);
  } else {
    theElement.prop("checked", false);
  }





  var  i20= $("#valor-o20"+id+"-"+idRate).html();
  var  i40= $("#valor-o40"+id+"-"+idRate).html();
  var  i40h= $("#valor-o40h"+id+"-"+idRate).html();

  var  sub20o= $("#sub_inland_20_o"+idRate);
  var  sub40o= $("#sub_inland_40_o"+idRate);
  var  sub40ho= $("#sub_inland_40h_o"+idRate);

  var  sub20d= $("#sub_inland_20_d"+idRate);
  var  sub40d= $("#sub_inland_40_d"+idRate);
  var  sub40hd= $("#sub_inland_40h_d"+idRate);


  var  sub20= $("#sub_inland_20"+idRate).html();
  var  sub40= $("#sub_inland_40"+idRate).html();
  var  sub40h= $("#sub_inland_40h"+idRate).html();
  if(theElement.prop('checked')){

    sub20o.val(parseFloat(i20));
    sub40o.val(parseFloat(i40));
    sub40ho.val(parseFloat(i40h));

    sub20 = parseFloat(sub20o.val()) + parseFloat(sub20d.val());
    sub40 =parseFloat(sub40o.val()) + parseFloat(sub40d.val());
    sub40h = parseFloat(sub40ho.val()) + parseFloat(sub40hd.val());


  }else{

    sub20o.val(0.00);
    sub40o.val(0.00);
    sub40ho.val(0.00);

    if(parseFloat(sub20o.val())  > parseFloat(sub20d.val()) )
      sub20 = parseFloat(sub20o.val())  - parseFloat(sub20d.val()) ;
    else
      sub20 = parseFloat(sub20d.val()) -  parseFloat(sub20o.val()) ;

    if( parseFloat(sub40o.val())  >  parseFloat(sub40d.val()))
      sub40 =parseFloat(sub40o.val())   - parseFloat(sub40d.val())  ;
    else
      sub40 =  parseFloat(sub40d.val())  - parseFloat(sub40o.val()) ;

    if(parseFloat(sub40ho.val()) > parseFloat(sub40hd.val()) )
      sub40h = parseFloat(sub40ho.val())   - parseFloat(sub40hd.val())  ;
    else
      sub40h =  parseFloat(sub40hd.val() -  parseFloat(sub40ho.val()) )  ;



  }
  $("#sub_inland_20"+idRate).html(sub20);
  $("#sub_inland_40"+idRate).html(sub40);
  $("#sub_inland_40h"+idRate).html(sub40h);

});




//Calcular el volumen individual
$(document).on("change keydown keyup", ".quantity, .height ,.width ,.large,.weight", function(){
  var sumAl = 0;
  var sumAn = 0;
  var sumLa = 0;
  var sumQ = 0;
  var result = 0;
  var width = 0;
  var length = 0;
  var thickness = 0;
  var quantity = 0;
  var weight = 0;
  var volume = 0;
  $( ".width" ).each(function() {
    $( this).each(function() {
      width = $(this).val();
      if (!isNaN(width)) {
        width = parseInt(width);
      }
    });
  });
  $( ".height" ).each(function() {
    $( this).each(function() {
      thickness = $(this).val();
      if (!isNaN(thickness)) {
        thickness = parseInt(thickness);
      }
    });
  });
  $( ".quantity" ).each(function() {
    $( this).each(function() {
      quantity = $(this).val();
      if (!isNaN(quantity)) {
        quantity = parseInt(quantity);
      }
    });
  });
  $( ".weight" ).each(function() {
    $(this).each(function() {
      weight = $(this).val();
      if (weight!='') {
        weight = parseFloat(weight);
      }
    });
  });

  $( ".large" ).each(function() {
    $( this).each(function() {
      length = $(this).val();
      if (!isNaN(length)) {
        length = parseInt(length);
      }
    });
    thickness = $(this).closest('.row').find('.height').val();
    length = $(this).closest('.row').find('.large').val();
    width = $(this).closest('.row').find('.width').val();
    quantity = $(this).closest('.row').find('.quantity').val();
    weight = $(this).closest('.row').find('.weight').val();

    if(thickness > 0 || length > 0 || quantity > 0) {
      volume = Math.round(thickness * length * width * quantity / 10000) / 100;
      if (isNaN(volume)) {
        volume = 0;
      }
    }
    if($( this).val()!=''){
      $(this).closest('.template').find('.volume').html(volume+" m<sup>3</sup>");
      $(this).closest('.template').find('.volume_input').val(volume);
    }
    $(this).closest('.template').find('.quantity').html(" "+quantity+" un");
    $(this).closest('.template').find('.weight').html(" "+weight*quantity+" kg");
    $(this).closest('.template').find('.quantity_input').val(quantity);
    $(this).closest('.template').find('.weight_input').val(weight*quantity);
    $(this).closest('.template').find('.volume_input').change();
    $(this).closest('.template').find('.quantity_input').change();
    $(this).closest('.template').find('.weight_input').change();
  });
});

$(document).on("change keydown keyup", ".quantity_input", function(){
  var sum = 0;
  //iterate through each textboxes and add the values
  $(".quantity_input").each(function() {
    //add only if the value is number
    if ($(this).val()>0 && $(this).val()!='') {
      sum += parseInt($(this).val());
    }
    else if ($(this).val().length != 0){
      $(this).css("background-color", "red");
    }
  });
  $("#total_quantity_pkg").html(sum + " un");
  $("#total_quantity_pkg_input").val(sum);
});

$(document).on("change keydown keyup", ".volume_input", function(){
  var sum = 0;
  //iterate through each textboxes and add the values
  $(".volume_input").each(function() {
    //add only if the value is number
    if ($(this).val()>0 && $(this).val()!='') {
      sum += parseFloat($(this).val());
    }
    else if ($(this).val().length != 0){
      $(this).css("background-color", "red");
    }
  });

  $("#total_volume_pkg").html((parseFloat(sum).toFixed(2)) + " m3");
  $("#total_volume_pkg_input").val(parseFloat(sum).toFixed(2));
});

$(document).on("change keydown keyup", ".weight_input", function(){
  var sum = 0;
  var sum_vol = 0;

  //iterate through each textboxes and add the values
  $(".weight_input").each(function() {
    //add only if the value is number
    if ($(this).val()>0 && $(this).val()!='') {
      sum += parseFloat($(this).val());
    }
  });
  $("#total_weight_pkg").html(sum + " kg");
  $("#total_weight_pkg_input").val(sum);

  $(".volume_input").each(function() {
    //add only if the value is number
    if ($(this).val()>0 && $(this).val()!='') {
      sum_vol += parseFloat($(this).val());
    }
    else if ($(this).val().length != 0){
      $(this).css("background-color", "red");
    }
  });
  var chargeable_weight= 0;
  var weight=sum;
  //Calculate chargeable weight
  if($('#quoteType').val()==2){
    total_vol_chargeable=sum_vol;
    total_weight=weight/1000;
    if(total_vol_chargeable>total_weight){
      chargeable_weight=total_vol_chargeable;
    }else{
      chargeable_weight=total_weight;
    }
    $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
  }else if($('#quoteType').val()==3){
    total_vol_chargeable=sum_vol*166;
    if(total_vol_chargeable>weight){
      chargeable_weight=total_vol_chargeable;
    }else{
      chargeable_weight=weight;
    }
    $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2)+" kg");
  }


  $("#chargeable_weight_pkg_input").val(chargeable_weight);
});

//Calculate chargeable weight by totals
$(document).on('change keyup keydown', '#total_volume, #total_weight', function () {
  var chargeable_weight=0;
  var volume=0;
  var total_volume=0;
  var total_weight=0;

  if(($('#total_volume').val()!='' && $('#total_volume').val()>0) && ($('#total_weight').val()!='' && $('#total_weight').val()>0)){

    total_volume=$('#total_volume').val();
    total_weight=$('#total_weight').val();
    if($("#quoteType").val()==2){

      total_weight=total_weight/1000;
      if(total_volume>total_weight){
        chargeable_weight=total_volume;
      }else{
        chargeable_weight=total_weight;
      }
      $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
    }else  if($("#quoteType").val()==3){

      total_volume=total_volume*166;
      if(total_volume>total_weight){
        chargeable_weight=total_volume;
      }else{
        chargeable_weight=total_weight;
      }
      $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" kg");
    }

    $("#chargeable_weight_pkg_input").val(chargeable_weight);
  }
});

function change_tab(tab){
  if(tab==2){
    $("#total_quantity").val('');
    $("#total_weight").val('');
    $("#total_volume").val('');
    $("#chargeable_weight_pkg_input").val('');
    $("#chargeable_weight_total").html('');

  }else{
    $('#lcl_air_load').find('.quantity').val('');
    $('#lcl_air_load').find('.height').val('');
    $('#lcl_air_load').find('.width').val('');
    $('#lcl_air_load').find('.large').val('');
    $('#lcl_air_load').find('.weight').val('');
    $('#lcl_air_load').find('.volume').val('');
    $("#chargeable_weight_pkg_input").val('');
    $("#chargeable_weight_pkg").html('');
  }
}

$(document).on('change', '#delivery_type_air', function (e) {

  if($(this).val()==5){
    $("#origin_address_label").addClass('hide');
    $("#destination_address_label").addClass('hide');
    $("#origin_address").val('');
    $("#destination_address").val('');
  }
  if($(this).val()==6){
    $("#origin_address_label").addClass('hide');
    $("#destination_address_label").removeClass('hide');
    $("#origin_address").val('');
  }
  if($(this).val()==7){
    $("#origin_address_label").removeClass('hide');
    $("#destination_address_label").addClass('hide');
    $("#destination_address").val('');
  }
  if($(this).val()==8){
    $("#origin_address_label").removeClass('hide');
    $("#destination_address_label").removeClass('hide');
  }
});

$('#origin_airport').select2({
  placeholder: "Select an option",
  minimumInputLength: 2,
  ajax: {
    url: '/quotes/airports/find',
    dataType: 'json',
    data: function (params) {
      return {
        q: $.trim(params.term)
      };
    },
    processResults: function (data) {
      return {
        results: data
      };
    },
  }
});

$('#destination_airport').select2({
  placeholder: "Select an option",
  minimumInputLength: 2,
  ajax: {
    url: '/quotes/airports/find',
    dataType: 'json',
    data: function (params) {
      return {
        q: $.trim(params.term)
      };
    },
    processResults: function (data) {
      return {
        results: data
      };
    },
  }
});

$(document).on('click', '#add_load_lcl_air', function (e) {
  var $template = $('#lcl_air_load_template'),
      $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertBefore($template);
});

$(document).on('click', '.remove_lcl_air_load', function (e) {
  var $row = $(this).closest('.template').remove();
  $row.remove();

  $('.quantity').change();
  $('.height').change();
  $('.width').change();
  $('.large').change();
  $('.weight').change();
});