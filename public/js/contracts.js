$(document).on('click', '#delete-contract', function () {
    var id = $(this).attr('data-contract-id');

    var theElement = $(this);
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Continue!'
    }).then(function(result) {

        if (result.value) {
            $.ajax({
                type: 'get',
                url: 'contracts/deleteContract/' + id,
                success: function(data) {

                    if(data.jobAssociate == false){
                        if(data.message!= "SN"){

                            swal({
                                title: 'Warning!',
                                text: "There are "+data.message+" rates associated with this contract and "+data.local+" charges. If you delete it, those rates  and charges will be deleted.",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it!'
                            }).then(function(result) {
                                if (result.value) {
                                    $.ajax({
                                        type: 'get',
                                        url: 'contracts/destroyContract/' + id,
                                        success: function(data) {
                                            if(data.message=='Ok'){
                                                swal(
                                                    'Deleted!',
                                                    'Your contract has been deleted.',
                                                    'success'
                                                )
                                                $(theElement).closest('tr').remove();
                                            }
                                        }
                                    });
                                }
                            });

                        }else{

                            $.ajax({
                                type: 'get',
                                url: 'contracts/destroyContract/' + id,
                                success: function(data) {
                                    if(data.message=='Ok'){
                                        swal(
                                            'Deleted!',
                                            'Your contract has been deleted.',
                                            'success'
                                        )
                                        $(theElement).closest('tr').remove();
                                    }
                                }
                            });


                        }
                    }else{
                        swal(
                            'Error!',
                            'Your contract cannot be deleted. It is being managed',
                            'warning'
                        );
                    }
                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });

        }

    });
});


$(document).on('click', '#delete-rate', function () {
    var id = $(this).attr('data-rate-id');

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
                url: 'contracts/delete-rates/' + id,
                success: function(data) {
                    swal(
                        'Deleted!',
                        'Your rate has been deleted.',
                        'success'
                    )
                    $(theElement).closest('tr').remove();

                },
                error: function (request, status, error) {
                    alert(request.responseText);
                }
            });

        }

    });
});