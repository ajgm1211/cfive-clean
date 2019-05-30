<div class="m-portlet__body">
  <div class="form-group m-form__group row">
    <div class="col-lg-3">
      <?php echo Form::label('name', 'Provider'); ?>

      <?php echo Form::text('name', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']); ?>

    </div>
    <div class="col-lg-3">
      <?php echo Form::label('ports', 'Port'); ?>

      <?php echo e(Form::select('irelandports[]', $harbor,null,['class'=>'m-select2-general form-control port','multiple' => 'multiple'])); ?>

    </div>
    <div class="col-lg-3">
      <?php echo Form::label('validation_expire', 'Validity'); ?>

      <?php echo Form::text('validation_expire', $validation_expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']); ?>

    </div>
    <div class="col-lg-3">

      <?php echo Form::label('change', 'Charge Type'); ?><br>
      <?php echo e(Form::select('status',['1' => 'Export','2' => 'Import','3' => 'All'],null,['class'=>'m-select2-general form-control'])); ?>

    </div>
  </div>
  <div class="form-group m-form__group row">
    <div class="col-lg-3">
      <?php echo Form::label('KM 20', 'Charge for 20'); ?>

      <?php echo Form::number('km_20',0, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0']); ?>

    </div>
    <div class="col-lg-3">
      <?php echo Form::label('KM 40', 'Charge for 40'); ?>

      <?php echo Form::number('km_40',0, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0']); ?>

    </div>
    <div class="col-lg-3">
      <?php echo Form::label('KM 40 HC', 'Charge for 40HC'); ?>

      <?php echo Form::number('km_40hc',0, ['placeholder' => 'Enter Aditional Charge per KM','class' => 'form-control m-input','step'=>'.01','min'=>'0' ]); ?>

    </div>
    <div class="col-lg-3">
      <label>Company Restriction</label>
      <div class="form-group m-form__group align-items-center">
        <?php echo e(Form::select('companies[]',$companies,null,['multiple','class'=>'m-select2-general','id' => 'm-select2-company'])); ?>

      </div>
    </div>
  </div>
  <div class="form-group m-form__group row">
    <div class="col-lg-3">
      <?php echo Form::label('Charge Currency', 'Charge Currency'); ?>

      <?php echo e(Form::select('chargecurrencykm',$currency,$currency_cfg->id,['class'=>'custom-select form-control','id' => ''])); ?>

    </div>
  </div>


</div>

