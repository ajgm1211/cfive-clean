<div class="form-group m-form__group row">
    <div class="col-lg-2">
        <?php echo Form::label('name', 'Reference'); ?>

        <?php echo Form::text('name', null, ['placeholder' => 'Reference','class' => 'form-control m-input','required' => 'required']); ?>

    </div>
    <div class="col-lg-2">
        <?php echo Form::label('direction', 'Direction'); ?>

        <?php echo Form::select('direction',$direction,@$contracts->direction_id, ['class' => 'form-control m-select2-general','required' => 'required']); ?>

    </div>
    <div class="col-lg-2">
        <div class="row">
            <?php echo Form::label('status', 'Status'); ?><br>
            <?php echo e(Form::select('status',['1' => 'Publish','2' => 'Draft'],null,['class'=>'custom-select form-control','id' => 'm_select2_2_modal'])); ?>

        </div>
    </div>
    <div class="col-lg-3">
        <?php echo Form::label('carrier', 'Carriers'); ?>

        <?php if(count(@$contracts->carriers) >=1): ?>
        <?php echo Form::select('carrierAr[]',$carrier,@$contracts->carriers->pluck('carrier_id'), ['class' => 'form-control m-select2-general','required' => 'required','multiple'=>'multiple']); ?>

        <?php else: ?>
        <?php echo Form::select('carrierAr[]',$carrier,null, ['class' => 'form-control m-select2-general','required' => 'required','multiple'=>'multiple']); ?>

        <?php endif; ?>
    </div>
    <div class="col-lg-3">
        <?php echo Form::label('validation_expire', 'Validation'); ?>

        <?php echo Form::text('validation_expire', $validation_expire, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']); ?>

    </div>

    <!--
<div class="col-lg-1">

<?php echo Form::label('free_days', 'Free Days'); ?><br>
<?php echo e(Form::number('free_days',null,['placeholder'=> '0','class'=>'custom-select form-control','id' => 'free_days','min' =>'0'])); ?>


</div> -->


</div>

<br>
