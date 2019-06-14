<?php echo Form::open(['route' => ['Region.update',$region->id],'method' => 'PUT']); ?>

<div class="form-group row">
    <div class="col-lg-6">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" value="<?php echo e($region->name); ?>" required="required" class="form-control" id="NameMD">
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-12">
        <?php echo Form::label('countries', 'Countries'); ?>

        <?php echo e(Form::select('countries[]', $countries,$region->CountriesRegions->pluck('country_id'),['id' => 'portOrig','class'=>'m-select2-general  form-control ','multiple' => 'multiple' ,'style' => 'width:100%;'])); ?>

    </div>
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

</script>