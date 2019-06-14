<?php echo Form::open(['route' => ['Countries.update',$country->id],'method' => 'PUT']); ?>

<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" value="<?php echo e($country->name); ?>" required="required" class="form-control" id="NameMD">
    </div>
    <div class="col-lg-4">
        <label for="CodeMD" class="form-control-label">
            Code:
        </label>
        <input type="text" name="code" value="<?php echo e($country->code); ?>" required="required" class="form-control" id="CodeMD">
    </div>
        <div class="col-lg-4">
        <label for="CodeMD" class="form-control-label">
            Continent:
        </label>
        <input type="text" name="continent" value="<?php echo e($country->continent); ?>" required="required" class="form-control" id="ContMD">
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-2">
        <br>
        <a href="#" class="btn btn-primary " onclick="agregarcampo()"><span class="la la-plus"></span></a>
    </div>

</div>
<hr>
<div class="form-group row" id="variatiogroup">
    <?php $__currentLoopData = $decodejosn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nameVaration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <?php if($nameVaration != ''): ?>
    <div class="col-lg-4" >
        <label for="DispNamMD" class="form-control-label">
            Variation:
        </label>
        <input type="text" name="variation[]" value="<?php echo e($nameVaration); ?>" class="form-control">
        <a href="#" class="borrarInput"><samp class="la la-remove"></samp></a>
    </div>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<hr>
<div class="form-group pull-right" >
    <button type="submit" class="btn btn-primary">
        Update
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Cancel
    </button>
</div>
<?php echo Form::close(); ?>


<script>
    $('.m-select2-general').select2({

    });
    
    $(document).on('click','.borrarInput',function(e){
       $(this).closest('div').remove();
    });
</script>