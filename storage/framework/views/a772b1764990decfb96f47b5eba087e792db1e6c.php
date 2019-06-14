<?php $__env->startSection('css'); ?>
##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'Failed GC '.$accounts['id'].' - '.$accounts['name']); ?>
<?php $__env->startSection('content'); ?>

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Global Charge
                    </h3><br>

                </div>
            </div>
        </div>

        <?php if(count($errors) > 0): ?>
        <div id="notificationError" class="alert alert-danger">
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

        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                        <?php if($tab): ?>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#FailSurcharge" role="tab">
                                <i class="la la-cog"></i>
                                Fail Global Charge 
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS " data-toggle="tab" href="#GoodSurcharge" role="tab">
                                <i class="la la-briefcase"></i>
                                Good Global Charge
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link " data-toggle="tab" href="#FailSurcharge" role="tab">
                                <i class="la la-cog"></i>
                                Fail Global Charge 
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link addS active" data-toggle="tab" href="#GoodSurcharge" role="tab">
                                <i class="la la-briefcase"></i>
                                Good Global Charge
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="tab-content">
                <?php if($tab): ?>
                <div class="tab-pane active" id="FailSurcharge" role="tabpanel">
                    <?php else: ?>
                    <div class="tab-pane " id="FailSurcharge" role="tabpanel">
                        <?php endif; ?>
                        <br>
                        <div class="m-portlet__head">
                            <label >
                                <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                                <strong >
                                    Failed Global Charge: 
                                </strong>
                                <strong id="strfail"><?php echo e($countfailglobal); ?></strong>
                                <input type="hidden" value="<?php echo e($countfailglobal); ?>" id="strfailinput" />
                            </label>
                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="<?php echo e(route('Reprocesar.globalcharge.fcl',$id)); ?>" class="btn btn-primary">Reprocess &nbsp;<span class="la la-refresh"></span></a>
                            <br>

                        </div>

                        <div class="m-portlet__body">
                            <!--begin: tab body -->

                            <table class="table tableData"  id="myatest" width="100%">
                                <thead width="100%">
                                    <tr>
                                        <th> Surcharge </th>
                                        <th> Origin </th>
                                        <th> Destiny </th>
                                        <th> Type Destiny </th>
                                        <th> Type Calculation </th>
                                        <th> Ammount </th>
                                        <th> Currency </th>
                                        <th> Carrier </th>
                                        <th> Validity To </th>
                                        <th> Validity From </th>
                                        <th> Options </th>
                                    </tr>
                                </thead>

                            </table>

                            <!--end: tab body -->

                        </div>
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center"></div>
                        </div>

                    </div>

                    <!-- /////////////////////////////////////////////////////////////////////////////////////////// -->
                    <?php if($tab): ?>
                    <div class="tab-pane " id="GoodSurcharge" role="tabpanel">
                        <?php else: ?>
                        <div class="tab-pane active" id="GoodSurcharge" role="tabpanel">
                            <?php endif; ?>
                            <br>
                            <div class="m-portlet__head">
                                <label>
                                    <i class="fa fa-dot-circle-o" style="color:green;"> </i>
                                    <strong id="">
                                        Good Global Charge: 
                                    </strong>
                                    <strong id="strgood">
                                        <?php echo e($countgoodglobal); ?>

                                    </strong>
                                    <input type="hidden" value="<?php echo e($countgoodglobal); ?>" id="strgoodinput" />
                                </label>
                            </div>

                            <div class="m-portlet__body">
                                <!--begin: tab body -->

                                <table class="table tableData"  id="myatest2" width="100%">
                                    <thead width="100%">
                                        <tr>
                                            <th> Surcharge </th>
                                            <th> Origin </th>
                                            <th> Destiny </th>
                                            <th> Type Destiny </th>
                                            <th> Type Calculation </th>
                                            <th> Ammount </th>
                                            <th> Currency </th>
                                            <th> Carrier </th>
                                            <th> Validity To </th>
                                            <th> Validity From </th>
                                            <th> Options </th>
                                        </tr>
                                    </thead>

                                </table>

                                <!--end: tab body -->
                            </div>
                            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                                <div class="row align-items-center"></div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <input type="hidden" value="<?php echo e($id); ?>" id="idcontract" />
        </div>

        <!--  begin modal editar edit -->
        <div class="modal fade bd-example-modal-lg" id="modalGlobalcharge"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Edit Global Charges
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>

                </div>
            </div>
        </div>


        <!--  begin modal editar add -->

        <div class="modal fade bd-example-modal-lg" id="modalGlobalchargeAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Global Charges
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>
                    </div>
                    <div class="modal-body-add">

                    </div>

                </div>
            </div>
        </div>

        <!--  end modal editar rate -->

        <?php $__env->stopSection(); ?>
        <?php $__env->startSection('js'); ?>
        ##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##


        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
        <script type="text/javascript" charset="utf8"  src="js/Contracts/RatesAndFailForContract.js"></script>
        <script>
            $(function() {
                $('#myatest').DataTable({
                    processing: true,
                    ajax: '<?php echo route("Fail.Load.globalcharge.fcl",[$id,1]); ?>',
                    columns: [
                        { data: 'surchargelb', name: 'surchargelb' },
                        { data: 'origin_portLb', name: 'origin_portLb' },
                        { data: 'destiny_portLb', name: 'destiny_portLb' },
                        { data: 'typedestinylb', name: "typedestinylb" },
                        { data: 'calculationtypelb', name: 'calculationtypelb' },
                        { data: 'ammount', name: "ammount" },
                        { data: 'currencylb', name: 'currencylb' },
                        { data: 'carrierlb', name: 'carrierlb' },
                        { data: 'validitytolb', name: 'validitytolb' },
                        { data: 'validityfromlb', name: 'validityfromlb' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "deferLoading": 57,
                    "autoWidth": true,
                    "processing": true,
                    "dom": 'Bfrtip',
                    "paging": true,
                    //"scrollX": true,
                });

                $('#myatest2').DataTable({
                    processing: true,
                    //serverSide: true,
                    ajax: '<?php echo route("Fail.Load.globalcharge.fcl",[$id,2]); ?>',
                    columns: [
                        { data: 'surchargelb', name: 'surchargelb' },
                        { data: 'origin_portLb', name: 'origin_portLb' },
                        { data: 'destiny_portLb', name: 'destiny_portLb' },
                        { data: 'typedestinylb', name: "typedestinylb" },
                        { data: 'calculationtypelb', name: 'calculationtypelb' },
                        { data: 'ammount', name: "ammount" },
                        { data: 'currencylb', name: 'currencylb' },
                        { data: 'carrierlb', name: 'carrierlb' },
                        { data: 'validitytolb', name: 'validitytolb' },
                        { data: 'validityfromlb', name: 'validityfromlb' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "deferLoading": 57,
                    "autoWidth": true,
                    "processing": true,
                    "dom": 'Bfrtip',
                    "paging": true,
                    //"scrollX": true,
                }); 
            });

            function showModalsavetoglobalcharge(id,operation){
                if(operation == 1){
                    var url = '<?php echo e(route("ImportationGlobalchargeFcl.edit", ":id")); ?>';
                    url = url.replace(':id', id);
                    $('.modal-body').load(url,function(){
                        $('#modalGlobalcharge').modal();
                    });
                }else if(operation == 2){
                    var url = '<?php echo e(route("edit.globalcharge.modal.fcl",":id")); ?>';
                    url = url.replace(':id', id);
                    $('.modal-body').load(url,function(){
                        $('#modalGlobalcharge').modal({show:true});
                    });
                }
            }

            $(document).on('click','#delete-Fail-global',function(){
                var id = $(this).attr('data-id-failglobal');
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

                        url='<?php echo route("Destroy.globalcharge.Fail.fcl",":id"); ?>';
                        url = url.replace(':id', id);
                        // $(this).closest('tr').remove();
                        $.ajax({
                            url:url,
                            method:'get',
                            success: function(data){
                                if(data == 1){
                                    swal(
                                        'Deleted!',
                                        'Your Surcharge has been deleted.',
                                        'success'
                                    )
                                    $(elemento).closest('tr').remove();
                                    var a = $('#strfailinput').val();
                                    a--;
                                    $('#strfail').text(a);
                                    $('#strfailinput').attr('value',a);
                                }else if(data == 2){
                                    swal("Error!", "An internal error occurred!", "error");
                                }
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your rate is safe :)',
                            'error'
                        )
                    }
                });
            });

            $(document).on('click','#delete-globalcharge',function(){
                var id = $(this).attr('data-id-globalcharge');
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

                        url='<?php echo route("Destroy.globalcharge.good.fcl",":id"); ?>';
                        url = url.replace(':id', id);
                        // $(this).closest('tr').remove();
                        $.ajax({
                            url:url,
                            method:'get',
                            success: function(data){
                                if(data == 1){
                                    swal(
                                        'Deleted!',
                                        'Your Surcharge has been deleted.',
                                        'success'
                                    )
                                    $(elemento).closest('tr').remove();
                                    var b = $('#strgoodinput').val();
                                    b--;
                                    $('#strgood').text(b);
                                    $('#strgoodinput').attr('value',b);
                                }else if(data == 2){
                                    swal("Error!", "an internal error occurred!", "error");
                                }
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your rate is safe :)',
                            'error'
                        )
                    }
                });
            });

        </script>

        <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>