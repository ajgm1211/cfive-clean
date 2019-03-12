{!! Form::open(['route' => 'Countries.store','method' => 'POST']) !!}
<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" required="required" class="form-control" id="NameMD">
    </div>
    <div class="col-lg-4">
        <label for="CodeMD" class="form-control-label">
            Code:
        </label>
        <input type="text" name="code" required="required" class="form-control" id="CodeMD">
    </div>
    <div class="col-lg-4">
        <label for="DispNamMD" class="form-control-label">
            Continent:
        </label>
        <input type="text" name="continent" required="required" class="form-control" id="ContMD">
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
    <div class="col-lg-4" >
        <label for="DispNamMD" class="form-control-label">
            Variation:
        </label>
        <input type="text" name="variation[]" class="form-control">
    </div>
</div>
<hr>
<div class="form-group pull-right" >
    <button type="submit" class="btn btn-primary">
        Load
    </button>
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        Cancel
    </button>
</div>
{!! Form::close() !!}

<script>
    $('.m-select2-general').select2({

    });
</script>