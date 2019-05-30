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
        <!--<div class="m-portlet__head">
<div class="m-portlet__head-caption">
<div class="m-portlet__head-title">
<h3 class="m-portlet__head-text">
Contacts
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

                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" onclick="AbrirModal('add',0)">
                            <span>
                                <span>
                                    Add Contact
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                        </button>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>


                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Importation
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -136px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalupload">
                                <span>
                                    <i class="la la-upload"></i>
                                    &nbsp;
                                    Upload Contacts
                                </span>
                            </a>      
                            <a href="<?php echo e(route('DownLoad.Files',2)); ?>" class="dropdown-item" >
                                <span>
                                    <i class="la la-download"></i>
                                    &nbsp;
                                    Download File
                                </span>
                            </a>
                            <a href="<?php echo e(route('view.fail.contact')); ?>" class="dropdown-item" >
                                <span>
                                    <i class="la la-search"></i>
                                    &nbsp;
                                    Failed Contacts
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <table class="m-datatable"  id="html_table" >
                <thead>
                    <tr>
                        <th title="Field #1">
                            First Name
                        </th>
                        <th title="Field #2">
                            Last Name
                        </th>
                        <th title="Field #3">
                            Company
                        </th>
                        <th title="Field #4">
                            Email
                        </th>
                        <th title="Field #5">
                            Phone
                        </th>
                        <th title="Field #6">
                            Position
                        </th>                        
                        <th title="Field #7">
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($contact->first_name); ?></td>
                        <td><?php echo e($contact->last_name); ?></td>
                        <td><?php echo e($contact->company->business_name); ?></td>
                        <td><?php echo e($contact->email); ?></td>
                        <td><?php echo e($contact->phone); ?></td>
                        <td><?php echo e($contact->position); ?></td>
                        <td>
                            <button onclick="AbrirModal('edit',<?php echo e($contact->id); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                <i class="la la-edit"></i>
                            </button>
                            <button id="delete-contact" data-contact-id="<?php echo e($contact->id); ?>" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
                                <i class="la la-eraser"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal fade bd-example-modal-lg" id="modalupload"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">
            Upload Contacts
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
        </div>
        <div id="edit-modal-body-E" class="modal-body-E">
        <br>
          <?php echo Form::open(['route' => 'Upload.Contacts', 'method' => 'POST', 'files' => 'true']); ?>


          <div class="form-group row pull-right">
            <div class="col-md-3 ">

            </div>
          </div>
          <div class="form-group row ">
            <div class="col-md-1 "></div>
            <div class="col-md-6 ">
              <input type="file" name="file" value="Load File" required />
            </div>
          </div>
        </div>
        <div id="edit-modal-body" class="modal-footer">
          <?php echo Form::submit('Load', ['class'=> 'btn btn-primary']); ?>

          <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">Cancel</span>
          </button>
        </div>
        <?php echo Form::close(); ?>

      </div>
    </div>
  </div>
    
</div>
<?php echo $__env->make('contacts.partials.contactsModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('contacts.partials.deleteContactsModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##
<script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-contracts.js" type="text/javascript"></script>
<script>
    function AbrirModal(action,id){
        if(action == "edit"){
            var url = '<?php echo e(route("contacts.edit", ":id")); ?>';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#contactModal').modal({show:true});
            });
        }if(action == "add"){
            var url = '<?php echo e(route("contacts.add")); ?>';
            $('.modal-body').load(url,function(){
                $('#contactModal').modal({show:true});
            });
        }
        if(action == "delete"){
            var url = '<?php echo e(route("contacts.delete", ":id")); ?>';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#deleteContactModal').modal({show:true});
            });
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>