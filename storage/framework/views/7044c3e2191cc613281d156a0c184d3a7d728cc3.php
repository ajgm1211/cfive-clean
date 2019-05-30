<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 23/04/2018
 * Time: 07:15 PM
 */
?>

<!--begin::Form-->
<?php echo Form::open(['route' => 'contacts.store','class' => 'form-group m-form__group']); ?>

<div class="m-portlet__body">
    <div class="m-form__section m-form__section--first">
        <div class="form-group m-form__group">
            <?php echo $__env->make('contacts.partials.form_add_contacts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php if(isset($company_id) && $company_id!=''): ?>
            <div class="form-group m-form__group">
                <?php echo Form::hidden('company_id', $company_id, ['placeholder' => 'Please enter a position','class' => 'form-control m-input phonec_input']); ?>

            </div>
            <?php else: ?>
            <div class="form-group m-form__group">
                <?php echo Form::label('company_id', 'Company'); ?><span style="color:red">*</span><br>
                <?php echo e(Form::select('company_id',$companies,null,['placeholder' => 'Please choose a company','class'=>'custom-select form-control','id' => 'm_select2_2_modal','required'=>'required'])); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
            <?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

        </div>
    </div>
</div>
<?php echo Form::close(); ?>

<!--end::Form-->
<script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>

