<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 15/05/2018
 * Time: 09:51 PM
 */
?>

<div class="modal fade" id="companyUserModal<?php echo e($company_user_id); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Duplicate Company
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php echo Form::open(['route' => array('settings.duplicate'),'class' => 'form-group m-form__group']); ?>

            <div class="modal-body">
                <div class="m-form__section m-form__section--first">
                    <div class="form-group m-form__group">
                        <div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="name">Name</label>
                                <input type="text" placeholder="Company's name" id="name" name="name" class="form-control" required/>
                                <input type="hidden" placeholder="Company's name" id="company_user_id" name="company_user_id" value="<?php echo e($company_user_id); ?>" class="form-control" required/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="phone">Phone</label>
                                <input type="text" placeholder="Company's phone" id="phone" name="phone" class="form-control" required/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" id="address" placeholder="Company's address" cols="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="currency_id">Currency</label>
                                <?php echo e(Form::select('currency_id',[149=>'USD',46=>'EUR'],null,['placeholder' => 'Please choose a currency','class'=>'custom-select form-control','id' => 'currency_id','required'=>'true'])); ?>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="pdf_language">PDF language</label>
                                <?php echo e(Form::select('pdf_language',['1'=>'English','2'=>'Spanish','3'=>'Portuguese'],null,['placeholder' => 'Please choose a option','class'=>'custom-select form-control','id' => 'pdf_language','required'=>'true'])); ?>

                            </div>
                        </div>
                        <!--<div class="col-md-12">
                            <div class="form-group m-form__group">
                                <label for="currency_id">Logo</label>
                                <input type="file" class="form-control-file" name="image">
                            </div>
                        </div>-->
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
