$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).on('click', '#delete-token', function () {
    var id = $(this).attr('data-token-id');
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
                url: '/oauth/delete/token/' + id,
                success: function(data) {
                    if(data.message=='Ok'){
                        swal(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                        $(theElement).closest('tr').remove();
                    }else{
                        swal(
                            'Error!',
                            'Has been ocurred an error',
                            'warning'
                        )
                        console.log(data.message);
                    }
                }
            });

        }

    });
});