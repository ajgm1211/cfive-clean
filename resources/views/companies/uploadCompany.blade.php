{!! Form::open(['route' => 'Upload.Company', 'method' => 'POST', 'files' => 'true'])!!}
    <div class="form-group row ">
        <div class="col-md-1 "></div>
        <div class="col-md-6 ">
            <input type="file" name="file" value="Load File" required />
        </div>
    </div>
    <div id="edit-modal-body" class="modal-footer">
        {!! Form::submit('Load', ['class'=> 'btn btn-primary']) !!}
        <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">Cancel</span>
        </button>
    </div>
{!! Form::close() !!}