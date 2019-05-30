<?php $__env->startSection('css'); ?>
##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'Global Charges'); ?>
<?php $__env->startSection('content'); ?>



<div class="m-content">
  <div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
          <h3 class="m-portlet__head-text">
            Global Charges LCL
          </h3>
        </div>
      </div>
    </div>
    <div class="m-portlet m-portlet--tabs">
      <div class="m-portlet__head">
        <div class="m-portlet__head-tools">
          <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
            <li class="nav-item m-tabs__item">
              <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                <i class="la la-cog"></i>
                List Global Charge LCL
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="m-portlet__body">
        <div class="tab-content">
          <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">


            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
              <div class="row align-items-center">

                <div class="new col-xl-12 order-1 order-xl-2 m--align-right">

                  <div class="m-separator m-separator--dashed d-xl-none"></div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-2">
                  <a  id="newmodal" class="">
                    <button id="new" type="button"  onclick="AbrirModal('addGlobalCharge',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                      Add New
                      <i class="fa fa-plus"></i>
                    </button>
                  </a>
                </div>

              </div>
            </div>
            <table class="table tableData" id="global-table" width="100%" >
              <thead>
                <tr>
                  <th title="Field #1">
                    Type
                  </th>
                  <th title="Field #2">
                    Origin Port
                  </th>
                  <th title="Field #2">
                    Destination Port
                  </th>
                  <th title="Field #3">
                    Charge Type
                  </th>
                  <th title="Field #4">
                    Carrier
                  </th>
                  <th title="Field #7">
                    Calculation type
                  </th>

                  <th title="Field #8">
                    Amount
                  </th>
                  <th title="Field #8">
                    Minimum
                  </th>
                  <th title="Field #9">
                    Currency
                  </th>
                  <th title="Field #10">
                    Validity
                  </th>
                  <th title="Field #11">
                    Options
                  </th>
                </tr>
              </thead>
              <tbody>

                <?php $__currentLoopData = $global; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $globalcharges): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr id='tr_l<?php echo e(++$loop->index); ?>'>
                  <td>
                    <div id="divtype<?php echo e($loop->index); ?>"  class="val"><?php echo $globalcharges->surcharge->name; ?></div>
                  </td>
                  <td>
                    <?php if(!$globalcharges->globalcharportlcl->isEmpty()): ?>
                    <div id="divport<?php echo e($loop->index); ?>"  class="val">
                      <?php echo str_replace(["[","]","\""], ' ', $globalcharges->globalcharportlcl->pluck('portOrig')->unique()->pluck('display_name') ); ?> 
                    </div>
                    <?php endif; ?>
                    <?php if(!$globalcharges->globalcharcountrylcl->isEmpty()): ?>
                    <div id="divcountry<?php echo e($loop->index); ?>"  class="val">
                      <?php echo str_replace(["[","]","\""], ' ', $globalcharges->globalcharcountrylcl->pluck('countryOrig')->unique()->pluck('name') ); ?> 
                    </div>
                    <?php endif; ?>
                  </td>
                  <td>

                    <?php if(!$globalcharges->globalcharportlcl->isEmpty()): ?>
                    <div id="divportDest<?php echo e($loop->index); ?>"  class="val">
                      <?php echo str_replace(["[","]","\""], ' ', $globalcharges->globalcharportlcl->pluck('portDest')->unique()->pluck('display_name') ); ?> 
                    </div>
                    <?php endif; ?>
                    <?php if(!$globalcharges->globalcharcountrylcl->isEmpty()): ?>
                    <div id="divcountryDest<?php echo e($loop->index); ?>"  class="val">
                      <?php echo str_replace(["[","]","\""], ' ', $globalcharges->globalcharcountrylcl->pluck('countryDest')->unique()->pluck('name') ); ?> 
                    </div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div id="divchangetype<?php echo e($loop->index); ?>"  class="val"><?php echo $globalcharges->typedestiny->description; ?></div>
                  </td>
                  <td>
                    <div id="divcarrier<?php echo e($loop->index); ?>"  class="val">
                      <?php echo str_replace(["[","]","\""], ' ', $globalcharges->globalcharcarrierslcl->pluck('carrier')->pluck('name') ); ?>

                    </div>
                  </td>
                  <td>   
                    <div id="divcalculation<?php echo e($loop->index); ?>"  class="val"><?php echo $globalcharges->calculationtypelcl->name; ?></div>

                  <td> 
                    <div id="divammount<?php echo e($loop->index); ?>" class="val"> <?php echo $globalcharges->ammount; ?> </div>
                  </td>
                  <td> 
                    <div id="divminimum<?php echo e($loop->index); ?>" class="val"> <?php echo $globalcharges->minimum; ?> </div>
                  </td>
                  <td>
                    <div id="divcurrency<?php echo e($loop->index); ?>"  class="val"> <?php echo $globalcharges->currency->alphacode; ?> </div>
                  </td>
                  <td>
                    <div id="divvalidity<?php echo e($loop->index); ?>"  class="val"> <?php echo $globalcharges->validity; ?> / <?php echo $globalcharges->expire; ?></div>
                  </td>
                  <td>
                    <a  id='edit_l<?php echo e($loop->index); ?>' onclick="AbrirModal('editGlobalCharge',<?php echo e($globalcharges->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                      <i class="la la-edit"></i>
                    </a>    
                    <a  id='remove_l<?php echo e($loop->index); ?>'  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
                      <i id='rm_l<?php echo e($globalcharges->id); ?>' class="la la-times-circle"></i>
                    </a>
                    <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='Duplicate '  onclick='AbrirModal("duplicateGlobalCharge",<?php echo e($globalcharges->id); ?>)'>
                      <i class='la la-plus'></i>
                    </a>

                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="modalGlobalchargeAdd" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">
            Add Global Charges
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

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##



<script type="text/javascript" charset="utf8" src="/assets/datatable/jquery.dataTables.js"></script>
<script>
  $(document).ready( function () {
    $('#global-table').DataTable();
  } );
</script>
<script>
  function AbrirModal(action,id){


    if(action == "editGlobalCharge"){
      var url = '<?php echo e(route("edit-global-charge-lcl", ":id")); ?>';
      url = url.replace(':id', id);
      $('.modal-body').load(url,function(){
        $('#modalGlobalcharge').modal({show:true});
      });

    }
    if(action == "addGlobalCharge"){
      var url = '<?php echo e(route("add-global-charge-lcl")); ?>';

      $('.modal-body-add').load(url,function(){
        $('#modalGlobalchargeAdd').modal({show:true});
      });

    }
    if(action == "duplicateGlobalCharge"){

      var url = '<?php echo e(route("duplicate-global-charge-lcl", ":id")); ?>';
      url = url.replace(':id', id);
      $('.modal-body-add').load(url,function(){
        $('#modalGlobalchargeAdd').modal({show:true});
      });
    }
  }

</script>
<script src="/js/globalchargeslcl.js"></script>
<?php if(session('globalchar')): ?>
<script>
  swal(
    'Done!',
    'GlobalCharge updated.',
    'success'
  )
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>