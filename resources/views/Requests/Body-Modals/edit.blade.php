<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" value="{{$requests->namecontract}}" required="required" class="form-control" disabled id="NameMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Number:
        </label>
        <input type="text" name="number" value="{{$requests->numbercontract}}" required="required" class="form-control" disabled id="NameMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Number:
        </label>
        <input type="text" name="number" value="{{$requests->companyuser->name}}" required="required" class="form-control" disabled id="NameMD">
    </div>
    <input type="hidden" id="idContract" value="{{$requests->id}}"/>
</div>
<div class="form-group row">
    <div class="col-lg-12">
        {!! Form::label('Status', 'Status',["class"=>"form-control-label"]) !!}
        {{ Form::select('status',['Pending'=>'Pending','Processing'=>'Processing','Done'=>'Done'],$requests->status,['id' => 'statusSelectMD','class'=>'m-select2-general  form-control ','style' => 'width:100%;']) }}
    </div>
</div>