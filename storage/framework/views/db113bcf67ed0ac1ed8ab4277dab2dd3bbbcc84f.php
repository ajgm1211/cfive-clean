<div class="form-group m-form__group">
    <?php echo Form::label('name', 'Name'); ?>

    <?php echo Form::text('name', null, ['placeholder' => 'Please enter a name','class' => 'form-control m-input','required' => 'required']); ?>

</div>
<div class="form-group m-form__group">
    <?php echo Form::label('type_20', '20\''); ?>

    <?php echo Form::text('type_20', null, ['placeholder' => 'Please enter a price','class' => 'form-control m-input','required' => 'required']); ?>

</div>
<div class="form-group m-form__group">
    <?php echo Form::label('type_40', '40\''); ?>

    <?php echo Form::text('type_40', null, ['placeholder' => 'Please enter a price','class' => 'form-control m-input','required' => 'required']); ?>

</div>
<div class="form-group m-form__group">
    <?php echo Form::label('type_40_hc', '40\' HC'); ?>

    <?php echo Form::text('type_40_hc', null, ['placeholder' => 'Please enter a price','class' => 'form-control m-input','required' => 'required']); ?>

</div>
<div class="form-group m-form__group">
    <?php echo Form::label('description', 'Description'); ?>

    <?php echo Form::textarea('description', null, ['placeholder' => 'Please enter a description','class' => 'form-control m-input','required' => 'required']); ?>

</div>