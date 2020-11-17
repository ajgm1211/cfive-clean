<!-- tinyMCE styles -->
<script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<!--end styles -->
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<br>
<div class="form-group m-form__group">
    {!! Form::label('Name', 'Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Name','class' => 'form-control m-input','required' => 'required']) !!}


<br>


<div class="form-group m-form__group">
    {!! Form::label('Subject', 'Subject') !!}
    {!! Form::text('subject', null, ['placeholder' => 'Subject','class' => 'form-control m-input','required' => 'required']) !!}

</div>            <span class="m-option__control">
</span>

<div class="form-group m-form__group">
    {!! Form::label('Labels', 'Labels',["class" => "form-label"]) !!}
    <div class="row">
        <div class="col-md-2 dimanic" onclick="AddLabel('{First Name}')">
            <label class="m-option">
                <span class="m-option__control la la-hand-pointer-o">
                </span>
                <span class="m-option__label">
                    <span class="m-option__head">
                        <span class="m-option__title">
                            First Name
                        </span>
                    </span>
                </span>
            </label>
        </div>

        <div class="col-md-2 dimanic" onclick="AddLabel('{Last Name}')">
            <label class="m-option">
                <span class="m-option__control la la-hand-pointer-o">
                </span>
                <span class="m-option__label">
                    <span class="m-option__head">
                        <span class="m-option__title">
                            Last Name
                        </span>
                    </span>
                </span>
            </label>
        </div>
        <div class="col-md-2 dimanic" onclick="AddLabel('{Quote ID}')">
            <label class="m-option">
                <span class="m-option__control la la-hand-pointer-o">
                </span>
                <span class="m-option__label">
                    <span class="m-option__head">
                        <span class="m-option__title">
                            Quote ID
                        </span>
                    </span>
                </span>
            </label>
        </div>
    </div>
</div>

<div class="form-group m-form__group">
    {!! Form::label('Text', 'Text') !!}
    {!! Form::textarea('menssage', null, ['placeholder' => 'Please enter your  menssage','id' => 'textarea_id','class' => 'form-control editor m-input']) !!}

</div>


<!-- tinyMCE script config -->
<script>


    function AddLabel(value){
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(value).select();
        document.execCommand("copy");
        $temp.remove();
        tinymce.activeEditor.execCommand('mceInsertContent', false, ' '+value); 
    }


    var title = <?php echo json_encode($templates) ?>;


    var editor_config = {
        path_absolute : "/",
        selector: "textarea.editor",
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar: 
        "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
        //
        relative_urls: false,
        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + "&type=Images";
            } else {
                cmsURL = cmsURL + "&type=Files";
            }

            tinyMCE.activeEditor.windowManager.open({
                file : cmsURL,
                title : 'Filemanager',
                width : x * 0.9,
                height : y * 0.9,
                resizable : "yes",
                close_previous : "no"
            });
        }
    };


    tinymce.init(editor_config);
</script>
<script>
    {!! \File::get(base_path('vendor/barryvdh/laravel-elfinder/resources/assets/js/standalonepopup.min.js')) !!}
</script>
<!-- end tinyMCE scripts config -->

