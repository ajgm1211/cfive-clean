<?php $__env->startSection('css'); ?>
##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title','Importation LCL '.$contract['id'].' - '.$contract['number'].' / '.$contract['name']); ?>
<?php $__env->startSection('content'); ?>

<div class="m-content">

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

    <!--Begin::Main Portlet-->
    <div class="m-portlet m-portlet--full-height">
        <!--begin: Portlet Head-->
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Importation New Contract LCL
                        <!--<small>
new registration
</small>-->
                    </h3>
                </div>
            </div>


            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="#" data-toggle="m-tooltip" class="m-portlet__nav-link m-portlet__nav-link--icon" data-direction="left" data-width="auto" title="Get help with filling up this form">
                            <i class="flaticon-info m--icon-font-size-lg3"></i> 
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php if($type == 1): ?>
        <?php echo Form::open(['route'=>'ImportationLCL.create','method'=>'get']); ?> <!-- W/M -->
        <?php elseif($type == 2): ?>
        <?php echo Form::open(['route'=>'process.contract.fcl.Rat.Surch','method'=>'get']); ?> <!-- W/M + Surchargers -->
        <input type="hidden" name="statustypecurren" value="<?php echo e($statustypecurren); ?>">
        <?php endif; ?>
        <div class="m-portlet__body">
            <div class="tab-content">
                <div class="tab-pane active" id="m_portlet_tab_1_1">
                    <div class="row">
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-lg-12">
                            <div class="form-group m-form__group row">

                                <div class="col-lg-2">
                                    <label class="col-form-labe"><b>CONTRACT:</b></label>
                                </div>
                                <?php echo Form::hidden('Contract_id',$value['Contract_id']); ?>

                                <?php echo Form::hidden('FileName',$value['fileName']); ?>

                                <div class="col-lg-3">
                                    <label for="nameid" class="">Contract Name</label>
                                    <?php echo Form::text('name',$value['name'],['id'=>'nameid',
                                    'placeholder'=>'Contract Name',
                                    'required',
                                    'class'=>'form-control m-input',
                                    'disabled'
                                    ]); ?>

                                </div>
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Validation</label>
                                    <input placeholder="Contract Validity" class="form-control m-input" readonly="" id="m_daterangepicker_1" required="required" name="validation_expire" type="text" value="<?php echo e($value['validatiion']); ?>" disabled>
                                </div>
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Name of File</label>
                                    <?php echo Form::text('filename',$value['fileName'],['id'=>'fileName',
                                    'placeholder'=>'File Name Contract',
                                    'required',
                                    'disabled',
                                    'class'=>'form-control m-input']); ?>

                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-3">
                                    <label for="validation_expire" class=" ">Contract Comments</label>
                                    <?php echo Form::text('comments',$value['comments'],['id'=>'comments',
                                    'placeholder'=>'Contract comments',
                                    'required',
                                    'disabled',
                                    'class'=>'form-control m-input']); ?>

                                </div>
                                <div class="col-lg-3">
                                    <label class="">Carrier</label>
                                    <div class="" id="carrierMul">
                                        <?php echo Form::select('carrierM[]',$carrier,$contract->carriers->pluck('carrier_id'),['class'=>'m-select2-general form-control','id'=>'carrierM','disabled','multiple'=>'multiple']); ?>

                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="">Direction</label>
                                    <div class="" id="direction">
                                        <?php echo Form::select('direction',$direction,$contract['direction_id'],['class'=>'m-select2-general form-control','disabled','id'=>'direction']); ?>

                                    </div>
                                </div>

                            </div>

                            <hr>

                            <div class="form-group m-form__group row">
                                <div class="col-lg-2">
                                    <label class="col-form-label"><b>DATA:</b></label>
                                </div>
                                <?php if($value['existorigin'] == true): ?>
                                <div class="col-2 col-form-label">
                                    <label for="origin" class=" ">Origin</label>
                                    <?php echo Form::select('origin[]',$harbor,$value['origin'],['class'=>'m-select2-general form-control  ','id'=>'origin','multiple'=>'multiple']); ?>                            
                                </div>
                                <?php endif; ?>

                                <input type="hidden" name="existorigin" id="existorigin" value="<?php echo e($value['existorigin']); ?>" />

                                <?php if($value['existdestiny'] == true): ?>
                                <div class="col-2 col-form-label">
                                    <label for="destiny" class=" ">Destiny</label>
                                    <?php echo Form::select('destiny[]',$harbor,$value['destiny'],['class'=>'m-select2-general form-control  ','id'=>'destiny','multiple'=>'multiple']); ?>

                                </div>
                                <?php endif; ?>

                                <input type="hidden" name="existdestiny" id="existdestiny" value="<?php echo e($value['existdestiny']); ?>" />

                                <?php if($value['existcarrier'] == true): ?>
                                <div class="col-2 col-form-label">
                                    <label for="carrier" class=" ">Carrier</label>
                                    <?php echo Form::select('carrier',$carrier,$value['carrier'],['class'=>'m-select2-general form-control','id'=>'carrier']); ?>

                                </div>
                                <?php endif; ?>

                                <?php if($type == 2): ?>
                                <div class="col-2 col-form-label">
                                    <label for="Charge" class=" ">Charge</label>
                                    <?php echo Form::text('chargeVal',null,['id'=>'chargeVal',
                                    'placeholder'=>'References to Rate',
                                    'required',
                                    'class'=>'form-control m-input',
                                    'onkeyup' => 'javascript:this.value=this.value.toUpperCase();']); ?>

                                </div>
                                <?php endif; ?>

                                <input type="hidden" name="existcarrier" id="existcarrier" value="<?php echo e($value['existcarrier']); ?>" />
                                <input type="hidden" name="statustypecurren" id="existcarrier" value="<?php echo e($statustypecurren); ?>" />
                                <input type="hidden" name="scheduleinfo" id="scheduleinfo" value="<?php echo e($value['scheduleinfo']); ?>" />

                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group m-form__group row"></div>
                        <div class="form-group m-form__group row">
                            <?php $__currentLoopData = $targetsArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $targets): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4">
                                <div class="m-portlet m-portlet--metal m-portlet--head-solid-bg m-portlet--bordered">
                                    <div class="m-portlet__head">
                                        <div class="m-portlet__head-caption">
                                            <div class="m-portlet__head-title">
                                                <h3 class="m-portlet__head-text">
                                                    <?php echo e($targets); ?>

                                                    <!--<small>portlet sub title</small>-->
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-portlet__body">
                                        <div class="col-md-12">
                                            <label for="" class="">Column  in the file excel</label>
                                        </div>
                                        <div class="col-md-12">
                                            <?php echo Form::select($targets,$coordenates,null,['class' => 'm-select2-general form-control', 'id' => 'select'.$loop->iteration, 'onchange'=>'equals('.$loop->iteration.')']); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <input type="hidden" name="countTarges" id="countTarges" value="<?php echo e($countTarges); ?>" />
                        <input type="hidden" name="CompanyUserId" id="CompanyUserId" value="<?php echo e($CompanyUserId); ?>" />
                    </div>
                    <div class="form-group m-form__group row">

                        <div class="col-lg-5 col-lg-offset-5"> </div>
                        <div class="col-lg-2 col-lg-offset-2">
                            <button type="submit" id="processid" class="btn btn-primary form-control">
                                Process
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php echo Form::close(); ?>

    <!--end: Form Wizard-->
</div>
<!--End::Main Portlet-->

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="<?php echo e(asset('js/Contracts/processFlcContract.js')); ?>" type="application/javascript"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>