
$(document).ready(function(){
        checkedCarrier();
    });

$('.m-select2-general').select2({
    placeholder: "Select an option"
});


$('#carrierchk').on('click',function(){
    checkedCarrier();
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

$('#typedestinychk').on('click',function(){
    if($('#typedestinychk').prop('checked')){
        $('#typedestinyinp').removeAttr('hidden');
        $('#typedestiny').attr('required','required');
    } else{
        $('#typedestinyinp').attr('hidden','hidden');
        $('#typedestiny').removeAttr('required');
    }
});


