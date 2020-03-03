@extends('layouts.app')
@section('css')
@parent

@endsection

@section('title', 'New Request')
@section('content')

<div class="m-content">
	@if (count($errors) > 0)
	<div id="notificationError" class="alert alert-danger">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
	@if(Session::has('message.nivel'))

	<div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
		<div class="m-alert__icon">
			<i class="la la-warning"></i>
		</div>
		<div class="m-alert__text">
			<strong>
				{{ session('message.title') }}
			</strong>
			{{ session('message.content') }}
		</div>
		<div class="m-alert__close">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
		</div>
	</div>
	@endif

	<!--Begin::Main Portlet-->
	<div class="m-portlet m-portlet--full-height">
		<!--begin: Portlet Head-->
		<div class="m-portlet__body">
			<div class="tab-content">
				<div class="tab-pane active" id="m_portlet_tab_1_1">
					<h5 class="m-portlet__head-text">
						<strong  style="color:#0062ff;">Import New Contract - Sea Freight FCL</strong>
					</h5>
					<br>
					<div class="row">
						<div class="col-lg-12">

							<form method="post" id="form" enctype="multipart/form-data">
								@csrf
								<div class="form-group m-form__group row">
									<div class="col-lg-2">
										<label class="">Carrier</label>
										<div class="" id="carrierMul">
											{!! Form::select('carrierM[]',$carrier,null,['class'=>'m-select2-general form-control','id'=>'carrierM','required','multiple'=>'multiple'])!!}
										</div>
									</div>
									<div class="col-lg-2">
										<label class="">Group Equipments</label>
										<div class="" id="ssss">
											{!! Form::select('ssss',['0'=>'---'],null,['class'=>'m-select2-general form-control','required','id'=>'ssss'])!!}
										</div>
									</div>
									<div class="col-lg-3">
										<label class="">Equipments</label>
										<div class="" id="-----">
											{!! Form::select('---[]',['0'=>''],null,['class'=>'m-select2-general form-control','id'=>'-----','required','multiple'=>'multiple'])!!}
										</div>
									</div>
									<div class="col-lg-2">
										<label class="">Direction</label>
										<div class="" id="direction">
											{!! Form::select('direction',$direction,null,['class'=>'m-select2-general form-control','required','id'=>'direction'])!!}
										</div>
									</div>
									<div class="col-lg-3">
										<label for="validation_expire" class=" ">Validation</label>
										<input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="Please enter validation date">
									</div>
								</div>
								<div class="form-group m-form__group row">
									<div class="col-lg-6">
										<label for="nameid" class="">References</label>
										{!!  Form::text('name',null,['id'=>'nameid',
										'placeholder'=>'References  ',
										'required',
										'class'=>'form-control m-input'])!!}
									</div>
								</div>

								<input type="hidden" name="CompanyUserId" value="{{$user->company_user_id}}" />
								<input type="hidden" name="user" value="{{$user->id}}" />

							</form>

						</div>
						<div class="col-md-4 col-md-offset-4">&nbsp;</div>
						<div class="col-lg-12">
							<!--begin::Section-->
							<div class="m-section m-section--last">
								<div class="m-section__content">
									<!--begin::Preview-->
									<div class="m-demo">
										<div class="m-demo__preview">
											<div class="m-list-search">
												<div class="m-list-search__results">
													<span class="m-list-search__result-message m--hide">
														No record found
													</span>
													<span class="m-list-search__result-category m-list-search__result-category--first" style="text-transform: initial;" >
														Upload
													</span>
													<br>
													<style>
														.m-list-search .m-list-search__results .m-list-search__result-category {
															color: #45426c;
														}
														.m-demo {
															background: #f7f7fa;
															margin-bottom: 20px;
															border-radius: 10px ;
														}
														.m-dropzone.m-dropzone--success {
															border-color: rgba(46, 35, 175, 0.28);
														}
														.m-dropzone {
															border: 1px solid;

														}
														.dropzone {
															background: #f7f7fa;
															border-radius: 10px;
														}
														.m-demo .m-demo__preview {
															/*border-radius: 100px;*/
															background: #f7f7fa;
															border: 4px solid #f7f7fa;
															border-radius: 10px;
															padding: 30px;
														}
														
														

													</style>
													<div class="tabDrag ">
														<div class="m-dropzone dropzone m-dropzone--success"  id="document-dropzone">
															<div class="m-dropzone__msg dz-message needsclick">
																<img class="img-dropzone" src="/images/upload-files.png" alt="Smiley face" height="100" width="100">
																<h3 class="m-dropzone__msg-title">
																	Drop files here or click to upload.
																</h3>
																<span class="m-dropzone__msg-desc">
																	Only image, pdf and psd files are allowed for upload
																</span>
															</div>
														</div>
													</div>

												</div>
											</div>
										</div>
									</div>


								</div>
							</div>
							<!--end::Section-->
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--end: Form Wizard-->
	</div>
	<!--End::Main Portlet-->
	<!--  begin modal editar rate -->

	<div class="modal fade bd-example-modal-lg" id="modaledit"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">
						Load Request
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
				</div>
				<div id="edit-modal-body" class="modal-body">
					<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
						<div class="row align-items-center">
							<div class="col-xl-12 order-2 order-xl-1 conten_load">
								<center>
									<div class="form-group">
										<div class="col-sm-6">
											<h2 id="mjsH"> Please Wait...</h2>
										</div>
										<div class="col-sm-6">
											<img src="{{asset('images/ship.gif')}}" style="height:170px">
										</div>
									</div>
									<div id="uploadStatus"></div>
									<div class="col-sm-8">
										<div class="percent">0%</div> Complete
									</div>
									<div class="col-sm-8">
										<div class="progress">
											<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%">
											</div>
										</div>
									</div>
								</center>
							</div>
						</div>


					</div>
				</div>
				<div class="modal-footer">
					<center>
						<h7>Do not leave this window, we will redirect you Thank you.</h7>
					</center>
				</div>
			</div>

			<!--  end modal editar rate -->


		</div>
	</div>


</div>

@endsection
@section('js')
@parent

<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="{{asset('js/Contracts/ImporContractFcl.js')}}"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});




	/*
    $('#btnFiterSubmitSearch').click(function(){
        $('#nameid').removeAttr('required');
        $('#carrierM').removeAttr('required');
        $('#direction').removeAttr('required');
        $('#requesttable').DataTable().draw(true);
        $('#nameid').attr('required','required');
        $('#carrierM').attr('required','required');
        $('#direction').attr('required','required');
    });

    function fileempty(){
        if( document.getElementById("file").files.length == 0 ){
            swal("Error!", "Choose File", "error");
        }
    }
    function cambiar(){
        var pdrs = document.getElementById('file').files[0].name;
        document.getElementById('info').innerHTML = pdrs;
    } 
    function validate(formData, jqForm, options) {
        var form = jqForm[0];
        if (!form.file.value) {
            //alert('File not found');
            return false;
        }
    }*/

	$("#form").on('submit', function(e){
		var date = $('#m_daterangepicker_1').val().split(' / ');
		var date_star = $.trim(date[0]);
		var date_end  = $.trim(date[1]);
		e.preventDefault();
		if(date_star == date_end){
			swal(
				"Error",
				"Error, Please select the date!", "error",
				true,
			);
		}else {

			var count =0;
			var bar = $('.progress-bar');
			var percent = $('.percent');
			var status = $('#status');
			var data = new FormData(this)
			$.ajax({
				xhr: function() {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(evt) {
						if (evt.lengthComputable) {
							var percentComplete = ((evt.loaded / evt.total) * 100);
							$(".progress-bar").width(percentComplete + '%');
							$(".progress-bar").html(percentComplete+'%');
							var percentVal = percentComplete + '%';
							bar.width(percentVal);
							percent.html(percentVal);
						}
					}, false);
					return xhr;
				},
				url: '{{route("RequestImportation.store2")}}',
				method: 'post',
				data:new FormData(this),
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				beforeSubmit: validate,
				beforeSend: function(){
					$('#modaledit').modal('show');
					$(".progress-bar").width('0%');
					status.empty();
					var percentVal = '0%';
					bar.width(percentVal)
					percent.html(percentVal);
				},
				error:function(){
					$('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
				},
				success: function(resp){
					if(resp.data == 'ok'){
						$('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
						$('#modaledit').modal('hide');
						window.location.href = "{{route('contracts.index')}}";
					}else if(resp.data == 'err'){
						$('#uploadStatus').html('<p style="color:#EA4335;">Error, try again.</p>');
						$('#modaledit').modal('hide');
						swal({
							title: "Error",
							text: "Error, Please try again !",
							icon: "error",
							buttons: true,
						})
							.then((willDelete) => {
							if (willDelete) {
								count = 0;
								$('#modaledit').modal('hide');
								window.location.href = "{{route('contracts.index')}}";
							} else {
								count = 0;
								$('#modaledit').modal('hide');
								window.location.href = "{{route('contracts.index')}}";
							}
						});
					}
				}
			});
		}

	});

	var uploadedDocumentMap = {}


	Dropzone.options.documentDropzone = {
		url: '{{ route('contracts.storeMedia') }}',
		maxFilesize: 2, // MB
		addRemoveLinks: true,
		headers: {
		'X-CSRF-TOKEN': "{{ csrf_token() }}"
	},
		success: function (file, response) {
			$('#formu').append('<input type="hidden" name="document[]" value="' + response.name + '">')
			uploadedDocumentMap[file.name] = response.name
		},
			removedfile: function (file) {
				file.previewElement.remove()
				var name = ''
				if (typeof file.file_name !== 'undefined') {
					name = file.file_name
				} else {
					name = uploadedDocumentMap[file.name]
				}
				$('#formu').find('input[name="document[]"][value="' + name + '"]').remove()
			},
				init: function () {
					@if(isset($project) && $project->document)
					var files =
						{!! json_encode($project->document) !!}
					for (var i in files) {
						var file = files[i]
						this.options.addedfile.call(this, file)
						file.previewElement.classList.add('dz-complete')
						$('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
					}
					@endif
				}
				}

</script>

@stop