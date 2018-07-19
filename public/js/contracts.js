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
          if(data.message>0){
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
                        'Your file has been deleted.',
                        'success'
                      )
                      $(theElement).closest('tr').remove();
                    }else{
                      swal(
                        'Error!',
                        'This . '+data.message+'You can\'t deleted companies with quotes associated.',
                        'error'
                      )
                      console.log(data.message);
                    }
                  }
                });
              }
            });
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
            'Your file has been deleted.',
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