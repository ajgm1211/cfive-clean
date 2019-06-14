<?php $__env->startSection('title', 'Add new term & condition'); ?>
<?php $__env->startSection('content'); ?>
<div class="m-portlet">
    <!--begin::Form-->
    <?php echo Form::open(['route' => 'terms.store']); ?>

    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                <?php echo $__env->make('terms.partials.form_terms', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                <?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

                <a class="btn btn-danger" href="<?php echo e(url()->previous()); ?>">
                    Cancel
                </a>
            </div>
        </div>
    </div>
    <?php echo Form::close(); ?>

    <!--end::Form-->
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
<script>
   $('.m-select2-general').select2({
       placeholder: "Select an option"
   });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>