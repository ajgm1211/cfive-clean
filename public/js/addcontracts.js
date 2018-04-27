$("#new").on("click", function() {

    var $template = $('#tclone');
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
    $myClone.find("select").select2();
    $("#sample_editable_1").append($myClone);
    // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
    // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});
$("#new2").on("click", function() {

    var $template = $('#tclone2');
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id');
    $myClone.find("select").select2();
    $("#sample_editable_2").append($myClone);
    // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
    // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$(document).on('click', '.remove', function () {
    $(this).closest('tr').remove();
});



$('.m-select2-general').select2({
    placeholder: "Select an option"
});



