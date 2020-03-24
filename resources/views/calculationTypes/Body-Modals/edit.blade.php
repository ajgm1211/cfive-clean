{!! Form::model($calculation,['route'=>['CalculationType.update',$calculation->id],'method'=>'PUT','id'=>'form'])!!}
<div class="modal-header">
  <h5 class="modal-title" id="exampleModalLongTitle">
    Update Calculation Type
  </h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">
      &times;
    </span>
  </button>
</div>
<div id="modal-body" class="modal-body">
  <div class="form-group m-form__group row">

    <div class="col-md-1"></div>
    <div class="col-md-5">
      <label class="">Name</label>
      <div class="" id="conatiner_class">
        {!! Form::text('name',$calculation->name,['class'=>'form-control','required','id'=>'nameCal'])!!}
      </div>
    </div>
    <div class="col-md-5">
      <label class="">Code</label>
      <div class="" id="calculationT_class">
        {!! Form::text('code',$calculation->code,['class'=>'form-control','required','id'=>'codeCal'])!!}
      </div>
    </div>

  </div>
  <div class="form-group m-form__group row">

    <div class="col-md-1"></div>
    <div class="col-md-5">

      <div class="" id="conatiner_class">

        <label class="m-checkbox m-checkbox--state-primary">
          {!! Form::checkbox('group',true,$options->group,['class'=>'form-control','id'=>'groupCal'])!!}
          Group
          <span></span>
        </label>



      </div>
    </div>
    <div class="col-md-5">

      <div class="" id="conatiner_class">
        <label class="m-checkbox m-checkbox--state-primary">
          {!! Form::checkbox('isteu',true,$options->isteu,['class'=>'form-control','id'=>'teuCal'])!!}
          Is Teu 
          <span></span>
        </label>
      </div>
    </div>

  </div>
</div>
<div class="modal-footer">
  <input type="submit" class="btn btn-primary" value="Update">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">
    Close
  </button>
</div>
{!! Form::close()!!}

<script>
  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });

</script>