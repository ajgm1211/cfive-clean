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
    $myClone = $template.clone().removeAttr('hidden').removeAttr('id').addClass('trclone2');
    $myClone.find("select").select2();
    $ids = $( ".port" ).length;
    $ids = $ids + 1;
    alert($ids);
    $myClone.find(".port").attr('name', 'port_id'+$ids+'[]');
    $myClone.find(".carrier").attr('name', 'localcarrier_id'+$ids+'[]');

    $("#sample_editable_2").append($myClone);
    // $("#tclone").clone().removeAttr('hidden').removeAttr('class').appendTo("#sample_editable_1");
    // $clone = $template.clone().removeAttr('hidden').removeAttr('id').insertBefore($template);

});

$(document).on('click', '.remove', function () {
    $(this).closest('tr').remove();
});

$(document).on('click', '.removeL', function () {
    $(this).closest('tr').remove();
    $i = 2;
    $('.trclone2').each(function () {

        var res = $(".port",this).removeAttr('name').attr('name', 'port_id'+$i+'[]');
        var car = $(".carrier",this).removeAttr('name').attr('name', 'localcarrier_id'+$i+'[]');
        $i++;
    });
});






$('.m-select2-general').select2({
    placeholder: "Select an option"
});



