<?php $__env->startSection('title', 'Price Levels'); ?>
<?php $__env->startSection('content'); ?>
    <div class="m-content">
        <div class="m-portlet m-portlet--mobile">
            <!--<div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            Price levels
                        </h3>
                    </div>
                </div>
            </div>-->
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
                                <!--<div class="col-md-4">
                                    <div class="m-form__group m-form__group--inline">
                                        <div class="m-form__label">
                                            <label class="m-label m-label--single">
                                                Status:
                                            </label>
                                        </div>
                                        <div class="m-form__control">
                                            <select class="form-control m-bootstrap-select" id="m_form_type">
                                                <option value="">
                                                    All
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-md-none m--margin-bottom-10"></div>
                                </div>-->
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

                            <a href="<?php echo e(route('prices.add')); ?>" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                <span>
                                    <span>
                                        Add Price Level
                                    </span>
                                    <i class="la la-plus"></i>
                                </span>
                            </a>
                            <div class="m-separator m-separator--dashed d-xl-none"></div>
                        </div>
                    </div>
                </div>
                <table class="m-datatable text-center"  id="html_table" >
                    <thead>
                    <tr>
                        <th title="Field #1">
                            Name
                        </th>
                        <th title="Field #2">
                            Description
                        </th>
                        <th title="Field #3">
                            Companies
                        </th>
                        <!-- <th title="Field #4">
                             20'
                         </th>
                         <th title="Field #5">
                             40'
                         </th>
                         <th title="Field #6">
                             40' HC
                         </th>-->
                        <th title="Field #7">
                            Options
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $prices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($price->name); ?></td>
                            <td><?php echo e($price->description); ?></td>
                            <td>
                                <?php $__currentLoopData = $price->company_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <ul>
                                        <li><?php echo e($company->business_name); ?></li>
                                    </ul>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('prices.edit',setearRouteKey($price->id))); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                    <i class="la la-edit"></i>
                                </a>
                                <button id="delete-pricing" data-pricing-id="<?php echo e($price->id); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
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
    <?php echo $__env->make('prices.partials.pricesModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>;
    <?php echo $__env->make('prices.partials.deletePricesModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>;
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    ##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
    <script>
        function AbrirModal(action,id){
            if(action == "edit"){
                var url = '<?php echo e(route("prices.edit", ":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#priceModal').modal({show:true});
                });
            }if(action == "add"){
                var url = '<?php echo e(route("prices.add")); ?>';
                $('.modal-body').load(url,function(){
                    $('#priceModal').modal({show:true});
                });
            }
            if(action == "delete"){
                var url = '<?php echo e(route("prices.delete", ":id")); ?>';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#deletePriceModal').modal({show:true});
                });
            }
        }

        $(document).ready(function() {
            $('#select-2').select2();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>