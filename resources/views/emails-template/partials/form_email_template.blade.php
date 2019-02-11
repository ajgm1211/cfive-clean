<!-- tinyMCE styles -->
<script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<!--end styles -->

<div class="form-group m-form__group">
    {!! Form::label('Name', 'Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Name','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('Subject', 'Subject') !!}
    {!! Form::text('subject', null, ['placeholder' => 'Subject','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('Labels', 'Labels') !!}
    {!! Form::select('label', ['{First Name}' => 'First Name',
    '{Last Name}' => 'Last Name'
    ,'{Quote ID}' => 'Quote ID',
    '{Company Name}' => 'Company Name'],null, ['class' => 'form-control custom-select','id' => 'label_id','onclick' => 'AddLabel()']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('Text', 'Text') !!}
    {!! Form::textarea('menssage', null, ['placeholder' => 'Please enter your  menssage','id' => 'textarea_id','class' => 'form-control editor m-input']) !!}

</div>


<!-- tinyMCE script config -->
<script>

    function AddLabel(){
        var label = $('#label_id option:selected').val();
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(label).select();
        document.execCommand("copy");
        $temp.remove();
        tinymce.activeEditor.execCommand('mceInsertContent', false, ' '+label); 
    }


    var title = <?php echo json_encode($templates) ?>;

        
    
    var editor_config = {
        path_absolute : "/",
        selector: "textarea.editor",
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern codesample",
            "fullpage toc imagetools help"
        ],
        toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
        templates: title,
        external_plugins: { "nanospell": "{{asset('js/tinymce/plugins/nanospell/plugin.js')}}" },
        nanospell_server:"php",
        browser_spellcheck: true,
        relative_urls: false,
        remove_script_host: false,
        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + "&type=Images";
            } else {
                cmsURL = cmsURL + "&type=Files";
            }

            tinymce.activeEditor.windowManager.open({
                file: '<?= route('elfinder.tinymce4') ?>',// use an absolute path!
                title: 'File manager',
                width: 900,
                height: 450,
                resizable: 'yes'
            }, {
                setUrl: function (url) {
                    win.document.getElementById(field_name).value = url;
                }
            });

            tinymce.init({
                plugins: "paste",
                paste_as_text: true
            });
        }
    };

    tinymce.init(editor_config);
</script>
<script>
    {!! \File::get(base_path('vendor/barryvdh/laravel-elfinder/resources/assets/js/standalonepopup.min.js')) !!}
</script>
<!-- end tinyMCE scripts config -->

