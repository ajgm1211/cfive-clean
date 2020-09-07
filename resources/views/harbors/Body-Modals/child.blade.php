
<form  id="form" enctype="multipart/form-data">
	@csrf
    <div class="form-group row">
		<div class="col-lg-8">
			<label for="NameMD" class="form-control-label">
				<h5>Parent:</h5> <br>
			</label>
			{{ $parent->name }}

            {{ Form::hidden('harbor_parent', $parent->id , ['id' => 'harbor_parent'  ])  }}
		</div>
		
	</div>
	<div class="form-group row">
		<div class="col-lg-8">
			<label for="NameMD" class="form-control-label">
				Children:
			</label>
			{!! Form::select('harbor_child',$harbor,$select,['id' => 'harbor_child', 'class' => 'm-select2-general form-control', 'multiple'=>'true'])!!}
		</div>
		
	</div>
	
	<hr>
	
	<div class="form-group pull-right" >
		<button type="submit" class="btn btn-primary">
			Save
		</button>
		<button type="button" class="btn btn-secondary" data-dismiss="modal">
			Cancel
		</button>
	</div>
</form>

<script src="/js/jquery.form.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script type="application/x-javascript" src="/js/toarts-config.js"></script>
<script>
	$('.m-select2-general').select2({

	});
    
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$("#form").on('submit', function(e){
		e.preventDefault();
		
        var harbor_parent		= $('#harbor_parent').val();
        var harbor_child		= $('#harbor_child').val();
		var data	= { parent:harbor_parent,child:harbor_child};
		//console.log(data);
		$.ajax({
			url: '{{route("store.hierarchy")}}',
			method: 'POST',
			data:data,
			dataType:'JSON',
            error: function(request, status, error) {
            alert(request.responseText);
             },
			success: function(resp){
				console.log(resp);
				if(resp.success == true){
					
					$('#addHarborModal').modal('hide');
					toastr.success("Aggregate port", "Success");
				}else if(resp.success == false){

				}
			}
		});


	});
</script>