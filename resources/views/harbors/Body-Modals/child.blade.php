
<form  id="form" enctype="multipart/form-data">
	@csrf
    <div class="form-group row">
		<div class="col-lg-8">
			<label for="NameMD" class="form-control-label">
				<h5>Parent:</h5> <br>
			</label>
			{{ $parent->name }}
		</div>
		
	</div>
	<div class="form-group row">
		<div class="col-lg-8">
			<label for="NameMD" class="form-control-label">
				Children:
			</label>
			{!! Form::select('harbor',$harbor,$select,['id' => 'countryMD', 'class' => 'm-select2-general form-control', 'multiple'=>'true'])!!}
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
    
       function agregarcampo(){
		var newtr = '<div class="col-lg-4 ">';
		newtr = newtr + '<label class="form-control-label">Variation:</label>';
		newtr = newtr + '<input type="text" name="variation[]" class="form-control" required="required">';
		newtr = newtr + '<a href="#" class="borrado"><span class="la la-remove"></span></a>';
		newtr = newtr + '</div>';
		$('#variatiogroup').append(newtr);
	}

	$(document).on('click','.borrado', function(e){
		var elemento = $(this);
		$(elemento).closest('div').remove();
	});

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$tableHar 	= $('#myatest').DataTable();

	$("#form").on('submit', function(e){
		e.preventDefault();
		var variation = [];
		var name		= $('#NameMD').val();
		var code		= $('#CodeMD').val();
		var display_name	= $('#DispNamMD').val();
		var coordinate	= $('#coordinateMD').val();
		var country		= $('#countryMD').val();
		variation = $("input[name='variation[]']").map(function(){return $(this).val();}).get();

		var data	= { name:name,code,display_name,coordinate,country,variation};
		//console.log(data);
		$.ajax({
			url: '{{route("UploadFile.store")}}',
			method: 'POST',
			data:data,
			dataType:'JSON',
			error:function(){
				alert('error');
			},
			success: function(resp){
				console.log(resp);
				if(resp.success == true){
					$tableHar.ajax.reload();
					$('#addHarborModal').modal('hide');
					toastr.success("Aggregate port", "Success");
				}else if(resp.success == false){

				}
			}
		});


	});
</script>