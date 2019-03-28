//binds to onchange event of your input field
$(document).on('change', '#logo', function (e) {
    if(this.files[0].size>1000000){
        $("#logo-error").removeClass('hide');
        $("#default-currency-submit").prop("disabled", true);
    }else{
        $("#logo-error").addClass('hide');
        $("#default-currency-submit").prop("disabled", false);
    }
});