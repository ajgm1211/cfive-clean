
<!--begin::Form-->
{!! Form::model($inlandL, ['route' => ['inlandL.update', $inlandL], 'method' => 'PUT']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('inlandLocations.partials.form_inlandLocation')

        <div class="form-group m-form__group">
            {!! Form::label('country_id', 'Country') !!}<br>
            {{ Form::select('country_id',$country,$inlandL->country->id,['class'=>'','id' => 'country_id']) }}
        </div>                
    </div>
</div>
<div class="m-form__actions m-form__actions">
    {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
</div>
<br>
{!! Form::close() !!}
<!--end::Form-->
<script type="text/javascript">
    $('#country_id').select2({
        placeholder: "Select an option"
    });
</script>