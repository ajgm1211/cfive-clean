<!-- En caso de ser Subusuario precargamos el combo de editar con el valor correspondiente -->
<?php if(isset($datosSubuser)): ?>
<?php echo e($valorSelect = $datosSubuser->id); ?>

<?php else: ?>
<?php echo e($valorSelect = ''); ?>

<?php endif; ?>
<!--begin::Form-->
<?php echo Form::open(['route' => 'users.store']); ?>


<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        <?php echo $__env->make('users.partials.form_users', array('type'=>'add'), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        <?php echo Form::submit('Save', ['class'=> 'btn btn-success']); ?>

    </div>
    <br>
</div>
<?php echo Form::close(); ?>

<!--end::Form-->

<script src="/js/users.js"></script>
