$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

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
      'port' : $("#port"+id).val(),
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

      $("#divtype"+id).html($("#type"+id+" option:selected").text());
      $("#divport"+id).html($("#port"+id+" option:selected").text());
      $("#divchangetype"+id).html($("#changetype"+id+" option:selected").text());
      $("#divcarrier"+id).html($("#localcarrier"+id+" option:selected").text());
      $("#divcalculation"+id).html($("#calculationtype"+id+" option:selected").text());
      $("#divammount"+id).html($("#ammount"+id).val());
      $("#divcurrency"+id).html($("#localcurrency"+id+" option:selected").text());

    }
  });

}

$("#new").on("click", function() {

  var $template = $('#tclone');
  $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
  $myClone.find("select").select2();
  $("#sample_editable_2").append($myClone);
  // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
  // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$("#newL").on("click", function() {

  var $template = $('#tclone2');

  $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
  $myClone.find("select").select2();
  $("#sample_editable_1").append($myClone);


});

//Surcharges

$(document).on('click', '#delete-surcharge', function () {
  var id = $(this).attr('data-surcharge-id');

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
        url: 'surcharges/delete/' + id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )
            $(theElement).closest('tr').remove();
          }else{
            swal(
              'Error!',
              'Your can\'t delete this surcharge because have sale terms related.',
              'warning'
            )
            console.log(data.message);
          }
        }
      });
    }
  });
});

$(document).on('click', '.delete-inlandl', function () {
  var id = $(this).attr('data-inlandl-id');

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
        url: 'inlandL/delete/' + id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )
            $(theElement).closest('tr').remove();
          }else{
            swal(
              'Error!',
              'Your can\'t delete this surcharge because have sale terms related.',
              'warning'
            )
            console.log(data.message);
          }
        }
      });
    }
  });
});

$(document).on('click', '.delete-inlandd', function () {
  var id = $(this).attr('data-inlandd-id');

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
        url: 'inlandD/delete/' + id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )
            $(theElement).closest('tr').remove();
          }else{
            swal(
              'Error!',
              'Your can\'t delete this surcharge because have sale terms related.',
              'warning'
            )
            console.log(data.message);
          }
        }
      });
    }
  });
});

//Contacts

$(document).on('click', '.remove', function () {
  $(this).closest('tr').remove();
});
$(document).on('click', '#delete-contact', function () {
  var id = $(this).attr('data-contact-id');
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
        url: '/contacts/delete/' + id,
        success: function(data) {
          swal(
            'Deleted!',
            'The contact has been deleted.',
            'success'
          )
          $(theElement).closest('ul').remove();
        }
      });

    }

  });
});

//Owners

$(document).on('click', '#delete-owner', function () {
  var id = $(this).attr('data-owner-id');
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
        url: '/companies/owner/delete/' + id,
        success: function(data) {
          swal(
            'Deleted!',
            'The owner has been deleted.',
            'success'
          )
          $(theElement).closest('li').remove();
        }
      });

    }

  });
});

//Prices

$(document).on('change', '#type_freight_markup_1', function (e) {
  if($(this).val()==1){
    $(".freight_fixed_markup_1").hide();
    $("#freight_fixed_markup_1").val(0);
    $(".freight_percent_markup_1").show();
  }else{
    $(".freight_fixed_markup_1").show();
    $(".freight_percent_markup_1").hide();
    $("#freight_percent_markup_1").val(0);
  }
});

$(document).on('change', '#type_freight_markup_2', function (e) {
  if($(this).val()==1){
    $(".freight_fixed_markup_2").hide();
    $("#freight_fixed_markup_2").val(0);
    $(".freight_percent_markup_2").show();
  }else{
    $(".freight_fixed_markup_2").show();
    $(".freight_percent_markup_2").hide();
    $("#freight_percent_markup_2").val(0);
  }
});

$(document).on('change', '#type_freight_markup_3', function (e) {
  if($(this).val()==1){
    $(".freight_fixed_markup_3").hide();
    $("#freight_fixed_markup_3").val(0);
    $(".freight_percent_markup_3").show();
  }else{
    $(".freight_fixed_markup_3").show();
    $(".freight_percent_markup_3").hide();
    $("#freight_percent_markup_3").val(0);
  }
});

$(document).on('change', '#type_local_markup_1', function (e) {
  if($(this).val()==1){
    $(".local_fixed_markup_1").hide();
    $(".local_fixed_markup_1_2").hide();
    $("#local_fixed_markup_1").val(0);
    $("#local_fixed_markup_1_2").val(0);
    $(".local_percent_markup_1").show();
    $(".local_percent_markup_1_2").show();
  }else{
    $(".local_fixed_markup_1").show();
    $(".local_fixed_markup_1_2").show();
    $(".local_percent_markup_1").hide();
    $(".local_percent_markup_1_2").hide();
    $("#local_percent_markup_1").val(0);
    $("#local_percent_markup_1_2").val(0);
  }
});

$(document).on('change', '#type_inland_markup_1', function (e) {
  if($(this).val()==1){
    $(".inland_fixed_markup_1").hide();
    $(".inland_fixed_markup_1_2").hide();
    $(".inland_percent_markup_1").show();
    $(".inland_percent_markup_1_2").show();
    $("#inland_fixed_markup_1").val(0);
    $("#inland_fixed_markup_1_2").val(0);
  }else{
    $(".inland_fixed_markup_1").show();
    $(".inland_fixed_markup_1_2").show();
    $(".inland_percent_markup_1").hide();
    $(".inland_percent_markup_1_2").hide();
    $("#inland_percent_markup_1").val(0);
    $("#inland_percent_markup_1_2").val(0);
  }
});

$(document).on('change', '#type_local_markup_2', function (e) {
  if($(this).val()==1){
    $(".local_fixed_markup_2").hide();
    $(".local_fixed_markup_2_2").hide();
    $(".local_percent_markup_2").show();
    $(".local_percent_markup_2_2").show();
    $("#local_fixed_markup_2").val('0');
    $("#local_fixed_markup_2_2").val('0');
  }else{
    $(".local_fixed_markup_2").show();
    $(".local_fixed_markup_2_2").show();
    $(".local_percent_markup_2").hide();
    $(".local_percent_markup_2_2").hide();
    $("#local_percent_markup_2").val('0');
    $("#local_percent_markup_2_2").val('0');
  }
});

$(document).on('change', '#type_inland_markup_2', function (e) {
  if($(this).val()==1){
    $(".inland_fixed_markup_2").hide();
    $(".inland_fixed_markup_2_2").hide();
    $(".inland_percent_markup_2").show();
    $(".inland_percent_markup_2_2").show();
    $("#inland_fixed_markup_2").val(0);
    $("#inland_fixed_markup_2_2").val(0);
  }else{
    $(".inland_fixed_markup_2").show();
    $(".inland_fixed_markup_2_2").show();
    $(".inland_percent_markup_2").hide();
    $(".inland_percent_markup_2_2").hide();
    $("#inland_percent_markup_2").val(0);
    $("#inland_percent_markup_2_2").val(0);
  }
});

$(document).on('change', '#type_local_markup_2', function (e) {
  if($(this).val()==1){
    $(".local_fixed_markup_2").hide();
    $(".local_fixed_markup_2_2").hide();
    $(".local_percent_markup_2").show();
    $(".local_percent_markup_2_2").show();
    $("#local_fixed_markup_2").val('0');
    $("#local_fixed_markup_2_2").val('0');
  }else{
    $(".local_fixed_markup_2").show();
    $(".local_fixed_markup_2_2").show();
    $(".local_percent_markup_2").hide();
    $(".local_percent_markup_2_2").hide();
    $("#local_percent_markup_2").val('0');
    $("#local_percent_markup_2_2").val('0');
  }
});

$(document).on('change', '#type_local_markup_3', function (e) {
  if($(this).val()==1){
    $(".local_fixed_markup_3").hide();
    $(".local_fixed_markup_3_2").hide();
    $(".local_percent_markup_3").show();
    $(".local_percent_markup_3_2").show();
    $("#local_fixed_markup_3").val('0');
    $("#local_fixed_markup_3_2").val('0');
  }else{
    $(".local_fixed_markup_3").show();
    $(".local_fixed_markup_3_2").show();
    $(".local_percent_markup_3").hide();
    $(".local_percent_markup_3_2").hide();
    $("#local_percent_markup_3").val('0');
    $("#local_percent_markup_3_2").val('0');
  }
});



$(document).on('change', '#type_inland_markup_3', function (e) {
  if($(this).val()==1){
    $(".inland_fixed_markup_3").hide();
    $(".inland_fixed_markup_3_2").hide();
    $(".inland_percent_markup_3").show();
    $(".inland_percent_markup_3_2").show();
    $("#inland_fixed_markup_3").val(0);
    $("#inland_fixed_markup_3_2").val(0);
  }else{
    $(".inland_fixed_markup_3").show();
    $(".inland_fixed_markup_3_2").show();
    $(".inland_percent_markup_3").hide();
    $(".inland_percent_markup_3_2").hide();
    $("#inland_percent_markup_3").val(0);
    $("#inland_percent_markup_3_2").val(0);
  }
});

/** EMAIL TEMPLATES **/

//Select email template to send quote
$(document).on('change', '#email_template', function () {
  var ed;
  var id = $('#email_template').val();
  var data = $('#emaildimanicdata').val();
  if(id==''){
    $('#subject-box').html('');
    $('#textarea-box').hide();
    $('.editor').html('');
  }else{
    $.ajax({
      type: 'GET',
      url: '/templates/preview',
      data:{"id":id,data:data},
      success: function(data) {
        $('#subject-box').html('<b>Subject:</b> </br></br><input type="text" name="subject" id="email-subject" class="form-control" value="'+data.subject+'"/><hr>');
        $('#textarea-box').show();

        ed = data.message;
        tinymce.init({
          selector: "#email-body",
          plugins: [
            "advlist autolink lists link charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime nonbreaking save table contextmenu directionality",
            "emoticons paste textcolor colorpicker textpattern codesample",
            "fullpage toc imagetools help"
          ],
          toolbar1: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
          menubar: false,
          toolbar_items_size: 'small',
          paste_as_text: true,
          browser_spellcheck: true,
          statusbar: false,
          height: 200,

          style_formats: [{
            title: 'Bold text',
            inline: 'b'
          }, ],

        });
        $('.editor').html(data.message).tinymce({
          theme: "modern",
        });

      }
    });
  }
  $('#email-body').html(ed).tinymce({
    theme: "modern",
  });
});

$(document).on('click', '#show_email_templates', function () {
  $('#email_templates_box').show();
});

//Select2 email template in quotes
$('#email_templte').select2({
  placeholder: "Select an option"
});

/** CLIENTS **/

$(document).on('click', '#delete-contact', function () {
  var id = $(this).attr('data-contact-id');
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
        url: '/contacts/delete/' + id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )
            $(theElement).closest('tr').remove();
          }else{
            swal(
              'Error!',
              'Your can\'t delete this contact because have quotes related.',
              'warning'
            )
            console.log(data.message);
          }
        }
      });

    }

  });
});

/** COMPANIES **/

$(document).on('click', '#delete-company', function () {
  var id = $(this).attr('data-company-id');
  var theElement = $(this);
  swal({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Continue!'
  }).then(function(result) {
    if (result.value) {
      $.ajax({
        type: 'get',
        url: '/companies/delete/' + id,
        success: function(data) {
          if(data.message>0){
            swal({
              title: 'Warning!',
              text: "There are "+data.message+" clients associated with this company. If you delete it, those contacts will be deleted.",
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
              if (result.value) {
                $.ajax({
                  type: 'get',
                  url: '/companies/destroy/' + id,
                  success: function(data) {
                    if(data.message=='Ok'){
                      swal(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      )
                      $(theElement).closest('tr').remove();
                    }else{
                      swal(
                        'Error!',
                        'This company has quotes associated. You can\'t deleted companies with quotes associated.',
                        'error'
                      )
                      console.log(data.message);
                    }
                  }
                });
              }
            });
          }else{
            $.ajax({
              type: 'get',
              url: '/companies/destroy/' + id,
              success: function(data) {
                if(data.message=='Ok'){
                  swal(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  )
                  $(theElement).closest('tr').remove();
                }else{
                  swal(
                    'Error!',
                    'This company has quotes associated. You can\'t deleted companies with quotes associated.',
                    'warning'
                  )
                  console.log(data.message);
                }
              }
            });
          }
        }
      });
    }
  });
});

$(document).on('click', '#delete-company-show', function () {
  var id = $(this).attr('data-company-id');
  var theElement = $(this);
  swal({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Continue!'
  }).then(function(result) {
    if (result.value) {
      $.ajax({
        type: 'get',
        url: '/companies/delete/' + id,
        success: function(data) {
          if(data.message>0){
            swal({
              title: 'Warning!',
              text: "There are "+data.message+" clients associated with this company. If you delete it, those contacts will be deleted.",
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
              if (result.value) {
                $.ajax({
                  type: 'get',
                  url: '/companies/destroy/' + id,
                  success: function(data) {
                    if(data.message=='Ok'){
                      swal(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      )
                      $(theElement).closest('tr').remove();
                    }else{
                      swal(
                        'Error!',
                        'This company has quotes associated. You can\'t deleted companies with quotes associated.',
                        'error'
                      )
                      console.log(data.message);
                    }
                  }
                });
              }
            });
          }else{
            $.ajax({
              type: 'get',
              url: '/companies/destroy/' + id,
              success: function(data) {
                if(data.message=='Ok'){
                  swal(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                  )
                  window.location.href = '/companies';
                }else{
                  swal(
                    'Error!',
                    'This company has quotes associated. You can\'t deleted companies with quotes associated.',
                    'warning'
                  )
                  console.log(data.message);
                }
              }
            });
          }
        }
      });
    }
  });
});

$(document).on('click', '#delete-company-user', function () {
  var id = $(this).attr('data-company-id');
  var theElement = $(this);
  swal({
    title: 'Are you sure?',
    text: "This action will delete all data associated to this company. You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Continue!'
  }).then(function(result) {
    if (result.value) {
      $.ajax({
        type: 'get',
        url: 'delete/company/' + id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Deleted!',
              'The company and all associated data has been deleted.',
              'success'
            )
            $(theElement).closest('tr').remove();
          }
        }
      });
    }
  });
});

// Pricing
$(document).on('click', '#delete-pricing', function () {
  var id = $(this).attr('data-pricing-id');
  var theElement = $(this);
  swal({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Continue!'
  }).then(function(result) {
    if (result.value) {
      $.ajax({
        type: 'get',
        url: 'prices/destroy/' + id,
        success: function(data) {
          if(data.message == "fail"){
            swal({
              title: 'Warning!',
              text: "There are  quotes assoociated with this pricing.",
              type: 'warning',
              showCancelButton: true,
              confirmButtonText: 'I understand'
            });
          }else if(data.message == "Ok"){

            swal(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )
            $(theElement).closest('tr').remove();

          }

        },
        error: function (request, status, error) {
          alert(request.responseText);
        }

      });
    }
  });
});

//SaleTerms

$(document).on('click', '#delete-saleterm', function () {
  var id = $(this).attr('data-saleterm-id');
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
        url: 'saleterms/delete/' + id,
        success: function(data) {
          if(data.message=='Ok'){
            swal(
              'Deleted!',
              'Your file has been deleted.',
              'success'
            )
            $(theElement).closest('tr').remove();
          }else{
            swal(
              'Error!',
              'Your can\'t delete this contact because have quotes related.',
              'warning'
            )
            console.log(data.message);
          }
        }
      });
    }
  });
});

$('#m_select2-edit-company').select2({
  placeholder: "Select an option"
});

$('#price_level_company').select2({
  placeholder: "Select an option"
});
$('#users_company').select2({
  placeholder: "Select an option"
});
$('#users_company_2').select2({
  placeholder: "Select an option"
});


// companies

$(document).on('click', '#savecompany', function () {

  var $element = $('#addContactModal');
  $.ajax({
    type: 'POST',
    url: '/companies',
    data: {
      'business_name' : $('.business_name_input').val(),
      'phone' : $('.phone_input').val(),
      'address' : $('.address_input').val(),
      'email' : $('.email_input').val(),

    },
    success: function(data) {
      $.ajax({
        url: "company/companies",
        dataType: 'json',
        success: function(dataC) {
          $('select[name="company_id_quote"]').empty();
          $.each(dataC, function(key, value) {
            $('select[name="company_id_quote"]').append('<option value="'+ key +'">'+ value +'</option>');
          });
          $('select[name="company_id"]').empty();
          $.each(dataC, function(key, value) {
            $('select[name="company_id"]').append('<option value="'+ key +'">'+ value +'</option>');
          });
          $('#companyModal').modal('hide');
          $("select[name='company_id_quote']").val('');
          $("select[name='company_id']").val('');
          $('#select2-m_select2_2_modal-container').text('Please an option');

          swal(
            'Done!',
            'Register completed',
            'success'
          )
        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
      });
    },
    error: function (request, status, error) {
      alert(request.responseText);
    }
  });
});



$(document).on('click', '#savecontact', function () {

  var $element = $('#contactModal');

  $.ajax({
    type: 'POST',
    url: '/contacts',
    data: {
      'first_name' : $('.first_namec_input').val(),
      'last_name' : $('.last_namec_input').val(),
      'email' : $('.emailc_input').val(),
      'phone' : $('.phonec_input').val(),
      'company_id' : $('.companyc_input').val(),

    },
    success: function(data) {
      var company_id = $("select[name='company_id_quote']").val();
      $.ajax({
        url: "contacts/contact/"+company_id,
        dataType: 'json',
        success: function(dataC) {
          $('select[name="contact_id"]').empty();
          $.each(dataC, function(key, value) {
            $('select[name="contact_id"]').append('<option value="'+ key +'">'+ value +'</option>');
          });
          $('#contactModal').modal('hide');

          swal(
            'Done!',
            'Register completed',
            'success'
          )
        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
      });
    },
    error: function (request, status, error) {
      alert(request.responseText);
    }
  });
});

/** SELECT2 **/

$('#sale_term_id').select2({
  placeholder: "Select an option"
});

$('#airline_id').select2({
  placeholder: "Select an option"
});

$('#carrier_id').select2({
  placeholder: "Select an option"
});

$('.m-select2-general').select2({
  placeholder: "Select an option"
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

$('.select2-company_id').select2({
  placeholder: "Select an option"
});

/** SCHEDULES **/

$(document).on('click', '#select-schedule', function () {

  var schevalues = new Array();
  var n = jQuery(".sche:checked").length;
  if (n > 0){
    jQuery(".sche:checked").each(function(){
      $valor =  $(this).val();
      var $obj = jQuery.parseJSON($valor);
      $('#schetable > tbody:last-child').append("<tr><td>"+$obj['vessel']+"</td><td>"+$obj['etd']+"</td><td> <div class='col-md-4 offset-md-4'> "+$obj['days']+" Days<div class='progress m-progress--sm'> <div class='progress-bar bg-success' role='progressbar' style='width: 100%;' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'></div> </div> "+$obj['type']+"</div></td><td>"+$obj['eta']+"</td></tr>");

      schevalues.push($valor);
    });


    //  alert(schevalues);
    $("#infoschedule").removeAttr('hidden');
    $(".removesche").removeAttr('hidden');
    $("#schedule").val(schevalues);
  }

});

$(document).on('click', '.removesche', function () {
  $("#infoschedule").attr('hidden','true');
  $(".removesche").attr('hidden','true');
  $("#scheduleBody").text('');
  $("#schedule").val('');
});

$(document).on('click', '#filter_data', function () {
  $.ajax({
    type: 'POST',
    url: '/dashboard/filter/',
    data: {
      'user': $("#user").val(),
      'pick_up_date': $("#m_daterangepicker_1").val(),
    },
    success: function(data) {
      alert(data);
    }
  });
});


/** FUNCTIONS **/

function msg(message){

  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-bottom-center",
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
  toastr.error(message,'IMPORTANT MESSAGE!');
}

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
