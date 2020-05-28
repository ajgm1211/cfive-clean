<div class="modal fade" id="EditIntegrationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="min-width: 700px; right: 100px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Edit Integration</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
                $modules = ['Contacts', 'Companies'];
            ?>
            {!! Form::open(['route' => 'api.update','method' => 'PUT'])!!}
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Name</b></label>
                    {{ Form::text('name', null,['placeholder' => 'Please enter a name','class'=>'form-control','required','id'=>'name']) }}
                    {{ Form::hidden('api_integration_id', null,['placeholder' => 'Please enter a id','class'=>'form-control','id'=>'api_integration_id']) }}
                    {{ Form::hidden('api_integration_setting_id', @$api->id,['placeholder' => 'Please enter a name','class'=>'form-control','id'=>'api_integration_setting_id']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>URL</b></label>
                    {{ Form::text('url', null,['placeholder' => 'Please enter an URL','class'=>'form-control','required','id'=>'url']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>API Key</b></label>
                    {{ Form::text('api_key', null,['placeholder' => 'Please enter an API Key','class'=>'form-control','required','id'=>'api_key']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Associated to</b></label>
                    {!! Form::select('partner_id', $partners, null, ['placeholder'=>'Select an option','class' => 'form-control','required','id'=>'partner_id']) !!}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Module</b></label>
                    <select name="module" class="form-control" id="module" required>
                        <option value="">Select an option</option>
                        <option value="Contacts">Contacts</option>
                        <option value="Companies">Companies</option>
                    </select>
                </div>
                <br>
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" data-toggle="modal" data-target="#EditIntegrationModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
            </div>
            {!! Form::close()!!}
        </div>
    </div>
</div>