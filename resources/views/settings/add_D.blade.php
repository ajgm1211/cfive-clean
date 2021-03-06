<div class="modal fade" id="AddDelegationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="min-width: 400px">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Add Delegation</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['route' => 'delegation.store','method' => 'POST'])!!}
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Name</b></label>
                    {{ Form::text('name', null,['placeholder' => 'Please enter a name','class'=>'form-control','required']) }}
                    {{ Form::hidden('company_user_id', @$company->companyUser->id,['placeholder' => 'Please enter a name','class'=>'form-control']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Phone</b></label>
                    {{ Form::text('phone', null,['placeholder' => 'Please enter a phone','class'=>'form-control','required']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Address</b></label>
                    {{ Form::text('address', null,['placeholder' => 'Please enter an address','class'=>'form-control','required']) }}
                </div>
                <br>
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" data-toggle="modal" data-target="#AddDelegationModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
            </div>
            {!! Form::close()!!}
        </div>
    </div>
</div>