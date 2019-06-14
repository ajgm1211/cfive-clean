<div class="m-portlet">


  <?php echo e(Form::open(array('route' => array('contracts.storeLocalChargeLcl', $id)),['class' => 'form-group m-form__group'])); ?>

  <div class="m-portlet__body">
    <div class="form-group m-form__group row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-4">
            <label>
              <?php echo Form::label('Type Route', 'Type Route'); ?>

            </label>
            <div class="m-radio-inline">
              <label class="m-radio">
                <input type="radio" id="rdrouteP" onclick="activarCountry('divport')" checked='true' name="typeroute" value="port"> Port
                <span></span>
              </label>
              <label class="m-radio">
                <input type="radio" id="rdrouteC" onclick="activarCountry('divcountry')"  name="typeroute" value="country"> Country
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
        <?php echo e(Form::select('surcharge_id', $surcharge,null,['id' => 'typeAdd','class'=>'m-select2-general form-control ','style' => 'width:100%;'])); ?>

      </div>
      <div class="col-lg-4">
        <div class="divport" >
          <?php echo Form::label('orig', 'Origin Port'); ?>

          <?php echo e(Form::select('port_origlocal[]', $harbor,null,['id' => 'port_orig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;','required'=>'true'])); ?>

        </div>

        <div class="divcountry" hidden="true">
          <?php echo Form::label('origC', 'Origin Country'); ?>

          <?php echo e(Form::select('country_orig[]', $countries,
          null,['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple'])); ?>


        </div>
      </div>
      <div class="col-lg-4">
        <div class="divport" >
          <?php echo Form::label('dest', 'Destination Port'); ?>


          <div class="m-input-icon m-input-icon--right">
            <?php echo e(Form::select('port_destlocal[]', $harbor,null,['id' => 'port_dest','class'=>'m-select2-general  form-control ','multiple' => 'multiple','style' => 'width:100%;','required'=>'true'])); ?>

            <span class="m-input-icon__icon m-input-icon__icon--right">
              <span>
                <i class="la la-info-circle"></i>
              </span>
            </span>
          </div>
        </div>
        <div class="divcountry" hidden="true">
          <?php echo Form::label('destC', 'Destination Country'); ?>

          <?php echo e(Form::select('country_dest[]',$countries,null,['id' => 'country_dest','class'=>'m-select2-general form-control','multiple' => 'multiple'  ])); ?>

        </div>




      </div>
    </div>
    <div class="form-group m-form__group row">
      <div class="col-lg-4">
        <?php echo Form::label('typed', 'Destination type'); ?>

        <?php echo e(Form::select('typedestiny_id',$typedestiny, null,['id' => 'changetypeAdd','class'=>'m-select2-general form-control','style' => 'width:100%;'])); ?>

      </div>
      <div class="col-lg-4">
        <?php echo Form::label('carrierL', 'Carrier'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo e(Form::select('carrier_id[]', $carrier,null,['id' => 'localcarrierAdd','class'=>'m-select2-general form-control','multiple' => 'multiple','required' => 'true'])); ?>

          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-info-circle"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        <?php echo Form::label('calculationt', 'Calculation Type'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo e(Form::select('calculationtype_id[]', $calculationT,null,['id' => 'calculationtypeAdd','class'=>'m-select2-general form-control ','multiple' => 'multiple','style' => 'width:80%;','required'=>'true'])); ?>

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
        <?php echo Form::label('ammountL', 'Amount'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo Form::text('ammount', 0, ['id' => 'ammountAdd','placeholder' => 'Please enter amount','class' => 'form-control m-input','min' => '0','step'=>'0.01']); ?>

          <span class="m-input-icon__icon m-input-icon__icon--right">
            <span>
              <i class="la la-bookmark-o"></i>
            </span>
          </span>
        </div>

      </div>
      <div class="col-lg-4">
        <?php echo Form::label('minimumL', 'Minimum'); ?>

        <div class="m-input-icon m-input-icon--right">
          <?php echo Form::text('minimum', 0, ['id' => 'minimumAdd','placeholder' => 'Please enter minimum','class' => 'form-control m-input']); ?>

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
          <?php echo e(Form::select('currency_id', $currency,$currency_cfg->id,['id' => 'localcurrencyAdd','class'=>'m-select2-general form-control' ,'style' => 'width:100%;'])); ?>

        </div>

      </div>
    </div>
  </div>  
  <br>
  <br>
  <div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
      &nbsp;&nbsp;<?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

      <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">Cancel</span>
      </button>
    </div>
    <br>
  </div>
  <?php echo Form::close(); ?>

</div>
<script src="/js/editcontracts.js"></script>
<script>
  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });
</script>
