<?php $__env->startSection('css'); ?>
##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
<link href="/css/quote.css" rel="stylesheet" type="text/css" />

<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title', 'Quotes'); ?>
<?php $__env->startSection('content'); ?>

<div class="row">

  

  <div class="col-xl-12">
    <?php if(!$arreglo->isEmpty()): ?>
    <div  class="row">
      <div class="col-xl-12">
        <div class="m-portlet m-portlet--full-height">

          <div class="m-portlet__body">
            <table  class="table m-table m-table--head-separator-primary"  border="0" id="">
              <thead>
                <tr>
                  <th  width = '20%' title="Field #2">
                    <span class="darkblue cabezeras">Carrier</span>
                  </th>
                  <th  width = '20%' title="Field #3">
                    <span class="gray cabezeras">Origin</span>
                  </th>
                  <th  width = '20%' title="Field #4" >
                    <span  class="gray cabezeras"  style=" float: right;">Destination</span>
                  </th>
                  <th  width = '20%' title="Field #5">
                    <span class="gray cabezeras"> Validity</span> 
                  </th>
                  <th  width = '20%' title="Field #6" >
                    <span class="gray cabezeras"  style=" float: right;">Price</span>  
                  </th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    <?php $__currentLoopData = $arreglo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $arr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
    $inl = 'false';
    ?>
    <div  class="row result-automatic">
      <div class="col-xl-12">
        <div class="m-portlet m-portlet--full-height result-automatic">
          <div class="m-portlet__body">
            <?php echo Form::open(['route' => ['quotes.test'] ,'name' => 'info','method' => 'post','class' => 'form-group m-form__group']); ?>

            <table  class="table m-table m-table--head-separator-primary" border="0" id="sample_editable">
              <tbody>
                <input type="hidden" name="info" value="<?php echo e(json_encode($arr)); ?>">
                <input type="hidden" name="form" value="<?php echo e(json_encode($form)); ?>">
                <tr id="principal<?php echo e($loop->iteration); ?>">
                  <td width = '20%'>
                    <div class="m-widget5">
                      <div class="m-widget5__item">
                        <div class="m-widget5__pic"> 
                          <img src="<?php echo e(url('imgcarrier/'.$arr->carrier->image)); ?>" alt="" title="" />
                        </div>
                      </div>
                    </div>

                  </td>
                  <td width = '40%' colspan="2">

                    <div class="row">
                      <div class="col-md-4">
                        <span class="portcss"> <?php echo e($arr->port_origin->name); ?></span><br>
                        <span class="portalphacode"> <?php echo e($arr->port_origin->code); ?></span>
                      </div>
                      <div class="col-md-4">
                        <div class="progress m-progress--sm">
                          <div class="progress-bar " role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div><br>
                        Contract: <?php echo e($arr->contract->name); ?> / <?php echo e($arr->contract->number); ?>

                      </div>
                      <div class="col-md-4">
                        <span class="portcss"> <?php echo e($arr->port_destiny->name); ?></span><br>
                        <span class="portalphacode"> <?php echo e($arr->port_destiny->code); ?></span>
                      </div>
                    </div>

                    <br>
                    <span class="workblue">Detail Cost</span>  <a  id='display_l<?php echo e($loop->iteration); ?>' onclick="display(<?php echo e($loop->iteration); ?>)" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" >
                    <i  class="la la-angle-down blue"></i>
                    </a>
                  </td>
                  <td width = '20%'>
                    <span class="darkblue validate"><?php echo e(\Carbon\Carbon::parse($arr->contract->validity)->format('d M Y')); ?> - <?php echo e(\Carbon\Carbon::parse($arr->contract->expire)->format('d M Y')); ?></span>                  
                  </td>
                  <td width = '20%'>     
                    <div class="m-widget5" style="float:right;">

                      <span class="m-widget5__number"> <span class="portalphacode"> <?php echo e($arr->quoteCurrency); ?> </span> <span class="darkblue totalq">  <?php echo e($arr->totalQuoteSin); ?> </span> 

                      </span><br>
                      <button type="submit" class="btn boton btn-md">Select</button><br>

                      <?php if(!$arr->schedulesFin->isEmpty()): ?>
                      <span class="workblue">Salling Schedules</span>  <a  id='schedule_l<?php echo e($loop->iteration); ?>'  class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" onclick="schedules(<?php echo e($loop->iteration); ?>)"  title="Cancel" >
                      <i  class="la la-angle-down blue"></i>
                      </a>
                      <?php endif; ?>

                    </div>
                  </td>
                </tr>
                <?php if((!$arr->globalOrig->isEmpty()) || (!$arr->localOrig->isEmpty())): ?>
                <tr id="origin<?php echo e($loop->iteration); ?>" hidden="true"  >
                  <td colspan="6">
                    <span class="darkblue cabezeras">Origin Charges</span>
                    <hr>
                    <table  class="table  table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Charge</span></th>
                        <th><span class="portalphacode">Detail</span>  </th>
                        <th><span class="portalphacode">Units</span></th>
                        <th><span class="portalphacode">Price per Unit</span></th>
                        <th><span class="portalphacode">Ammount</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>
                      <!--  Local charge  containter 20 , TEU , Per Container in Origin -->

                      <?php $__currentLoopData = $arr->localOrig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $origin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($origin['origin']['surcharge_name']); ?></td>
                        <td><?php echo e($origin['origin']['calculation_name']); ?> </td>
                        <td><?php echo e($origin['origin']['cantidad']); ?></td>
                        <td><?php echo e($origin['origin']['monto']); ?> <?php echo e($origin['origin']['currency']); ?></td>
                        <td><?php echo e($origin['origin']['subtotal_local']); ?> <?php echo e($origin['origin']['currency']); ?></td>
                        <td><?php echo e($origin['origin']['markup']); ?> <?php echo e($origin['origin']['typemarkup']); ?></td>
                        <td><?php echo e($origin['origin']['totalAmmount']); ?> </td>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php $__currentLoopData = $arr->globalOrig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $originGlo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($originGlo['origin']['surcharge_name']); ?></td>
                        <td><?php echo e($originGlo['origin']['calculation_name']); ?> </td>
                        <td><?php echo e($originGlo['origin']['cantidad']); ?></td>
                        <td><?php echo e($originGlo['origin']['monto']); ?> <?php echo e($originGlo['origin']['currency']); ?></td>
                        <td><?php echo e($originGlo['origin']['subtotal_global']); ?> <?php echo e($originGlo['origin']['currency']); ?></td>
                        <td><?php echo e($originGlo['origin']['markup']); ?> <?php echo e($originGlo['origin']['typemarkup']); ?></td>
                        <td><?php echo e($originGlo['origin']['totalAmmount']); ?> </td>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td colspan="5"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td><span  class="darkblue px12" ><?php echo e($arr->totalOrigin); ?> </span> </td>
                      </tr>
                    </table>
                  </td>
                </tr> 
                <?php endif; ?>
                <tr id="detail<?php echo e($loop->iteration); ?>"  hidden="true">
                  <td colspan="6">
                    <span class="darkblue cabezeras">Freight Charges</span>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Charge</span></th>
                        <th><span class="portalphacode">Details  </span></th>
                        <th><span class="portalphacode">Units</span></th>
                        <th><span class="portalphacode">Price per Unit</span></th>
                        <th><span class="portalphacode">Ammount</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>

                      <?php $__currentLoopData = $arr->rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $var): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($var['type']); ?></td>
                        <td><?php echo e($var['detail']); ?></td>
                        <td><?php echo e($var['cantidad']); ?></td>
                        <td><?php echo e($var['price']); ?> <?php echo e($var['currency']); ?></td>
                        <td><?php echo e($var['subtotal']); ?> <?php echo e($var['currency']); ?></td>
                        <td><?php echo e($var['markup']); ?> <?php echo e($var['typemarkup']); ?></td>
                        <td><?php echo e($var['total']); ?></td>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php $__currentLoopData = $arr->localFreight; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($freight['freight']['surcharge_name']); ?></td>
                        <td><?php echo e($freight['freight']['calculation_name']); ?> </td>
                        <td><?php echo e($freight['freight']['cantidad']); ?></td>
                        <td><?php echo e($freight['freight']['monto']); ?> <?php echo e($freight['freight']['currency']); ?></td>
                        <td><?php echo e($freight['freight']['subtotal_local']); ?> <?php echo e($freight['freight']['currency']); ?></td>
                        <td><?php echo e($freight['freight']['markup']); ?> <?php echo e($freight['freight']['typemarkup']); ?></td>
                        <td><?php echo e($freight['freight']['totalAmmount']); ?> </td>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                      <?php $__currentLoopData = $arr->globalFreight; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freightGlo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($freightGlo['freight']['surcharge_name']); ?></td>
                        <td><?php echo e($freightGlo['freight']['calculation_name']); ?> </td>
                        <td><?php echo e($freightGlo['freight']['cantidad']); ?></td>
                        <td><?php echo e($freightGlo['freight']['monto']); ?> <?php echo e($freightGlo['freight']['currency']); ?></td>
                        <td><?php echo e($freightGlo['freight']['subtotal_global']); ?> <?php echo e($freightGlo['freight']['currency']); ?></td>
                        <td><?php echo e($freightGlo['freight']['markup']); ?> <?php echo e($freightGlo['freight']['typemarkup']); ?></td>
                        <td><?php echo e($freightGlo['freight']['totalAmmount']); ?> </td>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php if( ($formulario->twuenty !="0") || ($formulario->forty !="0") || ($formulario->fortyhc!="0" ) ||  ($formulario->fortynor !="0") || ($formulario->fortyfive !="0") ): ?>
                      <tr>
                        <td colspan="5"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td> <span  class="darkblue px12" > <?php echo e($arr->totalFreight); ?> </span></td>
                      </tr>

                      <?php else: ?>
                      <tr>
                        <td colspan='6'>No rates available</td>
                      </tr>

                      <?php endif; ?>

                    </table>
                  </td>
                </tr>
                <?php if((!$arr->globalDest->isEmpty() ) || (!$arr->localDest->isEmpty() )): ?>
                <tr id="destination<?php echo e($loop->iteration); ?>" hidden="true" >
                  <td colspan="6">
                    <span class="darkblue cabezeras"> Destination Charges</span>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Charge</span></th>
                        <th><span class="portalphacode">Detail</span>  </th>
                        <th><span class="portalphacode">Units</span></th>
                        <th><span class="portalphacode">Price per Unit</span></th>
                        <th><span class="portalphacode">Ammount</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>

                      <?php $__currentLoopData = $arr->localDest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destiny): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($destiny['destiny']['surcharge_name']); ?></td>
                        <td><?php echo e($destiny['destiny']['calculation_name']); ?> </td>
                        <td><?php echo e($destiny['destiny']['cantidad']); ?></td>
                        <td><?php echo e($destiny['destiny']['monto']); ?> <?php echo e(@$destiny['destiny']['currency']); ?></td>
                        <td><?php echo e($destiny['destiny']['subtotal_local']); ?> <?php echo e($destiny['destiny']['currency']); ?></td>
                        <td><?php echo e($destiny['destiny']['markup']); ?> <?php echo e($destiny['destiny']['typemarkup']); ?></td>
                        <td><?php echo e($destiny['destiny']['totalAmmount']); ?> </td>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                      <?php $__currentLoopData = $arr->globalDest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destinyGlo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td><?php echo e($destinyGlo['destiny']['surcharge_name']); ?></td>
                        <td><?php echo e($destinyGlo['destiny']['calculation_name']); ?> </td>
                        <td><?php echo e($destinyGlo['destiny']['cantidad']); ?></td>
                        <td><?php echo e($destinyGlo['destiny']['monto']); ?> <?php echo e(@$destinyGlo['destiny']['currency']); ?></td>
                        <td><?php echo e($destinyGlo['destiny']['subtotal_global']); ?> <?php echo e($destinyGlo['destiny']['currency']); ?></td>
                        <td><?php echo e($destinyGlo['destiny']['markup']); ?> <?php echo e($destinyGlo['destiny']['typemarkup']); ?></td>
                        <td><?php echo e($destinyGlo['destiny']['totalAmmount']); ?> </td>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <td colspan="5"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td> <span  class="darkblue px12" > <?php echo e($arr->totalDestiny); ?></span>  </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <?php endif; ?>
                <?php if((!empty($inlandDestiny)) || (!empty($inlandOrigin))): ?>
                <tr id="inlands<?php echo e($loop->iteration); ?>" hidden="true" >
                  <td colspan="6">
                    <span class="darkblue cabezeras">Inland Charges</span>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Provider</span></th>
                        <th><span class="portalphacode">Type</span></th>
                        <th><span class="portalphacode">Distance</span>  </th>
                        <th><span class="portalphacode">Port Name</span></th>
                        <th><span class="portalphacode">Amount</span></th>
                        <th><span class="portalphacode">Markup</span></th>
                        <th><span class="portalphacode">Total Ammount</span></th>
                      </tr>
                      <?php if(!empty($inlandDestiny)): ?>
                      <tr>
                        <th colspan="6"> <span  class=" portalphacode darkblue px12" >Destination </span></th>
                      </tr>
                      <?php $__currentLoopData = $inlandDestiny; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inlandDest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($inlandDest['port_id'] == $arr->port_destiny->id ): ?>
                      <?php $__currentLoopData = $inlandDest['inlandDetails']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <th><?php echo e($inlandDest['provider']); ?></th>
                        <th><?php echo e($details['des_in']); ?></th>
                        <th><?php echo e($inlandDest['km']); ?> KM</th>
                        <th><?php echo e($inlandDest['port_name']); ?></th>
                        <th><?php echo e($details['amount']); ?> <?php echo e($details['currency']); ?></th>
                        <th><?php echo e($details['markup']); ?> <?php echo e($details['typemarkup']); ?></th>
                        <th><?php echo e($details['sub_in']); ?> <?php echo e($inlandDest['type_currency']); ?></th>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>

                      <?php if(!empty($inlandOrigin)): ?>
                      <tr>
                        <td colspan="6"> <span  class="darkblue px12" >Origin </span></td>
                      </tr>
                      <?php $__currentLoopData = $inlandOrigin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inlandOrig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($inlandOrig['port_id'] == $arr->port_origin->id ): ?>
                      <?php $__currentLoopData = $inlandOrig['inlandDetails']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detailsOrig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr>
                        <th><?php echo e($inlandOrig['provider']); ?></th>
                        <th><?php echo e($detailsOrig['des_in']); ?></th>
                        <th><?php echo e($inlandOrig['km']); ?> KM</th>
                        <th><?php echo e($inlandOrig['port_name']); ?></th>
                        <th><?php echo e($detailsOrig['amount']); ?> <?php echo e($detailsOrig['currency']); ?></th>
                        <th><?php echo e($detailsOrig['markup']); ?> <?php echo e($detailsOrig['typemarkup']); ?></th>
                        <th><?php echo e($detailsOrig['sub_in']); ?> <?php echo e($inlandOrig['type_currency']); ?></th>
                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                      <tr>
                        <td colspan="5"></td>
                        <td > <span  class="darkblue px12" >SUBTOTAL:</span></td>
                        <td> <span  class="darkblue px12" > <?php echo e($arr->totalInland); ?> <?php echo e($arr->quoteCurrency); ?></span>  </td>
                      </tr>
                    </table>
                  </td>
                </tr>                
                <?php endif; ?>
                <?php if(!$arr->schedulesFin->isEmpty()): ?>
                <tr id="schedules<?php echo e($loop->iteration); ?>" hidden="true"   >
                  <td colspan="6">
                    <span class="darkblue cabezeras"><b>Schedules</b></span>
                    <hr>
                    <table class="table table-hover">
                      <tr class="thead-light">
                        <th><span class="portalphacode">Vessel</span></th>
                        <th><span class="portalphacode">ETD</span></th>
                        <th><span class="portalphacode"><center>Transit Time</center></span>  </th>
                        <th><span class="portalphacode">ETA</span></th>
                        <th><span class="portalphacode">-</span></th>         
                      </tr>                    


                      <?php $__currentLoopData = $arr->schedulesFin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                      <tr>
                        <td width='15%'><?php echo e($schedule['vessel']); ?></td>
                        <td width='15%'><?php echo e($schedule['etd']); ?></td>
                        <td width='45%'>
                          <div class="row">
                            <div class="col-md-4">
                              <span class="portcss"> <?php echo e($arr->port_origin->name); ?></span><br>            
                            </div>
                            <div class="col-md-4">
                              <center> <?php echo e($schedule['days']); ?> Days</center>
                              <div class="progress m-progress--sm">    
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                              <center> <?php echo e($schedule['type']); ?> </center>

                            </div>
                            <div class="col-md-4">
                              <span class="portcss"> <?php echo e($arr->port_destiny->name); ?></span><br>

                            </div>
                          </div>                        
                        </td>
                        <td width='15%'><?php echo e($schedule['eta']); ?></td>
                        <td width='10%'>      
                          <label class="m-checkbox m-checkbox--state-brand">
                            <input name="schedules[]" type="checkbox" value="<?php echo e(json_encode($schedule)); ?>"> 
                            <span></span>
                          </label>
                        </td>

                      </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
            <?php echo Form::close(); ?>

          </div>
        </div>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>

    <!--end::Portlet-->

    <div  class="row" style="margin-top:10%">
      <div class="col-xl-12" >
        <div class="m-portlet m-portlet m-portlet--head-solid-bg m-portlet--rounded">
          <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
              <div class="m-portlet__head-title">
                <span class="m-portlet__head-icon">
                  <i class="flaticon-cogwheel-2"></i>
                </span>
                <h3 class="m-portlet__head-text m--font-brand">
                  <span class="darkblue">NO RATES HAVE BEEN FOUND.</span>

                </h3>
              </div>			
            </div>
            <div class="m-portlet__head-tools">
              <ul class="m-portlet__nav">
                <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover">
                  <a href="#" class="m-portlet__nav-link btn btn-primary m-btn m-btn--icon m-btn--icon-only m-btn--pill   m-dropdown__toggle">
                    <i class="la la-ellipsis-v"></i>
                  </a>
                  <div class="m-dropdown__wrapper">
                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                    <div class="m-dropdown__inner">
                      <div class="m-dropdown__body">
                        <div class="m-dropdown__content">
                          <div class="m-demo">
                            <div class="m-demo__preview">
                              <div class="m-list-search">
                                <div class="m-list-search__results">
                                  <span class="m-list-search__result-category m-list-search__result-category--first">
                                    Quick action
                                  </span>
                                  <a  href="<?php echo e(route('quotes.index')); ?>" class="m-list-search__result-item">
                                    <span class="m-list-search__result-item-icon"><i class="flaticon-interface-3 m--font-warning"></i></span>
                                    <span class="m-list-search__result-item-text">
                                      View rates                
                                    </span>

                                  </a>
                                  <a href="<?php echo e(route('contracts.index')); ?>"  class="m-list-search__result-item">
                                    <span class="m-list-search__result-item-icon"><i class="flaticon-share m--font-success"></i></span>
                                    <span class="m-list-search__result-item-text"> View quotes</span>

                                  </a>
                                  <a href="#" class="m-list-search__result-item">
                                    <span class="m-list-search__result-item-icon"><i class="flaticon-paper-plane m--font-info"></i></span>
                                    <span class="m-list-search__result-item-text">Quote automatic</span>

                                  </a>
                                  <hr>

                                  <a href="#" class="btn btn-outline-prima m-btn m-btn--pill m-btn--wide btn-sm">
                                    Cancel
                                  </a>

                                </div>
                              </div>
                            </div>
                          </div>                  
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="m-portlet__body">
            <div class="m-alert m-alert--icon m-alert--outline alert alert-danger" role="alert">
              <div class="m-alert__icon">
                <i class="la la-warning"></i>
              </div>
              <div class="m-alert__text">
                <strong>Sorry</strong> no rates have found 

              </div>	
            </div>
            <a  class="btn btn-sm btn-primary m-btn m-btn--icon" href="<?php echo e(route('quotes.create')); ?>">
              <span style="color: white;">
                <i class="la la-plus"></i>
                <span>Manual Quote </span>
              </span>
            </a>
            <a  class="btn btn-sm btn-primary m-btn m-btn--icon" href="<?php echo e(route('quotes.automatic')); ?>">
              <span style="color: white;">  
                <span>
                  Automatic Quote
                </span>
                <i class="la la-plus"></i>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>
  </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##

<script src="/js/quote.js"></script>
<script src="/assets/plugins/datatable.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/datatables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/portlets/draggable.js" type="text/javascript"></script>
<script>
  $(document).ready( function () {
    $('#sample_editable').DataTable();
  } );
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>