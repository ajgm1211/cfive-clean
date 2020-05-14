{!! Form::model($container,['route'=>['Container.update',$container->id],'method'=>'PUT','id'=>'form'])!!}
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLongTitle">
        Update Container
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
                {!! Form::text('name',$container->name,['class'=>'form-control','required','id'=>'nameCal'])!!}
            </div>
        </div>
        <div class="col-md-5">
            <label class="">Code</label>
            <div class="" id="calculationT_class">
                {!! Form::text('code',$container->code,['class'=>'form-control','required','id'=>'codeCal'])!!}
            </div>
        </div>

    </div>
    <div class="form-group m-form__group row">

        <div class="col-md-1"></div>
        <div class="col-md-5">
            <label class="">Column Name DB</label>
            <div class="input-group m-form__group">
                <input type="text" name="column_db" id="name_prin_inp" value="{{@$options->column_name}}" required class="form-control" aria-label="Text input with checkbox">
                <div class="input-group-append">
                    <span class="input-group-text">
                        <label class="m-checkbox m-checkbox--single m-checkbox--state m-checkbox--state-primary">
                            <input type="checkbox" id="name_prin_ch" name="column_db_ch">
                            <span></span>
                        </label>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <label class="">Equipment</label>
            <div class="" id="calculationT_class">
                {!! Form::select('equipment_id',$equipments,$container->gp_container_id,['class'=>'m-select2-general form-control','required','id'=>'equipment_id'])!!}
            </div>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-md-1"></div>
        <div class="col-md-2">
            <label class=""><br></label>    
            <div class="" id="only_div">
                <label class="m-checkbox m-checkbox--state-primary">
                    {!! Form::checkbox('optional',true,@$options->optional,['class'=>'form-control','title'=>'only importer','id'=>'optional'])!!}
                    Optional
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

    function checkName(bool){
        if(bool){
            $('#name_prin_inp').removeAttr('required');            
            $('#name_prin_inp').attr('required','required');
            $('#name_prin_ch').attr('checked','checked');
            $('#name_prin_inp').removeAttr('disabled');            
        } else {
            $('#name_prin_inp').attr('disabled','disabled');
            $('#name_prin_ch').removeAttr('checked');            
        }
    }

    $('#name_prin_ch').on('click',function(){
        if(!$('#name_prin_ch').prop('checked')){
            $('#name_prin_inp').attr('disabled','disabled');
            $('#name_prin_inp').removeAttr('required');            
        } else {
            $('#name_prin_inp').attr('required','required');
            $('#name_prin_inp').removeAttr('disabled');            
        }
    });

</script>
@if(!empty($options->column))
@if($options->column == true)
<script>
    checkName(true);
</script>
@else
<script>
    checkName(false);
</script>
@endif
@else
<script>
    checkName(false);
</script>
@endif