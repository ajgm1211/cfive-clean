<div class="col-lg-12">
	<div class="form-group row">
		<div class="col-md-6">
			<label> References </label>
			{!! Form::text('references',$request->namecontract,['class' => 'form-control','readonly'])!!}
		</div>
		<div class="col-md-4">
			<label>Company</label>
			{!! Form::text('company',null,['class' => 'form-control','readonly'])!!}
		</div>
		<div class="col-md-2">
			<label>Request ID</label>
			{!! Form::text('request_id',$request->id,['class' => 'form-control','readonly'])!!}
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-5">
			<label> Carrier </label>
			{!! Form::text('carrier',null,['class' => 'form-control','readonly'])!!}
		</div>
		<div class="col-md-3">
			<label>Direction</label>
			{!! Form::text('direction',null,['class' => 'form-control','readonly'])!!}
		</div>
		<div class="col-md-4">
			<label>Validation</label>
			{!! Form::text('validation',$request->validation,['class' => 'form-control','readonly'])!!}
		</div>
	</div>
</div>

<div class="modal-footer">      
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Close
    </button>
</div>