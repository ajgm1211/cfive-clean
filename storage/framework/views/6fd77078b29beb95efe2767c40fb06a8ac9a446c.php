<?php $__env->startSection('title', 'Add Quote'); ?>
<?php $__env->startSection('content'); ?>
<?php 
$termOrig ="";
$termDest ="";
$termAll = "";
$subtotalOrigin = 0;

$subtotalDestiny = 0;
?>

<div class="m-content">
  <br>
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


  <div class="tab-pane" id="m_portlet_tab_1_2">
    <?php echo Form::open(['route' => 'quotes.store','class' => 'm-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed']); ?>

    <br>
    <div class="row">
      <div class="col-md-2 col-xs-4" >
        <?php if($email_templates): ?>
        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#SendQuoteModal">
          Save and send
        </button>
        <?php endif; ?>
      </div>
      <div class="col-md-2 col-xs-4">
        <button id="store-pdf" value="submit-pdf" name="btnsubmit" type="submit" class="btn btn-primary btn-block">Save and PDF</button>
      </div>
      <div class="col-md-2 col-xs-4" >
        <button id="store" value="submit" name="btnsubmit" type="submit" class="btn btn-primary btn-block">Save</button>
      </div>
    </div>
    <hr><br><br>
    <div class="row">
      <div class="col-lg-10">
        <div class="row">
          <div class="col-md-12">
            <div class="m-portlet__body">
              <div class="m-portlet__head" style="min-height: 100px;">
                <div class="m-portlet__head-tools">
                  <div class="col-md-12" style="margin-top: 20px;">
                    <div class="pull-left text-left" style="line-height: .5;">
                      <?php if($user->companyUser->logo!=''): ?>
                      <img src="<?php echo e(Storage::disk('s3_upload')->url($user->companyUser->logo)); ?>" class="img img-responsive" style="width: 100px; height: auto; margin-bottom:35px">
                      <?php endif; ?>
                    </div>
                    <div class="pull-right text-right" style="line-height: .5">                                

                      <p><b>Date of issue:</b> <?php echo e($form->date); ?> </p>
                      <p><b>Validity: </b> <?php echo e(\Carbon\Carbon::parse( $info->contract->validity)->format('d M Y')); ?> -  <?php echo e(\Carbon\Carbon::parse( $info->contract->expire)->format('d M Y')); ?> </p>
                      <p><b>Contract Ref: <span style="color: #CFAC6C">#<?php echo e($info->contract->name); ?> / <?php echo e($info->contract->number); ?></span></b></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="m-portlet__body">
              <div class="m-portlet__head" style="min-height: 150px;">
                <div class="m-portlet__head-tools">
                  <div class="col-md-12">
                    <div class="pull-left text-left" style="line-height: .5">
                      <br><br>
                      <p><b>From:</b></p>
                      <p><?php echo e($user->name); ?></p>
                      <p><?php echo e($user->companyUser->address); ?></p>
                      <p><?php echo e($user->companyUser->phone); ?></p>
                      <p><?php echo e($user->email); ?></p>
                    </div>
                    <div class="pull-right text-right" style="line-height: .5">
                      <p><b>To:</b></p>
                      <p class="name size-12px"><?php echo e($contactInfo->first_name.' '.$contactInfo->last_name); ?></p>
                      <p><b><?php echo e($companyInfo->business_name); ?></b></p>
                      <p><?php echo e($contactInfo->address); ?></p>
                      <p><?php echo e($contactInfo->phone); ?></p>
                      <p><a href="mailto:<?php echo e($contactInfo->email); ?>"><?php echo e($contactInfo->email); ?></a></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="">
              <div class="row">
                <div class="container">
                  <br>
                  <br>
                  <div class="m-portlet__body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="panel panel-default">
                          <div class="panel-heading title-quote size-14px"><b>Origin</b></div>
                          <div class="panel-body">

                            <b>Port: </b><span id="origin_input">  <?php echo e($info->port_origin->name); ?></span><br>
                            <?php if($form->origin_address != ""): ?>
                            <b>Address: </b>
                            <span id="destinationA_input">
                              <?php echo e($form->origin_address); ?>

                            </span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="panel panel-default">
                          <div class="panel-heading title-quote size-14px"><b>Destination</b></div>
                          <div class="panel-body">
                            <b>Port: </b><span id="destination_input"><?php echo e($info->port_destiny->name); ?></span><br>
                            <?php if($form->destination_address != ""): ?>
                            <b>Address: </b><span id="destinationA_input">
                            <?php echo e($form->destination_address); ?>

                            </span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>

                    </div>

                    <div style="padding-top: 20px; padding-bottom: 20px;">
                      <div class="row">
                        <div class="col-md-12">

                          <img src="<?php echo e(url('imgcarrier/'.$info->carrier->image)); ?>"  class="img img-responsive" width="100px" height="auto" margin-bottom="25px" />

                        </div>
                      </div>
                    </div>
                    <div class="row" style="padding-top: 20px; padding-bottom: 20px;">
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-12">
                            <h5 class="title-quote size-14px">Cargo details</h5>
                            <hr>
                          </div>
                        </div>
                        <?php if($form->twuenty > 0): ?>
                        <p id="cargo_details_20_p" ><span id="cargo_details_20"></span> <?php echo e($form->twuenty); ?> x 20' Containers</p>
                        <?php endif; ?>
                        <?php if($form->forty > 0): ?>
                        <p id="cargo_details_40_p" ><span id="cargo_details_40"></span> <?php echo e($form->forty); ?> x 40' Containers</p>
                        <?php endif; ?>
                        <?php if($form->fortyhc > 0): ?>
                        <p id="cargo_details_40_hc_p" ><span id="cargo_details_40_hc"></span> <?php echo e($form->fortyhc); ?> x 40' HC Containers</p>
                        <?php endif; ?>
                        <?php if($form->fortynor > 0): ?>
                        <p id="cargo_details_40_nor_p" ><span id="cargo_details_40_nor"></span> <?php echo e($form->fortynor); ?> x 40' NOR Containers</p>
                        <?php endif; ?>
                        <?php if($form->fortyfive > 0): ?>
                        <p id="cargo_details_45_hc_p" ><span id="cargo_details_45_hc"></span> <?php echo e($form->fortyfive); ?> x 45' HC Containers</p> 
                        <?php endif; ?>
                      </div>
                    </div>
                    <?php if($form->total_quantity != null): ?>
                    <div class="row">
                      <div class="col-md-3">
                        <div id="cargo_details_cargo_type_p" ><b>Cargo type:</b> <span id="cargo_details_cargo_type"><?php echo e($form->type_cargo == 1  ? 'Pallets' : 'Packages'); ?></span></div>
                      </div>
                      <div class="col-md-3">
                        <div id="cargo_details_total_quantity_p">  <b>Total quantity:</b> <?php echo e($form->total_quantity); ?> <span id="cargo_details_total_quantity"></span>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div id="cargo_details_total_weight_p" ><b>Total weight: </b>  <?php echo e($form->total_weight); ?> <span id="cargo_details_total_weight"></span> KG</div>
                      </div>
                      <div class="col-md-3">
                        <p id="cargo_details_total_volume_p" ><b>Total volume: </b> <?php echo e($form->total_volume); ?> <span id="cargo_details_total_volume"></span> m<sup>3</sup></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <div id="chargeable_weight_div" >  <b>Chargeable weight:</b>
                          <span id="chargeable_weight_span"> <?php echo e($form->chargeable_weight); ?></span> kg
                        </div>
                      </div>
                    </div>
                    <?php endif; ?>
                    <?php if($form->total_quantity_pkg != null): ?>
                    <div class="row">
                      <table class="table table-bordered color-blue text-center">
                        <tbody>
                          <tr>
                            <th width="75" class="header-table title-quote">Quantity</th>
                            <th width="75" class="header-table title-quote">Height</th>
                            <th width="75" class="header-table title-quote">Width</th>
                            <th width="75" class="header-table title-quote">Large</th>
                            <th width="75" class="header-table title-quote">Weight</th>
                            <th width="75" class="header-table title-quote">Total Weight</th>
                            <th width="75" class="header-table title-quote">Volume</th>
                          </tr>
                          <?php $__currentLoopData = $form->quantity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($value != null): ?>
                          <tr>
                            <td width="75" ><?php echo e($form->quantity[$key]); ?></td>
                            <td width="75" ><?php echo e($form->height[$key]); ?></td>
                            <td width="75" ><?php echo e($form->width[$key]); ?> </td>
                            <td width="75" ><?php echo e($form->large[$key]); ?></td>
                            <td width="75" ><?php echo e($form->weight[$key]); ?> &nbsp; kg</td>
                            <td width="75" ><?php echo e($form->quantity[$key] * $form->weight[$key]); ?> &nbsp; kg</td>
                            <td width="75" ><?php echo e($form->volume[$key]); ?> m<sup>3</sup>&nbsp;</td>
                          </tr>
                          <?php endif; ?>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="row pull-right">
                      <div class="col-md-12">
                        <div id="cargo_details_total_pkg_p" >  <b>Total:</b> 
                          <span id="cargo_details_total_quantity_pkg"><?php echo e($form->total_quantity_pkg); ?></span> un&nbsp;
                          <span id="cargo_details_total_volume_pkg"><?php echo e($form->total_volume_pkg); ?></span> m<sup>3</sup>&nbsp;
                          <span id="cargo_details_total_weight_pkg"><?php echo e($form->total_weight_pkg); ?></span> km
                        </div>
                        <br>
                        <div id="chargeable_weight_div" >  <b>Chargeable weight:</b>
                          <span id="chargeable_weight_span"> <?php echo e($form->chargeable_weight); ?></span> kg
                        </div>
                      </div>
                    </div>
                    <?php endif; ?>

                    <br><br><br>
                    <div class="row">
                      <div class="col-md-3">
                        <h5 class="title-quote size-14px">Origin ammounts</h5>
                      </div>
                      <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table table-bordered color-blue">
                            <thead class="title-quote text-center header-table">
                              <tr>
                                <td >Charge</td>
                                <td >Detail</td>
                                <td >Units</td>
                                <td >Price per unit</td>
                                <td >Total</td>
                                <td >Markup</td>
                                <td >Total <?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?></td>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $__currentLoopData = $info->localOrig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php 
                              $total = explode(" ",$origin->origin->totalAmmount);
                              ?>       
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="<?php echo e($origin->origin->surcharge_terms); ?> " />
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="<?php echo e($origin->origin->calculation_name); ?>"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0" step="0.01" value="<?php echo e($origin->origin->cantidadT); ?>"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="0" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="<?php echo e($origin->origin->monto); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('origin_ammount_currency[]',$currency,$origin->origin->idCurrency,['class'=>' m-select2-general form-control origin_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="<?php echo e($origin->origin->subtotal_local); ?>"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" type="number" step=".01" min="0" value="<?php echo e($origin->origin->markupConvert); ?>"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="<?php echo e($total[0]); ?>">
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <?php $__currentLoopData = $info->globalOrig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php 
                              $total = explode(" ",$origin->origin->totalAmmount);
                              ?>           
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="<?php echo e($origin->origin->surcharge_terms); ?> " />
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="<?php echo e($origin->origin->calculation_name); ?>"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="number" min="0" step="0.01" value="<?php echo e($origin->origin->cantidadT); ?>"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="0" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="<?php echo e($origin->origin->monto); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('origin_ammount_currency[]',$currency,$origin->origin->idCurrency,['class'=>'m-select2-general form-control origin_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="<?php echo e($origin->origin->subtotal_global); ?>"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" type="number" step=".01" min="0" value="<?php echo e($origin->origin->markupConvert); ?>"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="<?php echo e($total[0]); ?>">
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <?php $__currentLoopData = $info->inlandOrigin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php $__currentLoopData = $origin->inlandDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailsOrig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" name="origin_ammount_charge[]" value="<?php echo e($origin->provider); ?> " />
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" class="form-control" type="text" value="<?php echo e($origin->type); ?>"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" class="form-control origin_ammount_units" type="text" min="0" value="<?php echo e($origin->km); ?> " readonly/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" min="0" step="0.01" class="origin_price_per_unit form-control" aria-label="..." value="<?php echo e($detailsOrig->price_unit); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        <?php echo e(Form::select('origin_ammount_currency[]',$currency,$info->idCurrency,['class'=>'m-select2-general form-control origin_ammount_currency'])); ?>              
                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" class="form-control origin_total_ammount" step=".01" type="number" min="0" value="<?php echo e($detailsOrig->sub_in); ?>"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" class="form-control origin_ammount_markup" step="0.01"  type="number" min="0" value="<?php echo e($detailsOrig->markupConvert); ?>"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  class="form-control origin_total_ammount_2" aria-label="..." value="<?php echo e($detailsOrig->sub_in); ?>">
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <?php if((empty($info->localOrig)) && (empty($info->globalOrig)) && (empty($info->inlandOrigin))): ?>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" value="" name="origin_ammount_charge[]"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="" class="form-control" type="text"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" value="" class="form-control origin_ammount_units" type="number" min="0" step="0.01"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="" min="0" step="0.01" class="origin_price_per_unit form-control" aria-label="...">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('origin_ammount_currency[]',$currency,null,['class'=>'m-select2-general form-control origin_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" />
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  value="" class="origin_total_ammount_2 form-control" aria-label="...">

                                    </div>
                                  </div>                
                                </td>
                              </tr>
                              <?php endif; ?>
                              <tr class="hide"  id="origin_ammounts">
                                <td>
                                  <input type="text" class="form-control" id="origin_ammount_charge" value="" name="origin_ammount_charge[]"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_detail" name="origin_ammount_detail[]" value="" class="form-control" type="text"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_units" name="origin_ammount_units[]" value="" class="form-control origin_ammount_units" type="number" min="0" step="0.01"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="origin_price_per_unit" name="origin_price_per_unit[]" value="" min="0" step="0.01" class="origin_price_per_unit form-control" aria-label="...">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        <?php echo e(Form::select('origin_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control origin_ammount_currency select-2-width'])); ?>

                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input id="origin_total_ammount" name="origin_total_ammount[]" value="" class="form-control origin_total_ammount" type="number" min="0"/>
                                </td>
                                <td>
                                  <input id="origin_ammount_markup" name="origin_ammount_markup[]" value="" class="form-control origin_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="origin_total_ammount_2[]"  value="" class="origin_total_ammount_2 form-control" aria-label="...">
                                      <a class="btn removeOriginButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>                
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class='row'>
                      <div class="col-md-12">
                        <h5 class="title-quote pull-right">
                          Sub-Total:<span id="sub_total_origin"><?php echo e($info->totalChargeOrig); ?></span><?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?> 
                          <input type="hidden" id="total_origin_ammount" name="sub_total_origin" class="form-control" value="<?php echo e($info->totalChargeOrig); ?>"/>
                          <a class="btn addButtonOrigin" style="vertical-align: middle">
                            <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                          </a>
                        </h5>                                                            
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-3">
                        <h5 class="title-quote size-14px">Freight ammounts</h5>
                      </div>
                      <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table table-bordered color-blue">
                            <thead class="title-quote text-center header-table">
                              <tr>
                                <td >Charge</td>
                                <td >Detail</td>
                                <td >Units</td>
                                <td >Price per unit</td>
                                <td >Total</td>
                                <td >Markup</td>
                                <td >Total <?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?></td>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $totalF = explode(" ",$info->totalFreight);
                              ?>
                              <?php $__currentLoopData = $info->rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php 
                              $total = explode(" ",$freight->total);
                              ?>
                              <tr>
                                <td>


                                  <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="<?php echo e($freight->type); ?>"  readonly='true'/>
                                </td>
                                <td>
                                  <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="<?php echo e($freight->detail); ?>"  readonly='true' />
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" step="0.01" max="99" type="text" value="<?php echo e($freight->cantidad); ?>" data-container="body" data-toggle="m-popover" data-placement="bottom" data-content="Im sorry " data-original-title=""  readonly='true'/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="0" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="<?php echo e($freight->price); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('freight_ammount_currency[]',$currency,$freight->idCurrency,['class'=>'m-select2-general form-control freight_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="<?php echo e($freight->subtotal); ?>">
                                </td>
                                <td>
                                  <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" step="0.01" min="0" type="number" value="<?php echo e($freight->markupConvert); ?>"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2"  step="0.01"  min="0" type="number" value="<?php echo e($total[0]); ?>"/>
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <?php $__currentLoopData = $info->localFreight; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php 
                              $total = explode(" ",$freight->freight->totalAmmount);
                              ?>    
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="<?php echo e($freight->freight->surcharge_terms); ?>" />
                                </td>
                                <td>
                                  <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="<?php echo e($freight->freight->calculation_name); ?>" />
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" step="0.01" max="99" type="number" value="<?php echo e($freight->freight->cantidadT); ?>"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="0" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="<?php echo e($freight->freight->monto); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('freight_ammount_currency[]',$currency,$freight->freight->idCurrency,['class'=>'m-select2-general form-control freight_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="<?php echo e($freight->freight->subtotal_local); ?>">
                                </td>
                                <td>
                                  <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" min="0" step=".01" type="number" value="<?php echo e($freight->freight->markupConvert); ?>"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2"  step="0.01"  min="0" type="number" value="<?php echo e($total[0]); ?>"/>
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <?php $__currentLoopData = $info->globalFreight; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php 
                              $total = explode(" ",$freight->freight->totalAmmount);
                              ?>  
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="freight_ammount_charge" name="freight_ammount_charge[]"  value="<?php echo e($freight->freight->surcharge_terms); ?>" />
                                </td>
                                <td>
                                  <input id="freight_ammount_detail" name="freight_ammount_detail[]" class="form-control" type="text" value="<?php echo e($freight->freight->calculation_name); ?>" />
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" class="form-control freight_ammount_units" min="0" step="0.01" max="99" type="number" value="<?php echo e($freight->freight->cantidadT); ?>"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="freight_price_per_unit" name="freight_price_per_unit[]" min="0" step="0.01" class="form-control freight_price_per_unit" aria-label="..." value="<?php echo e($freight->freight->monto); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        <?php echo e(Form::select('freight_ammount_currency[]',$currency,$freight->freight->idCurrency,['class'=>'m-select2-general form-control freight_ammount_currency'])); ?>              
                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input type="text" name="freight_total_ammount[]"  class="form-control freight_total_ammount"  aria-label="..." value="<?php echo e($freight->freight->subtotal_global); ?>">
                                </td>
                                <td>
                                  <input id="freight_ammount_markup" name="freight_ammount_markup[]" class="form-control freight_ammount_markup" step=".01" min="0" type="number" value="<?php echo e($freight->freight->markupConvert); ?>"/>
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input id="freight_total_ammount_2" name="freight_total_ammount_2[]" class="form-control freight_total_ammount_2"  step="0.01"  min="0" type="number" value="<?php echo e($total[0]); ?>"/>
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <tr class="hide"  id="freight_ammounts">
                                <td>
                                  <input type="text" class="form-control" id="freight_ammount_charge" value="" name="freight_ammount_charge[]"/>
                                </td>
                                <td>
                                  <input name="freight_ammount_detail[]" value="" class="form-control" type="text"/>
                                </td>
                                <td>
                                  <input  name="freight_ammount_units[]" value="" class="form-control freight_ammount_units" type="number" min="0" step="0.01"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number"  name="freight_price_per_unit[]" value="" min="0" step="0.01" class="freight_price_per_unit form-control" aria-label="...">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        <?php echo e(Form::select('freight_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control freight_ammount_currency select-2-width'])); ?>

                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input  name="freight_total_ammount[]" value="" class="form-control freight_total_ammount" type="number" min="0"/>
                                </td>
                                <td>
                                  <input  name="freight_ammount_markup[]" value="" class="form-control freight_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="freight_total_ammount_2[]"  value="" class="freight_total_ammount_2 form-control" aria-label="...">
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>                
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class='row'>
                      <div class="col-md-12">
                        <h5 class="title-quote pull-right">
                          Sub-Total:<span id="sub_total_freight"><?php echo e($totalF[0]); ?></span>&nbsp;<?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?>
                          <input type="hidden" id="total_freight_ammount" name="sub_total_freight"  class="form-control" value = '<?php echo e($totalF[0]); ?>'/>
                          <a class="btn addButton" style="vertical-align: middle">
                            <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                          </a>
                        </h5>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-3">
                        <h5 class="title-quote size-14px">Destination ammounts</h5>
                      </div>
                      <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table table-bordered color-blue">
                            <thead class="title-quote text-center header-table">
                              <tr>
                                <td >Charge</td>
                                <td >Detail</td>
                                <td >Units</td>
                                <td >Price per unit</td>

                                <td >Total</td>
                                <td >Markup</td>
                                <td >Total <?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?></td>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $__currentLoopData = $info->localDest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destiny): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php 
                              $total = explode(" ",$destiny->destiny->totalAmmount);
                              ?> 
                              <tr>
                                <td>
                                  <input type="text" class="form-control"  id="destination_ammount_charge"  name="destination_ammount_charge[]"  value="<?php echo e($destiny->destiny->surcharge_terms); ?>" />
                                </td>
                                <td>
                                  <input name="destination_ammount_detail[]" id="destination_ammount_detail"  class="form-control" type="text" value="<?php echo e($destiny->destiny->calculation_name); ?>"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0" step="0.01" value="<?php echo e($destiny->destiny->cantidadT); ?>"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" name="destination_price_per_unit[]" min="0" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="<?php echo e($destiny->destiny->monto); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('destination_ammount_currency[]',$currency,$destiny->destiny->idCurrency,['class'=>'m-select2-general form-control destination_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="<?php echo e($destiny->destiny->subtotal_local); ?>"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" type="number" step="0.01" min="0" value="<?php echo e($destiny->destiny->markupConvert); ?>" />
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="<?php echo e($total[0]); ?>">
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <?php $__currentLoopData = $info->globalDest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destiny): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php 
                              $total = explode(" ",$destiny->destiny->totalAmmount);
                              ?> 
                              <tr>
                                <td>
                                  <input type="text" class="form-control"  id="destination_ammount_charge"  name="destination_ammount_charge[]"  value="<?php echo e($destiny->destiny->surcharge_terms); ?>" />
                                </td>
                                <td>
                                  <input name="destination_ammount_detail[]" id="destination_ammount_detail"  class="form-control" type="text" value="<?php echo e($destiny->destiny->calculation_name); ?>"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="number" min="0" step="0.01" value="<?php echo e($destiny->destiny->cantidadT); ?>"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" name="destination_price_per_unit[]" min="0" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="<?php echo e($destiny->destiny->monto); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('destination_ammount_currency[]',$currency,$destiny->destiny->idCurrency,['class'=>'m-select2-general form-control destination_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="<?php echo e($destiny->destiny->subtotal_global); ?>"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" type="number" step="0.01" min="0" value="<?php echo e($destiny->destiny->markupConvert); ?>" />
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="<?php echo e($total[0]); ?>">
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                              <?php $__currentLoopData = $info->inlandDestiny; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destiny): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php $__currentLoopData = $destiny->inlandDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailsDest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="destination_ammount_charge[]"  value="<?php echo e($destiny->provider); ?> " />
                                </td>
                                <td>
                                  <input name="destination_ammount_detail[]" class="form-control" type="text" value="<?php echo e($destiny->type); ?>"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_units[]" class="form-control destination_ammount_units" type="text" step="0.01" value="<?php echo e($destiny->km); ?>" readonly='true'/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" name="destination_price_per_unit[]" min="0" step="0.01" class="destination_price_per_unit form-control" aria-label="..." value="<?php echo e($detailsDest->price_unit); ?>">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('destination_ammount_currency[]',$currency,$info->idCurrency,['class'=>'m-select2-general form-control destination_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input name="destination_total_ammount[]" class="form-control destination_total_ammount" type="number"  step=".01" min="0" value="<?php echo e($detailsDest->amount); ?>"/>
                                </td>
                                <td>
                                  <input name="destination_ammount_markup[]" class="form-control destination_ammount_markup" step=".01" type="number" min="0" value="<?php echo e($detailsDest->markupConvert); ?>" />
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  class="form-control destination_total_ammount_2" aria-label="..." value="<?php echo e($detailsDest->sub_in); ?>">
                                      <a class="btn removeButton">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php if(empty($info->localDest) && (empty($info->globalDest )) && (empty($info->inlandDestiny))): ?>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" id="destination_ammount_charge" value="" name="destination_ammount_charge[]"/>
                                </td>
                                <td>
                                  <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="" class="form-control" type="text"/>
                                </td>
                                <td>
                                  <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0" step="0.01"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="" min="0" step="0.01" class="destination_price_per_unit form-control" aria-label="...">
                                    <div class="input-group-btn">
                                      <div class="btn-group">

                                        <?php echo e(Form::select('destination_ammount_currency[]',$currency,null,['class'=>'m-select2-general form-control destination_ammount_currency'])); ?>


                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0"/>
                                </td>
                                <td>
                                  <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  value="" class="destination_total_ammount_2 form-control" aria-label="...">

                                    </div>
                                  </div>
                                </td>
                              </tr>
                              <?php endif; ?>

                              <tr class="hide"  id="destination_ammounts">
                                <td>
                                  <input type="text" class="form-control" id="destination_ammount_charge" value="" name="destination_ammount_charge[]"/>
                                </td>
                                <td>
                                  <input id="destination_ammount_detail" name="destination_ammount_detail[]" value="" class="form-control" type="text"/>
                                </td>
                                <td>
                                  <input id="destination_ammount_units" name="destination_ammount_units[]" value="" class="form-control destination_ammount_units" type="number" min="0" step="0.01"/>
                                </td>
                                <td>
                                  <div class="input-group">
                                    <input type="number" id="destination_price_per_unit" name="destination_price_per_unit[]" value="" min="0" step="0.01" class="destination_price_per_unit form-control" aria-label="...">
                                    <div class="input-group-btn">
                                      <div class="btn-group">
                                        <?php echo e(Form::select('destination_ammount_currency[]',$currencies,$currency_cfg->id,['class'=>'form-control destination_ammount_currency select-2-width'])); ?>

                                      </div>
                                    </div>
                                  </div>
                                </td>

                                <td>
                                  <input id="destination_total_ammount" name="destination_total_ammount[]" value="" class="form-control destination_total_ammount" type="number" min="0"/>
                                </td>
                                <td>
                                  <input id="destination_ammount_markup" name="destination_ammount_markup[]" value="" class="form-control destination_ammount_markup" step=".01" type="number" min="0"/> 
                                </td>
                                <td>
                                  <div class="form-group">
                                    <div class="input-group">
                                      <input type="text" name="destination_total_ammount_2[]"  value="" class="destination_total_ammount_2 form-control" aria-label="...">
                                      <a class="btn removeButtonDestination">
                                        <span class="fa fa-trash" role="presentation" aria-hidden="true"></span> &nbsp;
                                      </a>
                                    </div>
                                  </div>                
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class='row'>
                      <div class="col-md-12">
                        <h5 class="title-quote pull-right">
                          Sub-Total:<span id="sub_total_destination"><?php echo e($info->totalChargeDest); ?> </span><?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?>&nbsp;
                          <input type="hidden" id="total_destination_ammount" name="sub_total_destination" class="form-control"  value="<?php echo e($info->totalChargeDest); ?>"/>
                          <a class="btn addButtonDestination" style="vertical-align: middle">
                            <span class="fa fa-plus" role="presentation" aria-hidden="true"></span> &nbsp;
                          </a>
                        </h5>
                      </div>
                    </div><br>
                    <hr>
                    <div class='row'>
                      <div class="col-md-12">
                        <?php
                        $totalQ = explode(" ",$info->totalQuote);
                        ?>

                        <div class="form-group text-right">
                          <h3 class="size-16px color-blue"><button type="button" id="total" class="btn btn-primary"><b>Total: <span id="total"><?php echo e($totalQ[0]); ?></span> &nbsp;<?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?></b></button></h3>
                        </div>
                      </div>
                    </div>
                    <div class = 'row'>  
                      <div class="col-md-12">
                        <div class="form-group text-left">
                          <p>Exchange rate: <?php if($currency_cfg->alphacode=='EUR'): ?> 1 EUR = <?php echo e($exchange->rates); ?> USD <?php else: ?> 1 USD = <?php echo e($exchange->rates_eur); ?> EUR <?php endif; ?></p>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-12">      
                            <div class="header-table title-quote size-14px" style="padding-left: 10px;">
                              <b>Terms & conditions</b>                
                            </div>
                          </div>
                        </div>
                        <br/>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group ">

                              <?php if(isset($terms_all) && $terms_all->count()>0): ?>  
                              <?php $__currentLoopData = $terms_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php
                              $termAll .= $form->modality==1 ? $v->term->export : $v->term->import;
                              ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php endif; ?>


                              <?php if(isset($terms_origin) && $terms_origin->count()>0): ?>  
                              <?php $__currentLoopData = $terms_origin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php
                              $termOrig .= $form->modality==1 ? $v->term->export : $v->term->import;
                              ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php endif; ?>
                              <?php if(isset($terms_destination) && $terms_destination->count()>0): ?>           <?php $__currentLoopData = $terms_destination; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <?php
                              $termDest .= $form->modality==1 ? $v->term->export : $v->term->import;
                              ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php endif; ?>

                              <?php if($termOrig != "" || $termDest!="" || $termAll != "" ): ?>

                              <?php echo Form::textarea('term',$termAll." ".$termOrig." ".$termDest, ['placeholder' => 'Please enter your export text','class' => 'form-control editor m-input','id'=>'Export']); ?>

                              <?php else: ?>
                              <?php echo Form::textarea('term',null, ['placeholder' => 'Please enter your export text','class' => 'form-control editor m-input','id'=>'term']); ?>

                              <?php endif; ?>

                            </div>
                          </div>
                        </div> 
                        <div class="row">
                          <div class="col-md-12">      
                            <div class="header-table title-quote size-14px" style="padding-left: 10px;">
                              <b>Payment conditions</b>        
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-lg-12">
                            <br>
                            <div class="" style="margin-bottom:40px;">
                              <?php echo Form::textarea('payment_conditions', $companyInfo->payment_conditions, ['placeholder' => 'Please enter your payment conditions text','class' => 'form-control editor m-input','id'=>'payment_conditions']); ?>

                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-2 col-xs-4" >
                        <?php if($email_templates): ?>
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#SendQuoteModal">
                          Save and send 
                        </button>
                        <?php endif; ?>
                      </div>
                      <div class="col-md-2 col-xs-4">

                        <button id="store-pdf" value="submit-pdf" name="btnsubmit" type="submit" class="btn btn-primary btn-block">Save and PDF</button>
                      </div>
                      <div class="col-md-2 col-xs-4" >
                        <button id="store" value="submit" name="btnsubmit" type="submit" class="btn btn-primary btn-block">Save</button>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- <div class="col-md-2">
<h3 class="title-quote size-16px">Settings</h3>
<hr>
<p class="title-quote size-14px" data-toggle="collapse" data-target="#main_currency" style="cursor: pointer">Main currency <i class="fa fa-angle-down pull-right"></i></p>
<?php if(isset($currency_cfg->alphacode)): ?>
<input type="hidden" value="<?php echo e($currency_cfg->alphacode); ?>" id="currency_id">
<?php endif; ?>
<p class="settings size-12px" id="main_currency" class="collapse" style="font-weight: lighter">  <?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?> </p>
<hr>
<p class="title-quote title-quote size-14px" data-toggle="collapse" data-target="#exchange_rate" style="cursor: pointer">Exchange rate <i class="fa fa-angle-down pull-right"></i></p>
<?php if(isset($currency_cfg->alphacode)): ?>
<p class="settings size-12px" id="exchange_rate" style="font-weight: 100"><?php if($currency_cfg->alphacode=='EUR'): ?> 1 EUR = <?php echo e($exchange->rates); ?> USD <?php else: ?> 1 USD = <?php echo e($exchange->rates_eur); ?> EUR <?php endif; ?></p>
<?php endif; ?>
</div>        -->
      <div class="col-lg-2 col-md-2 col-sm-2 col-12 desktop">
        <h3 class="title-quote size-16px">Settings</h3>
        <hr>
        <p class="title-quote size-14px" data-toggle="collapse" data-target="#main_currency" style="cursor: pointer">Main currency <i class="fa fa-angle-down pull-right"></i></p>
        <?php if(isset($currency_cfg->alphacode)): ?>
        <input type="hidden" value="<?php echo e($currency_cfg->alphacode); ?>" id="currency_id">
        <?php endif; ?>
        <p class="settings size-12px" id="main_currency" class="collapse" style="font-weight: lighter">  <?php if(isset($currency_cfg->alphacode)): ?><?php echo e($currency_cfg->alphacode); ?><?php endif; ?> </p>
        <hr>
        <p class="title-quote title-quote size-14px" data-toggle="collapse" data-target="#exchange_rate" style="cursor: pointer">Exchange rate <i class="fa fa-angle-down pull-right"></i></p>
        <?php if(isset($currency_cfg->alphacode)): ?>
        <p class="settings size-12px" id="exchange_rate" style="font-weight: 100"><?php if($currency_cfg->alphacode=='EUR'): ?> 1 EUR = <?php echo e($exchange->rates); ?> USD <?php else: ?> 1 USD = <?php echo e($exchange->rates_eur); ?> EUR <?php endif; ?></p>
        <?php endif; ?>
        <hr>
        <label class="title-quote title-quote size-14px">PDF language</label>
        <?php if($companyInfo->pdf_language  == null ): ?>
        <?php echo Form::select('pdf_language', [1=>'English',2=>'Spanish',3=>'Portuguese'],$user->companyUser->pdf_language, ['placeholder' => 'Please choose a option','class' => 'form-control','id'=>'pdf_language']); ?>

        <?php else: ?>
        <?php echo Form::select('pdf_language', [1=>'English',2=>'Spanish',3=>'Portuguese'],$companyInfo->pdf_language, ['placeholder' => 'Please choose a option','class' => 'form-control','id'=>'pdf_language']); ?>

        <?php endif; ?>
        <hr>
        <label class="title-quote title-quote size-14px">PDF type</label>
        <?php echo Form::select('pdf_type', [1=>'All in',2=>'Detailed'],$user->companyUser->type_pdf, ['placeholder' => 'Please choose a option','class' => 'form-control','id'=>'pdf_type']); ?>

        <hr>
        <label class="title-quote title-quote size-14px">PDF Ammounts</label>
        <?php echo Form::select('pdf_ammounts', [1=>'Main Currency',2=>'Original ammounts'],$user->companyUser->pdf_ammounts, ['placeholder' => 'Please choose a option','class' => 'form-control','id'=>'pdf_ammounts']); ?>

        <hr>
        <label class="title-quote title-quote size-14px">Carrier visibility</label>
        <?php echo Form::select('hide_carrier', [true=>'Hide',false=>'Show'],true, ['placeholder' => 'Please choose a option','class' => 'form-control','id'=>'hide_carrier']); ?>

      </div>
    </div>

    <?php if(isset($form->price_id )): ?>
    <?php
    $priceID = $form->price_id;
    ?>
    <?php else: ?>
    <?php
    $priceID = "";
    ?>
    <?php endif; ?>
    <input type="hidden" class="form-control" id="incoterm" name="incoterm" value="<?php echo e($form->incoterm); ?> ">
    <input type="hidden" class="form-control" id="modality" name="modality" value="<?php echo e($form->modality); ?> ">
    <input type="hidden" class="form-control" id="since_validity" name="since_validity" value="<?php echo e($info->contract->validity); ?>">
    <input type="hidden" class="form-control" id="validity" name="validity" value="<?php echo e($info->contract->expire); ?>">
    <input type="hidden" class="form-control" id="origin_address" name="origin_address" value="<?php echo e($form->origin_address); ?> ">
    <input type="hidden" class="form-control" id="destination_address" name="destination_address" value="<?php echo e($form->destination_address); ?> ">
    <input type="hidden" class="form-control" id="company_id" name="company_id" value="<?php echo e($form->company_id_quote); ?> ">
    <input type="hidden" class="form-control" id="origin_harbor_id" name="origin_harbor_id" value="<?php echo e($info->origin_port); ?> ">
    <input type="hidden" class="form-control" id="destination_harbor_id" name="destination_harbor_id" value="<?php echo e($info->destiny_port); ?> ">
    <input type="hidden" class="form-control" id="price_id" name="price_id" value="<?php echo e($priceID); ?> ">
    <input type="hidden" class="form-control" id="contact_id" name="contact_id" value="<?php echo e($form->contact_id); ?> ">
    <input type="hidden" class="form-control" id="qty_20" name="qty_20" value="<?php echo e($form->twuenty); ?> ">
    <input type="hidden" class="form-control" id="qty_40" name="qty_40" value="<?php echo e($form->forty); ?> ">
    <input type="hidden" class="form-control" id="qty_40_hc" name="qty_40_hc" value="<?php echo e($form->fortyhc); ?> ">
    <input type="hidden" class="form-control" id="qty_40_nor" name="qty_40_nor" value="<?php echo e($form->fortynor); ?> ">
    <input type="hidden" class="form-control" id="qty_45_hc" name="qty_45_hc" value="<?php echo e($form->fortyfive); ?> ">
    <input type="hidden" class="form-control" id="pick_up_date" name="pick_up_date" value="<?php echo e($form->date); ?> ">
    <input type="hidden" class="form-control" id="status_id" name="status_id" value="1">
    <input type="hidden" class="form-control" id="delivery_type" name="delivery_type" value="<?php echo e($form->delivery_type); ?> ">
    <input type="hidden" class="form-control" id="type" name="type" value="<?php echo e($form->type); ?> ">
    <input type="hidden" class="form-control" id="schedule" name="schedule" value="<?php echo e(json_encode($schedules)); ?>">
    <input type="hidden" class="form-control" id="carrier_id" name="carrier_id" value="<?php echo e($info->carrier->id); ?>">

    <input type="hidden" class="form-control" id="lclAuto" name="lclAuto" value="true" >
    <input type="hidden" class="form-control" id="quot_auto" name="quot_auto" value="true" >
    <input type="hidden" class="form-control" id="quantity" name="quantity" value="<?php echo e(json_encode($form->quantity)); ?>" >
    <input type="hidden" class="form-control" id="type_load_cargo" name="type_load_cargo" value="<?php echo e(json_encode($form->type_load_cargo)); ?>">
    <input type="hidden" class="form-control" id="height" name="height" value="<?php echo e(json_encode($form->height)); ?>">
    <input type="hidden" class="form-control" id="width" name="width" value="<?php echo e(json_encode($form->width)); ?>" >
    <input type="hidden" class="form-control" id="large" name="large" value="<?php echo e(json_encode($form->large)); ?>" >
    <input type="hidden" class="form-control" id="weight" name="weight" value="<?php echo e(json_encode($form->weight)); ?>" >
    <input type="hidden" class="form-control" id="volume" name="volume" value="<?php echo e(json_encode($form->volume)); ?>" >

    <input type="hidden" class="form-control" id="total_quantity" name="total_quantity" value="<?php echo e($form->total_quantity); ?>">
    <input type="hidden" class="form-control" id="total_weight" name="total_weight" value="<?php echo e($form->total_weight); ?>">
    <input type="hidden" class="form-control" id="total_volume" name="total_volume" value="<?php echo e($form->total_volume); ?>">
    <input type="hidden" class="form-control" id="type_cargo" name="type_cargo" value="<?php echo e($form->type_cargo); ?>">

    <input type="hidden" class="form-control" id="chargeable_weight" name="chargeable_weight" value="<?php echo e($form->chargeable_weight); ?>">




    <input type="hidden" class="form-control" id="contract_number" name="contract_number" value="<?php echo e($info->contract->name); ?> / <?php echo e($info->contract->number); ?>">
    <input type="hidden" id="emaildimanicdata" value="<?php echo e($emaildimanicdata); ?>"/>

    <?php if($email_templates): ?>
    <?php echo $__env->make('quotes.partials.submitQuoteEmailModal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>
    <?php echo Form::close(); ?>  
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##

<script>
  $('.m-select2-general').select2({
    placeholder: "Select an option"
  });
</script>
<script src="<?php echo e(asset('js/base.js')); ?>" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/js/quote.js"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>
<script src="<?php echo e(asset('js/tinymce/jquery.tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/tinymce/tinymce.min.js')); ?>"></script>

<script>

  var editor_config = {
    path_absolute : "/",
    selector: "textarea.editor",
    plugins: ["template"],
    toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
    external_plugins: { "nanospell": "<?php echo e(asset('js/tinymce/plugins/nanospell/plugin.js')); ?>" },
    nanospell_server:"php",
    browser_spellcheck: true,
    relative_urls: false,
    remove_script_host: false,
    file_browser_callback : function(field_name, url, type, win) {
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

      var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
      if (type == 'image') {
        cmsURL = cmsURL + "&type=Images";
      } else {
        cmsURL = cmsURL + "&type=Files";
      }

      tinymce.activeEditor.windowManager.open({
        file: '<?= route('elfinder.tinymce4') ?>',// use an absolute path!
        title: 'File manager',
        width: 900,
        height: 450,
        resizable: 'yes'
      }, {
        setUrl: function (url) {
          win.document.getElementById(field_name).value = url;
        }
      });
    }
  };

  tinymce.init(editor_config);


</script>





<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>