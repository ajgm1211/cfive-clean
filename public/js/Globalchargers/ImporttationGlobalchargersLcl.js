
$('.m-select2-general').select2({
    placeholder: "Select an option"
});

$('#originchk').on('click',function(){
    if($('#originchk').prop('checked')){
        $('#origininp').removeAttr('hidden');
        $('#origin').attr('required','required');
        $('#destinychk').attr('disabled','true');
        if($('#portcountrychk').prop('checked')){
            $('#origininpCount').removeAttr('hidden');
            $('#originCountry').attr('required','required');
            $('#origininpRegion').removeAttr('hidden');
            $('#originRegion').attr('required','required');
        }
    } else{
        $('#origininp').attr('hidden','hidden');
        $('#origin').removeAttr('required');
        $('#destinychk').removeAttr('disabled');
        $('#origininpCount').attr('hidden','hidden');
        $('#originCountry').removeAttr('required');
        $('#origininpRegion').attr('hidden','hidden');
        $('#originRegion').removeAttr('required');
    }
});

$('#destinychk').on('click',function(){
    if($('#destinychk').prop('checked')){
        $('#destinyinp').removeAttr('hidden');
        $('#destiny').attr('required','required');
        $('#originchk').attr('disabled','true');
        if($('#portcountrychk').prop('checked')){
            $('#destinyinpCount').removeAttr('hidden');
            $('#destinyCountry').attr('required','required');
            $('#destinyinpRegion').removeAttr('hidden');
            $('#destinyRegion').attr('required','required');
        }
    } else{
        $('#destinyinp').attr('hidden','hidden');
        $('#destiny').removeAttr('required');
        $('#originchk').removeAttr('disabled');

        $('#destinyinpCount').attr('hidden','hidden');
        $('#destinyCountry').removeAttr('required');
        $('#destinyinpRegion').attr('hidden','hidden');
        $('#destinyRegion').removeAttr('required');
    }    
});

$('#carrierchk').on('click',function(){
    if($('#carrierchk').prop('checked')){
        $('#carrierinp').removeAttr('hidden');
        $('#carrier').attr('required','required');
    } else{
        $('#carrierinp').attr('hidden','hidden');
        $('#carrier').removeAttr('required');
    }
});


$('#typedestinychk').on('click',function(){
    if($('#typedestinychk').prop('checked')){
        $('#typedestinyinp').removeAttr('hidden');
        $('#typedestiny').attr('required','required');
    } else{
        $('#typedestinyinp').attr('hidden','hidden');
        $('#typedestiny').removeAttr('required');
    }
});

$('#datevaiditychk').on('click',function(){
    if($('#datevaiditychk').prop('checked')){
        $('#datevaiditydiv').removeAttr('hidden');
        $('.datevalidityinp').attr('required','required');
    } else{
        $('#datevaiditydiv').attr('hidden','hidden');
        $('.datevalidityinp').removeAttr('required');
    }
});


$('#portchk').on('click',function(){
    if($('#portchk').prop('checked')){
        if($('#originchk').prop('checked')){
            $('#origininpCount').attr('hidden','hidden');
            $('#originCountry').removeAttr('required');
            $('#origininpRegion').attr('hidden','hidden');
            $('#originRegion').removeAttr('required');
        }
        if($('#destinychk').prop('checked')){
            $('#destinyinpCount').attr('hidden','hidden');
            $('#destinyCountry').removeAttr('required');
            $('#destinyinpRegion').attr('hidden','hidden');
            $('#destinyRegion').removeAttr('required');
        }
    }
});

$('#portcountrychk').on('click',function(){
    if($('#portcountrychk').prop('checked')){
        if($('#originchk').prop('checked')){
            $('#origininpCount').removeAttr('hidden');
            $('#originCountry').attr('required','required');
            $('#origininpRegion').removeAttr('hidden');
            $('#originRegion').attr('required','required');
        }
        if($('#destinychk').prop('checked')){
            $('#destinyinpCount').removeAttr('hidden');
            $('#destinyCountry').attr('required','required');
            $('#destinyinpRegion').removeAttr('hidden');
            $('#destinyRegion').attr('required','required');
        }
    }
});


/*
$('#portchk').on('click',function(){
    if($('#portchk').prop('checked')){
        $('#destinychk').removeAttr('disabled');
        $('#divdestiny').removeAttr('disabled');
        $('#destinychk').removeAttr('hidden');
        $('#divdestiny').removeAttr('hidden');
        $('#originchk').removeAttr('disabled');
        $('#divorigin').removeAttr('disabled');
        $('#originchk').removeAttr('hidden');
        $('#divorigin').removeAttr('hidden');
    }
});

$('#portcountrychk').on('click',function(){
    if($('#portcountrychk').prop('checked')){
        $('#destinychk').attr('disabled','true');
        $('#divdestiny').attr('disabled','true');
        $('#destinychk').attr('hidden','hidden');
        $('#divdestiny').attr('hidden','hidden');

        $('#originchk').attr('disabled','true');
        $('#divorigin').attr('disabled','true');
        $('#originchk').attr('hidden','hidden');
        $('#divorigin').attr('hidden','hidden');
    }
});
*/
jQuery(document).ready(function($){
    Dropzone.options.mss = {
        paramName: "file", // The name that will be used to transfer the file
        maxFiles: 1,
        maxFilesize: 5, // MB
        addRemoveLinks: true,
        accept: function(file, done) {
            if (file.name == "justinbieber.jpg") {
                done("Naha, you don't.");
            } else { 
                done(); 
            }
        }   
    };
});
