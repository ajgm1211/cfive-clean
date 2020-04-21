<!--begin::Form-->
{!! Form::open(['route' => 'inlandD.store']) !!}
@csrf
<div class="m-form__section m-form__section--first">
  <div class="form-group m-form__group">
    @include('inlandDistances.partials.form_inlandLocation')
    <div class="form-group m-form__group">
      {!! Form::label('harbor', 'Port') !!}<br> 
      {{ Form::select('harbor_id',$harbor,null,['class'=>'m-select-2 form-control','id' => 'harbor_id','placeholder'=>'Select an option','required'=>'true']) }}

    </div>
    <div class="form-group m-form__group">
      {!! Form::label('Province', 'Province') !!}<br> 
      {{ Form::select('province',$inlandL,null,['class'=>'m-select-2 form-control','id' => 'inland_location_id','placeholder'=>'Select an option']) }}

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
  $('.m-select-2').select2({
    placeholder: "Select an option"
  });
</script>