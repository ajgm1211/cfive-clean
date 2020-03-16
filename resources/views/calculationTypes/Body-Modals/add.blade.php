 {!! Form::open(['route'=>'CalculationType.store','method'=>'POST','id'=>'form'])!!}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Add Calculation Type
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
                {!! Form::text('name',null,['class'=>'form-control','required','id'=>'nameCal'])!!}
            </div>
        </div>
        <div class="col-md-5">
            <label class="">Code</label>
            <div class="" id="calculationT_class">
                {!! Form::text('code',null,['class'=>'form-control','required','id'=>'codeCal'])!!}
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="submit" class="btn btn-primary" value="Save">
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