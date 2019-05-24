<?php $__env->startSection('title', 'Requests FCL'); ?>
<?php $__env->startSection('css'); ?>
##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/loadviewipmort.css')); ?>">
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Status Importation FCL
                    </h3>
                </div>
            </div>
        </div>
        <?php if(count($errors) > 0): ?>
        <div id="notificationError" class="alert alert-danger">
            <strong>Ocurrió un problema con tus datos de entrada</strong><br>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php if(Session::has('message.nivel')): ?>

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
        <?php endif; ?>

        <div class="m-portlet__body">
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1 conten_load">
                        <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                            <a href="<?php echo e(route('Request.importaion.fcl')); ?>">

                                 <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                 <span>
                                    <span>
                                       New Import Contract &nbsp;
                                    </span>
                                    <i class="la la-clipboard"></i>
                                 </span>
                                 </button>
                              </a>
                        </div>
                        
                        <br />
                        <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="100%" style="width:100%">
                            <thead >
                                <tr>
                                    <th >ID</th>
                                    <th >Contract Name</th>
                                    <th >Contract Number</th>
                                    <th >Contract Validation</th>
                                    <th >Date</th>
                                    <th >Status</th>
                                    <th >Options</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##

<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script>

    $(function() {
        $('#requesttable').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '<?php echo route("RequestImportation.listClient",$company_userid); ?>',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'number', name: 'number' },
                { data: 'validation', name: 'validation' },
                { data: 'date', name: 'date' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "order": [[0, 'des']],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "width": true,
            "info": true,
            "deferLoading": 57,
            "autoWidth": false,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true
        });

    });
    
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>