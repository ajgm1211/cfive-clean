function schedules(id){
  var elemento = $("#detail"+id);
  var origin = $("#origin"+id);
  var destination = $("#destination"+id);
  var global = $("#global"+id);
  var inlands = $("#inlands"+id);
  var schedule = $("#schedules"+id);
  if(schedule.attr('hidden')){
    schedule.removeAttr('hidden');
    elemento.attr('hidden','true');
    origin.attr('hidden','true');
    destination.attr('hidden','true');
    global.attr('hidden','true');
    inlands.attr('hidden','true');
  }else{
    schedule.attr('hidden','true');

  }
}


function display(id){
  var elemento = $("#detail"+id);
  var origin = $("#origin"+id);
  var destination = $("#destination"+id);
  var global = $("#global"+id);
  var inlands = $("#inlands"+id);
  var schedule = $("#schedules"+id);

  if(elemento.attr('hidden')){

    $("#detail"+id).removeAttr('hidden');
    schedule.attr('hidden','true');

  }else{
    $("#detail"+id).attr('hidden','true');
  }


  if(origin.attr('hidden')){

    $("#origin"+id).removeAttr('hidden');

  }else{
    $("#origin"+id).attr('hidden','true');
  }

  if(destination.attr('hidden')){

    $("#destination"+id).removeAttr('hidden');

  }else{
    $("#destination"+id).attr('hidden','true');
  }
  if(global.attr('hidden')){

    $("#global"+id).removeAttr('hidden');

  }else{
    $("#global"+id).attr('hidden','true');
  }
  if(inlands.attr('hidden')){

    $("#inlands"+id).removeAttr('hidden');

  }else{
    $("#inlands"+id).attr('hidden','true');
  }

}

$(".fcl_label").on("click", function() {
  $('#formId').attr('action', '/quotes/listRate');
});
$(".lcl_label").on("click", function() {
  $('#formId').attr('action', '/quotes/listRateLcl');
});

/********
 Quotes
 ********/


//Btn back
$(document).on('click', '#create-quote', function (e) {
  $(this).hide();
  $("#create-quote-back").show();
});

$(document).on('change', '#hide_carrier', function () {
  $.ajax({
    type: 'POST',
    url: 'carrier/visibility',
    data: {
      'carrier_visibility': $("#hide_carrier").val(),
      'quote_id': $("#quote-id").val()
    },
    success: function(data) {
      //
    }
  });
});

//Btn next
$(document).on('click', '#create-quote-back', function (e) {
  $(this).hide();
  $("#create-quote").show();
});

//Load types
$(document).on('click', '#fcl_type', function (e) {
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
});

$(document).on('click', '#lcl_type', function (e) {
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
    if($("input[name='type']:checked").val()==2){
      total_weight=weight/1000;
      if(total_volume>total_weight){
        chargeable_weight=total_volume;
      }else{
        chargeable_weight=total_weight;
      }
      $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
    }else if($("input[name='type']:checked").val()==3){
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
});

$(document).on('click', '#air_type', function (e) {
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
    if($("input[name='type']:checked").val()==2){
      total_weight=total_weight/1000;
      if(total_volume>total_weight){
        chargeable_weight=total_volume;
      }else{
        chargeable_weight=total_weight;
      }
      $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
    }else if($("input[name='type']:checked").val()==3){
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

});

//Clone load lcl form
$(document).on('click', '#add_load_lcl_air', function (e) {
  var $template = $('#lcl_air_load_template'),
  $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertBefore($template);
});

//Remove lcl closest row
$(document).on('click', '.remove_lcl_air_load', function (e) {
  var $row = $(this).closest('.template').remove();
  $row.remove();

  $('.quantity').change();
  $('.height').change();
  $('.width').change();
  $('.large').change();
  $('.weight').change();
});

//Duplicate Quote
$(document).on('click', '#duplicate-quote', function (e) {
  var quote_id = $('#quote-id').val();
  $.ajax({
    url: "/quotes/duplicate/"+quote_id,
    dataType: 'json',
    success: function(data) {
      swal(
        'Success!',
        'The quote has been duplicated.',
        'success'
        )
    }
  });
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

$(document).on('click', '.addButtonOrigin', function (e) {
  var $template = $('#origin_ammounts'),
  $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertAfter($template)
  $clone.find("select").select2({
    placeholder: "Currency"
  });
});
$(document).on('click', '.addButton', function (e) {
  var $template = $('#freight_ammounts'),
  $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertAfter($template)
  $clone.find("select").select2({
    placeholder: "Currency"
  });
  $('#freight_ammount_charge').attr("required");

});

$(document).on('click', '#delete-quote', function () {
  var id = $(this).attr('data-quote-id');
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
        url: 'quotes/delete/' + id,
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

$(document).on('click', '.addButtonDestination', function (e) {
  var $template = $('#destination_ammounts'),
  $clone = $template
  .clone()
  .removeClass('hide')
  .removeAttr('id')
  .insertAfter($template)
  $clone.find("select").select2({
    placeholder: "Currency"
  });
});

$(document).on('click', '.removeOriginButton', function (e) {
  var $row = $(this).closest('tr').remove();
  $(".origin_price_per_unit").change();
  $(".origin_ammount_units").change();
  $(".origin_ammount_currency").change();
  $(".origin_total_ammount_2").change();
});

$(document).on('click', '.removeButton', function (e) {
  var $row = $(this).closest('tr').remove();
  $(".freight_price_per_unit").change();
  $(".freight_ammount_units").change();
  $(".freight_ammount_currency").change();
  $(".freight_total_ammount_2").change();
});

$(document).on('click', '.removeButtonDestination', function (e) {
  var $row = $(this).closest('tr').remove();
  $(".destination_price_per_unit").change();
  $(".destination_ammount_units").change();
  $(".destination_ammount_currency").change();
  $(".destination_total_ammount_2").change();
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

$(document).on('click', '#create-quote', function (e) {

  if($(".pick_up_date").val() == ''){
    msg('Sorry, pick up date is empty. Please go back and complete this field');
    //return;
  }else if($(".validity").val() == ''){
    msg('Sorry, validity date is empty. Please go back and complete this field');
    //return;
  }else if($(".company_id").val() == ''){
    msg('Sorry, company is empty. Please go back and complete this field');
    //return;
  }else if($(".contact_id").val() == ''){
    msg('Sorry, contact is empty. Please go back and complete this field');
    //return;
  }

  var origin_harbor=$("#origin_harbor").val();
  var destination_harbor=$("#destination_harbor").val();
  var destination_address=$("#destination_address").val();
  var origin_address=$("#origin_address").val();
  var origin_airport=$("#origin_airport").val();
  var destination_airport=$("#destination_airport").val();
  var modality = $('#modality').val();
  var contact_id = $('#contact_id').val();
  var chargeable_weight=0;
  var qty_20='';
  var qty_40='';
  var qty_40_hc='';
  var qty_45_hc='';
  var qty_20_reefer='';
  var qty_40_reefer='';
  var qty_40_hc_reefer='';
  var qty_20_open_top='';
  var qty_40_open_top='';
  var qty_45_hc_open_top='';
  var total_quantity='';
  var total_weight='';
  var total_volume='';
  var total_quantity_pkg='';
  var total_weight_pkg='';
  var total_volume_pkg='';
  var type_cargo='';
  var quantity = new Array();
  var height = new Array();
  var width = new Array();
  var large = new Array();
  var weight = new Array();
  var total_weight_arr = new Array();
  var volume = new Array();
  var type_cargo = new Array();
  var myTableDiv = document.getElementById("label_package_loads");
  var table = document.createElement('table');
  var tableBody = document.createElement('tbody');
  var terms = '';
  var contact_id=$('#contact_id').val();
  $.ajax({
    type: 'GET',
    url: '/quotes/contact/email/'+contact_id,
    success: function(data) {
      $('#addresse').val(data);
    },
    error: function (request, status, error) {
      console.log(request.responseText);
    }
  });

  $.ajax({
    url: "/quotes/terms/"+origin_harbor+"/"+destination_harbor,
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

  if($(".qty_20").val()>0){
    qty_20=$(".qty_20").val();
  }
  if($(".qty_40").val()>0){
    qty_40=$(".qty_40").val();
  }
  if($(".qty_40_hc").val()>0){
    qty_40_hc=$(".qty_40_hc").val();
  }
  if($(".qty_45_hc").val()>0){
    qty_45_hc=$(".qty_45_hc").val();
  }
  if($(".qty_20_reefer").val()>0){
    qty_20_reefer=$(".qty_20_reefer").val();
  }
  if($(".qty_40_reefer").val()>0){
    qty_40_reefer=$(".qty_40_reefer").val();
  }
  if($(".qty_40_hc_reefer").val()>0){
    qty_40_hc_reefer=$(".qty_40_hc_reefer").val();
  }
  if($(".qty_20_open_top").val()>0){
    qty_20_open_top=$(".qty_20_open_top").val();
  }
  if($(".qty_40_open_top").val()>0){
    qty_40_open_top=$(".qty_40_open_top").val();
  }
  /*if($(".qty_40_hc_open_top").val()>0){
        qty_40_hc_open_top=$(".qty_40_hc_open_top").val();
      }  */
      if($("#total_quantity").val()>0){
        total_quantity=$("#total_quantity").val();
      }
      if($("#total_weight").val()>0){
        total_weight=$("#total_weight").val();
      }
      if($("#total_volume").val()>0){
        total_volume=$("#total_volume_pkg_input").val();
      }
      if($("#total_quantity_pkg_input").val()>0){
        total_quantity_pkg=$("#total_quantity_pkg_input").val();
      }
      if($("#total_weight_pkg_input").val()>0){
        total_weight_pkg=$("#total_weight_pkg_input").val();
      }
      if($("#total_volume_pkg_input").val()>0){
        total_volume_pkg=$("#total_volume_pkg_input").val();
      }
      if($("#chargeable_weight_pkg_input").val()>0){
        chargeable_weight=$("#chargeable_weight_pkg_input").val();
      }
  //Creating table to loads by packages
  table.appendChild(tableBody);

  var heading = new Array();
  heading[0] = "Quantity";
  heading[1] = "Height";
  heading[2] = "Width";
  heading[3] = "Large";
  heading[4] = "Weight";
  heading[5] = "Total Weight";
  heading[6] = "Volume";

  $(".type_cargo").each(function(){
    if($(this).val()==1){
      type_cargo.push('Pallets');
    }else{
      type_cargo.push('Packages');
    }
  });

  $(".quantity").each(function(){
    if($(this).val()!=''){
      quantity.push($(this).val());
    }
  });

  $(".height").each(function(){
    if($(this).val()!=''){
      height.push($(this).val());
    }
  });

  $(".width").each(function(){
    if($(this).val()!=''){
      width.push($(this).val());
    }
  });

  $(".large").each(function(){
    if($(this).val()!=''){
      large.push($(this).val());
    }
  });

  $(".volume_input").each(function(){
    if($(this).val()!=''){
      volume.push($(this).val()+" m3");
    }
  });

  $(".weight").each(function(){
    if($(this).val()!=''){
      weight.push($(this).val());
    }
  });

  var q2 = new Array();

  for (i = 0; i < quantity.length; i++) {
    for (i = 0; i < height.length; i++) {
      for (i = 0; i < width.length; i++) {
        for (i = 0; i < large.length; i++) {
          for (i = 0; i < weight.length; i++) {
            for (i = 0; i < volume.length; i++) {
              if((quantity[i]!=undefined) && (height[i]!=undefined) && (width[i]!=undefined) && (large[i]!=undefined) && (weight[i]!=undefined)){
                q2[i] = new Array (quantity[i]+" "+type_cargo[i],height[i],width[i],large[i],weight[i]+" kg",weight[i]*quantity[i]+" kg",volume[i]);
              }
            }
          }
        }
      }
    }
  }

  //TABLE COLUMNS
  var tr = document.createElement('tr');
  tableBody.appendChild(tr);
  for (i = 0; i < heading.length; i++) {
    var th = document.createElement('th')
    th.width = '75';
    th.setAttribute('class','header-table title-quote');
    th.appendChild(document.createTextNode(heading[i]));
    tr.appendChild(th);
  }

  //TABLE ROWS
  for (i = 0; i < q2.length; i++) {
    var tr = document.createElement('tr');
    for (j = 0; j < q2[i].length; j++) {
      var td = document.createElement('td')
      td.appendChild(document.createTextNode(q2[i][j]));
      tr.appendChild(td)
    }
    tableBody.appendChild(tr);
  }

  //Adding table body to table
  if(q2.length>0){
    table.setAttribute('class', 'table table-bordered color-blue text-center')
    $("#label_package_loads table").empty();
    myTableDiv.appendChild(table);
  }

  type_cargo=$("#type_cargo").val();
  if(type_cargo==1){
    type_cargo='Pallets';
  }else{
    type_cargo='Packages';
  }
  if(origin_harbor!=''){
    $.ajax({
      type: 'get',
      url: 'get/harbor/id/' + origin_harbor,
      success: function(data) {
        $("#origin_input").html(data.name+", "+data.code);
      }
    });
  }
  if(destination_harbor!=''){
    $.ajax({
      type: 'get',
      url: 'get/harbor/id/' + destination_harbor,
      success: function(data) {
        $("#destination_input").html(data.name+", "+data.code);
      }
    });
  }
  if(origin_airport!=''){
    $.ajax({
      type: 'get',
      url: 'get/airport/id/' + origin_airport,
      success: function(data) {
        $("#origin_input").html(data.name);
      }
    });
  }
  if(destination_airport!=''){
    $.ajax({
      type: 'get',
      url: 'get/airport/id/' + destination_airport,
      success: function(data) {
        $("#destination_input").html(data.name);
      }
    });
  }
  if(chargeable_weight!='' || chargeable_weight>0){
    $("#chargeable_weight_span").html(parseFloat(chargeable_weight).toFixed(2));
    $("#chargeable_weight_div").removeClass('hide');
  }
  if(qty_20!='' || qty_20>0){
    $("#cargo_details_20").html(qty_20);
    $("#cargo_details_20_p").removeClass('hide');
  }else{
    $("#cargo_details_20_p").addClass('hide');
  }
  if(qty_40!='' || qty_40>0){
    $("#cargo_details_40").html(qty_40);
    $("#cargo_details_40_p").removeClass('hide');
  }else{
    $("#cargo_details_40_p").addClass('hide');
  }
  if(qty_40_hc!='' || qty_40_hc>0){
    $("#cargo_details_40_hc").html(qty_40_hc);
    $("#cargo_details_40_hc_p").removeClass('hide');
  }else{
    $("#cargo_details_40_hc_p").addClass('hide');
  }
  if(qty_45_hc!='' || qty_45_hc>0){
    $("#cargo_details_45_hc").html(qty_45_hc);
    $("#cargo_details_45_hc_p").removeClass('hide');
  }else{
    $("#cargo_details_45_hc_p").addClass('hide');
  }
  if(qty_20_reefer!='' || qty_20_reefer>0){
    $("#cargo_details_20_reefer").html(qty_20_reefer);
    $("#cargo_details_20_reefer_p").removeClass('hide');
  }else{
    $("#cargo_details_20_reefer_p").addClass('hide');
  }
  if(qty_40_reefer!='' || qty_40_reefer>0){
    $("#cargo_details_40_reefer").html(qty_40_reefer);
    $("#cargo_details_40_reefer_p").removeClass('hide');
  }else{
    $("#cargo_details_40_reefer_p").addClass('hide');
  }
  if(qty_40_hc_reefer!='' || qty_40_hc_reefer>0){
    $("#cargo_details_40_hc_reefer").html(qty_40_hc_reefer);
    $("#cargo_details_40_hc_reefer_p").removeClass('hide');
  }else{
    $("#cargo_details_40_reefer_p").addClass('hide');
  }
  if(qty_20_open_top!='' || qty_20_open_top>0){
    $("#cargo_details_20_open_top").html(qty_20_open_top);
    $("#cargo_details_20_open_top_p").removeClass('hide');
  }else{
    $("#cargo_details_20_open_top_p").addClass('hide');
  }
  if(qty_40_open_top!='' || qty_40_open_top>0){
    $("#cargo_details_40_open_top").html(qty_40_open_top);
    $("#cargo_details_40_open_top_p").removeClass('hide');
  }else{
    $("#cargo_details_40_open_top_p").addClass('hide');
  }
  /*if(qty_40_hc_open_top!='' || qty_40_hc_open_top>0){
        $("#cargo_details_40_hc_open_top").html(qty_40_hc_open_top);
        $("#cargo_details_40_hc_open_top_p").removeClass('hide');
    }else{
        $("#cargo_details_40_hc_open_top_p").addClass('hide');
      } */
      if(total_quantity!='' && type_cargo!=''){
        $("#cargo_details_cargo_type").html(" "+type_cargo);
        $("#cargo_details_cargo_type_p").removeClass('hide');
      }else{
        $("#cargo_details_cargo_type_p").addClass('hide');
      }
      if(total_quantity!='' || total_quantity>0){
        $("#cargo_details_total_quantity").html(" "+total_quantity);
        $("#cargo_details_total_quantity_p").removeClass('hide');
      }else{
        $("#cargo_details_total_quantity_p").addClass('hide');
      }
      if(total_weight!='' || total_weight>0){
        $("#cargo_details_total_weight").html(" "+total_weight);
        $("#cargo_details_total_weight_p").removeClass('hide');
      }else{
        $("#cargo_details_total_weight_p").addClass('hide');
      }
      if(total_volume!='' || total_volume>0){
        $("#cargo_details_total_volume").html(" "+total_volume);
        $("#cargo_details_total_volume_p").removeClass('hide');
      }else{
        $("#cargo_details_total_volume_p").addClass('hide');
      }

      if((total_quantity_pkg!='' || total_quantity_pkg>0) && (total_weight_pkg!='' || total_weight_pkg>0) && (total_volume_pkg!='' || total_volume_pkg>0)){

        $("#cargo_details_total_quantity_pkg").html(" "+total_quantity_pkg);
        $("#cargo_details_total_weight_pkg").html(" "+total_weight_pkg);
        $("#cargo_details_total_volume_pkg").html(" "+total_volume_pkg);
        $("#cargo_details_total_pkg_p").removeClass('hide');
      }else{
        $("#cargo_details_total_pkg_p").addClass('hide');
      }

      if(origin_address!=''){
        $("#origin_address_p").html(origin_address);
        $("#origin_address_panel").removeClass('hide');
      }else{
        $("#origin_address_panel").addClass('hide');
      }
      if(destination_address!=''){
        $("#destination_address_p").html(destination_address);
        $("#destination_address_panel").removeClass('hide');
      }else{
        $("#destination_address_panel").addClass('hide');
      }


    });

$( document ).ready(function() {
  $('select[name="contact_id"]').prop("disabled",true);
  $( "select[name='company_id']" ).on('change', function() {
    var company_id = $(this).val();
    if(company_id) {
      $('select[name="contact_id"]').empty();
      $('select[name="contact_id"]').prop("disabled",false);
      $.ajax({
        url: "/quotes/company/contact/id/"+company_id,
        dataType: 'json',
        success: function(data) {
          $('select[name="client"]').empty();
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
        }
      });
      $.ajax({
        url: "/quotes/payments/"+company_id,
        dataType: 'json',
        success: function(data) {
          tinymce.init({
            selector: "#payment_conditions",
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
          if(data.payment_conditions!=''){
            $('#payment_conditions').val(data.payment_conditions).tinymce({
              theme: "modern",
            });
          }
        }
      });
    }else{
      $('select[name="contact_id"]').empty();
      $('select[name="price_id"]').empty();
    }
  });

  // CLEARING COMPANIES SELECT
  $("select[name='company_id_quote']").val('');
  $('#select2-m_select2_2_modal-container').text('Please an option');
  $( "select[name='company_id_quote']" ).on('change', function() {
    var company_id = $(this).val();
    if(company_id) {
      $('select[name="contact_id"]').empty();
      $('select[name="contact_id"]').prop("disabled",false);
      $.ajax({
        url: "/quotes/company/contact/id/"+company_id,
        dataType: 'json',
        success: function(data) {
          $('select[name="client"]').empty();
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
          $("select[name='price_id']").val('');
          $('#select2-price_id-n3-container').text('Please an option');
        }
      });
    }else{
      $('select[name="client"]').empty();
      $('select[name="price_id"]').empty();
    }
  });
});

//Calculando origin ammounts
$(document).on("change keyup keydown", ".origin_ammount_units, .origin_price_per_unit, .origin_ammount_currency, .origin_ammount_markup", function() {
  var sum = 0;
  var total_amount = 0;
  var self = this;
  var markup = 0;
  var currency_cfg = $("#currency_id").val();
  $(".origin_price_per_unit").each(function(){
    $( this).each(function() {
      var quantity = $(this).closest('tr').find('.origin_ammount_units').val();
      var currency_id = $(self).closest('tr').find('.origin_ammount_currency').val();
      if(quantity > 0) {
        if ($(self).closest('tr').find('.origin_ammount_currency').val() != "") {
          $.ajax({
            url: '/api/currency/'+currency_id,
            dataType: 'json',
            success: function (json) {

              //var value = $('.origin_exp_amount').val();
              var amount = $(self).closest('tr').find('.origin_price_per_unit').val();
              var quantity = $(self).closest('tr').find('.origin_ammount_units').val();
              markup = $(self).closest('tr').find('.origin_ammount_markup').val();
              var sub_total = amount * quantity;

              if(currency_cfg+json.alphacode == json.api_code){
                total = sub_total / json.rates;
              }else{
                total = sub_total / json.rates_eur;
              }
              total = total.toFixed(2);
              if(markup > 0){
                var total_amount_m = Number(total)+ Number(markup);
                $(self).closest('tr').find('.origin_total_ammount_2').val(total_amount_m.toFixed(2));
                $(self).closest('tr').find('.origin_total_ammount_2').change();
              }else{
                $(self).closest('tr').find('.origin_total_ammount_2').val(total);
                $(self).closest('tr').find('.origin_total_ammount_2').change();
              }
            }
          });

        }

        total_amount = quantity * $(this).val();
        total_amount = total_amount.toFixed(2);
        $(this).closest('tr').find('.origin_total_ammount').val(total_amount);
        $(this).closest('tr').find('.origin_total_ammount').change();
      }else{
        total_amount = 0;
        $(this).closest('tr').find('.origin_total_ammount').val(total_amount);
        $(this).closest('tr').find('.origin_total_ammount').change();
      }
    });
  });
});


//Calculando freight ammounts
$(document).on("change keyup keydown", ".freight_ammount_units, .freight_price_per_unit, .freight_ammount_currency, .freight_ammount_markup", function() {
  var sum = 0;
  var total_amount = 0;
  var self = this;
  var markup = 0;
  var currency_cfg = $("#currency_id").val();
  $(".freight_price_per_unit").each(function(){
    $( this).each(function() {
      var quantity = $(this).closest('tr').find('.freight_ammount_units').val();
      var currency_id = $(self).closest('tr').find('.freight_ammount_currency').val();
      if(quantity > 0) {
        if ($(self).closest('tr').find('.freight_ammount_currency').val() != "") {
          $.ajax({
            url: '/api/currency/'+currency_id,
            dataType: 'json',
            success: function (json) {

              //var value = $('.origin_exp_amount').val();
              var amount = $(self).closest('tr').find('.freight_price_per_unit').val();
              var quantity = $(self).closest('tr').find('.freight_ammount_units').val();
              markup = $(self).closest('tr').find('.freight_ammount_markup').val();
              var sub_total = amount * quantity;
              if(currency_cfg+json.alphacode == json.api_code){
                total = sub_total / json.rates;
              }else{
                total = sub_total / json.rates_eur;
              }
              total = total.toFixed(2);
              if(markup > 0){
                var total_amount_m = Number(total)+ Number(markup);
                $(self).closest('tr').find('.freight_total_ammount_2').val(total_amount_m.toFixed(2));
                $(self).closest('tr').find('.freight_total_ammount_2').change();
              }else{
                $(self).closest('tr').find('.freight_total_ammount_2').val(total);
                $(self).closest('tr').find('.freight_total_ammount_2').change();
              }
            }
          });

        }

        total_amount = quantity * $(this).val();
        $(this).closest('tr').find('.freight_total_ammount').val(total_amount);
        $(this).closest('tr').find('.freight_total_ammount').change();
      }else{
        total_amount = 0;
        $(this).closest('tr').find('.freight_total_ammount').val(total_amount);
        $(this).closest('tr').find('.freight_total_ammount').change();
      }
    });
  });
});

//Calculando destinations ammounts
$(document).on("change keyup keydown", ".destination_ammount_units, .destination_price_per_unit, .destination_ammount_currency, .destination_ammount_markup", function() {
  var sum = 0;
  var total_amount = 0;
  var markup = 0;
  var total=0;
  var self = this;
  var currency_cfg = $("#currency_id").val();
  $(".destination_price_per_unit").each(function(){
    $( this).each(function() {
      var quantity = $(this).closest('tr').find('.destination_ammount_units').val();
      var currency_id = $(self).closest('tr').find('.destination_ammount_currency').val();

      if(quantity > 0) {
        if ($(self).closest('tr').find('.destination_ammount_currency').val() != "") {
          $.ajax({
            url: '/api/currency/'+currency_id,
            dataType: 'json',
            success: function (json) {
              var amount = $(self).closest('tr').find('.destination_price_per_unit').val();
              var quantity = $(self).closest('tr').find('.destination_ammount_units').val();
              markup = $(self).closest('tr').find('.destination_ammount_markup').val();
              var sub_total = amount * quantity;

              if(currency_cfg+json.alphacode == json.api_code){
                total = sub_total / json.rates;
              }else{
                total = sub_total / json.rates_eur;
              }
              total = total.toFixed(2);
              if(markup > 0){
                var total_amount_m = Number(total)+ Number(markup);
                $(self).closest('tr').find('.destination_total_ammount_2').val(total_amount_m.toFixed(2));
                $(self).closest('tr').find('.destination_total_ammount_2').change();
              }else{
                $(self).closest('tr').find('.destination_total_ammount_2').val(total);
                $(self).closest('tr').find('.destination_total_ammount_2').change();
              }

            }
          });

        }

        total_amount = quantity * $(this).val();

        $(this).closest('tr').find('.destination_total_ammount').val(total_amount);
        $(this).closest('tr').find('.destination_total_ammount').change();
      }else{
        total_amount = 0;
        $(this).closest('tr').find('.destination_total_ammount').val(total_amount);
        $(this).closest('tr').find('.destination_total_ammount').change();
      }
    });
  });
});

//Calculando total origin
$(document).on("change keyup keydown", ".origin_total_ammount_2", function() {
  var sum = 0;
  var total = 0;
  var tot = 0;
  $(".origin_total_ammount_2").each(function(){
    total=$(this).closest('tr').find('.origin_total_ammount_2').val();
    sum += +total;

  });
  $("#sub_total_origin").html(" "+sum);
  $("#total_origin_ammount").val(sum);
  $("#total_origin_ammount").change();
});

//Calculando total freight
$(document).on("change keyup keydown", ".freight_total_ammount_2", function() {
  var sum = 0;
  var total = 0;
  $(".freight_total_ammount_2").each(function(){
    total=$(this).closest('tr').find('.freight_total_ammount_2').val();
    sum += +total;
  });
  $("#sub_total_freight").html(" "+sum);
  $("#total_freight_ammount").val(sum);
  $("#total_freight_ammount").change();
});

//Calculando total destination
$(document).on("change keyup keydown", ".destination_total_ammount_2", function() {
  var sum = 0;
  var total = 0;
  $(".destination_total_ammount_2").each(function(){
    total=$(this).closest('tr').find('.destination_total_ammount_2').val();
    sum += +total;
  });
  $("#sub_total_destination").html(" "+sum);
  $("#total_destination_ammount").val(sum);
  $("#total_destination_ammount").change();
});

$(document).on("change keyup keydown", ".destination_total_ammount_2", function() {
  var sum = 0;
  var total = 0;
  $(".destination_price_per_unit").each(function(){
    total=$(this).closest('tr').find('.destination_total_ammount_2').val();
    sum += +total;
  });
  $("#sub_total_destination").html(" "+sum);
  $("#total_destination_ammount").val(sum);
  $("#total_destination_ammount").change();
});

$(document).on("change keyup keydown", "#total_freight_ammount, #total_origin_ammount, #total_destination_ammount", function() {

  var total_origin=$("#total_origin_ammount").val();
  var total_freight=$("#total_freight_ammount").val();
  var total_destination=$("#total_destination_ammount").val();
  if(total_origin>0){
    total_origin=parseFloat(total_origin);
  }else{
    total_origin=0;
  }
  if(total_freight>0){
    total_freight=parseFloat(total_freight);
  }else{
    total_freight=0;
  }
  if(total_destination>0){
    total_destination=parseFloat(total_destination);
  }else{
    total_destination=0;
  }

  sum = total_origin+total_freight+total_destination;

  sum = parseFloat(sum);
  sum = sum.toFixed(2);

  $("#total").html(" "+sum);
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

  $("#total_volume_pkg").html((sum) + " m3");
  $("#total_volume_pkg_input").val(sum);
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
  if($("input[name='type']:checked").val()==2){
    total_vol_chargeable=sum_vol;
    total_weight=weight/1000;
    if(total_vol_chargeable>total_weight){
      chargeable_weight=total_vol_chargeable;
    }else{
      chargeable_weight=total_weight;
    }
    $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
  }else if($("input[name='type']:checked").val()==3){
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
    if($("input[name='type']:checked").val()==2){
      total_weight=total_weight/1000;
      if(total_volume>total_weight){
        chargeable_weight=total_volume;
      }else{
        chargeable_weight=total_weight;
      }
      $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2)+" m<sup>3</sup>");
    }else if($("input[name='type']:checked").val()==3){
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

$(document).on('click', '#send-pdf-quote', function () {
  var id = $('#quote-id').val();
  var email = $('#quote_email').val();
  var to = $('#addresse').val();
  var email_template_id = $('#email_template').val();
  var email_subject = $('#email-subject').val();
  var email_body = $('#email-body').val();

  if(email_template_id!=''&&to!=''){
    $.ajax({
      type: 'POST',
      url: '/quotes/send/pdf',
      data:{"email_template_id":email_template_id,"id":id,"subject":email_subject,"body":email_body,"to":to},
      beforeSend: function () {
        $('#send-pdf-quote').hide();
        $('#send-pdf-quote-sending').show();
      },
      success: function(data) {
        $('#spin').hide();
        $('#send-pdf-quote').show();
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

//Change Status Quote
$(document).on('change', '#status_quote_id', function () {
  var id = $('#quote-id').val();
  var status_quote_id = $('#status_quote_id').val();
  $.ajax({
    type: 'POST',
    url: '/quotes/update/status/'+id,
    data:{"status_quote_id":status_quote_id},
    success: function(data) {
      $('#spin').hide();

      if(data.message=='Ok'){
        swal(
          'Done!',
          'Status updated.',
          'success'
          )
      }else{
        swal(
          'Error!',
          'Has ocurred an error.',
          'error'
          )
      }
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

/** PDF **/

$(document).on('change', '#pdf_language', function () {
  var type=$("#pdf_language").val();
  var quote_id=$("#quote-id").val();
  $.ajax({
    type: 'POST',
    url: '/settings/update/pdf/language',
    data: {
      'pdf_language': $("#pdf_language").val(),
      'quote_id': $("#quote-id").val()
    },
    success: function(data) {
      //
    }
  });
});

$(document).on('change', '#pdf_type', function () {
  var type=$("#pdf_type").val();
  $.ajax({
    type: 'POST',
    url: '/settings/update/pdf/type',
    data: {
      'pdf_type': $("#pdf_type").val()
    },
    success: function(data) {
      //
    }
  });
});

$(document).on('change', '#pdf_ammounts', function () {
  var type=$("#pdf_ammounts").val();
  $.ajax({
    type: 'POST',
    url: '/settings/update/pdf/ammounts',
    data: {
      'pdf_ammounts': $("#pdf_ammounts").val()
    },
    success: function(data) {
      //
    }
  });
});