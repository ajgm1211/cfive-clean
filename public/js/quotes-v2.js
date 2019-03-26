$.fn.editable.defaults.mode = 'inline';

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.editable').editable({
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

$(document).on('click', '#edit-quote', function () {
    $(".quote_id_span").attr('hidden','true');
    $(".company_span").attr('hidden','true');
    $(".status_span").attr('hidden','true');
    $(".delivery_type_span").attr('hidden','true');
    $(".price_level_span").attr('hidden','true');
    $(".type_span").attr('hidden','true');
    $(".incoterm_id_span").attr('hidden','true');
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
    $(".contact_id").attr('hidden','true');
    $(".validity").attr('hidden','true');
    $(".user_id").attr('hidden','true');
    $(".date_issued").attr('hidden','true');
    $(".equipment").attr('hidden','true');
    $(".price_id").attr('hidden','true');
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
                $(".quote_id").val(data.quote['quote_id']);
                $(".quote_id_span").html(data.quote['quote_id']);
                $(".company_id").val(data.quote['company_id']);
                $(".company_id_span").html(data.quote['company_id']);
                $(".status").val(data.quote['status']);
                $(".status_span").html(data.quote['status']+' <i class="fa fa-check"></i>');
                $(".status_span").addClass('Status_'+data.quote['status']);
                $(".delivery_type").val(data.quote['delivery_type']);
                $(".delivery_type_span").html(delivery_type);
                $(".incoterm_id").val(data.quote['incoterm_id']);
                $(".incoterm_id_span").html(incoterm);
                $(".equipment").val(data.quote['equipment']);
                $(".equipment_span").empty();
                var length = data.quote['equipment'].length;
                $.each( data.quote['equipment'], function( index, value ){

                    if (index === (length-1)) {
                        $(".equipment_span").append(value);
                    }else{
                        $(".equipment_span").append(value + ', ');
                    }
                });

                $(".contact_id").val(data.quote['contact_id']);
                $(".contact_id_span").html(data.contact_name);
                $(".user_id").val(data.quote['user_id']);
                $(".user_id_span").val(data.quote['user_id']);
                $(".date_issued").val(data.quote['date_issued']);
                $(".date_issued_span").html(data.quote['date_issued']);
                $(".price_id").val(data.quote['price_id']);
                $(".price_level_span").val(data.quote['price_id']);
                $(".validity").val(data.quote['validity_start']+'/'+data.quote['validity_end']);
                $(".validity_span").html(data.quote['validity_start']+'/'+data.quote['validity_end']);

                $(".quote_id_span").removeAttr('hidden');
                $(".company_span").removeAttr('hidden');
                $(".status_span").removeAttr('hidden');
                $(".delivery_type_span").removeAttr('hidden');
                $(".price_level_span").removeAttr('hidden');
                $(".type_span").removeAttr('hidden');
                $(".incoterm_id_span").removeAttr('hidden');
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
            }
        }
    });
});

$('.date_issued').datetimepicker();

$('.select2-freight').select2();

$('.select2-origin').select2();

$('.select2-destination').select2();

$(document).on('click', '.addFreightCharge', function (e) {
    var $template = $('#freight_charges'),
        $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
});

$(document).on('click', '.addOriginCharge', function (e) {
    var $template = $('#origin_charges'),
        $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
});

$(document).on('click', '.addDestinationCharge', function (e) {
    var $template = $('#destination_charges'),
        $clone = $template
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
});

$(document).on('click', '.removeFreightCharge', function (e) {
    $(this).closest('tr').remove();
});

$(document).on('click', '.removeOriginCharge', function (e) {
    $(this).closest('tr').remove();
});

$(document).on('click', '.removeDestinationCharge', function (e) {
    $(this).closest('tr').remove();
});

