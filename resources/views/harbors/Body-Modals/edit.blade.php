
<form  id="formEdit" enctype="multipart/form-data">
	@csrf
	<div class="form-group row">
		<div class="col-lg-4">
			<label for="NameMD" class="form-control-label">
				Name:
			</label>
			<input type="text" name="name" value="{{$harbors->name}}" required="required" class="form-control" id="NameMD">
		</div>
		<div class="col-lg-4">
			<label for="CodeMD" class="form-control-label">
				Code:
			</label>
			<input type="text" name="code" value="{{$harbors->code}}" required="required" class="form-control" id="CodeMD">
		</div>
		<div class="col-lg-4">
			<label for="DispNamMD" class="form-control-label">
				Display Name:
			</label>
			<input type="text" name="display_name" value="{{$harbors->display_name}}" required="required" class="form-control" id="DispNamMD">
		</div>
	</div>
	<div class="form-group row">
		<div class="col-lg-4">
			<label for="DispNamMD" class="form-control-label">
				Coordinate:
			</label>
			<input type="text" name="coordinate" value="{{$harbors->coordinates}}" class="form-control" id="coordinateMD">
		</div>
		<div class="col-lg-4">
			<label for="countryMD" class="form-control-label">
				Country:
			</label>
			{!! Form::select('country',$country,$harbors->country_id,['id' => 'countryMD', 'class' => 'm-select2-general form-control'])!!}
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
		@foreach($decodejosn as $nameVaration)

		@if($nameVaration != '')
		<div class="col-lg-4" >
			<label for="DispNamMD" class="form-control-label">
				Variation:
			</label>
			<input type="text" name="variation[]" value="{{$nameVaration}}" class="form-control">
			<a href="#" class="borrarInput"><samp class="la la-remove"></samp></a>
		</div>
		@endif
		@endforeach
	</div>
	<hr>
	<div class="form-group pull-right" >
		<button type="submit" class="btn btn-primary">
			Update
		</button>
		<button type="button" class="btn btn-secondary" data-dismiss="modal">
			Cancel
		</button>
	</div>
</form>


<script>
	$('.m-select2-general').select2({

	});

	$(document).on('click','.borrarInput',function(e){
		$(this).closest('div').remove();
	});

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$("#formEdit").on('submit', function(e){
		e.preventDefault();
		var variation = [];
		var name		= $('#NameMD').val();
		var code		= $('#CodeMD').val();
		var displayname	= $('#DispNamMD').val();
		var coordinate	= $('#coordinateMD').val();
		var country		= $('#countryMD').val();
		variation = $("input[name='variation[]']").map(function(){return $(this).val();}).get();

		var data	= { name:name,code,displayname,coordinate,country,variation};
		//console.log(data);
		var url = '{{route("UploadFile.update",":id")}}';
		url = url.replace(':id','{{$harbors->id}}')
		$.ajax({
			url: url,
			method: 'PUT',
			data:data,
			dataType:'JSON',
			error:function(){
				alert('error');
			},
			success: function(resp){
				//console.log(resp);
				if(resp.success == true){
					$('#addHarborModal').modal('hide');
					$('#tdcode'+resp.data.id).text(resp.data.code);
					$('#tddisplay_name'+resp.data.id).text(resp.data.display_name);
					$('#tdcoordinates'+resp.data.id).text(resp.data.coordinates);
					$('#tdcountry'+resp.data.id).text(resp.data.country_id);
					$('#tdvaration'+resp.data.id).text(resp.data.varation);
					$('#tdname'+resp.data.id).text(resp.data.name);
					toastr.success("Updated port", "Success");

				}else if(resp.success == false){

				}
			}
		});


	});
</script>