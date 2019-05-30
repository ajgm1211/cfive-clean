
<!--begin::Form-->
<?php echo Form::model($surcharges, ['route' => ['surcharges.update', $surcharges], 'method' => 'PUT']); ?>


<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        <?php echo $__env->make('surcharges.partials.form_surcharges', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <div class="form-group m-form__group">
            <?php echo Form::label('sale_term_id', 'Sale Terms'); ?><br>
            <?php echo e(Form::select('sale_term_id',$sale_terms,$surcharges->sale_term_id,['class'=>'custom-select form-control','id' => 'sale_term_id','placeholder'=>'Select an option'])); ?>

        </div>                
    </div>
</div>
<div class="m-form__actions m-form__actions">
    <?php echo Form::submit('Update', ['class'=> 'btn btn-primary']); ?>

</div>
<br>
<?php echo Form::close(); ?>

<!--end::Form-->
<script type="text/javascript">
    $('#sale_term_id').select2({
        placeholder: "Select an option"
    });
</script>