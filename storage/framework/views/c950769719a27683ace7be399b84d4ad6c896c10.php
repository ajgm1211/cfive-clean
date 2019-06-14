<?php $__env->startSection('title', 'Users'); ?>
<?php $__env->startSection('content'); ?>
    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            List  Users
                        </h3>
                    </div>
                </div>
            </div>

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
                                    <div class="m-form__group m-form__group--inline">
                                        <div class="m-form__label">
                                            <label class="m-label m-label--single">
                                                Rol:
                                            </label>
                                        </div>
                                        <div class="m-form__control">
                                            <select class="form-control m-bootstrap-select" id="m_form_type">
                                                <option value="">
                                                    All
                                                </option>
                                                <option value="1">
                                                    Admin
                                                </option>
                                                <option value="2">
                                                    Company
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-md-none m--margin-bottom-10"></div>
                                </div>
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

                            <button id="add" type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                            <span>
                                <i class="la la-user"></i>
                                <span>
                                    Add User
                                </span>
                            </span>
                            </button>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
                </div>
                <table class="m-datatable" id="html_table" width="100%">
                    <thead>
                    <tr>
                        <th title="Field #1">
                            Name
                        </th>
                        <th title="Field #2">
                            Last Name
                        </th>
                        <th title="Field #3">
                            Email
                        </th>
                        <th title="Field #4">
                            Type
                        </th>
                        <th title="Field #5">
                            Company Name
                        </th>
                        <?php if(auth()->check() && auth()->user()->hasRole('administrator')): ?>
                        <th title="Field #6">
                            Last Login
                        </th>
                        <?php endif; ?>
                        <th title="Field #7">
                            Status
                        </th>
                        <th title="Field #8">
                            Options
                        </th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php $__currentLoopData = $arreglo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <tr>
                            <td><?php echo e($arr->name); ?></td>
                            <td><?php echo e($arr->lastname); ?></td>
                            <td><?php echo e($arr->email); ?></td>
                            <td><?php if($arr->type == "admin"): ?>
                                    1
                                <?php elseif($arr->type == "company"): ?>
                                    2
                                <?php else: ?>
                                    3
                                <?php endif; ?>
                            </td>
                            <td><?php if($arr->companyUser): ?><?php echo e($arr->companyUser->name); ?><?php endif; ?></td>
                            <?php if(auth()->check() && auth()->user()->hasRole('administrator')): ?>
                            <td>
                                <?php echo e($arr->last_login); ?>

                            </td>
                            <?php endif; ?>
                            <td><?php if($arr->state==1): ?> Active <?php else: ?> Inactive <?php endif; ?></td>
                            <?php if(Auth::user()->type != 'subuser'): ?>
                                <td>
                                    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  onclick="AbrirModal('edit',<?php echo e($arr->id); ?>)" title="Edit ">
                                        <i class="la la-edit"></i>
                                    </a>
                                    <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="AbrirModal('delete',<?php echo e($arr->id); ?>)" >
                                        <i class="la la-eraser"></i>
                                    </a>
                                    <?php if( (Auth::user()->type == 'company') ||  (Auth::user()->type == 'admin')  ): ?>
                                        <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Reset " onclick="AbrirModal('reset',<?php echo e($arr->id); ?>)"  >
                                            <i class="la la-key"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('users.activate',$arr->id)); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Enable/Disable User" >
                                        <i class="la la-user"></i>
                                    </a>
                                    <?php if(auth()->check() && auth()->user()->hasRole('administrator')): ?>
                                    <a href="<?php echo e(route('impersonate.impersonate',$arr->id)); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Impersonate User" >
                                        <i class="la la-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            <?php else: ?>
                                <td>-</td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="modal fade" id="m_modal_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">
                                    User
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                                </button>
                            </div>
                            <div class="modal-body">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    ##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
    <script src="/assets/demo/default/custom/components/datatables/base/html-table.js" type="text/javascript"></script>
    <script>

        function AbrirModal(action,id){

            if(action == "edit"){
                var url = '<?php echo e(route("users.edit", ":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });
            }if(action == "add"){
                var url = '<?php echo e(route("users.add")); ?>';
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });
            }
            if(action == "delete"){
                var url = '<?php echo e(route("users.msg", ":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });

            }
            if(action == "reset"){
                var url = '<?php echo e(route("users.msgreset", ":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#m_modal_5').modal({show:true});
                });

            }

        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>