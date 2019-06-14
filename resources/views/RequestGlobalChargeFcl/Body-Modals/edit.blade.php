<div class="form-group row">
    <div class="col-lg-4">
        <label for="NameMD" class="form-control-label">
            Name:
        </label>
        <input type="text" name="name" value="{{$requests->name}}" required="required" class="form-control" disabled id="NameMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Company User:
        </label>
        <input type="text" name="number" value="{{$requests->companyuser->name}}" required="required" class="form-control" disabled id="Company">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Id:
        </label>
        <input type="text" name="number" value="{{$requests->id}}" required="required" class="form-control" disabled id="NumberMD">
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Date:
        </label>
        <input type="text" name="number" value="{{$requests->created}}" required="required" class="form-control" disabled id="NumberMD">
    </div>
    <div class="col-lg-4">
        <label for="number" class="form-control-label">
            Username Load:
        </label>
        <input type="text" name="number" value="{{$requests->username_load}}" required="required" class="form-control" disabled id="NumberMD">
    </div>
    <input type="hidden" id="idContract" value="{{$requests->id}}"/>
</div>
<div class="form-group row">

    <div class="col-lg-12">
        {!! Form::label('Status', 'Status',["class"=>"form-control-label"]) !!}
        {{ Form::select('status',['Pending'=>'Pending','Processing'=>'Processing','Review'=>'Review','Done'=>'Done'],$requests->status,['id' => 'statusSelectMD','class'=>'m-select2-general  form-control ','style' => 'width:100%;']) }}
    </div>
</div>