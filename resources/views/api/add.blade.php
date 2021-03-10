<div class="modal fade" id="AddIntegrationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="min-width: 700px; right: 100px;">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    <b>Add Integration</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['route' => 'api.store','method' => 'POST'])!!}
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Name</b></label>
                    {{ Form::text('name', null,['placeholder' => 'Please enter a name','class'=>'form-control','required']) }}
                    {{ Form::hidden('api_integration_setting_id', @$api->id,['placeholder' => 'Please enter a name','class'=>'form-control','id'=>'api_integration_setting_id']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>URL (Enter entire URL)</b></label>
                    {{ Form::text('url', null,['placeholder' => 'Please enter an URL','class'=>'form-control','required']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>API Key</b></label>
                    {{ Form::text('api_key', null,['placeholder' => 'Please enter an API Key','class'=>'form-control','required']) }}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Associated to</b></label>
                    {!! Form::select('company_user_id', $companies, null, ['placeholder'=>'Select an option','class' => 'form-control','required']) !!}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Partner</b></label>
                    {!! Form::select('partner_id', $partners, null, ['placeholder'=>'Select an option','class' => 'form-control','required']) !!}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Module</b></label>
                    {!! Form::select('module', ['Contacts'=>'Contacts','Companies'=>'Companies'], null, ['placeholder'=>'Select an option','class' => 'form-control','required']) !!}
                </div>
                <div class="form-group m-form__group">
                    <label style="letter-spacing: 0.7px"><b>Type</b></label>
                    {!! Form::select('is_daily', [0=>'Not daily',1=>'Daily'], null, ['placeholder'=>'Select an option','class' => 'form-control','required']) !!}
                </div>
                <br>
                <hr>
                <div class="form-group m-form__group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" data-toggle="modal" data-target="#AddIntegrationModal" class="btn btn-danger">Cancel</button>
                </div>
                <br>
            </div>
            {!! Form::close()!!}
        </div>
    </div>
</div>