<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 07:15 PM
 */
?>


<!--begin::Form-->
<?php echo Form::open(['route' => 'companies.store','class' => 'form-group m-form__group','enctype'=>'multipart/form-data']); ?>

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        <?php echo $__env->make('companies.partials.form_add_companies', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <div class="form-group m-form__group">
            <?php echo Form::label('price_id', 'Price Level'); ?><br>
            <?php echo e(Form::select('price_id[]',$prices,null,['class'=>'custom-select form-control','id' => 'price_level_company','multiple'=>'true'])); ?>

        </div>
        <div class="form-group m-form__group">
            <?php echo Form::label('users_id', 'Associate User'); ?><br>
            <?php echo e(Form::select('users[]',$users,null,['class'=>'custom-select form-control','id' => 'users_company','multiple'=>'true'])); ?>

        </div>
        <div class="form-group m-form__group">
            <?php echo Form::label('logo', 'Logo (Max size 1 mb)'); ?>

            <br>
            <?php echo Form::file('logo', null, ['placeholder' => 'Please upload a logo','class' => 'form-control m-input logo_input','required' => 'required','id'=>'logo']); ?>

            <div id="logo-error" class="hide"><b>Image size can not be bigger than 1 mb</b></div>
        </div>        
    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        <?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

    </div>
</div>
<?php echo Form::close(); ?>

<!--end::Form-->
<script>
    $('#price_level_company').select2({
        placeholder: "Select an option"
    });
    $('#users_company').select2({
        placeholder: "Select an option"
    });
    $('#pdf_language').select2({
        placeholder: "Select an option"
    });
</script>
<script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="<?php echo e(asset('js/companies.js')); ?>"></script>

