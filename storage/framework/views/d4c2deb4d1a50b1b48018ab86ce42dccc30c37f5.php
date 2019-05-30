<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 15/05/2018
 * Time: 09:51 PM
 */
?>

<div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
            <?php echo Form::open(['route' => 'contacts.store','class' => 'form-group m-form__group']); ?>

            <div class="modal-body">
                <div class="m-form__section m-form__section--first">
                    <div class="form-group m-form__group">
                        <span class="size-8px">Fields with <span style="color:red">*</span> are mandatory</span>
                        <hr>
                        <div class="form-group m-form__group">
                            <?php echo Form::label('first_name', 'First Name'); ?><span style="color:red">*</span>
                            <?php echo Form::text('first_name', null, ['placeholder' => 'Please enter your first name','class' => 'form-control m-input','required' => 'required']); ?>

                            <?php echo Form::hidden('company_id', $company->id, ['class' => 'form-control m-input','required' => 'required']); ?>

                        </div>
                        <div class="form-group m-form__group">
                            <?php echo Form::label('last_name', 'Last Name'); ?><span style="color:red">*</span>
                            <?php echo Form::text('last_name', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input','required' => 'required']); ?>

                        </div>
                        <div class="form-group m-form__group">
                            <?php echo Form::label('email', 'Email'); ?><span style="color:red">*</span>
                            <?php echo Form::email('email', null, ['placeholder' => 'Please enter a valid email','class' => 'form-control m-input','required' => 'required']); ?>

                        </div>
                        <div class="form-group m-form__group">
                            <?php echo Form::label('phone', 'Phone'); ?>

                            <?php echo Form::text('phone', null, ['placeholder' => 'Please enter a phone','class' => 'form-control m-input','required' => 'required']); ?>

                        </div>
                        <div class="form-group m-form__group">
                            <?php echo Form::label('position', 'Position'); ?>

                            <?php echo Form::text('position', null, ['placeholder' => 'Please enter a position','class' => 'form-control m-input phonec_input']); ?>

                        </div>
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
