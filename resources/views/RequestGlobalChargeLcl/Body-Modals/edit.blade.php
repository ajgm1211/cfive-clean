<div class="form-group row">
	<div class="col-lg-4">
		<label for="NameMD" class="form-control-label">
			Name:
		</label>
		<input type="text" name="name" value="{{$requests->name}}" required="required" class="form-control" disabled id="NameMD">
	</div>
	<div class="col-lg-4">
		<label for="number" class="form-control-label">
			Company User:
		</label>
		<input type="text" name="number" value="{{$requests->companyuser->name}}" required="required" class="form-control" disabled id="Company">
	</div>
	<div class="col-lg-4">
		<label for="number" class="form-control-label">
			Id:
		</label>
		<input type="text" name="number" value="{{$requests->id}}" required="required" class="form-control" disabled id="NumberMD">
	</div>
</div>
<div class="form-group row">
	<div class="col-lg-4">
		<label for="number" class="form-control-label">
			Date:
		</label>
		<input type="text" name="number" value="{{$requests->created}}" required="required" class="form-control" disabled id="NumberMD">
	</div>
	<div class="col-lg-4">
		<label for="number" class="form-control-label">
			Username Load:
		</label>
		<input type="text" name="number" value="{{$requests->username_load}}" required="required" class="form-control" disabled id="NumberMD">
	</div>
	<input type="hidden" id="idContract" value="{{$requests->id}}"/>
</div>
<div class="form-group row">

	<div class="col-lg-12">
		{!! Form::label('Status', 'Status',["class"=>"form-control-label"]) !!}
		{{ Form::select('status',$status_arr,$requests->status,['id' => 'statusSelectMD','class'=>'m-select2-general  form-control ','style' => 'width:100%;']) }}
	</div>
</div>
<script>

	$('.m-select2-general').select2({

	});

	function SaveStatusModal(){
		//$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

		var status_id   = $('#statusSelectMD').val();
		var idContract    = $('#idContract').val();
		url='{!! route("Request.GlobalC.status.lcl") !!}';
		$.ajax({
			url:url,
			method:'get',
			data:{id:idContract,status:status_id},
			success: function(data){
				//alert(data.data + data.status);
				if(data.data == 1){
					if(data.request.time_total == null){
						$('#timeElapsed'+idContract).text(' ------------------ ');
					}else {
						$('#timeElapsed'+idContract).text(data.request.time_total);						
					}
					$('#userLoad'+idContract).text(data.request.username_load);
					$('a#statusHrf'+idContract).text(data.status);
					$('a#statusHrf'+idContract).css('color',data.color);
					$('#statusSamp'+idContract).css('color',data.color);
					$('#changeStatus').modal('hide');
					//swal('Deleted!','Your Status has been changed.','success');
					toastr.success("Your Status has been changed. ID: "+data.request.id+" - "+data.request.name, "Status. ID: "+data.request.id);
				}else if(data.data == 2){
					//swal("Error!", "An internal error occurred!", "error");
					toastr.success("An internal error occurred!", "Error!");
				}
			}
		});

	}

</script>