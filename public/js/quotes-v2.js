$.fn.editable.defaults.mode = 'inline';

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".open-inland-modal").click(function () {
        var rate_id = $(this).data('rate-id');
        $(".modal-body .automatic_rate_id").val(rate_id);
    });

    //Modal para editar rates
    $(document).on('click','.edit_rate_modal',function(){
        var url = "/v2/quotes/rates/edit";
        var rate_id = $(this).data('rate-id');
        $.get(url + '/' + rate_id, function (data) {
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
    if($('#show_hide_select').val()=='total in'){
        $(".group_origin_charges").addClass('hide');
        $(".group_freight_charges").addClass('hide');
        $(".group_destination_charges").addClass('hide');
    }    

    //Mostrar montos totales en Freight
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

    //Mostrar montos totales en Origin
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

    //Mostrar montos totales en destination
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
            setTimeout(location.reload.bind(location), 3000);
            if(!response) {
                return "Unknown error!";
            }

            if(response.success === false) {
                return response.msg;
            }
        }
    });

    $('.editable-saleterms').editable({
        url:'/v2/quotes/sale/charges/update',
        emptytext:0,
        success: function(response, newValue) {
            //setTimeout(location.reload.bind(location), 3000);
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

    $('.editable-quote-info').editable({
        url:'/v2/quotes/info/update',
        emptytext:0,
        success: function(response, newValue) {
            setTimeout(location.reload.bind(location), 3000);
            if(!response) {
                return "Unknown error!";
            }

            if(response.success === false) {
                return response.msg;
            }
        }
    });

    //Edición en línea para montos/markups en LCL/AIR
    $('.editable-lcl-air').editable({
        url:'/v2/quotes/lcl/charges/update',
        emptytext:0,
        success: function(response, newValue) {
            var sum = 0;
            var sum_total = 0;
            var sub_total = 0;
            var sub_total_markup = 0;
            var sum_total_markup = 0;
            var total_currency = 0;
            if($(this).attr("data-name")=='units'){
                value = (parseFloat(newValue) * parseFloat($(this).closest('tr').find('.price_per_unit').html())) + parseFloat($(this).closest('tr').find('.markup').html());
                $(this).closest('tr').find('.total-amount').html(value);
            }else if($(this).attr("data-name")=='price_per_unit'){
                value = (parseFloat(newValue) * parseFloat($(this).closest('tr').find('.units').html())) + parseFloat($(this).closest('tr').find('.markup').html());
                $(this).closest('tr').find('.total-amount').html(value);                
            }else if($(this).attr("data-name")=='markup'){
                value = (parseFloat($(this).closest('tr').find('.price_per_unit').html()) * parseFloat($(this).closest('tr').find('.units').html())) + parseFloat(newValue);
                $(this).closest('tr').find('.total-amount').html(value);                
            }

            $(this).editable('setValue', newValue);                       

            $(this).closest('table').find('.total-amount').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            $(this).closest('div.amount_charges').find('.sub_total').each(function(){
                if($(this).html()){
                    sub_total = parseFloat($(this).html());
                    sum_total += sub_total;
                }
            });

            //Mostrando total dinámico
            $(this).closest('div.amount_charges').find('.sum_total_amount').html(sum_total.toFixed(2));

            $(this).closest('div.amount_charges').find('.markup').each(function(){
                if($(this).html()){
                    sub_total_markup = parseFloat($(this).html());
                    sum_total_markup += sub_total_markup;
                }
            });

            $(this).closest('div.amount_charges').find('.sum_total_markup').html(sum_total_markup.toFixed(2));

            if(!response) {
                return "Unknown error!";
            }

            if(response.success === false) {
                return response.msg;
            }
        }
    });

    //Edición en línea para montos LCL/AIR en Inland
    $('.editable-lcl-air-inland').editable({
        url:'/v2/quotes/lcl/inland/charge/update',
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

    /** Edición en línea para montos y markups **/

    $('.editable-amount-20').editable({
        url:'/v2/quotes/charges/update',
        emptytext:0,
        success: function(response, newValue) {
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_c20 = 0;
            var sum_c20 = 0;
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var markup_m20=parseFloat($(this).closest('tr').find('.markup_20').html());

            if(markup_m20==''){
                markup_m20=0;
            }

            if(newValue==''){
                newValue=0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + markup_m20;
            $(this).closest('tr').find('.total_20').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_20').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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
            //amount_20_curr=currencyRate(currency, currency_cfg, data.amount20);
            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_20').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_20').html())+parseFloat($(this).closest('div.rates').find('.total_origin_20').html())+parseFloat($(this).closest('div.rates').find('.total_destination_20').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_20').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-amount-20').each(function(){
                console.log($(this).html());
                if(parseFloat($(this).html())){
                    amount_c20 = parseFloat($(this).html());
                }else{
                    amount_c20 = 0;
                }
                sum_c20 += amount_c20;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_amount_20').html(sum_c20);

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_m20 = 0;
            var sum_m20 = 0;            
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var amount_c20=parseFloat($(this).closest('tr').find('.amount_20').html());

            if(amount_c20==''){
                amount_c20=0;
            }

            if(newValue==''){
                newValue=0;
            }            
            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + amount_c20;
            $(this).closest('tr').find('.total_20').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_20').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_20').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_20').html())+parseFloat($(this).closest('div.rates').find('.total_origin_20').html())+parseFloat($(this).closest('div.rates').find('.total_destination_20').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_20').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-markup-20').each(function(){
                if(parseFloat($(this).html())){
                    amount_m20 = parseFloat($(this).html());
                }else{
                    amount_m20 = 0;
                }
                sum_m20 += amount_m20;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_markup_20').html(sum_m20);

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_c40 = 0;
            var sum_c40 = 0;
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var markup_m40=parseFloat($(this).closest('tr').find('.markup_40').html());

            if(markup_m40==''){
                markup_m40=0;
            }

            if(newValue==''){
                newValue=0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + markup_m40;
            $(this).closest('tr').find('.total_40').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_40').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_40').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_40').html())+parseFloat($(this).closest('div.rates').find('.total_origin_40').html())+parseFloat($(this).closest('div.rates').find('.total_destination_40').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_40').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-amount-40').each(function(){
                if(parseFloat($(this).html())){
                    amount_c40 = parseFloat($(this).html());
                }else{
                    amount_c40 = 0;
                }
                sum_c40 += amount_c40;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_amount_40').html(sum_c40);

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_m40 = 0;
            var sum_m40 = 0;             
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var amount_c40=parseFloat($(this).closest('tr').find('.amount_40').html());

            if(amount_c40==''){
                amount_c40=0;
            }

            if(newValue==''){
                newValue=0;
            }            
            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + amount_c40;            
            $(this).closest('tr').find('.total_40').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_40').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_40').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_40').html())+parseFloat($(this).closest('div.rates').find('.total_origin_40').html())+parseFloat($(this).closest('div.rates').find('.total_destination_40').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_40').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-markup-40').each(function(){
                if(parseFloat($(this).html())){
                    amount_m40 = parseFloat($(this).html());
                }else{
                    amount_m40 = 0;
                }
                sum_m40 += amount_m40;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_markup_40').html(sum_m40);            

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_c40hc = 0;
            var sum_c40hc = 0;
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var markup_m40hc=parseFloat($(this).closest('tr').find('.markup_40hc').html());

            if(markup_m40hc==''){
                markup_m40hc=0;
            }

            if(newValue==''){
                newValue=0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + markup_m40hc;
            $(this).closest('tr').find('.total_40hc').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_40hc').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_40hc').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_40hc').html())+parseFloat($(this).closest('div.rates').find('.total_origin_40hc').html())+parseFloat($(this).closest('div.rates').find('.total_destination_40hc').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_40hc').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-amount-40hc').each(function(){
                if(parseFloat($(this).html())){
                    amount_c40hc = parseFloat($(this).html());
                }else{
                    amount_c40hc = 0;
                }
                sum_c40hc += amount_c40hc;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_amount_40hc').html(sum_c40hc);            

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_m40hc = 0;
            var sum_m40hc = 0;             
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var amount_c40hc=parseFloat($(this).closest('tr').find('.amount_40hc').html());

            if(amount_c40hc==''){
                amount_c40hc=0;
            }

            if(newValue==''){
                newValue=0;
            }            
            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + amount_c40hc;
            $(this).closest('tr').find('.total_40hc').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_40hc').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_40hc').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_40hc').html())+parseFloat($(this).closest('div.rates').find('.total_origin_40hc').html())+parseFloat($(this).closest('div.rates').find('.total_destination_40hc').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_40hc').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-markup-40hc').each(function(){
                if(parseFloat($(this).html())){
                    amount_m40hc = parseFloat($(this).html());
                }else{
                    amount_m40hc = 0;
                }
                sum_m40hc += amount_m40hc;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_markup_40hc').html(sum_m40hc);               

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_c40nor = 0;
            var sum_c40nor = 0;
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var markup_m40nor=parseFloat($(this).closest('tr').find('.markup_40nor').html());

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            if(markup_m40nor==''){
                markup_m40nor=0;
            }

            if(newValue==''){
                newValue=0;
            }
            console.log(newValue);
            console.log(markup_m40nor);
            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + markup_m40nor;
            $(this).closest('tr').find('.total_40nor').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_40nor').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_40nor').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_40nor').html())+parseFloat($(this).closest('div.rates').find('.total_origin_40nor').html())+parseFloat($(this).closest('div.rates').find('.total_destination_40nor').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_40nor').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-amount-40nor').each(function(){
                if(parseFloat($(this).html())){
                    amount_c40nor = parseFloat($(this).html());
                }else{
                    amount_c40nor = 0;  
                }
                sum_c40nor += amount_c40nor;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_amount_40nor').html(sum_c40nor);            

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_m40nor = 0;
            var sum_m40nor = 0;             
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var amount_c40nor=parseFloat($(this).closest('tr').find('.amount_40nor').html());

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            if(amount_c40nor==''){
                amount_c40nor=0;
            }

            if(newValue==''){
                newValue=0;
            }
            console.log(newValue);
            console.log(amount_c40nor);
            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + amount_c40nor;

            $(this).closest('tr').find('.total_40nor').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_40nor').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_40nor').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_40nor').html())+parseFloat($(this).closest('div.rates').find('.total_origin_40nor').html())+parseFloat($(this).closest('div.rates').find('.total_destination_40nor').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_40nor').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-markup-40nor').each(function(){
                if(parseFloat($(this).html())){
                    amount_m40nor = parseFloat($(this).html());
                }else{
                    amount_m40nor=0;
                }

                sum_m40nor += amount_m40nor;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_markup_40nor').html(sum_m40nor);             

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_c45 = 0;
            var sum_c45 = 0;
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var markup_m45=parseFloat($(this).closest('tr').find('.markup_45').html());

            if(markup_m45==''){
                markup_m45=0;
            }

            if(newValue==''){
                newValue=0;
            }

            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + markup_m45;
            $(this).closest('tr').find('.total_45').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_45').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_45').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_45').html())+parseFloat($(this).closest('div.rates').find('.total_origin_45').html())+parseFloat($(this).closest('div.rates').find('.total_destination_45').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_45').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-amount-45').each(function(){
                if(parseFloat($(this).html())){
                    amount_c45 = parseFloat($(this).html());
                }else{
                    amount_c45 = 0;
                }
                sum_c45 += amount_c45;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_amount_45').html(sum_c45);              

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
            var type = $(this).attr('data-cargo-type');
            var sum = 0;
            var amount_m45 = 0;
            var sum_m45 = 0;             
            var sum_total = 0;
            var total = 0;
            var total_currency = 0;
            var amount_c45=parseFloat($(this).closest('tr').find('.amount_45').html());

            if(amount_c45==''){
                amount_c45=0;
            }

            if(newValue==''){
                newValue=0;
            }            
            //Seteando nuevo valor
            $(this).editable('setValue', newValue);

            //Calculando total de la línea dinámico
            total =  parseFloat(newValue) + amount_c45;
            $(this).closest('tr').find('.total_45').html(total);

            //Conversión de monedas dinámica
            $(this).closest('table').find('.total_45').each(function(){
                var value = parseFloat($(this).html());
                var currency=$(this).closest('tr').find('.local_currency').html();
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

            //Subtotal dinámico
            $(this).closest('table').find('.total_'+type+'_45').html(sum);

            //Calculando total dinámico
            sum_total = parseFloat($(this).closest('div.rates').find('.total_freight_45').html())+parseFloat($(this).closest('div.rates').find('.total_origin_45').html())+parseFloat($(this).closest('div.rates').find('.total_destination_45').html());

            //Mostrando total dinámico
            $(this).closest('div.rates').find('.sum_total_45').html(sum_total);

            //Calculando sub total de gastos
            $(this).closest('div.rates').find('.editable-markup-45').each(function(){
                if(parseFloat($(this).html())){
                    amount_m45 = parseFloat($(this).html());
                }else{
                    amount_m45 = 0;
                }
                sum_m45 += amount_m45;
            });

            //Mostrando sub total de gastos
            $(this).closest('div.rates').find('.sum_total_markup_45').html(sum_m45);             


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

//Guardar cargos LCL/AIR
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
    var number = $(this).closest("tr").find(".number").val();
    var theElement = $(this);
    var sum = 0;

    if(surcharge_id=='' || calculation_type_id=='' || units=='' || price_per_unit==''){
        notification('There are empty fields. Please verify and try again', 'error');
    }else{
        $(this).closest("table").find('.total-amount').each(function(){
            var sub_total = parseFloat($(this).html());
            var currency=$(this).closest('tr').find('.local_currency').html();
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

        //Subtotal dinámico
        $(this).closest('table').find('.td_sum_total').html(sum+parseFloat(total));

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
                    $(theElement).closest('tr').remove();
                    //Agregar nuevo tr en freight
                    if(data.type==3){
                        $('<tr style="height:40px;">'+
                          '<td class="tds" style="padding-left: 30px"><span class="td-a">'+data.surcharge+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a">'+data.calculation_type+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a units">'+data.units+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a price_per_unit">'+data.rate+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a markup">'+data.markup+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a total-amount">'+data.total+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a local_currency">'+data.currency+'</span></td>'+
                          '</tr>').insertBefore('.total_freight_'+number);
                    }else if(data.type==2){ //Agregar nuevo tr en destination
                        $('<tr style="height:40px;">'+
                          '<td class="tds" style="padding-left: 30px"><span class="td-a">'+data.surcharge+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a">'+data.calculation_type+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a units">'+data.units+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a price_per_unit">'+data.rate+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a markup">'+data.markup+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a total-amount">'+data.total+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a local_currency">'+data.currency+'</span></td>'+
                          '</tr>').insertBefore('.total_destination_'+number);
                    }else if(data.type==1){ //Agregar nuevo tr en origin
                        $('<tr style="height:40px;">'+
                          '<td class="tds" style="padding-left: 30px"><span class="td-a">'+data.surcharge+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a">'+data.calculation_type+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a units">'+data.units+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a price_per_unit">'+data.rate+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a markup">'+data.markup+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a total-amount">'+data.total+'</span></td>'+
                          '<td class="tds"><span class="editable-lcl-air td-a local_currency">'+data.currency+'</span></td>'+
                          '</tr>').insertBefore('.total_origin_'+number);
                    }

                }
                //setTimeout(location.reload.bind(location), 3000);
            }
        });
    }
});

$(document).on('click', '.store_sale_charge', function () {
    var id = $(this).closest("tr").find(".sale_term_id").val();
    var theElement = $(this);
    var charge = $(this).closest("tr").find(".charge").val();
    var detail = $(this).closest("tr").find(".detail").val();
    var c20 = $(this).closest("tr").find(".c20").val();
    var c40 = $(this).closest("tr").find(".c40").val();
    var c40hc = $(this).closest("tr").find(".c40hc").val();
    var c40nor = $(this).closest("tr").find(".c40nor").val();
    var c45 = $(this).closest("tr").find(".c45").val();
    var units = $(this).closest("tr").find(".units").val();
    var rate = $(this).closest("tr").find(".rate").val();
    var total = $(this).closest("tr").find(".total").val();
    var currency_id = $(this).closest("tr").find(".currency_id").val();
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/store/sale/charge',
        data:{
            "sale_term_id":id,
            "charge":charge,
            "detail":detail,
            "c20":c20,
            "c40":c40,
            "c40hc":c40hc,
            "c40nor":c40nor,
            "c45":c45,
            "units":units,
            "rate":rate,
            "total":total,
            "currency_id":currency_id,
        },
        success: function(data) {
            if(data.message=='Ok'){
                swal(
                    'Updated!',
                    'The payment conditions has been updated.',
                    'success'
                )
            }
        }
    });
});

//Guardar cargos FCL
$(document).on('click', '.store_charge', function () {
    var id = $(this).closest("tr").find(".automatic_rate_id").val();
    var number = $(this).closest("tr").find(".number").val();
    var theElement = $(this);
    var surcharge_id = $(this).closest("tr").find(".surcharge_id").val();
    var calculation_type_id = $(this).closest("tr").find(".calculation_type_id").val();
    var hide_20 = $(this).closest("tr").find(".hide_20").val();
    var amount_c20 = $(this).closest("tr").find(".amount_c20").val();
    var markup_m20 = $(this).closest("tr").find(".markup_m20").val();
    var hide_40 = $(this).closest("tr").find(".hide_40").val();
    var amount_c40 = $(this).closest("tr").find(".amount_c40").val();
    var markup_m40 = $(this).closest("tr").find(".markup_m40").val();
    var hide_40hc = $(this).closest("tr").find(".hide_40hc").val();
    var amount_c40hc = $(this).closest("tr").find(".amount_c40hc").val();
    var markup_m40hc = $(this).closest("tr").find(".markup_m40hc").val();
    var hide_40nor = $(this).closest("tr").find(".hide_40nor").val();
    var amount_c40nor = $(this).closest("tr").find(".amount_c40nor").val();
    var markup_m40nor = $(this).closest("tr").find(".markup_m40nor").val();
    var hide_45 = $(this).closest("tr").find(".hide_45").val();
    var amount_c45 = $(this).closest("tr").find(".amount_c45").val();
    var markup_m45 = $(this).closest("tr").find(".markup_m45").val();
    var type_id = $(this).closest("tr").find(".type_id").val();
    var currency_id = $(this).closest("tr").find(".currency_id").val();
    var sum_c20 = 0;
    var sum_c40 = 0;
    var self = $(this);
    var amount_20_curr = 0;

    $.ajax({
        type: 'POST',
        url: '/v2/quotes/store/charge',
        data:{
            "automatic_rate_id":id,
            "surcharge_id":surcharge_id,
            "calculation_type_id":calculation_type_id,
            "amount_c20":amount_c20,
            "markup_m20":markup_m20,
            "amount_c40":amount_c40,
            "markup_m40":markup_m40,
            "amount_c40hc":amount_c40hc,
            "markup_m40hc":markup_m40hc,
            "amount_c40nor":amount_c40nor,
            "markup_m40nor":markup_m40nor,
            "amount_c45":amount_c45,
            "markup_m45":markup_m45,
            "type_id":type_id,
            "currency_id":currency_id
        },
        success: function(data) {
            if(data.message=='Ok'){
                //alert(data.total_20);
                swal(
                    'Done!',
                    'Charge saved successfully',
                    'success'
                )
            }
            $(theElement).closest('tr').remove();
            line_total_20=parseFloat(data.amount20)+parseFloat(data.markup20);
            line_total_40=parseFloat(data.amount40)+parseFloat(data.markup40);
            line_total_40hc=parseFloat(data.amount40hc)+parseFloat(data.markup40hc);
            line_total_40nor=parseFloat(data.amount40nor)+parseFloat(data.markup40nor);
            line_total_45=parseFloat(data.amount45)+parseFloat(data.markup45);
            if(type_id==3){
                $('<tr style="height:40px;">'+
                  '<td class="tds" style="padding-left: 30px"><span class="td-a">'+data.surcharge+'</span></td>'+
                  '<td class="tds"><span class="td-a">'+data.calculation_type+'</span></td>'+
                  '<td '+hide_20+' class="tds"><span class="td-a">'+data.amount20+'</span> + <span class="td-a">'+data.markup20+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_20+'</td>'+
                  '<td '+hide_40+' class="tds"><span class="td-a">'+data.amount40+'</span> + <span class="td-a">'+data.markup40+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_40+'</td>'+
                  '<td '+hide_40hc+' class="tds"><span class="td-a">'+data.amount40hc+'</span> + <span class="td-a">'+data.markup40hc+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_40hc+'</td>'+
                  '<td '+hide_40nor+' class="tds"><span class="td-a">'+data.amount40nor+'</span> + <span class="td-a">'+data.markup40nor+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_40nor+'</td>'+
                  '<td '+hide_45+' class="tds"><span class="td-a">'+data.amount45+'</span> + <span class="td-a">'+data.markup45+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_45+'</td>'+
                  '<td class="tds"><span class="td-a">'+data.currency+'</span></td>'+
                  '</tr>').insertBefore('.total_freight_'+number);
                $('.total_freight_'+number).find('.total_freight_20').html('');
                $('.total_freight_'+number).find('.total_freight_20').html(data.sum_total_20);
                $('.total_freight_'+number).find('.total_freight_40').html('');
                $('.total_freight_'+number).find('.total_freight_40').html(data.sum_total_40);
                $('.total_freight_'+number).find('.total_freight_40hc').html('');
                $('.total_freight_'+number).find('.total_freight_40hc').html(data.sum_total_40hc);
                $('.total_freight_'+number).find('.total_freight_40nor').html('');
                $('.total_freight_'+number).find('.total_freight_40nor').html(data.sum_total_40nor);
                $('.total_freight_'+number).find('.total_freight_45').html('');
                $('.total_freight_'+number).find('.total_freight_45').html(data.sum_total_45);

                //Calculando total dinámico
                sum_total_20 = parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_freight_20').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_origin_20').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_destination_20').html());

                //Calculando total dinámico
                sum_total_40 = parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_freight_40').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_origin_40').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_destination_40').html());

                //Calculando total dinámico
                sum_total_40hc = parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_freight_40hc').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_origin_40hc').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_destination_40hc').html());

                //Calculando total dinámico
                sum_total_40nor = parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_freight_40nor').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_origin_40nor').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_destination_40nor').html());

                //Calculando total dinámico
                sum_total_45 = parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_freight_45').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_origin_45').html())+parseFloat($('.total_freight_'+number).closest('div.rates').find('.total_destination_45').html());
                //Mostrando total dinámico
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_20').html(sum_total_20);
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_40').html(sum_total_40);
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_40hc').html(sum_total_40hc);
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_40nor').html(sum_total_40nor);
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_45').html(sum_total_45);

                var currency=$(theElement).closest('tr').find('.local_currency').val();
                var currency_cfg = $("#currency_id").val();

                amount_20_curr=currencyRate(currency, currency_cfg, data.amount20);
                markup_20_curr=currencyRate(currency, currency_cfg, data.markup20);
                amount_40_curr=currencyRate(currency, currency_cfg, data.amount40);
                markup_40_curr=currencyRate(currency, currency_cfg, data.markup40);
                amount_40hc_curr=currencyRate(currency, currency_cfg, data.amount40hc);
                markup_40hc_curr=currencyRate(currency, currency_cfg, data.markup40hc);
                amount_40nor_curr=currencyRate(currency, currency_cfg, data.amount40nor);
                markup_40nor_curr=currencyRate(currency, currency_cfg, data.markup40nor);
                amount_45_curr=currencyRate(currency, currency_cfg, data.amount45);
                markup_45_curr=currencyRate(currency, currency_cfg, data.markup45);

                //Calculando subtotal de rates 20'
                subtotal_c20=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_c20_freight').val());         
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_c20_freight').val(subtotal_c20+parseFloat(amount_20_curr));

                //Calculando sum de subtotal de rates 20'
                sum_total_amount_20=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_20').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_20').html(parseFloat(amount_20_curr)+sum_total_amount_20);

                //Calculando subtotal de markups 20'
                subtotal_m20=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_m20_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_m20_freight').val(subtotal_m20+parseFloat(markup_20_curr));

                //Calculando sum de subtotal de markups 20'
                sum_total_markup_20=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_20').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_20').html(parseFloat(markup_20_curr)+sum_total_markup_20);

                //Calculando subtotal de rates 40'
                subtotal_c40=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_c40_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_c40_freight').val(subtotal_c40+parseFloat(amount_40_curr));

                //Calculando sum de subtotal de rates 40'
                sum_total_amount_40=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_40').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_40').html(parseFloat(amount_40_curr)+sum_total_amount_40);                

                //Calculando subtotal de markups 40'
                subtotal_m40=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_m40_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_m40_freight').val(subtotal_m40+parseFloat(markup_40_curr));

                //Calculando sum de subtotal de markups 40'
                sum_total_markup_40=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_40').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_40').html(parseFloat(markup_40_curr)+sum_total_markup_40);                

                //Calculando subtotal de rates 40hc'
                subtotal_c40hc=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_c40hc_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_c40hc_freight').val(subtotal_c40hc+parseFloat(amount_40hc_curr));

                //Calculando sum de subtotal de rates 40hc'
                sum_total_amount_40hc=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_40hc').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_40hc').html(parseFloat(amount_40hc_curr)+sum_total_amount_40hc);                

                //Calculando subtotal de markups 40hc'
                subtotal_m40hc=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_m40hc_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_m40hc_freight').val(subtotal_m40hc+parseFloat(markup_40hc_curr));

                //Calculando sum de subtotal de markups 40hc'
                sum_total_markup_40hc=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_40hc').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_40hc').html(parseFloat(markup_40hc_curr)+sum_total_markup_40hc);                

                //Calculando subtotal de rates 40nor'
                subtotal_c40nor=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_c40nor_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_c40nor_freight').val(subtotal_c40nor+parseFloat(amount_40nor_curr));

                //Calculando sum de subtotal de rates 40nor'
                sum_total_amount_40nor=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_40nor').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_40nor').html(parseFloat(amount_40nor_curr)+sum_total_amount_40nor);                

                //Calculando subtotal de markups 40nor'
                subtotal_m40nor=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_m40nor_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_m40nor_freight').val(subtotal_m40nor+parseFloat(markup_40nor_curr));

                //Calculando sum de subtotal de markups 40nor'
                sum_total_markup_40nor=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_40nor').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_40nor').html(parseFloat(markup_40nor_curr)+sum_total_markup_40nor);                  

                //Calculando subtotal de rates 45'
                subtotal_c45=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_c45_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_c45_freight').val(subtotal_c45+parseFloat(amount_45_curr));

                //Calculando sum de subtotal de rates 40nor'
                sum_total_amount_45=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_45').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_amount_45').html(parseFloat(amount_45_curr)+sum_total_amount_45);                 

                //Calculando subtotal de markups 45'
                subtotal_m45=parseFloat($('.total_freight_'+number).closest('div.rates').find('.subtotal_m45_freight').val());
                $('.total_freight_'+number).closest('div.rates').find('.subtotal_m45_freight').val(subtotal_m45+parseFloat(markup_45_curr));

                //Calculando sum de subtotal de markups 40nor'
                sum_total_markup_45=parseFloat($('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_45').html());
                $('.total_freight_'+number).closest('div.rates').find('.sum_total_markup_45').html(parseFloat(markup_45_curr)+sum_total_markup_45);

            }
            if(type_id==2){
                $('<tr style="height:40px;">'+
                  '<td class="tds" style="padding-left: 30px"><span class="td-a">'+data.surcharge+'</span></td>'+
                  '<td class="tds"><span class="td-a">'+data.calculation_type+'</span></td>'+
                  '<td '+hide_20+' class="tds"><span class="td-a">'+data.amount20+'</span> + <span class="td-a">'+data.markup20+'</span> <i class="la la-caret-right arrow-down"></i> '+line_total_20+'</td>'+
                  '<td '+hide_40+' class="tds"><span class="td-a">'+data.amount40+'</span> + <span class="td-a">'+data.markup40+'</span> <i class="la la-caret-right arrow-down"></i> '+line_total_40+'</td>'+
                  '<td '+hide_40hc+' class="tds"><span class="td-a">'+data.amount40hc+'</span> + <span class="td-a">'+data.markup40hc+'</span> <i class="la la-caret-right arrow-down"></i> '+line_total_40hc+'</td>'+
                  '<td '+hide_40nor+' class="tds"><span class="td-a">'+data.amount40nor+'</span> + <span class="td-a">'+data.markup40nor+'</span> <i class="la la-caret-right arrow-down"></i> '+line_total_40nor+'</td>'+
                  '<td '+hide_45+' class="tds"><span class="td-a">'+data.amount45+'</span> + <span class="td-a">'+data.markup45+'</span> <i class="la la-caret-right arrow-down"></i> '+line_total_45+'</td>'+
                  '<td class="tds"><span class="td-a">'+data.currency+'</span></td>'+
                  '</tr>').insertBefore('.total_destination_'+number);
                $('.total_destination_'+number).find('.total_destination_20').html('');
                $('.total_destination_'+number).find('.total_destination_20').html(data.sum_total_20);
                $('.total_destination_'+number).find('.total_destination_40').html('');
                $('.total_destination_'+number).find('.total_destination_40').html(data.sum_total_40);
                $('.total_destination_'+number).find('.total_destination_40hc').html('');
                $('.total_destination_'+number).find('.total_destination_40hc').html(data.sum_total_40hc);
                $('.total_destination_'+number).find('.total_destination_40nor').html('');
                $('.total_destination_'+number).find('.total_destination_40nor').html(data.sum_total_40nor);
                $('.total_destination_'+number).find('.total_destination_45').html('');
                $('.total_destination_'+number).find('.total_destination_45').html(data.sum_total_45);

                //Calculando total dinámico
                sum_total_20 = parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_freight_20').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_origin_20').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_destination_20').html());

                //Calculando total dinámico
                sum_total_40 = parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_freight_40').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_origin_40').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_destination_40').html());

                //Calculando total dinámico
                sum_total_40hc = parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_freight_40hc').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_origin_40hc').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_destination_40hc').html());

                //Calculando total dinámico
                sum_total_40nor = parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_freight_40nor').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_origin_40nor').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_destination_40nor').html());

                //Calculando total dinámico
                sum_total_45 = parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_freight_45').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_origin_45').html())+parseFloat($('.total_destination_'+number).closest('div.rates').find('.total_destination_45').html());

                //Mostrando total dinámico
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_20').html(sum_total_20);
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_40').html(sum_total_40);
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_40hc').html(sum_total_40hc);
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_40nor').html(sum_total_40nor);
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_45').html(sum_total_45);

                var currency=$(theElement).closest('tr').find('.local_currency').val();
                var currency_cfg = $("#currency_id").val();

                amount_20_curr=currencyRate(currency, currency_cfg, data.amount20);
                markup_20_curr=currencyRate(currency, currency_cfg, data.markup20);
                amount_40_curr=currencyRate(currency, currency_cfg, data.amount40);
                markup_40_curr=currencyRate(currency, currency_cfg, data.markup40);
                amount_40hc_curr=currencyRate(currency, currency_cfg, data.amount40hc);
                markup_40hc_curr=currencyRate(currency, currency_cfg, data.markup40hc);
                amount_40nor_curr=currencyRate(currency, currency_cfg, data.amount40nor);
                markup_40nor_curr=currencyRate(currency, currency_cfg, data.markup40nor);
                amount_45_curr=currencyRate(currency, currency_cfg, data.amount45);
                markup_45_curr=currencyRate(currency, currency_cfg, data.markup45);

                //Calculando subtotal de rates 20'
                subtotal_c20=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_c20_destination').val());         
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_c20_destination').val(subtotal_c20+parseFloat(amount_20_curr));

                //Calculando sum de subtotal de rates 20'
                sum_total_amount_20=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_20').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_20').html(parseFloat(amount_20_curr)+sum_total_amount_20);

                //Calculando subtotal de markups 20'
                subtotal_m20=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_m20_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_m20_destination').val(subtotal_m20+parseFloat(markup_20_curr));

                //Calculando sum de subtotal de markups 20'
                sum_total_markup_20=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_20').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_20').html(parseFloat(markup_20_curr)+sum_total_markup_20);

                //Calculando subtotal de rates 40'
                subtotal_c40=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_c40_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_c40_destination').val(subtotal_c40+parseFloat(amount_40_curr));

                //Calculando sum de subtotal de rates 40'
                sum_total_amount_40=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_40').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_40').html(parseFloat(amount_40_curr)+sum_total_amount_40);                

                //Calculando subtotal de markups 40'
                subtotal_m40=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_m40_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_m40_destination').val(subtotal_m40+parseFloat(markup_40_curr));

                //Calculando sum de subtotal de markups 40'
                sum_total_markup_40=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_40').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_40').html(parseFloat(markup_40_curr)+sum_total_markup_40);                

                //Calculando subtotal de rates 40hc'
                subtotal_c40hc=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_c40hc_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_c40hc_destination').val(subtotal_c40hc+parseFloat(amount_40hc_curr));

                //Calculando sum de subtotal de rates 40hc'
                sum_total_amount_40hc=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_40hc').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_40hc').html(parseFloat(amount_40hc_curr)+sum_total_amount_40hc);                

                //Calculando subtotal de markups 40hc'
                subtotal_m40hc=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_m40hc_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_m40hc_destination').val(subtotal_m40hc+parseFloat(markup_40hc_curr));

                //Calculando sum de subtotal de markups 40hc'
                sum_total_markup_40hc=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_40hc').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_40hc').html(parseFloat(markup_40hc_curr)+sum_total_markup_40hc);                

                //Calculando subtotal de rates 40nor'
                subtotal_c40nor=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_c40nor_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_c40nor_destination').val(subtotal_c40nor+parseFloat(amount_40nor_curr));

                //Calculando sum de subtotal de rates 40nor'
                sum_total_amount_40nor=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_40nor').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_40nor').html(parseFloat(amount_40nor_curr)+sum_total_amount_40nor);                

                //Calculando subtotal de markups 40nor'
                subtotal_m40nor=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_m40nor_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_m40nor_destination').val(subtotal_m40nor+parseFloat(markup_40nor_curr));

                //Calculando sum de subtotal de markups 40nor'
                sum_total_markup_40nor=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_40nor').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_40nor').html(parseFloat(markup_40nor_curr)+sum_total_markup_40nor);                  

                //Calculando subtotal de rates 45'
                subtotal_c45=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_c45_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_c45_destination').val(subtotal_c45+parseFloat(amount_45_curr));

                //Calculando sum de subtotal de rates 40nor'
                sum_total_amount_45=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_45').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_amount_45').html(parseFloat(amount_45_curr)+sum_total_amount_45);                 

                //Calculando subtotal de markups 45'
                subtotal_m45=parseFloat($('.total_destination_'+number).closest('div.rates').find('.subtotal_m45_destination').val());
                $('.total_destination_'+number).closest('div.rates').find('.subtotal_m45_destination').val(subtotal_m45+parseFloat(markup_45_curr));

                //Calculando sum de subtotal de markups 40nor'
                sum_total_markup_45=parseFloat($('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_45').html());
                $('.total_destination_'+number).closest('div.rates').find('.sum_total_markup_45').html(parseFloat(markup_45_curr)+sum_total_markup_45);                  

            }
            if(type_id==1){
                $('<tr style="height:40px;">'+
                  '<td class="tds" style="padding-left: 30px"><span class="td-a">'+data.surcharge+'</span></td>'+
                  '<td class="tds"><span class="td-a">'+data.calculation_type+'</span></td>'+
                  '<td '+hide_20+' class="tds"><span class="td-a">'+data.amount20+'</span> + <span class="td-a">'+data.markup20+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_20+'</td>'+
                  '<td '+hide_40+' class="tds"><span class="td-a">'+data.amount40+'</span> + <span class="td-a">'+data.markup40+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_40+'</td>'+
                  '<td '+hide_40hc+' class="tds"><span class="td-a">'+data.amount40hc+'</span> + <span class="td-a">'+data.markup40hc+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_40hc+'</td>'+
                  '<td '+hide_40nor+' class="tds"><span class="td-a">'+data.amount40nor+'</span> + <span class="td-a">'+data.markup40nor+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_40nor+'</td>'+
                  '<td '+hide_45+' class="tds"><span class="td-a">'+data.amount45+'</span> + <span class="td-a">'+data.markup45+'</span> <i class="la la-caret-right arrow-down"></i> '+data.total_45+'</td>'+
                  '<td class="tds"><span class="td-a">'+data.currency+'</span></td>'+
                  '</tr>').insertBefore('.total_origin_'+number);
                $('.total_origin_'+number).find('.total_origin_20').html('');
                $('.total_origin_'+number).find('.total_origin_20').html(data.sum_total_20);
                $('.total_origin_'+number).find('.total_origin_40').html('');
                $('.total_origin_'+number).find('.total_origin_40').html(data.sum_total_40);
                $('.total_origin_'+number).find('.total_origin_40hc').html('');
                $('.total_origin_'+number).find('.total_origin_40hc').html(data.sum_total_40hc);
                $('.total_origin_'+number).find('.total_origin_40nor').html('');
                $('.total_origin_'+number).find('.total_origin_40nor').html(data.sum_total_40nor);
                $('.total_origin_'+number).find('.total_origin_45').html('');
                $('.total_origin_'+number).find('.total_origin_45').html(data.sum_total_45);

                //Calculando total dinámico
                sum_total_20 = parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_freight_20').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_origin_20').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_destination_20').html());

                //Calculando total dinámico
                sum_total_40 = parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_freight_40').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_origin_40').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_destination_40').html());

                //Calculando total dinámico
                sum_total_40hc = parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_freight_40hc').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_origin_40hc').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_destination_40hc').html());

                //Calculando total dinámico
                sum_total_40nor = parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_freight_40nor').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_origin_40nor').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_destination_40nor').html());

                //Calculando total dinámico
                sum_total_45 = parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_freight_45').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_origin_45').html())+parseFloat($('.total_origin_'+number).closest('div.rates').find('.total_destination_45').html());

                //Mostrando total dinámico
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_20').html(sum_total_20);
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_40').html(sum_total_40);
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_40hc').html(sum_total_40hc);
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_40nor').html(sum_total_40nor);
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_45').html(sum_total_45);

                var currency=$(theElement).closest('tr').find('.local_currency').val();
                var currency_cfg = $("#currency_id").val();

                amount_20_curr=currencyRate(currency, currency_cfg, data.amount20);
                markup_20_curr=currencyRate(currency, currency_cfg, data.markup20);
                amount_40_curr=currencyRate(currency, currency_cfg, data.amount40);
                markup_40_curr=currencyRate(currency, currency_cfg, data.markup40);
                amount_40hc_curr=currencyRate(currency, currency_cfg, data.amount40hc);
                markup_40hc_curr=currencyRate(currency, currency_cfg, data.markup40hc);
                amount_40nor_curr=currencyRate(currency, currency_cfg, data.amount40nor);
                markup_40nor_curr=currencyRate(currency, currency_cfg, data.markup40nor);
                amount_45_curr=currencyRate(currency, currency_cfg, data.amount45);
                markup_45_curr=currencyRate(currency, currency_cfg, data.markup45);

                //Calculando subtotal de rates 20'
                subtotal_c20=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_c20_origin').val());         
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_c20_origin').val(subtotal_c20+parseFloat(amount_20_curr));

                //Calculando sum de subtotal de rates 20'
                sum_total_amount_20=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_20').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_20').html(parseFloat(amount_20_curr)+sum_total_amount_20);

                //Calculando subtotal de markups 20'
                subtotal_m20=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_m20_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_m20_origin').val(subtotal_m20+parseFloat(markup_20_curr));

                //Calculando sum de subtotal de markups 20'
                sum_total_markup_20=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_20').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_20').html(parseFloat(markup_20_curr)+sum_total_markup_20);

                //Calculando subtotal de rates 40'
                subtotal_c40=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_c40_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_c40_origin').val(subtotal_c40+parseFloat(amount_40_curr));

                //Calculando sum de subtotal de rates 40'
                sum_total_amount_40=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_40').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_40').html(parseFloat(amount_40_curr)+sum_total_amount_40);                

                //Calculando subtotal de markups 40'
                subtotal_m40=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_m40_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_m40_origin').val(subtotal_m40+parseFloat(markup_40_curr));

                //Calculando sum de subtotal de markups 40'
                sum_total_markup_40=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_40').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_40').html(parseFloat(markup_40_curr)+sum_total_markup_40);                

                //Calculando subtotal de rates 40hc'
                subtotal_c40hc=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_c40hc_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_c40hc_origin').val(subtotal_c40hc+parseFloat(amount_40hc_curr));

                //Calculando sum de subtotal de rates 40hc'
                sum_total_amount_40hc=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_40hc').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_40hc').html(parseFloat(amount_40hc_curr)+sum_total_amount_40hc);                

                //Calculando subtotal de markups 40hc'
                subtotal_m40hc=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_m40hc_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_m40hc_origin').val(subtotal_m40hc+parseFloat(markup_40hc_curr));

                //Calculando sum de subtotal de markups 40hc'
                sum_total_markup_40hc=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_40hc').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_40hc').html(parseFloat(markup_40hc_curr)+sum_total_markup_40hc);                

                //Calculando subtotal de rates 40nor'
                subtotal_c40nor=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_c40nor_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_c40nor_origin').val(subtotal_c40nor+parseFloat(amount_40nor_curr));

                //Calculando sum de subtotal de rates 40nor'
                sum_total_amount_40nor=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_40nor').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_40nor').html(parseFloat(amount_40nor_curr)+sum_total_amount_40nor);                

                //Calculando subtotal de markups 40nor'
                subtotal_m40nor=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_m40nor_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_m40nor_origin').val(subtotal_m40nor+parseFloat(markup_40nor_curr));

                //Calculando sum de subtotal de markups 40nor'
                sum_total_markup_40nor=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_40nor').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_40nor').html(parseFloat(markup_40nor_curr)+sum_total_markup_40nor);                  

                //Calculando subtotal de rates 45'
                subtotal_c45=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_c45_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_c45_origin').val(subtotal_c45+parseFloat(amount_45_curr));

                //Calculando sum de subtotal de rates 40nor'
                sum_total_amount_45=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_45').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_amount_45').html(parseFloat(amount_45_curr)+sum_total_amount_45);                 

                //Calculando subtotal de markups 45'
                subtotal_m45=parseFloat($('.total_origin_'+number).closest('div.rates').find('.subtotal_m45_origin').val());
                $('.total_origin_'+number).closest('div.rates').find('.subtotal_m45_origin').val(subtotal_m45+parseFloat(markup_45_curr));

                //Calculando sum de subtotal de markups 40nor'
                sum_total_markup_45=parseFloat($('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_45').html());
                $('.total_origin_'+number).closest('div.rates').find('.sum_total_markup_45').html(parseFloat(markup_45_curr)+sum_total_markup_45);
            }            
            //setTimeout(location.reload.bind(location), 3000);
        }
    });
});

//Borrar quote
$(document).on('click', '#delete-quote-v2', function () {
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
                url: '/v2/quotes/delete/' + id,
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

//Borrar rates
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

//Borrar sale terms
$(document).on('click', '.delete-sale-term', function () {
    var id=$(this).attr('data-saleterm-id');
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
                url: '/v2/quotes/delete/saleterm/'+id,
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

//Borrar cargo FCL
$(document).on('click', '.delete-charge', function () {
    var id=$(this).closest('tr').find('.charge_id').val();
    var type=$(this).closest('tr').find('.type').val();
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
                data: {
                    'type': type,
                },
                url: '/v2/quotes/delete/charge/'+id,
                success: function(data) {
                    if(data.message=='Ok'){
                        swal(
                            'Updated!',
                            'The charge has been deleted.',
                            'success'
                        )
                    }
                    if(data.type==1){
                        $(theElement).closest('tr').remove();
                    }else{
                        setTimeout(location.reload.bind(location), 3000); 
                    }
                }
            });
        }
    });
});

//Borrar cargos SaleTerms
$(document).on('click', '.delete-saleterm-charge', function () {
    var id=$(this).closest('tr').find('.saleterm_charge_id').val();
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
                url: '/v2/quotes/delete/saleterm/charge/'+id,
                success: function(data) {
                    if(data.message=='Ok'){
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
$(document).on('click', '.delete-charge-lcl', function () {
    var id=$(this).closest('tr').find('.charge_id').val();
    var type=$(this).closest('tr').find('.type').val();
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
                data: {
                    'type': type,
                },
                url: '/v2/quotes/lcl/delete/charge/'+id,
                success: function(data) {
                    if(data.message=='Ok'){
                        swal(
                            'Updated!',
                            'The charge has been deleted.',
                            'success'
                        )
                    }
                    if(data.type==1){
                        $(theElement).closest('tr').remove();
                    }else{
                        //setTimeout(location.reload.bind(location), 3000); 
                    }
                }
            });
        }
    });
});

//Borrar inland
$(document).on('click', '.delete-inland', function () {
    var id=$(this).closest('ul').find('.inland_id').val();
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
                url: '/v2/quotes/delete/inland/'+id,
                success: function(data) {
                    if(data.message=='Ok'){
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
$(document).on('click', '#edit-payments', function () {
    $(".payment_conditions_span").attr('hidden','true');
    $(".payment_conditions_textarea").removeAttr('hidden');
    $("#update_payments").removeAttr('hidden');
});

//Cancelar editar payments
$(document).on('click', '#cancel-payments', function () {
    $(".payment_conditions_span").removeAttr('hidden');
    $(".payment_conditions_textarea").attr('hidden','true');
    $("#update_payments").attr('hidden','true');
});

//Actualizar payments
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

//Editar terms
$(document).on('click', '#edit-terms', function () {
    $(".terms_and_conditions_span").attr('hidden','true');
    $(".terms_and_conditions_textarea").removeAttr('hidden');
    $("#update_terms").removeAttr('hidden');
});

//Cancelar editar terms
$(document).on('click', '#cancel-terms', function () {
    $(".terms_and_conditions_span").removeAttr('hidden');
    $(".terms_and_conditions_textarea").attr('hidden','true');
    $("#update_terms").attr('hidden','true');
});

//Actualizar terms
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

//Habilitar edicion campos de la cotizacion
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
    $(".quote-type ").removeAttr('hidden');
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
    if($(".kind_of_cargo").val()=='Pharma'){
        $(".gdp_span").attr('hidden','true');
        $(".gdp").removeAttr('hidden');
    }
    if($(".gdp").val()==1){
        $(".risk_level").removeAttr('hidden');
        $(".risk_level_span").attr('hidden','true');
    }
    if($(".delivery_type").val()==3 || $(".delivery_type").val()==4){
        $(".origin_address_span").attr('hidden','true');
        $(".origin_address").removeAttr('hidden');
    }
    if($(".delivery_type").val()==2 || $(".delivery_type").val()==4){
        $(".destination_address_span").attr('hidden','true');
        $(".destination_address").removeAttr('hidden');
    }
    $(".quote-type").select2();
    $(".status").select2();
    $(".kind_of_cargo").select2();
    $(".company_id").select2();
    $(".delivery_type").select2();
    $(".incoterm_id").select2();
    $(".contact_id").select2();
    $(".user_id").select2();
    $(".price_id").select2();
    $(".equipment").select2();
    $(".gdp").select2();
});

//Cancelar actualizacion de datos de cotizacion
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
    $(".quote-type ").attr('hidden','true');
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
    if($(".kind_of_cargo").val()=='Pharma'){
        $(".gdp").attr('hidden','true');
        $(".gdp_span").removeAttr('hidden');
    }
    if($(".gdp").val()==1){
        $(".risk_level").attr('hidden','true');
        $(".risk_level_span").removeAttr('hidden');
    }
    if($(".delivery_type").val()==3 || $(".delivery_type").val()==4){
        $(".origin_address").attr('hidden','true');
        $(".origin_address_span").removeAttr('hidden');
    }
    if($(".delivery_type").val()==2 || $(".delivery_type").val()==4){
        $(".destination_address").attr('hidden','true');
        $(".destination_address_span").removeAttr('hidden');
    }


    if ($('select').data('select2')) {
        $('select').select2('destroy');
    }
});

//Actualizar datos de cotización
$(document).on('click', '#update', function () {
    var id=$(".id").val();
    var quote_id=$(".quote_id").val();
    var company_id=$(".company_id").val();
    var type=$(".quote-type").val();
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
    var origin_address=$(".origin_address").val();
    var destination_address=$(".destination_address").val();
    var gdp=0;
    var risk_level='';
    if(kind_of_cargo=='Pharma'){
        gdp=$(".gdp").val();
        risk_level=$(".risk_level").val();
    }

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
            'gdp': gdp,
            'risk_level': risk_level,
            'origin_address': origin_address,
            'destination_address': destination_address,
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
                $(".quote-type").val(data.quote['type']);
                $(".type_span").html(data.quote['type']);
                if(data.quote['custom_quote_id']!=''){
                    $(".quote_id").val(data.quote['custom_quote_id']);
                    $(".quote_id_span").html(data.quote['custom_quote_id']);    
                }else{
                    $(".quote_id").val(data.quote['quote_id']);
                    $(".quote_id_span").html(data.quote['quote_id']);
                }
                $(".company_id").val(data.quote['company_id']);
                $(".company_span").html(data.company_name);
                $(".status").val(data.quote['status']);
                $(".status_span").html(data.quote['status']+' <i class="fa fa-check"></i>');
                $(".status_span").addClass('Status_'+data.quote['status']);
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
                $(".quote-type").attr('hidden','true');
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
                if($(".kind_of_cargo").val()=='Pharma'){
                    $(".gdp").attr('hidden','true');
                    $(".gdp_span").removeAttr('hidden');
                }
                if($(".gdp").val()==1){
                    $(".risk_level").attr('hidden','true');
                    $(".risk_level_span").removeAttr('hidden');
                }
                //if($(".origin_address").val()!=''){
                $(".origin_address").attr('hidden','true');
                $(".origin_address_span").removeAttr('hidden');
                //}
                //if($(".destination_address").val()!=''){
                $(".destination_address").attr('hidden','true');
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

/** Cargos dinámicos **/

//Remover campos en freight
$(document).on('click', '.removeFreightCharge', function (e) {
    $(this).closest('tr').remove();
});

//Remover campos en origin
$(document).on('click', '.removeOriginCharge', function (e) {
    $(this).closest('tr').remove();
});

//Remover campos en destination
$(document).on('click', '.removeDestinationCharge', function (e) {
    $(this).closest('tr').remove();
});

//Enviando cotizaciones FCL
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

//Enviando cotizaciones LCL/AIR
$(document).on('click', '#send-pdf-quotev2-lcl-air', function () {
    var id = $('#quote-id').val();
    var email = $('#quote_email').val();
    var to = $('#addresse').val();
    var email_template_id = $('#email_template').val();
    var email_subject = $('#email-subject').val();
    var email_body = $('#email-body').val();

    if(email_template_id!=''&&to!=''){
        $.ajax({
            type: 'POST',
            url: '/v2/quotes/send/lcl',
            data:{"email_template_id":email_template_id,"id":id,"subject":email_subject,"body":email_body,"to":to},
            beforeSend: function () {
                $('#send-pdf-quotev2-lcl-air').hide();
                $('#send-pdf-quote-sending').show();
            },
            success: function(data) {
                $('#spin').hide();
                $('#send-pdf-quotev2-lcl-air').show();
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

//Calculando el total de un cargo en Saleterm

$(document).on("change keyup keydown", ".units, .rate", function() {
    var sum = 0;
    var total_amount = 0;
    var sum_total = 0;
    var sum_total_2 = 0;
    var total_2 = 0;
    var markup = 0;
    var total=0;
    var self = this;
    var data = '';
    var currency_cfg = $("#currency_id").val();
    $(".rate").each(function(){
        $( this).each(function() {
            var quantity = $(this).closest('tr').find('.units').val();

            if(quantity > 0) {
                total_amount = quantity * $(this).val();
                $(this).closest('tr').find('.total').val(total_amount);
            }else{
                total_amount = 0;
                $(this).closest('tr').find('.total').val(total_amount);
            }
        });
    });
});

//Mostrar y ocultar puertos en Sale Terms
$(document).on('change', '#saleterm_type', function () {
    if($('#saleterm_type').val()=='origin'){
        $(".origin_port").removeClass('hide');

        $(".origin_airport").removeClass('hide');
        $(".destination_port").addClass('hide');
        $(".destination_airport").addClass('hide');
        $(".origin_port_select").prop('disabled', false);
        $(".origin_airport_select").prop('disabled', false);
        $(".destination_port_select").prop('disabled', true);
        $(".destination_airport_select").prop('disabled', true);
    }else{
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

//Actualizando opciones PDF
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

//GDP
$(document).on('change', '.gdp', function () {
    if($(this).val() == 1){
        $(".risk_level").removeAttr('hidden');
        $(".div_risk_level").removeAttr('hidden');
        $(".risk_level_span").attr('hidden','true');
    }else{
        $(".risk_level_span").attr('hidden','true');
        $(".div_risk_level").attr('hidden','true');
    }
});

//King of cargo
$(document).on('change', '.kind_of_cargo', function () {
    if($(this).val() == 'Pharma'){
        $(".gdp").removeAttr('hidden');
        $(".gdp_span").attr('hidden','true');
        $(".div_gdp").removeAttr('hidden');
        if($(".gdp").val()==1){
            $(".div_risk_level").removeAttr('hidden');  
        }
    }else{
        $(".div_gdp").attr('hidden','true');
        $(".div_risk_level").attr('hidden','true');
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
    var total=0;
    var self = this;
    var data = '';
    var currency_cfg = $("#currency_id").val();
    $(".price_per_unit").each(function(){
        $( this).each(function() {
            var quantity = $(this).closest('tr').find('.units').val();
            var currency_id = $(self).closest('tr').find('.currency_id').val();
            var number = $(self).closest('tr').find('.number').val();

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

                            if(currency_cfg+json.alphacode == json.api_code){
                                total = sub_total / json.rates;
                            }else{
                                total = sub_total / json.rates_eur;
                            }
                            total = total.toFixed(2);

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

$(document).on("change", ".total_22", function() {
    var sum = 0;
    var value = 0;
    $(this).each(function(){
        value = Number($(this).closest('table').find('.total-amount').html());
        sum += value;
    });
    sum_total= Number($(this).closest('div').find('.sum_total').val())+Number(sum);
    $(this).closest('div').find('.td_sum_total').html(sum_total);

});

$( document ).ready(function() {
    if($( "select[name='company_id']" ).val()==''){
        $('select[name="contact_id"]').empty(); 
    }

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

/** Search **/

$(document).on('change', '#quoteType', function (e) {


    if($(this).val()==1){

        $("#total_quantity").removeAttr( "required");
        $("#total_weight").removeAttr( "required");
        $("#total_volume").removeAttr( "required");
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

        // Validaciones por defecto 
        $("#total_quantity").prop( "required", true );
        $("#total_weight").prop( "required", true );
        $("#total_volume").prop( "required", true );


        $(".infocheck").val('');
        //$(".quote_search").hide();
        $(".formu").val('');



        $(".quote_search").show();

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

        // Validaciones
        $("#total_quantity").prop( "required", true );
        $("#total_weight").prop( "required", true );
        $("#total_volume").prop( "required", true );

        $(".infocheck").val('');
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
        if($("#m_select2_2_modal").val() != '0')
            $("#contact_id").prop('required',true);  
        else
            $("#contact_id").removeAttr('required');

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

$(".quote_search").on("click", function() {

    //FCL
    if($('#quoteType').val()==1){
        $('#FormQuote').attr('action', '/v2/quotes/processSearch');
    }

    // LCL
    if($('#quoteType').val()==2){
        $('#FormQuote').attr('action', '/v2/quotes/processSearchLCL');
    }
    $(".quote_search").attr("type","submit");

});

$(".quote_man").on("click", function() {



    $('#FormQuote').attr('action', '/v2/quotes/store');

    if($('#quoteType').val()==2){

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



    $(".quote_man").attr("type","submit");
});

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

//Calculos por cantidad
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

//Calculos por volumen
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

//Calculos por peso
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

//Calcular peso tasable
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

//Cambiar tipo de envio
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

//Agregar inputs dinámicos en LCL/AIR
$(document).on('click', '#add_load_lcl_air', function (e) {
    var $template = $('#lcl_air_load_template');
    $clone = $template.clone().removeClass('hide').removeAttr('id');

    $clone.find('.type_cargo').prop('required',true);
    $clone.find('.quantity').prop('required',true);
    $clone.find('.height').prop('required',true);
    $clone.find('.width').prop('required',true);
    $clone.find('.large').prop('required',true);
    $clone.find('.weight').prop('required',true);

    $clone.insertBefore($template);



});

//Guardar compañía
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

//Guardar contacto
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

//Remover inputs LCL/AIR
$(document).on('click', '.remove_lcl_air_load', function (e) {
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

$('#destination_airport_create').select2({
    dropdownParent: $('#createRateModal'),
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

//Combo select2 para Aereopuertos destinos
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

//Datetimepicker
$('.date_issued').datetimepicker();

/** Funciones **/

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

function change_tab(tab){
    if(tab==2){
        //Quitar validaciones del primer TAB 
        $("#total_quantity").removeAttr( "required");
        $("#total_weight").removeAttr( "required");
        $("#total_volume").removeAttr( "required");


        $(".type_cargo_2").prop( "required",true);

        $(".quantity_2").prop( "required",true);
        $(".height_2").prop( "required",true);
        $(".width_2").prop( "required",true);
        $(".large_2").prop( "required",true);
        $(".weight_2").prop( "required",true);


        $("#total_quantity").val('');
        $("#total_weight").val('');
        $("#total_volume").val('');
        $("#chargeable_weight_pkg_input").val('');
        $("#chargeable_weight_total").html('');

    }else{
        //colocar validaciones al cambiar tab 
        $("#total_quantity").prop( "required",true)
        $("#total_weight").prop( "required",true)
        $("#total_volume").prop( "required", true );

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

function precargarLCL(){


    // Validaciones por defecto

    if($("#total_quantity").val() != ""){
        $("#total_quantity").prop( "required", true );
        $("#total_weight").prop( "required", true );
        $("#total_volume").prop( "required", true );
    }



    $(".infocheck").val('');
    $(".quote_search").show();



    $("#origin_harbor").prop( "disabled", false );
    $("#destination_harbor").prop( "disabled", false );
    $("#equipment_id").hide();
    $("#equipment").prop( "disabled", true );
    $("#equipment").removeAttr('required');
    $("#delivery_type").prop( "disabled", false );
    $("#delivery_type_air").prop( "disabled", true );
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

function addSaleCharge($value){

    var $template = $('#sale_charges_'+$value),
        $clone = $template
    .clone()
    .removeClass('hide')
    .removeAttr('id')
    .insertAfter($template)
    $clone.find("select").select2({
        placeholder: "Currency"
    });
}

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

function addInlandCharge($value){
    var $template = $('#inland_charges_'+$value),
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
function edit_remark($span,$textarea,$update_box){
    $('.'+$span).attr('hidden','true');
    $('.'+$textarea).removeAttr('hidden');
    $('.'+$update_box).removeAttr('hidden');
}

//Cancelar editar remarks
function cancel_update($span,$textarea,$update_box){
    $('.'+$span).removeAttr('hidden');
    $('.'+$textarea).attr('hidden','true');
    $('.'+$update_box).attr('hidden','true');
}

//Actualizar remarks
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

                $(".remarks_box_"+$v).html(data.rate['remarks']);
                $(".remarks_span_"+$v).removeAttr('hidden');
                $(".remarks_textarea_"+$v).attr('hidden','true');
                $(".update_remarks_"+$v).attr('hidden','true');
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
    if(type=='all'){
        type='total in';
    }
    changeType(type, id);
}

function changeType(type, id){
    $.ajax({
        type: 'POST',
        url: '/v2/quotes/feature/pdf/update',
        data:{"value":type,"name":"show_type","id":id},
        success: function(data) {
            if(data.message=='Ok'){
                //$(this).attr('checked', true).val(0);
            }
        }
    });
}

function currencyRate(currency, currency_cfg, amount){
    $.ajax({
        url: '/api/currency/'+currency,
        dataType: 'json',
        async: false,
        success: function (json) {
            if(currency_cfg+json.alphacode == json.api_code){
                amount = parseFloat(amount) / json.rates;
            }else{
                amount = parseFloat(amount) / json.rates_eur;
            }
            amount = amount.toFixed(2);
        }
    });

    return amount; 
}

function currencyRateAlphacode(currency, currency_cfg, value){
    $.ajax({
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
    });

    return parseFloat(total_currency);
}

function notification(message, type){

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

    switch(type) {
        case "error":
            toastr.error(message,'ERROR');
            break;
        case "success":
            toastr.success(message,'SUCCESS');
            break;
        default:
            toastr.info(message,'IMPORTANT MESSAGE');
    }
}