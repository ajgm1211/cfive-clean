
$(document).ready(function(){
    checkedCarrier();
    checkedCurrency();
    checkedTypeDestiny();
});

$('.m-select2-general').select2({
    placeholder: "Select an option"
});


$('#carrierchk').on('click',function(){
    checkedCarrier();
});

$('.currencychk').on('click',function(){
    checkedCurrency();
});

function checkedCarrier(){
    if($('#carrierchk').prop('checked')){
        $('#carrierinp').removeAttr('hidden');
        $('#carrier').attr('required','required');
    } else{
        $('#carrierinp').attr('hidden','hidden');
        $('#carrier').removeAttr('required');
    }
}

function checkedCurrency(){
    if($('.currencychk').prop('checked')){
        $('#currencyinp').removeAttr('hidden');
        $('#currency').attr('required','required');
    } else{
        $('#currencyinp').attr('hidden','hidden');
        $('#currency').removeAttr('required');
    }
}
function checkedTypeDestiny(){
    if($('#typedestinychk').prop('checked')){
        $('#typedestinyinp').removeAttr('hidden');
        $('#typedestiny').attr('required','required');
    } else{
        $('#typedestinyinp').attr('hidden','hidden');
        $('#typedestiny').removeAttr('required');
    }
}


$('#typedestinychk').on('click',function(){
    if($('#typedestinychk').prop('checked')){
        $('#typedestinyinp').removeAttr('hidden');
        $('#typedestiny').attr('required','required');
    } else{
        $('#typedestinyinp').attr('hidden','hidden');
        $('#typedestiny').removeAttr('required');
    }
});


