<div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-max-height="200">

    <?php echo Form::open(['route' => 'gcadm.store.array', 'method' => 'post','class' => 'form-group m-form__group']); ?>

    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-8">
                        <?php echo Form::label('company_user', 'Company User'); ?>

                        <div class="m-input-icon m-input-icon--right">
                            <?php echo e(Form::select('company_user_id',$company_users,null,['id' => 'company_user_id','class'=>'m-select2-general form-control' ,'required' => 'true' ])); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group row">

            <div class="col-lg-12">
                <table class="table m-table m-table--head-separator-primary examm"  id="load" >
                    <thead class="examm" width="100%">
                        <tr>
                            <th>Type</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Charge T</th>
                            <th>Calculation T</th>
                            <th>Currency</th>
                            <th>Carrier</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $global; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th><?php echo e($gb['surcharge']); ?></th>
                            <th><?php echo e($gb['origin']); ?></th>
                            <th><?php echo e($gb['destination']); ?></th>
                            <th><?php echo e($gb['typedestiny']); ?></th>
                            <th><?php echo e($gb['calculationtype']); ?></th>
                            <th><?php echo e($gb['currency']); ?></th>
                            <th><?php echo e($gb['carrier']); ?></th>
                            <th><?php echo e($gb['ammount']); ?></th>

                        </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>
                <?php $__currentLoopData = $globals_id_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="idArray[]" value="<?php echo e($gb); ?>">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <style>
                    .scrollStyle
                    {
                        overflow-x:auto;
                    }
                </style>
            </div>
        </div>
    </div>  
    <br>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <br>
        <div class="m-form__actions m-form__actions">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

            <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
        <br>
    </div>
    <?php echo Form::close(); ?>


</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/globalcharges.js"></script>
<script>

    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });



    $(document).ready(function(){
        var id =[];
        $('input[name=array]').each(function(){
            id.push($(this).val());
        });
        // alert(id);

        $('#load').DataTable( {
            "scrollY":        "200px",
            "scrollCollapse": true,

            "paging":         false
        } ).columns.adjust().draw();

        setTimeout(function () {
            $('#load').DataTable().columns.adjust().draw();
        },200);
    });



</script>
