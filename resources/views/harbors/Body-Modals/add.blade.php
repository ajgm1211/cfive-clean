
<form  id="form" enctype="multipart/form-data">
	@csrf
	<div class="form-group row">
		<div class="col-lg-4">
			<label for="NameMD" class="form-control-label">
				Name:
			</label>
			<input type="text" name="name" required="required" class="form-control" id="NameMD">
		</div>
		<div class="col-lg-4">
			<label for="CodeMD" class="form-control-label">
				Code:
			</label>
			<input type="text" name="code" required="required" class="form-control" id="CodeMD">
		</div>
		<div class="col-lg-4">
			<label for="DispNamMD" class="form-control-label">
				Display Name:
			</label>
			<input type="text" name="display_name" required="required" class="form-control" id="DispNamMD">
		</div>
	</div>
	<div class="form-group row">
		<div class="col-lg-4">
			<label for="DispNamMD" class="form-control-label">
				Coordinate:
			</label>
			<input type="text" name="coordinate" class="form-control" id="coordinateMD">
		</div>
		<div class="col-lg-4">
			<label for="countryMD" class="form-control-label">
				Country:
			</label>
			{!! Form::select('country',$country,null,['id' => 'countryMD', 'class' => 'm-select2-general form-control'])!!}
		</div>
		<div class="col-lg-1">
		</div>
		<div class="col-lg-2">
			<br>
			<a href="#" class="btn btn-primary " onclick="agregarcampo()"><span class="la la-plus"></span></a>
		</div>

	</div>
	<hr>
	<div class="form-group row" id="variatiogroup">
		<div class="col-lg-4" >
			<label for="DispNamMD" class="form-control-label">
				Variation:
			</label>
			<input type="text" name="variation[]" class="variationMD form-control">
		</div>
	</div>
	<hr>
	<div class="form-group pull-right" >
		<button type="submit" class="btn btn-primary">
			Load
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