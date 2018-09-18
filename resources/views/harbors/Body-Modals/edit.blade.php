{!! Form::open(['route' => ['UploadFile.update',$harbors->id],'method' => 'PUT']) !!}
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
            Display Name:
        </label>
        <input type="text" name="display_name" required="required" class="form-control" id="DispNamMD">
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-4">
        <label for="DispNamMD" class="form-control-label">
            Coordinate:
        </label>
        <input type="text" name="coordinate" class="form-control" id="coordinateMD">
    </div>
    <div class="col-lg-4">
        <label for="countryMD" class="form-control-label">
            Country:
        </label>
        {!! Form::select('country',$country,null,['id' => 'countryMD', 'class' => 'm-select2-general form-control'])!!}
    </div>
    <div class="col-lg-1">
    </div>
    <div class="col-lg-2">
        <br>
        <a href="#" class="btn btn-primary " onclick="agregarcampo()"><span class="la la-plus"></span></a>
    </div>

</div>
<div class="form-group row" id="variatiogroup">
    @foreach($decodejosn as $varation)
    <div class="col-lg-4" >
        <label for="DispNamMD" class="form-control-label">
            Variation:
        </label>
        <input type="text" name="variation[]" value="{{$varation}}" class="form-control">
    </div>
    @endforeach
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
{!! Form::close() !!}

<script>
    $('.m-select2-general').select2({

    });
</script>