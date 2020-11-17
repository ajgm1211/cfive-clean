$(document).on('click', '.deleter', function() {
    $(this).closest('div.clone').find('.row').remove();
});

//Functions

function addExtraField() {
    var $template = $('#hide_extra_field'),
        $clone = $template
        .clone()
        .removeClass('hide')
        .removeAttr('id')
        .addClass('clone')
        .insertAfter($template);
}