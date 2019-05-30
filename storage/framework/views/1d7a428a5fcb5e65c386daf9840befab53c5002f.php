<span class="size-8px">Fields with <span style="color:red">*</span> are mandatory</span>
<hr>
<div class="form-group m-form__group">
    <?php echo Form::label('business_name', 'Business Name'); ?><span style="color:red">*</span>
    <?php echo Form::text('business_name', null, ['placeholder' => 'Please enter a business name','class' => 'form-control m-input business_name_input','required' => 'required']); ?>

</div>
