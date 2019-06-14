<?php echo e(Form::model($carrier, array('route' => array('managercarriers.update', $carrier->id), 'method' => 'PUT', 'id' => 'frmSurcharges','files'=>true))); ?>

<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" value="<?php echo e($carrier->name); ?>" required="required" class="form-control"  id="NameMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Name of the Image:
        </label>
        <input type="text" name="image" value="<?php echo e($carrier->image); ?>" required="required" class="form-control"  id="Number">
    </div>
    <div class="col-lg-4">
        <label class="form-control-label">
        </label>
        <label class="m-option">
            <span class="m-option__control">
                <span class="m-checkbox m-checkbox--brand m-checkbox--check-bold">
                    <input name="DatImag" id="iamgechk" type="checkbox">
                    <span></span>
                </span>
            </span>
            <span class="m-option__label">
                <span class="m-option__head">
                    <span class="m-option__title">
                        Change Image
                    </span>
                </span>
            </span>
        </label>

    </div>
</div>
<div class="form-group row">
    <div class="col-lg-5">
        <label for="number" class="form-control-label">
            Preview:
        </label>
        <br>
        <img id="blah" src="<?php echo e($image); ?>" alt="your image" width="175" height="150"/>
    </div>
    <div class="col-lg-6 " id="imagDiv">
        <label><br></label>
        <br>
        <label for="file" class="btn btn-primary form-control-label form-control imagIn" >
            Choose File
        </label>
        <input type="file" class="form-control imagIn" name="file" onchange='cambiar()' id="file"  style='display: none;'>
        <div id="info" style="color:red"></div>
    </div>
</div>
<div id="modal-body" class="modal-footer">
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
            <?php echo Form::submit('Update', ['class'=> 'btn btn-primary','onclick' => 'fileempty()']); ?>

            <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
    </div>
    <?php echo e(Form::close()); ?>



    <script>
        $(document).ready(function(){
            $('#imagDiv').attr('hidden','hidden');
        });
        $('#iamgechk').on('click',function(){
            if($('#iamgechk').prop('checked')){
                $('.imagIn').attr('required','required');
                $('#imagDiv').removeAttr('hidden');
            } else {
                $('.imagIn').removeAttr('required','required');
                $('#imagDiv').attr('hidden','hidden');
            }
        });


        function fileempty(){
            if($('#iamgechk').prop('checked')){
                if( document.getElementById("file").files.length == 0 ){
                    swal("Error!", "Choose File", "error");
                }
            }
        }
        function cambiar(){
            var pdrs = document.getElementById('file').files[0].name;
            document.getElementById('info').innerHTML = pdrs;
        } 

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#file").change(function(){
            readURL(this);
        });
    </script>
