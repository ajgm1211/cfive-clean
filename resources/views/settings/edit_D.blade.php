<div class="modal fade" id="EditDelegationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="min-width: 400px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Edit Delegation</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['route' => 'settings.updateD','method' => 'PUT'])!!}
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Name</b></label>
                    {{ Form::text('name', null,['placeholder' => 'Please enter a name','class'=>'form-control name','required','id'=>'name']) }}
                    {{ Form::hidden('id', null,['class'=>'form-control','id'=>'id']) }}

                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Phone</b></label>
                    {{ Form::text('phone', null,['placeholder' => 'Please enter a phone','class'=>'form-control phone','required','id'=>'phone']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Address</b></label>
                    {{ Form::text('address',null,['placeholder' => 'Please enter an address','class'=>'form-control address','required','id'=>'address']) }}
                </div>
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" data-toggle="modal" data-target="#EditDelegationModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
            </div>
            {!! Form::close()!!}
        </div>
    </div>
</div>