 {!! Form::open(['route'=>'ContainerCalculation.store','method'=>'POST','id'=>'form'])!!}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Add Container Calculation Type
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
            <label class="">Conatiner</label>
            <div class="" id="conatiner_class">
                {!! Form::select('container_id',$containers,null,['class'=>'m-select2-general form-control','required','id'=>'conatiner'])!!}
            </div>
        </div>
        <div class="col-md-5">
            <label class="">Calculation Type</label>
            <div class="" id="calculationT_class">
                {!! Form::select('calculationTs[]',$calculationts,null,['class'=>'m-select2-general form-control','required','id'=>'calculationT','multiple' => 'multiple'])!!}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="submit" class="btn btn-primary" value="Create">
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