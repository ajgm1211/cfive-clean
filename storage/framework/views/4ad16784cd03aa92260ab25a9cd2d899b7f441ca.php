<div class="modal fade bd-example-modal-lg" id="addOwnerModal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    Add Owners
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div id="edit-modal-body-E" class="modal-body-E">
                <br>
                <?php echo Form::open(['route' => 'companies.store.owner', 'method' => 'POST']); ?>


                <div class="form-group row pull-right">
                    <div class="col-md-3 ">
                        <?php echo Form::hidden('company_id', $company->id, ['placeholder' => 'Please enter phone number','class' => 'form-control m-input phone_input','required' => 'required']); ?>                       
                    </div>
                </div>
                <div class="form-group row ">
                    <div class="col-md-8 offset-md-2">
                        <?php echo Form::label('users_id', 'Associate User'); ?><br>
                        <?php echo e(Form::select('users[]',$users,null,['class'=>'custom-select form-control users_company','id' => 'users_company_2','multiple'=>'true'])); ?>

                    </div>
                </div>
            </div>
            <div id="edit-modal-body" class="modal-footer">
                <?php echo Form::submit('Save', ['class'=> 'btn btn-primary']); ?>

                <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">Cancel</span>
                </button>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>
</div>