<?php $__env->startSection('title', 'Companies | List'); ?>
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
                    <!--<div class="col-xl-4 order-1 order-xl-2 m--align-right">
<button type="button" dusk="addCompany" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
<span>
<i class="la la-user"></i>
<span>
Add Company
</span>
<i class="la la-plus"></i>
</span>
</button>
<div class="m-separator m-separator--dashed d-xl-none"></div>
</div>-->
                </div>
            </div>
            <table class="m-datatable text-center" id="html_table" >
                <thead>
                    <tr>
                        <th title="Field #1">
                            Name
                        </th>
                        <th title="Field #2">
                            Address
                        </th>
                        <th title="Field #3">
                            Phone
                        </th>
                        <th title="Field #4">
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($company->name); ?></td>
                        <td><?php echo e($company->address); ?></td>
                        <td><?php echo e($company->phone); ?></td>
                        <td>
                            <button id="delete-company-user" data-company-id="<?php echo e($company->id); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete">
                                <i class="la la-eraser"></i>
                            </button>
                            <button data-toggle="modal" data-target="#companyUserModal<?php echo e($company->id); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Duplicate">
                                <i class="la la-plus"></i>
                            </button>
                            <!--<a href="<?php echo e(route('settings.duplicate',setearRouteKey($company->id))); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill">
                                <i class="la la-plus"></i>
                            </a>-->
                        </td>
                    </tr>
                    <?php echo $__env->make('settings.partials.companyUserModal', ['company_user_id' => $company->id], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php echo $__env->make('companies.partials.companiesModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('companies.partials.deleteCompaniesModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
<script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
<script>
    function AbrirModal(action,id){
        if(action == "duplicate"){
            var url = '<?php echo e(route("settings.duplicate", ":id")); ?>';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#companyModal').modal({show:true});
            });
        }if(action == "add"){
            var url = '<?php echo e(route("companies.add")); ?>';
            $('.modal-body').load(url,function(){
                $('#companyModal').modal({show:true});
            });
        }
        if(action == "delete"){
            var url = '<?php echo e(route("companies.delete", ":id")); ?>';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#deleteCompanyModal').modal({show:true});
            });
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>