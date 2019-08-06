//Mostrar/Ocultar opciones api
$(document).on('change', '#enable-api', function () {
    var value = 0;
    if($(this). prop("checked") == true){
        $('#api-table').removeClass('hide');
    }else{
        $('#api-table').addClass('hide');
    }
});