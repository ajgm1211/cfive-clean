$.fn.editable.defaults.mode = 'inline';

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".open-inland-modal").click(function() {
        var rate_id = $(this).data('rate-id');
        $(".modal-body .automatic_rate_id").val(rate_id);
    });

    //Modal para editar rates
    $(document).on('click', '.edit_rate_modal', function() {
        var url = "/v2/quotes/rates/edit";
        var rate_id = $(this).data('rate-id');
        $.get(url + '/' + rate_id, function(data) {
            //success data
            console.log(data.origin_port_id);
            $('.origin_port_id').val(data.origin_port_id);
            $('.destination_port_id').val(data.destination_port_id);
            $('.contract').val(data.contract);
            $('.type').val(data.type);
            $('.transit_time').val(data.transit_time);
            $('.via').val(data.transit_time);
            $('#editRateModal').modal('show');
        })
    });

    //Hide grouped options in pdf layout
    if ($('#show_hide_select').val() == 'total in') {
        $(".group_origin_charges").addClass('hide');
        $(".group_freight_charges").addClass('hide');
        $(".group_destination_charges").addClass('hide');
    }

    //Mostrar montos totales en Freight
    var sum_freight = 0;
    $(".total_freight_20").each(function() {
        sum_freight = sum_freight + parseFloat($(this).html());
    });
    $(".total_freight_40").each(function() {
        sum_freight = sum_freight + parseFloat($(this).html());
    });
    $(".total_freight_40hc").each(function() {
        sum_freight = sum_freight + parseFloat($(this).html());
    });
    $(".total_freight_40nor").each(function() {
        sum_freight = sum_freight + parseFloat($(this).html());
    });
    $(".total_freight_45").each(function() {
        sum_freight = sum_freight + parseFloat($(this).html());
    });
    $("#sub_total_freight").html(sum_freight + " USD");

    //Mostrar montos totales en Origin
    var sum_origin = 0;
    $(".total_origin_20").each(function() {
        sum_origin = sum_origin + parseFloat($(this).html());
    });
    $(".total_origin_40").each(function() {
        sum_origin = sum_origin + parseFloat($(this).html());
    });
    $(".total_origin_40hc").each(function() {
        sum_origin = sum_origin + parseFloat($(this).html());
    });
    $(".total_origin_40nor").each(function() {
        sum_origin = sum_origin + parseFloat($(this).html());
    });
    $(".total_origin_45").each(function() {
        sum_origin = sum_origin + parseFloat($(this).html());
    });
    $("#sub_total_origin").html(sum_origin + " USD");

    //Mostrar montos totales en destination
    var sum_destination = 0;
    $(".total_destination_20").each(function() {
        sum_destination = sum_destination + parseFloat($(this).html());
    });
    $(".total_destination_40").each(function() {
        sum_destination = sum_destination + parseFloat($(this).html());
    });
    $(".total_destination_40hc").each(function() {
        sum_destination = sum_destination + parseFloat($(this).html());
    });
    $(".total_destination_40nor").each(function() {
        sum_destination = sum_destination + parseFloat($(this).html());
    });
    $(".total_destination_45").each(function() {
        sum_destination = sum_destination + parseFloat($(this).html());
    });
    $("#sub_total_destination").html(sum_destination + " USD");

    $("#total").html(sum_origin + sum_destination + sum_freight + " USD");


    //Edici??n en l??nea
    $('.editable').editable({
        url: '/v2/quotes/charges/update',
        emptytext: 0,
        success: function(response, newValue) {
            //setTimeout(location.reload.bind(location), 3000);
            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-saleterms').editable({
        url: '/v2/quotes/sale/charges/update',
        emptytext: 0,
        success: function(response, newValue) {
            //setTimeout(location.reload.bind(location), 3000);
            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-quote-info').editable({
        url: '/v2/quotes/info/update',
        emptytext: 0,
        success: function(response, newValue) {
            //setTimeout(location.reload.bind(location), 3000);
            var total_volume = parseFloat($('#total-volume').html());
            var weight = parseFloat($('#total-weight').html());
            var chargeable_weight = 0;

            if ($('#quote-type').val() == 'LCL') {
                total_weight = weight / 1000;

                if (total_volume > total_weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = total_weight;
                }
                $('#chargeable-weight').html(chargeable_weight);
            } else if ($('#quote-type').val() == 'AIR') {
                total_volume = total_volume * 166.67;
                if (total_volume > weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = weight;
                }
                $('#chargeable-weight').html(chargeable_weight);
            }
            update_cw(parseFloat(chargeable_weight));
            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-quote-weight').editable({
        url: '/v2/quotes/info/update',
        emptytext: 0,
        success: function(response, newValue) {

            var total_volume = parseFloat($('#total-volume').html());
            var weight = newValue;
            var chargeable_weight = 0;

            if ($('#quote-type').val() == 'LCL') {
                total_weight = weight / 1000;

                if (total_volume > total_weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = total_weight;
                }
                $('#chargeable-weight').html(chargeable_weight);
            } else if ($('#quote-type').val() == 'AIR') {
                total_volume = total_volume * 166.67;
                if (total_volume > weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = weight;
                }
                $('#chargeable-weight').html(chargeable_weight);
            }
            update_cw(parseFloat(chargeable_weight));
            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-quote-volume').editable({
        url: '/v2/quotes/info/update',
        emptytext: 0,
        success: function(response, newValue) {
            //setTimeout(location.reload.bind(location), 3000);
            var total_volume = newValue;
            var weight = parseFloat($('#total-weight').html());
            var chargeable_weight = 0;

            if ($('#quote-type').val() == 'LCL') {
                total_weight = weight / 1000;

                if (total_volume > total_weight) {
                    chargeable_weight = newValue;
                } else {
                    chargeable_weight = total_weight;
                }
                $('#chargeable-weight').html(chargeable_weight);
            } else if ($('#quote-type').val() == 'AIR') {
                total_volume = total_volume * 166.67;
                if (total_volume > weight) {
                    chargeable_weight = newValue;
                } else {
                    chargeable_weight = weight;
                }
                $('#chargeable-weight').html(chargeable_weight);
            }
            update_cw(parseFloat(chargeable_weight));
            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    function update_cw(chargeable_weight) {
        var id = $('#quote-id').val();

        $.ajax({
            type: 'POST',
            url: '/v2/quotes/update/chargeable/' + id,
            data: {
                "chargeable_weight": chargeable_weight,
            },
            success: function(data) {
                //
            }
        });
    };

    //Edici??n en l??nea para montos/markups en LCL/AIR
    $('.editable-lcl-air').editable({
        url: '/v2/quotes/lcl/charges/update',
        emptytext: 0,
        success: function(response, newValue) {
            var sum = 0;
            var sum_total = 0;
            var sub_total = 0;
            var sub_total_markup = 0;
            var sum_total_markup = 0;
            var total_currency = 0;
            if ($(this).attr("data-name") == 'units') {
                value = (parseFloat(newValue) * parseFloat($(this).closest('tr').find('.price_per_unit').html())) + parseFloat($(this).closest('tr').find('.markup').html());
                $(this).closest('tr').find('.total-amount').html(value);
            } else if ($(this).attr("data-name") == 'price_per_unit') {
                value = (parseFloat(newValue) * parseFloat($(this).closest('tr').find('.units').html())) + parseFloat($(this).closest('tr').find('.markup').html());
                $(this).closest('tr').find('.total-amount').html(value);
            } else if ($(this).attr("data-name") == 'markup') {
                value = (parseFloat($(this).closest('tr').find('.price_per_unit').html()) * parseFloat($(this).closest('tr').find('.units').html())) + parseFloat(newValue);
                $(this).closest('tr').find('.total-amount').html(value);
            }

            $(this).editable('setValue', newValue);

            $(this).closest('table').find('.total-amount').each(function() {
                var value = parseFloat($(this).html());
                var currency = $(this).closest('tr').find('.local_currency').html();
                var currency_cfg = $("#currency_id").val();
                /*$.ajax({
                            url: '/api/currency/alphacode/'+currency,
                            dataType: 'json',
                            async: false,
                            success: function (json) {
        
                                if(currency_cfg+json.alphacode == json.api_code){
                                    total_currency = value / json.rates;
                                }else{
                                    total_currency = value / json.rates_eur;
                                }
                                total_currency = total_currency.toFixed(2);
                            }
                        });*/
                total_currency = currencyRateAlphacode(currency, currency_cfg, value);
                sum += parseFloat(total_currency);
            });

            $(this).closest('table').find('.sub_total').html(sum);

            $(this).closest('div.amount_charges').find('.sub_total').each(function() {
                if ($(this).html()) {
                    sub_total = parseFloat($(this).html());
                    sum_total += sub_total;
                }
            });

            //Mostrando total din??mico
            $(this).closest('div.amount_charges').find('.sum_total_amount').html(sum_total.toFixed(2));

            $(this).closest('div.amount_charges').find('.markup').each(function() {
                if ($(this).html()) {
                    sub_total_markup = parseFloat($(this).html());
                    sum_total_markup += sub_total_markup;
                }
            });

            $(this).closest('div.amount_charges').find('.sum_total_markup').html(sum_total_markup.toFixed(2));

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    //Edici??n en l??nea para montos LCL/AIR en Inland
    $('.editable-lcl-air-inland').editable({
        url: '/v2/quotes/lcl/inland/charge/update',
        emptytext: 0,
        success: function(response, newValue) {

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-inland').editable({
        url: '/v2/quotes/inland/update',
        emptytext: 0,
        success: function(response, newValue) {

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-inland-rate').editable({
        url: '/v2/quotes/inland/update',
        emptytext: 0,
        success: function(response, newValue) {
            var code = $(this).attr('data-container');
            var amount = parseFloat($(this).closest('tr').find('.inland_markup_' + code).html());

            if (amount == '') {
                amount = 0;
            }

            if (newValue == '') {
                newValue = 0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la l??nea din??mico
            total = parseFloat(newValue) + amount;

            $(this).closest('tr').find('.total_inland_' + code).html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-inland-markup').editable({
        url: '/v2/quotes/inland/update',
        emptytext: 0,
        success: function(response, newValue) {
            var code = $(this).attr('data-container');
            var amount = parseFloat($(this).closest('tr').find('.inland_amount_' + code).html());

            if (amount == '') {
                amount = 0;
            }

            if (newValue == '') {
                newValue = 0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la l??nea din??mico
            total = parseFloat(newValue) + amount;

            $(this).closest('tr').find('.total_inland_' + code).html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    /** Edici??n en l??nea para montos y markups **/

    $('.editable-amount-rate').editable({
        url: '/v2/quotes/charges/update',
        emptytext: 0,
        success: function(response, newValue) {
            var type = $(this).attr('data-cargo-type');
            var code = $(this).attr('data-container');
            var sum = 0;
            var amount = 0;
            var sum = 0;
            var sum_total = 0;
            var sum_total_rate = 0;
            var total = 0;
            var total_currency = 0;
            var markup = parseFloat($(this).closest('tr').find('.markup_' + code).html());

            if (markup == '') {
                markup = 0;
            }

            if (newValue == '') {
                newValue = 0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la l??nea din??mico
            total = parseFloat(newValue) + markup;
            $(this).closest('tr').find('.total_' + code).html(total);

            //Conversi??n de monedas din??mica
            $(this).closest('table').find('.total_' + code).each(function() {
                var value = parseFloat($(this).html());
                var currency = $(this).closest('tr').find('.local_currency').html();
                var currency_cfg = $("#currency_id").val();

                total_currency = currencyRateAlphacode(currency, currency_cfg, value);
                sum += parseFloat(total_currency);
            });

            //Subtotal din??mico
            $(this).closest('table').find('.total_' + type + '_' + code).html(sum);

            //Calculando total din??mico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_' + code).html()) + parseFloat($(this).closest('div.rates').find('.total_origin_' + code).html()) + parseFloat($(this).closest('div.rates').find('.total_destination_' + code).html());

            //Mostrando total din??mico
            $(this).closest('div.rates').find('.sum_total_' + code).html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.amount_' + code).each(function() {
                console.log($(this).html());
                if (parseFloat($(this).html())) {
                    amount = parseFloat($(this).html());
                } else {
                    amount = 0;
                }
                sum_total_rate += amount;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_amount_' + code).html(sum_total_rate);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-amount-markup').editable({
        url: '/v2/quotes/charges/update',
        emptytext: 0,
        success: function(response, newValue) {
            var type = $(this).attr('data-cargo-type');
            var code = $(this).attr('data-container');
            var sum = 0;
            var amount = 0;
            var sum = 0;
            var sum_total = 0;
            var sum_total_markup = 0;
            var total = 0;
            var total_currency = 0;
            var amount = parseFloat($(this).closest('tr').find('.amount_' + code).html());

            if (amount == '') {
                amount = 0;
            }

            if (newValue == '') {
                newValue = 0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la l??nea din??mico
            total = parseFloat(newValue) + amount;

            $(this).closest('tr').find('.total_' + code).html(total);

            //Conversi??n de monedas din??mica
            $(this).closest('table').find('.total_' + code).each(function() {
                var value = parseFloat($(this).html());
                var currency = $(this).closest('tr').find('.local_currency').html();
                var isOceanFreight = $(this).closest('tr').find('.ocean_freight_rate').html();
                var currency_cfg = $("#currency_id").val();
                if (isOceanFreight == 1) {
                    currency_cfg = $(this).closest('tr').find('.local_currency').html();
                }
                total_currency = currencyRateAlphacode(currency, currency_cfg, value);
                sum += parseFloat(total_currency);
            });

            //Subtotal din??mico
            $(this).closest('table').find('.total_' + type + '_' + code).html(sum);

            //Calculando total din??mico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_' + code).html()) + parseFloat($(this).closest('div.rates').find('.total_origin_' + code).html()) + parseFloat($(this).closest('div.rates').find('.total_destination_' + code).html());

            //Mostrando total din??mico
            $(this).closest('div.rates').find('.sum_total_' + code).html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.markup_' + code).each(function() {
                console.log($(this).html());
                if (parseFloat($(this).html())) {
                    amount = parseFloat($(this).html());
                } else {
                    amount = 0;
                }
                sum_total_markup += amount;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_markup_' + code).html(sum_total_markup);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    //Inline rates charges
    $('.editable-rate-amount-20').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_20').attr('data-value'));
            $(this).closest('tr').find('.total_20').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-markup-20').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_20').attr('data-value'));
            $(this).closest('tr').find('.total_20').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-amount-40').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40').attr('data-value'));
            $(this).closest('tr').find('.total_40').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-markup-40').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40').attr('data-value'));
            $(this).closest('tr').find('.total_40').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-amount-40hc').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40hc').attr('data-value'));
            $(this).closest('tr').find('.total_40hc').html(total);


            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-markup-40hc').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40hc').attr('data-value'));
            $(this).closest('tr').find('.total_40hc').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-amount-40nor').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_40nor').attr('data-value'));
            $(this).closest('tr').find('.total_40nor').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-markup-40nor').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_40nor').attr('data-value'));
            $(this).closest('tr').find('.total_40nor').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-amount-45').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.markup_45').attr('data-value'));
            $(this).closest('tr').find('.total_45').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-rate-markup-45').editable({
        url: '/v2/quotes/rate/charges/update',
        emptytext: 0,
        success: function(response, newValue) {

            total = parseFloat(newValue) + parseFloat($(this).closest('tr').find('.amount_45').attr('data-value'));
            $(this).closest('tr').find('.total_45').html(total);

            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
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
        url: '/v2/quotes/update/details',
        success: function(response, newValue) {
            if (!response) {
                return "Unknown error!";
            }

            if (response.success === false) {
                return response.msg;
            }
        }
    });
});

//Guardar cargos LCL/AIR
$(document).on('click', '.store_charge_lcl', function() {
    var id = $(this).closest("tr").find(".automatic_rate_id").val();
    var surcharge_id = $(this).closest("tr").find(".surcharge_id").val();
    var calculation_type_id = $(this).closest("tr").find(".calculation_type_id").val();
    var units = $(this).closest("tr").find(".units").val();
    var price_per_unit = $(this).closest("tr").find(".price_per_unit").val();
    var total = $(this).closest("tr").find(".total_2").val();
    var markup = $(this).closest("tr").find(".markup").val();
    var type_id = $(this).closest("tr").find(".type_id").val();
    var currency_id = $(this).closest("tr").find(".currency_id").val();
    var number = $(this).closest("tr").find(".number").val();
    var theElement = $(this);
    var sum = 0;

    if (surcharge_id == '' || calculation_type_id == '' || units == '' || price_per_unit == '') {
        notification('There are empty fields. Please verify and try again', 'error');
    } else {
        $(this).closest("table").find('.total-amount').each(function() {
            var sub_total = parseFloat($(this).html());
            var currency = $(this).closest('tr').find('.local_currency').html();
            var currency_cfg = $("#currency_id").val();
            /*$.ajax({
                  url: '/api/currency/alphacode/'+currency,
                  dataType: 'json',
                  async: false,
                  success: function (json) {
      
                      if(currency_cfg+json.alphacode == json.api_code){
                          total_currency = sub_total / json.rates;
                      }else{
                          total_currency = sub_total / json.rates_eur;
                      }
                      total_currency = total_currency.toFixed(2);
                  }
              });*/
            total_currency = currencyRateAlphacode(currency, currency_cfg, sub_total);
            sum += parseFloat(total_currency);
        });

        //Subtotal din??mico
        $(this).closest('table').find('.td_sum_total').html(sum + parseFloat(total));

        $.ajax({
            type: 'POST',
            url: '/v2/quotes/lcl/store/charge',
            data: {
                "automatic_rate_id": id,
                "surcharge_id": surcharge_id,
                "calculation_type_id": calculation_type_id,
                "units": units,
                "price_per_unit": price_per_unit,
                "total": total,
                "markup": markup,
                "type_id": type_id,
                "currency_id": currency_id
            },
            success: function(data) {
                if (data.message == 'Ok') {
                    swal(
                        'Done!',
                        'Charge saved successfully',
                        'success'
                    )
                    $(theElement).closest('tr').remove();
                    //Agregar nuevo tr en freight
                    if (data.type == 3) {
                        $('<tr style="height:40px;">' +
                            '<input name="type" value="1" class="form-control type" type="hidden" /><input name="charge_id" value="' + data.id + '" class="form-control charge_id" type="hidden" /><td class="tds" style="padding-left: 30px"><span class="td-a">' + data.surcharge + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a">' + data.calculation_type + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a units">' + data.units + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a price_per_unit">' + data.rate + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a markup">' + data.markup + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a total-amount">' + data.total + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a local_currency">' + data.currency + '</span>&nbsp;&nbsp;&nbsp;<a class="delete-charge-lcl" style="cursor: pointer;" title="Delete"><span class="fa fa-trash" role="presentation" aria-hidden="true"></span></a></td></span></td>' +
                            '</tr>').insertBefore('.total_freight_' + number);
                    } else if (data.type == 2) { //Agregar nuevo tr en destination
                        $('<tr style="height:40px;">' +
                            '<input name="type" value="1" class="form-control type" type="hidden" /><input name="charge_id" value="' + data.id + '" class="form-control charge_id" type="hidden" /><td class="tds" style="padding-left: 30px"><span class="td-a">' + data.surcharge + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a">' + data.calculation_type + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a units">' + data.units + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a price_per_unit">' + data.rate + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a markup">' + data.markup + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a total-amount">' + data.total + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a local_currency">' + data.currency + '</span>&nbsp;&nbsp;&nbsp;<a class="delete-charge-lcl" style="cursor: pointer;" title="Delete"><span class="fa fa-trash" role="presentation" aria-hidden="true"></span></a></td></td></span></td>' +
                            '</tr>').insertBefore('.total_destination_' + number);
                    } else if (data.type == 1) { //Agregar nuevo tr en origin
                        $('<tr style="height:40px;">' +
                            '<td class="tds" style="padding-left: 30px"><input name="type" value="1" class="form-control type" type="hidden" /><input name="charge_id" value="' + data.id + '" class="form-control charge_id" type="hidden" /><span class="td-a">' + data.surcharge + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a">' + data.calculation_type + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a units">' + data.units + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a price_per_unit">' + data.rate + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a markup">' + data.markup + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a total-amount">' + data.total + '</span></td>' +
                            '<td class="tds"><span class="editable-lcl-air td-a local_currency">' + data.currency + '</span>&nbsp;&nbsp;&nbsp;<a class="delete-charge-lcl" style="cursor: pointer;" title="Delete"><span class="fa fa-trash" role="presentation" aria-hidden="true"></span></a></td>' +
                            '</tr>').insertBefore('.total_origin_' + number);
                    }

                }
                //setTimeout(location.reload.bind(location), 3000);
            }
        });
    }
});

$(document).on('click', '.store_sale_charge', function() {
    var theElement = $(this);
    var containers = ['20DV', '40DV', '40HC', '45HC', '40NOR', '20RF', '40RF', '40HCRF', '20OT', '40OT', '20FR', '40FR'];
    var equipments = {};

    $.each(containers, function(index, value) {
        equipments['c' + value] = theElement.closest("tr").find(".c" + value).val();
    });
    var id = $(this).closest("tr").find(".sale_term_id").val();
    var charge = $(this).closest("tr").find(".charge").val();
    var detail = $(this).closest("tr").find(".detail").val();
    var units = $(this).closest("tr").find(".units").val();
    var amount = $(this).closest("tr").find(".amount").val();
    var total = $(this).closest("tr").find(".total").val();
    var currency_id = $(this).closest("tr").find(".currency_id").val();
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/sale/charge/store',
        data: {
            "sale_term_id": id,
            "charge": charge,
            "detail": detail,
            "equipments": equipments,
            "units": units,
            "amount": amount,
            "total": total,
            "currency_id": currency_id,
        },
        success: function(data) {
            if (data.message == 'Ok') {
                swal(
                    'Success!',
                    'The record has been created.',
                    'success'
                )
            }
        }
    });
});

//Guardar cargos FCL
$(document).on('click', '.store_charge', function() {
    var id = $(this).closest("tr").find(".automatic_rate_id").val();
    var number = $(this).closest("tr").find(".number").val();
    var theElement = $(this);
    var surcharge_id = $(this).closest("tr").find(".surcharge_id").val();
    var calculation_type_id = $(this).closest("tr").find(".calculation_type_id").val();
    var containers = ['20DV', '40DV', '40HC', '45HC', '40NOR', '20RF', '40RF', '40HCRF', '20OT', '40OT', '20FR', '40FR'];
    var equipments = {};

    //Creando variables para guardar en BD
    $.each(containers, function(index, value) {
        window['hide_' + value] = theElement.closest("tr").find(".hide_" + value).val();
        window['amount_' + value] = theElement.closest("tr").find(".amount_c" + value).val();
        window['markup_' + value] = theElement.closest("tr").find(".markup_m" + value).val();
        window["total_" + value] = 0;
        equipments['hide_' + value] = theElement.closest("tr").find(".hide_" + value).val();
        equipments['amount_' + value] = theElement.closest("tr").find(".amount_c" + value).val();
        equipments['markup_' + value] = theElement.closest("tr").find(".markup_m" + value).val();
    });

    var type_id = $(this).closest("tr").find(".type_id").val();
    var currency_id = $(this).closest("tr").find(".currency_id").val();

    $.ajax({
        type: 'POST',
        url: '/v2/quotes/charge/store',
        data: {
            "automatic_rate_id": id,
            "surcharge_id": surcharge_id,
            "calculation_type_id": calculation_type_id,
            "equipments": equipments,
            "type_id": type_id,
            "currency_id": currency_id
        },
        beforeSend: function() {
            notification('Saving data &nbsp;<i class="fa fa-spinner fa-spin"></i>');
        },
        success: function(data) {
            if (data.message == 'Ok') {
                toastr.clear();
                swal(
                    'Done!',
                    'Charge saved successfully',
                    'success'
                )
            }
            $(theElement).closest('tr').remove();

            var amounts = $.parseJSON(data.charge.amount);
            var markups = $.parseJSON(data.charge.markups);

            //Creando variables para totalizar
            var table_first = '';
            var table_middle = '';
            var table_last = '';
            var currency = '';
            var currency_cfg = '';

            $.each(containers, function(index, value) {
                window["amount_from_db_" + value] = amounts['c' + value] || 0;
                window["markup_from_db_" + value] = markups['m' + value] || 0;
                window["total_" + value] = parseFloat(window["amount_from_db_" + value]) + parseFloat(window["markup_from_db_" + value]);
            });

            //Si es Freight
            if (type_id == 3) {
                table_first = '<tr style="height:40px;">' + '<input name="type" value="1" class="form-control type" type="hidden" /><td class="tds" style="padding-left: 30px"><input name="charge_id" value="' + data.id + '" class="form-control charge_id" type="hidden" /><span class="td-a">' + data.surcharge + '</span></td>' + '<td class="tds"><span class="td-a">' + data.calculation_type + '</span></td>';
                table_last = '<td class="tds"><span class="td-a">' + data.currency + '</span>&nbsp;&nbsp;&nbsp;<a class="delete-charge" style="cursor: pointer;" title="Delete"><span class="fa fa-trash" role="presentation" aria-hidden="true"></span></a></td>' + '</tr>';
                table_middle = '';
                $.each(containers, function(index, value) {
                    table_middle += '<td ' + window["hide_" + value] + ' class="tds"><span class="td-a">' + window["amount_from_db_" + value] + '</span> + <span class="td-a">' + window["markup_from_db_" + value] + '</span> <i class="la la-caret-right arrow-down"></i> <span class="td-a">' + window["total_" + value] + '</span></td>';
                });

                //Uniendo variables
                $(table_first + table_middle + table_last).insertBefore('.total_freight_' + number);

                $.each(containers, function(index, value) {
                    $('.total_freight_' + number).find('.total_freight_' + value).html('');
                    $('.total_freight_' + number).find('.total_freight_' + value).html(data.sum_total_freight[value]);
                });

                currency = $(theElement).closest('tr').find('.local_currency').val();
                currency_cfg = $("#currency_id").val();

                //Creando variables para sumatorias de totales y subtotales
                $.each(containers, function(index, value) {
                    window['subtotal_c' + value] = 0;
                    window["amount_currency_" + value] = 0;
                    window["subtotal_m" + value] = 0;
                    window["markup_currency_" + value] = 0;
                    window["sum_total_amount_" + value] = 0;
                    window["sum_total_markup_" + value] = 0;
                });

                $.each(containers, function(index, value) {
                    window["amount_currency_" + value] = currencyRate(currency, currency_cfg, window["amount_from_db_" + value]);
                    window["markup_currency_" + value] = currencyRate(currency, currency_cfg, window["markup_from_db_" + value]);
                    console.log(window["amount_currency_" + value]);
                    //Calculando subtotal de rates
                    window["subtotal_c" + value] = parseFloat($('.total_freight_' + number).closest('div.rates').find('.subtotal_c' + value + '_freight').val());
                    $('.total_freight_' + number).closest('div.rates').find('.subtotal_c' + value + '_freight').val(window["subtotal_c" + value] + parseFloat(window["amount_currency_" + value]));

                    //Calculando sum de subtotal de rates
                    window["sum_total_amount_" + value] = parseFloat($('.total_freight_' + number).closest('div.rates').find('.sum_total_amount_' + value).html());
                    $('.total_freight_' + number).closest('div.rates').find('.sum_total_amount_' + value).html(parseFloat(window["amount_currency_" + value]) + window["sum_total_amount_" + value]);

                    //Calculando subtotal de markups
                    window["subtotal_m" + value] = parseFloat($('.total_freight_' + number).closest('div.rates').find('.subtotal_m' + value + '_freight').val());
                    $('.total_freight_' + number).closest('div.rates').find('.subtotal_m' + value + '_freight').val(window["subtotal_m" + value] + parseFloat(window["markup_currency_" + value]));

                    //Calculando sum de subtotal de markups
                    window["sum_total_markup_" + value] = parseFloat($('.total_freight_' + number).closest('div.rates').find('.sum_total_markup_' + value).html());
                    $('.total_freight_' + number).closest('div.rates').find('.sum_total_markup_' + value).html(parseFloat(window["markup_currency_" + value]) + window["sum_total_markup_" + value]);

                    //Mostrando total din??mico
                    $('.total_freight_' + number).closest('div.rates').find('.sum_total_' + value).html(data.sum_total[value]);
                });
            }

            //Si es Destination
            if (type_id == 2) {
                table_first = '<tr style="height:40px;">' + '<input name="type" value="1" class="form-control type" type="hidden" /><td class="tds" style="padding-left: 30px"><input name="charge_id" value="' + data.id + '" class="form-control charge_id" type="hidden" /><span class="td-a">' + data.surcharge + '</span></td>' + '<td class="tds"><span class="td-a">' + data.calculation_type + '</span></td>';
                table_last = '<td class="tds"><span class="td-a">' + data.currency + '</span>&nbsp;&nbsp;&nbsp;<a class="delete-charge" style="cursor: pointer;" title="Delete"><span class="fa fa-trash" role="presentation" aria-hidden="true"></span></a></td>' + '</tr>';
                table_middle = '';
                $.each(containers, function(index, value) {
                    table_middle += '<td ' + window["hide_" + value] + ' class="tds"><span class="td-a">' + window["amount_from_db_" + value] + '</span> + <span class="td-a">' + window["markup_from_db_" + value] + '</span> <i class="la la-caret-right arrow-down"></i> <span class="td-a">' + window["total_" + value] + '</span></td>';
                });

                //Uniendo variables
                $(table_first + table_middle + table_last).insertBefore('.total_destination_' + number);

                $.each(containers, function(index, value) {
                    $('.total_destination_' + number).find('.total_destination_' + value).html('');
                    $('.total_destination_' + number).find('.total_destination_' + value).html(data.sum_total_destination[value]);
                });

                currency = $(theElement).closest('tr').find('.local_currency').val();
                currency_cfg = $("#currency_id").val();

                //Creando variables para sumatorias de totales y subtotales
                $.each(containers, function(index, value) {
                    window['subtotal_c' + value] = 0;
                    window["amount_currency_" + value] = 0;
                    window["subtotal_m" + value] = 0;
                    window["markup_currency_" + value] = 0;
                    window["sum_total_amount_" + value] = 0;
                    window["sum_total_markup_" + value] = 0;
                });

                $.each(containers, function(index, value) {
                    window["amount_currency_" + value] = currencyRate(currency, currency_cfg, window["amount_from_db_" + value]);
                    window["markup_currency_" + value] = currencyRate(currency, currency_cfg, window["markup_from_db_" + value]);

                    //Calculando subtotal de rates
                    window["subtotal_c" + value] = parseFloat($('.total_destination_' + number).closest('div.rates').find('.subtotal_c' + value + '_destination').val());
                    $('.total_destination_' + number).closest('div.rates').find('.subtotal_c' + value + '_destination').val(window["subtotal_c" + value] + parseFloat(window["amount_currency_" + value]));

                    //Calculando sum de subtotal de rates
                    window["sum_total_amount_" + value] = parseFloat($('.total_destination_' + number).closest('div.rates').find('.sum_total_amount_' + value).html());
                    $('.total_destination_' + number).closest('div.rates').find('.sum_total_amount_' + value).html(parseFloat(window["amount_currency_" + value]) + window["sum_total_amount_" + value]);

                    //Calculando subtotal de markups
                    window["subtotal_m" + value] = parseFloat($('.total_destination_' + number).closest('div.rates').find('.subtotal_m' + value + '_destination').val());
                    $('.total_destination_' + number).closest('div.rates').find('.subtotal_m' + value + '_destination').val(window["subtotal_m" + value] + parseFloat(window["markup_currency_" + value]));

                    //Calculando sum de subtotal de markups
                    window["sum_total_markup_" + value] = parseFloat($('.total_destination_' + number).closest('div.rates').find('.sum_total_markup_' + value).html());
                    $('.total_destination_' + number).closest('div.rates').find('.sum_total_markup_' + value).html(parseFloat(window["markup_currency_" + value]) + window["sum_total_markup_" + value]);

                    //Mostrando total din??mico
                    $('.total_destination_' + number).closest('div.rates').find('.sum_total_' + value).html(data.sum_total[value]);
                });
            }

            //Si es Origin
            if (type_id == 1) {
                table_first = '<tr style="height:40px;">' + '<input name="type" value="1" class="form-control type" type="hidden" /><td class="tds" style="padding-left: 30px"><input name="charge_id" value="' + data.id + '" class="form-control charge_id" type="hidden" /><span class="td-a">' + data.surcharge + '</span></td>' + '<td class="tds"><span class="td-a">' + data.calculation_type + '</span></td>';
                table_last = '<td class="tds"><span class="td-a">' + data.currency + '</span>&nbsp;&nbsp;&nbsp;<a class="delete-charge" style="cursor: pointer;" title="Delete"><span class="fa fa-trash" role="presentation" aria-hidden="true"></span></a></td>' + '</tr>';
                table_middle = '';
                $.each(containers, function(index, value) {
                    table_middle += '<td ' + window["hide_" + value] + ' class="tds"><span class="td-a">' + window["amount_from_db_" + value] + '</span> + <span class="td-a">' + window["markup_from_db_" + value] + '</span> <i class="la la-caret-right arrow-down"></i> <span class="td-a">' + window["total_" + value] + '</span></td>';
                });

                //Uniendo variables
                $(table_first + table_middle + table_last).insertBefore('.total_origin_' + number);

                $.each(containers, function(index, value) {
                    $('.total_origin_' + number).find('.total_origin_' + value).html('');
                    $('.total_origin_' + number).find('.total_origin_' + value).html(data.sum_total_origin[value]);
                });

                currency = $(theElement).closest('tr').find('.local_currency').val();
                currency_cfg = $("#currency_id").val();

                //Creando variables para sumatorias de totales y subtotales
                $.each(containers, function(index, value) {
                    window['subtotal_c' + value] = 0;
                    window["amount_currency_" + value] = 0;
                    window["subtotal_m" + value] = 0;
                    window["markup_currency_" + value] = 0;
                    window["sum_total_amount_" + value] = 0;
                    window["sum_total_markup_" + value] = 0;
                });

                $.each(containers, function(index, value) {
                    window["amount_currency_" + value] = currencyRate(currency, currency_cfg, window["amount_from_db_" + value]);
                    window["markup_currency_" + value] = currencyRate(currency, currency_cfg, window["markup_from_db_" + value]);

                    //Calculando subtotal de rates
                    window["subtotal_c" + value] = parseFloat($('.total_origin_' + number).closest('div.rates').find('.subtotal_c' + value + '_origin').val());
                    $('.total_origin_' + number).closest('div.rates').find('.subtotal_c' + value + '_origin').val(window["subtotal_c" + value] + parseFloat(window["amount_currency_" + value]));

                    //Calculando sum de subtotal de rates
                    window["sum_total_amount_" + value] = parseFloat($('.total_origin_' + number).closest('div.rates').find('.sum_total_amount_' + value).html());
                    $('.total_origin_' + number).closest('div.rates').find('.sum_total_amount_' + value).html(parseFloat(window["amount_currency_" + value]) + window["sum_total_amount_" + value]);

                    //Calculando subtotal de markups
                    window["subtotal_m" + value] = parseFloat($('.total_origin_' + number).closest('div.rates').find('.subtotal_m' + value + '_origin').val());
                    $('.total_origin_' + number).closest('div.rates').find('.subtotal_m' + value + '_origin').val(window["subtotal_m" + value] + parseFloat(window["markup_currency_" + value]));

                    //Calculando sum de subtotal de markups
                    window["sum_total_markup_" + value] = parseFloat($('.total_origin_' + number).closest('div.rates').find('.sum_total_markup_' + value).html());
                    $('.total_origin_' + number).closest('div.rates').find('.sum_total_markup_' + value).html(parseFloat(window["markup_currency_" + value]) + window["sum_total_markup_" + value]);

                    //Mostrando total din??mico
                    $('.total_origin_' + number).closest('div.rates').find('.sum_total_' + value).html(data.sum_total[value]);
                });
            }
            //setTimeout(location.reload.bind(location), 3000);
        }
    });
});

//Borrar quote
$(document).on('click', '#delete-quote-v2', function() {
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
                type: 'delete',
                url: '/api/quote/' + id + '/destroy',
                success: function(data) {
                    swal(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    console.log(data.message);
                    $(theElement).closest('tr').remove();
                }
            });

        }
    });
});

//Duplicar quote
$(document).on('click', '#duplicate-quote-v2', function() {
    var id = $(this).attr('data-quote-id');
    var theElement = $(this);
    $.ajax({
        type: 'post',
        url: '/api/quotes/' + id + '/duplicate',
        success: function(data) {
            console.log(data.message);
            location.reload();
            //REFRESH TABLE?
        }
    });
});

$(document).on('click', '#delete-quote-show', function() {
    var id = $(this).attr('data-quote-show-id');
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
                url: '/v2/quotes/delete/' + id,
                success: function(data) {
                    swal(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    window.location.href = '../';
                }
            });

        }
    });
});

//Borrar rates
$(document).on('click', '.delete-rate', function() {
    var id = $(this).attr('data-rate-id');
    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "Please confirm!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, I am sure!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: 'GET',
                url: '/v2/quotes/delete/rate/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
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

//Guardar Sale Terms

$('#saveSaleTerm').on('click', function(e) {
    e.preventDefault();
    var origin_port = $("#origin_port_select").val();
    var destination_port = $("#destination_port_select").val();
    var origin_airport = $("#origin_airport_select").val();
    var destination_airport = $("#destination_airport_select").val();

    var form = $(this).parents('form');

    if (origin_port != '' || destination_port != '' || origin_airport != '' || destination_airport != '') {
        form.submit();
    } else {
        notification('You must select a port/airport', 'error');
    }

});

//Borrar sale terms
$(document).on('click', '.delete-sale-term', function() {
    var id = $(this).attr('data-saleterm-id');
    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "Please confirm!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, I am sure!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: 'GET',
                url: '/v2/quotes/delete/saleterm/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
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

//Borrar cargo FCL
$(document).on('click', '.delete-charge', function() {
    var id = $(this).closest('tr').find('.charge_id').val();
    var type = $(this).closest('tr').find('.type').val();
    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "Please confirm!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, I am sure!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: 'GET',
                data: {
                    'type': type,
                },
                url: '/v2/quotes/delete/charge/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
                        swal(
                            'Updated!',
                            'The charge has been deleted.',
                            'success'
                        )
                    }
                    if (data.type == 1) {
                        $(theElement).closest('tr').remove();
                    } else {
                        setTimeout(location.reload.bind(location), 3000);
                    }
                }
            });
        }
    });
});

//Borrar cargos SaleTerms
$(document).on('click', '.delete-saleterm-charge', function() {
    var id = $(this).closest('tr').find('.saleterm_charge_id').val();
    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "Please confirm!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, I am sure!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: 'GET',
                url: '/v2/quotes/delete/saleterm/charge/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
                        swal(
                            'Updated!',
                            'The charge has been deleted.',
                            'success'
                        )
                    }
                    $(theElement).closest('tr').remove();
                }
            });
        }
    });
});

//Borrar cargo LCL/AIR
$(document).on('click', '.delete-charge-lcl', function() {
    var id = $(this).closest('tr').find('.charge_id').val();
    var type = $(this).closest('tr').find('.type').val();
    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "Please confirm!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, I am sure!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: 'GET',
                data: {
                    'type': type,
                },
                url: '/v2/quotes/lcl/delete/charge/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
                        swal(
                            'Updated!',
                            'The charge has been deleted.',
                            'success'
                        )
                    }
                    if (data.type == 1) {
                        $(theElement).closest('tr').remove();
                    } else {
                        //setTimeout(location.reload.bind(location), 3000); 
                    }
                }
            });
        }
    });
});

//Borrar inland
$(document).on('click', '.delete-inland', function() {
    var id = $(this).closest('ul').find('.inland_id').val();
    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "Please confirm!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, I am sure!'
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: 'GET',
                url: '/v2/quotes/delete/inland/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
                        swal(
                            'Updated!',
                            'The charge has been deleted.',
                            'success'
                        )
                    }
                    $(theElement).closest('span').find('.tab-content').remove();
                }
            });
        }
    });
});

//Editar payments
$(document).on('click', '#edit-payments', function() {
    $(".payment_conditions_span").attr('hidden', 'true');
    $(".payment_conditions_textarea").removeAttr('hidden');
    $("#update_payments").removeAttr('hidden');
});

//Cancelar editar payments
$(document).on('click', '#cancel-payments', function() {
    $(".payment_conditions_span").removeAttr('hidden');
    $(".payment_conditions_textarea").attr('hidden', 'true');
    $("#update_payments").attr('hidden', 'true');
});

//Actualizar payments
$(document).on('click', '#update-payments', function() {
    var id = $(".id").val();
    var payments = tinymce.get("payment_conditions").getContent();
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/update/payments/' + id,
        data: {
            'payments': payments,
        },
        success: function(data) {
            if (data.message == 'Ok') {
                swal(
                    'Updated!',
                    'The payment conditions has been updated.',
                    'success'
                )

                $(".payment_conditions_span").html(data.quote['payment_conditions']);
                $(".payment_conditions_span").removeAttr('hidden');
                $(".payment_conditions_textarea").attr('hidden', 'true');
                $("#update_payments").attr('hidden', 'true');
            }
        }
    });
});

//Editar terms
$(document).on('click', '#edit-terms', function() {
    $(".terms_and_conditions_span").attr('hidden', 'true');
    $(".terms_and_conditions_textarea").removeAttr('hidden');
    $("#update_terms").removeAttr('hidden');
});

$(document).on('click', '#edit-terms-english', function() {
    $(".terms_and_conditions_english_span").attr('hidden', 'true');
    $(".terms_and_conditions_english_textarea").removeAttr('hidden');
    $("#update_terms_english").removeAttr('hidden');
});

$(document).on('click', '#edit-terms-portuguese', function() {
    $(".terms_and_conditions_portuguese_span").attr('hidden', 'true');
    $(".terms_and_conditions_portuguese_textarea").removeAttr('hidden');
    $("#update_terms_portuguese").removeAttr('hidden');
});

//Cancelar editar terms
$(document).on('click', '#cancel-terms', function() {
    $(".terms_and_conditions_span").removeAttr('hidden');
    $(".terms_and_conditions_textarea").attr('hidden', 'true');
    $("#update_terms").attr('hidden', 'true');
});

$(document).on('click', '#cancel-terms-english', function() {
    $(".terms_and_conditions_english_span").removeAttr('hidden');
    $(".terms_and_conditions_english_textarea").attr('hidden', 'true');
    $("#update_terms_english").attr('hidden', 'true');
});

$(document).on('click', '#cancel-terms-portuguese', function() {
    $(".terms_and_conditions_portuguese_span").removeAttr('hidden');
    $(".terms_and_conditions_portuguese_textarea").attr('hidden', 'true');
    $("#update_terms_portuguese").attr('hidden', 'true');
});

//Actualizar terms
$(document).on('click', '#update-terms', function() {
    var id = $(".id").val();
    var terms = tinymce.get("terms_and_conditions").getContent();
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/update/terms/' + id,
        data: {
            'name': 'terms_and_conditions',
            'terms': terms,
        },
        success: function(data) {
            if (data.message == 'Ok') {
                swal(
                    'Updated!',
                    'The terms and conditions has been updated.',
                    'success'
                )

                $(".terms_and_conditions_span").html(data.quote['terms_and_conditions']);
                $(".terms_and_conditions_span").removeAttr('hidden');
                $(".terms_and_conditions_textarea").attr('hidden', 'true');
                $("#update_terms").attr('hidden', 'true');
            }
        }
    });
});

$(document).on('click', '#update-terms-english', function() {
    var id = $(".id").val();
    var terms = tinymce.get("terms_and_conditions_english").getContent();
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/update/terms/' + id,
        data: {
            'name': 'terms_english',
            'terms': terms,
        },
        success: function(data) {
            if (data.message == 'Ok') {
                swal(
                    'Updated!',
                    'The terms and conditions has been updated.',
                    'success'
                )

                $(".terms_and_conditions_english_span").html(data.quote['terms_english']);
                $(".terms_and_conditions_english_span").removeAttr('hidden');
                $(".terms_and_conditions_english_textarea").attr('hidden', 'true');
                $("#update_terms_english").attr('hidden', 'true');
            }
        }
    });
});


$(document).on('click', '#update-terms-portuguese', function() {
    var id = $(".id").val();
    var terms = tinymce.get("terms_and_conditions_portuguese").getContent();
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/update/terms/' + id,
        data: {
            'name': 'terms_portuguese',
            'terms': terms,
        },
        success: function(data) {
            if (data.message == 'Ok') {
                swal(
                    'Updated!',
                    'The terms and conditions has been updated.',
                    'success'
                )

                $(".terms_and_conditions_portuguese_span").html(data.quote['terms_portuguese']);
                $(".terms_and_conditions_portuguese_span").removeAttr('hidden');
                $(".terms_and_conditions_portuguese_textarea").attr('hidden', 'true');
                $("#update_terms_portuguese").attr('hidden', 'true');
            }
        }
    });
});

//Mostrar inputs Origin/Destination address
$(document).on('change', '.delivery_type', function(e) {

    if ($(this).val() == 1) {
        $(".origin_address_label").addClass('hide');
        $(".origin_address_span").addClass('hide');
        $("#origin_address").attr('hidden', true);
        $(".destination_address_label").addClass('hide');
        $(".destination_address_span").addClass('hide');
        $("#destination_address").attr('hidden', true);
        $("#origin_address").val('');
        $("#destination_address").val('');
    }
    if ($(this).val() == 2) {

        $(".origin_address_label").addClass('hide');
        $(".origin_address_span").addClass('hide');
        $("#origin_address").attr('hidden', true);
        $(".destination_address_label").removeClass('hide');
        $(".destination_address_span").removeClass('hide');
        $("#destination_address").removeAttr('hidden');
        $("#origin_address").val('');
    }
    if ($(this).val() == 3) {
        $(".origin_address_label").removeClass('hide');
        $(".origin_address_span").removeClass('hide');
        $("#origin_address").removeAttr('hidden');
        $(".destination_address_label").addClass('hide');
        $(".destination_address_span").addClass('hide');
        $("#destination_address").attr('hidden', true);
        $("#destination_address").val('');
    }
    if ($(this).val() == 4) {
        $(".origin_address_label").removeClass('hide');
        $(".origin_address_span").removeClass('hide');
        $("#origin_address").removeAttr('hidden');
        $(".destination_address_label").removeClass('hide');
        $(".destination_address_span").removeClass('hide');
        $("#destination_address").removeAttr('hidden');
    }
});

//Habilitar edicion campos de la cotizacion
$(document).on('click', '#edit-quote', function() {
    $(".quote_id_span").attr('hidden', 'true');
    $(".company_span").attr('hidden', 'true');
    $(".status_span").attr('hidden', 'true');
    $(".delivery_type_span").attr('hidden', 'true');
    $(".price_level_span").attr('hidden', 'true');
    $(".type_span").attr('hidden', 'true');
    $(".incoterm_id_span").attr('hidden', 'true');
    $(".commodity_span").attr('hidden', 'true');
    $(".kind_of_cargo_span").attr('hidden', 'true');
    $(".contact_id_span").attr('hidden', 'true');
    $(".validity_span").attr('hidden', 'true');
    $(".user_id_span").attr('hidden', 'true');
    $(".date_issued_span").attr('hidden', 'true');
    $(".equipment_span").attr('hidden', 'true');
    $(".quote_id").removeAttr('hidden');
    $(".company_id").removeAttr('hidden');
    $(".quote-type ").removeAttr('hidden');
    $(".status").removeAttr('hidden');
    $(".delivery_type").removeAttr('hidden');
    $(".incoterm_id").removeAttr('hidden');
    $(".commodity").removeAttr('hidden');
    $(".kind_of_cargo").removeAttr('hidden');
    $(".contact_id").removeAttr('hidden');
    $(".contact_id").prop('disabled', false);
    $(".validity").removeAttr('hidden');
    $(".user_id").removeAttr('hidden');
    $(".equipment").removeAttr('hidden');
    $(".date_issued").removeAttr('hidden');
    $(".price_id").removeAttr('hidden');
    $("#update_buttons").removeAttr('hidden');
    $("#edit_li").attr('hidden', 'true');
    if ($(".kind_of_cargo").val() == 'Pharma') {
        $(".gdp_span").attr('hidden', 'true');
        $(".gdp").removeAttr('hidden');
    }
    if ($(".gdp").val() == 1) {
        $(".risk_level").removeAttr('hidden');
        $(".risk_level_span").attr('hidden', 'true');
    }
    if ($(".delivery_type").val() == 3 || $(".delivery_type").val() == 4) {
        $(".origin_address_span").attr('hidden', 'true');
        $(".origin_address").removeAttr('hidden');
    }
    if ($(".delivery_type").val() == 2 || $(".delivery_type").val() == 4) {
        $(".destination_address_span").attr('hidden', 'true');
        $(".destination_address").removeAttr('hidden');
    }

    $(".quote-type").select2();
    $(".status").select2();
    $(".kind_of_cargo").select2();
    $(".company_id").select2({
        placeholder: "Select an option",
        minimumInputLength: 2,
        ajax: {
            url: '/companies/search',
            dataType: 'json',
            data: function(params) {
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
        }
    });
    $(".delivery_type").select2();
    $(".incoterm_id").select2();
    $(".contact_id").select2();
    $(".user_id").select2();
    $(".price_id").select2();
    $(".equipment").select2();
    $(".gdp").select2();
});

//Cancelar actualizacion de datos de cotizacion
$(document).on('click', '#cancel', function() {
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
    $(".quote_id").attr('hidden', 'true');
    $(".company_id").attr('hidden', 'true');
    $(".quote-type ").attr('hidden', 'true');
    $(".status").attr('hidden', 'true');
    $(".delivery_type").attr('hidden', 'true');
    $(".incoterm_id").attr('hidden', 'true');
    $(".commodity").attr('hidden', 'true');
    $(".kind_of_cargo").attr('hidden', 'true');
    $(".contact_id").attr('hidden', 'true');
    $(".validity").attr('hidden', 'true');
    $(".user_id").attr('hidden', 'true');
    $(".date_issued").attr('hidden', 'true');
    $(".equipment").attr('hidden', 'true');
    $(".price_id").attr('hidden', 'true');
    $("#update_buttons").attr('hidden', 'true');
    $("#edit_li").removeAttr('hidden');
    if ($(".kind_of_cargo").val() == 'Pharma') {
        $(".gdp").attr('hidden', 'true');
        $(".gdp_span").removeAttr('hidden');
    }
    if ($(".gdp").val() == 1) {
        $(".risk_level").attr('hidden', 'true');
        $(".risk_level_span").removeAttr('hidden');
    }
    if ($(".delivery_type").val() == 3 || $(".delivery_type").val() == 4) {
        $(".origin_address").attr('hidden', 'true');
        $(".origin_address_span").removeAttr('hidden');
    }
    if ($(".delivery_type").val() == 2 || $(".delivery_type").val() == 4) {
        $(".destination_address").attr('hidden', 'true');
        $(".destination_address_span").removeAttr('hidden');
    }

    if ($('select').data('select2')) {
        $('select').select2('destroy');
    }
});

//Actualizar datos de cotizaci??n
$(document).on('click', '#update', function() {
    var id = $(".id").val();
    var quote_id = $(".quote_id").val();
    var company_id = $(".company_id").val();
    var type = $(".quote-type").val();
    var status = $(".status").val();
    var delivery_type = $(".delivery_type").val();
    var incoterm_id = $(".incoterm_id").val();
    var contact_id = $(".contact_id").val();
    var validity = $(".validity").val();
    var equipment = $(".equipment").val();
    var user_id = $(".user_id").val();
    var date_issued = $(".date_issued").val();
    var price_id = $(".price_id").val();
    var commodity = $(".commodity").val();
    var kind_of_cargo = $(".kind_of_cargo").val();
    var origin_address = $(".origin_address").val();
    var destination_address = $(".destination_address").val();
    var gdp = 0;
    var risk_level = '';
    if (kind_of_cargo == 'Pharma') {
        gdp = $(".gdp").val();
        risk_level = $(".risk_level").val();
    }

    $.ajax({
        type: 'POST',
        url: '/v2/quotes/update/' + id,
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
            'gdp': gdp,
            'risk_level': risk_level,
            'origin_address': origin_address,
            'destination_address': destination_address,
        },
        success: function(data) {
            if (data.message == 'Ok') {
                swal(
                    'Updated!',
                    'Your quote has been updated.',
                    'success'
                )
                var incoterm = data.quote['incoterm_id'];
                var delivery_type = data.quote['delivery_type'];

                if (incoterm == 1) {
                    incoterm = 'EWX';
                } else if (incoterm == 2) {
                    incoterm = 'FAS';
                } else if (incoterm == 3) {
                    incoterm = 'FCA';
                } else if (incoterm == 4) {
                    incoterm = 'FOB';
                } else if (incoterm == 5) {
                    incoterm = 'CFR';
                } else if (incoterm == 6) {
                    incoterm = 'CIF';
                } else if (incoterm == 7) {
                    incoterm = 'CIP';
                } else if (incoterm == 8) {
                    incoterm = 'DAT';
                } else if (incoterm == 10) {
                    incoterm = 'DAP';
                } else if (incoterm == 11) {
                    incoterm = 'DDP';
                } else {
                    incoterm = 'DDU';
                }

                if (delivery_type == 1 && (type == 'FCL' || type == 'LCL')) {
                    delivery_type = 'Port to Port';
                } else if (delivery_type == 2 && (type == 'FCL' || type == 'LCL')) {
                    delivery_type = 'Port to Door';
                } else if (delivery_type == 3 && (type == 'FCL' || type == 'LCL')) {
                    delivery_type = 'Door to Port';
                } else if (delivery_type == 4 && (type == 'FCL' || type == 'LCL')) {
                    delivery_type = 'Door to Door';
                } else if (delivery_type == 1 && type == 'AIR') {
                    delivery_type = 'Airport to Airport';
                } else if (delivery_type == 2 && type == 'AIR') {
                    delivery_type = 'Airport to Door';
                } else if (delivery_type == 3 && type == 'AIR') {
                    delivery_type = 'Door to Airport';
                } else {
                    delivery_type = 'Door to Door';
                }


                $(".quote-type").val(data.quote['type']);
                $(".type_span").html(data.quote['type']);
                if (data.quote['custom_quote_id'] != '') {
                    $(".quote_id").val(data.quote['custom_quote_id']);
                    $(".quote_id_span").html(data.quote['custom_quote_id']);
                } else {
                    $(".quote_id").val(data.quote['quote_id']);
                    $(".quote_id_span").html(data.quote['quote_id']);
                }
                $(".company_id").val(data.quote['company_id']);
                $(".company_span").html(data.company_name);
                $(".status").val(data.quote['status']);
                $(".status_span").html(data.quote['status'] + ' <i class="fa fa-check"></i>');
                $(".status_span").addClass('Status_' + data.quote['status']);
                $(".delivery_type").val(data.quote['delivery_type']);
                $(".delivery_type_span").html(delivery_type);
                $(".incoterm_id").val(data.quote['incoterm_id']);
                $(".incoterm_id_span").html(incoterm);
                $(".commodity").val(data.quote['commodity']);
                $(".commodity_span").html(data.quote['commodity']);
                $(".gdp").val(data.quote['gdp']);
                $(".gdp_span").html(data.gdp);
                $(".risk_level").val(data.quote['risk_level']);
                $(".risk_level_span").html(data.quote['risk_level']);
                $(".kind_of_cargo").val(data.quote['kind_of_cargo']);
                $(".kind_of_cargo_span").html(data.quote['kind_of_cargo']);
                $(".origin_address_span").html(data.quote['origin_address']);
                $(".destination_address_span").html(data.quote['destination_address']);
                $(".equipment").val(data.quote['equipment']);
                $(".equipment_span").empty();
                var length = $.parseJSON(data.quote['equipment']).length;
                $.each($.parseJSON(data.quote['equipment']), function(index, value) {
                    if (index === (length - 1)) {
                        $(".equipment_span").append(value);
                    } else {
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
                $(".validity").val(data.quote['validity_start'] + '/' + data.quote['validity_end']);
                $(".validity_span").html(data.quote['validity_start'] + '/' + data.quote['validity_end']);

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
                $(".quote_id").attr('hidden', 'true');
                $(".company_id").attr('hidden', 'true');
                $(".quote-type").attr('hidden', 'true');
                $(".status").attr('hidden', 'true');
                $(".delivery_type").attr('hidden', 'true');
                $(".incoterm_id").attr('hidden', 'true');
                $(".commodity").attr('hidden', 'true');
                $(".kind_of_cargo").attr('hidden', 'true');
                $(".contact_id").attr('hidden', 'true');
                $(".validity").attr('hidden', 'true');
                $(".user_id").attr('hidden', 'true');
                $(".date_issued").attr('hidden', 'true');
                $(".price_id").attr('hidden', 'true');
                $(".equipment").attr('hidden', 'true');
                $("#update_buttons").attr('hidden', 'true');
                $("#edit_li").removeAttr('hidden');
                if ($(".kind_of_cargo").val() == 'Pharma') {
                    $(".gdp").attr('hidden', 'true');
                    $(".gdp_span").removeAttr('hidden');
                }
                if ($(".gdp").val() == 1) {
                    $(".risk_level").attr('hidden', 'true');
                    $(".risk_level_span").removeAttr('hidden');
                }
                //if($(".origin_address").val()!=''){
                $(".origin_address").attr('hidden', 'true');
                $(".origin_address_span").removeAttr('hidden');
                //}
                //if($(".destination_address").val()!=''){
                $(".destination_address").attr('hidden', 'true');
                $(".destination_address_span").removeAttr('hidden');
                //}
                if ($('select').data('select2')) {
                    $('select').select2('destroy');
                }

                //Refresh page after 5 seconds
                //setTimeout(location.reload.bind(location), 5000);
            }
        }
    });
});

/** Cargos din??micos **/

//Remover campos en freight
$(document).on('click', '.removeFreightCharge', function(e) {
    $(this).closest('tr').remove();
});

//Remover campos en origin
$(document).on('click', '.removeOriginCharge', function(e) {
    $(this).closest('tr').remove();
});

//Remover campos en destination
$(document).on('click', '.removeDestinationCharge', function(e) {
    $(this).closest('tr').remove();
});

//Enviando cotizaciones FCL
$(document).on('click', '#send-pdf-quotev2', function() {
    var id = $('#quote-id').val();
    var email = $('#quote_email').val();
    var to = $('#addresse').val();
    var email_template_id = $('#email_template').val();
    var email_subject = $('#email-subject').val();
    var email_body = $('#email-body').val();

    if (email_template_id != '' && to != '') {
        $.ajax({
            type: 'POST',
            url: '/v2/quotes/send',
            data: { "email_template_id": email_template_id, "id": id, "subject": email_subject, "body": email_body, "to": to },
            beforeSend: function() {
                $('#send-pdf-quotev2').hide();
                $('#send-pdf-quote-sending').show();
            },
            success: function(data) {
                $('#spin').hide();
                $('#send-pdf-quotev2').show();
                $('#send-pdf-quote-sending').hide();
                if (data.message == 'Ok') {
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
                } else {
                    swal(
                        'Error!',
                        'Your message has not been sent.',
                        'error'
                    )
                }
            }
        });
    } else {
        swal(
            '',
            'Please complete all fields',
            'error'
        )
    }
});

//Enviando cotizaciones LCL
$(document).on('click', '#send-pdf-quotev2-lcl-air', function() {
    var id = $('#quote-id').val();
    var email = $('#quote_email').val();
    var to = $('#addresse').val();
    var email_template_id = $('#email_template').val();
    var email_subject = $('#email-subject').val();
    var email_body = $('#email-body').val();

    if (email_template_id != '' && to != '') {
        $.ajax({
            type: 'POST',
            url: '/v2/quotes/send/lcl',
            data: { "email_template_id": email_template_id, "id": id, "subject": email_subject, "body": email_body, "to": to },
            beforeSend: function() {
                $('#send-pdf-quotev2-lcl-air').hide();
                $('#send-pdf-quote-sending').show();
            },
            success: function(data) {
                $('#spin').hide();
                $('#send-pdf-quotev2-lcl-air').show();
                $('#send-pdf-quote-sending').hide();
                if (data.message == 'Ok') {
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
                } else {
                    swal(
                        'Error!',
                        'Your message has not been sent.',
                        'error'
                    )
                }
            }
        });
    } else {
        swal(
            '',
            'Please complete all fields',
            'error'
        )
    }
});

//Enviando cotizaciones AIR
$(document).on('click', '#send-pdf-quotev2-air', function() {
    var id = $('#quote-id').val();
    var email = $('#quote_email').val();
    var to = $('#addresse').val();
    var email_template_id = $('#email_template').val();
    var email_subject = $('#email-subject').val();
    var email_body = $('#email-body').val();

    if (email_template_id != '' && to != '') {
        $.ajax({
            type: 'POST',
            url: '/v2/quotes/send/air',
            data: { "email_template_id": email_template_id, "id": id, "subject": email_subject, "body": email_body, "to": to },
            beforeSend: function() {
                $('#send-pdf-quotev2-air').hide();
                $('#send-pdf-quote-sending').show();
            },
            success: function(data) {
                $('#spin').hide();
                $('#send-pdf-quotev2-air').show();
                $('#send-pdf-quote-sending').hide();
                if (data.message == 'Ok') {
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
                } else {
                    swal(
                        'Error!',
                        'Your message has not been sent.',
                        'error'
                    )
                }
            }
        });
    } else {
        swal(
            '',
            'Please complete all fields',
            'error'
        )
    }
});

//Calculando el total de un cargo en Saleterm

$(document).on("change keyup keydown", ".units, .rate", function() {
    var sum = 0;
    var total_amount = 0;
    var sum_total = 0;
    var sum_total_2 = 0;
    var total_2 = 0;
    var markup = 0;
    var total = 0;
    var self = this;
    var data = '';
    var currency_cfg = $("#currency_id").val();
    $(".rate").each(function() {
        $(this).each(function() {
            var quantity = $(this).closest('tr').find('.units').val();

            if (quantity > 0) {
                total_amount = quantity * $(this).val();
                $(this).closest('tr').find('.total').val(total_amount);
            } else {
                total_amount = 0;
                $(this).closest('tr').find('.total').val(total_amount);
            }
        });
    });
});

$(document).on('change', '#inland_type', function() {
    if ($('#inland_type').val() == 'Origin') {
        $(".origin_port").removeClass('hide');
        $(".destination_port").addClass('hide');
        $(".origin_port_select").prop('disabled', false);
        $(".destination_port_select").prop('disabled', true);
    } else {
        $(".origin_port").addClass('hide');
        $(".destination_port").removeClass('hide');
        $(".origin_port_select").prop('disabled', true);
        $(".destination_port_select").prop('disabled', false);
    }
});

//Mostrar y ocultar puertos en Sale Terms
$(document).on('change', '#saleterm_type', function() {
    if ($('#saleterm_type').val() == 'origin') {
        $(".origin_port").removeClass('hide');

        $(".origin_airport").removeClass('hide');
        $(".destination_port").addClass('hide');
        $(".destination_airport").addClass('hide');
        $(".origin_port_select").prop('disabled', false);
        $(".origin_airport_select").prop('disabled', false);
        $(".destination_port_select").prop('disabled', true);
        $(".destination_airport_select").prop('disabled', true);
    } else {
        $(".origin_port").addClass('hide');
        $(".origin_airport").addClass('hide');
        $(".destination_port").removeClass('hide');
        $(".destination_airport").removeClass('hide');
        $(".origin_port_select").prop('disabled', true);
        $(".origin_airport_select").prop('disabled', true);
        $(".destination_port_select").prop('disabled', false);
        $(".destination_airport_select").prop('disabled', false);
    }
});

//Mostrar y ocultar opciones pdf
$(document).on('change', '#show_hide_select', function() {
    if ($('#show_hide_select').val() == 'total in') {
        $(".group_origin_charges").addClass('hide');
        $(".group_destination_charges").addClass('hide');
        $(".group_freight_charges").addClass('hide');
    } else {
        $(".group_origin_charges").removeClass('hide');
        $(".group_destination_charges").removeClass('hide');
        $(".group_freight_charges").removeClass('hide');
    }

});

//Actualizando opciones PDF
$(document).on('change', '.pdf-feature', function() {
    var id = $(this).attr('data-quote-id');
    var name = $(this).attr('data-name');
    var value = 0;
    if ($(this).attr('data-type') == 'checkbox') {
        if ($(this).prop("checked") == true) {
            value = 1;
        }
    } else {
        value = $(this).val();
    }
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/feature/pdf/update',
        data: { "value": value, "name": name, "id": id },
        success: function(data) {
            if (data.message == 'Ok') {
                //$(this).attr('checked', true).val(0);
            }
        }
    });
});

//GDP
$(document).on('change', '.gdp', function() {
    if ($(this).val() == 1) {
        $(".risk_level").removeAttr('hidden');
        $(".div_risk_level").removeAttr('hidden');
        $(".risk_level_span").attr('hidden', 'true');
    } else {
        $(".risk_level_span").attr('hidden', 'true');
        $(".div_risk_level").attr('hidden', 'true');
    }
});

//King of cargo
$(document).on('change', '.kind_of_cargo', function() {
    if ($(this).val() == 'Pharma') {
        $(".gdp").removeAttr('hidden');
        $(".gdp_span").attr('hidden', 'true');
        $(".div_gdp").removeAttr('hidden');
        if ($(".gdp").val() == 1) {
            $(".div_risk_level").removeAttr('hidden');
        }
    } else {
        $(".div_gdp").attr('hidden', 'true');
        $(".div_risk_level").attr('hidden', 'true');
    }
});

//Calculando total en cada cargo LCL/AIR

$(document).on("change keyup keydown", ".units, .price_per_unit, .markup", function() {
    var sum = 0;
    var total_amount = 0;
    var sum_total = 0;
    var sum_total_2 = 0;
    var total_2 = 0;
    var markup = 0;
    var total = 0;
    var self = this;
    var data = '';
    var currency_cfg = $("#currency_id").val();
    $(".price_per_unit").each(function() {
        $(this).each(function() {
            var quantity = $(this).closest('tr').find('.units').val();
            var currency_id = $(self).closest('tr').find('.currency_id').val();
            var number = $(self).closest('tr').find('.number').val();

            if (quantity > 0) {
                if ($(self).closest('tr').find('.currency_id').val() != "") {
                    $.ajax({
                        url: '/api/currency/' + currency_id,
                        dataType: 'json',
                        success: function(json) {
                            var amount = $(self).closest('tr').find('.price_per_unit').val();
                            var quantity = $(self).closest('tr').find('.units').val();
                            markup = $(self).closest('tr').find('.markup').val();
                            var sub_total = amount * quantity;

                            if (currency_cfg + json.alphacode == json.api_code) {
                                total = sub_total / json.rates;
                            } else {
                                total = sub_total / json.rates_eur;
                            }
                            total = total.toFixed(2);

                            if (markup > 0) {
                                var total_amount_m = Number(total) + Number(markup);
                                $(self).closest('tr').find('.total_2').val(total_amount_m.toFixed(2));
                                $(self).closest('tr').find('.total_2').change();
                            } else {
                                $(self).closest('tr').find('.total_2').val(total);
                                $(self).closest('tr').find('.total_2').change();
                            }
                        }
                    });
                }
                total_amount = quantity * $(this).val();
                $(this).closest('tr').find('.total').val(total_amount);
                $(this).closest('tr').find('.total').change();
            } else {
                total_amount = 0;
                $(this).closest('tr').find('.total').val(total_amount);
                $(this).closest('tr').find('.total').change();
            }
        });
    });
});

$(document).on("change", ".total_22", function() {
    var sum = 0;
    var value = 0;
    $(this).each(function() {
        value = Number($(this).closest('table').find('.total-amount').html());
        sum += value;
    });
    sum_total = Number($(this).closest('div').find('.sum_total').val()) + Number(sum);
    $(this).closest('div').find('.td_sum_total').html(sum_total);

});

$(document).ready(function() {
    if ($("select[name='company_id']").val() == '') {
        $('select[name="contact_id"]').empty();
    }

    $("select[name='company_id']").on('change', function() {
        var company_id = $(this).val();
        if (company_id) {
            $('select[name="contact_id"]').empty();
            $.ajax({
                url: "/quotes/company/contact/id/" + company_id,
                dataType: 'json',
                success: function(data) {
                    $('select[name="client"]').empty();
                    $('select[name="contact_id"]').append('<option value="">Select an option</option>');
                    $.each(data, function(key, value) {
                        $('select[name="contact_id"]').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
            $.ajax({
                url: "/quotes/company/price/id/" + company_id,
                dataType: 'json',
                success: function(data) {
                    $('select[name="price_id"]').empty();
                    $('select[name="price_id"]').append('<option value="">Select an option</option>');
                    $.each(data, function(key, value) {
                        $('select[name="price_id"]').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('select[name="contact_id"]').empty();
            $('select[name="price_id"]').empty();
        }
    });
});

// dinamic 

$("select[name='group_containerC']").on('change', function() {

    var valor = $(this).val();

    if (valor) {
        $.ajax({
            url: "/v2/quotes/groupContainer/" + valor,
            dataType: 'json',
            success: function(data) {
                var cont = 0;
                var texto = "";
                $.each(data, function(key, value) {

                    if (cont == 0) {
                        texto += "<div class='form-group m-form__group row'> ";

                    }
                    texto += "<label class='col-12 col-sm-6 col-form-label'> <p><b> " + value['code'] + "  </b></p><input name='C" + value['code'] + "' value= '0' type='number' class='form-control' required >   </label>  ";
                    if (cont == 1) {
                        texto += " </div>";
                    }
                    cont++;
                    if (cont == 2) {
                        cont = 0;
                    }
                });
                $("#containerDinamic").html(texto);
            }
        });
    }
});




/** Search **/

$(document).on('change', '#quoteType', function(e) {


    if ($(this).val() == 1) {


        $('#mode4').prop('checked', true);
        $("#cmadiv").show();

        $("#total_quantity").removeAttr("required");
        $("#total_weight").removeAttr("required");
        $("#total_volume").removeAttr("required");
        $('#quantity').removeAttr('required');
        $('#height').removeAttr('required');
        $('#width').removeAttr('required');
        $('#large').removeAttr('required');
        $('#weight').removeAttr('required');
        $('#volume').removeAttr('required');


        $(".infocheck").val('');

        $(".quote_search").show();
        $(".formu").val('');
        $(".search").hide();

        $("#origin_harbor").prop("disabled", false);
        $("#destination_harbor").prop("disabled", false);
        $("#equipment_id").show();
        $("#equipment").prop("disabled", false);

        $("#delivery_type").prop("disabled", false);
        $("#delivery_type_air").prop("disabled", true);
        $("#delivery_type_label").show();
        $("#delivery_type_air_label").hide();
        $("#fcl_load").show();
        $("#origin_harbor_label").show();
        $("#destination_harbor_label").show();
        $("#airline_label").hide();
        $("#carrier_label").show();

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

    if ($(this).val() == 2) {

        // Validaciones por defecto 
        $("#total_quantity").prop("required", true);
        $("#total_weight").prop("required", true);
        $("#total_volume").prop("required", true);

        $('#mode4').prop('checked', false);
        $("#cmadiv").hide();

        $(".infocheck").val('');
        //$(".quote_search").hide();
        $(".formu").val('');



        $(".quote_search").show();

        $(".search").hide();

        $("#origin_harbor").prop("disabled", false);
        $("#destination_harbor").prop("disabled", false);
        $("#equipment_id").hide();
        $("#equipment").prop("disabled", true);
        $("#delivery_type").prop("disabled", false);
        $("#delivery_type_air").prop("disabled", true);
        $("#delivery_type_label").show();
        $("#delivery_type_air_label").hide();
        $("#lcl_air_load").show();
        $("#origin_harbor_label").show();
        $("#destination_harbor_label").show();
        $("#airline_label").hide();
        $("#carrier_label").show();

        $("#fcl_load").hide();
        $("#origin_airport_label").hide();
        $("#destination_airport_label").hide();
        $("input[name=qty_20]").val('');
        $("input[name=qty_40]").val('');
        $("input[name=qty_40_hc]").val('');
        $("input[name=qty_45_hc]").val('');
        var chargeable_weight = 0;
        var volume = 0;
        var total_volume = 0;
        var total_weight = 0;
        var weight = sum;
        var sum = 0;
        var sum_vol = 0;

        if (($('#total_volume').val() != '' && $('#total_volume').val() > 0) && ($('#total_weight').val() != '' && $('#total_weight').val() > 0)) {
            total_volume = $('#total_volume').val();
            weight = $('#total_weight').val();

            if ($('#quoteType').val() == 2) {


                total_weight = weight / 1000;
                if (total_volume > total_weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = total_weight;
                }
                $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2) + " m<sup>3</sup>");
            } else if ($('#quoteType').val() == 3) {
                total_volume = total_volume * 166.67;
                if (total_volume > weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = weight;
                }
                $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2) + " kg");
            }

            $("#chargeable_weight_pkg_input").val(chargeable_weight);
        } else {
            if (($('#total_volume_pkg_input').val() != '' && $('#total_volume_pkg_input').val() > 0) && ($('#total_weight_pkg_input').val() != '' && $('#total_weight_pkg_input').val() > 0)) {

                sum_vol = $('#total_volume_pkg_input').val();
                weight = $('#total_weight_pkg_input').val() / 1000;

                total_vol_chargeable = sum_vol;
                if (total_vol_chargeable > weight) {
                    chargeable_weight = total_vol_chargeable;
                } else {
                    chargeable_weight = weight;
                }

            }

            $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2) + " m<sup>3</sup>");
            $("#chargeable_weight_pkg_input").val(chargeable_weight);
        }

    }

    if ($(this).val() == 3) {

        // Validaciones
        $("#total_quantity").prop("required", true);
        $("#total_weight").prop("required", true);
        $("#total_volume").prop("required", true);

        $(".infocheck").val('');
        $(".quote_search").hide();
        $(".formu").val('');
        $(".search").hide();



        $('#mode4').prop('checked', false);


        $("#origin_harbor").prop("disabled", true);
        $("#destination_harbor").prop("disabled", true);
        $("#equipment_id").hide();
        $("#equipment").prop("disabled", true);
        $("#delivery_type").prop("disabled", true);
        $("#delivery_type_air").prop("disabled", false);
        $("#delivery_type_label").hide();
        $("#delivery_type_air_label").show();
        $("#lcl_air_load").show();
        $("#origin_airport_label").show();
        $("#destination_airport_label").show();
        $("#airline_label").show();
        $("#carrier_label").hide();

        $("#fcl_load").hide();
        $("#origin_harbor_label").hide();
        $("#destination_harbor_label").hide();
        $("input[name=qty_20]").val('');
        $("input[name=qty_40]").val('');
        $("input[name=qty_40_hc]").val('');
        $("input[name=qty_45_hc]").val('');
        var chargeable_weight = 0;
        var volume = 0;
        var total_volume = 0;
        var total_weight = 0;
        var weight = sum;
        var sum = 0;
        var sum_vol = 0;

        if (($('#total_volume').val() != '' && $('#total_volume').val() > 0) && ($('#total_weight').val() != '' && $('#total_weight').val() > 0)) {
            total_volume = $('#total_volume').val();
            total_weight = $('#total_weight').val();
            if ($('#quoteType').val() == 2) {
                total_weight = total_weight / 1000;
                if (total_volume > total_weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = total_weight;
                }
                $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2) + " m<sup>3</sup>");
            } else if ($('#quoteType').val() == 3) {
                total_volume = total_volume * 166.67;
                if (total_volume > total_weight) {
                    chargeable_weight = total_volume;
                } else {
                    chargeable_weight = total_weight;
                }
                $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2) + " kg");
            }

            $("#chargeable_weight_pkg_input").val(chargeable_weight);
        } else {
            if (($('#total_volume_pkg_input').val() != '' && $('#total_volume_pkg_input').val() > 0) && ($('#total_weight_pkg_input').val() != '' && $('#total_weight_pkg_input').val() > 0)) {

                sum_vol = $('#total_volume_pkg_input').val();
                weight = $('#total_weight_pkg_input').val();

                total_vol_chargeable = sum_vol * 166.67;
                if (total_vol_chargeable > weight) {
                    chargeable_weight = total_vol_chargeable;
                } else {
                    chargeable_weight = weight;
                }
            }
            $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2) + " kg");
            $("#chargeable_weight_pkg_input").val(chargeable_weight);
        }
    }
});

$(document).on('change', '#delivery_type', function(e) {

    if ($(this).val() == 1) {
        $("#origin_address_label").addClass('hide');
        $("#destination_address_label").addClass('hide');
        $("#origin_address").val('');
        $("#destination_address").val('');
    }
    if ($(this).val() == 2) {

        $("#origin_address_label").addClass('hide');
        $("#destination_address_label").removeClass('hide');
        $("#origin_address").val('');
    }
    if ($(this).val() == 3) {
        $("#origin_address_label").removeClass('hide');
        $("#destination_address_label").addClass('hide');
        $("#destination_address").val('');
    }
    if ($(this).val() == 4) {
        $("#origin_address_label").removeClass('hide');
        $("#destination_address_label").removeClass('hide');
    }
});

$(document).ready(function() {
    $('.select2-selection__rendered').removeAttr('title');
    $('#select2-price_id-container').text('Please an option');

    // CLEARING COMPANIES SELECT

    $("select[name='company_id_quote']").on('change', function() {
        var company_id = $(this).val();
        $("#contact_id").val('');
        /*if ($("#m_select2_2_modal").val() != '0')
            $("#contact_id").prop('required', true);
        else
            $("#contact_id").removeAttr('required');*/

        $('#select2-contact_id-container').text('Please an option');
        if (company_id) {
            $('select[name="contact_id"]').empty();
            $('select[name="contact_id"]').prop("disabled", false);

            $.ajax({
                url: "/quotes/company/contact/id/" + company_id,
                dataType: 'json',
                success: function(data) {
                    $('select[name="contact_id"]').empty();
                    $.each(data, function(key, value) {
                        $('select[name="contact_id"]').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });

            $.ajax({
                url: "/quotes/company/price/id/" + company_id,
                dataType: 'json',
                success: function(data) {
                    $('select[name="price_id"]').empty();
                    $('select[name="price_id"]').append('<option value="0">Select an option</option>');
                    $.each(data, function(key, value) {
                        $('select[name="price_id"]').append('<option value="' + key + '">' + value + '</option>');
                    });

                    // CLEARING PRICE SELECT
                    $("select[name='contact_id']").val('');
                    $('#select2-contact_id-container').text('Please an option');

                    $("select[name='price_id']").val('');

                }
            });
        } else {
            $('#select2-contact_id-container').text('Please an option');
            $('select[name="contact_id"]').empty();
            $('select[name="price_id"]').empty();
        }
    });
});

$(".quote_search").on("click", function() {

    //FCL
    if ($('#quoteType').val() == 1) {
        $('#FormQuote').attr('action', '/v2/quotes/processSearch');
    }

    // LCL
    if ($('#quoteType').val() == 2) {
        $('#FormQuote').attr('action', '/v2/quotes/processSearchLCL');
    }
    $(".quote_search").attr("type", "submit");

    var form = $(this).parents('form');
    $(form).submit(function() {
        notification('Searching  &nbsp;&nbsp;<i class="fa fa-spinner fa-spin"></i>', 'info');
    });
});

function submitForm(type, quote) {

    if (quote == 'FCL') {
        $('#rateForm').attr('action', '/v2/quotes/store/' + type);
    } else {
        $('#rateForm').attr('action', '/v2/quotes/storeLCL/' + type);
    }

    $("#rateForm").submit();
}

$('.tool_tip').tooltip({ trigger: 'manual' }).tooltip('show');

$(".quote_man").on("click", function() {

    //$('#FormQuote').attr('action', '/api/quote/store');
    var type = 1;
    $('#FormQuote').attr('action', '/v2/quotes/store/' + type);

    if ($('#quoteType').val() == 2) {

        if ($("#total_quantity_pkg_input").val() > 0) {
            $("#total_quantity").val($("#total_quantity_pkg_input").val());
        }
        if ($("#total_weight_pkg_input").val() > 0) {
            $("#total_weight").val($("#total_weight_pkg_input").val());
        }
        if ($("#total_volume_pkg_input").val() > 0) {
            $("#total_volume").val($("#total_volume_pkg_input").val());
        }
    }

    $(".quote_man").attr("type", "submit");
});

$('.btn-input__select').on('click', function() {

    var idRate = $(this).attr('rate-id');
    $cantidadDestino = $('.labelDest' + idRate).length;
    $cantidadOrigen = $('.labelOrig' + idRate).length;


    $('.labelSelectDest' + idRate).toggleClass('hidden-general');
    $('.labelOrig' + idRate).toggleClass('visible__select-add');
    $('.labelDest' + idRate).toggleClass('visible__select-add');
    if ($cantidadDestino == 1) {
        //   $('.labelDest' + idRate).addClass('style__select-add');
        //   $('#inputID-select1-' + idRate).attr('checked', true);
        //   calcularInlands('destino', idRate);

    }

    if ($cantidadOrigen == 1) {

        //   $('.labelOrig' + idRate).addClass('style__select-add');
        //   $('#inputIO-select1-' + idRate).attr('checked', true);
        //   calcularInlands('origen', idRate);
    }

});

$('.btn-input__select-add').on('click', function() {
    $(this).toggleClass('style__select-add');
});

$('.input-select').on('click', function() {
    var ident = $(this).attr('id');
    $('.' + ident + '').toggleClass('border-card');
});

$('.inlands').on('click', function() {
    $('.card-p__quotes').toggleClass('border-card-p');
    var id = $(this).attr('data-inland');
    var idRate = $(this).attr('data-rate');

    var isDecimal = $("#isDecimal").val();

    var theElement = $(this);
    $('.labelDest' + idRate).removeClass('style__select-add');
    if (theElement.prop('checked')) {

        $('.labelI' + idRate + '-' + id).addClass('style__select-add');
        var group = "input:checkbox[name='" + theElement.attr("name") + "']";
        $(group).prop("checked", false);
        theElement.prop("checked", true);
    } else {

        theElement.prop("checked", false);
    }




    //DRY
    var i20 = $("#valor-d20DV" + id + "-" + idRate).html();
    var i40 = $("#valor-d40DV" + id + "-" + idRate).html();
    var i40h = $("#valor-d40HC" + id + "-" + idRate).html();
    var i40nor = $("#valor-d40NOR" + id + "-" + idRate).html();
    var i45h = $("#valor-d45HC" + id + "-" + idRate).html();

    var tot20dv_html = $(".tot20DV-" + idRate);
    var tot20dv_val = $("#tot20DV-" + idRate).val();
    var tot_20 = '';

    var tot40dv_html = $(".tot40DV-" + idRate);
    var tot40dv_val = $("#tot40DV-" + idRate).val();
    var tot_40 = '';

    var tot40hc_html = $(".tot40HC-" + idRate);
    var tot40hc_val = $("#tot40HC-" + idRate).val();
    var tot_40hc = '';

    var tot40nor_html = $(".tot40NOR-" + idRate);
    var tot40nor_val = $("#tot40NOR-" + idRate).val();
    var tot_40nor = '';


    var tot45hc_html = $(".tot45HC-" + idRate);
    var tot45hc_val = $("#tot45HC-" + idRate).val();
    var tot_45hc = '';


    var sub20o = $("#sub_inland_20DV_o" + idRate);
    var sub40o = $("#sub_inland_40DV_o" + idRate);
    var sub40ho = $("#sub_inland_40HC_o" + idRate);
    var sub40noro = $("#sub_inland_40NOR_o" + idRate);
    var sub45ho = $("#sub_inland_45HC_o" + idRate);

    var sub20d = $("#sub_inland_20DV_d" + idRate);
    var sub40d = $("#sub_inland_40DV_d" + idRate);
    var sub40hd = $("#sub_inland_40HC_d" + idRate);
    var sub40nord = $("#sub_inland_40NOR_d" + idRate);
    var sub45hd = $("#sub_inland_45HC_d" + idRate);


    var sub20 = $("#sub_inland_20DV" + idRate).html();
    var sub40 = $("#sub_inland_40DV" + idRate).html();
    var sub40h = $("#sub_inland_40HC" + idRate).html();
    var sub40nor = $("#sub_inland_40NOR" + idRate).html();
    var sub45h = $("#sub_inland_45HC" + idRate).html();



    //REFEER

    var i20RF = $("#valor-d20RF" + id + "-" + idRate).html();
    var i40RF = $("#valor-d40RF" + id + "-" + idRate).html();
    var i40HCRF = $("#valor-d40HCRF" + id + "-" + idRate).html();

    var tot20rf_html = $(".tot20RF-" + idRate);
    var tot20rf_val = $("#tot20RF-" + idRate).val();
    var tot_20rf = '';

    var tot40rf_html = $(".tot40RF-" + idRate);
    var tot40rf_val = $("#tot40RF-" + idRate).val();
    var tot_40rf = '';

    var tot40hcrf_html = $(".tot40HCRF-" + idRate);
    var tot40hcrf_val = $("#tot40HCRF-" + idRate).val();
    var tot_40hcrf = '';

    var sub20RFo = $("#sub_inland_20RF_o" + idRate);
    var sub40RFo = $("#sub_inland_40RF_o" + idRate);
    var sub40HCRFo = $("#sub_inland_40HCRF_o" + idRate);

    var sub20RFd = $("#sub_inland_20RF_d" + idRate);
    var sub40RFd = $("#sub_inland_40RF_d" + idRate);
    var sub40HCRFd = $("#sub_inland_40HCRF_d" + idRate);



    var sub20RF = $("#sub_inland_20RF" + idRate).html();
    var sub40RF = $("#sub_inland_40RF" + idRate).html();
    var sub40HCRF = $("#sub_inland_40HCRF" + idRate).html();

    // OT 

    var i20OT = $("#valor-d20OT" + id + "-" + idRate).html();
    var i40OT = $("#valor-d40OT" + id + "-" + idRate).html();


    var tot20ot_html = $(".tot20OT-" + idRate);
    var tot20ot_val = $("#tot20OT-" + idRate).val();
    var tot_20ot = '';

    var tot40ot_html = $(".tot40OT-" + idRate);
    var tot40ot_val = $("#tot40OT-" + idRate).val();
    var tot_40ot = '';


    var sub20OTo = $("#sub_inland_20OT_o" + idRate);
    var sub40OTo = $("#sub_inland_40OT_o" + idRate);
    var sub20OTd = $("#sub_inland_20OT_d" + idRate);
    var sub40OTd = $("#sub_inland_40OT_d" + idRate);


    var sub20OT = $("#sub_inland_20OT" + idRate).html();
    var sub40OT = $("#sub_inland_40OT" + idRate).html();


    // RACK

    var i20FR = $("#valor-d20FR" + id + "-" + idRate).html();
    var i40FR = $("#valor-d40FR" + id + "-" + idRate).html();


    var tot20fr_html = $(".tot20FR-" + idRate);
    var tot20fr_val = $("#tot20FR-" + idRate).val();
    var tot_20fr = '';

    var tot40fr_html = $(".tot40FR-" + idRate);
    var tot40fr_val = $("#tot40FR-" + idRate).val();
    var tot_40fr = '';

    var sub20FRo = $("#sub_inland_20FR_o" + idRate);
    var sub40FRo = $("#sub_inland_40FR_o" + idRate);
    var sub20FRd = $("#sub_inland_20FR_d" + idRate);
    var sub40FRd = $("#sub_inland_40FR_d" + idRate);


    var sub20FR = $("#sub_inland_20FR" + idRate).html();
    var sub40FR = $("#sub_inland_40FR" + idRate).html();






    if (theElement.prop('checked')) {


        //Dry 

        sub20d.val(parseFloat(i20));
        sub40d.val(parseFloat(i40));
        sub40hd.val(parseFloat(i40h));
        sub40nord.val(parseFloat(i40nor));
        sub45hd.val(parseFloat(i45h));

        sub20 = parseFloat(sub20o.val()) + parseFloat(sub20d.val());
        sub40 = parseFloat(sub40o.val()) + parseFloat(sub40d.val());
        sub40h = parseFloat(sub40ho.val()) + parseFloat(sub40hd.val());
        sub40nor = parseFloat(sub40noro.val()) + parseFloat(sub40nord.val());
        sub45h = parseFloat(sub45ho.val()) + parseFloat(sub45hd.val());

        tot_20 = parseFloat(tot20dv_val) + parseFloat(sub20);
        tot_40 = parseFloat(tot40dv_val) + parseFloat(sub40);
        tot_40hc = parseFloat(tot40hc_val) + parseFloat(sub40h);
        tot_40nor = parseFloat(tot40nor_val) + parseFloat(sub40nor);
        tot_45hc = parseFloat(tot45hc_val) + parseFloat(sub45h);

        //Refeer
        sub20RFd.val(parseFloat(i20RF));
        sub40RFd.val(parseFloat(i40RF));
        sub40HCRFd.val(parseFloat(i40HCRF));

        sub20RF = parseFloat(sub20RFo.val()) + parseFloat(sub20RFd.val());
        sub40RF = parseFloat(sub40RFo.val()) + parseFloat(sub40RFd.val());
        sub40HCRF = parseFloat(sub40HCRFo.val()) + parseFloat(sub40HCRFd.val());

        tot_20rf = parseFloat(tot20rf_val) + parseFloat(sub20RF);
        tot_40rf = parseFloat(tot40rf_val) + parseFloat(sub40RF);
        tot_40hcrf = parseFloat(tot40hcrf_val) + parseFloat(sub40HCRF);


        //OT
        sub20OTd.val(parseFloat(i20OT));
        sub40OTd.val(parseFloat(i40OT));

        sub20OT = parseFloat(sub20OTo.val()) + parseFloat(sub20OTd.val());
        sub40OT = parseFloat(sub40OTo.val()) + parseFloat(sub40OTd.val());

        tot_20ot = parseFloat(tot20ot_val) + parseFloat(sub20OT);
        tot_40ot = parseFloat(tot40ot_val) + parseFloat(sub40OT);


        //RACK 


        sub20FRd.val(parseFloat(i20FR));
        sub40FRd.val(parseFloat(i40FR));

        sub20FR = parseFloat(sub20FRo.val()) + parseFloat(sub20FRd.val());
        sub40FR = parseFloat(sub40FRo.val()) + parseFloat(sub40FRd.val());

        tot_20fr = parseFloat(tot20fr_val) + parseFloat(sub20FR);
        tot_40fr = parseFloat(tot40fr_val) + parseFloat(sub40FR);



    } else {

        //Dry
        sub20d.val(0.00);
        sub40d.val(0.00);
        sub40hd.val(0.00);
        sub40nord.val(0.00);
        sub45hd.val(0.00);

        //Refeer

        sub20RFd.val(0.00);
        sub40RFd.val(0.00);
        sub40HCRFd.val(0.00);


        //OT
        sub20OTd.val(0.00);
        sub40OTd.val(0.00);

        //FR 


        sub20FRd.val(0.00);
        sub40FRd.val(0.00);

        // Dry
        if (parseFloat(sub20o.val()) > parseFloat(sub20d.val()))
            sub20 = parseFloat(sub20o.val()) - parseFloat(sub20d.val());
        else
            sub20 = parseFloat(sub20d.val()) - parseFloat(sub20o.val());

        tot_20 = parseFloat(tot20dv_val) + parseFloat(sub20);

        if (parseFloat(sub40o.val()) > parseFloat(sub40d.val()))
            sub40 = parseFloat(sub40o.val()) - parseFloat(sub40d.val());
        else
            sub40 = parseFloat(sub40d.val()) - parseFloat(sub40o.val());


        tot_40 = parseFloat(tot40dv_val) + parseFloat(sub40);


        if (parseFloat(sub40ho.val()) > parseFloat(sub40hd.val()))
            sub40h = parseFloat(sub40ho.val()) - parseFloat(sub40hd.val());
        else
            sub40h = parseFloat(sub40hd.val() - parseFloat(sub40ho.val()));

        tot_40hc = parseFloat(tot40hc_val) + parseFloat(sub40h);

        if (parseFloat(sub40noro.val()) > parseFloat(sub40nord.val()))
            sub40nor = parseFloat(sub40noro.val()) - parseFloat(sub40nord.val());
        else
            sub40nor = parseFloat(sub40nord.val() - parseFloat(sub40noro.val()));

        tot_40nor = parseFloat(tot40nor_val) + parseFloat(sub40nor);


        if (parseFloat(sub45ho.val()) > parseFloat(sub45hd.val()))
            sub45h = parseFloat(sub45ho.val()) - parseFloat(sub45hd.val());
        else
            sub45h = parseFloat(sub45hd.val() - parseFloat(sub45ho.val()));

        tot_45hc = parseFloat(tot45hc_val) + parseFloat(sub45h);



        //Refeer

        if (parseFloat(sub20RFo.val()) > parseFloat(sub20RFd.val()))
            sub20RF = parseFloat(sub20RFo.val()) - parseFloat(sub20RFd.val());
        else
            sub20RF = parseFloat(sub20RFd.val()) - parseFloat(sub20RFo.val());

        tot_20rf = parseFloat(tot20rf_val) + parseFloat(sub20RF);


        if (parseFloat(sub40RFo.val()) > parseFloat(sub40RFd.val()))
            sub40RF = parseFloat(sub40RFo.val()) - parseFloat(sub40RFd.val());
        else
            sub40RF = parseFloat(sub40RFo.val()) - parseFloat(sub40RFd.val());


        tot_40rf = parseFloat(tot40rf_val) + parseFloat(sub40RF);


        if (parseFloat(sub40HCRFo.val()) > parseFloat(sub40HCRFd.val()))
            sub40HCRF = parseFloat(sub40HCRFo.val()) - parseFloat(sub40HCRFd.val());
        else
            sub40HCRF = parseFloat(sub40HCRFo.val() - parseFloat(sub40HCRFd.val()));

        tot_40hcrf = parseFloat(tot40hcrf_val) + parseFloat(sub40HCRF);


        //OT

        if (parseFloat(sub20OTo.val()) > parseFloat(sub20OTd.val()))
            sub20OT = parseFloat(sub20OTo.val()) - parseFloat(sub20OTd.val());
        else
            sub20OT = parseFloat(sub20OTd.val()) - parseFloat(sub20OTo.val());

        tot_20ot = parseFloat(tot20ot_val) + parseFloat(sub20OT);


        if (parseFloat(sub40OTo.val()) > parseFloat(sub40OTd.val()))
            sub40OT = parseFloat(sub40OTo.val()) - parseFloat(sub40OTd.val());
        else
            sub40OT = parseFloat(sub40OTd.val()) - parseFloat(sub40OTo.val());

        tot_40ot = parseFloat(tot40ot_val) + parseFloat(sub40OT);

        //Flat Rack 

        if (parseFloat(sub20FRo.val()) > parseFloat(sub20FRd.val()))
            sub20FR = parseFloat(sub20FRo.val()) - parseFloat(sub20FRd.val());
        else
            sub20FR = parseFloat(sub20FRd.val()) - parseFloat(sub20FRo.val());

        tot_20fr = parseFloat(tot20fr_val) + parseFloat(sub20FR);


        if (parseFloat(sub40FRo.val()) > parseFloat(sub40FRd.val()))
            sub40FR = parseFloat(sub40FRo.val()) - parseFloat(sub40FRd.val());
        else
            sub40FR = parseFloat(sub40FRd.val()) - parseFloat(sub40FRo.val());

        tot_40fr = parseFloat(tot40fr_val) + parseFloat(sub40FR);

    }

    //DRY


    if (isDecimal == 1) {

        $("#sub_inland_20DV" + idRate).html(sub20.toFixed(2));
        $("#sub_inland_40DV" + idRate).html(sub40.toFixed(2));
        $("#sub_inland_40HC" + idRate).html(sub40h.toFixed(2));
        $("#sub_inland_40NOR" + idRate).html(sub40nor.toFixed(2));
        $("#sub_inland_45HC" + idRate).html(sub45h.toFixed(2));

        tot20dv_html.html(tot_20.toFixed(2));
        tot40dv_html.html(tot_40.toFixed(2));
        tot40hc_html.html(tot_40hc.toFixed(2));
        tot40nor_html.html(tot_40nor.toFixed(2));
        tot45hc_html.html(tot_45hc.toFixed(2));

        //refeer
        $("#sub_inland_20RF" + idRate).html(sub20RF.toFixed(2));
        $("#sub_inland_40RF" + idRate).html(sub40RF.toFixed(2));
        $("#sub_inland_40HCRF" + idRate).html(sub40HCRF.toFixed(2));

        tot20rf_html.html(tot_20rf.toFixed(2));
        tot40rf_html.html(tot_40rf.toFixed(2));
        tot40hcrf_html.html(tot_40hcrf.toFixed(2));

        //OT


        $("#sub_inland_20OT" + idRate).html(sub20OT.toFixed(2));
        $("#sub_inland_40OT" + idRate).html(sub40OT.toFixed(2));


        tot20ot_html.html(tot_20ot.toFixed(2));
        tot40ot_html.html(tot_40ot.toFixed(2));


        //FR

        $("#sub_inland_20FR" + idRate).html(sub20FR.toFixed(2));
        $("#sub_inland_40FR" + idRate).html(sub40FR.toFixed(2));

        tot20fr_html.html(tot_20fr.toFixed(2));
        tot40fr_html.html(tot_40fr.toFixed(2));

    } else {
        //Dry
        tot20dv_html.html(Math.round(tot_20));
        tot40dv_html.html(Math.round(tot_40));
        tot40hc_html.html(Math.round(tot_40hc));
        tot40nor_html.html(Math.round(tot_40nor));
        tot45hc_html.html(Math.round(tot_45hc));

        $("#sub_inland_20DV" + idRate).html(Math.round(sub20));
        $("#sub_inland_40DV" + idRate).html(Math.round(sub40));
        $("#sub_inland_40HC" + idRate).html(Math.round(sub40h));
        $("#sub_inland_40NOR" + idRate).html(Math.round(sub40nor));
        $("#sub_inland_45HC" + idRate).html(Math.round(sub45h));


        //refeer
        $("#sub_inland_20RF" + idRate).html(Math.round(sub20RF));
        $("#sub_inland_40RF" + idRate).html(Math.round(sub40RF));
        $("#sub_inland_40HCRF" + idRate).html(Math.round(sub40HCRF));

        tot20rf_html.html(Math.round(tot_20rf));
        tot40rf_html.html(Math.round(tot_40rf));
        tot40hcrf_html.html(Math.round(tot_40hcrf));

        //OT


        $("#sub_inland_20OT" + idRate).html(Math.round(sub20OT));
        $("#sub_inland_40OT" + idRate).html(Math.round(sub40OT));


        tot20ot_html.html(Math.round(tot_20ot));
        tot40ot_html.html(Math.round(tot_40ot));


        //FR

        $("#sub_inland_20FR" + idRate).html(Math.round(sub20FR));
        $("#sub_inland_40FR" + idRate).html(Math.round(sub40FR));

        tot20fr_html.html(Math.round(tot_20fr));
        tot40fr_html.html(Math.round(tot_40fr));

    }





});

$('.inlandsO').on('click', function() {
    $('.card-p__quotes').toggleClass('border-card-p');
    var id = $(this).attr('data-inland');
    var idRate = $(this).attr('data-rate');

    var isDecimal = $("#isDecimal").val();
    var theElement = $(this);

    $('.labelOrig' + idRate).removeClass('style__select-add');

    if (theElement.prop('checked')) {
        $('.labelO' + idRate + '-' + id).addClass('style__select-add');
        var group = "input:checkbox[name='" + theElement.attr("name") + "']";
        $(group).prop("checked", false);
        theElement.prop("checked", true);
    } else {
        theElement.prop("checked", false);
    }




    // DRY 
    var i20 = $("#valor-o20DV" + id + "-" + idRate).html();
    var i40 = $("#valor-o40DV" + id + "-" + idRate).html();
    var i40h = $("#valor-o40HC" + id + "-" + idRate).html();
    var i40nor = $("#valor-o40NOR" + id + "-" + idRate).html();
    var i45h = $("#valor-o45HC" + id + "-" + idRate).html();



    var tot20dv_html = $(".tot20DV-" + idRate);
    var tot20dv_val = $("#tot20DV-" + idRate).val();
    var tot_20 = '';

    var tot40dv_html = $(".tot40DV-" + idRate);
    var tot40dv_val = $("#tot40DV-" + idRate).val();
    var tot_40 = '';

    var tot40hc_html = $(".tot40HC-" + idRate);
    var tot40hc_val = $("#tot40HC-" + idRate).val();
    var tot_40hc = '';


    var tot40nor_html = $(".tot40NOR-" + idRate);
    var tot40nor_val = $("#tot40NOR-" + idRate).val();
    var tot_40nor = '';


    var tot45hc_html = $(".tot45HC-" + idRate);
    var tot45hc_val = $("#tot45HC-" + idRate).val();
    var tot_45hc = '';

    var sub20o = $("#sub_inland_20DV_o" + idRate);
    var sub40o = $("#sub_inland_40DV_o" + idRate);
    var sub40ho = $("#sub_inland_40HC_o" + idRate);
    var sub40noro = $("#sub_inland_40NOR_o" + idRate);
    var sub45ho = $("#sub_inland_45HC_o" + idRate);

    var sub20d = $("#sub_inland_20DV_d" + idRate);
    var sub40d = $("#sub_inland_40DV_d" + idRate);
    var sub40hd = $("#sub_inland_40HC_d" + idRate);
    var sub40nord = $("#sub_inland_40NOR_d" + idRate);
    var sub45hd = $("#sub_inland_45HC_d" + idRate);


    var sub20 = $("#sub_inland_20" + idRate).html();
    var sub40 = $("#sub_inland_40" + idRate).html();
    var sub40h = $("#sub_inland_40h" + idRate).html();
    var sub40nor = $("#sub_inland_40NOR" + idRate).html();
    var sub45h = $("#sub_inland_45HC" + idRate).html();


    //Refeeer 

    var i20RF = $("#valor-o20RF" + id + "-" + idRate).html();
    var i40RF = $("#valor-o40RF" + id + "-" + idRate).html();
    var i40HCRF = $("#valor-o40HCRF" + id + "-" + idRate).html();

    var tot20rf_html = $(".tot20RF-" + idRate);
    var tot20rf_val = $("#tot20RF-" + idRate).val();
    var tot_20rf = '';

    var tot40rf_html = $(".tot40RF-" + idRate);
    var tot40rf_val = $("#tot40RF-" + idRate).val();
    var tot_40rf = '';

    var tot40hcrf_html = $(".tot40HCRF-" + idRate);
    var tot40hcrf_val = $("#tot40HCRF-" + idRate).val();
    var tot_40hcrf = '';

    var sub20RFo = $("#sub_inland_20RF_o" + idRate);
    var sub40RFo = $("#sub_inland_40RF_o" + idRate);
    var sub40HCRFo = $("#sub_inland_40HCRF_o" + idRate);

    var sub20RFd = $("#sub_inland_20RF_d" + idRate);
    var sub40RFd = $("#sub_inland_40RF_d" + idRate);
    var sub40HCRFd = $("#sub_inland_40HCRF_d" + idRate);



    var sub20RF = $("#sub_inland_20RF" + idRate).html();
    var sub40RF = $("#sub_inland_40RF" + idRate).html();
    var sub40HCRF = $("#sub_inland_40HCRF" + idRate).html();


    // OT 

    var i20OT = $("#valor-o20OT" + id + "-" + idRate).html();
    var i40OT = $("#valor-o40OT" + id + "-" + idRate).html();


    var tot20ot_html = $(".tot20OT-" + idRate);
    var tot20ot_val = $("#tot20OT-" + idRate).val();
    var tot_20ot = '';

    var tot40ot_html = $(".tot40OT-" + idRate);
    var tot40ot_val = $("#tot40OT-" + idRate).val();
    var tot_40ot = '';


    var sub20OTo = $("#sub_inland_20OT_o" + idRate);
    var sub20OTd = $("#sub_inland_20OT_d" + idRate);


    var sub40OTo = $("#sub_inland_40OT_o" + idRate);
    var sub40OTd = $("#sub_inland_40OT_d" + idRate);


    var sub20OT = $("#sub_inland_20OT" + idRate).html();
    var sub40OT = $("#sub_inland_40OT" + idRate).html();


    // RACK

    var i20FR = $("#valor-o20FR" + id + "-" + idRate).html();
    var i40FR = $("#valor-o40FR" + id + "-" + idRate).html();


    var tot20fr_html = $(".tot20FR-" + idRate);
    var tot20fr_val = $("#tot20FR-" + idRate).val();
    var tot_20fr = '';

    var tot40fr_html = $(".tot40FR-" + idRate);
    var tot40fr_val = $("#tot40FR-" + idRate).val();
    var tot_40fr = '';

    var sub20FRo = $("#sub_inland_20FR_o" + idRate);
    var sub40FRo = $("#sub_inland_40FR_o" + idRate);
    var sub20FRd = $("#sub_inland_20FR_d" + idRate);
    var sub40FRd = $("#sub_inland_40FR_d" + idRate);


    var sub20FR = $("#sub_inland_20FR" + idRate).html();
    var sub40FR = $("#sub_inland_40FR" + idRate).html();




    if (theElement.prop('checked')) {


        //Dry
        sub20o.val(parseFloat(i20));
        sub40o.val(parseFloat(i40));
        sub40ho.val(parseFloat(i40h));
        sub40noro.val(parseFloat(i40nor));
        sub45ho.val(parseFloat(i45h));

        sub20 = parseFloat(sub20o.val()) + parseFloat(sub20d.val());
        sub40 = parseFloat(sub40o.val()) + parseFloat(sub40d.val());
        sub40h = parseFloat(sub40ho.val()) + parseFloat(sub40hd.val());
        sub40nor = parseFloat(sub40noro.val()) + parseFloat(sub40nord.val());
        sub45h = parseFloat(sub45ho.val()) + parseFloat(sub45hd.val());

        tot_20 = parseFloat(tot20dv_val) + parseFloat(sub20);
        tot_40 = parseFloat(tot40dv_val) + parseFloat(sub40);
        tot_40hc = parseFloat(tot40hc_val) + parseFloat(sub40h);
        tot_40nor = parseFloat(tot40nor_val) + parseFloat(sub40nor);
        tot_45hc = parseFloat(tot45hc_val) + parseFloat(sub45h);

        //Refeer

        sub20RFo.val(parseFloat(i20RF));
        sub40RFo.val(parseFloat(i40RF));
        sub40HCRFo.val(parseFloat(i40HCRF));

        sub20RF = parseFloat(sub20RFo.val()) + parseFloat(sub20RFd.val());
        sub40RF = parseFloat(sub40RFo.val()) + parseFloat(sub40RFd.val());
        sub40HCRF = parseFloat(sub40HCRFo.val()) + parseFloat(sub40HCRFd.val());

        tot_20rf = parseFloat(tot20rf_val) + parseFloat(sub20RF);
        tot_40rf = parseFloat(tot40rf_val) + parseFloat(sub40RF);
        tot_40hcrf = parseFloat(tot40hcrf_val) + parseFloat(sub40HCRF);

        //OT
        sub20OTo.val(parseFloat(i20OT));
        sub40OTo.val(parseFloat(i40OT));

        sub20OT = parseFloat(sub20OTo.val()) + parseFloat(sub20OTd.val());
        sub40OT = parseFloat(sub40OTo.val()) + parseFloat(sub40OTd.val());

        tot_20ot = parseFloat(tot20ot_val) + parseFloat(sub20OT);
        tot_40ot = parseFloat(tot40ot_val) + parseFloat(sub40OT);


        //RACK 


        sub20FRo.val(parseFloat(i20FR));
        sub40FRo.val(parseFloat(i40FR));

        sub20FR = parseFloat(sub20FRo.val()) + parseFloat(sub20FRd.val());
        sub40FR = parseFloat(sub40FRo.val()) + parseFloat(sub40FRd.val());

        tot_20fr = parseFloat(tot20fr_val) + parseFloat(sub20FR);
        tot_40fr = parseFloat(tot40fr_val) + parseFloat(sub40FR);



    } else {


        //Dry
        sub20o.val(0.00);
        sub40o.val(0.00);
        sub40ho.val(0.00);
        sub40noro.val(0.00);
        sub45ho.val(0.00);

        //Refeer
        sub20RFo.val(0.00);
        sub40RFo.val(0.00);
        sub40HCRFo.val(0.00);

        //OT
        sub20OTo.val(0.00);
        sub40OTo.val(0.00);

        //FR
        sub20FRo.val(0.00);
        sub40FRo.val(0.00);


        // DRY
        if (parseFloat(sub20o.val()) > parseFloat(sub20d.val()))
            sub20 = parseFloat(sub20o.val()) - parseFloat(sub20d.val());

        else
            sub20 = parseFloat(sub20d.val()) - parseFloat(sub20o.val());

        tot_20 = parseFloat(tot20dv_val) + parseFloat(sub20);

        if (parseFloat(sub40o.val()) > parseFloat(sub40d.val()))
            sub40 = parseFloat(sub40o.val()) - parseFloat(sub40d.val());
        else
            sub40 = parseFloat(sub40d.val()) - parseFloat(sub40o.val());

        tot_40 = parseFloat(tot40dv_val) + parseFloat(sub40);


        if (parseFloat(sub40ho.val()) > parseFloat(sub40hd.val()))
            sub40h = parseFloat(sub40ho.val()) - parseFloat(sub40hd.val());
        else
            sub40h = parseFloat(sub40hd.val() - parseFloat(sub40ho.val()));

        tot_40hc = parseFloat(tot40hc_val) + parseFloat(sub40h);

        if (parseFloat(sub40noro.val()) > parseFloat(sub40nord.val()))
            sub40nor = parseFloat(sub40noro.val()) - parseFloat(sub40nord.val());
        else
            sub40nor = parseFloat(sub40nord.val() - parseFloat(sub40noro.val()));

        tot_40nor = parseFloat(tot40nor_val) + parseFloat(sub40nor);


        if (parseFloat(sub45ho.val()) > parseFloat(sub45hd.val()))
            sub45h = parseFloat(sub45ho.val()) - parseFloat(sub45hd.val());
        else
            sub45h = parseFloat(sub45hd.val() - parseFloat(sub45ho.val()));

        tot_45hc = parseFloat(tot45hc_val) + parseFloat(sub45h);


        // Refeer

        if (parseFloat(sub20RFo.val()) > parseFloat(sub20RFd.val()))
            sub20RF = parseFloat(sub20RFo.val()) - parseFloat(sub20RFd.val());
        else
            sub20RF = parseFloat(sub20RFd.val()) - parseFloat(sub20RFo.val());

        tot_20rf = parseFloat(tot20rf_val) + parseFloat(sub20RF);


        if (parseFloat(sub40RFo.val()) > parseFloat(sub40RFd.val()))
            sub40RF = parseFloat(sub40RFo.val()) - parseFloat(sub40RFd.val());
        else
            sub40RF = parseFloat(sub40RFd.val()) - parseFloat(sub40RFo.val());


        tot_40rf = parseFloat(tot40rf_val) + parseFloat(sub40RF);


        if (parseFloat(sub40HCRFo.val()) > parseFloat(sub40HCRFd.val()))
            sub40HCRF = parseFloat(sub40HCRFo.val()) - parseFloat(sub40HCRFd.val());
        else
            sub40HCRF = parseFloat(sub40HCRFd.val() - parseFloat(sub40HCRFo.val()));

        tot_40hcrf = parseFloat(tot40hcrf_val) + parseFloat(sub40HCRF);

        //OT

        if (parseFloat(sub20OTo.val()) > parseFloat(sub20OTd.val()))
            sub20OT = parseFloat(sub20OTo.val()) - parseFloat(sub20OTd.val());
        else
            sub20OT = parseFloat(sub20OTd.val()) - parseFloat(sub20OTo.val());

        tot_20ot = parseFloat(tot20ot_val) + parseFloat(sub20OT);


        if (parseFloat(sub40OTo.val()) > parseFloat(sub40OTd.val()))
            sub40OT = parseFloat(sub40OTo.val()) - parseFloat(sub40OTd.val());
        else
            sub40OT = parseFloat(sub40OTd.val()) - parseFloat(sub40OTo.val());

        tot_40ot = parseFloat(tot40ot_val) + parseFloat(sub40OT);

        //Flat Rack 

        if (parseFloat(sub20FRo.val()) > parseFloat(sub20FRd.val()))
            sub20FR = parseFloat(sub20FRo.val()) - parseFloat(sub20FRd.val());
        else
            sub20FR = parseFloat(sub20FRd.val()) - parseFloat(sub20FRo.val());

        tot_20fr = parseFloat(tot20fr_val) + parseFloat(sub20FR);


        if (parseFloat(sub40FRo.val()) > parseFloat(sub40FRd.val()))
            sub40FR = parseFloat(sub40FRo.val()) - parseFloat(sub40FRd.val());
        else
            sub40FR = parseFloat(sub40FRd.val()) - parseFloat(sub40FRo.val());

        tot_40fr = parseFloat(tot40fr_val) + parseFloat(sub40FR);



    }
    //DRY

    if (isDecimal == 1) {

        $("#sub_inland_20DV" + idRate).html(sub20.toFixed(2));
        $("#sub_inland_40DV" + idRate).html(sub40.toFixed(2));
        $("#sub_inland_40HC" + idRate).html(sub40h.toFixed(2));
        $("#sub_inland_40NOR" + idRate).html(sub40nor.toFixed(2));
        $("#sub_inland_45HC" + idRate).html(sub45h.toFixed(2));

        tot20dv_html.html(tot_20.toFixed(2));
        tot40dv_html.html(tot_40.toFixed(2));
        tot40hc_html.html(tot_40hc.toFixed(2));
        tot40nor_html.html(tot_40nor.toFixed(2));
        tot45hc_html.html(tot_45hc.toFixed(2));

        //refeer
        $("#sub_inland_20RF" + idRate).html(sub20RF.toFixed(2));
        $("#sub_inland_40RF" + idRate).html(sub40RF.toFixed(2));
        $("#sub_inland_40HCRF" + idRate).html(sub40HCRF.toFixed(2));

        tot20rf_html.html(tot_20rf.toFixed(2));
        tot40rf_html.html(tot_40rf.toFixed(2));
        tot40hcrf_html.html(tot_40hcrf.toFixed(2));

        //OT


        $("#sub_inland_20OT" + idRate).html(sub20OT.toFixed(2));
        $("#sub_inland_40OT" + idRate).html(sub40OT.toFixed(2));


        tot20ot_html.html(tot_20ot.toFixed(2));
        tot40ot_html.html(tot_40ot.toFixed(2));


        //FR

        $("#sub_inland_20FR" + idRate).html(sub20FR.toFixed(2));
        $("#sub_inland_40FR" + idRate).html(sub40FR.toFixed(2));

        tot20fr_html.html(tot_20fr.toFixed(2));
        tot40fr_html.html(tot_40fr.toFixed(2));

    } else {
        //Dry
        tot20dv_html.html(Math.round(tot_20));
        tot40dv_html.html(Math.round(tot_40));
        tot40hc_html.html(Math.round(tot_40hc));
        tot40nor_html.html(Math.round(tot_40nor));
        tot45hc_html.html(Math.round(tot_45hc));

        $("#sub_inland_20DV" + idRate).html(Math.round(sub20));
        $("#sub_inland_40DV" + idRate).html(Math.round(sub40));
        $("#sub_inland_40HC" + idRate).html(Math.round(sub40h));
        $("#sub_inland_40NOR" + idRate).html(Math.round(sub40nor));
        $("#sub_inland_45HC" + idRate).html(Math.round(sub45h));


        //refeer
        $("#sub_inland_20RF" + idRate).html(Math.round(sub20RF));
        $("#sub_inland_40RF" + idRate).html(Math.round(sub40RF));
        $("#sub_inland_40HCRF" + idRate).html(Math.round(sub40HCRF));

        tot20rf_html.html(Math.round(tot_20rf));
        tot40rf_html.html(Math.round(tot_40rf));
        tot40hcrf_html.html(Math.round(tot_40hcrf));

        //OT


        $("#sub_inland_20OT" + idRate).html(Math.round(sub20OT));
        $("#sub_inland_40OT" + idRate).html(Math.round(sub40OT));


        tot20ot_html.html(Math.round(tot_20ot));
        tot40ot_html.html(Math.round(tot_40ot));


        //FR

        $("#sub_inland_20FR" + idRate).html(Math.round(sub20FR));
        $("#sub_inland_40FR" + idRate).html(Math.round(sub40FR));

        tot20fr_html.html(Math.round(tot_20fr));
        tot40fr_html.html(Math.round(tot_40fr));

    }





});

//Calcular el volumen individual
$(document).on("change keydown keyup", ".quantity, .height ,.width ,.large,.weight", function() {
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
    $(".width").each(function() {
        $(this).each(function() {
            width = $(this).val();
            if (!isNaN(width)) {
                width = parseInt(width);
            }
        });
    });
    $(".height").each(function() {
        $(this).each(function() {
            thickness = $(this).val();
            if (!isNaN(thickness)) {
                thickness = parseInt(thickness);
            }
        });
    });
    $(".quantity").each(function() {
        $(this).each(function() {
            quantity = $(this).val();
            if (!isNaN(quantity)) {
                quantity = parseInt(quantity);
            }
        });
    });
    $(".weight").each(function() {
        $(this).each(function() {
            weight = $(this).val();
            if (weight != '') {
                weight = parseFloat(weight);
            }
        });
    });

    $(".large").each(function() {
        $(this).each(function() {
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

        if (thickness > 0 || length > 0 || quantity > 0) {
            volume = Math.round(thickness * length * width * quantity / 10000) / 100;
            if (isNaN(volume)) {
                volume = 0;
            }
        }
        if ($(this).val() != '') {
            $(this).closest('.template').find('.volume').html(volume + " m<sup>3</sup>");
            $(this).closest('.template').find('.volume_input').val(volume);
        }
        $(this).closest('.template').find('.quantity').html(" " + quantity + " un");
        $(this).closest('.template').find('.weight').html(" " + weight * quantity + " kg");
        $(this).closest('.template').find('.quantity_input').val(quantity);
        $(this).closest('.template').find('.weight_input').val(weight * quantity);
        $(this).closest('.template').find('.volume_input').change();
        $(this).closest('.template').find('.quantity_input').change();
        $(this).closest('.template').find('.weight_input').change();
    });
});

//Calculos por cantidad
$(document).on("change keydown keyup", ".quantity_input", function() {
    var sum = 0;
    //iterate through each textboxes and add the values
    $(".quantity_input").each(function() {
        //add only if the value is number
        if ($(this).val() > 0 && $(this).val() != '') {
            sum += parseInt($(this).val());
        } else if ($(this).val().length != 0) {
            $(this).css("background-color", "red");
        }
    });
    $("#total_quantity_pkg").html(sum + " un");
    $("#total_quantity_pkg_input").val(sum);
});

//Calculos por volumen
$(document).on("change keydown keyup", ".volume_input", function() {
    var sum = 0;
    //iterate through each textboxes and add the values
    $(".volume_input").each(function() {
        //add only if the value is number
        if ($(this).val() > 0 && $(this).val() != '') {
            sum += parseFloat($(this).val());
        } else if ($(this).val().length != 0) {
            $(this).css("background-color", "red");
        }
    });

    $("#total_volume_pkg").html((parseFloat(sum).toFixed(2)) + " m3");
    $("#total_volume_pkg_input").val(parseFloat(sum).toFixed(2));
});

//Calculos por peso
$(document).on("change keydown keyup", ".weight_input", function() {
    var sum = 0;
    var sum_vol = 0;

    //iterate through each textboxes and add the values
    $(".weight_input").each(function() {
        //add only if the value is number
        if ($(this).val() > 0 && $(this).val() != '') {
            sum += parseFloat($(this).val());
        }
    });
    $("#total_weight_pkg").html(sum + " kg");
    $("#total_weight_pkg_input").val(sum);

    $(".volume_input").each(function() {
        //add only if the value is number
        if ($(this).val() > 0 && $(this).val() != '') {
            sum_vol += parseFloat($(this).val());
        } else if ($(this).val().length != 0) {
            $(this).css("background-color", "red");
        }
    });
    var chargeable_weight = 0;
    var weight = sum;
    //Calculate chargeable weight
    if ($('#quoteType').val() == 2) {
        total_vol_chargeable = sum_vol;
        total_weight = weight / 1000;
        if (total_vol_chargeable > total_weight) {
            chargeable_weight = total_vol_chargeable;
        } else {
            chargeable_weight = total_weight;
        }
        $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2) + " m<sup>3</sup>");
    } else if ($('#quoteType').val() == 3) {
        total_vol_chargeable = sum_vol * 166.67;
        if (total_vol_chargeable > weight) {
            chargeable_weight = total_vol_chargeable;
        } else {
            chargeable_weight = weight;
        }
        $("#chargeable_weight_pkg").html(parseFloat(chargeable_weight).toFixed(2) + " kg");
    }


    $("#chargeable_weight_pkg_input").val(chargeable_weight);
});

//Calcular peso tasable
$(document).on('change keyup keydown', '#total_volume, #total_weight', function() {
    var chargeable_weight = 0;
    var volume = 0;
    var total_volume = 0;
    var total_weight = 0;

    if (($('#total_volume').val() != '' && $('#total_volume').val() > 0) && ($('#total_weight').val() != '' && $('#total_weight').val() > 0)) {

        total_volume = $('#total_volume').val();
        total_weight = $('#total_weight').val();
        if ($("#quoteType").val() == 2) {

            total_weight = total_weight / 1000;
            if (total_volume > total_weight) {
                chargeable_weight = total_volume;
            } else {
                chargeable_weight = total_weight;
            }
            $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2) + " m<sup>3</sup>");
        } else if ($("#quoteType").val() == 3) {

            total_volume = total_volume * 166.67;
            if (total_volume > total_weight) {
                chargeable_weight = total_volume;
            } else {
                chargeable_weight = total_weight;
            }
            $("#chargeable_weight_total").html(parseFloat(chargeable_weight).toFixed(2) + " kg");
        }

        $("#chargeable_weight_pkg_input").val(chargeable_weight);
    }
});

//Cambiar tipo de envio
$(document).on('change', '#delivery_type_air', function(e) {

    if ($(this).val() == 5) {
        $("#origin_address_label").addClass('hide');
        $("#destination_address_label").addClass('hide');
        $("#origin_address").val('');
        $("#destination_address").val('');
    }
    if ($(this).val() == 6) {
        $("#origin_address_label").addClass('hide');
        $("#destination_address_label").removeClass('hide');
        $("#origin_address").val('');
    }
    if ($(this).val() == 7) {
        $("#origin_address_label").removeClass('hide');
        $("#destination_address_label").addClass('hide');
        $("#destination_address").val('');
    }
    if ($(this).val() == 8) {
        $("#origin_address_label").removeClass('hide');
        $("#destination_address_label").removeClass('hide');
    }
});

//Agregar inputs din??micos en LCL/AIR
$(document).on('click', '#add_load_lcl_air', function(e) {
    var $template = $('#lcl_air_load_template');
    $clone = $template.clone().removeClass('hide').removeAttr('id');

    $clone.find('.type_cargo').prop('required', true);
    $clone.find('.quantity').prop('required', true);
    $clone.find('.height').prop('required', true);
    $clone.find('.width').prop('required', true);
    $clone.find('.large').prop('required', true);
    $clone.find('.weight').prop('required', true);

    $clone.insertBefore($template);



});

//Guardar compa????a
$(document).on('click', '#savecompany', function() {

    var $element = $('#addContactModal');

    var $buss = $('.business_name_input').val();
    var $phone = $('.phone_input').val();
    var $email = $('.email_input').val();
    var $tax_number = $('.tax_number_input').val();


    if ($buss != '' && $phone != '' && $email != '' && $tax_number != '') {
        $.ajax({
            type: 'POST',
            url: '/companies',
            data: {
                'business_name': $('.business_name_input').val(),
                'phone': $('.phone_input').val(),
                'address': $('.address_input').val(),
                'email': $('.email_input').val(),
                'tax_number':$('.tax_number_input').val(),
            },
            success: function(data) {
                $.ajax({
                    url: "company/companies",
                    dataType: 'json',
                    success: function(dataC) {
                        $('select[name="company_id_quote"]').empty();
                        $.each(dataC, function(key, value) {
                            $('select[name="company_id_quote"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                        $('select[name="company_id"]').empty();
                        $.each(dataC, function(key, value) {
                            $('select[name="company_id"]').append('<option value="' + key + '">' + value + '</option>');
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
                    error: function(request, status, error) {
                        alert(request.responseText);
                    }
                });
            },
            error: function(request, status, error) {
                swal(
                    'Error!',
                    'Please contact administrator',
                    'error'
                )
            }
        });
    } else {
        swal(
            'Sorry!',
            'All fields are required',
            'warning'
        )

    }

});

// Remover Surcharge 

$(document).on('click', '.removeSurcharge', function() {
    $(this).closest('div').remove();
    /* $i = 1;
     $('.closetr').each(function() {
         var res = $(".port_orig", this).removeAttr('name').attr('name', 'port_orig' + $i + '[]');
         var resDest = $(".port_dest", this).removeAttr('name').attr('name', 'port_dest' + $i + '[]');
         var car = $(".carrier", this).removeAttr('name').attr('name', 'localcarrier' + $i + '[]');
         $i++;
     });*/
});

//Agregar Surcharge

$(document).on('click', '#addSurcharge', function() {


    var $template = $('#cloneSurcharge');
    $myClone = $template.clone().removeClass('hide').removeAttr('id');

    $myClone.find(".typeC").removeAttr('name').attr('name', 'type[]');
    $myClone.find(".calculationC").attr('name', 'calculation[]');
    $myClone.find(".currencyC").attr('name', 'currency[]');
    $myClone.find(".amountC").attr('name', 'amount[]');
    $myClone.find("select").select2();
    $("#colSurcharge").append($myClone);

});

//Guardar contacto
$(document).on('click', '#savecontact', function() {

    var $element = $('#contactModal');

    var $name = $('.first_namec_input').val();
    var $lastname = $('.last_namec_input').val();
    var $email = $('.emailc_input').val();
    var $company_id = $('.companyc_input').val();

    if (($name != '') && ($lastname != '') && ($email != '') && ($company_id)) {
        $.ajax({
            type: 'POST',
            url: '/contacts',
            data: {
                'first_name': $('.first_namec_input').val(),
                'last_name': $('.last_namec_input').val(),
                'email': $('.emailc_input').val(),
                'phone': $('.phonec_input').val(),
                'company_id': $('.companyc_input').val(),

            },
            success: function(data) {
                var company_id = $("select[name='company_id_quote']").val();
                $.ajax({
                    url: "contacts/contact/" + company_id,
                    dataType: 'json',
                    success: function(dataC) {
                        $('select[name="contact_id"]').empty();
                        $.each(dataC, function(key, value) {
                            $('select[name="contact_id"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                        $('#contactModal').modal('hide');

                        swal(
                            'Done!',
                            'Register completed',
                            'success'
                        )
                    },
                    error: function(request, status, error) {
                        swal(
                            'Error!',
                            'Please contact administrator',
                            'error'
                        )
                    }
                });
            },
            error: function(request, status, error) {
                swal(
                    'Error!',
                    'Please contact administrator',
                    'error'
                )
            }

        });

    } else {

        swal(
            'Sorry!',
            'Fields with * are mandatory',
            'warning'
        )

    }







});

//Remover inputs LCL/AIR
$(document).on('click', '.remove_lcl_air_load', function(e) {
    var $row = $(this).closest('.template').remove();
    $row.remove();

    $('.quantity').change();
    $('.height').change();
    $('.width').change();
    $('.large').change();
    $('.weight').change();
});

/** Select2 **/
$('.m-select2-general').select2({
    placeholder: "Select an option"
});

$('.m-select2-edit').select2({
    placeholder: "Select an option"
});

$('#origin_airport_create').select2({
    dropdownParent: $('#createRateModal'),
    placeholder: "Select an option",
    minimumInputLength: 2,
    ajax: {
        url: '/quotes/airports/find',
        dataType: 'json',
        data: function(params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function(data) {
            return {
                results: data
            };
        },
    }
});

$('#destination_airport_create').select2({
    dropdownParent: $('#createRateModal'),
    placeholder: "Select an option",
    minimumInputLength: 2,
    ajax: {
        url: '/quotes/airports/find',
        dataType: 'json',
        data: function(params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function(data) {
            return {
                results: data
            };
        },
    }
});

$('.select2-freight').select2();

$('.select2-origin').select2();

$('.select2-destination').select2();

//Combo select2 para Aereopuertos origen
$('#origin_airport').select2({
    placeholder: "Select an option",
    minimumInputLength: 2,
    ajax: {
        url: '/quotes/airports/find',
        dataType: 'json',
        data: function(params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function(data) {
            return {
                results: data
            };
        },
    }
});

//Combo select2 para Aereopuertos destinos
$('#destination_airport').select2({
    placeholder: "Select an option",
    minimumInputLength: 2,
    ajax: {
        url: '/quotes/airports/find',
        dataType: 'json',
        data: function(params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function(data) {
            return {
                results: data
            };
        },
    }
});

//Combo select2 para Companies em Search Rates
$('.company_dropdown').select2({
    placeholder: "Select an option",
    minimumInputLength: 2,
    ajax: {
        url: '/companies/search',
        dataType: 'json',
        data: function(params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function(data) {
            return {
                results: data
            };
        },
    }
});

//portharbors
$('.portharbors').select2({
    placeholder: "Search a port",
    minimumInputLength: 3,
    ajax: {
        url: '/harbor/search',
        dataType: 'json',
        data: function(params) {
            return {
                q: $.trim(params.term)
            };
        },
        processResults: function(data) {
            return {
                results: data
            };
        },
    }
});

$('.m-select2-general').select2({
    placeholder: "Select an option"
});


//Datetimepicker
$('.date_issued').datetimepicker();

/** Funciones **/

function calcularInlands(tipo, idRate) {


    if (tipo == 'destino') {
        var i20 = $("#valor-d201-" + idRate).html();
        var i40 = $("#valor-d401-" + idRate).html();
        var i40h = $("#valor-d40h1-" + idRate).html();


    } else {

        var i20 = $("#valor-o201-" + idRate).html();
        var i40 = $("#valor-o401-" + idRate).html();
        var i40h = $("#valor-o40h1-" + idRate).html();
    }

    var sub20d = $("#sub_inland_20_d" + idRate);
    var sub40d = $("#sub_inland_40_d" + idRate);
    var sub40hd = $("#sub_inland_40h_d" + idRate);

    var sub20o = $("#sub_inland_20_o" + idRate);
    var sub40o = $("#sub_inland_40_o" + idRate);
    var sub40ho = $("#sub_inland_40h_o" + idRate);

    var sub20 = $("#sub_inland_20" + idRate).html();
    var sub40 = $("#sub_inland_40" + idRate).html();
    var sub40h = $("#sub_inland_40h" + idRate).html();

    if (tipo == 'destino') {

        sub20d.val(parseFloat(i20));
        sub40d.val(parseFloat(i40));
        sub40hd.val(parseFloat(i40h));

    } else {

        sub20o.val(parseFloat(i20));
        sub40o.val(parseFloat(i40));
        sub40ho.val(parseFloat(i40h));

    }

    sub20 = parseFloat(sub20o.val()) + parseFloat(sub20d.val());
    sub40 = parseFloat(sub40o.val()) + parseFloat(sub40d.val());
    sub40h = parseFloat(sub40ho.val()) + parseFloat(sub40hd.val());



    $("#sub_inland_20" + idRate).html(sub20);
    $("#sub_inland_40" + idRate).html(sub40);
    $("#sub_inland_40h" + idRate).html(sub40h);


}

function show_hide_element($element, $button) {
    if ($('.' + $element).hasClass('hide')) {
        $('.' + $element).removeClass('hide');
    } else {
        $('.' + $element).addClass('hide');
    }
}

function precargar() {
    //Otros 


    var origComb = $("#origComb").val();
    var destComb = $("#destComb").val();

    var ids = $('#origin_harbor').val();
    $.ajax({
        type: 'GET',
        url: '/inlandD/getDistance/' + ids,
        success: function(data) {
            $('select[name="originA"]').empty();
            if (data.message == 'Ok') {




                $("#selectA").removeClass('hide');
                $("#textA").addClass('hide');

                $.each(data.data, function(key, value) {
                    if (key == origComb) {
                        selected = 'selected';
                    } else {
                        selected = '';
                    }
                    $('select[name="originA"]').append('<option ' + selected + ' value="' + key + '">' + value + '</option>');
                });
            }
            if (data.message == 'maxOne') {
                $("#selectA").removeClass('hide');
                $("#textA").addClass('hide');

                $('#select2-originA-container').text('Select one Origin Harbor');
            }
            if (data.message == 'empty') {
                $("#selectA").addClass('hide');
                $("#textA").removeClass('hide');


            }
        }
    });
    var ids = $('#destination_harbor').val();
    $.ajax({
        type: 'GET',
        url: '/inlandD/getDistance/' + ids,
        success: function(data) {
            $('select[name="destinationA"]').empty();




            if (data.message == 'Ok') {
                $("#selectD").removeClass('hide');
                $("#textD").addClass('hide');
                $.each(data.data, function(key, value) {
                    if (key == destComb) {
                        selected = 'selected';
                    } else {
                        selected = '';
                    }
                    $('select[name="destinationA"]').append('<option ' + selected + '  value="' + key + '">' + value + '</option>');
                });
            }

            if (data.message == 'maxOne') {
                $("#selectD").removeClass('hide');
                $("#textD").addClass('hide');
                $('#select2-destinationA-container').text('Select one Dest Harbor');
            }

            if (data.message == 'empty') {
                $("#selectD").addClass('hide');
                $("#textD").removeClass('hide');


            }




        }
    });


    //Companiasss 
    var company_id = $("#company_id_num").val();
    var contact_id = $("#contact_id_num").val();

    if ($("#price_id_num").val() != '') {
        var price_id = $("#price_id_num").val();
        $("#price_").select2().val(price_id).trigger("change");


    }



    var selected = '';
    var selected_price = '';

    if (company_id) {
        $('select[name="contact_id"]').empty();
        $('select[name="contact_id"]').prop("disabled", false);

        $.ajax({
            url: "/quotes/company/contact/id/" + company_id,
            dataType: 'json',
            success: function(data) {
                $('select[name="client"]').empty();
                $.each(data, function(key, value) {
                    if (key == contact_id) {
                        selected = 'selected';
                    } else {
                        selected = '';
                    }

                    $('select[name="contact_id"]').append('<option ' + selected + ' value="' + key + '">' + value + '</option>');
                });
            }
        });

        $.ajax({
            url: "/quotes/company/price/id/" + company_id,
            dataType: 'json',
            success: function(data) {


                $('select[name="price_id"]').empty();
                $.each(data, function(key, value) {
                    if (key == price_id) {
                        selected_price = 'selected';
                    } else {
                        selected_price = '';
                    }
                    $('select[name="price_id"]').append('<option ' + selected_price + ' value="0">Select an option</option>');
                    $('select[name="price_id"]').append('<option ' + selected_price + ' value="' + key + '">' + value + '</option>');
                });
            }
        });
    }
}

function display(id) {

    var freight = $("#freight" + id);
    var origin = $("#origin" + id);
    var destiny = $("#destiny" + id);
    var inland = $("#inland" + id);
    var remark = $("#remark" + id);

    if (freight.attr('hidden')) {
        $("#freight" + id).removeAttr('hidden');
        $("#remark" + id).attr('hidden', 'true');
    } else {
        $("#freight" + id).attr('hidden', 'true');
    }

    if (origin.attr('hidden')) {
        $("#origin" + id).removeAttr('hidden');
    } else {
        $("#origin" + id).attr('hidden', 'true');
    }

    if (destiny.attr('hidden')) {
        $("#destiny" + id).removeAttr('hidden');
    } else {
        $("#destiny" + id).attr('hidden', 'true');
    }
    if (inland.attr('hidden')) {
        $("#inland" + id).removeAttr('hidden');
    } else {
        $("#inland" + id).attr('hidden', 'true');
    }
}

function display_r(id) {

    var freight = $("#freight" + id);
    var origin = $("#origin" + id);
    var destiny = $("#destiny" + id);
    var inland = $("#inland" + id);
    var remark = $("#remark" + id);
    if (remark.attr('hidden')) {
        $("#remark" + id).removeAttr('hidden');
        $("#freight" + id).attr('hidden', 'true');
        $("#origin" + id).attr('hidden', 'true');
        $("#destiny" + id).attr('hidden', 'true');
        $("#inland" + id).attr('hidden', 'true');
    } else {
        $("#remark" + id).attr('hidden', 'true');
    }

}

function change_tab(tab) {
    if (tab == 2) {
        //Quitar validaciones del primer TAB 
        $("#total_quantity").removeAttr("required");
        $("#total_weight").removeAttr("required");
        $("#total_volume").removeAttr("required");


        $(".type_cargo_2").prop("required", true);

        $(".quantity_2").prop("required", true);
        $(".height_2").prop("required", true);
        $(".width_2").prop("required", true);
        $(".large_2").prop("required", true);
        $(".weight_2").prop("required", true);


        $("#total_quantity").val('');
        $("#total_weight").val('');
        $("#total_volume").val('');
        $("#chargeable_weight_pkg_input").val('');
        $("#chargeable_weight_total").html('');

    } else {
        //colocar validaciones al cambiar tab 
        $("#total_quantity").prop("required", true)
        $("#total_weight").prop("required", true)
        $("#total_volume").prop("required", true);

        $('#lcl_air_load').find('.quantity').val('').removeAttr('required');
        $('#lcl_air_load').find('.height').val('').removeAttr('required');
        $('#lcl_air_load').find('.width').val('').removeAttr('required');
        $('#lcl_air_load').find('.large').val('').removeAttr('required');
        $('#lcl_air_load').find('.weight').val('').removeAttr('required');
        $('#lcl_air_load').find('.volume').val('').removeAttr('required');



        $("#total_quantity_pkg_input").val('');
        $("#total_weight_pkg_input").val('');
        $("#total_volume_pkg_input").val('');
        $("#chargeable_weight_pkg_input").val('');
        $("#chargeable_weight_pkg").html('');
    }
}

function precargarLCL() {


    // Validaciones por defecto

    if ($("#total_quantity").val() != "") {
        $("#total_quantity").prop("required", true);
        $("#total_weight").prop("required", true);
        $("#total_volume").prop("required", true);
    }



    $(".infocheck").val('');
    $(".quote_search").show();



    $("#origin_harbor").prop("disabled", false);
    $("#destination_harbor").prop("disabled", false);
    $("#equipment_id").hide();
    $("#equipment").prop("disabled", true);
    $("#equipment").removeAttr('required');
    $("#delivery_type").prop("disabled", false);
    $("#delivery_type_air").prop("disabled", true);
    $("#delivery_type_label").show();
    $("#delivery_type_air_label").hide();
    $("#lcl_air_load").show();
    $("#origin_harbor_label").show();
    $("#destination_harbor_label").show();
    $("#airline_label").hide();
    $("#carrier_label").show();

    $("#fcl_load").hide();
    $("#origin_airport_label").hide();
    $("#destination_airport_label").hide();
    $("input[name=qty_20]").val('');
    $("input[name=qty_40]").val('');
    $("input[name=qty_40_hc]").val('');
    $("input[name=qty_45_hc]").val('');

}

function addSaleCharge($value) {

    var $template = $('#sale_charges_' + $value),
        $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id')
        .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
}

function addFreightCharge($value) {
    var $template = $('#freight_charges_' + $value),
        $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id')
        .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
}

function addOriginCharge($value) {
    var $template = $('#origin_charges_' + $value),
        $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id')
        .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
}

function addDestinationCharge($value) {
    var $template = $('#destination_charges_' + $value),
        $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id')
        .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
}

function addInlandCharge($value) {
    var $template = $('#inland_charges_' + $value),
        $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id')
        .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
}

//Editar remarks
function edit_remark($span, $textarea, $update_box) {
    $('.' + $span).attr('hidden', 'true');
    $('.' + $textarea).removeAttr('hidden');
    $('.' + $update_box).removeAttr('hidden');
}

//Cancelar editar remarks
function cancel_update($span, $textarea, $update_box) {
    $('.' + $span).removeAttr('hidden');
    $('.' + $textarea).attr('hidden', 'true');
    $('.' + $update_box).attr('hidden', 'true');
}

//Actualizar remarks
function update_remark($id, $content, $v, $language) {
    var id = $(".id").val();
    var remarks = tinymce.get($content).getContent();
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/update/remarks/' + $id,
        data: {
            'remarks': remarks,
            'language': $language,
        },
        success: function(data) {
            if (data.message == 'Ok') {
                swal(
                    'Updated!',
                    'The remarks has been updated.',
                    'success'
                )
                if ($language == 'all') {
                    $(".remarks_box_" + $v).html(data.rate['remarks']);
                    $(".remarks_span_" + $v).removeAttr('hidden');
                    $(".remarks_textarea_" + $v).attr('hidden', 'true');
                    $(".update_remarks_" + $v).attr('hidden', 'true');
                } else if ($language == 'english') {
                    $(".remarks_box_english_" + $v).html(data.rate['remarks_english']);
                    $(".remarks_span_english_" + $v).removeAttr('hidden');
                    $(".remarks_textarea_english_" + $v).attr('hidden', 'true');
                    $(".update_remarks_english_" + $v).attr('hidden', 'true');
                } else if ($language == 'spanish') {
                    $(".remarks_box_spanish_" + $v).html(data.rate['remarks_spanish']);
                    $(".remarks_span_spanish_" + $v).removeAttr('hidden');
                    $(".remarks_textarea_spanish_" + $v).attr('hidden', 'true');
                    $(".update_remarks_spanish_" + $v).attr('hidden', 'true');
                } else if ($language == 'portuguese') {
                    $(".remarks_box_portuguese_" + $v).html(data.rate['remarks_portuguese']);
                    $(".remarks_span_portuguese_" + $v).removeAttr('hidden');
                    $(".remarks_textarea_portuguese_" + $v).attr('hidden', 'true');
                    $(".update_remarks_portuguese_" + $v).attr('hidden', 'true');
                }

            }
        }
    });
};

function openTab(evt, type, id) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(type).style.display = "block";
    evt.currentTarget.className += " active";
    if (type == 'all') {
        type = 'total in';
    }
    changeType(type, id);
}

function changeType(type, id) {
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/feature/pdf/update',
        data: { "value": type, "name": "show_type", "id": id },
        success: function(data) {
            if (data.message == 'Ok') {
                //$(this).attr('checked', true).val(0);
            }
        }
    });
}

function currencyRate(currency, currency_cfg, amount) {
    $.ajax({
        url: '/api/currency/' + currency,
        dataType: 'json',
        async: false,
        success: function(json) {
            if (currency_cfg + json.alphacode == json.api_code) {
                amount = parseFloat(amount) / json.rates;
            } else {
                amount = parseFloat(amount) / json.rates_eur;
            }
            amount = amount.toFixed(2);
        }
    });

    return amount;
}

function currencyRateAlphacode(currency, currency_cfg, value) {
    $.ajax({
        url: '/api/currency/alphacode/' + currency,
        dataType: 'json',
        async: false,
        success: function(json) {
            if (currency_cfg + json.alphacode == json.api_code) {
                total_currency = value / json.rates;
            } else {
                total_currency = value / json.rates_eur;
            }
            total_currency = total_currency.toFixed(2);
        }
    });

    return parseFloat(total_currency);
}

function notification(message, type) {

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

    switch (type) {
        case "error":
            toastr.error(message, 'ERROR');
            break;
        case "success":
            toastr.success(message, 'SUCCESS');
            break;
        default:
            toastr.info(message, '');
    }
}



$(document).on('change', '#origin_harbor', function(e) {
    var ids = $('#origin_harbor').val();
    $.ajax({
        type: 'GET',
        url: '/inlandD/getDistance/' + ids,
        success: function(data) {
            $('select[name="originA"]').empty();
            if (data.message == 'Ok') {

                $("#selectA").removeClass('hide');
                $("#textA").addClass('hide');

                $.each(data.data, function(key, value) {
                    $('select[name="originA"]').append('<option  value="' + key + '">' + value + '</option>');
                });
            }
            if (data.message == 'maxOne') {
                $("#selectA").removeClass('hide');
                $("#textA").addClass('hide');

                $('#select2-originA-container').text('Select one Origin Harbor');
            }
            if (data.message == 'empty') {
                $("#selectA").addClass('hide');
                $("#textA").removeClass('hide');


            }
        }
    });
});


$(document).on('change', '#destination_harbor', function(e) {
    var ids = $('#destination_harbor').val();
    $.ajax({
        type: 'GET',
        url: '/inlandD/getDistance/' + ids,
        success: function(data) {
            $('select[name="destinationA"]').empty();
            if (data.message == 'Ok') {
                $("#selectD").removeClass('hide');
                $("#textD").addClass('hide');
                $.each(data.data, function(key, value) {
                    $('select[name="destinationA"]').append('<option  value="' + key + '">' + value + '</option>');
                });
            }

            if (data.message == 'maxOne') {
                $("#selectD").removeClass('hide');
                $("#textD").addClass('hide');
                $('#select2-destinationA-container').text('Select one Dest Harbor');
            }

            if (data.message == 'empty') {
                $("#selectD").addClass('hide');
                $("#textD").removeClass('hide');


            }




        }
    });
});