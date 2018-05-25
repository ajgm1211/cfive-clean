
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
                        'Your file has been deleted.',
                        'success'
                    )
                    $(theElement).closest('li').remove();
                }
            });

        }

    });
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
                url: 'quotes.destroy/' + id,
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
$(document).on('click', '#create-quote', function (e) {
    $(this).hide();
    $("#create-quote-back").show();
});
$(document).on('click', '#create-quote-back', function (e) {
    $(this).hide();
    $("#create-quote").show();
});

$(document).on('click', '.addButtonOrigin', function (e) {
    var $template = $('#origin_ammounts'),
        $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertAfter($template);
});
$(document).on('click', '.addButton', function (e) {
    var $template = $('#freight_ammounts'),
        $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertAfter($template);
});

$(document).on('click', '.addButtonDestination', function (e) {
    var $template = $('#destination_ammounts'),
        $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertAfter($template);
});
$(document).on('click', '.removeOriginButton', function (e) {
    var $row = $(this).closest('.row').remove();
    $(".origin_price_per_unit").change();
});
$(document).on('click', '.removeButton', function (e) {
    var $row = $(this).closest('.row').remove();
    $(".freight_price_per_unit").change();
});
$(document).on('click', '.removeButtonDestination', function (e) {
    var $row = $(this).closest('.row').remove();
    $(".destination_price_per_unit").change();
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

$(document).on('change', '#delivery_type', function (e) {
    if($(this).val()==1){
        $("#origin_address_label").hide();
        $("#destination_address_label").hide();
    }
    if($(this).val()==2){
        $("#origin_address_label").hide();
        $("#destination_address_label").show();
    }
    if($(this).val()==3){
        $("#origin_address_label").show();
        $("#destination_address_label").hide();
    }
    if($(this).val()==4){
        $("#origin_address_label").show();
        $("#destination_address_label").show();
    }
});

$(document).on('click', '#create-quote', function (e) {
    var origin_harbor=$("#origin_harbor").val();
    var qty_20='';
    if($(".qty_20").val()>0){
        qty_20=$(".qty_20").val();
    }else{
        qty_20='';
    }

    var qty_40=$(".qty_40").val();
    var qty_40_hc=$(".qty_40_hc").val();
    var destination_harbor=$("#destination_harbor").val();
    $.ajax({
        type: 'get',
        url: 'get/harbor/id/' + origin_harbor,
        success: function(data) {
            $("#origin_input").html(data.name);
        }
    });
    $.ajax({
        type: 'get',
        url: 'get/harbor/id/' + destination_harbor,
        success: function(data) {
            $("#destination_input").html(data.name);
        }
    });
    if(qty_20!='' || qty_20>0){
        $("#cargo_details_20").html(qty_20);
        $("#cargo_details_20_p").removeClass('hide');
    }else{
        $("#cargo_details_20_p").addClass('hide');
    }
    if(qty_40!=''){
        $("#cargo_details_40").html(qty_40);
        $("#cargo_details_40_p").removeClass('hide');
    }
    if(qty_40_hc!=''){
        $("#cargo_details_40_hc").html(qty_40_hc);
        $("#cargo_details_40_hc_p").removeClass('hide');
    }
});
$( document ).ready(function() {
    $( "select[name='company_id']" ).on('change', function() {
        var company_id = $(this).val();
        if(company_id) {
            $.ajax({
                url: "company/contact/id/"+company_id,
                dataType: 'json',
                success: function(data) {
                    $('select[name="client"]').empty();
                    $.each(data, function(key, value) {
                        $('select[name="contact_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
            $.ajax({
                url: "company/price/id/"+company_id,
                dataType: 'json',
                success: function(data) {
                    $('select[name="price_id"]').empty();
                    $.each(data, function(key, value) {
                        $('select[name="price_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
        }else{
            $('select[name="client"]').empty();
            $('select[name="price_id"]').empty();
        }
    });
});

$(document).on("change keyup keydown", ".origin_ammount_units, .origin_price_per_unit, .origin_ammount_currency, .origin_ammount_markup", function() {
    var sum = 0;
    var total_amount = 0;
    var markup = 0;
    $(".origin_price_per_unit").each(function(){
        $( this).each(function() {
            var quantity = $(this).closest('.row').find('.origin_ammount_units').val();
            if(quantity > 0) {
                /*if($(this).closest('.col-md-12').find('.origin_ammount_currency').val() == "clp" || $(this).closest('.col-md-12').find('.international_freight_amount_currency').val() == "ars" || $(this).closest('.col-md-12').find('.international_freight_amount_currency').val() == "eur") {
                    total_amount = $(this).closest('.col-md-12').find('.international_freight_amount_usd').val();
                }else{
                    total_amount = quantity * $(this).val();
                }*/
                markup = $(this).closest('.row').find('.origin_ammount_markup').val();
                total_amount = quantity * $(this).val();
                if(markup > 0){
                    total_amount = total_amount + +markup;
                }
                $(this).closest('.row').find('.origin_total_ammount').val(total_amount);
            }else{
                total_amount = 0;
                $(this).closest('.row').find('.origin_total_ammount').val(total_amount);
            }
            sum += +total_amount;
            $("#total_origin_ammount").val(sum);
            $("#sub_total_origin").html(" "+sum + " USD");
            $("#total_origin_ammount").change();
        });
    });
});

$(document).on("change keyup keydown", ".freight_ammount_units, .freight_price_per_unit, .freight_ammount_currency, .freight_ammount_markup", function() {
    var sum = 0;
    var total_amount = 0;
    var markup = 0;
    $(".freight_price_per_unit").each(function(){
        $( this).each(function() {
            var quantity = $(this).closest('.row').find('.freight_ammount_units').val();
            if(quantity > 0) {
                /*if($(this).closest('.col-md-12').find('.origin_ammount_currency').val() == "clp" || $(this).closest('.col-md-12').find('.international_freight_amount_currency').val() == "ars" || $(this).closest('.col-md-12').find('.international_freight_amount_currency').val() == "eur") {
                    total_amount = $(this).closest('.col-md-12').find('.international_freight_amount_usd').val();
                }else{
                    total_amount = quantity * $(this).val();
                }*/
                markup = $(this).closest('.row').find('.freight_ammount_markup').val();
                total_amount = quantity * $(this).val();
                if(markup > 0){
                    total_amount = total_amount + +markup;
                }
                $(this).closest('.row').find('.freight_total_ammount').val(total_amount);
            }else{
                total_amount = 0;
                $(this).closest('.row').find('.freight_total_ammount').val(total_amount);
            }
            sum += +total_amount;
            $("#total_freight_ammount").val(sum);
            $("#sub_total_freight").html(" "+sum + " USD");
            $("#total_freight_ammount").change();
        });
    });
});

$(document).on("change keyup keydown", ".destination_ammount_units, .destination_price_per_unit, .destination_ammount_currency, .destination_ammount_markup", function() {
    var sum = 0;
    var total_amount = 0;
    var markup = 0;
    $(".destination_price_per_unit").each(function(){
        $( this).each(function() {
            var quantity = $(this).closest('.row').find('.destination_ammount_units').val();
            if(quantity > 0) {
                /*if($(this).closest('.col-md-12').find('.origin_ammount_currency').val() == "clp" || $(this).closest('.col-md-12').find('.international_freight_amount_currency').val() == "ars" || $(this).closest('.col-md-12').find('.international_freight_amount_currency').val() == "eur") {
                    total_amount = $(this).closest('.col-md-12').find('.international_freight_amount_usd').val();
                }else{
                    total_amount = quantity * $(this).val();
                }*/
                markup = $(this).closest('.row').find('.destination_ammount_markup').val();
                total_amount = quantity * $(this).val();
                if(markup > 0){
                    total_amount = total_amount + +markup;
                }
                $(this).closest('.row').find('.destination_total_ammount').val(total_amount);
            }else{
                total_amount = 0;
                $(this).closest('.row').find('.destination_total_ammount').val(total_amount);
            }
            sum += +total_amount;
            $("#total_destination_ammount").val(sum);
            $("#sub_total_destination").html(" "+sum + " USD");
            $("#total_destination_ammount").change();
        });
    });
});

$(document).on("change keyup keydown", "#total_origin_ammount, #total_freight_ammount, #total_destination_ammount", function() {
    var total_origin=$("#total_origin_ammount").val();
    var total_freight=$("#total_freight_ammount").val();
    var total_destination=$("#total_destination_ammount").val();
    if(total_origin>0){
        total_origin=parseFloat(total_origin);
    }
    if(total_freight>0){
        total_freight=parseFloat(total_freight);
    }
    if(total_destination>0){
        total_destination=parseFloat(total_destination);
    }
    var sum = 0;
    sum = total_origin + +total_freight + +total_destination;
    $("#total").html(" "+sum + " USD");
});