<?php $__env->startSection('title', 'Companies | Contacts'); ?>
<?php $__env->startSection('css'); ?>
    ##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
    <link rel="stylesheet" type="text/css" href="/assets/plugins/button-dropdown/css/bootstrap.css">
    <script src="/assets/plugins/button-dropdown/js/jquery3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="/assets/plugins/button-dropdown/js/bootstrap.js"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
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
            <div class="m-portlet__body">
                <!--begin: Search Form -->
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-4">
                                    <div class="m-input-icon m-input-icon--left">
                                        <input type="text" class="form-control m-input" placeholder="Search..." id="generalSearch">
                                        <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                            <a href="<?php echo e(route('create.passport.client')); ?>" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                            <span>
                                <span>
                                    Add Password Grant Client
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                            </a>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
                </div>
                <table class="m-datatable">
                    <thead>
                    <tr>
                        <th title="Client id">
                            Client id
                        </th>
                        <th title="Name">
                            Name
                        </th>
                        <th title="Company user">
                            Company
                        </th>
                        <th title="Secret">
                            Secret
                        </th>
                        <th title="Created at">
                            Created at
                        </th>
                        <th title="Options">
                            Options
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $tokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($token->id); ?></td>
                            <td><?php echo e($token->name); ?></td>
                            <td><?php echo e($token->company_user['name']); ?></td>
                            <td><?php echo e($token->secret); ?></td>
                            <td><?php echo e($token->created_at); ?></td>
                            <td>
                                <button id="delete-token" data-token-id="<?php echo e($token->id); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
                                    <i class="la la-eraser"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    ##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
    <script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/oauth.js')); ?>" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-oauth.js" type="text/javascript"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>