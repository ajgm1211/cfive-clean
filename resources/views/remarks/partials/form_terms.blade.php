<!-- tinyMCE styles -->
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>

<!--end styles -->

<div class="form-group m-form__group">
    {!! Form::label('Name', 'Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Please enter the term name','class' => 'form-control
    m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('Port', 'Ports') !!}
    {!! Form::select('ports[]',$harbors,@$selected_harbors,
    ['class' => 'm-select2-general form-control','required','multiple' => 'multiple']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('Carriers', 'Carriers') !!}
    {!! Form::select('carriers[]',$carriers,@$selected_carriers,
    ['class' => 'm-select2-general form-control', 'multiple' => 'multiple']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::label('Language', 'Language') !!}
    {!! Form::select('language',$languages,@$remark['language_id'],
    ['class' => 'm-select2-general form-control']) !!}
</div>

<div class="form-group m-form__group">
    {!! Form::label('Import', 'Import') !!}
    {!! Form::textarea('import', null, ['placeholder' => 'Please enter your import text','class' => 'form-control editor
    m-input','id'=>'Import']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('Export', 'Export') !!}
    {!! Form::textarea('export', null, ['placeholder' => 'Please enter your export text','class' => 'form-control editor
    m-input','id'=>'Export']) !!}
</div>

<!-- tinyMCE script config -->
<script>
    var editor_config = {
    path_absolute : "/",
    selector: "textarea.editor",
    plugins: ["template"],
    toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
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
    }
  };

  tinymce.init(editor_config);
</script>
<script>
    {!! \File::get(base_path('vendor/barryvdh/laravel-elfinder/resources/assets/js/standalonepopup.min.js')) !!}
</script>
<!-- end tinyMCE scripts config -->