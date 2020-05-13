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
            <label class="">Group Recognition Name</label>
            <div class="input-group m-form__group">
                <input type="text" name="name_prin_inp" id="name_prin_inp" value="{{@$options->name}}" required class="form-control" aria-label="Text input with checkbox">
                <div class="input-group-append">
                    <span class="input-group-text">
                        <label class="m-checkbox m-checkbox--single m-checkbox--state m-checkbox--state-primary">
                            <input type="checkbox" id="name_prin_ch" name="name_prin_ch">
                            <span></span>
                        </label>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label class=""><br></label> 
            <div class="" id="conatiner_class">
                <label class="m-checkbox m-checkbox--state-primary">
                    {!! Form::checkbox('group',true,$options->group,['class'=>'form-control','id'=>'groupCal'])!!}
                    Group
                    <span></span>
                </label>
            </div>
        </div>
        <div class="col-md-">
            <label class=""><br></label> 
            <div class="" id="conatiner_class">
                <label class="m-checkbox m-checkbox--state-primary">
                    {!! Form::checkbox('isteu',true,$options->isteu,['class'=>'form-control','id'=>'teuCal'])!!}
                    Is Teu 
                    <span></span>
                </label>
            </div>
        </div>
        <div class="col-md-2">
            <label class=""><br></label>    
            <div class="" id="only_div">
                <label class="m-checkbox m-checkbox--state-primary">
                    {!! Form::checkbox('gp_pcontainer',true,$calculation->gp_pcontainer,['class'=>'form-control','title'=>'Only importer.','id'=>'only_imp'])!!}
                    Only Imp.
                    <span title="Only importer. The calculation type belongs to a column in the container table"></span>
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
            $('#name_prin_inp').attr('disabled','disabled');
            $('#name_prin_inp').removeAttr('required');            
            $('#name_prin_ch').attr('checked','checked');
        } else {
            $('#name_prin_inp').attr('required','required');
            $('#name_prin_ch').removeAttr('checked');            
            $('#name_prin_inp').removeAttr('disabled');            
        }
    }

    $('#name_prin_ch').on('click',function(){
        if($('#name_prin_ch').prop('checked')){
            $('#name_prin_inp').attr('disabled','disabled');
            $('#name_prin_inp').removeAttr('required');            
        } else {
            $('#name_prin_inp').attr('required','required');
            $('#name_prin_inp').removeAttr('disabled');            
        }
    });

</script>
@if(!empty($options->name))
@if($options->name != 'N\A')
<script>
    checkName(false);
</script>
@else
<script>
    checkName(true);
</script>
@endif
@else
<script>
    checkName(true);
</script>
@endif