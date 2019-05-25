<!-- Condiciones para mostrar o no los bloques que se necesitan -->
<?php if($user->type == 'admin'): ?>

<?php
$load = '1';
?>
<?php elseif($user->type == 'company'): ?>
<?php
$load = '2';
?>
<?php else: ?>
<?php
$load = '3';
?>
<?php endif; ?>


<!-- En caso de ser Subusuario precargamos el combo de editar con el valor correspondiente -->
<?php if(isset($datosSubuser)): ?>
<?php
$valorSelect = $datosSubuser->id;
?>
<?php else: ?>
<?php
$valorSelect = '';
?>
<?php endif; ?>

<!--begin::Form-->
<?php echo Form::model($user, ['route' => ['users.update', $user], 'method' => 'PUT']); ?>


<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        <?php echo $__env->make('users.partials.form_users', array('type'=>'edit'), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
</div>

<div class="m-form__actions m-form__actions">
    <?php echo Form::submit('Save', ['class'=> 'btn btn-success']); ?>

</div>
<br>

<?php echo Form::close(); ?>

<!--end::Form-->

<script src="/js/users.js"></script>
<script>change(<?php echo $load; ?>)</script>



