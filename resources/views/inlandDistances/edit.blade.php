
<!--begin::Form-->
{!! Form::model($inlandD, ['route' => ['inlandD.update', $inlandD], 'method' => 'PUT']) !!}

<div class="m-form__section m-form__section--first">
  <div class="form-group m-form__group">
    @include('inlandDistances.partials.form_inlandLocation')
    <div class="form-group m-form__group">
      {!! Form::label('harbor', 'Port') !!}<br> 

      {{  $inlandD->harbor->name }}
      {{ Form::hidden('harbor_id',$inlandD->harbor_id,null,['class'=>'m-select-2 form-control','id' => 'harbor_id','placeholder'=>'Select an option','required'=>'true']) }}


    </div>
    <div class="form-group m-form__group">
      {!! Form::label('location', 'Location') !!}<br> 
      {{ Form::select('inland_location_id',$inlandL,$inlandD->inland_location_id,['class'=>'m-select-2 form-control','id' => 'inland_location_id','placeholder'=>'Select an option']) }}

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