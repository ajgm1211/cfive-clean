{!! Form::open(['route' => 'companies.store','class' => 'form-group m-form__group']) !!}
<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        <div class="form-group m-form__group">
            {!! Form::label('users_id', 'Associate User') !!}<br>
            {{ Form::select('users[]',$users,null,['class'=>'custom-select form-control users_company','id' => 'users_company','multiple'=>'true']) }}
        </div>
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    </div>
</div>
{!! Form::close() !!}
