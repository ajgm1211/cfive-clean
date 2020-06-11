$(document).on('click', '#delete-remarks', function() {
    var id = $(this).attr('data-remarks-id');
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
                url: '/remarks/delete/' + id,
                success: function(data) {
                    if (data.message == 'Ok') {
                        swal(
                            'Deleted!',
                            'Record deleted successfully!',
                            'success'
                        )
                        $(theElement).closest('tr').remove();
                    }
                }
            });

        }

    });
});