
<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLongTitle">
		Add Container Calculation Type
	</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">
			&times;
		</span>
	</button>
</div>
<div id="modal-body" class="modal-body">
	<div class="form-group m-form__group row">
		<div class="col-lg-2">
			<label class="">Direction</label>
			<div class="" id="direction">
				{!! Form::select('conatiner',$conatiner,null,['class'=>'m-select2-general form-control','required','id'=>'direction'])!!}
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="SaveStatusModal()">
		Load
	</button>
	<button type="button" class="btn btn-secondary" data-dismiss="modal">
		Close
	</button>
</div>