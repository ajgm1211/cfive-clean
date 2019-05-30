<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 15/05/2018
 * Time: 09:51 PM
 */
?>

<div class="modal fade" id="addQuoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Add contact
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo Form::open(['route' => 'quotes.store','class' => 'form-group m-form__group']); ?>

            <div class="modal-body">
                <div class="m-form__section m-form__section--first">
                    <div class="form-group m-form__group">
                        <?php echo Form::label('company_id', 'Client'); ?><br>
                        <?php echo e(Form::select('company_id',$companies,null,['placeholder' => 'Please choose a client','class'=>'custom-select form-control','id' => 'm_select2_2_modal'])); ?>

                    </div>
                    <div class="form-group m-form__group">
                        <?php echo Form::label('origin', 'Origin'); ?><br>
                        <?php echo e(Form::select('origin',$countries,null,['placeholder' => 'Please choose an origin','class'=>'custom-select form-control','id' => 'select-origin-2'])); ?>

                    </div>
                    <div class="form-group m-form__group">
                        <?php echo Form::label('destination', 'Destination'); ?><br>
                        <?php echo e(Form::select('destination',$countries,null,['placeholder' => 'Please choose a destination','class'=>'custom-select form-control','id' => 'select-destination-2'])); ?>

                    </div>
                    <div class="form-group m-form__group">
                        <?php echo Form::label('ammount', 'Ammount'); ?>

                        <?php echo Form::text('ammount', null, ['placeholder' => 'Please enter an ammount','class' => 'form-control m-input','required' => 'required']); ?>

                    </div>
                    <div class="form-group m-form__group">
                        <?php echo Form::label('status_id', 'Status'); ?><br>
                        <?php echo e(Form::select('status_id',[1=>'Sent',2=>'Accepted'],null,['placeholder' => 'Please choose a status','class'=>'custom-select form-control','id' => 'm_select2_2_modal'])); ?>

                    </div>
                </div>
                <div class="m-portlet__foot m-portlet__foot--fit">
                    <br>
                    <div class="m-form__actions m-form__actions">
                        <?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
