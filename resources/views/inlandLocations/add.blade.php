<!--begin::Form-->
{!! Form::open(['route' => 'inlandL.store']) !!}
  @csrf
<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('inlandLocations.partials.form_inlandLocation')
        <div class="form-group m-form__group">
            {!! Form::label('country', 'Country') !!}<br> 
            {{ Form::select('country_id',$country,null,['class'=>'custom-select form-control','id' => 'country_id','placeholder'=>'Select an option']) }}

        </div>
    </div>
</div>
<div class="m-form__actions m-form__actions">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
</div>
<br>
{!! Form::close() !!}
<!--end::Form-->

<script src="/js/users.js"></script>
<script type="text/javascript">
    $('#country_id').select2({
        placeholder: "Select an option"
    });
</script>