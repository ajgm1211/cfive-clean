<?php if(!$globalcharges->globalcharport->isEmpty()): ?>
<?php
$portRadio = true; 
$countryRadio = false; 
?>
<script>
  activarCountry('divport');
</script>
<?php endif; ?>
<?php if(!$globalcharges->globalcharcountry->isEmpty()): ?>
<?php
$countryRadio = true; 
$portRadio = false; 
?>
<script>
  activarCountry('divcountry');
</script>
<?php endif; ?>
<div class="m-portlet">

  <?php echo e(Form::model($globalcharges, array('route' => array('globalcharges.store', $globalcharges->id), 'id' => 'frmSurcharges'))); ?>

  <div class="m-portlet__body">
    <div class="form-group m-form__group row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-4">
            <label>
              <?php echo Form::label('Type Route', 'Type of route'); ?>

            </label>
            <div class="m-radio-inline">
              <label class="m-radio">
                <?php echo e(Form::radio('typeroute', 'port', $portRadio ,['id' => 'rdrouteP' , 'onclick' => 'activarCountry(\'divport\')' ])); ?> Port
                <span></span>
              </label>
              <label class="m-radio">
                <?php echo e(Form::radio('typeroute', 'country', $countryRadio ,['id' => 'rdrouteC' , 'onclick' => 'activarCountry(\'divcountry\')' ])); ?> Country
                <span></span>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <label>
          <?php echo Form::label('type', 'Type'); ?>

        </label>
        <?php echo e(Form::select('type', $surcharge,$globalcharges->surcharge_id,['id' => 'type','class'=>'m-select2-general form-control ','style' => 'width:100%;'])); ?>

      </div>
      <div class="col-lg-4">
        <div class="divport" >
          <?php echo Form::label('orig', 'Origin Port'); ?>

          <?php echo e(Form::select('port_orig[]', $harbor,$globalcharges->globalcharport->pluck('port_orig'),['id' => 'portOrig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;'])); ?>

        </div>
        <div class="divcountry" hidden="true">
          <?php echo Form::label('origC', 'Origin Country'); ?>

          <?php echo e(Form::select('country_orig[]', $countries,
          $globalcharges->globalcharcountry->pluck('countryOrig')->unique()->pluck('id'),['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple'])); ?>


        </div>
      </div>
      <div class="col-lg-4">
        <div class="divport" >
          <?php echo Form::label('dest', 'Destination Port'); ?>

          <div class="m-input-icon m-input-icon--right">
            <?php echo e(Form::select('port_dest[]', $harbor,$globalcharges->globalcharport->pluck('port_dest'),['id' => 'portDest','class'=>'m-select2-general  form-control ','multiple' => 'multiple','style' => 'width:100%;'])); ?>

            <span class="m-input-icon__icon m-input-icon__icon--right">
              <span>
                <i class="la la-info-circle"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="divcountry" hidden="true">
          <?php echo Form::label('destC', 'Destination Country'); ?>

          <?php echo e(Form::select('country_dest[]',$countries,$globalcharges->globalcharcountry->pluck('countryDest')->unique()->pluck('id'),[ 'id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ])); ?>

        </div>
      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <?php echo Form::label('typed', 'Destination type'); ?>

        <?php echo e(Form::select('changetype',$typedestiny, $globalcharges->typedestiny_id,['id' => 'changetype','class'=>'m-select2-general form-control','style' => 'width:100%;'])); ?>

      </div>


      <div class="col-lg-4">
        <?php echo Form::label('validation_expire', 'Validation'); ?>

        <?php echo Form::text('validation_expire', $globalcharges->validation_expire, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']); ?>

      </div>


      <div class="col-lg-4">
        <?php echo Form::label('calculationt', 'Calculation Type'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo e(Form::select('calculationtype[]', $calculationT,$globalcharges->calculationtype_id,['id' => 'calculationtype','class'=>'m-select2-general form-control ','style' => 'width:80%;','multiple' => 'multiple'  ])); ?>

          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-map-marker"></i>
            </span>
          </span>
        </div>

      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <?php echo Form::label('carrierL', 'Carrier'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo e(Form::select('localcarrier[]', $carrier,$globalcharges->globalcharcarrier->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple'])); ?>

          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        <?php echo Form::label('ammountL', 'Amount'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo Form::text('ammount', $globalcharges->ammount, ['id' => 'ammount','placeholder' => 'Please enter the 40HC','class' => 'form-control m-input']); ?>

          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-bookmark-o"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        <?php echo Form::label('currencyl', 'Currency'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo e(Form::select('localcurrency_id', $currency,$globalcharges->currency_id,['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'style' => 'width:100%;'])); ?>

          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-bookmark-o"></i>
            </span>
          </span>
        </div>

      </div>
    </div>
  </div>  
  <br>
  <div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <?php echo Form::submit('Duplicate', ['class'=> 'btn btn-primary']); ?>

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
</script>
