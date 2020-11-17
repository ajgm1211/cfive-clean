<div class="modal fade bd-example-modal-lg" id="modalupload"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Upload Companies
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            {!! Form::open(['route' => 'Upload.Company', 'method' => 'POST', 'files' => 'true'])!!}
                <div id="edit-modal-body" class="modal-body">
                    <div class="form-group row pull-right">
                        <div class="col-md-3 ">

                        </div>
                    </div>
                    <div class="form-group row ">
                        <div class="col-md-1 "></div>
                        <div class="col-md-6 ">
                            <input type="file" name="file" value="Load File" required />
                        </div>
                    </div>
                </div>
                <div id="edit-modal-body" class="modal-footer">
                    {!! Form::submit('Load', ['class'=> 'btn btn-primary']) !!}
                    <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">Cancel</span>
                    </button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>