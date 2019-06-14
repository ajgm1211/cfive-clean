<?php $__env->startSection('title', 'Show term & condition'); ?>
<?php $__env->startSection('content'); ?>
<div class="m-portlet">
    <!--begin::Form-->
    <?php echo Form::model($term, ['route' => ['terms.update', $term], 'method' => 'PUT']); ?>

    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                <div class="form-group m-form__group">
                    <?php echo Form::label('Name', 'Name'); ?>

                    <?php echo Form::text('name', null, ['placeholder' => 'Please enter the term name','class' => 'form-control m-input','disabled' => 'true']); ?>


                </div>

                <div class="form-group m-form__group">
                    <?php echo Form::label('Port', 'Ports'); ?>

                    <?php echo Form::select('ports[]',$harbors,@$selected_harbors, 
                    ['class' => 'm-select2-general form-control', 'multiple' => 'multiple','disabled' => 'true']); ?>

                </div>
                
                <div class="form-group m-form__group">
                    <?php echo Form::label('Carrier', 'Carriers'); ?>

                    <?php echo Form::select('carriers[]',$carriers,@$selected_carriers, 
                    ['class' => 'm-select2-general form-control', 'multiple' => 'multiple','disabled' => 'true']); ?>

                </div>
                
                <div class="form-group m-form__group">
                    <?php echo Form::label('Language', 'Language'); ?>

                    <?php echo Form::select('language',$languages,$term['language_id'], 
                    ['class' => 'm-select2-general form-control','disabled' => 'true']); ?>

                </div>

                <div class="form-group m-form__group">
                    <?php echo Form::label('Import', 'Import terms'); ?>

                    <div class="jumbotron"><?php echo $term->import; ?></div>
                </div>

                <div class="form-group m-form__group">
                    <?php echo Form::label('Export', 'Export terms'); ?>

                    <div class="jumbotron"><?php echo $term->export; ?></div>
                </div>
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                <a class="btn btn-danger" href="<?php echo e(url()->previous()); ?>">
                    Go back
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