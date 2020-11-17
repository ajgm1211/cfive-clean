{!! Form::open(['route' => 'gruopSurcharger.store','method' => 'POST']) !!}
<div class="form-group row">
    <div class="col-lg-3"></div>
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" required="required" class="form-control" id="NameMD">
    </div>
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
        <input type="text" name="varation[]" class="form-control">
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