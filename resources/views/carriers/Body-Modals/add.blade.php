{!! Form::open(['route'=>'managercarriers.store','method'=>'POST','id'=>'frmCarrier','files'=>true])!!}
<div class="form-group row">
    <div class="col-lg-3">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" value="" required="required" class="form-control"  id="NameMD">
    </div>
    <div class="col-lg-3">
        <label for="number" class="form-control-label">
            Name of the Image:
        </label>
        <input type="text" name="image" value="" required="required" class="form-control"  id="Number">
    </div>
    <div class="col-lg-4">
        <label><br></label>
        <br>
        <label for="file" class="btn btn-primary form-control-label form-control imagIn" >
            Choose File
        </label>
        <input type="file" class="form-control imagIn" required name="file" onchange='cambiar()' id="file"  style='display: none;'>
        <div id="info" style="color:red"></div>
    </div>
    <div class="col-lg-2">
        <label><br></label>
        <a href="#" class="btn btn-primary form-control" onclick="agregarcampo()"><span class="la la-plus"></span></a>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-12">
        <center>
            <label for="number" class="blah form-control-label">
                Preview:
            </label>
            <br>
            <img id="blah" src="#" class="blah" alt="your image" width="175" height="150"/>
        </center>
    </div>
</div>
<hr>
<div class="form-group row" id="variatiogroup">
    <div class="col-lg-4" >
        <label for="DispNamMD" class="form-control-label">
            Variation:
        </label>
        <input type="text" name="variation[]" class="form-control">
    </div>
</div>
<div id="modal-body" class="modal-footer">
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
            {!! Form::submit('Add', ['class'=> 'btn btn-primary','onclick' => 'fileempty()']) !!}
            <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
    </div>
    {{ Form::close()}}


    <script>

        $(document).ready(function(){
            $('.blah').attr('hidden','hidden');
        });
        function fileempty(){
            if( document.getElementById("file").files.length == 0 ){
                swal("Error!", "Choose File", "error");
            }
        }

        function cambiar(){
            $('#blah').removeAttr('hidden');
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
