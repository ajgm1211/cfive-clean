
    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });

    $('#originchk').on('click',function(){
        if($('#originchk').prop('checked')){
            $('#origininp').removeAttr('hidden');
            $('#origin').attr('required','required');
            $('#destinychk').attr('disabled','true');
        } else{
            $('#origininp').attr('hidden','hidden');
            $('#origin').removeAttr('required');
            $('#destinychk').removeAttr('disabled');
        }
    }); 

    $('#destinychk').on('click',function(){
        if($('#destinychk').prop('checked')){
            $('#destinyinp').removeAttr('hidden');
            $('#destiny').attr('required','required');
            $('#originchk').attr('disabled','true');
        } else{
            $('#destinyinp').attr('hidden','hidden');
            $('#destiny').removeAttr('required');
            $('#originchk').removeAttr('disabled');
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
