<?php $__env->startSection('title', 'Manager Carriers'); ?>
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
                        Manager Carriers
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
                            <a href="#" onclick="showModal(1,'2')">

                                <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                    <span>
                                        <span>
                                            Add &nbsp; &nbsp;
                                        </span>
                                        <i class="la la-ship"></i>
                                    </span>
                                </button>
                            </a>
                            <a href="<?php echo e(route('synchronous.carrier')); ?>" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                                <span>
                                    <span>
                                        Synchronous &nbsp; &nbsp;
                                    </span>
                                    <i class="la la-refresh"></i>
                                </span>
                            </a>
                        </div>

                        <br />
                        <table class="table m-table m-table--head-separator-primary"  id="carriertable" width="100%" style="width:100%">
                            <thead >
                                <tr>
                                    <th >ID</th>
                                    <th >Name</th>
                                    <th >Picture</th>
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

<div class="modal fade bd-example-modal-lg" id="modaleditCarrier"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Carriers
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div id="modal-body" class="modal-body">



            </div>
        </div>
    </div>

    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('js'); ?>
    ##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script>

        function showModal(id,operation){

            if(operation == 1){
                var url = '<?php echo e(route("managercarriers.edit", ":id")); ?>';
                url = url.replace(':id', id);
                $('#modal-body').load(url,function(){
                    $('#modaleditCarrier').modal();
                });
            } else if(operation == 2){
                var url = '<?php echo e(route("managercarriers.show",":id")); ?>';
                url = url.replace(':id', id);
                $('#modal-body').load(url,function(){
                    $('#modaleditCarrier').modal();
                });
            }
        }

        $(function() {
            $('#carriertable').DataTable({
                processing: true,
                //serverSide: true,
                ajax: '<?php echo route("managercarriers.create"); ?>',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'image', name: 'image' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                "order": [[0, 'asc']],
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

        $(document).on('click','.delete-carrier',function(){
            var id = $(this).attr('data-id-carrier');
            var elemento = $(this);
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then(function(result){
                if (result.value) {
                    var token = $("meta[name='csrf-token']").attr("content");
                    url='<?php echo route("managercarriers.destroy",":id"); ?>';
                    url = url.replace(':id', id);
                    // $(this).closest('tr').remove();
                    $.ajax({
                        url:url,
                        method:'DELETE',
                        data:{"id":id,
                              "_token":token},
                        success: function(data){
                            if(data.success == 1){
                                swal(
                                    'Deleted!',
                                    'Your Carrier has been deleted.',
                                    'success'
                                )
                                //$(elemento).closest('tr').remove();
                                $('#carriertable').DataTable().ajax.reload();
                            }else if(data == 2){
                                swal("Error!", "an internal error occurred!", "error");
                            }
                            //alert(data.success);
                        }
                    });
                } else if (result.dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your Carrier is safe :)',
                        'error'
                    )
                }
            });
        });

    </script>

    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>