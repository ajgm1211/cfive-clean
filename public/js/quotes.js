$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});


$('#price_id').select2({
  placeholder: "Select an lala",
  enable : false
});

$(document).on('click', '#default-currency-submit', function () {
  var id = $('#company_id').val();
  var form = $('#default-currency');
  //event.preventDefault();
  if($('#company_id').val()!=''&&$('#name').val()!=''&&$('#phone').val()&&$('#address').val()) {
    swal({
      title: 'Are you sure?',
      text: "Please confirm!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, I am sure!'
    }).then(function (result) {
      if (result.value) {

        // Create an FormData object
        //var data = new FormData(form);
        var data = new FormData($("#default-currency")[0]);

        // disabled the submit button
        $("#default-currency-submit").prop("disabled", true);

        $.ajax({
          type: 'POST',
          enctype: 'multipart/form-data',
          url: '/settings/store/profile/company',
          data: data,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.message == 'Ok') {
              swal(
                'Done!',
                'Your choice has been saved.',
                'success'
              )
              window.location.href = "/";
            }
            $("#default-currency-submit").prop("disabled", false);
          }
        });

      }

    });
  }else{
    swal({
      title: 'There are empty fields',
      text: "",
      type: 'error',
      showCancelButton: false,
    })
  }
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



/********
 Quotes
 ********/



//Btn next
$(document).on('click', '#create-quote-back', function (e) {
  $(this).hide();
  $("#create-quote").show();
});


$(document).on('change', '#modality', function (e) {
  var origin_harbor_id = $('#origin_harbor').val();
  var destination_harbor_id = $('#destination_harbor').val();
  var modality = $('#modality').val();
  if(origin_harbor_id!='' && destination_harbor_id!=''){
    $.ajax({
      url: "/quotes/terms/"+origin_harbor_id+"/"+destination_harbor_id,
      dataType: 'json',
      success: function(data) {
        $('#terms_box').show();
        tinymce.init({
          selector: "#terms_and_conditions",
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
        $.each(data, function(key, value) {
          if(modality==1){
            terms += value.term.export;
          }else{
            terms += value.term.import;
          }
        });
        $('#terms_and_conditions').val(terms);
      }
    });
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
          $('select[name="price_id"]').append('<option '+selected_price+' value="'+ key +'">'+ value +'</option>');
        });
      }
    });




  }
}
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


//SaleTerms



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

$(document).on('click', '#savecontactmanualquote', function () {

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
      var company_id = $("select[name='company_id']").val();
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


