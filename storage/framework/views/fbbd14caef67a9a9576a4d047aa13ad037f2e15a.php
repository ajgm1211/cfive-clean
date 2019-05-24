<?php $__env->startSection('css'); ?>
    ##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
    <link href="/css/quote.css" rel="stylesheet" type="text/css" />

    <link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title', 'Quotes'); ?>
<?php $__env->startSection('content'); ?>
    <br>
    <div class="m-content">
        <?php if(Session::has('message.nivel')): ?>
            <div class="col-md-12">
                <br>
                <div class="m-alert m-alert--icon m-alert--outline alert alert-<?php echo e(session('message.nivel')); ?> alert-dismissible fade show" role="alert">
                    <div class="m-alert__icon">
                        <i class="la la-warning"></i>
                    </div>
                    <div class="m-alert__text">
                        <strong>
                            <?php echo e(session('message.title')); ?>

                        </strong>
                        <?php echo e(session('message.content')); ?>

                    </div>
                    <div class="m-alert__close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php echo $__env->make('quotesv2.partials.show_head', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <!-- Charges -->
        <?php if($quote->type=='FCL'): ?>
            <?php echo $__env->make('quotesv2.partials.ratesByContainer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('quotesv2.partials.ratesByPackage', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>

        <!-- Payments and terms conditions -->
        <?php echo $__env->make('quotesv2.partials.payments', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <!-- Terms and conditions -->
        <?php echo $__env->make('quotesv2.partials.terms', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <!-- PDF Layout -->
        <?php echo $__env->make('quotesv2.partials.pdf_layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>
    <?php echo $__env->make('quotesv2.partials.sendQuoteModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    ##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
    <script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/quotes-v2.js')); ?>" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/tinymce/jquery.tinymce.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/tinymce/tinymce.min.js')); ?>"></script>
    <script type="text/javascript">

        var editor_config = {
            path_absolute : "/",
            selector: "textarea.editor",
            plugins: ["template"],
            toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
            external_plugins: { "nanospell": "<?php echo e(asset('js/tinymce/plugins/nanospell/plugin.js')); ?>" },
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>