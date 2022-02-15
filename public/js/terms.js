$(document).on('click', '#delete-terms', function () {
    var id = $(this).attr('data-terms-id');
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
                url: '/terms/delete/' + id,
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
                            'Your can\'t delete this contact because have quotes related.',
                            'warning'
                        )
                        console.log(data.message);
                    }
                }
            });

        }

    });
});