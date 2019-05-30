
<div class="m-portlet">

  <?php echo e(Form::open(array('route' => array('contracts.storeRate', $id)),['class' => 'form-group m-form__group'])); ?>

  <div class="m-portlet__body">
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <?php echo Form::label('origin_port', 'Origin Port'); ?>

        <?php echo e(Form::select('origin_port[]', $harbor,null,['id' => 'm_select2_1_modal','class'=>'m-select2-general form-control', 'style' => 'width:100%;','multiple' => 'multiple','required'=>'true'])); ?>

      </div>
      <div class="col-lg-4">
        <?php echo Form::label('destination_port', 'Destination Port'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo e(Form::select('destiny_port[]', $harbor,null,['id' => 'destiny','class'=>'m-select2-general form-control' ,'style' => 'width:100%;','multiple' => 'multiple','required'=>'true'])); ?>

          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        <?php echo Form::label('carrier', 'Carrier'); ?>

        <?php echo e(Form::select('carrier_id', $carrier,null,['id' => 'carrier','class'=>'m-select2-general form-control','required'=>'true'])); ?>


      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <?php echo Form::label('twuenty', '20 \' '); ?>

        <?php echo Form::number('twuenty',0, ['id' => 'twuenty','placeholder' => 'Default value zero','min' => '0','class' => 'form-control m-input' ,'required'=>'true' ]); ?>

      </div>


      <div class="col-lg-4">
        <?php echo Form::label('forty', '40  \' '); ?>

        <?php echo Form::number('forty', 0, ['id' => 'forty','placeholder' => 'Default value zero','min' => '0','class' => 'form-control m-input','required'=>'true' ]); ?>


      </div>
      <div class="col-lg-4">

        <?php echo Form::label('fortyhc', '40 HC \' '); ?>

        <?php echo Form::number('fortyhc', 0, ['id' => 'fortyhc','placeholder' => 'Default value zero','min' => '0','class' => 'form-control ' , 'required'=>'true']); ?>


      </div>

    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <?php echo Form::label('fortynor', '40 NOR \' '); ?>

        <?php echo Form::number('fortynor', 0, ['id' => 'fortynor','placeholder' => 'Default value zero','min' => '0','class' => 'form-control m-input' , 'required'=>'true' ]); ?>


      </div>
      <div class="col-lg-4">

        <?php echo Form::label('fortyfive', '45 \' '); ?>

        <?php echo Form::number('fortyfive',0, ['id' => 'fortyfive','placeholder' => 'Default value zero','min' => '0','class' => 'form-control ' , 'required'=>'true']); ?>


      </div>
      <div class="col-lg-4">
        <?php echo Form::label('currency', 'Currency'); ?>


        <div class="m-input-icon m-input-icon--right">
          <?php echo e(Form::select('currency_id', $currency,$currency_cfg->id,['id' => 'currency','class'=>'m-select2-general form-control','required'=>'true'])); ?>

        </div>
      </div>
    </div>
  </div>
  <br>
  <br>
  <div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
      &nbsp;&nbsp;&nbsp;<?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

      <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
      </button>
    </div>
    <br>
  </div>
</div>
<?php echo Form::close(); ?>

<!--end::Form-->
</div>
<script>


  $('.m-select2-general').select2({

  });


</script>
